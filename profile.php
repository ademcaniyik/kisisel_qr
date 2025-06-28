<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/utilities.php';
require_once __DIR__ . '/includes/ImageOptimizer.php';

// URL parametrelerini al
$qrId = isset($_GET['qr_id']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['qr_id']) : null;
$slug = isset($_GET['slug']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['slug']) : null;

// Debug bilgileri (geçici)
error_log("Profile.php debug - qr_id: " . ($qrId ?? 'null') . ", slug: " . ($slug ?? 'null') . ", REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'null'));

// Profili ve tema bilgilerini bul
$db = Database::getInstance();
$connection = $db->getConnection();

if ($qrId) {
    // QR kod ile profil ve tema bilgilerini al
    $stmt = $connection->prepare("
        SELECT p.*, q.id as qr_id, t.* 
        FROM qr_codes q 
        JOIN profiles p ON q.profile_id = p.id 
        LEFT JOIN themes t ON p.theme = t.theme_name
        WHERE q.id = ?
    ");
    $stmt->bind_param("s", $qrId);
} else if ($slug) {
    // Slug ile profil ve tema bilgilerini al
    $stmt = $connection->prepare("
        SELECT p.*, NULL as qr_id, t.*
        FROM profiles p 
        LEFT JOIN themes t ON p.theme = t.theme_name
        WHERE p.slug = ?
    ");
    $stmt->bind_param("s", $slug);
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Profil bulunamadı.";
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Hata logla - detaylı debug
    error_log('Geçersiz profil erişimi: qr_id=' . ($qrId ?? 'null') . ' slug=' . ($slug ?? 'null') . ' IP=' . $_SERVER['REMOTE_ADDR'] . ' URI=' . $_SERVER['REQUEST_URI']);

    // Debug için SQL query'i de logla
    $debugQuery = $qrId ? "QR query for ID: $qrId" : ($slug ? "Slug query for: $slug" : "No QR ID or slug provided");
    error_log('Profile lookup debug: ' . $debugQuery);

    header("HTTP/1.0 404 Not Found");
    echo "Profil bulunamadı.";
    exit();
}

$profile = $result->fetch_assoc();

// Debug - profile verilerini logla  
error_log("Profile loaded - ID: " . $profile['id'] . ", photo_url: " . ($profile['photo_url'] ?? 'null') . ", photo_data: " . (empty($profile['photo_data']) ? 'empty' : 'has_data'));

// QR kod tarama istatistiğini kaydet (sadece QR ile erişimde)
if ($qrId) {
    Utilities::logScan($qrId);
}

// Sosyal medya ikonları için yardımcı fonksiyon
function getSocialIcon($platform)
{
    $icons = [
        'facebook' => 'fab fa-facebook-f',
        'twitter' => 'fab fa-twitter',
        'x' => 'fab fa-x-twitter', // Yeni X logosu
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
        'steam' => 'fab fa-steam',
        'behance' => 'fab fa-behance',
        'dribbble' => 'fab fa-dribbble',
        'email' => 'fas fa-envelope',
        'website' => 'fas fa-globe',
        'default' => 'fas fa-link'
    ];

    $platform = strtolower(trim($platform));
    return $icons[$platform] ?? $icons['default'];
}

function formatSocialUrl($platform, $url)
{
    // Eğer $url bir dizi ise, ilk elemanı al veya boş string yap
    if (is_array($url)) {
        $url = isset($url[0]) && !empty($url[0]) ? $url[0] : '';
    }

    // Boş URL kontrolü
    if (empty($url) || $url === 'undefined' || is_numeric($url)) {
        return '#';
    }

    // URL zaten http:// veya https:// ile başlıyorsa olduğu gibi döndür
    if (preg_match('/^https?:\/\//i', $url)) {
        return $url;
    }

    // Email ve website için özel işlem
    if ($platform === 'email') {
        // Email için mailto: protokolü ekle
        return 'mailto:' . $url;
    }

    if ($platform === 'website') {
        // Website için http/https yoksa ekle
        if (!preg_match('/^https?:\/\//i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }

    // Platform'a göre base URL'leri belirle
    $baseUrls = [
        'facebook' => 'https://facebook.com/',
        'twitter' => 'https://twitter.com/',
        'x' => 'https://x.com/',
        'instagram' => 'https://instagram.com/',
        'linkedin' => 'https://linkedin.com/in/',
        'github' => 'https://github.com/',
        'youtube' => 'https://youtube.com/@',
        'tiktok' => 'https://tiktok.com/@',
        'pinterest' => 'https://pinterest.com/',
        'spotify' => 'https://open.spotify.com/user/',
        'medium' => 'https://medium.com/@',
        'twitch' => 'https://twitch.tv/',
        'discord' => 'https://discord.com/users/',
        'reddit' => 'https://reddit.com/user/',
        'whatsapp' => 'https://wa.me/',
        'telegram' => 'https://t.me/',
        'snapchat' => 'https://snapchat.com/add/',
        'steam' => 'https://steamcommunity.com/id/',
        'behance' => 'https://behance.net/',
        'dribbble' => 'https://dribbble.com/'
    ];

    // URL'de www. varsa kaldır
    $url = preg_replace('/^www\./i', '', $url);

    // Platform için base URL varsa, ekle
    $platform = strtolower(trim($platform));
    if (isset($baseUrls[$platform])) {
        // Eğer platformun kendi domain'i URL'de varsa, onu kaldır
        $domainPattern = preg_quote(parse_url($baseUrls[$platform], PHP_URL_HOST), '/');
        $url = preg_replace('/^' . $domainPattern . '\/?/i', '', $url);
        $url = ltrim($url, '/@');
        // Base URL ile birleştir
        return $baseUrls[$platform] . $url;
    }

    // Bilinmeyen platformlar için https:// ekle
    return 'https://' . $url;
}

function adjustColor($color, $amount)
{
    $color = ltrim($color, '#');
    $r = hexdec(substr($color, 0, 2));
    $g = hexdec(substr($color, 2, 2));
    $b = hexdec(substr($color, 4, 2));

    $r = max(0, min(255, $r + $amount));
    $g = max(0, min(255, $g + $amount));
    $b = max(0, min(255, $b + $amount));

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

function getThemeClass($themeName)
{
    $themeMapping = [
        // Varsayılan
        'default' => 'default',
        'klasik' => 'default',

        // Karanlık/Neon
        'dark' => 'dark',
        'karanlık' => 'dark',
        'neon' => 'neon',
        'cyberpunk' => 'neon',
        'siber' => 'neon',

        // Yeşil/Doğa
        'zarif' => 'green',
        'doğa' => 'green',
        'nature' => 'green',
        'green' => 'green',
        'orman' => 'green',
        'forest' => 'green',

        // Mor/Elegant
        'minimalist' => 'purple',
        'minimal' => 'purple',
        'mor' => 'purple',
        'kraliyet' => 'purple',
        'purple' => 'purple',
        'elegant' => 'purple',
        'şık' => 'purple',

        // Mavi/Okyanus
        'okyanus' => 'blue',
        'mavi' => 'blue',
        'ocean' => 'blue',
        'blue' => 'blue',
        'corporate' => 'blue',
        'kurumsal' => 'blue',

        // Turuncu/Günbatımı
        'sunset' => 'orange',
        'günbatımı' => 'orange',
        'turuncu' => 'orange',
        'orange' => 'orange',

        // Pembe
        'sakura' => 'pink',
        'pembe' => 'pink',
        'pink' => 'pink',

        // Altın
        'altın' => 'gold',
        'gold' => 'gold',
        'lüks' => 'gold',

        // Pastel
        'pastel' => 'pastel',
        'rüya' => 'pastel',

        // Retro
        'retro' => 'retro',
        'synthwave' => 'retro',
        'nostaljik' => 'retro',

        // Minimal Beyaz
        'sade' => 'minimal',
        'beyaz' => 'minimal',

        // Sade Temiz
        'clean' => 'clean',
        'temiz' => 'clean',
        'sade temiz' => 'clean'
    ];

    $lowerTheme = strtolower(trim($themeName));
    return $themeMapping[$lowerTheme] ?? 'clean';
}

// Tema bilgilerini al
$themeQuery = "SELECT * FROM themes WHERE theme_name = ?";
$themeStmt = $connection->prepare($themeQuery);
$themeStmt->bind_param("s", $profile['theme']);
$themeStmt->execute();
$theme = $themeStmt->get_result()->fetch_assoc();

if (!$theme) {
    // Varsayılan tema
    $theme = [
        'background_color' => '#f8f9fa',
        'text_color' => '#333333',
        'accent_color' => '#007bff',
        'card_background' => '#ffffff',
        'font_family' => 'system-ui, -apple-system, sans-serif',
        'button_style' => 'rounded'
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['name']); ?> - Kişisel QR Profil</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($profile['name']); ?> adlı kişinin dijital kartviziti. <?php echo htmlspecialchars(substr(strip_tags($profile['bio']), 0, 150)); ?>...">
    <meta name="keywords" content="<?php echo htmlspecialchars($profile['name']); ?>, dijital kartvizit, QR kod, profil, iletişim bilgileri, sosyal medya">
    <meta name="author" content="<?php echo htmlspecialchars($profile['name']); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://yourdomain.com/profile.php?<?php echo $qrId ? 'qr_id=' . $qrId : 'slug=' . $slug; ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($profile['name']); ?> - Kişisel QR Profil">
    <meta property="og:description" content="<?php echo htmlspecialchars($profile['name']); ?> adlı kişinin dijital kartviziti ve iletişim bilgileri.">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="https://yourdomain.com/profile.php?<?php echo $qrId ? 'qr_id=' . $qrId : 'slug=' . $slug; ?>">
    <?php if (!empty($profile['photo_url'])): ?>
        <meta property="og:image" content="https://acdisoftware.com.tr/kisisel_qr<?php echo htmlspecialchars($profile['photo_url']); ?>">
    <?php else: ?>
        <meta property="og:image" content="https://acdisoftware.com.tr/kisisel_qr/assets/images/default-profile.jpg">
    <?php endif; ?>
    <meta property="og:image:width" content="400">
    <meta property="og:image:height" content="400">
    <meta property="og:site_name" content="Kişisel QR">
    <meta property="og:locale" content="tr_TR">
    <meta property="profile:first_name" content="<?php echo htmlspecialchars(explode(' ', $profile['name'])[0]); ?>">
    <?php if (str_word_count($profile['name']) > 1): ?>
        <meta property="profile:last_name" content="<?php echo htmlspecialchars(substr($profile['name'], strpos($profile['name'], ' ') + 1)); ?>">
    <?php endif; ?>

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($profile['name']); ?> - Kişisel QR Profil">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($profile['name']); ?> adlı kişinin dijital kartviziti ve iletişim bilgileri.">
    <?php if (!empty($profile['photo'])): ?>
        <meta name="twitter:image" content="https://yourdomain.com/kisisel_qr/<?php echo htmlspecialchars($profile['photo_url'] ?? ''); ?>">
    <?php else: ?>
        <meta name="twitter:image" content="https://yourdomain.com/kisisel_qr/assets/images/default-profile.jpg">
    <?php endif; ?>

    <!-- Additional Meta Tags -->
    <meta name="application-name" content="Kişisel QR">
    <meta name="msapplication-TileColor" content="#3498db">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "name": "<?php echo addslashes(htmlspecialchars($profile['name'])); ?>",
            "description": "<?php echo addslashes(htmlspecialchars(substr(strip_tags($profile['bio']), 0, 200))); ?>",
            "url": "https://yourdomain.com/profile.php?<?php echo $qrId ? 'qr_id=' . $qrId : 'slug=' . $slug; ?>",
            <?php if (!empty($profile['photo'])): ?> "image": "https://yourdomain.com/kisisel_qr/<?php echo addslashes(htmlspecialchars($profile['photo_url'] ?? '')); ?>",
            <?php endif; ?>
            <?php if (!empty($profile['phone'])): ?> "telephone": "<?php echo addslashes(htmlspecialchars($profile['phone'])); ?>",
            <?php endif; ?>
            <?php if (!empty($profile['email'])): ?> "email": "<?php echo addslashes(htmlspecialchars($profile['email'])); ?>",
            <?php endif; ?> "sameAs": [
                <?php
                $socialLinksArray = [];
                if (!empty($profile['social_links'])) {
                    $socialLinks = json_decode($profile['social_links'], true);
                    if (is_array($socialLinks)) {
                        foreach ($socialLinks as $platform => $url) {
                            // Eğer URL array ise, ilk elemanı al
                            if (is_array($url)) {
                                $url = isset($url[0]) && !empty($url[0]) ? $url[0] : '';
                            }

                            if (!empty($url) && is_string($url)) {
                                $formattedUrl = formatSocialUrl($platform, $url);
                                if ($formattedUrl !== '#') {
                                    $socialLinksArray[] = '"' . addslashes(htmlspecialchars($formattedUrl)) . '"';
                                }
                            }
                        }
                    }
                }
                echo implode(',', $socialLinksArray);
                ?>
            ],
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "personal"
                <?php if (!empty($profile['phone'])): ?>,
                    "telephone": "<?php echo addslashes(htmlspecialchars($profile['phone'])); ?>"
                <?php endif; ?>
                <?php if (!empty($profile['email'])): ?>,
                    "email": "<?php echo addslashes(htmlspecialchars($profile['email'])); ?>"
                <?php endif; ?>
            }
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="/kisisel_qr/assets/css/profile-themes.css" rel="stylesheet">
    <link href="/kisisel_qr/assets/css/image-enhancements.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --background-color: <?php echo htmlspecialchars($profile['background_color'] ?? '#f8f9fa'); ?>;
            --text-color: <?php echo htmlspecialchars($profile['text_color'] ?? '#333333'); ?>;
            --accent-color: <?php echo htmlspecialchars($profile['accent_color'] ?? '#007bff'); ?>;
            --card-background: <?php echo htmlspecialchars($profile['card_background'] ?? '#ffffff'); ?>;
            --font-family: <?php echo htmlspecialchars($profile['font_family'] ?? "'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"); ?>;
        }

        /* Additional Info Styles - Modern Compact Design */
        .additional-info {
            margin-top: 2rem;
            padding: 0;
        }

        .info-item {
            background: var(--card-background);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
            color: white;
            background: linear-gradient(135deg, #3498db, #2980b9);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .blood-icon {
            background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3) !important;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }

        .blood-info .info-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0;
        }

        .blood-value {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .info-action {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .info-item:hover .info-action {
            background: #3498db;
            color: white;
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .additional-info {
                margin-top: 1.5rem;
                padding: 0 0.5rem;
            }

            .info-item {
                padding: 1rem;
                margin-bottom: 0.75rem;
            }

            .info-icon {
                width: 40px;
                height: 40px;
                margin-right: 0.75rem;
                font-size: 1rem;
            }

            .info-value {
                font-size: 0.9rem;
                letter-spacing: 0.3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .blood-info .info-label {
                font-size: 1rem;
            }

            .blood-value {
                font-size: 1.2rem;
            }

            .info-action {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
                flex-shrink: 0;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .info-item {
                padding: 0.75rem;
            }

            .info-icon {
                width: 36px;
                height: 36px;
                margin-right: 0.5rem;
                font-size: 0.9rem;
            }

            .info-value {
                font-size: 0.85rem;
                letter-spacing: 0.2px;
            }

            .info-action {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body class="theme-<?php echo htmlspecialchars(getThemeClass($profile['theme'] ?? 'default')); ?>" data-button-style="<?php echo htmlspecialchars($profile['button_style'] ?? 'default'); ?>">
    <div class="profile-container">
        <div class="profile-header">
            <?php if (isset($profile['photo_url']) && $profile['photo_url']): ?>
                <?php
                // Modern responsive image display with WebP support
                if (!empty($profile['photo_data'])) {
                    // Use optimized images if available
                    $photoData = json_decode($profile['photo_data'], true);
                    if ($photoData && isset($photoData['filename'])) {
                        $imageOptimizer = new ImageOptimizer();
                        echo $imageOptimizer->generateResponsiveImageHtml(
                            $photoData['filename'],
                            htmlspecialchars($profile['name']) . ' profil fotoğrafı',
                            'profile-photo',
                            "(max-width: 480px) 150px, (max-width: 768px) 300px, 600px"
                        );
                    } else {
                        // Fallback for legacy photo_data format
                        echo '<img src="' . htmlspecialchars($profile['photo_url']) . '" alt="' . htmlspecialchars($profile['name']) . ' profil fotoğrafı" class="profile-photo" loading="lazy">';
                    }
                } elseif (!empty($profile['photo_url'])) {
                    // Fallback to original image with improved alt text
                    echo '<img src="' . htmlspecialchars($profile['photo_url']) . '" alt="' . htmlspecialchars($profile['name']) . ' profil fotoğrafı" class="profile-photo" loading="lazy">';
                }
                ?>
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($profile['name']); ?></h1>
            <?php if ($profile['bio']): ?>
                <div class="bio">
                    <?php echo nl2br(htmlspecialchars($profile['bio'])); ?>
                </div>
            <?php endif; ?>

            <?php if ($profile['phone']): ?>
                <div class="contact-info">
                    <div class="contact-buttons-row">
                        <a href="tel:<?php echo htmlspecialchars($profile['phone']); ?>" class="contact-btn phone-btn">
                            <div class="btn-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="btn-content">
                                <span class="btn-title">Telefon ile Ara</span>
                                <span class="btn-subtitle"><?php echo htmlspecialchars($profile['phone']); ?></span>
                            </div>
                            <div class="btn-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($profile['social_links']): ?>
                <div class="social-links">
                    <?php
                    $links = json_decode($profile['social_links'], true);
                    if (is_array($links) && !empty($links)) {
                        foreach ($links as $platform => $url):
                            // Yeni JSON format: {"platform": "url"}
                            if (is_string($platform) && is_string($url)) {
                                // Bu doğru format - kullan
                            }
                            // Eski array format: [{"platform":"facebook","url":"..."}]
                            else if (isset($url['platform']) && isset($url['url'])) {
                                $platform = $url['platform'];
                                $url = $url['url'];
                            }
                            // Geçersiz format
                            else {
                                continue;
                            }

                            // Boş URL'leri atla
                            if (empty($url) || $url === 'undefined' || is_numeric($url)) {
                                continue;
                            }

                            $formattedUrl = formatSocialUrl($platform, $url);
                            $platformLower = strtolower($platform);

                            // Platform isimlerini Türkçe'ye çevir
                            $platformNames = [
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter',
                                'instagram' => 'Instagram',
                                'linkedin' => 'LinkedIn',
                                'github' => 'GitHub',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'pinterest' => 'Pinterest',
                                'spotify' => 'Spotify',
                                'medium' => 'Medium',
                                'twitch' => 'Twitch',
                                'discord' => 'Discord',
                                'reddit' => 'Reddit',
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'email' => 'E-posta',
                                'website' => 'Website'
                            ];

                            $displayName = $platformNames[$platformLower] ?? ucfirst($platform);
                    ?>
                            <a href="<?php echo htmlspecialchars($formattedUrl); ?>"
                                target="_blank"
                                class="social-link <?php echo $platformLower; ?>"
                                rel="noopener noreferrer">
                                <i class="<?php echo getSocialIcon($platform); ?>"></i>
                                <span><?php echo htmlspecialchars($displayName); ?></span>
                            </a>
                    <?php
                        endforeach;
                    } else {
                        echo "<!-- Sosyal medya linkleri bulunamadı veya geçersiz JSON -->";
                    }
                    ?>
                </div>
            <?php else: ?>
                <!-- Sosyal medya linkleri yok -->
            <?php endif; ?>

            <!-- IBAN ve Kan Grubu Bilgileri - Modern Compact Design -->
            <?php if ($profile['iban'] || $profile['blood_type']): ?>
                <div class="additional-info">

                    <?php if ($profile['iban']): ?>
                        <!-- İban Bilgisi -->
                        <div class="info-item iban-info"
                            onclick="copyToClipboard('<?php echo htmlspecialchars($profile['iban']); ?>', '✅ İban kopyalandı!')"
                            style="display: flex; align-items: center; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.25); margin-bottom: 12px; cursor: pointer; transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.35)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.25)'">

                            <div class="info-icon" style="margin-right: 12px; font-size: 20px; color: #fff; background: rgba(255,255,255,0.2); padding: 8px; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-credit-card"></i>
                            </div>

                            <div class="info-content" style="flex: 1; display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #fff;">
                                <div style="display: flex; flex-direction: column; text-align: left;">
                                    <div class="info-label" style="font-size: 12px; opacity: 0.9; margin-bottom: 2px; text-align: left;">IBAN</div>
                                    <div class="info-value" style="font-weight: 600; font-family: monospace; text-align: left; direction: ltr;">
                                        <?php echo htmlspecialchars($profile['iban']); ?>
                                    </div>
                                </div>
                                <div class="info-action" style="background: rgba(255,255,255,0.2); padding: 6px; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-copy" style="font-size: 12px;"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($profile['blood_type']): ?>
                        <!-- Kan Grubu Bilgisi -->
                        <div class="info-item blood-group-info"
                            style="display: flex; align-items: center; padding: 12px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.25); cursor: pointer; transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 107, 107, 0.35)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255, 107, 107, 0.25)'">

                            <div class="info-icon" style="margin-right: 12px; font-size: 20px; color: #fff; background: rgba(255,255,255,0.2); padding: 8px; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-heartbeat"></i>
                            </div>

                            <div class="info-content" style="flex: 1; display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #fff;">
                                <div style="display: flex; flex-direction: column;">
                                    <div class="info-label" style="font-size: 12px; opacity: 0.9; margin-bottom: 2px;">Kan Grubu</div>
                                    <div class="info-value" style="font-weight: 600; font-size: 16px;">
                                        <?php echo htmlspecialchars($profile['blood_type']); ?>
                                    </div>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 6px; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus" style="font-size: 12px;"></i>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/kisisel_qr/assets/js/image-cleanup.js"></script>
    <script>
        // Copy to clipboard function with feedback
        function copyToClipboard(text, message = 'Kopyalandı!', feedbackId = null) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopyFeedback(message, feedbackId);
                }).catch(function() {
                    fallbackCopyText(text, message, feedbackId);
                });
            } else {
                fallbackCopyText(text, message, feedbackId);
            }
        }

        function fallbackCopyText(text, message, feedbackId) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showCopyFeedback(message, feedbackId);
            } catch (err) {
                showCopyFeedback('Kopyalama başarısız!', feedbackId);
            }
            textArea.remove();
        }

        function showCopyFeedback(message, feedbackId) {
            if (feedbackId) {
                // Use specific feedback element
                const feedback = document.getElementById(feedbackId);
                if (feedback) {
                    feedback.querySelector('span').textContent = message;
                    feedback.classList.add('show');
                    setTimeout(() => {
                        feedback.classList.remove('show');
                    }, 2000);
                    return;
                }
            }

            // Fallback to general toast
            showToast(message);
        }

        function showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--accent-color);
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-weight: 500;
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            `;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Profile page specific image enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // WebP support detection
            function supportsWebP() {
                return new Promise((resolve) => {
                    const webP = new Image();
                    webP.onload = webP.onerror = () => resolve(webP.height === 2);
                    webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
                });
            }

            // Add no-webp class if WebP is not supported
            supportsWebP().then((supported) => {
                if (!supported) {
                    document.documentElement.classList.add('no-webp');
                }
            });

            // Enhanced error handling for profile photo
            const profilePhoto = document.querySelector('.profile-photo');
            if (profilePhoto) {
                profilePhoto.addEventListener('error', function() {
                    this.classList.add('error');
                    this.alt = 'Profil fotoğrafı yüklenemedi';
                    if (!this.src.includes('default-profile.svg')) {
                        this.src = '/kisisel_qr/assets/images/default-profile.svg';
                    }
                });

                profilePhoto.addEventListener('load', function() {
                    this.classList.add('loaded');
                    this.classList.remove('error');
                });
            }

            // Intersection Observer for additional lazy loading
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        });
    </script>

    <!-- Kişisel QR Reklam Footer -->
    <footer class="qr-footer-ad" onclick="window.open('https://acdisoftware.com.tr/kisisel_qr', '_blank')" style="
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        cursor: pointer;
        z-index: 1000;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 2px solid rgba(255,255,255,0.2);
    ">
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zm8-2v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4z" />
                <path d="M15 13h2v2h-2zm2 2h2v2h-2zm-2 2h2v2h-2zm4 0h2v2h-2z" />
            </svg>
            <span>Siz de kendi Kişisel QR kodunuzu oluşturun!</span>
            <span style="font-size: 12px; opacity: 0.8;">→</span>
        </div>
    </footer>

    <style>
        .qr-footer-ad:hover {
            transform: translateY(-2px);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
        }

        /* Mobil responsive */
        @media (max-width: 768px) {
            .qr-footer-ad {
                font-size: 13px;
                padding: 10px 15px;
            }

            .qr-footer-ad span:last-child {
                display: none;
            }
        }

        /* Body'ye alt padding ekle ki footer content'i kapatmasın */
        body {
            padding-bottom: 60px;
        }
    </style>
</body>

</html>