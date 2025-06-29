<?php
/**
 * QR Pool Başlatma Script'i - Basitleştirilmiş Versiyon
 * İlk 100 QR'ı oluşturmak için kullanılır
 */

// Sadece admin erişimi için güvenlik
session_start();
if (!isset($_SESSION['admin_logged_in']) && PHP_SAPI !== 'cli') {
    die('Bu script sadece admin tarafından çalıştırılabilir.');
}

require_once 'config/database.php';
require_once 'config/site.php'; // Base URL fonksiyonu

echo "<h2>QR Pool Başlatma İşlemi (Basitleştirilmiş)</h2>\n";

try {
    // Database bağlantısını test et
    $db = Database::getInstance();
    echo "✅ Database bağlantısı başarılı\n<br>";
    
    // Tabloları tek tek oluştur
    echo "<h3>1. Tabloları oluşturuluyor...</h3>\n";
    
    // QR Pool tablosu
    echo "➤ qr_pool tablosu oluşturuluyor...\n<br>";
    $sql1 = "CREATE TABLE IF NOT EXISTS qr_pool (
        id INT PRIMARY KEY AUTO_INCREMENT,
        pool_id VARCHAR(10) UNIQUE NOT NULL,
        qr_code_id VARCHAR(32) UNIQUE NOT NULL,
        edit_token VARCHAR(32) UNIQUE NOT NULL,
        edit_code VARCHAR(6) UNIQUE NOT NULL,
        status ENUM('available', 'assigned', 'delivered') DEFAULT 'available',
        profile_id INT NULL,
        batch_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        assigned_at TIMESTAMP NULL,
        delivered_at TIMESTAMP NULL,
        
        INDEX idx_status (status),
        INDEX idx_pool_id (pool_id),
        INDEX idx_qr_code_id (qr_code_id),
        INDEX idx_edit_token (edit_token)
    )";
    
    if (!$db->query($sql1)) {
        throw new Exception("qr_pool tablosu oluşturulamadı: " . $db->getConnection()->error);
    }
    echo "✅ qr_pool tablosu oluşturuldu\n<br>";
    
    // Print Batches tablosu
    echo "➤ print_batches tablosu oluşturuluyor...\n<br>";
    $sql2 = "CREATE TABLE IF NOT EXISTS print_batches (
        id INT PRIMARY KEY AUTO_INCREMENT,
        batch_name VARCHAR(50) UNIQUE NOT NULL,
        pool_start_id VARCHAR(10) NOT NULL,
        pool_end_id VARCHAR(10) NOT NULL,
        quantity INT DEFAULT 100,
        status ENUM('planned', 'ready_to_print', 'printed', 'stocked') DEFAULT 'planned',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        printed_at TIMESTAMP NULL,
        notes TEXT
    )";
    
    if (!$db->query($sql2)) {
        throw new Exception("print_batches tablosu oluşturulamadı: " . $db->getConnection()->error);
    }
    echo "✅ print_batches tablosu oluşturuldu\n<br>";
    
    // Profile Edit Logs tablosu
    echo "➤ profile_edit_logs tablosu oluşturuluyor...\n<br>";
    $sql3 = "CREATE TABLE IF NOT EXISTS profile_edit_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        profile_id INT NOT NULL,
        qr_pool_id INT NOT NULL,
        edit_type ENUM('profile_info', 'social_media', 'theme', 'image') NOT NULL,
        old_values JSON,
        new_values JSON,
        edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45),
        user_agent TEXT,
        
        INDEX idx_profile_id (profile_id),
        INDEX idx_qr_pool_id (qr_pool_id),
        INDEX idx_edit_type (edit_type)
    )";
    
    if (!$db->query($sql3)) {
        throw new Exception("profile_edit_logs tablosu oluşturulamadı: " . $db->getConnection()->error);
    }
    echo "✅ profile_edit_logs tablosu oluşturuldu\n<br>";
    
    // Orders tablosuna kolon ekle (eğer yoksa)
    echo "➤ orders tablosuna qr_pool_id kolonu ekleniyor...\n<br>";
    $sql4 = "SHOW COLUMNS FROM orders LIKE 'qr_pool_id'";
    $result = $db->query($sql4);
    
    if ($result->num_rows === 0) {
        $sql4_alter = "ALTER TABLE orders ADD COLUMN qr_pool_id INT NULL AFTER profile_slug";
        if (!$db->query($sql4_alter)) {
            throw new Exception("orders tablosuna qr_pool_id kolonu eklenemedi: " . $db->getConnection()->error);
        }
        echo "✅ orders tablosuna qr_pool_id kolonu eklendi\n<br>";
    } else {
        echo "ℹ️ orders tablosunda qr_pool_id kolonu zaten var\n<br>";
    }
    
    echo "✅ Tüm tablolar başarıyla hazırlandı\n<br>";
    
    // QR Pool Manager'ı yükle
    echo "<h3>2. QR Pool Manager yükleniyor...</h3>\n";
    
    // QRPoolManager sınıfının varlığını kontrol et
    if (!class_exists('QRPoolManager')) {
        if (!file_exists(__DIR__ . '/includes/QRPoolManager.php')) {
            throw new Exception("QRPoolManager.php dosyası bulunamadı: " . __DIR__ . '/includes/QRPoolManager.php');
        }
        require_once __DIR__ . '/includes/QRPoolManager.php';
    }
    
    $qrPoolManager = new QRPoolManager();
    echo "✅ QRPoolManager yüklendi\n<br>";
    
    echo "<h3>3. İlk QR Batch'i oluşturuluyor (100 adet)...</h3>\n";
    
    // Zaten batch var mı kontrol et
    $existingBatches = $db->query("SELECT COUNT(*) as count FROM print_batches")->fetch_assoc();
    if ($existingBatches['count'] > 0) {
        echo "ℹ️ Zaten " . $existingBatches['count'] . " batch var. Yeni batch oluşturuluyor...\n<br>";
    }
    
    // QR Pool'da zaten QR var mı kontrol et
    $existingQRs = $db->query("SELECT COUNT(*) as count FROM qr_pool")->fetch_assoc();
    if ($existingQRs['count'] > 0) {
        echo "ℹ️ QR Pool'da zaten " . $existingQRs['count'] . " QR var.\n<br>";
        
        // Stok durumunu göster
        $stockStatus = $qrPoolManager->getStockStatus();
        echo "📊 Mevcut Stok - Toplam: {$stockStatus['total']}, Müsait: {$stockStatus['available']}, Atanmış: {$stockStatus['assigned']}\n<br>";
        
        if ($stockStatus['available'] >= 50) {
            echo "✅ Yeterli QR stoku var. Yeni batch oluşturma atlanıyor.\n<br>";
            echo "<h3>4. Mevcut QR'lar:</h3>\n";
            
            // Mevcut QR'ları göster
            $sampleQRs = $db->query("SELECT pool_id, qr_code_id, edit_token, edit_code FROM qr_pool ORDER BY id LIMIT 5")->fetch_all(MYSQLI_ASSOC);
            
            if (!empty($sampleQRs)) {
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
            }
            
            echo "<h3>✅ QR Pool sistemi hazır!</h3>\n";
            echo "<p><strong>Sistem Durumu:</strong></p>\n";
            echo "<ul>\n";
            echo "<li>✅ Tablolar oluşturuldu</li>\n";
            echo "<li>✅ QR Pool hazır ({$stockStatus['available']} müsait QR)</li>\n";
            echo "<li>✅ Sipariş sistemi QR Pool kullanabilir</li>\n";
            echo "<li>✅ Admin panel QR Pool kullanabilir</li>\n";
            echo "</ul>\n";
            
            // Admin panel linki
            echo "<p><a href='admin/dashboard.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Admin Panel'e Git</a></p>\n";
            echo "<p><a href='admin/qr_pool.php' style='display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px;'>QR Pool Yönetimi</a></p>\n";
            
            return; // Script'i sonlandır
        }
    }
    
    // İlk batch'i oluştur
    $result = $qrPoolManager->createNewBatch(100);
    
    if ($result['success']) {
        echo "✅ Batch başarıyla oluşturuldu!\n<br>";
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
        
        if (!empty($sampleQRs)) {
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
        }
        
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
    
    echo "<p><strong>Çözüm Önerileri:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>🔍 Veritabanı bağlantı bilgilerini kontrol edin</li>\n";
    echo "<li>🔐 MySQL kullanıcısının CREATE, ALTER yetkilerini kontrol edin</li>\n";
    echo "<li>📝 Tabloların zaten var olup olmadığını kontrol edin</li>\n";
    echo "<li>🔄 Sayfayı yenilemeyi deneyin</li>\n";
    echo "</ul>\n";
}

// CLI için farklı çıktı
if (PHP_SAPI === 'cli') {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "QR Pool Kurulum Tamamlandı!\n";
    echo str_repeat("=", 50) . "\n";
}
?>
