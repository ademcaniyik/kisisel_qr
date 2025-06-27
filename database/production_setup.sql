-- Production Veritabanı Kurulum Scripti
-- Bu dosyayı sunucuda MySQL'de çalıştırın

-- 1. Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS acdisoftware_kisisel_qr 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 2. Veritabanını seç
USE acdisoftware_kisisel_qr;

-- 3. Profiles tablosu
CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    bio TEXT,
    profile_image VARCHAR(255),
    theme VARCHAR(50) DEFAULT 'theme1',
    slug VARCHAR(255) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    social_links JSON,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_created (created_at)
);

-- 4. QR Codes tablosu
CREATE TABLE IF NOT EXISTS qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    qr_code VARCHAR(8) UNIQUE NOT NULL,
    qr_image_path VARCHAR(255) NOT NULL,
    scan_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE,
    INDEX idx_qr_code (qr_code),
    INDEX idx_profile_id (profile_id),
    INDEX idx_active (is_active)
);

-- 5. Orders tablosu
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(255),
    profile_id INT,
    profile_slug VARCHAR(255),
    product_type VARCHAR(100) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'bank_transfer',
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    special_requests TEXT,
    admin_notes TEXT,
    whatsapp_sent BOOLEAN DEFAULT FALSE,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_order_date (order_date),
    INDEX idx_profile_id (profile_id)
);

-- 6. Admin kullanıcıları tablosu
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    full_name VARCHAR(255),
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_active (is_active)
);

-- 7. İstatistikler için view'lar
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'pending') as pending_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'completed') as completed_orders,
    (SELECT COUNT(*) FROM profiles) as total_profiles,
    (SELECT COUNT(*) FROM qr_codes) as total_qr_codes,
    (SELECT SUM(scan_count) FROM qr_codes) as total_scans;

-- 8. Demo profil oluştur (isteğe bağlı)
INSERT IGNORE INTO profiles (name, phone, email, bio, slug, theme, social_links) 
VALUES (
    'Demo Kullanıcı',
    '+90 555 000 00 00',
    'demo@example.com',
    'Bu bir demo profilidir. Kendi profilinizi oluşturmak için QR Sticker siparişi verin!',
    'demo-profil',
    'theme1',
    JSON_OBJECT(
        'instagram', 'https://instagram.com/demo',
        'website', 'https://example.com'
    )
);

-- Kurulum tamamlandı mesajı
SELECT 'Veritabanı kurulumu başarıyla tamamlandı!' as message;
