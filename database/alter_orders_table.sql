-- Orders tablosuna profile_id alanı ekle
ALTER TABLE orders ADD COLUMN profile_id INT DEFAULT NULL AFTER customer_email;
ALTER TABLE orders ADD COLUMN profile_slug VARCHAR(32) DEFAULT NULL AFTER profile_id;

-- Foreign key constraint ekle (isteğe bağlı)
-- ALTER TABLE orders ADD CONSTRAINT fk_orders_profile FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL;

-- Index ekle
ALTER TABLE orders ADD INDEX idx_profile_id (profile_id);
ALTER TABLE orders ADD INDEX idx_profile_slug (profile_slug);
