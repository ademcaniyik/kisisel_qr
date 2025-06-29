<?php
/**
 * QR Pool Başlatma Script'i
 * İlk 100 QR'ı oluşturmak için kullanılır
 * 
 * KULLANIM:
 * - Web tarayıcıdan: http://yourdomain.com/init_qr_pool.php
 * - Komut satırından: php init_qr_pool.php
 */

// Sadece admin erişimi için güvenlik
session_start();
if (!isset($_SESSION['admin_logged_in']) && PHP_SAPI !== 'cli') {
    die('Bu script sadece admin tarafından çalıştırılabilir.');
}

require_once 'config/database.php';
require_once 'config/site.php'; // Base URL fonksiyonu
require_once 'includes/QRPoolManager.php';

echo "<h2>QR Pool Başlatma İşlemi</h2>\n";

try {
    // Database bağlantısını test et
    $db = Database::getInstance();
    echo "✅ Database bağlantısı başarılı\n<br>";
    
    // Tabloları oluştur
    echo "<h3>1. Tabloları oluşturuluyor...</h3>\n";
    
    // SQL dosyasının varlığını kontrol et
    $sqlFilePath = 'database/qr_pool_setup.sql';
    if (!file_exists($sqlFilePath)) {
        throw new Exception("SQL dosyası bulunamadı: $sqlFilePath");
    }
    
    $sqlFile = file_get_contents($sqlFilePath);
    if ($sqlFile === false) {
        throw new Exception("SQL dosyası okunamadı: $sqlFilePath");
    }
    
    echo "📄 SQL dosyası okundu (" . strlen($sqlFile) . " karakter)\n<br>";
    
    // SQL komutlarını parçala ve çalıştır
    $sqlCommands = array_filter(array_map('trim', explode(';', $sqlFile)));
    echo "🔢 " . count($sqlCommands) . " SQL komutu bulundu\n<br>";
    
    foreach ($sqlCommands as $index => $sql) {
        if (!empty($sql)) {
            echo "➤ SQL " . ($index + 1) . " çalıştırılıyor...\n<br>";
            
            // SQL komutunu göster (ilk 100 karakteri)
            $sqlPreview = strlen($sql) > 100 ? substr($sql, 0, 100) . '...' : $sql;
            echo "📝 <code>" . htmlspecialchars($sqlPreview) . "</code>\n<br>";
            
            $result = $db->query($sql);
            if (!$result) {
                $error = $db->getConnection()->error;
                $errno = $db->getConnection()->errno;
                throw new Exception("SQL hatası (Komut " . ($index + 1) . ", Errno: $errno): $error\n\nSQL: " . htmlspecialchars($sql));
            }
            echo "✅ Başarılı\n<br>";
        }
    }
    echo "✅ Tüm tablolar başarıyla oluşturuldu\n<br>";
    
    // QR Pool Manager'ı başlat
    $qrPoolManager = new QRPoolManager();
    echo "<h3>2. İlk QR Batch'i oluşturuluyor (100 adet)...</h3>\n";
    
    // İlk batch'i oluştur
    $result = $qrPoolManager->createNewBatch(100, 'BATCH001');
    
    if ($result['success']) {
        echo "✅ İlk batch başarıyla oluşturuldu!\n<br>";
        echo "📦 Batch: {$result['batch_name']}\n<br>";
        echo "🔢 Miktar: {$result['quantity']} QR\n<br>";
        echo "📋 Pool Aralığı: {$result['pool_range']}\n<br>";
        
        // Stok durumunu kontrol et
        $stockStatus = $qrPoolManager->getStockStatus();
        echo "<h3>3. Stok Durumu:</h3>\n";
        echo "📊 Toplam QR: {$stockStatus['total']}\n<br>";
        echo "✅ Müsait QR: {$stockStatus['available']}\n<br>";
        echo "🔒 Atanmış QR: {$stockStatus['assigned']}\n<br>";
        echo "📦 Teslim Edilmiş: {$stockStatus['delivered']}\n<br>";
        
        // Örnek QR'ları göster
        echo "<h3>4. Örnek QR'lar:</h3>\n";
        $sampleQRs = $db->query("SELECT pool_id, qr_code_id, edit_token, edit_code FROM qr_pool ORDER BY id LIMIT 5")->fetch_all(MYSQLI_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Pool ID</th><th>QR Code ID</th><th>Edit Token</th><th>Edit Code</th><th>Profil URL</th><th>Edit URL</th></tr>\n";
        
        foreach ($sampleQRs as $qr) {
            $profileUrl = getBaseUrl() . '/qr/' . $qr['qr_code_id'];
            $editUrl = getBaseUrl() . '/edit/' . $qr['edit_token'];
            
            echo "<tr>";
            echo "<td>{$qr['pool_id']}</td>";
            echo "<td>{$qr['qr_code_id']}</td>";
            echo "<td>{$qr['edit_token']}</td>";
            echo "<td>{$qr['edit_code']}</td>";
            echo "<td><a href='$profileUrl' target='_blank'>Profil</a></td>";
            echo "<td><a href='$editUrl' target='_blank'>Düzenle</a></td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        echo "<h3>✅ QR Pool başarıyla kuruldu!</h3>\n";
        echo "<p><strong>Sonraki Adımlar:</strong></p>\n";
        echo "<ul>\n";
        echo "<li>🖨️ Fiziksel QR sticker'larını bastırın</li>\n";
        echo "<li>📦 Batch durumunu 'printed' olarak güncelleyin</li>\n";
        echo "<li>🛒 Sipariş sistemi artık QR Pool kullanıyor</li>\n";
        echo "<li>👥 Admin panelden manuel profil oluşturma da QR Pool kullanıyor</li>\n";
        echo "</ul>\n";
        
        // Admin panel linki
        echo "<p><a href='admin/dashboard.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Admin Panel'e Git</a></p>\n";
        
    } else {
        throw new Exception("Batch oluşturma hatası: " . $result['error']);
    }
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
    echo "<h3 style='color: #d32f2f;'>❌ Hata Detayları</h3>\n";
    echo "<p><strong>Hata Mesajı:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p><strong>Dosya:</strong> " . htmlspecialchars($e->getFile()) . "</p>\n";
    echo "<p><strong>Satır:</strong> " . $e->getLine() . "</p>\n";
    
    // Stack trace'i güvenli şekilde göster
    echo "<details>\n";
    echo "<summary><strong>Detaylı Hata İzleme (Stack Trace)</strong></summary>\n";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>\n";
    echo "</details>\n";
    echo "</div>\n";
    
    // PHP hata logunu kontrol et
    $errorLog = ini_get('error_log');
    if ($errorLog && file_exists($errorLog)) {
        echo "<p><strong>İpucu:</strong> Daha fazla detay için PHP hata logunu kontrol edin: <code>" . htmlspecialchars($errorLog) . "</code></p>\n";
    }
    
    echo "<p><strong>Çözüm Önerileri:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>🔍 Veritabanı bağlantı bilgilerini kontrol edin</li>\n";
    echo "<li>📁 database/qr_pool_setup.sql dosyasının varlığını kontrol edin</li>\n";
    echo "<li>🔐 MySQL kullanıcısının CREATE, ALTER yetkilerini kontrol edin</li>\n";
    echo "<li>📝 Tabloların zaten var olup olmadığını kontrol edin</li>\n";
    echo "</ul>\n";
}

// CLI için farklı çıktı
if (PHP_SAPI === 'cli') {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "QR Pool Kurulum Tamamlandı!\n";
    echo str_repeat("=", 50) . "\n";
}
?>
