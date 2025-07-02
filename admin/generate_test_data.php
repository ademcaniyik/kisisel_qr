<?php
/**
 * Analytics Test Data Generator
 * Grafikleri test etmek için örnek veri oluşturur
 */

require_once '../config/database.php';
require_once '../includes/AnalyticsManager.php';

$db = Database::getInstance();
$analytics = new AnalyticsManager();

echo "Analytics Test Verileri Oluşturuluyor...\n";

// Son 7 gün için test verileri oluştur
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    
    // Random test verileri
    $visitors = rand(10, 50);
    $pageViews = rand($visitors, $visitors * 3);
    $orderClicks = rand(1, intval($visitors * 0.3));
    $ordersStarted = rand(0, $orderClicks);
    $ordersCompleted = rand(0, $ordersStarted);
    $avgDuration = rand(120, 600);
    $bounceRate = rand(20, 80);
    $conversionRate = $visitors > 0 ? ($ordersCompleted / $visitors) * 100 : 0;
    
    // Daily stats tablosuna ekle (duplicate key olursa update et)
    $sql = "INSERT INTO daily_stats 
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
            updated_at = NOW()";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param('siiiiiiddd', 
        $date, $visitors, $visitors, $pageViews,
        $orderClicks, $ordersStarted, $ordersCompleted,
        $avgDuration, $bounceRate, $conversionRate
    );
    
    if ($stmt->execute()) {
        echo "✅ $date: $visitors ziyaretçi, $ordersCompleted sipariş\n";
    } else {
        echo "❌ $date: Hata - " . $stmt->error . "\n";
    }
}

// Bazı page_visits verileri de ekle
$pages = ['/', '/profile.php', '/index.php', '/edit.php'];
for ($i = 0; $i < 20; $i++) {
    $page = $pages[array_rand($pages)];
    $sessionId = 'test_session_' . rand(1000, 9999);
    $userId = rand(1, 10);
    
    $sql = "INSERT INTO page_visits (session_id, user_id, page_url, referrer, user_agent, ip_address) 
            VALUES (?, ?, ?, '', 'Test Browser', '127.0.0.1')";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('sis', $sessionId, $userId, $page);
    $stmt->execute();
}

echo "\n✅ Test verileri oluşturuldu!\n";
echo "Analytics sayfasını yenileyin: http://localhost/kisisel_qr_canli/admin/analytics.php\n";
?>
