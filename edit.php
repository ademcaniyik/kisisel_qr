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
        // Modern ve responsive profil düzenleme formu (index.php sipariş modalı ile aynı yapı)
        echo '<link rel="stylesheet" href="/kisisel_qr/assets/css/landing.css">';
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">';
        echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
        echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">';
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        echo '<script src="/kisisel_qr/assets/js/profile-manager.js"></script>';
        echo '<div class="container" style="max-width:600px;margin:auto;margin-top:2rem;">';
        echo '<div class="card shadow-lg border-0 rounded-4">';
        echo '<div class="card-body p-4">';
        echo '<h2 class="mb-4 text-center">Profil Bilgilerini Düzenle</h2>';
        echo '<form method="post" autocomplete="off">';
        echo '<input type="hidden" name="edit_code" value="' . htmlspecialchars($editCode) . '">';
        echo '<div class="row g-3">';
        echo '<div class="col-md-6"><label class="form-label">Ad Soyad *</label><input type="text" class="form-control" name="name" value="' . htmlspecialchars($profile['name']) . '" readonly style="background:#f5f5f5;cursor:not-allowed;"></div>';
        echo '<div class="col-md-6"><label class="form-label">Telefon *</label>';
        echo '<input type="text" class="form-control" name="phone" value="' . htmlspecialchars($profile['phone']) . '" required placeholder="555 555 55 55" maxlength="20">';
        echo '<small class="form-text text-muted">Telefon numaranızı ülke kodu ile birlikte giriniz</small>';
        echo '</div>';
        echo '</div>';
        echo '<div class="mb-3 mt-3"><label class="form-label">Kısa Yazı (Bio)</label><textarea class="form-control" name="bio" rows="2" placeholder="Kendinizi tanıtın...">' . htmlspecialchars($profile['bio'] ?? '') . '</textarea></div>';
        echo '<div class="row g-3">';
        echo '<div class="col-md-6"><label class="form-label">IBAN</label>';
        echo '<input type="text" class="form-control" name="iban" value="' . htmlspecialchars($profile['iban'] ?? '') . '" placeholder="TR00 0000 0000 0000 0000 0000 00" maxlength="32">';
        echo '<small class="form-text text-muted">TR ile başlayan 26 haneli İban numarası</small>';
        echo '</div>';
        echo '<div class="col-md-6"><label class="form-label">Kan Grubu</label>';
        echo '<select class="form-select" name="blood_type">';
        $bloodTypes = ["", "A+", "A-", "B+", "B-", "AB+", "AB-", "0+", "0-"];
        foreach ($bloodTypes as $type) {
            $selected = ($profile['blood_type'] ?? '') === $type ? 'selected' : '';
            $label = $type ? ($type === '0+' ? '0 Rh+' : ($type === '0-' ? '0 Rh-' : $type . ' Rh' . ($type[1] === '+' ? '+' : '-'))) : 'Seçiniz';
            echo '<option value="' . $type . '" ' . $selected . '>' . $label . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '<div class="mb-3 mt-3"><label class="form-label">Tema</label>';
        echo '<select class="form-select" name="theme">';
        $themes = [
            'default' => 'Sade Temiz (Varsayılan)',
            'blue' => 'Deniz Mavisi',
            'nature' => 'Günbatımı Sıcak',
            'elegant' => 'Doğa Yeşil',
            'gold' => 'Altın Lüks',
            'purple' => 'Kraliyet Moru',
            'dark' => 'Karanlık Siyah',
            'ocean' => 'Sakura Pembe',
            'minimal' => 'Şık Mor',
            'pastel' => 'Pastel Rüya',
            'retro' => 'Retro Synthwave',
            'neon' => 'Neon Siber',
        ];
        foreach ($themes as $val => $label) {
            $selected = ($profile['theme'] ?? '') === $val ? 'selected' : '';
            echo '<option value="' . $val . '" ' . $selected . '>' . $label . '</option>';
        }
        echo '</select>';
        echo '<small class="form-text text-muted">Profilinizde kullanılacak görsel tema</small>';
        echo '</div>';
        echo '</div>';
        // Sosyal Medya Alanı (index.php ile uyumlu)
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
        echo '</div></div></div>';
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
