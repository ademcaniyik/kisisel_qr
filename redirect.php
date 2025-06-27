<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/utilities.php';

// QR kod ID'sini kontrol et
$qrId = isset($_GET['qr_id']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['qr_id']) : null;

if (!$qrId) {
    error_log('Geçersiz QR yönlendirme denemesi: qr_id=' . ($_GET['qr_id'] ?? '') . ' IP=' . $_SERVER['REMOTE_ADDR']);
    header('HTTP/1.1 400 Bad Request');
    die('Geçersiz QR kod.');
}

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();

    // QR kod ve profil bilgilerini al
    $stmt = $connection->prepare("
        SELECT qr.id as qr_id, qr.profile_id, qr.is_active
        FROM qr_codes qr
        WHERE qr.id = ?
    ");

    $stmt->bind_param("s", $qrId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        error_log('QR kod bulunamadı: qr_id=' . $qrId . ' IP=' . $_SERVER['REMOTE_ADDR']);
        header('HTTP/1.1 404 Not Found');
        die('QR kod bulunamadı.');
    }

    $qrInfo = $result->fetch_assoc();

    // QR kod aktif değilse hata ver
    if (!$qrInfo['is_active']) {
        error_log('Pasif QR kod erişimi: qr_id=' . $qrId . ' IP=' . $_SERVER['REMOTE_ADDR']);
        header('HTTP/1.1 403 Forbidden');
        die('Bu QR kod artık aktif değil.');
    }

    // Tarama istatistiğini kaydet
    Utilities::logScan($qrId);    // Profil sayfasına yönlendir
    header('Location: /kisisel_qr_canli/qr/' . $qrId);
    exit();

} catch (Exception $e) {
    error_log("QR Yönlendirme Hatası: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    die('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
}
?>
