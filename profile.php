<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/utilities.php';
require_once __DIR__ . '/includes/ImageOptimizer.php';

// URL parametrelerini al
$qrId = isset($_GET['qr_id']) ? preg_replace('/[^a-zA-Z0-9]/', '', $_GET['qr_id']) : null;
$slug = isset($_GET['slug']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['slug']) : null;



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
    header("HTTP/1.0 404 Not Found");
    echo "Profil bulunamadı.";
    exit();
}

$profile = $result->fetch_assoc();

// QR kod tarama istatistiğini kaydet (sadece QR ile erişimde)
if ($qrId) {
    Utilities::logScan($qrId);
}

// Sosyal medya ikonları için yardımcı fonksiyon
function getSocialIcon($platform)
{
    $icons = [
        'facebook' => 'fab fa-facebook-f',
        'x' => 'fab fa-twitter',
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
    // Debug için HTML comment ekleyelim
    $debugMsg = "DEBUG formatSocialUrl: Platform: $platform, URL: $url";
    
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
        // WhatsApp için özel işlem
        if ($platform === 'whatsapp') {
            // + işaretini kaldır çünkü wa.me/905349334631 formatında olmalı
            $cleanUrl = ltrim($url, '+');
            $finalUrl = $baseUrls[$platform] . $cleanUrl;
            return $finalUrl;
        }
        
        // Eğer platformun kendi domain'i URL'de varsa, onu kaldır
        $domainPattern = preg_quote(parse_url($baseUrls[$platform], PHP_URL_HOST), '/');
        $url = preg_replace('/^' . $domainPattern . '\/?/i', '', $url);
        $url = ltrim($url, '/@');
        // Base URL ile birleştir
        $finalUrl = $baseUrls[$platform] . $url;
        return $finalUrl;
    }

    // Bilinmeyen platformlar için https:// ekle
    $finalUrl = 'https://' . $url;
    return $finalUrl;
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
    <?php if (!empty($profile['photo_url']) && !$profile['photo_hidden']): ?>
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
    <?php if (!empty($profile['photo_url']) && !$profile['photo_hidden']): ?>
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
            <?php if (!empty($profile['photo_url']) && !$profile['photo_hidden']): ?> "image": "https://yourdomain.com/kisisel_qr/<?php echo addslashes(htmlspecialchars($profile['photo_url'] ?? '')); ?>",
            <?php endif; ?>
            <?php if (!empty($profile['phone']) && !$profile['phone_hidden']): ?> "telephone": "<?php echo addslashes(htmlspecialchars($profile['phone'])); ?>",
            <?php endif; ?>
            <?php if (!empty($profile['email'])): ?> "email": "<?php echo addslashes(htmlspecialchars($profile['email'])); ?>",
            <?php endif; ?>
            "sameAs": [
                <?php
                $socialLinksArray = [];
                if (!empty($profile['social_links'])) {
                    $socialLinks = json_decode($profile['social_links'], true);
                    
                    // Eğer string döndüyse, tekrar decode et (double-encoded durumu)
                    if (is_string($socialLinks)) {
                        $socialLinks = json_decode($socialLinks, true);
                    }
                    
                    if (is_array($socialLinks)) {
                        foreach ($socialLinks as $key => $value) {
                            $platform = '';
                            $url = '';
                            
                            // Yeni array format: [{"platform":"facebook","url":"..."}] - EN YAYGINI
                            if (is_array($value) && isset($value['platform']) && isset($value['url'])) {
                                $platform = $value['platform'];
                                $url = $value['url'];
                            }
                            // Eski JSON format: {"platform": "url"}
                            else if (is_string($key) && is_string($value)) {
                                $platform = $key;
                                $url = $value;
                            }
                            // Geçersiz format
                            else {
                                continue;
                            }

                            // Boş URL kontrolü
                            if (!empty($url) && is_string($url) && $url !== 'undefined') {
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
                <?php if (!empty($profile['phone']) && !$profile['phone_hidden']): ?>,
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
    <link href="/kisisel_qr/assets/css/profile-page.min.css" rel="stylesheet">
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
    </style>
</head>

<body class="theme-<?php echo htmlspecialchars(getThemeClass($profile['theme'] ?? 'default')); ?>" data-button-style="<?php echo htmlspecialchars($profile['button_style'] ?? 'default'); ?>">
    <div class="profile-container">
        <div class="profile-header">
            <?php if (isset($profile['photo_url']) && $profile['photo_url'] && !$profile['photo_hidden']): ?>
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

            <?php if ($profile['phone'] && !$profile['phone_hidden']): ?>
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
                    
                    // Eğer string döndüyse, tekrar decode et (double-encoded durumu)
                    if (is_string($links)) {
                        $links = json_decode($links, true);
                    }
                    
                    if (is_array($links) && !empty($links)) {
                        $linkCount = 0;
                        foreach ($links as $key => $value) {
                            $platform = '';
                            $url = '';
                            
                            // Yeni array format: [{"platform":"facebook","url":"..."}] - EN YAYGINI
                            if (is_array($value) && isset($value['platform']) && isset($value['url'])) {
                                $platform = $value['platform'];
                                $url = $value['url'];
                            }
                            // Eski JSON format: {"platform": "url"}
                            else if (is_string($key) && is_string($value)) {
                                $platform = $key;
                                $url = $value;
                            }
                            // Geçersiz format
                            else {
                                continue;
                            }

                            // Boş URL'leri atla - ancak telefon numaralarını kabul et
                            if (empty($url) || $url === 'undefined') {
                                continue;
                            }
                            
                            // Sadece tamamen sayısal olanları atla (telefon numaraları değil)
                            if (is_numeric($url) && !str_contains($url, '+')) {
                                continue;
                            }

                            $formattedUrl = formatSocialUrl($platform, $url);
                            
                            // formatSocialUrl'den # dönerse atla
                            if ($formattedUrl === '#') {
                                continue;
                            }
                            
                            $platformLower = strtolower($platform);

                            // Platform isimlerini Türkçe'ye çevir
                            $platformNames = [
                                'facebook' => 'Facebook',
                                'x' => 'X',
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
                            $linkCount++;
                    ?>
                            <a href="<?php echo htmlspecialchars($formattedUrl); ?>"
                                target="_blank"
                                class="social-link <?php echo $platformLower; ?>"
                                rel="noopener noreferrer">
                                <i class="<?php echo getSocialIcon($platform); ?>"></i>
                                <span><?php echo htmlspecialchars($displayName); ?></span>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- IBAN ve Kan Grubu - Mükemmel Hizalanmış Tasarım -->
            <?php if ($profile['iban'] || $profile['blood_type']): ?>
                <div class="minimal-info" style="margin-top: 20px; border-top: 1px solid #f0f0f0; padding-top: 20px;">

                    <?php if ($profile['iban']): ?>
                        <!-- IBAN - Mükemmel Hizalama -->
                        <div style="display: grid; grid-template-columns: 32px 1fr; gap: 12px; align-items: center; margin-bottom: 16px; padding: 12px 0; border-bottom: 1px solid #f8f8f8;">
                            <!-- İkon ve Copy Butonu Alanı -->
                            <div style="position: relative; width: 32px; height: 32px; background: #f8f8f8; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-university" style="font-size: 12px; color: #666;"></i>
                                <button onclick="copyIban()" style="position: absolute; top: -4px; right: -4px; background: #fff; border: 1px solid #ddd; color: #666; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; font-size: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" 
                                        onmouseover="this.style.color='#000'; this.style.borderColor='#999'; this.style.background='#f9f9f9'" 
                                        onmouseout="this.style.color='#666'; this.style.borderColor='#ddd'; this.style.background='#fff'"
                                        title="IBAN Kopyala">
                                    <i class="fas fa-copy" style="font-size: 7px;"></i>
                                </button>
                            </div>
                            <!-- Metin Alanı -->
                            <div style="display: flex; flex-direction: column; gap: 2px; min-width: 0;">
                                <span style="color: #888; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">IBAN</span>
                                <span id="iban-number" style="color: #000; font-family: 'SF Mono', monospace; font-size: 14px; font-weight: 500; word-break: break-all;">
                                    <?php echo htmlspecialchars($profile['iban']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile['blood_type']): ?>
                        <!-- Kan Grubu - Mükemmel Hizalama -->
                        <div style="display: grid; grid-template-columns: 32px 1fr; gap: 12px; align-items: center; margin-bottom: 16px; padding: 12px 0;">
                            <!-- İkon Alanı -->
                            <div style="width: 32px; height: 32px; background: #f8f8f8; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tint" style="font-size: 12px; color: #666;"></i>
                            </div>
                            <!-- Metin Alanı -->
                            <div style="display: flex; flex-direction: column; gap: 2px; min-width: 0;">
                                <span style="color: #888; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Kan Grubu</span>
                                <span style="color: #000; font-size: 14px; font-weight: 600;">
                                    <?php echo htmlspecialchars($profile['blood_type']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/kisisel_qr/assets/js/image-cleanup.js"></script>
    <script src="/kisisel_qr/assets/js/profile-page.min.js"></script>

    <!-- Kişisel QR Reklam Footer -->
    <footer class="qr-footer-ad">
        <div class="qr-footer-ad-content">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zm8-2v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4z" />
                <path d="M15 13h2v2h-2zm2 2h2v2h-2zm-2 2h2v2h-2zm4 0h2v2h-2z" />
            </svg>
            <span>Siz de kendi Kişisel QR kodunuzu oluşturun!</span>
            <span class="qr-footer-ad-arrow">→</span>
        </div>
    </footer>
</body>

</html>