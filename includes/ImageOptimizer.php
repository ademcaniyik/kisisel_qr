<?php
/**
 * Image Optimization ve Thumbnail Oluşturma Sınıfı
 * WebP format desteği ve farklı boyutlarda thumbnail oluşturma
 */

class ImageOptimizer {
    private $uploadPath;
    private $thumbnailSizes;
    private $maxFileSize;
    private $allowedTypes;
    private $jpegQuality;
    private $webpQuality;
    
    public function __construct() {
        $this->uploadPath = __DIR__ . '/../public/uploads/profiles/';
        $this->thumbnailSizes = [
            'thumb' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 600, 'height' => 600]
        ];
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $this->jpegQuality = 85;
        $this->webpQuality = 80;
        
        // Upload klasörünü oluştur
        $this->ensureDirectoryExists($this->uploadPath);
        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $this->ensureDirectoryExists($this->uploadPath . $size . '/');
        }
    }
    
    /**
     * Resim yükleme ve optimizasyon
     */
    public function uploadAndOptimize($file, $filename = null) {
        try {
            // Dosya doğrulaması
            $validation = $this->validateFile($file);
            if (!$validation['success']) {
                return $validation;
            }
            
            // Benzersiz dosya adı oluştur
            if (!$filename) {
                $filename = $this->generateFilename($file['name']);
            }
            
            // Orijinal resmi yükle ve optimize et
            $originalPath = $this->uploadPath . $filename;
            $image = $this->createImageFromFile($file['tmp_name'], $file['type']);
            
            if (!$image) {
                return ['success' => false, 'message' => 'Resim dosyası işlenemedi.'];
            }
            
            // Orijinal boyutları al
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            
            // Orijinal resmi kaydet (JPEG olarak optimize edilmiş)
            $this->saveOptimizedImage($image, $originalPath, 'jpeg');
            
            // WebP versiyonunu kaydet
            $webpPath = $this->uploadPath . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
            $this->saveOptimizedImage($image, $webpPath, 'webp');
            
            // Thumbnail'ları oluştur
            $thumbnails = [];
            foreach ($this->thumbnailSizes as $size => $dimensions) {
                $thumbnailPath = $this->uploadPath . $size . '/' . $filename;
                $webpThumbnailPath = $this->uploadPath . $size . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                
                // Thumbnail boyutlarını hesapla (aspect ratio korunarak)
                $newDimensions = $this->calculateThumbnailSize(
                    $originalWidth, 
                    $originalHeight, 
                    $dimensions['width'], 
                    $dimensions['height']
                );
                
                // Thumbnail oluştur
                $thumbnail = $this->createThumbnail(
                    $image, 
                    $newDimensions['width'], 
                    $newDimensions['height']
                );
                
                if ($thumbnail) {
                    // JPEG thumbnail
                    $this->saveOptimizedImage($thumbnail, $thumbnailPath, 'jpeg');
                    // WebP thumbnail
                    $this->saveOptimizedImage($thumbnail, $webpThumbnailPath, 'webp');
                    
                    $thumbnails[$size] = [
                        'jpeg' => 'public/uploads/profiles/' . $size . '/' . $filename,
                        'webp' => 'public/uploads/profiles/' . $size . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp',
                        'width' => $newDimensions['width'],
                        'height' => $newDimensions['height']
                    ];
                    
                    imagedestroy($thumbnail);
                }
            }
            
            imagedestroy($image);
            
            return [
                'success' => true,
                'filename' => $filename,
                'original' => [
                    'jpeg' => 'public/uploads/profiles/' . $filename,
                    'webp' => 'public/uploads/profiles/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp',
                    'width' => $originalWidth,
                    'height' => $originalHeight
                ],
                'thumbnails' => $thumbnails,
                'filesize' => filesize($originalPath)
            ];
            
        } catch (Exception $e) {
            error_log('Image optimization error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Resim işleme hatası: ' . $e->getMessage()];
        }
    }
    
    /**
     * Dosya doğrulaması
     */
    private function validateFile($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Dosya yükleme hatası.'];
        }
        
        if ($file['size'] > $this->maxFileSize) {
            $maxSizeMB = $this->maxFileSize / (1024 * 1024);
            return ['success' => false, 'message' => "Dosya boyutu {$maxSizeMB}MB'dan büyük olamaz."];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['success' => false, 'message' => 'Desteklenmeyen dosya türü. Sadece JPEG, PNG, GIF ve WebP dosyaları kabul edilir.'];
        }
        
        return ['success' => true];
    }
    
    /**
     * Benzersiz dosya adı oluştur
     */
    private function generateFilename($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        // WebP dışında tüm formatları JPEG'e çevir
        if (!in_array($extension, ['jpg', 'jpeg'])) {
            $extension = 'jpg';
        }
        return uniqid() . '.' . $extension;
    }
    
    /**
     * Dosya türüne göre resim kaynağı oluştur
     */
    private function createImageFromFile($filePath, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return imagecreatefromwebp($filePath);
            default:
                return false;
        }
    }
    
    /**
     * Optimize edilmiş resmi kaydet
     */
    private function saveOptimizedImage($image, $path, $format) {
        switch ($format) {
            case 'jpeg':
                return imagejpeg($image, $path, $this->jpegQuality);
            case 'webp':
                return imagewebp($image, $path, $this->webpQuality);
            case 'png':
                return imagepng($image, $path, 6); // Compression level 6
            default:
                return false;
        }
    }
    
    /**
     * Thumbnail boyutlarını hesapla (aspect ratio korunarak)
     */
    private function calculateThumbnailSize($originalWidth, $originalHeight, $maxWidth, $maxHeight) {
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        
        return [
            'width' => round($originalWidth * $ratio),
            'height' => round($originalHeight * $ratio)
        ];
    }
    
    /**
     * Thumbnail oluştur
     */
    private function createThumbnail($sourceImage, $newWidth, $newHeight) {
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        // PNG ve WebP için şeffaflık desteği
        if (function_exists('imageantialias')) {
            imageantialias($thumbnail, true);
        }
        
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefill($thumbnail, 0, 0, $transparent);
        imagealphablending($thumbnail, true);
        
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        
        imagecopyresampled(
            $thumbnail, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $sourceWidth, $sourceHeight
        );
        
        return $thumbnail;
    }
    
    /**
     * Klasör varlığını kontrol et ve oluştur
     */
    private function ensureDirectoryExists($path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    
    /**
     * Profil resmini sil (tüm boyutlarıyla birlikte)
     */
    public function deleteProfileImage($filename) {
        if (empty($filename)) return;
        
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Orijinal dosyaları sil
        @unlink($this->uploadPath . $filename);
        @unlink($this->uploadPath . $baseName . '.webp');
        
        // Thumbnail'ları sil
        foreach ($this->thumbnailSizes as $size => $dimensions) {
            @unlink($this->uploadPath . $size . '/' . $filename);
            @unlink($this->uploadPath . $size . '/' . $baseName . '.webp');
        }
    }
    
    /**
     * Profil resmini sil ve silinen dosyalar hakkında detay döndür
     */
    public function deleteImageFiles($filename) {
        if (empty($filename)) {
            return ['success' => false, 'message' => 'Dosya adı boş', 'deleted_files' => []];
        }
        
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $deletedFiles = [];
        
        // Orijinal dosyaları sil
        $originalJpeg = $this->uploadPath . $filename;
        $originalWebp = $this->uploadPath . $baseName . '.webp';
        
        if (file_exists($originalJpeg) && unlink($originalJpeg)) {
            $deletedFiles[] = $originalJpeg;
        }
        if (file_exists($originalWebp) && unlink($originalWebp)) {
            $deletedFiles[] = $originalWebp;
        }
        
        // Thumbnail'ları sil
        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $thumbJpeg = $this->uploadPath . $size . '/' . $filename;
            $thumbWebp = $this->uploadPath . $size . '/' . $baseName . '.webp';
            
            if (file_exists($thumbJpeg) && unlink($thumbJpeg)) {
                $deletedFiles[] = $thumbJpeg;
            }
            if (file_exists($thumbWebp) && unlink($thumbWebp)) {
                $deletedFiles[] = $thumbWebp;
            }
        }
        
        return [
            'success' => true,
            'message' => count($deletedFiles) . ' dosya silindi',
            'deleted_files' => $deletedFiles
        ];
    }
    
    /**
     * Optimize edilmiş resim URL'si oluştur
     */
    public function getOptimizedImageUrl($filename, $size = 'original', $format = 'auto') {
        if (empty($filename)) {
            return '/kisisel_qr_canli/assets/images/default-profile.svg';
        }
        
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        // WebP desteğini kontrol et (basit browser detection)
        $supportsWebP = ($format === 'webp') || 
                       ($format === 'auto' && isset($_SERVER['HTTP_ACCEPT']) && 
                        strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false);
        
        if ($size === 'original') {
            $path = $supportsWebP ? 
                "/kisisel_qr_canli/public/uploads/profiles/{$baseName}.webp" :
                "/kisisel_qr_canli/public/uploads/profiles/{$filename}";
        } else {
            $path = $supportsWebP ?
                "/kisisel_qr_canli/public/uploads/profiles/{$size}/{$baseName}.webp" :
                "/kisisel_qr_canli/public/uploads/profiles/{$size}/{$filename}";
        }
        
        // Dosya var mı kontrol et
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
        if (!file_exists($fullPath)) {
            // Fallback to JPEG
            if ($size === 'original') {
                $path = "/kisisel_qr_canli/public/uploads/profiles/{$filename}";
            } else {
                $path = "/kisisel_qr_canli/public/uploads/profiles/{$size}/{$filename}";
            }
            
            // Hala yoksa default resim
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
            if (!file_exists($fullPath)) {
                return '/kisisel_qr_canli/assets/images/default-profile.svg';
            }
        }
        
        return $path;
    }
    
    /**
     * Responsive resim HTML'i oluştur
     */
    public function generateResponsiveImageHtml($filename, $alt = '', $class = '', $sizes = 'auto') {
        if (empty($filename)) {
            return '<img src="/kisisel_qr_canli/assets/images/default-profile.svg" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '">';
        }
        
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        
        // WebP source set
        $webpSrcset = [];
        $jpegSrcset = [];
        
        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $webpSrcset[] = "/kisisel_qr_canli/public/uploads/profiles/{$size}/{$baseName}.webp {$dimensions['width']}w";
            $jpegSrcset[] = "/kisisel_qr_canli/public/uploads/profiles/{$size}/{$filename} {$dimensions['width']}w";
        }
        
        if ($sizes === 'auto') {
            $sizes = "(max-width: 150px) 150px, (max-width: 300px) 300px, 600px";
        }
        
        $html = '<picture>';
        $html .= '<source srcset="' . implode(', ', $webpSrcset) . '" sizes="' . $sizes . '" type="image/webp">';
        $html .= '<source srcset="' . implode(', ', $jpegSrcset) . '" sizes="' . $sizes . '" type="image/jpeg">';
        $html .= '<img src="' . $this->getOptimizedImageUrl($filename, 'medium', 'jpeg') . '" alt="' . htmlspecialchars($alt) . '" class="' . htmlspecialchars($class) . '" loading="lazy">';
        $html .= '</picture>';
        
        return $html;
    }
}
?>
