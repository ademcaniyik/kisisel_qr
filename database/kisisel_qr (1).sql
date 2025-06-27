-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Haz 2025, 22:46:23
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kisisel_qr`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `last_login`, `created_at`) VALUES
(3, 'admin', '$2y$10$5ZCpuHzTmlPiTWB630Mc7eurP1ou/jW/z8LpzhHfLhfeMZu6L90Yq', '2025-06-26 20:38:10', '2025-06-18 14:44:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `theme` varchar(50) DEFAULT 'default',
  `social_links` longtext DEFAULT NULL CHECK (json_valid(`social_links`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `slug` varchar(32) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `profiles`
--

INSERT INTO `profiles` (`id`, `name`, `bio`, `photo_url`, `theme`, `social_links`, `created_at`, `updated_at`, `slug`, `phone`) VALUES
(25, '', '', '/kisisel_qr/public/uploads/profiles/685db07a07b28.jpg', 'default', '[]', '2025-06-26 20:41:21', '2025-06-26 20:41:30', '41eb51db5fdb5fc3bfdeb6b05de9cf7c', '');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `qr_codes`
--

CREATE TABLE `qr_codes` (
  `id` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `profile_id` int(11) NOT NULL,
  `is_dynamic` tinyint(1) DEFAULT 0,
  `redirect_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `qr_codes`
--

INSERT INTO `qr_codes` (`id`, `created_at`, `updated_at`, `is_active`, `profile_id`, `is_dynamic`, `redirect_url`) VALUES
('b0a48202', '2025-06-26 20:41:21', '2025-06-26 20:41:21', 1, 25, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `scan_statistics`
--

CREATE TABLE `scan_statistics` (
  `id` int(11) NOT NULL,
  `qr_id` varchar(32) DEFAULT NULL,
  `scan_time` timestamp NULL DEFAULT current_timestamp(),
  `device_info` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `scan_statistics`
--

INSERT INTO `scan_statistics` (`id`, `qr_id`, `scan_time`, `device_info`, `ip_address`, `user_agent`) VALUES
(21, '71252446', '2025-06-23 14:28:31', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-23 14:28:31\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'),
(22, '71252446', '2025-06-23 14:28:36', '{\"ip\":\"74.125.208.141\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 7.0; SM-G930V Build\\/NRD90M) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/59.0.3071.125 Mobile Safari\\/537.36 (compatible; Google-Read-Aloud; +https:\\/\\/support.google.com\\/webmasters\\/answer\\/1061943)\",\"referer\":\"\",\"timestamp\":\"2025-06-23 14:28:36\"}', '74.125.208.141', 'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)'),
(23, '71252446', '2025-06-23 14:28:37', '{\"ip\":\"66.102.8.101\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 7.0; SM-G930V Build\\/NRD90M) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/59.0.3071.125 Mobile Safari\\/537.36 (compatible; Google-Read-Aloud; +https:\\/\\/support.google.com\\/webmasters\\/answer\\/1061943)\",\"referer\":\"\",\"timestamp\":\"2025-06-23 14:28:37\"}', '66.102.8.101', 'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)'),
(24, '71252446', '2025-06-23 14:28:37', '{\"ip\":\"66.249.88.5\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 7.0; SM-G930V Build\\/NRD90M) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/59.0.3071.125 Mobile Safari\\/537.36 (compatible; Google-Read-Aloud; +https:\\/\\/support.google.com\\/webmasters\\/answer\\/1061943)\",\"referer\":\"\",\"timestamp\":\"2025-06-23 14:28:37\"}', '66.249.88.5', 'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)'),
(26, 'aab73e8c', '2025-06-24 11:25:34', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-24 11:25:34\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'),
(27, '489433ea', '2025-06-24 11:35:30', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-24 11:35:30\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'),
(28, '347fc893', '2025-06-24 12:42:10', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-24 12:42:10\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'),
(29, '45342b13', '2025-06-24 19:43:39', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-24 19:43:39\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'),
(30, 'b503bf73', '2025-06-25 12:33:00', '{\"ip\":\"78.183.64.246\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/136.0.0.0 Mobile Safari\\/537.36\",\"referer\":\"\",\"timestamp\":\"2025-06-25 12:33:00\"}', '78.183.64.246', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `theme_name` varchar(50) NOT NULL,
  `theme_title` varchar(100) NOT NULL,
  `background_color` varchar(20) DEFAULT NULL,
  `text_color` varchar(20) DEFAULT NULL,
  `accent_color` varchar(20) DEFAULT NULL,
  `card_background` varchar(20) DEFAULT NULL,
  `font_family` varchar(100) DEFAULT NULL,
  `button_style` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `themes`
--

INSERT INTO `themes` (`id`, `theme_name`, `theme_title`, `background_color`, `text_color`, `accent_color`, `card_background`, `font_family`, `button_style`, `is_active`, `created_at`) VALUES
(1, 'default', 'Varsayılan', '#f8f9fa', '#333333', '#007bff', '#ffffff', 'system-ui, -apple-system, sans-serif', 'rounded', 1, '2025-06-24 11:18:50'),
(2, 'dark', 'Koyu Tema', '#1a1a1a', '#ffffff', '#00ff9d', '#2d2d2d', 'system-ui, -apple-system, sans-serif', 'rounded', 1, '2025-06-24 11:18:50'),
(3, 'elegant', 'Zarif', '#f0f0f0', '#2c3e50', '#e74c3c', '#ffffff', 'Georgia, serif', 'pill', 1, '2025-06-24 11:18:50'),
(4, 'minimal', 'Minimalist', '#ffffff', '#000000', '#666666', '#fafafa', 'Inter, sans-serif', 'flat', 1, '2025-06-24 11:18:50'),
(5, 'nature', 'Doğal', '#e8f5e9', '#2e7d32', '#4caf50', '#ffffff', 'Montserrat, sans-serif', 'soft', 1, '2025-06-24 11:18:50'),
(6, 'ocean', 'Okyanus', '#e3f2fd', '#1565c0', '#29b6f6', '#ffffff', 'Roboto, sans-serif', 'gradient', 1, '2025-06-24 11:18:50'),
(7, 'sunset', 'Gün Batımı', '#fff3e0', '#e65100', '#ff9800', '#ffffff', 'Poppins, sans-serif', 'modern', 1, '2025-06-24 11:18:50');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_profiles_name` (`name`);

--
-- Tablo için indeksler `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_qr_codes_id` (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Tablo için indeksler `scan_statistics`
--
ALTER TABLE `scan_statistics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_scan_statistics_qr_id` (`qr_id`);

--
-- Tablo için indeksler `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `theme_name` (`theme_name`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Tablo için AUTO_INCREMENT değeri `scan_statistics`
--
ALTER TABLE `scan_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Tablo için AUTO_INCREMENT değeri `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD CONSTRAINT `qr_codes_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
