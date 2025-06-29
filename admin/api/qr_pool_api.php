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
    // Diğer işlemler buraya eklenebilir
    default:
        echo json_encode(['success' => false, 'error' => 'Bilinmeyen işlem']);
        exit();
}
