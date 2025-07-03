<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim']);
    exit();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/QRPoolManager.php';
$qrPoolManager = new QRPoolManager();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'download_qr_batch':
        $batchId = (int)($_POST['batch_id'] ?? 0);
        if ($batchId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Geçersiz batch ID']);
            exit();
        }
        $zipPath = $qrPoolManager->createQRBatchZip($batchId);
        if ($zipPath) {
            echo json_encode(['success' => true, 'download_url' => $zipPath]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ZIP oluşturulamadı']);
        }
        exit();
        
    case 'unassign_qr':
        $qrId = (int)($_POST['qr_id'] ?? 0);
        if ($qrId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Geçersiz QR ID']);
            exit();
        }
        
        // QR'ı müsait duruma çevir
        $result = $qrPoolManager->updateQRStatus($qrId, 'available');
        
        if ($result['success']) {
            // QR'ın profile bağlantısını kaldır
            require_once __DIR__ . '/../../config/database.php';
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE qr_pool SET profile_id = NULL, assigned_at = NULL WHERE id = ?");
            $stmt->bind_param("i", $qrId);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'QR kodu başarıyla müsait duruma getirildi']);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
        exit();
    
    // Diğer işlemler buraya eklenebilir
    default:
        echo json_encode(['success' => false, 'error' => 'Bilinmeyen işlem']);
        exit();
}
