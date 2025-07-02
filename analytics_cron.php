<?php
/**
 * Daily Analytics Cron Job
 * Günlük istatistikleri hesaplar ve güncel tutar
 * 
 * Bu script günde bir kez çalıştırılmalıdır (örn: her gece 23:59'da)
 * Cron Job örneği: 59 23 * * * php /path/to/analytics_cron.php
 */

require_once __DIR__ . '/includes/AnalyticsManager.php';

try {
    $analytics = new AnalyticsManager();
    
    // Bugünün istatistiklerini hesapla
    $today = date('Y-m-d');
    $result = $analytics->calculateDailyStats($today);
    
    if ($result) {
        echo "[" . date('Y-m-d H:i:s') . "] SUCCESS: Daily stats calculated for $today\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ERROR: Failed to calculate daily stats for $today\n";
    }
    
    // Son 7 günün istatistiklerini de güncelle (düzeltmeler için)
    for ($i = 1; $i <= 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $analytics->calculateDailyStats($date);
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] INFO: Updated stats for last 7 days\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] COMPLETED: Analytics cron job finished\n";
?>
