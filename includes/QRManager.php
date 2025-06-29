<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/utilities.php';
require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }    public function createQR($profileId) {
        // Profil ID'sinin geçerliliğini kontrol et
        $stmt = $this->db->prepare("SELECT id FROM profiles WHERE id = ?");
        $stmt->bind_param("i", $profileId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Geçersiz profil ID'];
        }

        // Benzersiz bir QR kodu oluştur
        $qrId = Utilities::generateUniqueId(8); // 8 karakterlik kısa bir ID
        
        // QR kodunu veritabanına kaydet
        $stmt = $this->db->prepare("INSERT INTO qr_codes (id, profile_id) VALUES (?, ?)");
        $stmt->bind_param("si", $qrId, $profileId);

        if ($stmt->execute()) {            // QR kod görselini oluştur
            $qrImagePath = __DIR__ . "/../public/qr_codes/{$qrId}.png";
            $qrUrl = "https://acdisoftware.com.tr/kisisel_qr/qr/" . $qrId;
            
            if (!file_exists(__DIR__ . "/../public/qr_codes/")) {
                mkdir(__DIR__ . "/../public/qr_codes/", 0777, true);
            }

            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => false
            ]);

            $qrcode = new QRCode($options);
            $qrcode->render($qrUrl, $qrImagePath);

            return [
                'success' => true,
                'qr_id' => $qrId,
                'qr_image' => "/public/qr_codes/{$qrId}.png"
            ];
        }

        return ['success' => false, 'message' => 'QR kod oluşturulurken bir hata oluştu'];
    }

    public function updateQR($qrId, $newRedirectUrl) {
        if (!Utilities::isValidUrl($newRedirectUrl)) {
            return ['success' => false, 'message' => 'Geçersiz URL formatı'];
        }

        $stmt = $this->db->prepare("UPDATE qr_codes SET redirect_url = ? WHERE id = ?");
        $stmt->bind_param("ss", $newRedirectUrl, $qrId);

        if ($stmt->execute()) {
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'QR kod güncellenirken bir hata oluştu'];
    }    public function deleteQR($qrId) {
        try {
            // QR kodu sil
            $stmt = $this->db->prepare("DELETE FROM qr_codes WHERE id = ?");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Sorgu hazırlanamadı'];
            }
            
            $stmt->bind_param("s", $qrId);
            if (!$stmt->execute()) {
                $stmt->close();
                return ['success' => false, 'message' => 'QR kod silinemedi'];
            }
            
            if ($stmt->affected_rows === 0) {
                $stmt->close();
                return ['success' => false, 'message' => 'QR kod bulunamadı'];
            }
            
            $stmt->close();
            
            // İlişkili kayıtları sil
            $this->db->query("DELETE FROM scan_statistics WHERE qr_id = '$qrId'");
            
            // QR görselini sil
            $qrImagePath = __DIR__ . "/../public/qr_codes/{$qrId}.png";
            if (file_exists($qrImagePath)) {
                @unlink($qrImagePath);
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Bir hata oluştu'];
        }
    }

    public function getQRInfo($qrId) {
        $stmt = $this->db->prepare("SELECT * FROM qr_codes WHERE id = ?");
        $stmt->bind_param("s", $qrId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function getAllQRCodes() {
        $query = "SELECT qr.*, 
                 (SELECT COUNT(*) FROM scan_statistics WHERE qr_id = qr.id) as scan_count 
                 FROM qr_codes qr 
                 ORDER BY created_at DESC";
        
        $result = $this->db->query($query);
        $qrCodes = [];

        while ($row = $result->fetch_assoc()) {
            $qrCodes[] = $row;
        }

        return $qrCodes;
    }

    public function getQRStatistics($qrId) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(scan_time) as scan_date,
                COUNT(*) as scan_count,
                GROUP_CONCAT(DISTINCT ip_address) as unique_ips
            FROM scan_statistics 
            WHERE qr_id = ?
            GROUP BY DATE(scan_time)
            ORDER BY scan_date DESC
        ");
        
        $stmt->bind_param("s", $qrId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $statistics = [];
        while ($row = $result->fetch_assoc()) {
            $statistics[] = $row;
        }
        
        return $statistics;
    }
    
    /**
     * QR görsel dosyası oluştur (QRPoolManager için)
     */
    public function generateQRImage($url, $filename, $scale = 5) {
        $qrImagePath = __DIR__ . "/../public/qr_codes/{$filename}.png";
        
        if (!file_exists(__DIR__ . "/../public/qr_codes/")) {
            mkdir(__DIR__ . "/../public/qr_codes/", 0777, true);
        }

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => $scale,
            'imageBase64' => false
        ]);

        $qrcode = new QRCode($options);
        $qrcode->render($url, $qrImagePath);
        
        return file_exists($qrImagePath);
    }
}
?>
