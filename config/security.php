<?php
/**
 * Production Güvenlik ve Error Handling Ayarları
 * Bu dosya production ortamında error reporting ve güvenlik ayarlarını yönetir
 */

// Session ayarlarını session başlamadan önce yapılandır
if (session_status() === PHP_SESSION_NONE) {
    // Session güvenlik ayarları
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', 7200); // 2 saat
    
    // Production ortamında ek güvenlik
    if ((isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') || 
        (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') === false)) {
        ini_set('session.cookie_secure', '1'); // HTTPS için
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', 3600); // 1 saat
    }
}

// Ortam tespiti (.env'den)
$isProduction = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') || 
                (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') === false);

if ($isProduction) {
    // Production ortamı - Hataları gizle
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error_log');
    
    // Debug bilgilerini gizle
    ini_set('expose_php', '0');
    
    // Güvenlik başlıkları
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
} else {
    // Development ortamı - Hataları göster
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error_log');
}

// Güvenli hata yakalama fonksiyonu
function handleError($errno, $errstr, $errfile, $errline) {
    // Kritik hataları logla
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($errorMessage, 3, __DIR__ . '/../logs/error_log');
    
    global $isProduction;
    if ($isProduction) {
        // Production'da kullanıcıya güvenli mesaj göster
        http_response_code(500);
        include __DIR__ . '/../errors/500.php';
        exit();
    }
    
    return false; // PHP'nin varsayılan error handler'ını çalıştır
}

// Exception yakalama fonksiyonu
function handleException($exception) {
    // Exception'ı logla
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Uncaught Exception: " . $exception->getMessage() . 
                   " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($errorMessage, 3, __DIR__ . '/../logs/error_log');
    
    global $isProduction;
    if ($isProduction) {
        // Production'da kullanıcıya güvenli mesaj göster
        http_response_code(500);
        include __DIR__ . '/../errors/500.php';
        exit();
    } else {
        // Development'da detayları göster
        echo "<h1>Uncaught Exception</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
        echo "<h2>Stack Trace:</h2>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    }
}

// Error handler'ları kaydet
set_error_handler('handleError');
set_exception_handler('handleException');

// Fatal error yakalama
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Fatal Error: " . $error['message'] . 
                       " in " . $error['file'] . " on line " . $error['line'] . "\n";
        error_log($errorMessage, 3, __DIR__ . '/../logs/error_log');
        
        global $isProduction;
        if ($isProduction) {
            http_response_code(500);
            include __DIR__ . '/../errors/500.php';
        }
    }
});
