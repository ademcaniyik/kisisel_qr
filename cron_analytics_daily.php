<?php
/**
 * Analytics Daily Report Generator
 * Her gün çalıştırılmalı - Günlük istatistikleri oluşturur
 */

require_once __DIR__ . '/includes/AnalyticsManager.php';

try {
    $analytics = new AnalyticsManager();
    
    // Bugünkü tarih için özet oluştur
    $today = date('Y-m-d');
    $result = $analytics->generateDailySummary($today);
    
    if ($result) {
        echo "[" . date('Y-m-d H:i:s') . "] SUCCESS: Daily summary generated for $today\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] INFO: Daily summary already exists for $today\n";
    }
    
    // Dün için de kontrol et (gecikme durumunda)
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $analytics->generateDailySummary($yesterday);
    
    echo "[" . date('Y-m-d H:i:s') . "] COMPLETED: Analytics daily job finished\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
