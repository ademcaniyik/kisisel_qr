<?php
/**
 * Analytics Manager - Site trafiği ve kullanıcı davranışı analizi
 */

require_once __DIR__ . '/../config/database.php';

class AnalyticsManager {
    private $db;
    private $sessionId;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }
    
    /**
     * Oturum başlat veya devam ettir
     */
    private function initSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Session ID yoksa oluştur
        if (!isset($_SESSION['analytics_session_id'])) {
            $_SESSION['analytics_session_id'] = $this->generateSessionId();
            $_SESSION['session_start_time'] = time();
            
            // Yeni oturum kaydı oluştur
            $this->createSession();
        }
        
        $this->sessionId = $_SESSION['analytics_session_id'];
        
        // Son aktiviteyi güncelle
        $this->updateLastActivity();
    }
    
    /**
     * Benzersiz session ID oluştur
     */
    private function generateSessionId() {
        return bin2hex(random_bytes(16)) . '_' . time();
    }
    
    /**
     * Yeni oturum kaydı oluştur
     */
    private function createSession() {
        $sessionId = $_SESSION['analytics_session_id'];
        $ipAddress = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (session_id, ip_address, user_agent, referrer) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE last_activity = NOW()
        ");
        $stmt->bind_param("ssss", $sessionId, $ipAddress, $userAgent, $referrer);
        $stmt->execute();
    }
    
    /**
     * Son aktiviteyi güncelle
     */
    private function updateLastActivity() {
        $stmt = $this->db->prepare("
            UPDATE user_sessions 
            SET last_activity = NOW(), 
                total_time_spent = TIMESTAMPDIFF(SECOND, first_visit, NOW())
            WHERE session_id = ?
        ");
        $stmt->bind_param("s", $this->sessionId);
        $stmt->execute();
    }
    
    /**
     * Kullanıcı olayını kaydet (user_events tablosuna)
     */
    public function trackEvent($eventType, $eventName, $eventData = null, $pageUrl = null) {
        $pageUrl = $pageUrl ?? $_SERVER['REQUEST_URI'] ?? '';
        $eventDataJson = $eventData ? json_encode($eventData) : null;
        
        $stmt = $this->db->prepare("
            INSERT INTO user_events (session_id, event_type, event_name, event_data, page_url) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $this->sessionId, $eventType, $eventName, $eventDataJson, $pageUrl);
        $result = $stmt->execute();
        
        // Özel olayları işle
        $this->handleSpecialEvents($eventType, $eventName, $eventData);
        
        return $result;
    }
    
    /**
     * Sipariş funnel adımını kaydet (order_funnel tablosuna)
     */
    public function trackOrderFunnel($step, $stepData = null) {
        $timeFromStart = 0;
        if (isset($_SESSION['session_start_time'])) {
            $timeFromStart = time() - $_SESSION['session_start_time'];
        }
        
        $stepDataJson = $stepData ? json_encode($stepData) : null;
        
        $stmt = $this->db->prepare("
            INSERT INTO order_funnel (session_id, step, step_data, time_from_start) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("sssi", $this->sessionId, $step, $stepDataJson, $timeFromStart);
        $result = $stmt->execute();
        
        return $result;
    }
    
    /**
     * Özel olayları işle
     */
    private function handleSpecialEvents($eventType, $eventName, $eventData) {
        switch ($eventName) {
            case 'order_button_clicked':
                $this->trackOrderFunnel('order_clicked');
                break;
        }
    }
    
    /**
     * Günlük istatistikleri hesapla ve kaydet (daily_stats tablosuna)
     */
    public function calculateDailyStats($date = null) {
        $date = $date ?? date('Y-m-d');
        
        // Sipariş butonuna tıklama sayısı
        $orderButtonClicks = $this->db->query("
            SELECT COUNT(*) as count 
            FROM user_events 
            WHERE event_name = 'order_button_clicked'
            AND DATE(created_at) = '$date'
        ")->fetch_assoc()['count'];
        
        // Basit stats kaydetme
        $stmt = $this->db->prepare("
            INSERT INTO daily_stats 
            (stat_date, total_visitors, unique_visitors, total_page_views, 
             order_button_clicks, orders_started, orders_completed, 
             avg_session_duration, bounce_rate, conversion_rate) 
            VALUES (?, 1, 1, 1, ?, 0, 0, 0, 0, 0)
            ON DUPLICATE KEY UPDATE
            order_button_clicks = order_button_clicks + VALUES(order_button_clicks),
            updated_at = NOW()
        ");
        
        $stmt->bind_param("si", $date, $orderButtonClicks);
        return $stmt->execute();
    }
    
    /**
     * Analytics dashboard için veri al
     */
    public function getDashboardData($startDate = null, $endDate = null) {
        $startDate = $startDate ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $endDate ?? date('Y-m-d');
        
        // Günlük istatistikler
        $dailyStats = [];
        $stmt = $this->db->prepare("
            SELECT * FROM daily_stats 
            WHERE stat_date BETWEEN ? AND ? 
            ORDER BY stat_date ASC
        ");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dailyStats[] = $row;
        }
        
        // Toplam özetler
        $totalStats = $this->db->query("
            SELECT 
                COALESCE(SUM(total_visitors), 0) as total_visitors,
                COALESCE(SUM(unique_visitors), 0) as unique_visitors,
                COALESCE(SUM(total_page_views), 0) as total_page_views,
                COALESCE(SUM(order_button_clicks), 0) as order_button_clicks,
                COALESCE(SUM(orders_started), 0) as orders_started,
                COALESCE(SUM(orders_completed), 0) as orders_completed,
                COALESCE(AVG(avg_session_duration), 0) as avg_session_duration,
                COALESCE(AVG(bounce_rate), 0) as avg_bounce_rate,
                COALESCE(AVG(conversion_rate), 0) as avg_conversion_rate
            FROM daily_stats 
            WHERE stat_date BETWEEN '$startDate' AND '$endDate'
        ")->fetch_assoc();
        
        // En popüler sayfalar (boş array)
        $popularPages = [];
        
        return [
            'daily_stats' => $dailyStats,
            'total_stats' => $totalStats,
            'popular_pages' => $popularPages
        ];
    }
    
    /**
     * Client IP adresini al
     */
    private function getClientIP() {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Session ID'yi al
     */
    public function getSessionId() {
        return $this->sessionId;
    }
    
    /**
     * Günlük özet oluştur
     */
    public function generateDailySummary($date = null) {
        return $this->calculateDailyStats($date);
    }
    
    /**
     * Sayfa ziyaretini kaydet
     */
    public function trackPageVisit($pageUrl, $pageTitle = null) {
        $pageUrl = $pageUrl ?? $_SERVER['REQUEST_URI'] ?? '';
        $pageTitle = $pageTitle ?? $pageUrl;
        
        // Page visits tablosuna kaydet
        $stmt = $this->db->prepare("
            INSERT INTO page_visits (session_id, page_url, page_title) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $this->sessionId, $pageUrl, $pageTitle);
        $result = $stmt->execute();
        
        return $result;
    }
    
    /**
     * Otomatik daily stats güncelleme
     * Analytics dashboard açıldığında otomatik çalışır
     */
    public function autoUpdateDailyStats() {
        $today = date('Y-m-d');
        
        try {
            // Bugünkü order button clicks sayısını hesapla
            $result = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user_events 
                WHERE event_name = 'order_button_clicked' 
                AND DATE(created_at) = '$today'
            ");
            $orderClicks = $result->fetch_assoc()['count'];
            
            // Bugünkü unique sessions sayısını hesapla
            $result = $this->db->query("
                SELECT COUNT(DISTINCT session_id) as count 
                FROM user_events 
                WHERE DATE(created_at) = '$today'
            ");
            $uniqueVisitors = $result->fetch_assoc()['count'];
            
            // Page views sayısını hesapla
            $result = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user_events 
                WHERE event_name = 'page_loaded' 
                AND DATE(created_at) = '$today'
            ");
            $pageViews = $result->fetch_assoc()['count'];
            
            // Daily stats tablosunu güncelle
            $stmt = $this->db->prepare("
                INSERT INTO daily_stats 
                (stat_date, total_visitors, unique_visitors, total_page_views, order_button_clicks) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                total_visitors = VALUES(total_visitors),
                unique_visitors = VALUES(unique_visitors),
                total_page_views = VALUES(total_page_views),
                order_button_clicks = VALUES(order_button_clicks),
                updated_at = NOW()
            ");
            
            $stmt->bind_param("siiii", $today, $uniqueVisitors, $uniqueVisitors, $pageViews, $orderClicks);
            $result = $stmt->execute();
            
            // Son 3 günün verilerini de kontrol et (eksik varsa güncelle)
            for ($i = 1; $i <= 3; $i++) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                // Bu tarih için kayıt var mı kontrol et
                $checkResult = $this->db->query("SELECT id FROM daily_stats WHERE stat_date = '$date'");
                if ($checkResult->num_rows == 0) {
                    // Kayıt yoksa oluştur
                    $this->updateDailyStatsForDate($date);
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Analytics auto update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Belirli bir tarih için daily stats güncelle
     */
    private function updateDailyStatsForDate($date) {
        try {
            // Order button clicks
            $result = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user_events 
                WHERE event_name = 'order_button_clicked' 
                AND DATE(created_at) = '$date'
            ");
            $orderClicks = $result->fetch_assoc()['count'];
            
            // Unique visitors
            $result = $this->db->query("
                SELECT COUNT(DISTINCT session_id) as count 
                FROM user_events 
                WHERE DATE(created_at) = '$date'
            ");
            $uniqueVisitors = $result->fetch_assoc()['count'];
            
            // Page views
            $result = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user_events 
                WHERE event_name = 'page_loaded' 
                AND DATE(created_at) = '$date'
            ");
            $pageViews = $result->fetch_assoc()['count'];
            
            // Sadece veri varsa kaydet
            if ($uniqueVisitors > 0 || $orderClicks > 0 || $pageViews > 0) {
                $stmt = $this->db->prepare("
                    INSERT INTO daily_stats 
                    (stat_date, total_visitors, unique_visitors, total_page_views, order_button_clicks) 
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    total_visitors = VALUES(total_visitors),
                    unique_visitors = VALUES(unique_visitors),
                    total_page_views = VALUES(total_page_views),
                    order_button_clicks = VALUES(order_button_clicks),
                    updated_at = NOW()
                ");
                
                $stmt->bind_param("siiii", $date, $uniqueVisitors, $uniqueVisitors, $pageViews, $orderClicks);
                return $stmt->execute();
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Analytics date update error for $date: " . $e->getMessage());
            return false;
        }
    }
}
?>
