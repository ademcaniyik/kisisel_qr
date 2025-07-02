<?php
/**
 * Analytics Manager - Site trafiği ve kullanıcı davranışı analizi
 * analytics_tables.sql yapısına uygun olarak güncellenmiştir
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
     * Sayfa ziyaretini kaydet (page_visits tablosuna)
     */
    public function trackPageVisit($pageUrl, $pageTitle = '') {
        $ipAddress = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // page_visits tablosuna kaydet
        $stmt = $this->db->prepare("
            INSERT INTO page_visits (session_id, ip_address, user_agent, page_url, page_title, referrer) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $this->sessionId, $ipAddress, $userAgent, $pageUrl, $pageTitle, $referrer);
        $result = $stmt->execute();
        
        // user_sessions tablosundaki sayfa görüntüleme sayısını güncelle
        $stmt = $this->db->prepare("
            UPDATE user_sessions 
            SET total_page_views = total_page_views + 1, last_activity = NOW()
            WHERE session_id = ?
        ");
        $stmt->bind_param("s", $this->sessionId);
        $stmt->execute();
        
        return $result;
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
        // İlk ziyaretten bu ana kadar geçen süre
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
        
        // Conversion durumunu güncelle
        $this->updateConversionStatus($step);
        
        return $result;
    }
    
    /**
     * Conversion durumunu güncelle
     */
    private function updateConversionStatus($step) {
        $conversionStatus = 'none';
        
        switch ($step) {
            case 'order_clicked':
            case 'step1_completed':
            case 'step2_completed':
                $conversionStatus = 'order_started';
                break;
            case 'order_completed':
                $conversionStatus = 'order_completed';
                break;
        }
        
        if ($conversionStatus !== 'none') {
            $stmt = $this->db->prepare("
                UPDATE user_sessions 
                SET conversion_status = ? 
                WHERE session_id = ?
            ");
            $stmt->bind_param("ss", $conversionStatus, $this->sessionId);
            $stmt->execute();
        }
    }
    
    /**
     * Sipariş tamamlandığında order_id'yi ilişkilendir
     */
    public function linkOrderToSession($orderId) {
        $stmt = $this->db->prepare("
            UPDATE user_sessions 
            SET order_id = ?, conversion_status = 'order_completed' 
            WHERE session_id = ?
        ");
        $stmt->bind_param("is", $orderId, $this->sessionId);
        $stmt->execute();
        
        // Funnel'da da kaydet
        $this->trackOrderFunnel('order_completed', ['order_id' => $orderId]);
    }
    
    /**
     * Özel olayları işle
     */
    private function handleSpecialEvents($eventType, $eventName, $eventData) {
        switch ($eventName) {
            case 'order_button_clicked':
                $this->trackOrderFunnel('order_clicked');
                break;
            case 'order_step1_completed':
                $this->trackOrderFunnel('step1_completed', $eventData);
                break;
            case 'order_step2_completed':
                $this->trackOrderFunnel('step2_completed', $eventData);
                break;
        }
    }
    
    /**
     * Günlük istatistikleri hesapla ve kaydet (daily_stats tablosuna)
     */
    public function calculateDailyStats($date = null) {
        $date = $date ?? date('Y-m-d');
        
        // Toplam ziyaretçi sayısı (user_sessions tablosundan)
        $totalVisitors = $this->db->query("
            SELECT COUNT(*) as count 
            FROM user_sessions 
            WHERE DATE(first_visit) = '$date'
        ")->fetch_assoc()['count'];
        
        // Benzersiz ziyaretçi sayısı (IP bazında)
        $uniqueVisitors = $this->db->query("
            SELECT COUNT(DISTINCT ip_address) as count 
            FROM user_sessions 
            WHERE DATE(first_visit) = '$date'
        ")->fetch_assoc()['count'];
        
        // Toplam sayfa görüntüleme (page_visits tablosundan)
        $totalPageViews = $this->db->query("
            SELECT COUNT(*) as count 
            FROM page_visits 
            WHERE DATE(visited_at) = '$date'
        ")->fetch_assoc()['count'];
        
        // Sipariş butonuna tıklama sayısı (user_events tablosundan)
        $orderButtonClicks = $this->db->query("
            SELECT COUNT(*) as count 
            FROM user_events 
            WHERE event_name = 'order_button_clicked'
            AND DATE(created_at) = '$date'
        ")->fetch_assoc()['count'];
        
        // Başlayan sipariş sayısı (order_funnel tablosundan)
        $ordersStarted = $this->db->query("
            SELECT COUNT(DISTINCT session_id) as count 
            FROM order_funnel 
            WHERE step IN ('order_clicked', 'step1_completed') 
            AND DATE(completed_at) = '$date'
        ")->fetch_assoc()['count'];
        
        // Tamamlanan sipariş sayısı
        $ordersCompleted = $this->db->query("
            SELECT COUNT(*) as count 
            FROM order_funnel 
            WHERE step = 'order_completed' 
            AND DATE(completed_at) = '$date'
        ")->fetch_assoc()['count'];
        
        // Ortalama oturum süresi (user_sessions tablosundan)
        $avgSessionDuration = $this->db->query("
            SELECT AVG(total_time_spent) as avg_duration 
            FROM user_sessions 
            WHERE DATE(first_visit) = '$date'
        ")->fetch_assoc()['avg_duration'] ?? 0;
        
        // Bounce rate (tek sayfa görüntüleyenler)
        $bounceRate = 0;
        if ($totalVisitors > 0) {
            $bounceSessions = $this->db->query("
                SELECT COUNT(*) as count 
                FROM user_sessions 
                WHERE total_page_views = 1 
                AND DATE(first_visit) = '$date'
            ")->fetch_assoc()['count'];
            $bounceRate = ($bounceSessions / $totalVisitors) * 100;
        }
        
        // Conversion rate
        $conversionRate = 0;
        if ($totalVisitors > 0) {
            $conversionRate = ($ordersCompleted / $totalVisitors) * 100;
        }
        
        // daily_stats tablosuna kaydet/güncelle
        $stmt = $this->db->prepare("
            INSERT INTO daily_stats 
            (stat_date, total_visitors, unique_visitors, total_page_views, 
             order_button_clicks, orders_started, orders_completed, 
             avg_session_duration, bounce_rate, conversion_rate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            total_visitors = VALUES(total_visitors),
            unique_visitors = VALUES(unique_visitors),
            total_page_views = VALUES(total_page_views),
            order_button_clicks = VALUES(order_button_clicks),
            orders_started = VALUES(orders_started),
            orders_completed = VALUES(orders_completed),
            avg_session_duration = VALUES(avg_session_duration),
            bounce_rate = VALUES(bounce_rate),
            conversion_rate = VALUES(conversion_rate),
            updated_at = NOW()
        ");
        
        $stmt->bind_param("siiiiiiidd", 
            $date, $totalVisitors, $uniqueVisitors, $totalPageViews,
            $orderButtonClicks, $ordersStarted, $ordersCompleted,
            $avgSessionDuration, $bounceRate, $conversionRate
        );
        
        return $stmt->execute();
    }
    
    /**
     * Analytics dashboard için veri al (daily_stats tablosundan)
     */
    public function getDashboardData($startDate = null, $endDate = null) {
        $startDate = $startDate ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $endDate ?? date('Y-m-d');
        
        // Günlük istatistikler (daily_stats tablosundan)
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
        
        // Toplam özetler (daily_stats'ten hesaplanan)
        $totalStats = $this->db->query("
            SELECT 
                SUM(total_visitors) as total_visitors,
                SUM(unique_visitors) as unique_visitors,
                SUM(total_page_views) as total_page_views,
                SUM(order_button_clicks) as order_button_clicks,
                SUM(orders_started) as orders_started,
                SUM(orders_completed) as orders_completed,
                AVG(avg_session_duration) as avg_session_duration,
                AVG(bounce_rate) as avg_bounce_rate,
                AVG(conversion_rate) as avg_conversion_rate
            FROM daily_stats 
            WHERE stat_date BETWEEN '$startDate' AND '$endDate'
        ")->fetch_assoc();
        
        // En popüler sayfalar (page_visits tablosundan)
        $popularPages = [];
        $stmt = $this->db->prepare("
            SELECT page_url, COUNT(*) as visits 
            FROM page_visits 
            WHERE DATE(visited_at) BETWEEN ? AND ?
            GROUP BY page_url 
            ORDER BY visits DESC 
            LIMIT 10
        ");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $popularPages[] = $row;
        }
        
        return [
            'daily_stats' => $dailyStats,
            'total_stats' => $totalStats,
            'popular_pages' => $popularPages,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
    }
    
    /**
     * Client IP adresini al
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Session ID'yi al
     */
    public function getSessionId() {
        return $this->sessionId;
    }
    /**
     * Günlük özet oluştur (calculateDailyStats ile aynı işlevi görür)
     */
    public function generateDailySummary($date = null) {
        return $this->calculateDailyStats($date);
    }
}
