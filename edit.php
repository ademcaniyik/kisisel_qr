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
        $showForm = false;
        $profile = $profileManager->getProfile($profileId);
        if (!$profile) {
            echo '<h2>Profil bulunamadı.</h2>';
            exit;
        }
        if (isset($_POST['save_profile'])) {
            $phone = $_POST['phone'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $iban = $_POST['iban'] ?? '';
            $blood_type = $_POST['blood_type'] ?? '';
            $theme = $_POST['theme'] ?? '';
            $socialLinks = isset($_POST['social_links']) ? $_POST['social_links'] : [];
            $profileManager->updateProfile($profileId, $profile['name'], '', $phone, $bio, $iban, $blood_type, $theme, $socialLinks);
            echo '<p class="alert alert-success">Profil başarıyla güncellendi.</p>';
            $profile = $profileManager->getProfile($profileId);
        }
        // index.php'deki modalın step1 (profil oluşturma) kısmının birebir kopyası ve JS entegrasyonu
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Profil Düzenle - Kişisel QR</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
            <link href="/kisisel_qr/assets/css/landing.css" rel="stylesheet">
            <link href="/kisisel_qr/assets/css/profile-page.css" rel="stylesheet">
            <link href="/kisisel_qr/assets/css/profile-themes.css" rel="stylesheet">
            <link href="/kisisel_qr/assets/css/social-buttons.css" rel="stylesheet">
            <style>
            /* Sosyal medya label'larını tüm temalarda okunaklı yapmak için */
            .social-label {
              color: #111 !important;
              font-weight: 700 !important;
              font-size: 1rem !important;
              text-align: center;
              letter-spacing: 0.01em;
              line-height: 1.2;
              text-shadow: 0 1px 2px #fff8, 0 0 1px #fff8;
              user-select: text;
            }
            /* Koyu temalarda da okunaklı olması için arka planı hafif beyazlaştır */
            .theme-dark .social-label,
            .theme-dark .card.bg-light .social-label,
            .card.bg-dark .social-label {
              color: #fff !important;
              text-shadow: 0 1px 2px #000a, 0 0 2px #000a;
              background: rgba(0,0,0,0.08);
              border-radius: 4px;
              padding: 1px 4px;
            }
            .social-platform-btn {
              min-height: 60px;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
            }
            </style>
        </head>
        <body style="background:#f8f9fa;">
        <div class="container" style="max-width:700px;margin:auto;margin-top:2rem;">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Profil Bilgilerini Düzenle</h2>
                    <form id="editProfileForm" method="post" autocomplete="off" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <label class="form-label d-block">Profil Fotoğrafı</label>
                            <?php
                            $photoUrl = !empty($profile['photo']) ? '/kisisel_qr/uploads/profiles/' . htmlspecialchars($profile['photo']) : '/kisisel_qr/assets/images/default-profile.svg';
                            ?>
                            <img src="<?=$photoUrl?>" alt="Profil Fotoğrafı" id="profilePhotoPreview" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid #e3e3e3;">
                            <div class="d-flex justify-content-center mt-2">
                                <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="form-control" style="max-width:220px;">
                            </div>
                            <small class="form-text text-muted">Yeni fotoğraf seçerek güncelleyebilirsiniz.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ad Soyad *</label>
                                <input type="text" class="form-control" name="name" value="<?=htmlspecialchars($profile['name'])?>" readonly style="background:#f5f5f5;cursor:not-allowed;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon *</label>
                                <div class="phone-input-container">
                                    <?php
                                    // Telefonu ülke kodu ve numara olarak ayır
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
                                    <select class="form-control country-dropdown" name="country_code" id="editCountryCode" style="max-width:110px;display:inline-block;">
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
                                    <input type="tel" class="form-control phone-number-input" name="phone" id="editPhone" value="<?=htmlspecialchars($phoneNumber)?>" required placeholder="555 555 55 55" maxlength="20" style="display:inline-block;width:calc(100% - 120px);margin-left:5px;">
                                </div>
                                <small class="form-text text-muted">Telefon numaranızı ülke kodu ile birlikte giriniz</small>
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
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="instagram"><i class="fab fa-instagram text-danger"></i><span class="d-block small social-label">Instagram</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="x"><i class="fab fa-twitter" style="color: #1da1f2;"></i><span class="d-block small social-label">X</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="linkedin"><i class="fab fa-linkedin text-primary"></i><span class="d-block small social-label">LinkedIn</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="facebook"><i class="fab fa-facebook text-primary"></i><span class="d-block small social-label">Facebook</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="youtube"><i class="fab fa-youtube text-danger"></i><span class="d-block small social-label">YouTube</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="tiktok"><i class="fab fa-tiktok text-dark"></i><span class="d-block small social-label">TikTok</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="whatsapp"><i class="fab fa-whatsapp text-success"></i><span class="d-block small social-label">WhatsApp</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="website"><i class="fas fa-globe text-info"></i><span class="d-block small social-label">Website</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="snapchat"><i class="fab fa-snapchat text-warning"></i><span class="d-block small social-label">Snapchat</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="discord"><i class="fab fa-discord text-primary"></i><span class="d-block small social-label">Discord</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="telegram"><i class="fab fa-telegram text-info"></i><span class="d-block small social-label">Telegram</span></button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="twitch"><i class="fab fa-twitch text-purple"></i><span class="d-block small social-label">Twitch</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="selectedSocialMedias" class="selected-social-medias"></div>
                        </div>
                        <button type="submit" name="save_profile" class="btn btn-primary w-100" style="font-size:1.1rem;">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/kisisel_qr/assets/js/profile-manager.js"></script>
        <script src="/kisisel_qr/assets/js/profile-page.js"></script>
        <script src="/kisisel_qr/assets/js/landing.js"></script>
        <script>
        // index.php'deki sosyal medya, tema ve input mask JS fonksiyonları
        window.addSocialMediaPlatform = window.addSocialMediaPlatform || function(platform) {
          if (typeof addSocialMediaPlatformOrig === 'function') return addSocialMediaPlatformOrig(platform);
          // ...buraya yedek fonksiyon eklenebilir...
        };

        // Sosyal medya platform butonlarına tıklama eventini her zaman güncel ve doğru şekilde bağla
        function bindSocialButtons() {
          document.querySelectorAll('.social-platform-btn').forEach(function(btn){
            btn.onclick = function(e){
              e.preventDefault();
              if(typeof window.addSocialMediaPlatform === 'function') {
                window.addSocialMediaPlatform(this.getAttribute('data-platform'));
              }
            };
          });
        }
        document.addEventListener('DOMContentLoaded', function() {
          initSocialMediaHandlers && initSocialMediaHandlers();
          updateThemePreview && updateThemePreview();
          // Mevcut sosyal medya linklerini doldur
          var socialLinks = <?php echo json_encode(json_decode($profile['social_links'] ?? '[]', true)); ?>;
          if (Array.isArray(socialLinks)) {
            socialLinks.forEach(function(item) {
              addSocialMediaPlatform && addSocialMediaPlatform(item.platform);
              setTimeout(function() {
                var idx = window.selectedSocialMedias ? selectedSocialMedias.findIndex(function(x){return x.platform===item.platform;}) : -1;
                if(idx>-1){
                  var input = document.querySelector('input[data-index="'+idx+'"]');
                  if(input){
                    input.value = item.username || '';
                    updateSocialMediaUrl && updateSocialMediaUrl(idx);
                  }
                }
              }, 200);
            });
          }
          // Profil fotoğrafı önizleme
          const photoInput = document.getElementById('editPhotoInput');
          const photoPreview = document.getElementById('profilePhotoPreview');
          if(photoInput && photoPreview) {
            photoInput.addEventListener('change', function(e) {
              if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                  photoPreview.src = ev.target.result;
                };
                reader.readAsDataURL(this.files[0]);
              }
            });
          }
          // Sosyal medya platform butonları tıklama
          document.querySelectorAll('.social-platform-btn').forEach(function(btn){
            btn.onclick = function(e){
              e.preventDefault();
              if(typeof window.addSocialMediaPlatform === 'function') {
                window.addSocialMediaPlatform(this.getAttribute('data-platform'));
              }
            };
          });
          bindSocialButtons(); // Her zaman güncel event bağla
        });
        </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo '<p style="color:red">Hatalı şifre!</p>';
    }
}

if ($showForm) {
    // Modern ve şık şifre giriş ekranı
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil Düzenleme Şifresi | Kişisel QR</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    exit;
}
