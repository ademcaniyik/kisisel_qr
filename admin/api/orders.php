<?php
/**
 * Sipariş API Endpoint
 * Yeni siparişleri oluşturmak ve yönetmek için kullanılır
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Güvenlik kontrolleri
session_start();

// CSRF koruması için basit token kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['csrf_token'])) {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

require_once __DIR__ . '/../../includes/OrderManager.php';

try {
    $orderManager = new OrderManager();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'POST':
            // Yeni sipariş oluştur
            $input = null;
            
            // FormData (multipart/form-data) kontrolü
            if (!empty($_POST)) {
                $input = $_POST;
            } else {
                // JSON verisi
                $input = json_decode(file_get_contents('php://input'), true);
            }
            
            // POST verisini kontrol et
            if (!$input) {
                throw new Exception("Veri alınamadı");
            }
            
            // Zorunlu alanları kontrol et
            $required = ['customer_name', 'customer_phone', 'product_type', 'product_name'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    throw new Exception("Zorunlu alan eksik: " . $field);
                }
            }
            
            // Sipariş verilerini hazırla
            $orderData = [
                'customer_name' => htmlspecialchars(trim($input['customer_name'])),
                'customer_phone' => htmlspecialchars(trim($input['customer_phone'])),
                'customer_email' => isset($input['customer_email']) ? htmlspecialchars(trim($input['customer_email'])) : null,
                'product_type' => htmlspecialchars(trim($input['product_type'])),
                'product_name' => htmlspecialchars(trim($input['product_name'])),
                'quantity' => isset($input['quantity']) ? (int)$input['quantity'] : 1,
                'price' => isset($input['price']) ? (float)$input['price'] : 200.00,
                'special_requests' => (isset($input['special_requests']) && !empty(trim($input['special_requests']))) ? 
                    trim($input['special_requests']) : null,
                'shipping_address' => (isset($input['shipping_address']) && !empty(trim($input['shipping_address']))) ? 
                    trim($input['shipping_address']) : '',
                'payment_method' => isset($input['payment_method']) ? htmlspecialchars(trim($input['payment_method'])) : 'bank_transfer',
                'whatsapp_sent' => isset($input['whatsapp_sent']) ? (bool)$input['whatsapp_sent'] : true
            ];
            
            // Fotoğraf dosyası varsa ekle
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $orderData['photo_file'] = $_FILES['photo'];
            }
            
            // Siparişi oluştur
            $result = $orderManager->createOrder($orderData);
            
            // Yeni format (array) veya eski format (sadece ID) kontrolü
            if (is_array($result)) {
                $orderId = $result['order_id'];
                $profileData = [
                    'profile_id' => $result['profile_id'],
                    'profile_slug' => $result['profile_slug'],
                    'profile_url' => $result['profile_url'],
                    'qr_created' => $result['qr_created'],
                    'qr_id' => $result['qr_id']
                ];
            } else {
                // Geriye uyumluluk için eski format
                $orderId = $result;
                $profileData = null;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Sipariş başarıyla oluşturuldu',
                'order_id' => $orderId,
                'profile' => $profileData,
                'data' => $orderData
            ]);
            break;
            
        case 'GET':
            // Siparişleri listele (admin panel için)
            if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
                throw new Exception("Yetkisiz erişim");
            }
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            $offset = ($page - 1) * $limit;
            
            $orders = $orderManager->getAllOrders($limit, $offset, $status);
            $totalCount = $orderManager->getOrderCount($status);
            $totalPages = ceil($totalCount / $limit);
            
            echo json_encode([
                'success' => true,
                'orders' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_count' => $totalCount,
                    'limit' => $limit
                ]
            ]);
            break;
            
        default:
            throw new Exception("Desteklenmeyen HTTP metodu");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
