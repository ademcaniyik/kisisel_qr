<?php
/**
 * Profil Yönetim Sınıfı
 * Profillerin veritabanı işlemlerini yönetir
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/utilities.php';

class ProfileManager {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }
    
    /**
     * Sipariş verileriyle otomatik profil oluştur
     */
    public function createProfileFromOrder($orderData) {
        try {
            // Sosyal medya verilerini parse et
            $socialLinks = $this->parseSocialMediaFromOrder($orderData);
            
            // Benzersiz slug oluştur
            $slug = $this->generateUniqueSlug($orderData['customer_name']);
            
            // Temayı belirle (order'dan gelen tema adını profil tema koduna çevir)
            $theme = $this->convertThemeNameToCode($orderData);
            
            // Fotoğraf işleme
            $photoData = null;
            if (isset($orderData['photo_file']) && $orderData['photo_file']['error'] === UPLOAD_ERR_OK) {
                $photoData = $this->processUploadedPhoto($orderData['photo_file']);
            }
            
            // Profil verileri hazırla
            $profileData = [
                'name' => $orderData['customer_name'],
                'bio' => $this->extractBioFromOrder($orderData),
                'phone' => $orderData['customer_phone'],
                'theme' => $theme,
                'slug' => $slug,
                'iban' => $this->extractIbanFromOrder($orderData),
                'blood_type' => $this->extractBloodTypeFromOrder($orderData),
                'social_links' => json_encode($socialLinks, JSON_UNESCAPED_UNICODE),
                'photo_data' => $photoData ? json_encode($photoData, JSON_UNESCAPED_UNICODE) : null
            ];
            
            // Profili oluştur
            $profileId = $this->createProfile($profileData);
            
            if ($profileId) {
                // QR kod ataması: Sadece QRPoolManager kullanılacak
                require_once __DIR__ . '/QRPoolManager.php';
                $qrPoolManager = new QRPoolManager();
                $qrAssignment = $qrPoolManager->assignAvailableQR($profileId, $orderData);
                if ($qrAssignment['success']) {
                    return [
                        'success' => true,
                        'profile_id' => $profileId,
                        'slug' => $slug,
                        'qr_created' => true,
                        'qr_id' => $qrAssignment['qr_code_id']
                    ];
                } else {
                    // Fallback: QR havuzunda QR yoksa eski sistemle üret
                    require_once __DIR__ . '/QRManager.php';
                    $qrManager = new QRManager();
                    $qrResult = $qrManager->createQR($profileId);
                    return [
                        'success' => true,
                        'profile_id' => $profileId,
                        'slug' => $slug,
                        'qr_created' => $qrResult['success'] ?? false,
                        'qr_id' => $qrResult['qrId'] ?? null,
                        'fallback_qr' => true
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Profil oluşturulamadı'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Profil oluştur
     */
    private function createProfile($data) {
        $sql = "INSERT INTO profiles (name, bio, phone, theme, slug, iban, blood_type, social_links, photo_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        $stmt->bind_param(
            "sssssssss",
            $data['name'],
            $data['bio'],
            $data['phone'],
            $data['theme'],
            $data['slug'],
            $data['iban'],
            $data['blood_type'],
            $data['social_links'],
            $data['photo_data']
        );
        
        if ($stmt->execute()) {
            $profileId = $this->connection->insert_id;
            $stmt->close();
            return $profileId;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Profil oluşturulurken hata: " . $error);
        }
    }
    
    /**
     * Sipariş verisinden sosyal medya linklerini parse et
     */
    private function parseSocialMediaFromOrder($orderData) {
        $socialLinks = [];
        
        if (isset($orderData['special_requests']) && $orderData['special_requests']) {
            $lines = explode("\n", $orderData['special_requests']);
            $inSocialSection = false;
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                if (strpos($line, 'Sosyal Medya:') !== false) {
                    $inSocialSection = true;
                    continue;
                }
                
                if ($inSocialSection && strpos($line, 'Tema:') !== false) {
                    break;
                }
                
                if ($inSocialSection && $line) {
                    // Instagram: @username veya Instagram: url formatını parse et
                    if (preg_match('/^(\w+):\s*(.+)$/', $line, $matches)) {
                        $platform = strtolower(trim($matches[1]));
                        $value = trim($matches[2]);
                        
                        // Platform adlarını normalize et
                        $platformMap = [
                            'instagram' => 'instagram',
                            'twitter' => 'twitter',
                            'linkedin' => 'linkedin', 
                            'facebook' => 'facebook',
                            'youtube' => 'youtube',
                            'tiktok' => 'tiktok',
                            'whatsapp' => 'whatsapp',
                            'website' => 'website',
                            'telegram' => 'telegram',
                            'discord' => 'discord',
                            'snapchat' => 'snapchat',
                            'twitch' => 'twitch',
                            'behance' => 'behance'
                        ];
                        
                        if (isset($platformMap[$platform])) {
                            // @ ile başlıyorsa tam URL'e çevir
                            if (strpos($value, '@') === 0) {
                                $username = substr($value, 1);
                                switch ($platform) {
                                    case 'instagram':
                                        $value = "https://instagram.com/{$username}";
                                        break;
                                    case 'twitter':
                                        $value = "https://twitter.com/{$username}";
                                        break;
                                    case 'tiktok':
                                        $value = "https://tiktok.com/@{$username}";
                                        break;
                                    case 'whatsapp':
                                        // WhatsApp için telefon numarası formatı
                                        $value = "https://wa.me/{$username}";
                                        break;
                                }
                            }
                            
                            $socialLinks[$platformMap[$platform]] = $value;
                        }
                    }
                }
            }
        }
        
        return $socialLinks;
    }
    
    /**
     * Sipariş verisinden bio çıkar
     */
    private function extractBioFromOrder($orderData) {
        if (isset($orderData['special_requests']) && $orderData['special_requests']) {
            $lines = explode("\n", $orderData['special_requests']);
            
            foreach ($lines as $line) {
                if (strpos($line, 'Bio:') !== false) {
                    return trim(str_replace('Bio:', '', $line));
                }
            }
        }
        
        return 'Merhaba! Benim dijital profilim.';
    }
    
    /**
     * Tema adını profil tema koduna çevir
     */
    private function convertThemeNameToCode($orderData) {
        $defaultTheme = 'default';
        
        if (isset($orderData['special_requests']) && $orderData['special_requests']) {
            $lines = explode("\n", $orderData['special_requests']);
            
            foreach ($lines as $line) {
                if (strpos($line, 'Tema:') !== false) {
                    $themeName = trim(str_replace('Tema:', '', $line));
                    
                    // Tema adlarını kodlara çevir
                    $themeMap = [
                        'Sade Temiz (Varsayılan)' => 'default',
                        'Deniz Mavisi' => 'blue',
                        'Günbatımı Sıcak' => 'nature', 
                        'Doğa Yeşil' => 'elegant',
                        'Altın Lüks' => 'gold',
                        'Kraliyet Moru' => 'purple',
                        'Karanlık Siyah' => 'dark',
                        'Sakura Pembe' => 'ocean',
                        'Şık Mor' => 'minimal',
                        'Pastel Rüya' => 'pastel',
                        'Retro Synthwave' => 'retro',
                        'Neon Siber' => 'neon'
                    ];
                    
                    return $themeMap[$themeName] ?? $defaultTheme;
                }
            }
        }
        
        return $defaultTheme;
    }
    
    /**
     * Benzersiz slug oluştur
     */
    private function generateUniqueSlug($name) {
        // Türkçe karakterleri çevir ve slug oluştur
        $slug = $this->createSlugFromName($name);
        $originalSlug = $slug;
        $counter = 1;
        
        // Benzersiz olana kadar dene
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * İsimden slug oluştur
     */
    private function createSlugFromName($name) {
        // Türkçe karakterleri çevir
        $turkishChars = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
        $englishChars = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
        
        $slug = str_replace($turkishChars, $englishChars, $name);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = substr($slug, 0, 30); // Maksimum 30 karakter
        
        return $slug ?: 'profil-' . uniqid();
    }
    
    /**
     * Slug'ın mevcut olup olmadığını kontrol et
     */
    private function slugExists($slug) {
        $sql = "SELECT id FROM profiles WHERE slug = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Profil ID'sine göre profil getir
     */
    public function getProfile($profileId) {
        $sql = "SELECT * FROM profiles WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $profileId);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();
        
        return $profile;
    }
    
    /**
     * Slug ile profil getir
     */
    public function getProfileBySlug($slug) {
        $sql = "SELECT * FROM profiles WHERE slug = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();
        
        return $profile;
    }
    
    /**
     * Sipariş verisinden IBAN bilgisini çıkar
     */
    private function extractIbanFromOrder($orderData) {
        if (isset($orderData['special_requests']) && $orderData['special_requests']) {
            $lines = explode("\n", $orderData['special_requests']);
            
            foreach ($lines as $line) {
                if (strpos($line, 'İban:') !== false) {
                    return trim(str_replace('İban:', '', $line));
                }
            }
        }
        
        return null;
    }
    
    /**
     * Sipariş verisinden kan grubu bilgisini çıkar
     */
    private function extractBloodTypeFromOrder($orderData) {
        if (isset($orderData['special_requests']) && $orderData['special_requests']) {
            $lines = explode("\n", $orderData['special_requests']);
            
            foreach ($lines as $line) {
                if (strpos($line, 'Kan Grubu:') !== false) {
                    return trim(str_replace('Kan Grubu:', '', $line));
                }
            }
        }
        
        return null;
    }
    
    /**
     * Yüklenen fotoğrafı işle ve kaydet
     */
    public function processUploadedPhoto($file) {
        try {
            // ImageOptimizer'ı yükle
            require_once __DIR__ . '/ImageOptimizer.php';
            $imageOptimizer = new ImageOptimizer();
            
            // Dosya kontrolü
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Dosya yükleme hatası: " . $file['error']);
            }
            
            // ImageOptimizer ile fotoğrafı yükle ve optimize et
            $result = $imageOptimizer->uploadAndOptimize($file);
            
            if ($result['success']) {
                return [
                    'filename' => $result['filename'],
                    'original_name' => $file['name'],
                    'size' => $file['size'],
                    'mime_type' => $result['mime_type'] ?? 'image/jpeg',
                    'created_at' => date('Y-m-d H:i:s')
                ];
            } else {
                throw new Exception("Fotoğraf işleme hatası: " . $result['message']);
            }
        } catch (Exception $e) {
            throw new Exception("Fotoğraf işleme hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Profil güncelle (temel bilgiler + sosyal medya + fotoğraf)
     */
    public function updateProfile($profileId, $name, $phone, $bio = null, $iban = null, $blood_type = null, $theme = null, $socialLinks = null, $photoUrl = null, $photoData = null) {
        try {
            $this->debugLog("====== UpdateProfile BAŞLADI ======");
            $this->debugLog("Profile ID", $profileId);
            $this->debugLog("İşlenecek veriler", [
                'name' => $name,
                'phone' => $phone,
                'bio' => $bio,
                'iban' => $iban,
                'blood_type' => $blood_type,
                'theme' => $theme,
                'social_links' => $socialLinks,
                'photo_url' => $photoUrl,
                'has_photo_data' => !empty($photoData)
            ]);

            // Önce mevcut profili kontrol et
            $currentProfile = $this->getProfile($profileId);
            if (!$currentProfile) {
                $this->debugLog("Profil bulunamadı", $profileId);
                return false;
            }
            $this->debugLog("Mevcut profil bulundu", $currentProfile);

            // Değişiklikleri analiz et
            $updateFields = [];
            $params = [];
            $types = "";

            if (!empty($name) && $name !== $currentProfile['name']) {
                $updateFields[] = "name = ?";
                $params[] = $name;
                $types .= "s";
            }

            if (!empty($phone) && $phone !== $currentProfile['phone']) {
                $updateFields[] = "phone = ?";
                $params[] = $phone;
                $types .= "s";
            }

            if ($bio !== null && $bio !== $currentProfile['bio']) {
                $updateFields[] = "bio = ?";
                $params[] = $bio;
                $types .= "s";
            }

            if ($iban !== null && $iban !== $currentProfile['iban']) {
                $updateFields[] = "iban = ?";
                $params[] = $iban;
                $types .= "s";
            }

            if ($blood_type !== null && $blood_type !== $currentProfile['blood_type']) {
                $updateFields[] = "blood_type = ?";
                $params[] = $blood_type;
                $types .= "s";
            }

            if ($theme !== null && $theme !== $currentProfile['theme']) {
                $updateFields[] = "theme = ?";
                $params[] = $theme;
                $types .= "s";
            }

            // Social links özel işlem
            $socialLinksJson = is_array($socialLinks) ? json_encode($socialLinks, JSON_UNESCAPED_UNICODE) : $socialLinks;
            if ($socialLinksJson !== null && $socialLinksJson !== $currentProfile['social_links']) {
                $updateFields[] = "social_links = ?";
                $params[] = $socialLinksJson;
                $types .= "s";
            }

            if ($photoUrl !== null && $photoUrl !== $currentProfile['photo_url']) {
                $updateFields[] = "photo_url = ?";
                $params[] = $photoUrl;
                $types .= "s";
            }

            if ($photoData !== null && $photoData !== $currentProfile['photo_data']) {
                $updateFields[] = "photo_data = ?";
                $params[] = $photoData;
                $types .= "s";
            }

            // Hiç değişiklik yoksa erken çık
            if (empty($updateFields)) {
                $this->debugLog("Değişiklik tespit edilmedi, güncelleme yapılmayacak");
                return true; // Başarılı sayılır çünkü zaten güncel
            }

            // SQL sorgusunu hazırla
            $sql = "UPDATE profiles SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $params[] = $profileId;
            $types .= "i";

            $this->debugLog("SQL Sorgusu", $sql);
            $this->debugLog("Parametre tipleri", $types);
            $this->debugLog("Parametreler", $params);

            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                $this->debugLog("Prepare Hatası", $this->connection->error);
                throw new Exception("Prepare hatası: " . $this->connection->error);
            }

            // bind_param için referans array'i hazırla
            $bindParams = array($types);
            for($i = 0; $i < count($params); $i++) {
                $bindParams[] = &$params[$i];
            }

            $this->debugLog("Bind için hazırlanan parametreler", [
                'types' => $types,
                'params' => $params,
                'bindParams' => $bindParams
            ]);

            // bind_param'i dinamik olarak çağır
            if (!call_user_func_array(array($stmt, 'bind_param'), $bindParams)) {
                $this->debugLog("Bind Hatası", $stmt->error);
                throw new Exception("Parametre bağlama hatası: " . $stmt->error);
            }

            $executeResult = $stmt->execute();
            $this->debugLog("Execute sonucu", $executeResult);
            
            if (!$executeResult) {
                $this->debugLog("MySQL Hatası", [
                    'error' => $stmt->error,
                    'errno' => $stmt->errno
                ]);
                throw new Exception("Güncelleme hatası: " . $stmt->error);
            }

            $this->debugLog("Etkilenen kayıt sayısı", $stmt->affected_rows);
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            $this->debugLog("İşlem sonucu", $success);
            return $success;

        } catch (Exception $e) {
            $this->debugLog("HATA", $e->getMessage());
            throw $e;
        }
    }

    private function debugLog($message, $data = null) {
        $logMessage = "[DEBUG] " . $message;
        if ($data !== null) {
            if (is_array($data) || is_object($data)) {
                $logMessage .= ": " . json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $logMessage .= ": " . var_export($data, true);
            }
        }
        error_log($logMessage);
    }
}
?>
