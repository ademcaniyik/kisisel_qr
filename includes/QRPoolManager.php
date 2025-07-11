<?php
/**
 * QR Pool Manager - Önceden hazırlanmış QR havuzu yönetimi
 * Güvenli URL formatını koruyarak (örn: /qr/7d268b70) QR havuzu işlemleri
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/QRManager.php';

class QRPoolManager {
    /** @var Database */
    private $db;
    private $qrManager;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->qrManager = new QRManager();
    }
    
    /**
     * Yeni QR batch oluştur (100 adet varsayılan)
     * Güvenli rastgele ID'ler kullanarak
     */
    public function createNewBatch($quantity = 100, $batchName = null) {
        try {
            // Batch adı oluştur
            if (!$batchName) {
                $batchCount = $this->db->query("SELECT COUNT(*) as count FROM print_batches")->fetch_assoc()['count'];
                $batchName = 'BATCH' . str_pad($batchCount + 1, 3, '0', STR_PAD_LEFT);
            }
            
            // Başlangıç pool ID'sini belirle
            $lastPoolId = $this->db->query("SELECT pool_id FROM qr_pool ORDER BY id DESC LIMIT 1")->fetch_assoc();
            $startNumber = 1;
            if ($lastPoolId) {
                $lastNumber = (int)str_replace('QR', '', $lastPoolId['pool_id']);
                $startNumber = $lastNumber + 1;
            }
            
            $poolStartId = 'QR' . str_pad($startNumber, 3, '0', STR_PAD_LEFT);
            $poolEndId = 'QR' . str_pad($startNumber + $quantity - 1, 3, '0', STR_PAD_LEFT);
            
            // Batch kaydını oluştur
            $batchStmt = $this->db->prepare("INSERT INTO print_batches (batch_name, pool_start_id, pool_end_id, quantity, status) VALUES (?, ?, ?, ?, 'planned')");
            $batchStmt->bind_param("sssi", $batchName, $poolStartId, $poolEndId, $quantity);
            $batchStmt->execute();
            $batchId = $this->db->insert_id;
            
            // QR'ları oluştur
            $qrData = [];
            for ($i = 0; $i < $quantity; $i++) {
                $poolId = 'QR' . str_pad($startNumber + $i, 3, '0', STR_PAD_LEFT);
                $qrCodeId = $this->generateSecureId(8); // Güvenli rastgele ID
                $editToken = $this->generateSecureId(8); // Düzenleme token
                $editCode = $this->generateEditCode(); // 6 haneli şifre
                
                $qrData[] = [
                    'pool_id' => $poolId,
                    'qr_code_id' => $qrCodeId,
                    'edit_token' => $editToken,
                    'edit_code' => $editCode,
                    'batch_id' => $batchId
                ];
            }
            
            // Toplu insert
            $this->insertQRBatch($qrData);
            
            // Eklenen QR'ların tamamı için görselleri oluştur
            foreach ($qrData as $qr) {
                $this->generateQRImages($qr);
            }
            
            // Batch durumunu güncelle
            $this->db->query("UPDATE print_batches SET status = 'ready_to_print' WHERE id = $batchId");
            
            return [
                'success' => true,
                'batch_id' => $batchId,
                'batch_name' => $batchName,
                'quantity' => $quantity,
                'pool_range' => "$poolStartId - $poolEndId"
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Mevcut QR'dan bir profil atama
     * Sipariş geldiğinde kullanılır
     */
    public function assignAvailableQR($profileId, $orderData = null) {
        try {
            error_log("=== QRPoolManager assignAvailableQR Debug ===");
            error_log("ProfileID to assign: " . $profileId);
            
            // Önce bu profile zaten QR atanmış mı kontrol et (EN ÖNEMLİ KONTROL!)
            $existingQuery = $this->db->prepare("SELECT * FROM qr_pool WHERE profile_id = ? AND status = 'assigned' LIMIT 1");
            $existingQuery->bind_param("i", $profileId);
            $existingQuery->execute();
            $existingResult = $existingQuery->get_result();
            
            if ($existingResult->num_rows > 0) {
                $existing = $existingResult->fetch_assoc();
                error_log("WARNING: Profile $profileId already has QR assigned: " . $existing['qr_code_id']);
                error_log("Returning existing QR instead of creating new one");
                
                // Mevcut QR'ı döndür
                return [
                    'success' => true,
                    'qr_pool_id' => $existing['id'],
                    'pool_id' => $existing['pool_id'],
                    'qr_code_id' => $existing['qr_code_id'],
                    'edit_token' => $existing['edit_token'],
                    'edit_code' => $existing['edit_code'],
                    'profile_url' => $this->getBaseUrl() . '/qr/' . $existing['qr_code_id'],
                    'edit_url' => $this->getBaseUrl() . '/edit/' . $existing['edit_token']
                ];
            }
            
            // Müsait QR bul
            $qrQuery = $this->db->query("SELECT * FROM qr_pool WHERE status = 'available' ORDER BY id ASC LIMIT 1");
            
            error_log("Available QR count: " . $qrQuery->num_rows);
            
            if ($qrQuery->num_rows === 0) {
                error_log("ERROR: No available QR codes in pool!");
                throw new Exception("Hiç müsait QR yok! Yeni batch oluşturun.");
            }
            
            $qrData = $qrQuery->fetch_assoc();
            error_log("Selected QR for assignment: " . json_encode($qrData));
            
            // QR'ı profile ataма
            $updateStmt = $this->db->prepare("UPDATE qr_pool SET status = 'assigned', profile_id = ?, assigned_at = NOW() WHERE id = ?");
            $updateStmt->bind_param("ii", $profileId, $qrData['id']);
            $updateResult = $updateStmt->execute();
            
            error_log("QR Pool update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));
            error_log("Affected rows: " . $updateStmt->affected_rows);
            
            // Mevcut qr_codes tablosuna da kaydet (geriye uyumluluk için)
            $qrCodeStmt = $this->db->prepare("INSERT INTO qr_codes (id, profile_id, created_at) VALUES (?, ?, NOW())");
            $qrCodeStmt->bind_param("si", $qrData['qr_code_id'], $profileId);
            $qrCodeResult = $qrCodeStmt->execute();
            
            error_log("QR Codes table insert result: " . ($qrCodeResult ? 'SUCCESS' : 'FAILED'));
            error_log("=== End QRPoolManager Debug ===");
            
            // QR görseli oluştur (fiziksel basım için)
            $this->generateQRImages($qrData);
            
            return [
                'success' => true,
                'qr_pool_id' => $qrData['id'],
                'pool_id' => $qrData['pool_id'],
                'qr_code_id' => $qrData['qr_code_id'],
                'edit_token' => $qrData['edit_token'],
                'edit_code' => $qrData['edit_code'],
                // Tam kök URL ile döndür
                'profile_url' => $this->getBaseUrl() . '/qr/' . $qrData['qr_code_id'],
                'edit_url' => $this->getBaseUrl() . '/edit/' . $qrData['edit_token']
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
            
        }
    }
    
    /**
     * Stok durumunu kontrol et
     */
    public function getStockStatus() {
        $available = $this->db->query("SELECT COUNT(*) as count FROM qr_pool WHERE status = 'available'")->fetch_assoc()['count'];
        $assigned = $this->db->query("SELECT COUNT(*) as count FROM qr_pool WHERE status = 'assigned'")->fetch_assoc()['count'];
        $delivered = $this->db->query("SELECT COUNT(*) as count FROM qr_pool WHERE status = 'delivered'")->fetch_assoc()['count'];
        $total = $available + $assigned + $delivered;
        
        return [
            'total' => $total,
            'available' => $available,
            'assigned' => $assigned,
            'delivered' => $delivered,
            'stock_warning' => $available < 20 ? true : false
        ];
    }
    
    /**
     * Batch listesini getir
     * @return array<int, array<string, mixed>>
     */
    public function getBatches() {
        $result = $this->db->query("SELECT * FROM print_batches ORDER BY created_at DESC");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    /**
     * Güvenli rastgele ID oluştur (mevcut sistemle uyumlu)
     */
    private function generateSecureId($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        // Benzersizlik kontrolü (qr_code_id için)
        $exists = $this->db->query("SELECT id FROM qr_pool WHERE qr_code_id = '$id' OR edit_token = '$id'")->num_rows > 0;
        if ($exists) {
            return $this->generateSecureId($length); // Recursive retry
        }
        
        return $id;
    }
    
    /**
     * 6 haneli edit code oluştur
     */
    private function generateEditCode() {
        do {
            $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $exists = $this->db->query("SELECT id FROM qr_pool WHERE edit_code = '$code'")->num_rows > 0;
        } while ($exists);
        
        return $code;
    }
    
    /**
     * QR verilerini toplu insert
     */
    private function insertQRBatch($qrData) {
        $values = [];
        $params = [];
        
        foreach ($qrData as $qr) {
            $values[] = "(?, ?, ?, ?, ?, 'available')";
            $params = array_merge($params, [
                $qr['pool_id'],
                $qr['qr_code_id'], 
                $qr['edit_token'],
                $qr['edit_code'],
                $qr['batch_id']
            ]);
        }
        
        $sql = "INSERT INTO qr_pool (pool_id, qr_code_id, edit_token, edit_code, batch_id, status) VALUES " . implode(', ', $values);
        $stmt = $this->db->prepare($sql);
        
        // Tüm parametreler string
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }
    
    /**
     * QR görsellerini oluştur (fiziksel basım için)
     */
    private function generateQRImages($qrData) {
        require_once __DIR__ . '/../config/site.php';
        $profileUrl = getBaseUrl() . '/qr/' . $qrData['qr_code_id'];
        $this->qrManager->generateQRImage($profileUrl, $qrData['qr_code_id']);
        $editUrl = getBaseUrl() . '/edit/' . $qrData['edit_token'];
        $this->qrManager->generateQRImage($editUrl, $qrData['edit_token'] . '_edit', 150); // Küçük boyut
    }
    
    /**
     * Pool'dan QR sil (sadmin paneli için)
     */
    public function deleteFromPool($poolId) {
        try {
            $qrData = $this->db->query("SELECT * FROM qr_pool WHERE id = $poolId")->fetch_assoc();
            
            if ($qrData['status'] === 'assigned') {
                throw new Exception("Atanmış QR silinemez!");
            }
            
            // Pool'dan sil
            $this->db->query("DELETE FROM qr_pool WHERE id = $poolId");
            
            // QR görsellerini sil
            @unlink("public/qr_codes/" . $qrData['qr_code_id'] . ".png");
            @unlink("public/qr_codes/" . $qrData['edit_token'] . "_edit.png");
            
            return ['success' => true];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * QR'ı delivered olarak işaretle
     */
    public function markAsDelivered($poolId) {
        $stmt = $this->db->prepare("UPDATE qr_pool SET status = 'delivered', delivered_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $poolId);
        return $stmt->execute();
    }
    
    /**
     * Batch için QR görsellerini ZIP dosyası olarak hazırla
     * @param int $batchId
     * @return string|false
     */
    public function createQRBatchZip($batchId) {
        try {
            $batchData = $this->db->query("SELECT * FROM print_batches WHERE id = $batchId")->fetch_assoc();
            if (!$batchData) {
                throw new Exception("Batch bulunamadı");
            }
            
            $qrs = $this->db->query("SELECT * FROM qr_pool WHERE batch_id = $batchId ORDER BY pool_id")->fetch_all(MYSQLI_ASSOC);
            if (empty($qrs)) {
                throw new Exception("Batch'te QR bulunamadı");
            }
            
            // ZIP dosyası oluştur
            $zipFileName = "QR_Batch_" . $batchData['batch_name'] . ".zip";
            $zipPath = __DIR__ . "/../public/downloads/" . $zipFileName;

            // Eğer dosya zaten varsa yeniden oluşturma, doğrudan yolu döndür
            // DÜZELTME: /admin ifadesini kaldırarak doğru kök yolu oluştur
            $basePath = rtrim(str_replace('/admin', '', dirname($_SERVER['SCRIPT_NAME'])), '/');
            $webPath = $basePath . '/public/downloads/' . $zipFileName;
            if (file_exists($zipPath)) {
                return $webPath;
            }
            
            // Downloads klasörünü oluştur
            if (!file_exists(__DIR__ . "/../public/downloads/")) {
                mkdir(__DIR__ . "/../public/downloads/", 0777, true);
            }
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                throw new Exception("ZIP dosyası oluşturulamadı");
            }
            
            // Her QR için dosyaları ekle
            foreach ($qrs as $qr) {
                // Ana QR görseli
                $profileQRPath = __DIR__ . "/../public/qr_codes/" . $qr['qr_code_id'] . ".png";
                if (file_exists($profileQRPath)) {
                    $zip->addFile($profileQRPath, "Profile_QRs/" . $qr['pool_id'] . "_profile.png");
                }
                
                // Edit QR görseli
                $editQRPath = __DIR__ . "/../public/qr_codes/" . $qr['edit_token'] . "_edit.png";
                if (file_exists($editQRPath)) {
                    $zip->addFile($editQRPath, "Edit_QRs/" . $qr['pool_id'] . "_edit.png");
                }
            }
            
            // QR listesi CSV dosyası oluştur
            $csvContent = "Pool ID,QR Code ID,Edit Token,Edit Code,Profile URL,Edit URL\n";
            foreach ($qrs as $qr) {
                $baseUrl = $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
                $profileUrl = "https://$baseUrl/qr/" . $qr['qr_code_id'];
                $editUrl = "https://$baseUrl/edit/" . $qr['edit_token'];
                
                $csvContent .= implode(',', [
                    $qr['pool_id'],
                    $qr['qr_code_id'],
                    $qr['edit_token'],
                    $qr['edit_code'],
                    $profileUrl,
                    $editUrl
                ]) . "\n";
            }
            
            $zip->addFromString("QR_List.csv", $csvContent);
            
            // Batch bilgileri README
            $readmeContent = "QR BATCH BİLGİLERİ\n";
            $readmeContent .= "==================\n\n";
            $readmeContent .= "Batch Adı: " . $batchData['batch_name'] . "\n";
            $readmeContent .= "QR Aralığı: " . $batchData['pool_start_id'] . " - " . $batchData['pool_end_id'] . "\n";
            $readmeContent .= "Miktar: " . $batchData['quantity'] . " QR\n";
            $readmeContent .= "Oluşturma Tarihi: " . $batchData['created_at'] . "\n";
            $readmeContent .= "İndirme Tarihi: " . date('Y-m-d H:i:s') . "\n\n";
            $readmeContent .= "KLASÖR YAPISI:\n";
            $readmeContent .= "- Profile_QRs/: Ana profil QR'ları (büyük boyut)\n";
            $readmeContent .= "- Edit_QRs/: Düzenleme QR'ları (küçük boyut)\n";
            $readmeContent .= "- QR_List.csv: Tüm QR bilgileri ve URL'leri\n";
            
            $zip->addFromString("README.txt", $readmeContent);
            $zip->close();
            return $webPath;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Tüm QR'ları listele (sayfalama ile)
     */
    public function getQRList($page = 1, $limit = 20, $status = 'all') {
        $offset = ($page - 1) * $limit;
        $whereClause = '';
        
        if ($status !== 'all') {
            $whereClause = "WHERE status = '" . $this->db->getConnection()->real_escape_string($status) . "'";
        }
        
        $totalQuery = "SELECT COUNT(*) as total FROM qr_pool $whereClause";
        $total = $this->db->query($totalQuery)->fetch_assoc()['total'];
        
        $qrQuery = "SELECT qp.*, pb.batch_name FROM qr_pool qp 
                   LEFT JOIN print_batches pb ON qp.batch_id = pb.id 
                   $whereClause 
                   ORDER BY qp.id DESC 
                   LIMIT $limit OFFSET $offset";
        
        $qrs = $this->db->query($qrQuery)->fetch_all(MYSQLI_ASSOC);
        
        return [
            'qrs' => $qrs,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ];
    }
    
    /**
     * QR durumunu güncelle
     */
    public function updateQRStatus($qrId, $status) {
        $validStatuses = ['available', 'assigned', 'delivered'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'error' => 'Geçersiz durum'];
        }
        
        $stmt = $this->db->prepare("UPDATE qr_pool SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $qrId);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Güncelleme başarısız'];
        }
    }
    
    /**
     * Batch durumunu güncelle
     */
    public function updateBatchStatus($batchId, $status) {
        $validStatuses = ['planned', 'ready_to_print', 'printed', 'stocked'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'error' => 'Geçersiz durum'];
        }
        
        $updateData = ['status' => $status];
        if ($status === 'printed') {
            $updateData['printed_at'] = date('Y-m-d H:i:s');
        }
        
        $stmt = $this->db->prepare("UPDATE print_batches SET status = ?, printed_at = ? WHERE id = ?");
        $printedAt = $updateData['printed_at'] ?? null;
        $stmt->bind_param("ssi", $status, $printedAt, $batchId);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Güncelleme başarısız'];
        }
    }
    
    /**
     * Duplicate QR atamaları temizle
     * Bir profile birden fazla QR atandığında kullanılır
     */
    public function cleanupDuplicateAssignments($profileId = null) {
        try {
            $sql = "SELECT profile_id, COUNT(*) as qr_count FROM qr_pool WHERE profile_id IS NOT NULL";
            if ($profileId) {
                $sql .= " AND profile_id = ?";
            }
            $sql .= " GROUP BY profile_id HAVING COUNT(*) > 1";
            
            $stmt = $this->db->prepare($sql);
            if ($profileId) {
                $stmt->bind_param("i", $profileId);
            }
            $stmt->execute();
            $duplicates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $cleaned = 0;
            foreach ($duplicates as $duplicate) {
                $profileId = $duplicate['profile_id'];
                
                // En eski atamayı tut, diğerlerini temizle
                $keepQuery = $this->db->prepare("SELECT id FROM qr_pool WHERE profile_id = ? ORDER BY assigned_at ASC LIMIT 1");
                $keepQuery->bind_param("i", $profileId);
                $keepQuery->execute();
                $keepId = $keepQuery->get_result()->fetch_assoc()['id'];
                
                // Diğer atamaları temizle
                $cleanupStmt = $this->db->prepare("UPDATE qr_pool SET profile_id = NULL, status = 'available', assigned_at = NULL WHERE profile_id = ? AND id != ?");
                $cleanupStmt->bind_param("ii", $profileId, $keepId);
                $cleanupStmt->execute();
                $cleaned += $cleanupStmt->affected_rows;
                
                error_log("Profile $profileId: Kept QR ID $keepId, cleaned " . $cleanupStmt->affected_rows . " duplicates");
            }
            
            return [
                'success' => true,
                'profiles_cleaned' => count($duplicates),
                'qr_codes_freed' => $cleaned
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Profil silindiğinde o profile atanmış QR'ı tekrar müsait duruma çevir
     * @param int $profileId Silinecek profil ID'si
     * @return array İşlem sonucu
     */
    public function unassignProfileQR($profileId) {
        try {
            error_log("=== QRPoolManager unassignProfileQR Debug ===");
            error_log("Removing QR assignment for profile: " . $profileId);
            
            // İlk olarak bu profile atanmış QR'ları bul
            $stmt = $this->db->prepare("SELECT id, pool_id, qr_code_id FROM qr_pool WHERE profile_id = ? AND status = 'assigned'");
            $stmt->bind_param("i", $profileId);
            $stmt->execute();
            $result = $stmt->get_result();
            $assignedQRs = $result->fetch_all(MYSQLI_ASSOC);
            
            if (empty($assignedQRs)) {
                error_log("No assigned QR codes found for profile: " . $profileId);
                return [
                    'success' => true,
                    'message' => 'Bu profile atanmış QR kodu bulunamadı',
                    'unassigned_count' => 0
                ];
            }
            
            // QR atamalarını kaldır ve durumu 'available' yap
            $updateStmt = $this->db->prepare("UPDATE qr_pool SET profile_id = NULL, status = 'available', assigned_at = NULL WHERE profile_id = ?");
            $updateStmt->bind_param("i", $profileId);
            
            if ($updateStmt->execute()) {
                $unassignedCount = $updateStmt->affected_rows;
                error_log("Successfully unassigned {$unassignedCount} QR codes from profile: " . $profileId);
                
                // Log the unassigned QR codes
                foreach ($assignedQRs as $qr) {
                    error_log("QR {$qr['pool_id']} (ID: {$qr['qr_code_id']}) is now available again");
                }
                
                return [
                    'success' => true,
                    'message' => "{$unassignedCount} adet QR kodu tekrar müsait duruma getirildi",
                    'unassigned_count' => $unassignedCount,
                    'qr_codes' => array_column($assignedQRs, 'pool_id')
                ];
            } else {
                error_log("Failed to unassign QR codes for profile: " . $profileId);
                return [
                    'success' => false,
                    'error' => 'QR atamalarını kaldırırken bir hata oluştu'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Exception in unassignProfileQR: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'QR ataması kaldırılırken hata: ' . $e->getMessage()
            ];
        }
    }

    // Yardımcı fonksiyon ekle:
    private function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        // admin, api, vs. ne olursa olsun kök dizini bul
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = preg_replace('#/(admin|api)(/.*)?$#', '', dirname($scriptName));
        $basePath = rtrim($basePath, '/');
        return $protocol . '://' . $host . ($basePath ? $basePath : '');
    }
}
