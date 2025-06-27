<?php
// Session ayarları ve güvenlik önce yüklensin
require_once __DIR__ . '/../config/database.php';

// Session'ı güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charset ayarı
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/utilities.php';
require_once __DIR__ . '/../includes/ImageOptimizer.php';

// Oturum kontrolü
Utilities::requireLogin();

$db = Database::getInstance();
$connection = $db->getConnection();
$imageOptimizer = new ImageOptimizer();

// Charset kontrolü
$connection->set_charset("utf8mb4");

// Profilleri al
$query = "SELECT * FROM profiles ORDER BY created_at DESC";
$result = $connection->query($query);
$profiles = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $profiles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Yönetimi - Kişisel QR Sistemi</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr_canli/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr_canli/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr_canli/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr_canli/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="/kisisel_qr_canli/assets/css/dashboard.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/profile-themes.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Responsive image styles for admin panel */
        .profile-photo-admin {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .profile-photo-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .profile-photo-edit {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        /* DataTable responsive adjustments */
        @media (max-width: 768px) {
            .profile-photo-admin {
                width: 40px;
                height: 40px;
            }
        }
        
        /* Loading state for images */
        .profile-image-loading {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
    </style>
    <?php $csrf_token = Utilities::generateCsrfToken(); ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'templates/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Profil Yönetimi</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProfileModal">
                            <i class="fas fa-plus me-2"></i>Yeni Profil
                        </button>
                    </div>

                    <!-- Profil Listesi -->
                    <div class="card">
                        <div class="card-body">
                            <table id="profilesTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Profil Fotoğrafı</th>
                                        <th>ID</th>
                                        <th>İsim</th>
                                        <th>Oluşturulma Tarihi</th>
                                        <th>Son Güncelleme</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profiles as $profile): ?>                                    <tr>
                                        <td>
                                            <?php 
                                            $photoData = null;
                                            if (!empty($profile['photo_data'])) {
                                                $photoData = json_decode($profile['photo_data'], true);
                                            }
                                            
                                            if ($photoData && isset($photoData['filename'])) {
                                                $baseName = pathinfo($photoData['filename'], PATHINFO_FILENAME);
                                                echo '<picture>';
                                                echo '<source srcset="/kisisel_qr_canli/public/uploads/profiles/thumb/' . $baseName . '.webp" type="image/webp">';
                                                echo '<img src="/kisisel_qr_canli/public/uploads/profiles/thumb/' . $photoData['filename'] . '" ';
                                                echo 'alt="' . htmlspecialchars($profile['name']) . ' profil fotoğrafı" ';
                                                echo 'class="profile-photo-admin" loading="lazy">';
                                                echo '</picture>';
                                            } else {
                                                echo '<img src="/kisisel_qr_canli/assets/images/default-profile.svg" alt="Varsayılan profil" class="profile-photo-admin" loading="lazy">';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $profile['id']; ?></td>
                                        <td><?php echo htmlspecialchars($profile['name']); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($profile['created_at'])); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($profile['updated_at'])); ?></td>
                                        <td>
                                            <?php
                                            // İlgili QR kodlarını getir
                                            $qrQuery = "SELECT id, created_at FROM qr_codes WHERE profile_id = ?";
                                            $qrStmt = $connection->prepare($qrQuery);
                                            $qrStmt->bind_param("i", $profile['id']);
                                            $qrStmt->execute();
                                            $qrResult = $qrStmt->get_result();
                                            ?>
                                            <button class="btn btn-sm btn-primary" onclick="editProfile(<?php echo $profile['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="viewProfile(<?php echo $profile['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProfile(<?php echo $profile['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="createQRForProfile(<?php echo $profile['id']; ?>)">
                                                        <i class="fas fa-plus"></i> Yeni QR Oluştur
                                                    </a></li>
                                                    <?php while ($qr = $qrResult->fetch_assoc()): ?>
                                                    <li class="d-flex align-items-center justify-content-between px-2">
                                                        <a class="dropdown-item flex-grow-1" href="/kisisel_qr_canli/public/qr_codes/<?php echo $qr['id']; ?>.png" download>
                                                            <i class="fas fa-download"></i> QR #<?php echo substr($qr['id'], 0, 8); ?>
                                                        </a>
                                                        <span class="text-muted small ms-2">Oluşturulma: <?php echo date('d.m.Y H:i', strtotime($qr['created_at'])); ?></span>
                                                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/kisisel_qr_canli/public/qr_codes/' . $qr['id']; ?>.png')" title="Kopyala"><i class="fas fa-copy"></i></button>
                                                        <button class="btn btn-sm btn-danger ms-2" onclick="deleteQR('<?php echo $qr['id']; ?>', <?php echo $profile['id']; ?>)"><i class="fas fa-trash"></i></button>
                                                    </li>
                                                    <?php endwhile; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yeni Profil Oluşturma Modal -->
    <div class="modal fade" id="createProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Profil Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">                    <form id="createProfileForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">İsim</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>                        <div class="mb-3">
                            <label for="bio" class="form-label">Biyografi</label>
                            <textarea class="form-control" id="bio" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" id="phone" placeholder="+90 5XX XXX XX XX">
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Profil Fotoğrafı</label>
                            <input type="file" class="form-control" id="photo" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="socialLinks" class="form-label">Sosyal Medya Bağlantıları</label>
                            <div id="socialLinksContainer">
                                <!-- Sosyal medya bağlantıları buraya dinamik olarak eklenecek -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSocialLink()">
                                <i class="fas fa-plus"></i> Bağlantı Ekle
                            </button>
                        </div>
                        <div class="mb-3">
                            <label for="theme" class="form-label">Profil Teması</label>
                            <select class="form-select" id="theme" name="theme">
                                <?php
                                $themesQuery = "SELECT * FROM themes WHERE is_active = 1 ORDER BY theme_title";
                                $themesResult = $connection->query($themesQuery);
                                if ($themesResult) {
                                    while ($theme = $themesResult->fetch_assoc()):
                                        // Charset sorunu için özel işlem
                                        $themeTitle = mb_convert_encoding($theme['theme_title'], 'UTF-8', 'UTF-8');
                                ?>
                                <option value="<?php echo htmlspecialchars($theme['theme_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-background-color="<?php echo htmlspecialchars($theme['background_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-text-color="<?php echo htmlspecialchars($theme['text_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-accent-color="<?php echo htmlspecialchars($theme['accent_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-card-background="<?php echo htmlspecialchars($theme['card_background'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-font-family="<?php echo htmlspecialchars($theme['font_family'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-button-style="<?php echo htmlspecialchars($theme['button_style'], ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo (isset($profile['theme']) && $profile['theme'] === $theme['theme_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($themeTitle, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php 
                                    endwhile; 
                                }
                                ?>
                            </select>
                        </div>
                          <div class="mb-3">
                            <label class="form-label">Tema Önizleme</label>
                            <div id="theme-preview" class="p-4 rounded" style="min-height: 200px;">
                                <div id="preview-card" class="card shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3">
                                            <h5 class="card-title fs-4 mb-3">Tema Önizleme</h5>
                                            <p class="card-text mb-4">Bu önizleme seçilen temanın profilinizde nasıl görüneceğini gösterir.</p>
                                        </div>
                                        <button id="preview-button" class="btn theme-button">Örnek Sosyal Medya Butonu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="createProfile()">Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Düzenleme Modalı -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profili Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        <input type="hidden" id="edit_current_photo_url" name="current_photo_url">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">İsim</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_bio" class="form-label">Biyografi</label>
                            <textarea class="form-control" id="edit_bio" name="bio" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_photo" class="form-label">Profil Fotoğrafı</label>
                            <input type="file" class="form-control" id="edit_photo" name="photo" accept="image/*">
                            <div id="edit_photo_preview_container" class="mt-2" style="display:none;">
                                <img id="edit_photo_preview" src="" alt="Profil fotoğrafı önizleme" class="img-thumbnail" style="max-width:120px;">
                            </div>
                            <div id="edit_current_photo_container" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_theme" class="form-label">Profil Teması</label>
                            <select class="form-select" id="edit_theme" name="theme">
                                <?php
                                $themesQuery = "SELECT * FROM themes WHERE is_active = 1 ORDER BY theme_title";
                                $themesResult = $connection->query($themesQuery);
                                if ($themesResult) {
                                    while ($theme = $themesResult->fetch_assoc()): 
                                        // Charset sorunu için özel işlem
                                        $themeTitle = mb_convert_encoding($theme['theme_title'], 'UTF-8', 'UTF-8');
                                ?>
                                    <option value="<?php echo htmlspecialchars($theme['theme_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($themeTitle, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php 
                                    endwhile; 
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_socialLinks" class="form-label">Sosyal Medya Bağlantıları</label>
                            <div id="edit_socialLinksContainer"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSocialLink('edit_socialLinksContainer')">
                                <i class="fas fa-plus"></i> Bağlantı Ekle
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="updateProfile()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Önizleme Modalı -->
    <div class="modal fade" id="viewProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profil Önizleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="viewProfileContent">
                        <div class="text-center mb-3">
                            <div id="view_photo_container"></div>
                        </div>
                        <h5 id="view_name"></h5>
                        <p id="view_bio"></p>
                        <p><strong>Telefon:</strong> <span id="view_phone"></span></p>
                        <p><strong>Tema:</strong> <span id="view_theme"></span></p>
                        <div id="view_socialLinks"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Toast for notifications -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
  <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="mainToastBody">
        <!-- Mesaj buraya gelecek -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
    </div>
  </div>
</div>

    <!-- Scripts -->    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="/kisisel_qr_canli/assets/js/image-cleanup.js"></script>
    <script src="/kisisel_qr_canli/assets/js/profile-manager.js"></script>
    <script>
    // Profil düzenleme modalını aç ve verileri doldur
    function editProfile(id) {
        $.ajax({
            url: '/kisisel_qr_canli/admin/api/profile.php',
            method: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#edit_id').val(res.profile.id);
                    $('#edit_name').val(res.profile.name);
                    $('#edit_bio').val(res.profile.bio);
                    $('#edit_phone').val(res.profile.phone);
                    $('#edit_theme').val(res.profile.theme);
                    $('#edit_current_photo_url').val(res.profile.photo_url);
                    
                    // Mevcut foto gösterimi (photo_data kullanarak)
                    const photoContainer = $('#edit_current_photo_container');
                    if (res.profile.photo_data) {
                        try {
                            const photoData = JSON.parse(res.profile.photo_data);
                            if (photoData.filename) {
                                photoContainer.html(`
                                    <div class="d-flex align-items-center">
                                        <picture>
                                            <source srcset="/kisisel_qr_canli/public/uploads/profiles/thumb/${photoData.filename.replace(/\.[^/.]+$/, '')}.webp" type="image/webp">
                                            <img src="/kisisel_qr_canli/public/uploads/profiles/thumb/${photoData.filename}" 
                                                 alt="Mevcut profil fotoğrafı" 
                                                 class="img-thumbnail profile-photo-edit me-2" 
                                                 loading="lazy">
                                        </picture>
                                        <small class="text-muted">Mevcut fotoğraf</small>
                                    </div>
                                `);
                            }
                        } catch(e) {
                            console.error('Photo data parse error:', e);
                            photoContainer.empty();
                        }
                    } else if (res.profile.photo_url) {
                        // Eski format için fallback
                        let photoUrl = res.profile.photo_url;
                        if (photoUrl.startsWith('/kisisel_qr_canli/kisisel_qr/public')) {
                            photoUrl = photoUrl.replace('/kisisel_qr_canli/kisisel_qr/public', '/kisisel_qr_canli/public');
                        } else if (photoUrl.startsWith('/kisisel_qr/public')) {
                            photoUrl = photoUrl.replace('/kisisel_qr/public', '/kisisel_qr_canli/public');
                        }
                        photoContainer.html(`
                            <div class="d-flex align-items-center">
                                <img src="${photoUrl}" alt="Mevcut profil fotoğrafı" 
                                     class="img-thumbnail profile-photo-edit me-2">
                                <small class="text-muted">Mevcut fotoğraf</small>
                            </div>
                        `);
                    } else {
                        photoContainer.empty();
                    }
                    
                    // Sosyal medya linklerini doldur
                    let links = [];
                    try { links = JSON.parse(res.profile.social_links); } catch(e) {}
                    // Dizi değilse objeden diziye çevir
                    if (!Array.isArray(links) && typeof links === 'object' && links !== null) {
                        links = Object.entries(links).map(([platform, url]) => ({ platform, url }));
                    }
                    const container = $('#edit_socialLinksContainer');
                    container.html('');
                    if (Array.isArray(links) && links.length > 0) {
                        links.forEach(link => addSocialLink('edit_socialLinksContainer', link.platform, link.url));
                    }
                    $('#editProfileModal').modal('show');
                } else {
                    alert('Profil verileri alınamadı!');
                }
            },
            error: function() { alert('Sunucu hatası!'); }
        });
    }

    // Profil güncelleme fonksiyonu
    function updateProfile() {
        var form = document.getElementById('editProfileForm');
        var formData = new FormData(form);
        // Sosyal medya linklerini topla
        let links = [];
        $('#edit_socialLinksContainer .input-group').each(function() {
            let platform = $(this).find('select').val();
            let url = $(this).find('input').val();
            if (platform && url) links.push({ platform, url });
        });
        formData.append('social_links', JSON.stringify(links));
        formData.append('action', 'update');
        
        $.ajax({
            url: '/kisisel_qr_canli/admin/api/profile.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showToast('Profil başarıyla güncellendi!', 'success');
                    
                    // Eğer yeni fotoğraf yüklendiyse, lazy loading'i yenile
                    if (res.photo_data && window.imageCleanupUtility) {
                        window.imageCleanupUtility.refreshLazyImages();
                    }
                    
                    // Modal'ı kapat ve sayfayı yenile
                    $('#editProfileModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(res.message || 'Güncelleme başarısız!', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update error:', error);
                showToast('Sunucu hatası: ' + error, 'danger');
            }
        });
    }

    function viewProfile(id) {
        $.ajax({
            url: '/kisisel_qr_canli/admin/api/profile.php',
            method: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#view_name').text(res.profile.name);
                    $('#view_bio').text(res.profile.bio);
                    $('#view_phone').text(res.profile.phone);
                    $('#view_theme').text(res.profile.theme);
                    
                    // Profil fotoğrafı gösterimi (photo_data kullanarak)
                    const photoContainer = $('#view_photo_container');
                    if (res.profile.photo_data) {
                        try {
                            const photoData = JSON.parse(res.profile.photo_data);
                            if (photoData.filename) {
                                photoContainer.html(`
                                    <picture>
                                        <source srcset="/kisisel_qr_canli/public/uploads/profiles/medium/${photoData.filename.replace(/\.[^/.]+$/, '')}.webp" type="image/webp">
                                        <img src="/kisisel_qr_canli/public/uploads/profiles/medium/${photoData.filename}" 
                                             alt="${res.profile.name} profil fotoğrafı" 
                                             class="img-thumbnail profile-photo-preview" 
                                             loading="lazy">
                                    </picture>
                                `);
                            } else {
                                photoContainer.html('<img src="/kisisel_qr_canli/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">');
                            }
                        } catch(e) {
                            console.error('Photo data parse error:', e);
                            photoContainer.html('<img src="/kisisel_qr_canli/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">');
                        }
                    } else if (res.profile.photo_url) {
                        // Eski format için fallback
                        let photoUrl = res.profile.photo_url;
                        if (photoUrl.startsWith('/kisisel_qr/public')) {
                            photoUrl = photoUrl.replace('/kisisel_qr/public', '/kisisel_qr_canli/public');
                        } else if (photoUrl.startsWith('/kisisel_qr_canli/kisisel_qr/public')) {
                            photoUrl = photoUrl.replace('/kisisel_qr_canli/kisisel_qr/public', '/kisisel_qr_canli/public');
                        }
                        photoContainer.html(`<img src="${photoUrl}" alt="${res.profile.name} profil fotoğrafı" class="img-thumbnail profile-photo-preview">`);
                    } else {
                        photoContainer.html('<img src="/kisisel_qr_canli/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">');
                    }
                    
                    // Sosyal medya linkleri
                    let links = [];
                    try { links = JSON.parse(res.profile.social_links); } catch(e) {}
                    if (!Array.isArray(links) && typeof links === 'object' && links !== null) {
                        links = Object.entries(links).map(([platform, url]) => ({ platform, url }));
                    }
                    let html = '';
                    if (Array.isArray(links) && links.length > 0) {
                        html += '<div class="mb-2"><strong>Sosyal Medya:</strong></div>';
                        links.forEach(link => {
                            html += `<div><a href="${link.url}" target="_blank"><i class="fab fa-${link.platform}"></i> ${link.platform.charAt(0).toUpperCase() + link.platform.slice(1)}</a></div>`;
                        });
                    }
                    $('#view_socialLinks').html(html);
                    $('#viewProfileModal').modal('show');
                } else {
                    alert('Profil verileri alınamadı!');
                }
            },
            error: function() { alert('Sunucu hatası!'); }
        });
    }

    function deleteProfile(profileId) {
        if (confirm('Bu profili silmek istediğinize emin misiniz?')) {
            var formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', profileId);
            $.ajax({
                url: '/kisisel_qr_canli/admin/api/profile.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        alert('Profil başarıyla silindi!');
                        location.reload();
                    } else {
                        alert('Profil silinirken bir hata oluştu: ' + (res.message || ''));
                    }
                },
                error: function() { alert('Sunucu hatası!'); }
            });
        }
    }

    // QR kod silme fonksiyonu
    function deleteQR(qrId, profileId) {
        if (confirm('Bu QR kodunu silmek istediğinize emin misiniz?')) {
            fetch('/kisisel_qr_canli/admin/api/qr.php?action=delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${encodeURIComponent(qrId)}&csrf_token=${encodeURIComponent(document.querySelector('meta[name=csrf-token]').getAttribute('content'))}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('QR kod başarıyla silindi!');
                    location.reload();
                } else {
                    alert('QR kod silinirken bir hata oluştu: ' + (data.message || ''));
                }
            })
            .catch(() => alert('Sunucu hatası!'));
        }
    }

    function showToast(message, type = 'primary') {
        const toastEl = document.getElementById('mainToast');
        const toastBody = document.getElementById('mainToastBody');
        toastBody.textContent = message;
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    $('#edit_photo').on('change', function(e) {
        const [file] = this.files;
        const previewContainer = $('#edit_photo_preview_container');
        const previewImg = $('#edit_photo_preview');
        
        if (file) {
            // Dosya boyutu kontrolü (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Dosya boyutu 5MB\'dan büyük olamaz!', 'danger');
                $(this).val('');
                previewContainer.hide();
                return;
            }
            
            // Dosya türü kontrolü
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Sadece JPEG, PNG, GIF ve WebP dosyaları kabul edilir!', 'danger');
                $(this).val('');
                previewContainer.hide();
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(ev) {
                previewImg.attr('src', ev.target.result);
                previewContainer.show();
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.hide();
        }
    });

    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Bağlantı kopyalandı!', 'success');
            }, function() {
                showToast('Kopyalama başarısız!', 'danger');
            });
        } else {
            // Eski tarayıcılar için fallback
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            try {
                document.execCommand('copy');
                showToast('Bağlantı kopyalandı!', 'success');
            } catch (err) {
                showToast('Kopyalama başarısız!', 'danger');
            }
            document.body.removeChild(tempInput);
        }
    }
    </script>
</body>
</html>
