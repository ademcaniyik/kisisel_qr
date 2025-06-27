<?php
require_once __DIR__ . '/../config/database.php';

class Utilities {
    public static function generateUniqueId($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function getDeviceInfo() {
        return json_encode([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public static function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }    public static function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /kisisel_qr_canli/admin/login.php');
            exit();
        }
    }

    public static function logScan($qrId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO scan_statistics (qr_id, device_info, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        
        $deviceInfo = self::getDeviceInfo();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->bind_param("ssss", $qrId, $deviceInfo, $ipAddress, $userAgent);
        return $stmt->execute();
    }

    public static function generateSlug() {
        return bin2hex(random_bytes(16)); // 32 karakterlik benzersiz bir string üretir
    }

    public static function validateImageUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return "Sadece JPG, PNG ve GIF dosyaları yüklenebilir.";
        }

        if ($file['size'] > $maxSize) {
            return "Dosya boyutu 5MB'dan küçük olmalıdır.";
        }

        return true;
    }

    public static function getSocialIcon($platform) {
        return match (strtolower($platform)) {
            'facebook' => 'fab fa-facebook-f',
            'twitter' => 'fab fa-twitter',
            'instagram' => 'fab fa-instagram',
            'linkedin' => 'fab fa-linkedin-in',
            'github' => 'fab fa-github',
            'youtube' => 'fab fa-youtube',
            'tiktok' => 'fab fa-tiktok',
            'pinterest' => 'fab fa-pinterest',
            'spotify' => 'fab fa-spotify',
            'medium' => 'fab fa-medium',
            'twitch' => 'fab fa-twitch',
            'discord' => 'fab fa-discord',
            'reddit' => 'fab fa-reddit',
            'whatsapp' => 'fab fa-whatsapp',
            'telegram' => 'fab fa-telegram',
            'snapchat' => 'fab fa-snapchat',
            default => 'fas fa-link'
        };
    }

    // --- RATE LIMITING ---
    public static function rateLimit($key, $limit = 60, $seconds = 60) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $now = time();
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [];
        }
        // Eski kayıtları temizle
        $_SESSION['rate_limit'][$key] = array_filter(
            $_SESSION['rate_limit'][$key],
            function($timestamp) use ($now, $seconds) {
                return ($now - $timestamp) < $seconds;
            }
        );
        if (count($_SESSION['rate_limit'][$key]) >= $limit) {
            return false;
        }
        $_SESSION['rate_limit'][$key][] = $now;
        return true;
    }

    // --- CSRF TOKEN ---
    public static function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    public static function validateCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function logAdminAction($action, $details = null) {
        $logFile = __DIR__ . '/../logs/admin_actions.log';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] IP: $ip | Action: $action";
        if ($details) {
            $entry .= " | Details: " . (is_array($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : $details);
        }
        $entry .= " | UA: $userAgent\n";
        file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
?>
