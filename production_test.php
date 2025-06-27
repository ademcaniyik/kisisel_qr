<?php
/**
 * Production Test Script
 * Bu dosyayı sunucuya yükledikten sonra çalıştırarak bağlantıları test edin
 * Test tamamlandıktan sonra bu dosyayı silin!
 */

echo "<h1>Kişisel QR - Production Test</h1>";
echo "<p>Test Tarihi: " . date('Y-m-d H:i:s') . "</p>";

// 1. PHP Versiyonu
echo "<h2>1. PHP Versiyonu</h2>";
echo "PHP Version: " . phpversion() . "<br>";
if (version_compare(phpversion(), '7.4', '>=')) {
    echo "✅ PHP versiyonu uygun<br>";
} else {
    echo "❌ PHP versiyonu 7.4 veya üzeri olmalı<br>";
}

// 2. .env dosyası kontrolü
echo "<h2>2. .env Dosyası</h2>";
if (file_exists('.env')) {
    echo "✅ .env dosyası mevcut<br>";
    
    // .env dosyasının içeriğini kontrol et
    $envContent = file_get_contents('.env');
    echo "📄 .env dosyası boyutu: " . strlen($envContent) . " bytes<br>";
    
    // Dotenv yükle
    require_once 'vendor/autoload.php';
    
    try {
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            echo "✅ .env dosyası başarıyla yüklendi<br>";
            echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'tanımsız') . "<br>";
            echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'tanımsız') . "<br>";
            echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'tanımsız') . "<br>";
        } else {
            echo "❌ Dotenv sınıfı bulunamadı<br>";
        }
    } catch (Exception $e) {
        echo "❌ .env dosyası yüklenemedi: " . $e->getMessage() . "<br>";
        echo "🔍 .env dosyası içeriği (ilk 500 karakter):<br>";
        echo "<pre style='background:#f5f5f5;padding:10px;font-size:12px;'>" . htmlspecialchars(substr($envContent, 0, 500)) . "</pre>";
        
        // Manuel parse deneme
        echo "<h3>Manuel .env Parse Denemesi:</h3>";
        $lines = explode("\n", $envContent);
        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                echo "Line " . ($lineNum + 1) . ": " . htmlspecialchars($key) . " = " . htmlspecialchars($value) . "<br>";
            } else {
                echo "❌ Hatalı satır " . ($lineNum + 1) . ": " . htmlspecialchars($line) . "<br>";
            }
        }
    }
} else {
    echo "❌ .env dosyası bulunamadı<br>";
}

// 3. Veritabanı Bağlantısı
echo "<h2>3. Veritabanı Bağlantısı</h2>";
try {
    // Önce .env ile deneme
    if (isset($_ENV['DB_HOST'])) {
        $db_host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $db_user = $_ENV['DB_USER'];
        $db_pass = $_ENV['DB_PASS'];
        echo "🔍 .env'den veritabanı bilgileri alındı<br>";
    } else {
        // Manuel değerler ile test
        echo "⚠️ .env yüklenemediği için manuel değerlerle test ediliyor<br>";
        $db_host = 'localhost';
        $db_name = 'acdisoftware_kisisel_qr';
        $db_user = 'acdisoftware_qr_user';
        $db_pass = 'STRONG_PASSWORD_HERE'; // Bu değer gerçek şifre ile değiştirilmeli
        echo "❌ Manuel test için .env dosyasındaki gerçek değerleri kullanmanız gerekiyor<br>";
    }
    
    if (isset($_ENV['DB_HOST'])) {
        require_once 'config/database.php';
        $db = Database::getInstance();
        $connection = $db->getConnection();
        echo "✅ Veritabanı bağlantısı başarılı<br>";
        echo "Veritabanı: " . DB_NAME . "<br>";
        
        // Tabloları kontrol et
        $tables = ['profiles', 'qr_codes', 'orders'];
        foreach ($tables as $table) {
            $result = $connection->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "✅ $table tablosu mevcut<br>";
            } else {
                echo "❌ $table tablosu bulunamadı<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "<br>";
}

// 4. Klasör İzinleri
echo "<h2>4. Klasör İzinleri</h2>";
$folders = [
    'public/qr_codes',
    'public/uploads',
    'public/uploads/profiles',
    'logs'
];

foreach ($folders as $folder) {
    if (is_dir($folder)) {
        if (is_writable($folder)) {
            echo "✅ $folder klasörü yazılabilir<br>";
        } else {
            echo "❌ $folder klasörü yazılamıyor<br>";
        }
    } else {
        echo "❌ $folder klasörü bulunamadı<br>";
    }
}

// 5. Composer Autoloader
echo "<h2>5. Composer</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Composer autoloader mevcut<br>";
} else {
    echo "❌ Composer autoloader bulunamadı<br>";
}

// 6. SSL/HTTPS
echo "<h2>6. HTTPS</h2>";
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    echo "✅ HTTPS aktif<br>";
} else {
    echo "⚠️ HTTPS aktif değil (production için gerekli)<br>";
}

// 7. URL Testleri
echo "<h2>7. URL Testleri</h2>";
$base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
echo "Base URL: $base_url<br>";

echo "<h2>Test Tamamlandı</h2>";
echo "<p style='color: red;'><strong>ÖNEMLİ: Bu dosyayı test tamamlandıktan sonra silin!</strong></p>";

if (isset($_ENV['DB_HOST'])) {
    echo "<p style='color: green;'><strong>✅ .env dosyası başarıyla yüklendi - sistem hazır!</strong></p>";
} else {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0;'>";
    echo "<h3>🔧 .env Dosyası Düzeltme Talimatları:</h3>";
    echo "<ol>";
    echo "<li>Sunucunuzdaki .env dosyasını düzenleyin</li>";
    echo "<li>Türkçe karakter ve boşluk içeren değerleri çift tırnak içine alın</li>";
    echo "<li>Örnek: <code>SITE_NAME=\"Kisisel QR\"</code></li>";
    echo "<li>Veritabanı bilgilerini gerçek değerlerle değiştirin</li>";
    echo "<li>Bu sayfayı tekrar yükleyin</li>";
    echo "</ol>";
    echo "<p><strong>Örnek temiz .env dosyası için .env.clean dosyasına bakın</strong></p>";
    echo "</div>";
}

echo "<p><a href='index.php'>Ana Sayfaya Git</a> | <a href='admin/'>Admin Panel</a></p>";
?>
