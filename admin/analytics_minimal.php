<?php
// Minimal analytics test - security.php olmadan
session_start();

echo "<!DOCTYPE html><html><head><title>Minimal Analytics Test</title></head><body>";
echo "<h1>Minimal Analytics Test</h1>";

// Session kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "<p>❌ Admin giriş yapmamış</p>";
    echo "<p><a href='login.php'>Login</a></p>";
    exit;
}

echo "<p>✅ Admin giriş yapmış</p>";

// AnalyticsManager'ı yükle
try {
    require_once '../includes/AnalyticsManager.php';
    echo "<p>✅ AnalyticsManager yüklendi</p>";
    
    $analytics = new AnalyticsManager();
    echo "<p>✅ AnalyticsManager oluşturuldu</p>";
    
    // Test veri al
    $data = $analytics->getDashboardData();
    echo "<p>✅ Dashboard verisi alındı</p>";
    
    echo "<h2>Test Verileri:</h2>";
    echo "<pre>" . print_r($data, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Hata: " . $e->getMessage() . "</p>";
}

echo "<p><a href='dashboard.php'>Dashboard</a></p>";
echo "</body></html>";
?>
