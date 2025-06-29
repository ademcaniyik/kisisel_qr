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
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $iban = $_POST['iban'] ?? '';
            $blood_type = $_POST['blood_type'] ?? '';
            $theme = $_POST['theme'] ?? '';
            $profileManager->updateProfile($profileId, $name, $email, $phone, $bio, $iban, $blood_type, $theme);
            echo '<p>Profil başarıyla güncellendi.</p>';
            // Güncellenmiş veriyi tekrar çek
            $profile = $profileManager->getProfile($profileId);
        }
        // Profil düzenleme formu
        echo '<h2>Profil Bilgilerini Düzenle</h2>';
        echo '<form method="post">';
        echo '<input type="hidden" name="edit_code" value="' . htmlspecialchars($editCode) . '">';
        echo '<label>Ad Soyad: <input type="text" name="name" value="' . htmlspecialchars($profile['name']) . '"></label><br>';
        echo '<label>Email: <input type="email" name="email" value="' . htmlspecialchars($profile['email'] ?? '') . '"></label><br>';
        echo '<label>Telefon: <input type="text" name="phone" value="' . htmlspecialchars($profile['phone']) . '"></label><br>';
        echo '<label>Bio: <input type="text" name="bio" value="' . htmlspecialchars($profile['bio'] ?? '') . '"></label><br>';
        echo '<label>IBAN: <input type="text" name="iban" value="' . htmlspecialchars($profile['iban'] ?? '') . '"></label><br>';
        echo '<label>Kan Grubu: <input type="text" name="blood_type" value="' . htmlspecialchars($profile['blood_type'] ?? '') . '"></label><br>';
        echo '<label>Tema: <input type="text" name="theme" value="' . htmlspecialchars($profile['theme'] ?? '') . '"></label><br>';
        echo '<button type="submit" name="save_profile">Kaydet</button>';
        echo '</form>';
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
