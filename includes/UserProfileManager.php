<?php
/**
 * Kullanıcı Profil Yönetim Sınıfı
 * Kullanıcı tarafından yapılan profil düzenleme işlemlerini yönetir
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/utilities.php';
require_once __DIR__ . '/ImageOptimizer.php';

class UserProfileManager {
    private $db;
    private $connection;
    private $logFile;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
        $this->logFile = __DIR__ . '/../logs/user_profile_edit.log';
    }
    
    /**
     * QR token ile profil bilgilerini getir
     */
    public function getProfileByEditToken($editToken) {
        try {
            $this->log("Profil bilgileri getiriliyor. Token: " . $editToken);
            
            $sql = "SELECT p.*, q.edit_code 
                   FROM profiles p 
                   INNER JOIN qr_pool q ON q.profile_id = p.id 
                   WHERE q.edit_token = ?";
                   
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL prepare hatası: " . $this->connection->error);
            }
            
            $stmt->bind_param("s", $editToken);
            $stmt->execute();
            $result = $stmt->get_result();
            $profile = $result->fetch_assoc();
            $stmt->close();
            
            if (!$profile) {
                $this->log("Profil bulunamadı! Token: " . $editToken);
                return null;
            }
            
            $this->log("Profil bulundu. ID: " . $profile['id']);
            return $profile;
            
        } catch (Exception $e) {
            $this->log("HATA: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Kullanıcı profilini güncelle
     */
    public function updateProfile($editToken, $data) {
        try {
            $this->log("Profil güncelleme başladı. Token: " . $editToken);
            $this->log("Gelen veriler:", $data);
            
            // Önce profili al
            $profile = $this->getProfileByEditToken($editToken);
            if (!$profile) {
                throw new Exception("Profil bulunamadı");
            }
            
            // Telefon numarasını formatla
            if (!empty($data['phone'])) {
                $countryCode = $data['country_code'] ?? '+90';
                $data['phone'] = $this->formatPhoneNumber($data['phone'], $countryCode);
                $this->log("Telefon formatlandı: " . $data['phone']);
            }
            
            // IBAN formatını kontrol et
            if (!empty($data['iban']) && !$this->validateIban($data['iban'])) {
                throw new Exception("Geçersiz IBAN formatı");
            }
            
            // Sosyal medya bağlantılarını işle
            $socialLinks = [];
            if (!empty($data['social_links'])) {
                if (is_string($data['social_links'])) {
                    $socialLinks = json_decode($data['social_links'], true) ?: [];
                } else {
                    $socialLinks = $data['social_links'];
                }
            }
            
            // Değişiklikleri tespit et
            $updateFields = [];
            $params = [];
            $types = "";
            
            // Ad değiştirilemez, sadece log
            if (isset($data['name']) && $data['name'] !== $profile['name']) {
                $this->log("UYARI: Ad değiştirme girişimi - izin verilmedi");
            }
            
            // Telefon kontrolü
            if (!empty($data['phone']) && $data['phone'] !== $profile['phone']) {
                $this->log("Telefon değişiyor: '{$profile['phone']}' -> '{$data['phone']}'");
                $updateFields[] = "phone = ?";
                $params[] = $data['phone'];
                $types .= "s";
            } else {
                $this->log("Telefon değişmedi veya boş: mevcut='{$profile['phone']}', yeni='{$data['phone']}'");
            }
            
            // Diğer alanlar
            $fieldMap = [
                'bio' => 's',
                'iban' => 's',
                'blood_type' => 's',
                'theme' => 's'
            ];
            
            foreach ($fieldMap as $field => $type) {
                if (isset($data[$field]) && $data[$field] !== $profile[$field]) {
                    $this->log("'{$field}' değişiyor: '{$profile[$field]}' -> '{$data[$field]}'");
                    $updateFields[] = "{$field} = ?";
                    $params[] = $data[$field];
                    $types .= $type;
                } else {
                    $this->log("'{$field}' değişmedi: mevcut='{$profile[$field]}', yeni='" . ($data[$field] ?? 'null') . "'");
                }
            }
            
            // Sosyal medya bağlantıları
            $newSocialLinksJson = json_encode($socialLinks, JSON_UNESCAPED_UNICODE);
            if ($newSocialLinksJson !== $profile['social_links']) {
                $this->log("Sosyal medya değişiyor");
                $this->log("Eski: " . $profile['social_links']);
                $this->log("Yeni: " . $newSocialLinksJson);
                $updateFields[] = "social_links = ?";
                $params[] = $newSocialLinksJson;
                $types .= "s";
            } else {
                $this->log("Sosyal medya değişmedi");
            }
            
            // Fotoğraf yükleme işlemi
            if (isset($data['photo']) && $data['photo']['error'] === UPLOAD_ERR_OK) {
                $imageOptimizer = new ImageOptimizer();
                $photoResult = $imageOptimizer->uploadAndOptimize($data['photo']);
                
                if ($photoResult['success']) {
                    // Eski fotoğrafı sil (eğer varsa)
                    if (!empty($profile['photo_data'])) {
                        $oldPhotoData = json_decode($profile['photo_data'], true);
                        if ($oldPhotoData && !empty($oldPhotoData['filename'])) {
                            $this->log("Eski fotoğraf siliniyor: " . $oldPhotoData['filename']);
                            $deleteResult = $imageOptimizer->deleteImageFiles($oldPhotoData['filename']);
                            $this->log("Silme sonucu:", $deleteResult);
                        }
                    }
                    
                    $photoData = [
                        'filename' => $photoResult['filename'],
                        'original_name' => $data['photo']['name'],
                        'size' => $data['photo']['size'],
                        'mime_type' => $photoResult['mime_type'] ?? 'image/jpeg',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $updateFields[] = "photo_url = ?";
                    $params[] = '/kisisel_qr/public/uploads/profiles/' . $photoResult['filename'];
                    $types .= "s";
                    
                    $updateFields[] = "photo_data = ?";
                    $params[] = json_encode($photoData, JSON_UNESCAPED_UNICODE);
                    $types .= "s";
                    
                    $this->log("Yeni fotoğraf eklendi: " . $photoResult['filename']);
                }
            }
            
            // Fotoğraf aksiyon işlemleri (gizle/sil)
            if (!empty($data['photo_action'])) {
                $this->log("Fotoğraf aksiyonu: " . $data['photo_action']);
                
                if ($data['photo_action'] === 'hide') {
                    // Fotoğrafı gizle (URL'yi null yap ama dosyayı silme)
                    $updateFields[] = "photo_url = ?";
                    $params[] = null;
                    $types .= "s";
                    $this->log("Fotoğraf gizlendi");
                    
                } elseif ($data['photo_action'] === 'delete') {
                    // Fotoğrafı kalıcı olarak sil
                    if (!empty($profile['photo_data'])) {
                        $oldPhotoData = json_decode($profile['photo_data'], true);
                        if ($oldPhotoData && !empty($oldPhotoData['filename'])) {
                            $this->log("Fotoğraf kalıcı olarak siliniyor: " . $oldPhotoData['filename']);
                            $imageOptimizer = new ImageOptimizer();
                            $deleteResult = $imageOptimizer->deleteImageFiles($oldPhotoData['filename']);
                            $this->log("Silme sonucu:", $deleteResult);
                        }
                    }
                    
                    // Veritabanından da temizle
                    $updateFields[] = "photo_url = ?";
                    $params[] = null;
                    $types .= "s";
                    
                    $updateFields[] = "photo_data = ?";
                    $params[] = null;
                    $types .= "s";
                    
                    $this->log("Fotoğraf kalıcı olarak silindi");
                }
            }
            
            // Değişiklik yoksa çık
            if (empty($updateFields)) {
                $this->log("Değişiklik tespit edilmedi");
                return true;
            }
            
            // SQL hazırla
            $sql = "UPDATE profiles SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $params[] = $profile['id'];
            $types .= "i";
            
            $this->log("SQL: " . $sql);
            $this->log("Params:", $params);
            
            // Güncelleme yap
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("SQL prepare hatası: " . $this->connection->error);
            }
            
            // Parametreleri bağla
            $bindParams = array_merge([$types], $params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($bindParams));
            
            // Çalıştır
            if (!$stmt->execute()) {
                throw new Exception("Güncelleme hatası: " . $stmt->error);
            }
            
            $success = $stmt->affected_rows >= 0;
            $stmt->close();
            
            $this->log("Güncelleme " . ($success ? "başarılı" : "başarısız"));
            return $success;
            
        } catch (Exception $e) {
            $this->log("HATA: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Telefon numarası formatlama
     */
    private function formatPhoneNumber($phone, $countryCode = '+90') {
        // Sadece rakamları al
        $number = preg_replace('/\D+/', '', $phone);
        
        // Boşsa olduğu gibi döndür
        if (empty($number)) {
            return $phone;
        }
        
        // Ülke kodu kontrolü - eğer numarada ülke kodu varsa çıkar
        $countryCodeNumber = str_replace('+', '', $countryCode);
        if (strpos($number, $countryCodeNumber) === 0) {
            $number = substr($number, strlen($countryCodeNumber));
        }
        
        // Başındaki 0'ı kaldır
        if (strpos($number, '0') === 0) {
            $number = substr($number, 1);
        }
        
        // Türkiye için özel kontrol
        if ($countryCode === '+90' && strlen($number) === 10) {
            return '+90' . $number;
        }
        
        // Diğer ülkeler için
        return $countryCode . $number;
    }
    
    /**
     * IBAN doğrulama
     */
    private function validateIban($iban) {
        // Boşlukları kaldır
        $iban = str_replace(' ', '', strtoupper($iban));
        
        // TR IBAN format kontrolü
        if (!preg_match('/^TR\d{24}$/', $iban)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Log kayıtları
     */
    private function log($message, $data = null) {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] $message";
        
        if ($data !== null) {
            $logMessage .= "\nData: " . (is_array($data) || is_object($data) 
                ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) 
                : $data);
        }
        
        $logMessage .= "\n";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        error_log($logMessage); // Aynı zamanda PHP error log'a da yaz
    }
    
    /**
     * bind_param için referans array'i oluştur
     */
    private function refValues($arr) {
        $refs = array();
        foreach($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }
}
