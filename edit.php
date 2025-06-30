<?php
// Profil Edit QR ile yönlendirme ve şifre kontrolü
define('ROOT', __DIR__);
require_once ROOT . '/includes/QRPoolManager.php';
require_once ROOT . '/includes/ProfileManager.php';
require_once ROOT . '/includes/template_helpers.php';

$qrPoolManager = new QRPoolManager();
$profileManager = new ProfileManager();

// URL: /edit/{edit_token}
$editToken = null;
if (isset($_GET['token'])) {
    $editToken = $_GET['token'];
} else {
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

session_start();
// CSRF token oluştur (her oturumda bir kez)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// Şifre kontrolü
$showForm = true;
$editCode = $qr['edit_code'];
$profileId = $qr['profile_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    // CSRF token kontrolü (hem profil güncelleme hem şifre giriş için)
    if ((isset($_POST['save_profile']) || isset($_POST['edit_code'])) && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? ''))) {
        ob_end_clean();
        echo '<p style="color:red">Güvenlik hatası: Geçersiz CSRF token.</p>';
        exit;
    }
    $inputCode = $_POST['edit_code'] ?? '';
    $inputPhone = $_POST['phone_check'] ?? '';
    $profilePhone = $profileManager->getProfile($profileId)['phone'] ?? '';
    $loginError = false;
    $loginErrorType = '';
    // Şifre ve telefon kontrolü
    // Telefon numaralarını normalize et (sadece rakamlar)
    $inputPhoneNorm = preg_replace('/\D+/', '', $inputPhone);
    $profilePhoneNorm = preg_replace('/\D+/', '', $profilePhone);
    if ($inputCode === $editCode && $inputPhoneNorm === $profilePhoneNorm) {
        session_regenerate_id(true); // Session fixation önlemi
        $_SESSION['edit_auth_'.$editToken] = true;
        ob_end_clean(); // Tüm tamponu temizle, hiçbir çıktı olmasın
        // Pretty URL varsa tekrar ?token= ekleme
        $redirectUrl = $_SERVER['REQUEST_URI'];
        if (strpos($redirectUrl, '/edit/') !== false) {
            // /edit/xxxxxx formatı, parametre eklemeye gerek yok
            $redirectUrl = preg_replace('/\?.*/', '', $redirectUrl); // varsa query string'i temizle
        } else {
            // Sadece ?token= ile gelmişse, parametreli şekilde yönlendir
            $redirectUrl = '/kisisel_qr/edit/' . urlencode($editToken);
        }
        header('Location: ' . $redirectUrl);
        exit;
    } else if (isset($_POST['save_profile']) && ($_SESSION['edit_auth_'.$editToken] ?? false)) {
        $profile = $profileManager->getProfile($profileId); // Her zaman güncel profili çek
        // Telefon numarasını ülke kodu ile birleştir
        $countryCode = $_POST['country_code'] ?? '+90';
        $phoneNumber = $_POST['phone'] ?? '';
        $phone = $countryCode . preg_replace('/\D+/', '', $phoneNumber);
        $bio = $_POST['bio'] ?? '';
        $iban = $_POST['iban'] ?? '';
        $blood_type = $_POST['blood_type'] ?? '';
        $theme = $_POST['theme'] ?? '';
        $socialLinks = isset($_POST['social_links']) ? $_POST['social_links'] : [];
        if (is_string($socialLinks)) {
            $decoded = json_decode($socialLinks, true);
            if (is_array($decoded)) $socialLinks = $decoded;
        }
        // Fotoğraf yükleme işlemi
        $photoUrl = $profile['photo_url'] ?? null;
        $photoData = $profile['photo_data'] ?? null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            require_once ROOT . '/includes/ImageOptimizer.php';
            try {
                $photoDataArr = $profileManager->processUploadedPhoto($_FILES['photo']);
                if ($photoDataArr && isset($photoDataArr['filename'])) {
                    $photoUrl = '/kisisel_qr/public/uploads/profiles/' . $photoDataArr['filename'];
                    $photoData = json_encode($photoDataArr, JSON_UNESCAPED_UNICODE);
                }
            } catch (Exception $e) {
                // Hata olursa eski fotoğrafı koru
            }
        }
        $profileManager->updateProfile($profileId, $profile['name'], $phone, $bio, $iban, $blood_type, $theme, $socialLinks, $photoUrl, $photoData);
        ob_end_clean();
        header('Location: /kisisel_qr/edit/' . urlencode($editToken));
        exit;
    } else if (isset($_POST['save_profile'])) {
        echo '<p style="color:red">Oturum doğrulaması başarısız. Lütfen tekrar giriş yapın.</p>';
        unset($_SESSION['edit_auth_'.$editToken]);
    } else if (isset($_POST['edit_code']) || isset($_POST['phone_check'])) {
        if ($inputCode !== $editCode && $inputPhone !== $profilePhone) {
            $loginError = true;
            $loginErrorType = 'both';
        } else if ($inputCode !== $editCode) {
            $loginError = true;
            $loginErrorType = 'code';
        } else if ($inputPhone !== $profilePhone) {
            $loginError = true;
            $loginErrorType = 'phone';
        }
    }
}

