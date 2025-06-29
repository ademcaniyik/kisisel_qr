<?php
header('Content-Type: text/html; charset=utf-8');

// Güvenlik ayarlarını yükle
require_once __DIR__ . '/security.php';

// .env dosyasından ortam değişkenlerini yükle
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);

// MySQL bağlantısı için karakter seti ayarları
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

class Database {
    private $connection;
    private static $instance = null;    private function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            die("Veritabanı bağlantı hatası: " . $this->connection->connect_error);
        }
        
        // Karakter seti ayarları
        $this->connection->set_charset("utf8mb4");
        $this->connection->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->connection->query("SET CHARACTER SET utf8mb4");
        $this->connection->query("SET collation_connection = utf8mb4_unicode_ci");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }

    public function __get($property) {
        if ($property === 'insert_id') {
            return $this->connection->insert_id;
        }
        return null;
    }
}
?>
