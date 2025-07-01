<?php
/**
 * Profil Edit QR ile yönlendirme ve şifre kontrolü
 * Optimize edilmiş versiyon - Mantık hataları düzeltildi
 */

define('ROOT', __DIR__);
require_once ROOT . '/includes/template_helpers.php';
require_once ROOT . '/includes/utilities.php';
require_once ROOT . '/includes/UserProfileManager.php';

// Error reporting (production'da kapatılmalı)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sınıf örneği
$userProfileManager = new UserProfileManager();

/**
 * Loglama fonksiyonları
 */
function log_edit_error($msg) {
    $logFile = __DIR__ . '/logs/edit_error_log.txt';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $msg\n";
    error_log($logMessage);
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function log_edit_debug($msg, $data = null) {
    $logMessage = $msg;
    if ($data !== null) {
        $logMessage .= "\nData: " . (is_scalar($data) ? $data : json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    log_edit_error("[DEBUG] " . $logMessage);
}

/**
 * POST verilerini güvenli şekilde al ve sanitize et
 */
function getSanitizedPostData() {
    $data = [];
    
    // Temel alanları sanitize et
    $data['name'] = trim(Utilities::sanitizeInput($_POST['name'] ?? ''));
    $data['phone'] = preg_replace('/\D+/', '', Utilities::sanitizeInput($_POST['phone'] ?? ''));
    $data['bio'] = trim(Utilities::sanitizeInput($_POST['bio'] ?? ''));
    $data['iban'] = trim(str_replace(' ', '', strtoupper(Utilities::sanitizeInput($_POST['iban'] ?? ''))));
    $data['blood_type'] = trim(Utilities::sanitizeInput($_POST['blood_type'] ?? ''));
    $data['theme'] = trim(Utilities::sanitizeInput($_POST['theme'] ?? 'default'));
    
    // Social links'i işle
    $socialLinks = $_POST['social_links'] ?? '';
    if (empty($socialLinks)) {
        $data['social_links'] = [];
    } elseif (is_string($socialLinks)) {
        $decoded = json_decode($socialLinks, true);
        $data['social_links'] = ($decoded === null) ? [] : $decoded;
    } else {
        $data['social_links'] = is_array($socialLinks) ? $socialLinks : [];
    }
    
    log_edit_debug('Sanitized POST data', $data);
    return $data;
}

/**
 * Telefon numaralarını normalize et
 */
function normalizePhone($phone, $addCountryCode = false) {
    $normalized = preg_replace('/\D+/', '', $phone);
    if ($addCountryCode && !empty($normalized)) {
        $normalized = '90' . $normalized;
    }
    return $normalized;
}

// Session timeout'u 30 dakika olarak ayarla
session_set_cookie_params(1800);
session_start();

// CSRF token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// Edit token'ı al
$editToken = $_GET['token'] ?? null;
if (!$editToken) {
    $requestUri = $_SERVER['REQUEST_URI'];
    if (preg_match('#/edit/([a-zA-Z0-9]+)#', $requestUri, $matches)) {
        $editToken = $matches[1];
    }
}

// Edit token kontrolü
if (!$editToken) {
    http_response_code(404);
    echo '<h2>Geçersiz edit QR!</h2>';
    exit;
}

// QR token ile profil bilgilerini al
try {
    $profile = $userProfileManager->getProfileByEditToken($editToken);
    if (!$profile) {
        http_response_code(404);
        echo '<h2>Geçersiz veya silinmiş edit QR!</h2>';
        exit;
    }
    $editCode = $profile['edit_code'];
    $profileId = $profile['id'];
} catch (Exception $e) {
    log_edit_error('Profil arama hatası: ' . $e->getMessage());
    http_response_code(500);
    echo '<h2>Sistem hatası: Profil bilgileri alınamadı.</h2>';
    exit;
}

// Değişkenler
$showForm = true;
$loginError = false;
$loginErrorType = '';

// POST işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $submittedUrl = $_SERVER['REQUEST_URI'] ?? 'unknown';
    log_edit_error('Form post edildi. IP: ' . $submittedIp . ', URL: ' . $submittedUrl);
    log_edit_debug('POST verileri', [
        'post' => $_POST,
        'files' => isset($_FILES['photo']) ? [
            'name' => $_FILES['photo']['name'],
            'size' => $_FILES['photo']['size'],
            'error' => $_FILES['photo']['error']
        ] : 'no_file'
    ]);
    
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        log_edit_error('CSRF token hatası');
        http_response_code(403);
        echo '<p style="color:red">Güvenlik hatası: Geçersiz CSRF token.</p>';
        exit;
    }
    
    // Giriş işlemi
    if (isset($_POST['edit_code']) && isset($_POST['phone_check'])) {
        $inputCode = trim($_POST['edit_code']);
        $inputPhone = trim($_POST['phone_check']);
        
        // Profil bilgilerini al
        $profile = $userProfileManager->getProfileByEditToken($editToken);
        if (!$profile) {
            echo '<h2>Profil bulunamadı.</h2>';
            exit;
        }
        
        // Telefon numaralarını normalize et
        $inputPhoneNorm = normalizePhone($inputPhone, true);
        $profilePhoneNorm = normalizePhone($profile['phone']);
        
        log_edit_debug('Giriş denemesi', [
            'input_code' => $inputCode,
            'expected_code' => $editCode,
            'input_phone_norm' => $inputPhoneNorm,
            'profile_phone_norm' => $profilePhoneNorm
        ]);
        
        // Doğrulama
        $codeValid = ($inputCode === $editCode);
        $phoneValid = ($inputPhoneNorm === $profilePhoneNorm);
        
        if ($codeValid && $phoneValid) {
            log_edit_error('Şifre ve telefon doğru, oturum açılıyor');
            session_regenerate_id(true);
            $_SESSION['edit_auth_' . $editToken] = true;
            
            // Redirect
            $redirectUrl = '/kisisel_qr/edit/' . urlencode($editToken);
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            log_edit_error('Giriş başarısız');
            $loginError = true;
            if (!$codeValid && !$phoneValid) {
                $loginErrorType = 'both';
            } elseif (!$codeValid) {
                $loginErrorType = 'code';
            } else {
                $loginErrorType = 'phone';
            }
        }
    }
    
    // Profil güncelleme işlemi
    if (isset($_POST['save_profile'])) {
        log_edit_error('save_profile parametresi bulundu, güncelleme işlemi başlıyor');
        
        if (!($_SESSION['edit_auth_' . $editToken] ?? false)) {
            log_edit_error('Oturum doğrulaması başarısız');
            echo '<p style="color:red">Oturum doğrulaması başarısız. Lütfen tekrar giriş yapın.</p>';
            unset($_SESSION['edit_auth_' . $editToken]);
            exit;
        }
        
        log_edit_error('Profil güncelleme başlıyor. EditToken: ' . $editToken);
        
        try {
            // Mevcut profil bilgilerini al
            $profile = $userProfileManager->getProfileByEditToken($editToken);
            if (!$profile) {
                throw new Exception('Profil bulunamadı');
            }
            
            // POST verilerini sanitize et
            $postData = getSanitizedPostData();
            
            // Güncelleme verilerini hazırla
            $updateData = [
                'phone' => $postData['phone'],
                'country_code' => Utilities::sanitizeInput($_POST['country_code'] ?? '+90'),
                'bio' => $postData['bio'],
                'iban' => $postData['iban'],
                'blood_type' => $postData['blood_type'],
                'theme' => $postData['theme'],
                'social_links' => $postData['social_links']
            ];
            
            // Fotoğraf varsa ekle
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $updateData['photo'] = $_FILES['photo'];
            }
            
            // Debug için güncelleme verilerini logla
            log_edit_debug('Güncelleme verileri:', $updateData);
            
            // Profili güncelle
            $updateResult = $userProfileManager->updateProfile($editToken, $updateData);
            
            if ($updateResult) {
                log_edit_error('Profil başarıyla güncellendi. EditToken: ' . $editToken);
                $_SESSION['profile_update_success'] = true;
                $_SESSION['profile_update_message'] = 'Profiliniz başarıyla güncellendi! Değişiklikler anında yayına alındı.';
                $_SESSION['profile_update_type'] = 'success';
            } else {
                log_edit_error('Değişiklik yapılmadı. EditToken: ' . $editToken);
                $_SESSION['profile_update_message'] = 'Herhangi bir değişiklik yapılmadı veya aynı bilgiler girildi.';
                $_SESSION['profile_update_type'] = 'info';
            }
            
            // Redirect
            header('Location: /kisisel_qr/edit/' . urlencode($editToken));
            exit;
            
        } catch (Exception $e) {
            log_edit_error('Güncelleme hatası: ' . $e->getMessage());
            $_SESSION['profile_update_message'] = 'Profil güncellenirken bir hata oluştu: ' . $e->getMessage();
            $_SESSION['profile_update_type'] = 'danger';
            header('Location: /kisisel_qr/edit/' . urlencode($editToken));
            exit;
        }
    } else {
        // save_profile parametresi yok, hangi parametreler var kontrol et
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            log_edit_error('POST yapıldı ama save_profile parametresi yok!');
            log_edit_debug('Mevcut POST parametreleri:', array_keys($_POST));
        }
    }
}