// Profil düzenleme ekranı sadece doğrulama varsa gösterilsin
if (($_SESSION['edit_auth_'.$editToken] ?? false)) {
    $profile = $profileManager->getProfile($profileId);
    if (!$profile) {
        echo '<h2>Profil bulunamadı.</h2>';
        exit;
    }
    $showSuccess = false;
    if (isset($_SESSION['profile_update_success']) && $_SESSION['profile_update_success']) {
        $showSuccess = true;
        unset($_SESSION['profile_update_success']);
    }
    // Profil fotoğrafı önizlemesi için photo_data veya photo_url kullan
    $photoUrl = '/kisisel_qr/assets/images/default-profile.svg';
    if (!empty($profile['photo_data'])) {
        $photoDataArr = json_decode($profile['photo_data'], true);
        if ($photoDataArr && isset($photoDataArr['filename'])) {
            $photoUrl = '/kisisel_qr/public/uploads/profiles/' . htmlspecialchars($photoDataArr['filename']);
        }
    } elseif (!empty($profile['photo_url'])) {
        $photoUrl = $profile['photo_url'];
        if (strpos($photoUrl, '/kisisel_qr/') === false) {
            $photoUrl = '/kisisel_qr/public/uploads/profiles/' . htmlspecialchars($photoUrl);
        }
    }
    renderPageHeader('Profil Düzenle - Kişisel QR', ['/kisisel_qr/assets/css/profile-edit.css']);
    ?>
    <div class="container py-5">
        <?php if ($showSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="profileSuccessAlert">
            <strong>Başarılı!</strong> Profiliniz başarıyla güncellendi.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
        setTimeout(function(){
            var alert = document.getElementById('profileSuccessAlert');
            if(alert) alert.classList.remove('show');
        }, 3500);
        </script>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Profilini Düzenle</h4>
                    </div>
                    <div class="card-body">
                        <form id="editProfileForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?=$csrfToken?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ad Soyad *</label>
                                    <input type="text" class="form-control" name="name" value="<?=htmlspecialchars($profile['name'])?>" readonly style="background:#f5f5f5;cursor:not-allowed;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefon *</label>
                                    <div class="phone-input-container d-flex">
                                        <?php
                                        $phoneRaw = $profile['phone'] ?? '';
                                        $countryCode = '+90';
                                        $phoneNumber = '';
                                        if (preg_match('/^(\+\d{1,3})(\d{10,})$/', $phoneRaw, $m)) {
                                            $countryCode = $m[1];
                                            $phoneNumber = $m[2];
                                        } elseif (preg_match('/^(\+\d{1,3})(.*)$/', $phoneRaw, $m)) {
                                            $countryCode = $m[1];
                                            $phoneNumber = trim($m[2]);
                                        } elseif (preg_match('/^(\d{10,})$/', $phoneRaw)) {
                                            $countryCode = '+90';
                                            $phoneNumber = $phoneRaw;
                                        }
                                        ?>
                                        <select class="form-control country-dropdown me-2" name="country_code" id="editCountryCode" style="max-width:110px;">
                                            <option value="+90" data-flag="🇹🇷" <?=($countryCode==='+90')?'selected':''?>>🇹🇷 +90</option>
                                            <option value="+1" data-flag="🇺🇸" <?=($countryCode==='+1')?'selected':''?>>🇺🇸 +1</option>
                                            <option value="+44" data-flag="🇬🇧" <?=($countryCode==='+44')?'selected':''?>>🇬🇧 +44</option>
                                            <option value="+49" data-flag="🇩🇪" <?=($countryCode==='+49')?'selected':''?>>🇩🇪 +49</option>
                                            <option value="+33" data-flag="🇫🇷" <?=($countryCode==='+33')?'selected':''?>>🇫🇷 +33</option>
                                            <option value="+971" data-flag="🇦🇪" <?=($countryCode==='+971')?'selected':''?>>🇦🇪 +971</option>
                                            <option value="+966" data-flag="🇸🇦" <?=($countryCode==='+966')?'selected':''?>>🇸🇦 +966</option>
                                            <option value="+7" data-flag="🇷🇺" <?=($countryCode==='+7')?'selected':''?>>🇷🇺 +7</option>
                                            <option value="+86" data-flag="🇨🇳" <?=($countryCode==='+86')?'selected':''?>>🇨🇳 +86</option>
                                            <option value="+91" data-flag="🇮🇳" <?=($countryCode==='+91')?'selected':''?>>🇮🇳 +91</option>
                                        </select>
                                        <input type="tel" class="form-control phone-number-input" name="phone" id="editPhone" value="<?=htmlspecialchars($phoneNumber)?>" required placeholder="555 555 55 55" maxlength="20" pattern="[0-9]{10,15}" title="Sadece rakam, 10-15 hane arası.">
                                    </div>
                                    <small class="form-text text-muted">Telefon numaranızı ülke kodu ile birlikte giriniz</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Profil Fotoğrafı</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?=$photoUrl?>" alt="Profil Fotoğrafı" id="profilePhotoPreview" class="profile-photo" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="form-control" style="max-width:250px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kısa Yazı (Bio)</label>
                                <textarea class="form-control" name="bio" rows="2" placeholder="Kendinizi tanıtın..."><?=htmlspecialchars($profile['bio'] ?? '')?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">IBAN</label>
                                    <input type="text" class="form-control" name="iban" value="<?=htmlspecialchars($profile['iban'] ?? '')?>" placeholder="TR00 0000 0000 0000 0000 0000 00" maxlength="32" pattern="^TR[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{2}$" title="TR ile başlayan 26 haneli IBAN.">
                                    <small class="form-text text-muted">TR ile başlayan 26 haneli İban numarası</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kan Grubu</label>
                                    <select class="form-select" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        <option value="A+" <?=($profile['blood_type'] ?? '')==='A+'?'selected':''?>>A Rh+</option>
                                        <option value="A-" <?=($profile['blood_type'] ?? '')==='A-'?'selected':''?>>A Rh-</option>
                                        <option value="B+" <?=($profile['blood_type'] ?? '')==='B+'?'selected':''?>>B Rh+</option>
                                        <option value="B-" <?=($profile['blood_type'] ?? '')==='B-'?'selected':''?>>B Rh-</option>
                                        <option value="AB+" <?=($profile['blood_type'] ?? '')==='AB+'?'selected':''?>>AB Rh+</option>
                                        <option value="AB-" <?=($profile['blood_type'] ?? '')==='AB-'?'selected':''?>>AB Rh-</option>
                                        <option value="0+" <?=($profile['blood_type'] ?? '')==='0+'?'selected':''?>>0 Rh+</option>
                                        <option value="0-" <?=($profile['blood_type'] ?? '')==='0-'?'selected':''?>>0 Rh-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tema Seçimi</label>
                                <select class="form-select" name="theme" id="editTheme">
                                    <option value="default" <?=($profile['theme'] ?? '')==='default'?'selected':''?>>Sade Temiz (Varsayılan)</option>
                                    <option value="blue" <?=($profile['theme'] ?? '')==='blue'?'selected':''?>>Deniz Mavisi</option>
                                    <option value="nature" <?=($profile['theme'] ?? '')==='nature'?'selected':''?>>Günbatımı Sıcak</option>
                                    <option value="elegant" <?=($profile['theme'] ?? '')==='elegant'?'selected':''?>>Doğa Yeşil</option>
                                    <option value="gold" <?=($profile['theme'] ?? '')==='gold'?'selected':''?>>Altın Lüks</option>
                                    <option value="purple" <?=($profile['theme'] ?? '')==='purple'?'selected':''?>>Kraliyet Moru</option>
                                    <option value="dark" <?=($profile['theme'] ?? '')==='dark'?'selected':''?>>Karanlık Siyah</option>
                                    <option value="ocean" <?=($profile['theme'] ?? '')==='ocean'?'selected':''?>>Sakura Pembe</option>
                                    <option value="minimal" <?=($profile['theme'] ?? '')==='minimal'?'selected':''?>>Şık Mor</option>
                                    <option value="pastel" <?=($profile['theme'] ?? '')==='pastel'?'selected':''?>>Pastel Rüya</option>
                                    <option value="retro" <?=($profile['theme'] ?? '')==='retro'?'selected':''?>>Retro Synthwave</option>
                                    <option value="neon" <?=($profile['theme'] ?? '')==='neon'?'selected':''?>>Neon Siber</option>
                                </select>
                                <small class="form-text text-muted">Profilinizde kullanılacak görsel tema</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sosyal Medya Hesapları</label>
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Platform Ekle</h6>
                                        <div class="row g-2 social-platforms-grid">
                                            <div class="col-12">
                                                <div id="socialPlatformsButtons" class="d-flex flex-wrap gap-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="socialLinksContainer"></div>
                            </div>
                            <input type="hidden" name="social_links" id="socialLinksInput">
                            <button type="submit" name="save_profile" class="btn btn-primary w-100" style="font-size:1.1rem;" id="saveProfileBtn">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.0/imask.min.js"></script>
    <script src="/kisisel_qr/assets/js/profile-manager.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Telefon input mask
      var editPhone = document.getElementById('editPhone');
      if(editPhone && window.IMask){
        IMask(editPhone, { mask: '000 000 00 00' });
      }
      // Kaydet butonuna loading animasyonu
      var editForm = document.getElementById('editProfileForm');
      var saveBtn = document.getElementById('saveProfileBtn');
      if(editForm && saveBtn){
        editForm.addEventListener('submit', function(){
          saveBtn.disabled = true;
          saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Kaydediliyor...';
        });
      }
    });
    </script>
    <?php
    renderPageFooter();
    exit;
}

