<?php
/**
 * Analytics Data Check - Veritabanındaki verileri kontrol et
 */

echo "<h1>Analytics Data Check</h1>";

try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    
    echo "<h2>📊 Veritabanı İstatistikleri</h2>";
    
    // Tablolar ve kayıt sayıları
    $tables = [
        'user_sessions' => 'Kullanıcı Oturumları',
        'page_visits' => 'Sayfa Ziyaretleri', 
        'user_events' => 'Kullanıcı Olayları',
        'order_funnel' => 'Sipariş Funnel',
        'daily_stats' => 'Günlük İstatistikler'
    ];
    
    foreach ($tables as $table => $description) {
        $result = $db->query("SELECT COUNT(*) as count FROM $table");
        if ($result) {
            $count = $result->fetch_assoc()['count'];
            echo "✅ <strong>$description ($table):</strong> $count kayıt<br>";
        } else {
            echo "❌ <strong>$description ($table):</strong> Tablo bulunamadı<br>";
        }
    }
    
    echo "<h2>🔍 Son User Events (Son 10 kayıt)</h2>";
    $result = $db->query("SELECT * FROM user_events ORDER BY id DESC LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Session ID</th><th>Event Type</th><th>Event Name</th><th>Created At</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . substr($row['session_id'], 0, 20) . "...</td>";
            echo "<td>{$row['event_type']}</td>";
            echo "<td>{$row['event_name']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Henüz user_events kaydı yok";
    }
    
    echo "<h2>🛒 Son Order Funnel (Son 10 kayıt)</h2>";
    $result = $db->query("SELECT * FROM order_funnel ORDER BY id DESC LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Session ID</th><th>Step</th><th>Created At</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . substr($row['session_id'], 0, 20) . "...</td>";
            echo "<td>{$row['step']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Henüz order_funnel kaydı yok";
    }
    
    echo "<h2>📈 Daily Stats</h2>";
    $result = $db->query("SELECT * FROM daily_stats ORDER BY stat_date DESC LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Tarih</th><th>Ziyaretçi</th><th>Sayfa Görüntüleme</th><th>Sipariş Tıklama</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['stat_date']}</td>";
            echo "<td>{$row['total_visitors']}</td>";
            echo "<td>{$row['total_page_views']}</td>";
            echo "<td>{$row['order_button_clicks']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Henüz daily_stats kaydı yok";
    }
    
    // Analytics Manager test
    echo "<h2>🔧 Analytics Manager Test</h2>";
    require_once 'includes/AnalyticsManager.php';
    $analytics = new AnalyticsManager();
    $dashboardData = $analytics->getDashboardData();
    
    echo "<h3>Dashboard Data:</h3>";
    echo "<pre>";
    print_r($dashboardData);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ <strong>Hata:</strong> " . $e->getMessage();
}

?>

<style>
table { margin: 10px 0; }
th { background: #f0f0f0; padding: 8px; }
td { padding: 8px; }
</style>

<p><a href="admin/analytics.php">📊 Analytics Dashboard'a Git</a></p>
<p><a href="index.php">🏠 Ana Sayfaya Git</a></p>
