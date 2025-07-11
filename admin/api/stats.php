<?php
// admin/api/stats.php
// Tüm istatistik işlemleri (dashboard, device, scan) tek dosyada yönetilir.

// CORS headers for analytics tracking
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/utilities.php';

// Session'ı güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_REQUEST['action'] ?? '';

// Analytics tracking endpoints için tüm güvenlik kontrollerini bypass et
$analyticsActions = ['track_event', 'track_order_funnel'];
$isAnalyticsAction = in_array($action, $analyticsActions);

// Skip admin check for analytics tracking endpoints
if (!$isAnalyticsAction) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
        exit();
    }
}

// --- RATE LIMITING (Skip for analytics) ---
if (!$isAnalyticsAction && !Utilities::rateLimit('stats_api_' . ($_SERVER['REMOTE_ADDR'] ?? 'guest'), 60, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok fazla istek. Lütfen daha sonra tekrar deneyin.']);
    exit();
}

// --- CSRF KORUMASI (Skip for analytics) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isAnalyticsAction) {
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
        case 'dashboard':
            // Dashboard istatistikleri
            $stats = [
                'totalProfiles' => 0,
                'totalScans' => 0,
                'todayScans' => 0,
                'activeProfiles' => 0
            ];
            $query = "SELECT COUNT(*) as total FROM profiles";
            $result = $connection->query($query);
            $stats['totalProfiles'] = $result->fetch_assoc()['total'];
            $query = "SELECT COUNT(*) as total FROM scan_statistics";
            $result = $connection->query($query);
            $stats['totalScans'] = $result->fetch_assoc()['total'];
            $query = "SELECT COUNT(*) as total FROM scan_statistics WHERE DATE(scan_date) = CURDATE()";
            $result = $connection->query($query);
            $stats['todayScans'] = $result->fetch_assoc()['total'];
            $query = "SELECT COUNT(*) as total FROM profiles WHERE status = 'active'";
            $result = $connection->query($query);
            $stats['activeProfiles'] = $result->fetch_assoc()['total'];
            header('Content-Type: application/json');
            echo json_encode($stats);
            break;
        case 'device':
            // Cihaz dağılımı istatistikleri
            $query = "SELECT 
                        CASE 
                            WHEN user_agent LIKE '%Mobile%' THEN 'Mobil'
                            WHEN user_agent LIKE '%Tablet%' THEN 'Tablet'
                            ELSE 'Masaüstü'
                        END as device_type,
                        COUNT(*) as count
                      FROM scan_statistics 
                      GROUP BY device_type";
            $result = $connection->query($query);
            $data = [
                'labels' => [],
                'values' => []
            ];
            while ($row = $result->fetch_assoc()) {
                $data['labels'][] = $row['device_type'];
                $data['values'][] = (int)$row['count'];
            }
            header('Content-Type: application/json');
            echo json_encode($data);
            break;
        case 'scan':
            // Son 7 günün tarama istatistikleri
            $query = "SELECT DATE(scan_date) as date, COUNT(*) as count 
                      FROM scan_statistics 
                      WHERE scan_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                      GROUP BY DATE(scan_date)
                      ORDER BY date ASC";
            $result = $connection->query($query);
            $data = [
                'labels' => [],
                'values' => []
            ];
            while ($row = $result->fetch_assoc()) {
                $data['labels'][] = date('d M', strtotime($row['date']));
                $data['values'][] = (int)$row['count'];
            }
            header('Content-Type: application/json');
            echo json_encode($data);
            break;
        case 'track_event':
            // Analytics event tracking (allow without CSRF for frontend tracking)
            $input = json_decode(file_get_contents('php://input'), true);
            $eventType = $input['event_type'] ?? '';
            $eventName = $input['event_name'] ?? '';
            $eventData = $input['event_data'] ?? null;
            
            if ($eventType && $eventName) {
                require_once __DIR__ . '/../../includes/AnalyticsManager.php';
                $analytics = new AnalyticsManager();
                $result = $analytics->trackEvent($eventType, $eventName, $eventData);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing event parameters']);
            }
            break;
        case 'track_order_funnel':
            // Analytics order funnel tracking (allow without CSRF for frontend tracking)
            $input = json_decode(file_get_contents('php://input'), true);
            $step = $input['step'] ?? '';
            $stepData = $input['step_data'] ?? null;
            
            if ($step) {
                require_once __DIR__ . '/../../includes/AnalyticsManager.php';
                $analytics = new AnalyticsManager();
                $result = $analytics->trackOrderFunnel($step, $stepData);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing step parameter']);
            }
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
