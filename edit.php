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
        $phone = $_POST['phone'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $iban = $_POST['iban'] ?? '';
        $blood_type = $_POST['blood_type'] ?? '';
        $theme = $_POST['theme'] ?? '';
        $socialLinks = isset($_POST['social_links']) ? $_POST['social_links'] : [];
        if (is_string($socialLinks)) {
            $decoded = json_decode($socialLinks, true);
            if (is_array($decoded)) $socialLinks = $decoded;
        }
        $profileManager->updateProfile($profileId, $profile['name'], '', $phone, $bio, $iban, $blood_type, $theme, $socialLinks);
        header('Location: '.$_SERVER['REQUEST_URI'].'?token='.urlencode($editToken).'&success=1');
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
    // index.php'deki modalın step1 (profil oluşturma) kısmasının birebir kopyası ve JS entegrasyonu
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
        <link href="/kisisel_qr/assets/css/profile-edit.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="edit-container">
            <div class="edit-header">
                <h1>Profilinizi Özelleştirin</h1>
                <p>Kişisel QR profilinizi dilediğiniz gibi düzenleyin</p>
            </div>
            
            <div class="edit-card">
                <ul class="edit-sections">
                    <li class="active"><i class="fas fa-user-circle me-2"></i>Temel Bilgiler</li>
                    <li><i class="fas fa-paint-brush me-2"></i>Görünüm</li>
                    <li><i class="fas fa-share-alt me-2"></i>Sosyal Medya</li>
                    <li><i class="fas fa-cog me-2"></i>Diğer Ayarlar</li>
                </ul>
                
                <div class="edit-content">
                    <form id="editProfileForm" method="post" autocomplete="off" enctype="multipart/form-data">
                        <div class="profile-photo-container">
                            <?php
                            $photoUrl = !empty($profile['photo']) ? '/kisisel_qr/uploads/profiles/' . htmlspecialchars($profile['photo']) : '/kisisel_qr/assets/images/default-profile.svg';
                            ?>
                            <img src="<?=$photoUrl?>" alt="Profil Fotoğrafı" id="profilePhotoPreview" class="profile-photo">
                            <label for="editPhotoInput" class="photo-upload-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="d-none">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ad Soyad *</label>
                            <input type="text" class="form-control" name="name" value="<?=htmlspecialchars($profile['name'])?>" readonly style="background:#f5f5f5;cursor:not-allowed;">
                        </div>
                        <div class="mb-3">
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
                        <input type="hidden" name="social_links" id="socialLinksInput">
                        <button type="submit" name="save_profile" class="btn btn-primary w-100" style="font-size:1.1rem;">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <button class="preview-toggle" id="previewToggle">
            <i class="fas fa-eye"></i>
        </button>

        <div class="floating-preview" id="floatingPreview">
            <div class="theme-preview">
                <!-- Profil önizlemesi burada gösterilecek -->
            </div>
            <button class="btn btn-sm btn-outline-secondary w-100" onclick="document.getElementById('floatingPreview').style.display='none'">
                <i class="fas fa-times me-2"></i>Önizlemeyi Kapat
            </button>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/kisisel_qr/assets/js/profile-manager.js"></script>
        <script src="/kisisel_qr/assets/js/profile-page.js"></script>
        <script src="/kisisel_qr/assets/js/landing.js"></script>
        <script>
        // HTML özel karakterlerini temizle (Fonksiyonu global scope'a taşıdık)
        function htmlspecialchars(str) {
            if (typeof str !== 'string') {
                return str;
            }
            return str.replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '>').replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
        }

        // --- Sosyal Medya Platform Ekleme Fonksiyonu (Bağımsız ve Garantili) ---
        window.selectedSocialMedias = window.selectedSocialMedias || [];
        window.addSocialMediaPlatform = function(platform, initialUsername = '') { // initialUsername parametresi eklendi
          console.log('addSocialMediaPlatform called for:', platform, 'with username:', initialUsername); // Debug log
          if (!window.selectedSocialMedias) window.selectedSocialMedias = [];
          // Zaten ekli mi kontrol et
          if (window.selectedSocialMedias.some(function(x){return x.platform===platform;})) {
              console.log(platform, 'already added.'); // Debug log
              return;
          }
          var idx = window.selectedSocialMedias.length;
          window.selectedSocialMedias.push({platform: platform, username: initialUsername}); // initialUsername kullanılıyor
          console.log('selectedSocialMedias after push:', window.selectedSocialMedias); // Debug log

          var labelMap = {
            instagram: 'Instagram', x: 'X', linkedin: 'LinkedIn', facebook: 'Facebook', youtube: 'YouTube', tiktok: 'TikTok', whatsapp: 'WhatsApp', website: 'Website', snapchat: 'Snapchat', discord: 'Discord', telegram: 'Telegram', twitch: 'Twitch'
          };
          var iconMap = {
            instagram: '<i class="fab fa-instagram text-danger"></i>',
            x: '<i class="fab fa-twitter" style="color:#1da1f2"></i>',
            linkedin: '<i class="fab fa-linkedin text-primary"></i>',
            facebook: '<i class="fab fa-facebook text-primary"></i>',
            youtube: '<i class="fab fa-youtube text-danger"></i>',
            tiktok: '<i class="fab fa-tiktok text-dark"></i>',
            whatsapp: '<i class="fab fa-whatsapp text-success"></i>',
            website: '<i class="fas fa-globe text-info"></i>',
            snapchat: '<i class="fab fa-snapchat text-warning"></i>',
            discord: '<i class="fab fa-discord text-primary"></i>',
            telegram: '<i class="fab fa-telegram text-info"></i>',
            twitch: '<i class="fab fa-twitch text-purple"></i>'
          };

          var html = '<div class="input-group mb-2" data-platform="'+platform+'">'+
            '<span class="input-group-text">'+iconMap[platform]+'</span>'+
            '<input type="text" class="form-control" placeholder="'+labelMap[platform]+' kullanıcı adı/link" data-index="'+idx+'" value="'+htmlspecialchars(initialUsername)+'" oninput="window.selectedSocialMedias['+idx+'].username=this.value">'+
            '<button type="button" class="btn btn-outline-danger" onclick="window.removeSocialMediaPlatform(\''+platform+'\')"><i class="fas fa-times"></i></button>'+
            '</div>';

          var selectedMediasContainer = document.getElementById('selectedSocialMedias');
          if(selectedMediasContainer) {
              selectedMediasContainer.insertAdjacentHTML('beforeend', html);
              console.log('HTML inserted for', platform); // Debug log
          } else {
              console.error('#selectedSocialMedias element not found!'); // Debug log
          }
        };

        window.removeSocialMediaPlatform = function(platform) {
          console.log('removeSocialMediaPlatform called for:', platform); // Debug log
          var idx = window.selectedSocialMedias.findIndex(function(x){return x.platform===platform;});
          if(idx>-1) {
              window.selectedSocialMedias.splice(idx,1);
              console.log('selectedSocialMedias after splice:', window.selectedSocialMedias); // Debug log
          }
          var el = document.querySelector('#selectedSocialMedias [data-platform="'+platform+'"]');
          if(el) {
              el.remove();
              console.log('Element removed for', platform); // Debug log
          } else {
              console.error('Element not found for removal:', platform); // Debug log
          }
        };

        document.addEventListener('DOMContentLoaded', function() {
          console.log('DOMContentLoaded fired'); // Debug log

          // Mevcut sosyal medya linklerini doldur
          var socialLinks = <?php echo json_encode(json_decode($profile['social_links'] ?? '[]', true)); ?>;
          console.log('Existing social links from PHP:', socialLinks); // Debug log
          if (Array.isArray(socialLinks)) {
            socialLinks.forEach(function(item) {
              console.log('Attempting to add existing social link:', item.platform, item.username); // Debug log
              window.addSocialMediaPlatform && window.addSocialMediaPlatform(item.platform, item.username || ''); // Mevcut kullanıcı adıyla ekle
            });
          }

          // Direct event listeners for social media buttons
          document.querySelectorAll('.social-platform-btn').forEach(function(btn){
              // Önceki event listener'ları kaldır (emin olmak için)
              if (btn._socialClickListener) {
                  btn.removeEventListener('click', btn._socialClickListener);
              }
              // Yeni event listener ekle
              btn._socialClickListener = function(e){
                e.preventDefault();
                const platform = this.getAttribute('data-platform');
                console.log('Button clicked directly for platform:', platform); // Debug log

                // Add temporary visual feedback
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.classList.remove('btn-success');
                }, 300);

                if(typeof window.addSocialMediaPlatform === 'function') {
                  window.addSocialMediaPlatform(platform);
                } else {
                    console.error('addSocialMediaPlatform function is not defined when button clicked directly'); // Debug log
                }
              };
              btn.addEventListener('click', btn._socialClickListener);
              console.log('Direct event listener attached to button for platform:', btn.getAttribute('data-platform')); // Debug log
          }); // Close the forEach loop

          // Form submit olmadan önce sosyal medya dizisini inputa yaz
          var editForm = document.getElementById('editProfileForm');
          if(editForm){
            editForm.addEventListener('submit', function(e){
              var hiddenInput = document.getElementById('socialLinksInput');
              if(hiddenInput){
                hiddenInput.value = JSON.stringify(window.selectedSocialMedias || []);
                console.log('Social links prepared for submit:', hiddenInput.value); // Debug log
              } else {
                  console.error('socialLinksInput hidden input not found'); // Debug log
              }
            });
          }
          // Diğer DOMContentLoaded içindeki kodlar
        }); // Close DOMContentLoaded listener
        </script>
    </body>
    </html>
    <?php
    exit; // Exit after showing the profile edit form
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
    exit; // Exit after showing the password form
} // Close the second PHP if block here
?>
