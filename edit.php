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

session_start();

// Şifre kontrolü
$showForm = true;
$editCode = $qr['edit_code'];
$profileId = $qr['profile_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['edit_code'] ?? '';
    $inputPhone = $_POST['phone_check'] ?? '';
    // Şifre ve telefon kontrolü
    if ($inputCode === $editCode && ($inputPhone === ($profileManager->getProfile($profileId)['phone'] ?? ''))) {
        $_SESSION['edit_auth_'.$editToken] = true;
        header('Location: '.$_SERVER['REQUEST_URI'].'?token='.urlencode($editToken));
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
        header('Location: /kisisel_qr/edit/' . urlencode($editToken) . '?success=1');
        exit;
    } else if (isset($_POST['save_profile'])) {
        echo '<p style="color:red">Oturum doğrulaması başarısız. Lütfen tekrar giriş yapın.</p>';
        session_destroy();
        
    }
}

// Profil düzenleme ekranı sadece doğrulama varsa gösterilsin
if (($_SESSION['edit_auth_'.$editToken] ?? false)) {
    $profile = $profileManager->getProfile($profileId);
    if (!$profile) {
        echo '<h2>Profil bulunamadı.</h2>';
        exit;
    }
    $showSuccess = isset($_GET['success']) && $_GET['success'] == 1;
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
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil Düzenle - Kişisel QR</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="/kisisel_qr/assets/css/profile-edit.css" rel="stylesheet">
    </head>
    <body>
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
                                        if (preg_match('/^(\+\d{1,3})(.*)$/', $phoneRaw, $m)) {
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
                                        <input type="tel" class="form-control phone-number-input" name="phone" id="editPhone" value="<?=htmlspecialchars($phoneNumber)?>" required placeholder="555 555 55 55" maxlength="20" pattern="[0-9 ]{10,20}">
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
                                    <input type="text" class="form-control" name="iban" value="<?=htmlspecialchars($profile['iban'] ?? '')?>" placeholder="TR00 0000 0000 0000 0000 0000 00" maxlength="32">
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
                                            <?php
                                            $platforms = [
                                                'instagram' => 'Instagram', 'x' => 'X', 'linkedin' => 'LinkedIn', 'facebook' => 'Facebook',
                                                'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'whatsapp' => 'WhatsApp', 'website' => 'Website',
                                                'snapchat' => 'Snapchat', 'discord' => 'Discord', 'telegram' => 'Telegram', 'twitch' => 'Twitch'
                                            ];
                                            $icons = [
                                                'instagram' => '<i class="fab fa-instagram text-danger"></i>',
                                                'x' => '<i class="fab fa-twitter" style="color:#1da1f2"></i>',
                                                'linkedin' => '<i class="fab fa-linkedin text-primary"></i>',
                                                'facebook' => '<i class="fab fa-facebook text-primary"></i>',
                                                'youtube' => '<i class="fab fa-youtube text-danger"></i>',
                                                'tiktok' => '<i class="fab fa-tiktok text-dark"></i>',
                                                'whatsapp' => '<i class="fab fa-whatsapp text-success"></i>',
                                                'website' => '<i class="fas fa-globe text-info"></i>',
                                                'snapchat' => '<i class="fab fa-snapchat text-warning"></i>',
                                                'discord' => '<i class="fab fa-discord text-primary"></i>',
                                                'telegram' => '<i class="fab fa-telegram text-info"></i>',
                                                'twitch' => '<i class="fab fa-twitch text-purple"></i>'
                                            ];
                                            foreach ($platforms as $key => $label) {
                                                echo '<div class="col-6 col-md-4 col-lg-3 mb-2">';
                                                echo '<button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="'.$key.'">'.$icons[$key].'<span class="d-block small">'.$label.'</span></button>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div id="selectedSocialMedias" class="selected-social-medias"></div>
                            </div>
                            <input type="hidden" name="social_links" id="socialLinksInput">
                            <button type="submit" name="save_profile" class="btn btn-primary w-100" style="font-size:1.1rem;">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Sosyal medya platform ekleme ve mevcut linkleri doldurma
    window.selectedSocialMedias = window.selectedSocialMedias || [];
    window.addSocialMediaPlatform = function(platform, initialUsername = '') {
      if (!window.selectedSocialMedias) window.selectedSocialMedias = [];
      if (window.selectedSocialMedias.some(function(x){return x.platform===platform;})) return;
      var idx = window.selectedSocialMedias.length;
      window.selectedSocialMedias.push({platform: platform, username: initialUsername});
      var labelMap = { instagram: 'Instagram', x: 'X', linkedin: 'LinkedIn', facebook: 'Facebook', youtube: 'YouTube', tiktok: 'TikTok', whatsapp: 'WhatsApp', website: 'Website', snapchat: 'Snapchat', discord: 'Discord', telegram: 'Telegram', twitch: 'Twitch' };
      var iconMap = { instagram: '<i class="fab fa-instagram text-danger"></i>', x: '<i class="fab fa-twitter" style="color:#1da1f2"></i>', linkedin: '<i class="fab fa-linkedin text-primary"></i>', facebook: '<i class="fab fa-facebook text-primary"></i>', youtube: '<i class="fab fa-youtube text-danger"></i>', tiktok: '<i class="fab fa-tiktok text-dark"></i>', whatsapp: '<i class="fab fa-whatsapp text-success"></i>', website: '<i class="fas fa-globe text-info"></i>', snapchat: '<i class="fab fa-snapchat text-warning"></i>', discord: '<i class="fab fa-discord text-primary"></i>', telegram: '<i class="fab fa-telegram text-info"></i>', twitch: '<i class="fab fa-twitch text-purple"></i>' };
      var html = '<div class="input-group mb-2" data-platform="'+platform+'">'+
        '<span class="input-group-text">'+iconMap[platform]+'</span>'+
        '<input type="text" class="form-control" placeholder="'+labelMap[platform]+' kullanıcı adı/link" data-index="'+idx+'" value="'+(initialUsername||'')+'" oninput="window.selectedSocialMedias['+idx+'].username=this.value">'+
        '<button type="button" class="btn btn-outline-danger" onclick="window.removeSocialMediaPlatform(\''+platform+'\')"><i class="fas fa-times"></i></button>'+ '</div>';
      var selectedMediasContainer = document.getElementById('selectedSocialMedias');
      if(selectedMediasContainer) selectedMediasContainer.insertAdjacentHTML('beforeend', html);
    };
    window.removeSocialMediaPlatform = function(platform) {
      var idx = window.selectedSocialMedias.findIndex(function(x){return x.platform===platform;});
      if(idx>-1) window.selectedSocialMedias.splice(idx,1);
      var el = document.querySelector('#selectedSocialMedias [data-platform="'+platform+'"]');
      if(el) el.remove();
    };
    document.addEventListener('DOMContentLoaded', function() {
      // Mevcut sosyal medya linklerini doldur
      var socialLinks = <?php echo json_encode(json_decode($profile['social_links'] ?? '[]', true)); ?>;
      if (Array.isArray(socialLinks)) {
        socialLinks.forEach(function(item) {
          window.addSocialMediaPlatform && window.addSocialMediaPlatform(item.platform, item.username || '');
        });
      }
      document.querySelectorAll('.social-platform-btn').forEach(function(btn){
        if (btn._socialClickListener) btn.removeEventListener('click', btn._socialClickListener);
        btn._socialClickListener = function(e){
          e.preventDefault();
          const platform = this.getAttribute('data-platform');
          this.classList.add('btn-success');
          setTimeout(() => { this.classList.remove('btn-success'); }, 300);
          if(typeof window.addSocialMediaPlatform === 'function') window.addSocialMediaPlatform(platform);
        };
        btn.addEventListener('click', btn._socialClickListener);
      });
      var editForm = document.getElementById('editProfileForm');
      if(editForm){
        editForm.addEventListener('submit', function(e){
          var hiddenInput = document.getElementById('socialLinksInput');
          if(hiddenInput){
            hiddenInput.value = JSON.stringify(window.selectedSocialMedias || []);
          }
        });
      }
    });
    // Telefon inputunda +90 seçiliyse sadece 10 hane ve rakam girilsin
    const countryCodeInput = document.getElementById('editCountryCode');
    const phoneInput = document.getElementById('editPhone');
    function enforceTRPhoneFormat() {
      if (countryCodeInput && phoneInput) {
        if (countryCodeInput.value === '+90') {
          phoneInput.setAttribute('maxlength', '10');
          phoneInput.setAttribute('pattern', '\\d{10}');
          phoneInput.placeholder = '5555555555';
          phoneInput.value = phoneInput.value.replace(/[^0-9]/g, '').slice(0, 10);
          phoneInput.oninput = function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
          };
        } else {
          phoneInput.setAttribute('maxlength', '20');
          phoneInput.setAttribute('pattern', '[0-9 ]{10,20}');
          phoneInput.placeholder = '555 555 55 55';
          phoneInput.oninput = null;
        }
      }
    }
    if (countryCodeInput && phoneInput) {
      countryCodeInput.addEventListener('change', enforceTRPhoneFormat);
      enforceTRPhoneFormat();
    }
    </script>
    </body>
    </html>
    <?php
    exit;
}

// Modern ve şık şifre giriş ekranı (This part is shown if session auth is false)
if ($showForm) {
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil Düzenleme Şifresi | Kişisel QR</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="/kisisel_qr/assets/css/landing.css" rel="stylesheet">
        <style>
            body { background: #f8f9fa; }
            .edit-pass-card { max-width: 400px; margin: 5vh auto; border-radius: 1.5rem; box-shadow: 0 4px 32px rgba(0,0,0,0.08); }
            .edit-pass-card .card-body { padding: 2.5rem 2rem; }
            .edit-pass-card .form-control:focus { box-shadow: 0 0 0 2px #3498db33; border-color: #3498db; }
            .edit-pass-card .btn-primary { font-size: 1.1rem; border-radius: 2rem; }
            .edit-pass-card .input-group-text { background: #f1f3f6; border: none; }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="card edit-pass-card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                    <h4 class="fw-bold">Profil Düzenleme Şifresi</h4>
                    <p class="text-muted mb-0">Profil bilgilerini güncellemek için size verilen şifreyi giriniz.</p>
                </div>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Edit Şifresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="text" name="edit_code" class="form-control" placeholder="Şifrenizi girin" required autofocus>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profil oluştururken kullandığınız telefon numarası</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone_check" class="form-control" placeholder="5xx xxx xx xx" maxlength="20" required>
                        </div>
                        <small class="form-text text-muted">Güvenlik için telefon numaranız istenmektedir.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Devam</button>
                </form>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit; // Exit after showing the password form
} // Close the second PHP if block here
?>
