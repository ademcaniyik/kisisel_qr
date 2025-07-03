<?php
/**
 * Update Daily Stats - Günlük istatistikleri güncelle
 */

echo "<h1>Daily Stats Update</h1>";

try {
    require_once 'config/database.php';
    require_once 'includes/AnalyticsManager.php';
    
    $db = Database::getInstance();
    $analytics = new AnalyticsManager();
    
    echo "<h2>📊 Bugünün İstatistiklerini Güncelliyoruz...</h2>";
    
    $today = date('Y-m-d');
    
    // Bugünkü order button clicks sayısını hesapla
    $result = $db->query("
        SELECT COUNT(*) as count 
        FROM user_events 
        WHERE event_name = 'order_button_clicked' 
        AND DATE(created_at) = '$today'
    ");
    $orderClicks = $result->fetch_assoc()['count'];
    
    // Bugünkü toplam events sayısını hesapla
    $result = $db->query("
        SELECT COUNT(*) as count 
        FROM user_events 
        WHERE DATE(created_at) = '$today'
    ");
    $totalEvents = $result->fetch_assoc()['count'];
    
    // Bugünkü unique sessions sayısını hesapla
    $result = $db->query("
        SELECT COUNT(DISTINCT session_id) as count 
        FROM user_events 
        WHERE DATE(created_at) = '$today'
    ");
    $uniqueVisitors = $result->fetch_assoc()['count'];
    
    // Page views sayısını hesapla
    $result = $db->query("
        SELECT COUNT(*) as count 
        FROM user_events 
        WHERE event_name = 'page_loaded' 
        AND DATE(created_at) = '$today'
    ");
    $pageViews = $result->fetch_assoc()['count'];
    
    echo "📈 Bugün ($today) için hesaplanan veriler:<br>";
    echo "- Sipariş butonu tıklama: $orderClicks<br>";
    echo "- Toplam events: $totalEvents<br>";
    echo "- Unique visitors: $uniqueVisitors<br>";
    echo "- Page views: $pageViews<br><br>";
    
    // Daily stats tablosunu güncelle
    $stmt = $db->prepare("
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
    
    if ($stmt->execute()) {
        echo "✅ <strong>Daily stats başarıyla güncellendi!</strong><br>";
    } else {
        echo "❌ Daily stats güncellenemedi: " . $stmt->error . "<br>";
    }
    
    // Son 7 günün verilerini de güncelle
    echo "<h2>📊 Son 7 Günün Verilerini Güncelliyoruz...</h2>";
    
    for ($i = 1; $i <= 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        
        // Her gün için istatistikleri hesapla
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM user_events 
            WHERE event_name = 'order_button_clicked' 
            AND DATE(created_at) = '$date'
        ");
        $dayOrderClicks = $result->fetch_assoc()['count'];
        
        $result = $db->query("
            SELECT COUNT(DISTINCT session_id) as count 
            FROM user_events 
            WHERE DATE(created_at) = '$date'
        ");
        $dayUniqueVisitors = $result->fetch_assoc()['count'];
        
        $result = $db->query("
            SELECT COUNT(*) as count 
            FROM user_events 
            WHERE event_name = 'page_loaded' 
            AND DATE(created_at) = '$date'
        ");
        $dayPageViews = $result->fetch_assoc()['count'];
        
        if ($dayUniqueVisitors > 0 || $dayOrderClicks > 0 || $dayPageViews > 0) {
            $stmt = $db->prepare("
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
            
            $stmt->bind_param("siiii", $date, $dayUniqueVisitors, $dayUniqueVisitors, $dayPageViews, $dayOrderClicks);
            $stmt->execute();
            
            echo "✅ $date: $dayUniqueVisitors ziyaretçi, $dayOrderClicks sipariş tıklama<br>";
        } else {
            echo "➖ $date: Veri yok<br>";
        }
    }
    
    echo "<h2>✅ Güncelleme Tamamlandı!</h2>";
    echo "<p><a href='admin/analytics.php'>📊 Analytics Dashboard'a Git</a></p>";
    echo "<p><a href='check_analytics_data.php'>🔍 Veri Kontrolü Yap</a></p>";
    
} catch (Exception $e) {
    echo "❌ <strong>Hata:</strong> " . $e->getMessage();
}
?>
