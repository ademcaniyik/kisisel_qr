<?php
/**
 * Site Konfigürasyon Dosyası
 * URL ve path ayarlarını merkezi olarak yönetir
 */

// Site base URL'si - Production'da değiştirilecek
define('SITE_BASE_URL', 'https://acdisoftware.com.tr/kisisel_qr');
define('SITE_BASE_PATH', '/kisisel_qr'); // Sunucudaki path

// Local development için
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define('SITE_BASE_URL_DEV', 'http://localhost/kisisel_qr');
    define('SITE_BASE_PATH_DEV', '/kisisel_qr');
} else {
    define('SITE_BASE_URL_DEV', SITE_BASE_URL);
    define('SITE_BASE_PATH_DEV', SITE_BASE_PATH);
}

/**
 * Ortama göre base URL döndürür
 */
function getBaseUrl() {
    return (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) 
        ? SITE_BASE_URL_DEV 
        : SITE_BASE_URL;
}

/**
 * Ortama göre base path döndürür
 */
function getBasePath() {
    return (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) 
        ? SITE_BASE_PATH_DEV 
        : SITE_BASE_PATH;
}

/**
 * Asset URL'si oluşturur
 */
function assetUrl($path) {
    return getBasePath() . '/assets/' . ltrim($path, '/');
}

/**
 * Public URL'si oluşturur
 */
function publicUrl($path) {
    return getBasePath() . '/public/' . ltrim($path, '/');
}

/**
 * Admin URL'si oluşturur
 */
function adminUrl($path = '') {
    return getBasePath() . '/admin/' . ltrim($path, '/');
}

/**
 * Full URL oluşturur
 */
function fullUrl($path = '') {
    return getBaseUrl() . '/' . ltrim($path, '/');
}
?>
