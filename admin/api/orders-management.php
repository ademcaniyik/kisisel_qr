<?php
/**
 * Sipariş Yönetim API Endpoint
 * Admin panel sipariş yönetimi işlemleri için kullanılır
 */

header('Content-Type: application/json; charset=utf-8');

// Güvenlik kontrolleri
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

require_once __DIR__ . '/../../includes/OrderManager.php';

try {
    $orderManager = new OrderManager();
    
    // JSON verisini al
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        throw new Exception("Geçersiz istek");
    }
    
    switch ($input['action']) {
        case 'update_status':
            if (empty($input['order_id']) || empty($input['status'])) {
                throw new Exception("Sipariş ID ve durum gerekli");
            }
            
            $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            if (!in_array($input['status'], $validStatuses)) {
                throw new Exception("Geçersiz durum");
            }
            
            $notes = isset($input['notes']) ? $input['notes'] : null;
            $result = $orderManager->updateOrderStatus($input['order_id'], $input['status'], $notes);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Sipariş durumu güncellendi'
                ]);
            } else {
                throw new Exception("Sipariş bulunamadı veya güncellenemedi");
            }
            break;
            
        case 'add_note':
            if (empty($input['order_id']) || empty($input['note'])) {
                throw new Exception("Sipariş ID ve not gerekli");
            }
            
            // Mevcut siparişi getir
            $order = $orderManager->getOrder($input['order_id']);
            if (!$order) {
                throw new Exception("Sipariş bulunamadı");
            }
            
            // Mevcut notları koru ve yeni notu ekle
            $existingNotes = $order['notes'] ? $order['notes'] . "\n\n" : '';
            $newNote = date('d.m.Y H:i') . " - " . $input['note'];
            $updatedNotes = $existingNotes . $newNote;
            
            $result = $orderManager->updateOrderStatus($input['order_id'], $order['status'], $updatedNotes);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Not eklendi'
                ]);
            } else {
                throw new Exception("Not eklenemedi");
            }
            break;
            
        case 'delete':
            if (empty($input['order_id'])) {
                throw new Exception("Sipariş ID gerekli");
            }
            
            $result = $orderManager->deleteOrder($input['order_id']);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Sipariş silindi'
                ]);
            } else {
                throw new Exception("Sipariş bulunamadı veya silinemedi");
            }
            break;
            
        case 'get_order':
            if (empty($input['order_id'])) {
                throw new Exception("Sipariş ID gerekli");
            }
            
            $order = $orderManager->getOrder($input['order_id']);
            
            if ($order) {
                echo json_encode([
                    'success' => true,
                    'order' => $order
                ]);
            } else {
                throw new Exception("Sipariş bulunamadı");
            }
            break;
            
        case 'get_stats':
            $stats = $orderManager->getDashboardStats();
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        default:
            throw new Exception("Geçersiz işlem");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
