<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Önce tablonun var olduğundan emin olalım
    $createTable = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$connection->query($createTable)) {
        throw new Exception("Tablo oluşturulamadı: " . $connection->error);
    }
    
    // Admin hesabı için değerler
    $username = 'admin';
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Önce eski admin hesabını silelim
    $connection->query("DELETE FROM admins WHERE username = 'admin'");
    
    // Yeni admin hesabını oluştur
    $stmt = $connection->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "Admin hesabı başarıyla oluşturuldu!\n";
        echo "Kullanıcı adı: admin\n";
        echo "Şifre: admin123\n";
    } else {
        throw new Exception("Admin hesabı oluşturulamadı: " . $stmt->error);
    }
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
