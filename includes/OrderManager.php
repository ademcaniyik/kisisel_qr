<?php
/**
 * Sipariş Yönetim Sınıfı
 * Siparişlerin veritabanı işlemlerini yönetir
 */

require_once __DIR__ . '/../config/database.php';

class OrderManager {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }
    
    /**
     * Yeni sipariş oluştur ve otomatik profil oluştur (QR Pool ile)
     */
    public function createOrder($data) {
        // ProfileManager ve QRPoolManager'ı yükle
        require_once __DIR__ . '/ProfileManager.php';
        require_once __DIR__ . '/QRPoolManager.php';
        $profileManager = new ProfileManager();
        $qrPoolManager = new QRPoolManager();
        
        try {
            // Transaction başlat
            $this->connection->autocommit(false);
            
            // Önce profil oluştur (QR olmadan)
            $profileResult = $profileManager->createProfileFromOrder($data);
            
            if (!$profileResult['success']) {
                throw new Exception("Profil oluşturulamadı: " . $profileResult['message']);
            }
            
            // QR Pool'dan müsait QR ata
            error_log("=== QR Assignment Debug ===");
            error_log("Profile ID: " . $profileResult['profile_id']);
            error_log("Attempting QR assignment...");
            
            $qrAssignment = $qrPoolManager->assignAvailableQR($profileResult['profile_id'], $data);
            
            error_log("QR Assignment Result: " . json_encode($qrAssignment));
            
            if (!$qrAssignment['success']) {
                throw new Exception("QR ataması başarısız: " . $qrAssignment['error']);
            }
            
            error_log("QR successfully assigned - Pool ID: " . $qrAssignment['pool_id'] . ", QR ID: " . $qrAssignment['qr_code_id']);
            error_log("===========================");
            
            // Sipariş verisine profil ve QR bilgilerini ekle
            $data['profile_id'] = $profileResult['profile_id'];
            $data['profile_slug'] = $profileResult['slug'];
            $data['qr_pool_id'] = $qrAssignment['qr_pool_id'];
            
            // Siparişi oluştur
            $sql = "INSERT INTO orders (
                customer_name, 
                customer_phone, 
                customer_email,
                profile_id,
                profile_slug,
                qr_pool_id,
                product_type, 
                product_name, 
                quantity, 
                price, 
                special_requests,
                shipping_address,
                payment_method,
                whatsapp_sent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare hatası: " . $this->connection->error);
            }
            
            $whatsapp_sent = isset($data['whatsapp_sent']) ? (int)$data['whatsapp_sent'] : 1;
            $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'bank_transfer';
            $shipping_address = isset($data['shipping_address']) ? $data['shipping_address'] : '';
            
            // Debug: Tüm parametreleri kontrol et
            error_log("=== OrderManager Debug ===");
            error_log("SQL Placeholder count: 14");
            error_log("Bind param type string: sssisssidssssi (length: " . strlen("sssisssidssssi") . ")");
            error_log("=========================");
            
            $stmt->bind_param(
                "sssisssidssssi",
                $data['customer_name'],        // s
                $data['customer_phone'],       // s
                $data['customer_email'],       // s
                $data['profile_id'],           // i
                $data['profile_slug'],         // s
                $data['qr_pool_id'],          // i
                $data['product_type'],         // s
                $data['product_name'],         // s
                $data['quantity'],             // i
                $data['price'],                // d (decimal)
                $data['special_requests'],     // s
                $shipping_address,             // s
                $payment_method,               // s
                $whatsapp_sent                 // i
            );
            
            if ($stmt->execute()) {
                $orderId = $this->connection->insert_id;
                $stmt->close();
                
                // Transaction'ı commit et
                $this->connection->commit();
                $this->connection->autocommit(true);
                
                // Debug log
                error_log("Sipariş başarıyla oluşturuldu: ID=" . $orderId);
                
                return [
                    'order_id' => $orderId,
                    'profile_id' => $profileResult['profile_id'],
                    'profile_slug' => $profileResult['slug'],
                    'profile_url' => $qrAssignment['profile_url'], // QR Pool'dan gelen güvenli URL
                    'qr_created' => true,
                    'qr_id' => $qrAssignment['qr_code_id'],
                    'pool_id' => $qrAssignment['pool_id'],
                    'edit_token' => $qrAssignment['edit_token'],
                    'edit_code' => $qrAssignment['edit_code'],
                    'edit_url' => $qrAssignment['edit_url']
                ];
            } else {
                $error = $stmt->error;
                $stmt->close();
                error_log("Sipariş execute hatası: " . $error);
                throw new Exception("Sipariş oluşturulurken hata: " . $error);
            }
            
        } catch (Exception $e) {
            // Transaction'ı rollback et
            $this->connection->rollback();
            $this->connection->autocommit(true);
            
            // Detaylı error logging
            error_log("OrderManager createOrder hatası: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            throw $e;
        }
    }
    
    /**
     * Eski createOrder metodu (geriye uyumluluk için)
     */
    public function createOrderOnly($data) {
        $sql = "INSERT INTO orders (
            customer_name, 
            customer_phone, 
            customer_email, 
            product_type, 
            product_name, 
            quantity, 
            price, 
            special_requests,
            shipping_address,
            whatsapp_sent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        $whatsapp_sent = isset($data['whatsapp_sent']) ? $data['whatsapp_sent'] : true;
        $shipping_address = isset($data['shipping_address']) ? $data['shipping_address'] : '';
        
        $stmt->bind_param(
            "sssssidssi",
            $data['customer_name'],
            $data['customer_phone'],
            $data['customer_email'],
            $data['product_type'],
            $data['product_name'],
            $data['quantity'],
            $data['price'],
            $data['special_requests'],
            $shipping_address,
            $whatsapp_sent
        );
        
        if ($stmt->execute()) {
            $orderId = $this->connection->insert_id;
            $stmt->close();
            return $orderId;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Sipariş oluşturulurken hata: " . $error);
        }
    }
    
    /**
     * Tüm siparişleri getir
     */
    public function getAllOrders($limit = null, $offset = 0, $status = null, $orderBy = 'order_date DESC') {
        $sql = "SELECT * FROM orders";
        $params = [];
        $types = "";
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $sql .= " ORDER BY " . $orderBy;
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $orders;
    }
    
    /**
     * Sipariş sayısını getir
     */
    public function getOrderCount($status = null) {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $params = [];
        $types = "";
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'];
    }
    
    /**
     * Sipariş durumunu güncelle
     */
    public function updateOrderStatus($orderId, $status, $notes = null) {
        $sql = "UPDATE orders SET status = ?, notes = ?";
        
        if ($status === 'completed' || $status === 'processing') {
            $sql .= ", processed_date = NOW()";
        }
        
        $sql .= " WHERE id = ?";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        $stmt->bind_param("ssi", $status, $notes, $orderId);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return $affected > 0;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Sipariş güncellenirken hata: " . $error);
        }
    }
    
    /**
     * Siparişi sil
     */
    public function deleteOrder($orderId) {
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        $stmt->bind_param("i", $orderId);
        
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return $affected > 0;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Sipariş silinirken hata: " . $error);
        }
    }
    
    /**
     * Tek bir siparişi getir
     */
    public function getOrder($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare hatası: " . $this->connection->error);
        }
        
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();
        
        return $order;
    }
    
    /**
     * Dashboard istatistikleri
     */
    public function getDashboardStats() {
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'processing_orders' => 0,
            'completed_orders' => 0,
            'cancelled_orders' => 0,
            'today_orders' => 0,
            'this_month_orders' => 0,
            'total_revenue' => 0
        ];
        
        // Toplam sipariş sayısı
        $stats['total_orders'] = $this->getOrderCount();
        
        // Durum bazında sayılar
        $stats['pending_orders'] = $this->getOrderCount('pending');
        $stats['processing_orders'] = $this->getOrderCount('processing');
        $stats['completed_orders'] = $this->getOrderCount('completed');
        $stats['cancelled_orders'] = $this->getOrderCount('cancelled');
        
        // Bugünkü siparişler
        $sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(order_date) = CURDATE()";
        $result = $this->connection->query($sql);
        $row = $result->fetch_assoc();
        $stats['today_orders'] = $row['count'];
        
        // Bu ayki siparişler
        $sql = "SELECT COUNT(*) as count FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
        $result = $this->connection->query($sql);
        $row = $result->fetch_assoc();
        $stats['this_month_orders'] = $row['count'];
        
        // Toplam gelir (tamamlanan siparişlerden)
        $sql = "SELECT SUM(price * quantity) as total FROM orders WHERE status = 'completed'";
        $result = $this->connection->query($sql);
        $row = $result->fetch_assoc();
        $stats['total_revenue'] = $row['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Son siparişleri getir
     */
    public function getRecentOrders($limit = 10) {
        return $this->getAllOrders($limit, 0, null, 'order_date DESC');
    }
}
?>
