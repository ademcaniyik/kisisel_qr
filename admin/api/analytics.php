<?php
/**
 * Analytics Tracking API - Güvenlik kontrolü olmadan analytics tracking
 */

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Production'da false olmalı

try {
    // Database connection
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../includes/AnalyticsManager.php';
    
    // Session başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Request method kontrolü
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
        exit();
    }
    
    // JSON input al
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit();
    }
    
    $action = $input['action'] ?? '';
    
    // Analytics Manager oluştur
    $analytics = new AnalyticsManager();
    
    switch ($action) {
        case 'track_event':
            $eventType = $input['event_type'] ?? '';
            $eventName = $input['event_name'] ?? '';
            $eventData = $input['event_data'] ?? null;
            
            if (!$eventType || !$eventName) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing event_type or event_name']);
                exit();
            }
            
            $result = $analytics->trackEvent($eventType, $eventName, $eventData);
            echo json_encode(['success' => $result]);
            break;
            
        case 'track_order_funnel':
            $step = $input['step'] ?? '';
            $stepData = $input['step_data'] ?? null;
            
            if (!$step) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing step parameter']);
                exit();
            }
            
            $result = $analytics->trackOrderFunnel($step, $stepData);
            echo json_encode(['success' => $result]);
            break;
            
        case 'track_page_visit':
            $pageUrl = $input['page_url'] ?? '';
            $pageTitle = $input['page_title'] ?? '';
            
            if (!$pageUrl) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing page_url parameter']);
                exit();
            }
            
            $result = $analytics->trackPageVisit($pageUrl, $pageTitle);
            echo json_encode(['success' => $result]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error', 
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
