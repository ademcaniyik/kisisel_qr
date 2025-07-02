<?php
/**
 * Analytics API Endpoint
 * Frontend'den gelen analytics verilerini işler
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight request handling
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../includes/AnalyticsManager.php';

try {
    $analytics = new AnalyticsManager();
    
    // JSON data al
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // FormData ile gelen veriyi de destekle
    if (!$data && isset($_POST['action'])) {
        $data = [
            'action' => $_POST['action'],
            'data' => json_decode($_POST['data'], true)
        ];
    }
    
    if (!$data || !isset($data['action'])) {
        throw new Exception('Invalid request data');
    }
    
    $action = $data['action'];
    $eventData = $data['data'] ?? [];
    
    switch ($action) {
        case 'track_page_view':
            $pageUrl = $eventData['page'] ?? $_SERVER['REQUEST_URI'] ?? '/';
            $analytics->trackPageVisit($pageUrl);
            
            $response = [
                'success' => true,
                'message' => 'Page view tracked',
                'data' => ['page' => $pageUrl]
            ];
            break;
            
        case 'track_user_action':
            $actionType = $eventData['action_type'] ?? 'user_interaction';
            $details = $eventData['details'] ?? null;
            $pageUrl = $eventData['page_url'] ?? $_SERVER['REQUEST_URI'] ?? '/';
            
            $analytics->trackEvent('user_action', $actionType, $details, $pageUrl);
            
            $response = [
                'success' => true,
                'message' => 'User action tracked',
                'data' => ['action' => $actionType]
            ];
            break;
            
        case 'track_order_funnel':
            $step = $eventData['step'] ?? 'unknown';
            $details = $eventData['details'] ?? null;
            
            $analytics->trackOrderFunnel($step, $details);
            
            $response = [
                'success' => true,
                'message' => 'Order funnel tracked',
                'data' => ['step' => $step]
            ];
            break;
            
        case 'track_event':
            $eventType = $eventData['event_type'] ?? 'user_interaction';
            $eventName = $eventData['event_name'] ?? 'unknown';
            $eventDetails = $eventData['event_data'] ?? null;
            $pageUrl = $eventData['page_url'] ?? null;
            
            $analytics->trackEvent($eventType, $eventName, $eventDetails, $pageUrl);
            
            $response = [
                'success' => true,
                'message' => 'Event tracked successfully'
            ];
            break;
            
        case 'track_funnel':
            $step = $eventData['step'] ?? 'unknown';
            $stepData = $eventData['step_data'] ?? null;
            
            $analytics->trackOrderFunnel($step, $stepData);
            
            $response = [
                'success' => true,
                'message' => 'Funnel step tracked successfully'
            ];
            break;
            
        case 'link_order':
            $orderId = $eventData['order_id'] ?? null;
            if ($orderId) {
                $analytics->linkOrderToSession($orderId);
                $response = [
                    'success' => true,
                    'message' => 'Order linked to session successfully'
                ];
            } else {
                throw new Exception('Order ID is required');
            }
            break;
            
        case 'get_session_info':
            $response = [
                'success' => true,
                'session_id' => $analytics->getSessionId(),
                'message' => 'Session info retrieved successfully'
            ];
            break;
            
        default:
            throw new Exception('Unknown action: ' . $action);
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
