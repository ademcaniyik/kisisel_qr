<?php
/**
 * Duplicate QR atamalarını temizleme scripti
 * Sadece geliştirme aşamasında kullanılır
 */

require_once __DIR__ . '/includes/QRPoolManager.php';

echo "=== QR Pool Duplicate Cleanup ===\n";

$qrPoolManager = new QRPoolManager();

// Profile ID 129 için duplicateları temizle
echo "Profile ID 129 için duplicate temizleme...\n";
$result = $qrPoolManager->cleanupDuplicateAssignments(129);

if ($result['success']) {
    echo "✅ Başarılı!\n";
    echo "Temizlenen profil sayısı: " . $result['profiles_cleaned'] . "\n";
    echo "Serbest bırakılan QR sayısı: " . $result['qr_codes_freed'] . "\n";
} else {
    echo "❌ Hata: " . $result['error'] . "\n";
}

echo "\n=== Tüm duplicate profiller için temizlik ===\n";
$allResult = $qrPoolManager->cleanupDuplicateAssignments();

if ($allResult['success']) {
    echo "✅ Tüm duplicate temizleme başarılı!\n";
    echo "Temizlenen profil sayısı: " . $allResult['profiles_cleaned'] . "\n";
    echo "Serbest bırakılan QR sayısı: " . $allResult['qr_codes_freed'] . "\n";
} else {
    echo "❌ Hata: " . $allResult['error'] . "\n";
}

echo "\n=== İşlem Tamamlandı ===\n";
?>