// Modern ve şık şifre giriş ekranı (This part is shown if session auth is false)
if ($showForm) {
    renderPageHeader('Profil Düzenleme Şifresi | Kişisel QR', ['/kisisel_qr/assets/css/landing.css']);
    ?>
    <div class="container">
        <div class="card edit-pass-card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                    <h4 class="fw-bold">Profil Düzenleme Şifresi</h4>
                    <p class="text-muted mb-0">Profil bilgilerini güncellemek için size verilen şifreyi giriniz.</p>
                </div>
                <?php if (!empty($loginError)) {
    if ($loginErrorType === 'both') {
        echo '<div class="alert alert-danger">Şifre ve telefon numarası hatalı!</div>';
    } else if ($loginErrorType === 'code') {
        echo '<div class="alert alert-danger">Şifre hatalı!</div>';
    } else if ($loginErrorType === 'phone') {
        echo '<div class="alert alert-danger">Telefon numarası hatalı!</div>';
    }
} ?>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?=$csrfToken?>">
                    <div class="mb-3">
                        <label class="form-label">Edit Şifresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="edit_code" id="editCodeInput" class="form-control" placeholder="Şifrenizi girin" required autofocus pattern="[A-Za-z0-9]{4,}" title="En az 4 karakter, harf ve rakam içerebilir.">
                            <button type="button" class="btn btn-outline-secondary" tabindex="-1" id="toggleEditCode"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profil oluştururken kullandığınız telefon numarası</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone_check" class="form-control" placeholder="5xx xxx xx xx" maxlength="20" required pattern="[0-9]{10,15}" title="Sadece rakam, 10-15 hane arası." id="loginPhoneInput">
                        </div>
                        <small class="form-text text-muted">Güvenlik için telefon numaranız istenmektedir.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Devam</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.0/imask.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var toggleBtn = document.getElementById('toggleEditCode');
      var input = document.getElementById('editCodeInput');
      if(toggleBtn && input) {
        toggleBtn.addEventListener('click', function() {
          if(input.type === 'password') {
            input.type = 'text';
            toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
          } else {
            input.type = 'password';
            toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
          }
        });
      }
      // Telefon input mask
      var phoneInput = document.getElementById('loginPhoneInput');
      if(phoneInput && window.IMask){
        IMask(phoneInput, { mask: '000 000 00 00' });
      }
    });
    </script>
    <?php
    renderPageFooter();
    exit; // Exit after showing the password form
}
