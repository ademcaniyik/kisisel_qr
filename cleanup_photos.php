<?php
/**
 * Kullanılmayan profil fotoğraflarını temizleme script'i
 * Veritabanında kayıtlı olmayan fotoğrafları siler
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/ImageOptimizer.php';

class PhotoCleanup {
    private $db;
    private $connection;
    private $imageOptimizer;
    private $uploadPath;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
        $this->imageOptimizer = new ImageOptimizer();
        $this->uploadPath = __DIR__ . '/public/uploads/profiles/';
    }
    
    /**
     * Veritabanında kayıtlı olan fotoğraf dosyalarını getir
     */
    private function getUsedPhotos() {
        $usedPhotos = [];
        
        $sql = "SELECT photo_data FROM profiles WHERE photo_data IS NOT NULL AND photo_data != ''";
        $result = $this->connection->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $photoData = json_decode($row['photo_data'], true);
                if ($photoData && !empty($photoData['filename'])) {
                    $usedPhotos[] = $photoData['filename'];
                }
            }
        }
        
        return $usedPhotos;
    }
    
    /**
     * Disk üzerindeki tüm fotoğraf dosyalarını getir
     */
    private function getAllPhotoFiles() {
        $allFiles = [];
        
        // Ana dizindeki dosyalar
        $files = glob($this->uploadPath . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        foreach ($files as $file) {
            $allFiles[] = basename($file);
        }
        
        return $allFiles;
    }
    
    /**
     * Kullanılmayan fotoğrafları bul ve sil
     */
    public function cleanupUnusedPhotos($dryRun = true) {
        echo "🧹 Fotoğraf temizleme işlemi başlıyor...\n\n";
        
        $usedPhotos = $this->getUsedPhotos();
        $allFiles = $this->getAllPhotoFiles();
        
        echo "📊 İstatistikler:\n";
        echo "- Veritabanında kayıtlı fotoğraf: " . count($usedPhotos) . "\n";
        echo "- Disk üzerindeki toplam dosya: " . count($allFiles) . "\n\n";
        
        // Kullanılmayan dosyaları bul
        $unusedFiles = array_diff($allFiles, $usedPhotos);
        
        if (empty($unusedFiles)) {
            echo "✅ Temizlenecek dosya bulunamadı!\n";
            return;
        }
        
        echo "🗑️  Kullanılmayan dosyalar (" . count($unusedFiles) . " adet):\n";
        
        $totalSizeFreed = 0;
        $deletedCount = 0;
        
        foreach ($unusedFiles as $filename) {
            $filePath = $this->uploadPath . $filename;
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            $totalSizeFreed += $fileSize;
            
            echo "- " . $filename . " (" . $this->formatBytes($fileSize) . ")";
            
            if (!$dryRun) {
                $deleteResult = $this->imageOptimizer->deleteImageFiles($filename);
                if ($deleteResult['success']) {
                    echo " ✅ SİLİNDİ";
                    $deletedCount++;
                } else {
                    echo " ❌ HATA: " . $deleteResult['message'];
                }
            } else {
                echo " [TEST MODU]";
            }
            echo "\n";
        }
        
        echo "\n📈 Özet:\n";
        if ($dryRun) {
            echo "- Test modu: " . count($unusedFiles) . " dosya silinebilir\n";
            echo "- Boşaltılabilir alan: " . $this->formatBytes($totalSizeFreed) . "\n";
            echo "\n💡 Gerçekten silmek için: php cleanup_photos.php --delete\n";
        } else {
            echo "- Silinen dosya: $deletedCount / " . count($unusedFiles) . "\n";
            echo "- Boşaltılan alan: " . $this->formatBytes($totalSizeFreed) . "\n";
        }
    }
    
    /**
     * Byte'ları okunabilir formata çevir
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Disk kullanım raporu
     */
    public function getDiskUsageReport() {
        $usedPhotos = $this->getUsedPhotos();
        $allFiles = $this->getAllPhotoFiles();
        
        $totalSize = 0;
        $usedSize = 0;
        
        foreach ($allFiles as $filename) {
            $filePath = $this->uploadPath . $filename;
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            $totalSize += $fileSize;
            
            if (in_array($filename, $usedPhotos)) {
                $usedSize += $fileSize;
            }
        }
        
        $wastedSize = $totalSize - $usedSize;
        $wastedPercentage = $totalSize > 0 ? ($wastedSize / $totalSize) * 100 : 0;
        
        echo "💾 Disk Kullanım Raporu:\n";
        echo "- Toplam dosya: " . count($allFiles) . "\n";
        echo "- Kullanılan dosya: " . count($usedPhotos) . "\n";
        echo "- Gereksiz dosya: " . (count($allFiles) - count($usedPhotos)) . "\n\n";
        
        echo "- Toplam boyut: " . $this->formatBytes($totalSize) . "\n";
        echo "- Kullanılan alan: " . $this->formatBytes($usedSize) . "\n";
        echo "- İsraf edilen alan: " . $this->formatBytes($wastedSize) . " (%" . round($wastedPercentage, 1) . ")\n";
    }
}

// Script çalıştırma
if (php_sapi_name() === 'cli') {
    $cleanup = new PhotoCleanup();
    
    $dryRun = true;
    if (isset($argv[1]) && $argv[1] === '--delete') {
        $dryRun = false;
    }
    
    if (isset($argv[1]) && $argv[1] === '--report') {
        $cleanup->getDiskUsageReport();
    } else {
        $cleanup->cleanupUnusedPhotos($dryRun);
    }
} else {
    echo "Bu script sadece komut satırından çalıştırılabilir.";
}