// Profil düzenleme ekranı - sadece oturum doğrulaması varsa
if ($_SESSION['edit_auth_' . $editToken] ?? false) {
    $profile = $userProfileManager->getProfileByEditToken($editToken);
    if (!$profile) {
        echo '<h2>Profil bulunamadı.</h2>';
        exit;
    }
    
    // Başarı/hata mesajları
    $alertMessage = '';
    $alertType = '';
    
    if (isset($_SESSION['profile_update_message'])) {
        $alertMessage = $_SESSION['profile_update_message'];
        $alertType = $_SESSION['profile_update_type'] ?? 'info';
        unset($_SESSION['profile_update_message'], $_SESSION['profile_update_type'], $_SESSION['profile_update_success']);
    }
    
    // Profil fotoğrafı URL'sini hazırla
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
    
    // Sosyal medya linklerini hazırla
    $socialLinks = [];
    if (!empty($profile['social_links'])) {
        $decoded = json_decode($profile['social_links'], true);
        $socialLinks = is_array($decoded) ? $decoded : [];
    }
    ?>
    <script>
    // Sosyal medya verilerini JavaScript'e gönder
    window.existingSocialLinks = <?= json_encode($socialLinks, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <style>
    .text-purple {
        color: #9146ff !important;
    }
    </style>
    <div class="container py-5">
        <?php if ($alertMessage): ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert" id="profileAlert">
            <strong><?= $alertType === 'success' ? 'Başarılı!' : ($alertType === 'danger' ? 'Hata!' : 'Bilgi:') ?></strong> 
            <?= htmlspecialchars($alertMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
        setTimeout(function(){
            var alert = document.getElementById('profileAlert');
            if(alert) alert.classList.remove('show');
        }, 5000);
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
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ad Soyad *</label>
                                    <input type="text" class="form-control" 
                                           value="<?= htmlspecialchars($profile['name']) ?>" 
                                           disabled style="background:#f5f5f5;cursor:not-allowed;">
                                    <small class="form-text text-muted">Ad soyad değiştirilemez</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefon *</label>
                                    <div class="phone-input-container d-flex">
                                        <?php
                                        $phoneRaw = $profile['phone'] ?? '';
                                        $countryCode = '+90';
                                        $phoneNumber = '';
                                        
                                        if (preg_match('/^(\+\d{1,3})(\d{10,})$/', $phoneRaw, $matches)) {
                                            $countryCode = $matches[1];
                                            $phoneNumber = $matches[2];
                                        } elseif (preg_match('/^(\d{10,})$/', $phoneRaw)) {
                                            $phoneNumber = $phoneRaw;
                                        }
                                        ?>
                                        <select class="form-control country-dropdown me-2" name="country_code" 
                                                id="editCountryCode" style="max-width:110px;">
                                            <option value="+90" <?= $countryCode === '+90' ? 'selected' : '' ?>>🇹🇷 +90</option>
                                            <option value="+1" <?= $countryCode === '+1' ? 'selected' : '' ?>>🇺🇸 +1</option>
                                            <option value="+44" <?= $countryCode === '+44' ? 'selected' : '' ?>>🇬🇧 +44</option>
                                            <option value="+49" <?= $countryCode === '+49' ? 'selected' : '' ?>>🇩🇪 +49</option>
                                            <option value="+33" <?= $countryCode === '+33' ? 'selected' : '' ?>>🇫🇷 +33</option>
                                            <option value="+971" <?= $countryCode === '+971' ? 'selected' : '' ?>>🇦🇪 +971</option>
                                            <option value="+966" <?= $countryCode === '+966' ? 'selected' : '' ?>>🇸🇦 +966</option>
                                            <option value="+7" <?= $countryCode === '+7' ? 'selected' : '' ?>>🇷🇺 +7</option>
                                            <option value="+86" <?= $countryCode === '+86' ? 'selected' : '' ?>>🇨🇳 +86</option>
                                            <option value="+91" <?= $countryCode === '+91' ? 'selected' : '' ?>>🇮🇳 +91</option>
                                        </select>
                                        <input type="tel" class="form-control phone-number-input" name="phone" 
                                               id="editPhone" value="<?= htmlspecialchars($phoneNumber) ?>" 
                                               required placeholder="555 555 55 55" maxlength="20">
                                    </div>
                                    <small class="form-text text-muted">Telefon numaranızı ülke kodu ile birlikte giriniz</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Profil Fotoğrafı</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $photoUrl ?>" alt="Profil Fotoğrafı" id="profilePhotoPreview" 
                                         class="profile-photo" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" 
                                           class="form-control" style="max-width:250px;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kısa Yazı (Bio)</label>
                                <textarea class="form-control" name="bio" rows="2" 
                                          placeholder="Kendinizi tanıtın..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">IBAN</label>
                                    <input type="text" class="form-control" name="iban" 
                                           value="<?= htmlspecialchars($profile['iban'] ?? '') ?>" 
                                           placeholder="TR00 0000 0000 0000 0000 0000 00" maxlength="26">
                                    <small class="form-text text-muted">TR ile başlayan 26 haneli İban numarası</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kan Grubu</label>
                                    <select class="form-select" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', '0+', '0-'];
                                        foreach ($bloodTypes as $type) {
                                            $selected = ($profile['blood_type'] ?? '') === $type ? 'selected' : '';
                                            echo "<option value=\"$type\" $selected>$type</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tema Seçimi</label>
                                <select class="form-select" name="theme" id="editTheme">
                                    <?php
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
                                        'neon' => 'Neon Siber'
                                    ];
                                    
                                    foreach ($themes as $value => $label) {
                                        $selected = ($profile['theme'] ?? 'default') === $value ? 'selected' : '';
                                        echo "<option value=\"$value\" $selected>$label</option>";
                                    }
                                    ?>
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
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'instagram')">
                                                    <i class="fab fa-instagram text-danger"></i>
                                                    <span class="d-block small">Instagram</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'x')">
                                                    <i class="fab fa-twitter" style="color: #1da1f2;"></i>
                                                    <span class="d-block small">X</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'linkedin')">
                                                    <i class="fab fa-linkedin text-primary"></i>
                                                    <span class="d-block small">LinkedIn</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'facebook')">
                                                    <i class="fab fa-facebook text-primary"></i>
                                                    <span class="d-block small">Facebook</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'youtube')">
                                                    <i class="fab fa-youtube text-danger"></i>
                                                    <span class="d-block small">YouTube</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'tiktok')">
                                                    <i class="fab fa-tiktok text-dark"></i>
                                                    <span class="d-block small">TikTok</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'whatsapp')">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                    <span class="d-block small">WhatsApp</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'website')">
                                                    <i class="fas fa-globe text-info"></i>
                                                    <span class="d-block small">Website</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'snapchat')">
                                                    <i class="fab fa-snapchat text-warning"></i>
                                                    <span class="d-block small">Snapchat</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'discord')">
                                                    <i class="fab fa-discord text-primary"></i>
                                                    <span class="d-block small">Discord</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'telegram')">
                                                    <i class="fab fa-telegram text-info"></i>
                                                    <span class="d-block small">Telegram</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" onclick="addSocialLink('socialLinksContainer', 'twitch')">
                                                    <i class="fab fa-twitch text-purple"></i>
                                                    <span class="d-block small">Twitch</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="socialLinksContainer"></div>
                            </div>
                            
                            <input type="hidden" name="social_links" id="socialLinksInput">
                            <button type="submit" name="save_profile" class="btn btn-primary w-100" 
                                    style="font-size:1.1rem;" id="saveProfileBtn">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.0/imask.min.js"></script>
    <script src="/kisisel_qr/assets/js/profile-manager.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sosyal medya verilerini yükle
        if (typeof window.existingSocialLinks !== 'undefined' && window.existingSocialLinks) {
            // var existingSocialLinks olduğunu kontrol et, varsa sosyal medya linklerini yükle
            Object.entries(window.existingSocialLinks).forEach(([platform, url]) => {
                if (url && url.trim()) {
                    addSocialLink('socialLinksContainer', platform, url);
                }
            });
        }
        
        // Telefon input mask
        var editPhone = document.getElementById('editPhone');
        if(editPhone && window.IMask){
            IMask(editPhone, { mask: '000 000 00 00' });
        }
        
        // Form submit handling
        var editForm = document.getElementById('editProfileForm');
        var saveBtn = document.getElementById('saveProfileBtn');
        if(editForm && saveBtn){
            editForm.addEventListener('submit', function(e){
                // Sosyal medya verilerini hidden input'a yaz - kullanıcı adlarını URL'e dönüştür
                const socialLinks = {};
                const socialInputs = document.querySelectorAll('#socialLinksContainer .input-group');
                socialInputs.forEach(group => {
                    const platform = group.querySelector('select').value;
                    const urlInput = group.querySelector('input[type="url"], input[type="text"], input[type="tel"]');
                    if (urlInput && urlInput.value.trim()) {
                        const userInput = urlInput.value.trim();
                        // Platform tipine göre tam URL oluştur
                        let fullUrl = userInput;
                        if (platform === 'instagram') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://instagram.com/${userInput}`;
                        } else if (platform === 'x') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://twitter.com/${userInput}`;
                        } else if (platform === 'linkedin') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://linkedin.com/in/${userInput}`;
                        } else if (platform === 'facebook') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://facebook.com/${userInput}`;
                        } else if (platform === 'youtube') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://youtube.com/@${userInput}`;
                        } else if (platform === 'tiktok') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://tiktok.com/@${userInput}`;
                        } else if (platform === 'snapchat') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://snapchat.com/add/${userInput}`;
                        } else if (platform === 'discord') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://discord.gg/${userInput}`;
                        } else if (platform === 'telegram') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://t.me/${userInput}`;
                        } else if (platform === 'twitch') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://twitch.tv/${userInput}`;
                        } else if (platform === 'whatsapp') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://wa.me/${userInput}`;
                        } else if (platform === 'website') {
                            fullUrl = userInput.startsWith('http') ? userInput : `https://${userInput}`;
                        }
                        socialLinks[platform] = fullUrl;
                    }
                });
                document.getElementById('socialLinksInput').value = JSON.stringify(socialLinks);
                
                // save_profile parametresini ekle (eğer yoksa)
                if (!editForm.querySelector('input[name="save_profile"]')) {
                    const saveProfileInput = document.createElement('input');
                    saveProfileInput.type = 'hidden';
                    saveProfileInput.name = 'save_profile';
                    saveProfileInput.value = '1';
                    editForm.appendChild(saveProfileInput);
                }
                
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Kaydediliyor...';
            });
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // CSRF token'ı tüm AJAX istekleri için otomatik ekle
        var csrfToken = '<?= $csrfToken ?>';
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }

        // Fotoğraf önizleme
        document.getElementById('editPhotoInput')?.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePhotoPreview').src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Form değişikliklerini izle
        let formChanged = false;
        const editForm = document.getElementById('editProfileForm');
        
        if (editForm) {
            const formElements = editForm.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                element.addEventListener('change', () => {
                    formChanged = true;
                });
            });

            // Sayfa yenileme veya kapatma öncesi uyarı
            window.addEventListener('beforeunload', (e) => {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Form gönderildiğinde uyarıyı devre dışı bırak
            editForm.addEventListener('submit', () => {
                formChanged = false;
            });
        }
    });
    </script>
    <?php
    renderPageFooter();
    exit;
}

// Şifre giriş ekranı
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
            
            <?php if ($loginError): ?>
                <div class="alert alert-danger">
                    <?php
                    switch ($loginErrorType) {
                        case 'both':
                            echo 'Şifre ve telefon numarası hatalı!';
                            break;
                        case 'code':
                            echo 'Şifre hatalı!';
                            break;
                        case 'phone':
                            echo 'Telefon numarası hatalı!';
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="mb-3">
                    <label class="form-label">Edit Şifresi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="edit_code" id="editCodeInput" class="form-control" 
                               placeholder="Şifrenizi girin" required autofocus>
                        <button type="button" class="btn btn-outline-secondary" tabindex="-1" id="toggleEditCode">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Profil oluştururken kullandığınız telefon numarası</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <span class="input-group-text" style="min-width:48px;">+90</span>
                        <input type="tel" name="phone_check" class="form-control" 
                               placeholder="5xx xxx xx xx" maxlength="13" required id="loginPhoneInput">
                    </div>
                    <small class="form-text text-muted">Telefon numaranızın başında +90 sabit, sadece numarayı giriniz.</small>
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
