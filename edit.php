<?php
// Profil Edit QR ile yönlendirme ve şifre kontrolü
define('ROOT', __DIR__);
require_once ROOT . '/includes/QRPoolManager.php';
require_once ROOT . '/includes/ProfileManager.php';

$qrPoolManager = new QRPoolManager();
$profileManager = new ProfileManager();

// URL: /edit/{edit_token}
$editToken = null;
if (isset($_GET['token'])) {
    $editToken = $_GET['token'];
} else {
    // Pretty URL desteği: /edit/xxxxxxx
    $requestUri = $_SERVER['REQUEST_URI'];
    if (preg_match('#/edit/([a-zA-Z0-9]+)#', $requestUri, $m)) {
        $editToken = $m[1];
    }
}

if (!$editToken) {
    http_response_code(404);
    echo '<h2>Geçersiz edit QR!</h2>';
    exit;
}

// QR havuzunda bu edit token var mı?
$connection = Database::getInstance()->getConnection();
$stmt = $connection->prepare("SELECT * FROM qr_pool WHERE edit_token = ?");
$stmt->bind_param("s", $editToken);
$stmt->execute();
$result = $stmt->get_result();
$qr = $result->fetch_assoc();

if (!$qr) {
    http_response_code(404);
    echo '<h2>Geçersiz veya silinmiş edit QR!</h2>';
    exit;
}

// Profil atanmış mı?
if (!$qr['profile_id']) {
    echo '<h2>Bu QR henüz bir profile atanmamış.</h2>';
    exit;
}

// Şifre kontrolü
$showForm = true;
$editCode = $qr['edit_code'];
$profileId = $qr['profile_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['edit_code'] ?? '';
    if ($inputCode === $editCode) {
        // Şifre doğru, profil düzenleme formunu göster
        $showForm = false;
        $profile = $profileManager->getProfile($profileId);
        if (!$profile) {
            echo '<h2>Profil bulunamadı.</h2>';
            exit;
        }
        // Profil düzenleme işlemi
        if (isset($_POST['save_profile'])) {
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $iban = $_POST['iban'] ?? '';
            $blood_type = $_POST['blood_type'] ?? '';
            $theme = $_POST['theme'] ?? '';
            $socialLinks = isset($_POST['social_links']) ? $_POST['social_links'] : [];
            $profileManager->updateProfile($profileId, $profile['name'], $email, $phone, $bio, $iban, $blood_type, $theme, $socialLinks);
            echo '<p class="alert alert-success">Profil başarıyla güncellendi.</p>';
            $profile = $profileManager->getProfile($profileId);
        }
        // Modern ve responsive profil düzenleme formu
        echo '<link rel="stylesheet" href="/kisisel_qr/assets/css/profile-page.css">';
        echo '<link rel="stylesheet" href="/kisisel_qr/assets/css/profile-themes.css">';
        echo '<link rel="stylesheet" href="/kisisel_qr/assets/css/social-buttons.css">';
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo '<script src="/kisisel_qr/assets/js/profile-manager.js"></script>';
        echo '<div class="profile-edit-container" style="max-width:480px;margin:auto;background:#fff;border-radius:16px;padding:2rem;box-shadow:0 4px 24px rgba(0,0,0,0.08);margin-top:2rem;">';
        echo '<h2 style="text-align:center;margin-bottom:1.5rem;">Profil Bilgilerini Düzenle</h2>';
        echo '<form method="post" autocomplete="off">';
        echo '<input type="hidden" name="edit_code" value="' . htmlspecialchars($editCode) . '">';
        echo '<div class="mb-3"><label class="form-label">Ad Soyad</label><input type="text" class="form-control" name="name" value="' . htmlspecialchars($profile['name']) . '" readonly style="background:#f5f5f5;cursor:not-allowed;"></div>';
        echo '<div class="mb-3"><label class="form-label">E-posta</label><input type="email" class="form-control" name="email" value="' . htmlspecialchars($profile['email'] ?? '') . '"></div>';
        echo '<div class="mb-3"><label class="form-label">Telefon</label><input type="text" class="form-control" name="phone" value="' . htmlspecialchars($profile['phone']) . '"></div>';
        echo '<div class="mb-3"><label class="form-label">Bio</label><input type="text" class="form-control" name="bio" value="' . htmlspecialchars($profile['bio'] ?? '') . '"></div>';
        echo '<div class="mb-3"><label class="form-label">IBAN</label><input type="text" class="form-control" name="iban" value="' . htmlspecialchars($profile['iban'] ?? '') . '"></div>';
        echo '<div class="mb-3"><label class="form-label">Kan Grubu</label><input type="text" class="form-control" name="blood_type" value="' . htmlspecialchars($profile['blood_type'] ?? '') . '"></div>';
        echo '<div class="mb-3"><label class="form-label">Tema</label><input type="text" class="form-control" name="theme" value="' . htmlspecialchars($profile['theme'] ?? '') . '"></div>';
        // Sosyal Medya Alanı
        $socialLinksRaw = $profile['social_links'] ?? '[]';
        $socialLinks = json_decode($socialLinksRaw, true);
        echo '<div class="mb-3"><label class="form-label">Sosyal Medya Hesapları</label>';
        echo '<div id="socialLinksContainer">';
        if (!empty($socialLinks) && is_array($socialLinks)) {
            foreach ($socialLinks as $item) {
                $platform = htmlspecialchars($item['platform'] ?? '');
                $url = htmlspecialchars($item['url'] ?? '');
                echo "<script>$(function(){addSocialLink('socialLinksContainer','{$platform}','{$url}');});</script>";
            }
        }
        echo '</div>';
        echo '<button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="addSocialLink(\'socialLinksContainer\')"><i class="fas fa-plus"></i> Sosyal Medya Ekle</button>';
        echo '</div>';
        echo '<button type="submit" name="save_profile" class="btn btn-primary w-100" style="font-size:1.1rem;">Kaydet</button>';
        echo '</form>';
        echo '</div>';
        echo '<script>if(typeof addSocialLink==="function" && $("#socialLinksContainer").children().length===0){addSocialLink("socialLinksContainer");}</script>';
        exit;
    } else {
        echo '<p style="color:red">Hatalı şifre!</p>';
    }
}

if ($showForm) {
    echo '<h2>Profil Düzenleme Şifresi</h2>';
    echo '<form method="post">';
    echo '<label>Edit Şifresi: <input type="text" name="edit_code"></label>';
    echo '<button type="submit">Devam</button>';
    echo '</form>';
}
