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
        <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            background: linear-gradient(135deg, #f6f8fa 0%, #f0f2f5 100%);
            min-height: 100vh;
        }

        .edit-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .edit-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .edit-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .edit-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .edit-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .edit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .edit-sections {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee;
            padding: 0;
            list-style: none;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .edit-sections li {
            flex: none;
            padding: 1rem 1.5rem;
            color: #666;
            cursor: pointer;
            position: relative;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .edit-sections li.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .edit-sections li.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-color);
        }

        .edit-content {
            padding: 2rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .profile-photo-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .photo-upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-upload-btn:hover {
            transform: scale(1.1);
            background: var(--dark-color);
        }

        .social-platform-btn {
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
            border: 2px solid #eee;
            background: #fff;
        }

        .social-platform-btn:hover {
            transform: translateY(-2px);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .social-platform-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .social-label {
            font-weight: 600 !important;
            color: var(--dark-color) !important;
        }

        .save-button {
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .save-button:hover {
            background: var(--dark-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .selected-social-medias .input-group {
            margin-bottom: 1rem;
        }

        .selected-social-medias .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #eee;
            border-right: none;
        }

        .selected-social-medias .form-control {
            border-radius: 0 12px 12px 0;
            border-left: none;
        }

        .selected-social-medias .btn-outline-danger {
            border-radius: 12px;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .edit-content {
                padding: 1.5rem;
            }

            .edit-sections {
                padding: 0 1rem;
            }

            .edit-sections li {
                padding: 0.75rem 1rem;
            }
        }

        .floating-preview {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            padding: 1rem;
            display: none;
            z-index: 1000;
            max-width: 300px;
        }

        .theme-preview {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .preview-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .preview-toggle:hover {
            transform: scale(1.1);
        }

        </style>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Mevcut JS kodları
            // ...existing code...

            // Yeni interaktif özellikler
            const sections = document.querySelectorAll('.edit-sections li');
            sections.forEach(section => {
                section.addEventListener('click', function() {
                    sections.forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                    // Seksiyon değişikliğinde smooth scroll
                    document.querySelector('.edit-content').scrollIntoView({ behavior: 'smooth' });
                });
            });

            // Profil fotoğrafı yükleme
            const photoInput = document.getElementById('editPhotoInput');
            const photoPreview = document.getElementById('profilePhotoPreview');
            if(photoInput && photoPreview) {
                photoInput.addEventListener('change', function(e) {
                    if(this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            photoPreview.src = ev.target.result;
                            photoPreview.style.animation = 'pulse 0.5s';
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }

            // Önizleme toggle
            const previewToggle = document.getElementById('previewToggle');
            const floatingPreview = document.getElementById('floatingPreview');
            previewToggle.addEventListener('click', function() {
                floatingPreview.style.display = floatingPreview.style.display === 'none' ? 'block' : 'none';
            });

            // Form değişikliklerinde otomatik kaydetme göstergesi
            const form = document.getElementById('editProfileForm');
            const saveBtn = form.querySelector('button[type="submit"]');
            let timeout;
            
            form.addEventListener('input', function() {
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Kaydet*';
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Kaydet';
                }, 2000);
            });
        });
        // --- Sosyal Medya Platform Ekleme Fonksiyonu (Bağımsız ve Garantili) ---
        window.selectedSocialMedias = window.selectedSocialMedias || [];
        window.addSocialMediaPlatform = function(platform) {
          if (!window.selectedSocialMedias) window.selectedSocialMedias = [];
          // Zaten ekli mi kontrol et
          if (window.selectedSocialMedias.some(function(x){return x.platform===platform;})) return;
          var idx = window.selectedSocialMedias.length;
          window.selectedSocialMedias.push({platform: platform, username: ''});
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
            '<input type="text" class="form-control" placeholder="'+labelMap[platform]+' kullanıcı adı/link" data-index="'+idx+'" oninput="window.selectedSocialMedias['+idx+'].username=this.value">'+
            '<button type="button" class="btn btn-outline-danger" onclick="window.removeSocialMediaPlatform(\''+platform+'\')"><i class="fas fa-times"></i></button>'+
            '</div>';
          document.getElementById('selectedSocialMedias').insertAdjacentHTML('beforeend', html);
        };
        window.removeSocialMediaPlatform = function(platform) {
          var idx = window.selectedSocialMedias.findIndex(function(x){return x.platform===platform;});
          if(idx>-1) window.selectedSocialMedias.splice(idx,1);
          var el = document.querySelector('#selectedSocialMedias [data-platform="'+platform+'"]');
          if(el) el.remove();
        };
        // Tüm sosyal medya butonlarına tıklama eventini tekrar bağla
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
          bindSocialButtons();
        });
        </script>
    </body>
    </html>
    <?php
    exit;
}

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
