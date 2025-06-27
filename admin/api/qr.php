<?php
// admin/api/qr.php
// Tüm QR işlemleri (create, delete, stats) tek dosyada yönetilir.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/utilities.php';
require_once __DIR__ . '/../../includes/QRManager.php';

// Session'ı güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit();
}

$action = $_REQUEST['action'] ?? '';

// --- RATE LIMITING ---
if (!Utilities::rateLimit('qr_api_' . ($_SERVER['REMOTE_ADDR'] ?? 'guest'), 60, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok fazla istek. Lütfen daha sonra tekrar deneyin.']);
    exit();
}
// --- CSRF KORUMASI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!Utilities::validateCsrfToken($csrfToken)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Geçersiz CSRF token']);
        exit();
    }
}

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();

    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $profileId = intval($_POST['profileId'] ?? 0);
            if ($profileId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçerli bir profil seçmelisiniz']);
                exit();
            }
            $qrManager = new QRManager();
            $result = $qrManager->createQR($profileId);
            echo json_encode($result);
            break;
        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $id = Utilities::sanitizeInput($_POST['id'] ?? '');
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID gerekli']);
                exit();
            }
            $qrManager = new QRManager();
            $result = $qrManager->deleteQR($id);
            echo json_encode($result);
            break;
        case 'stats':
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $qrId = isset($_GET['qr_id']) ? Utilities::sanitizeInput($_GET['qr_id']) : null;
            if (!$qrId) {
                echo json_encode(['success' => false, 'message' => 'QR kod ID gerekli']);
                exit();
            }
            $stmt = $connection->prepare("
                SELECT COUNT(*) as total_scans,
                SUM(CASE WHEN scan_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as last_24h,
                SUM(CASE WHEN scan_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as last_7d
                FROM scan_statistics 
                WHERE qr_id = ?
            ");
            $stmt->bind_param("s", $qrId);
            $stmt->execute();
            $result = $stmt->get_result();
            $stats = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_scans' => (int)$stats['total_scans'],
                    'last_24h' => (int)$stats['last_24h'],
                    'last_7d' => (int)$stats['last_7d']
                ]
            ]);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Geçersiz action parametresi']);
            exit();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası', 'error' => $e->getMessage()]);
    exit();
}
