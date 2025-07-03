<?php
// Session ayarları ve güvenlik önce yüklensin
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';

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
    <link rel="icon" type="image/svg+xml" href="<?= getBasePath() ?>/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= getBasePath() ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= getBasePath() ?>/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= getBasePath() ?>/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="<?= getBasePath() ?>/assets/css/dashboard.css" rel="stylesheet">
    <link href="<?= getBasePath() ?>/assets/css/profile-themes.css" rel="stylesheet">
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
        
        /* IBAN display formatting */
        .iban-display {
            font-family: 'Courier New', monospace !important;
            font-weight: 600 !important;
            letter-spacing: 1px !important;
            text-align: left !important;
            display: inline-block !important;
            background: #f8f9fa !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            border: 1px solid #dee2e6 !important;
            direction: ltr !important;
        }
        
        /* Admin Panel İnfo Kartları */
        .admin-info-card {
            margin-bottom: 1rem;
        }
        
        .admin-info-card .card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .admin-info-card .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        .admin-info-card .card-body {
            padding: 1.25rem;
        }
        
        .admin-info-card .icon-container {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
        }
        
        .admin-info-card .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .admin-info-card .info-value {
            line-height: 1.2;
            word-break: break-all;
        }
        
        /* İban özel formatı */
        .iban-card .info-value {
            font-family: 'Segoe UI', 'Roboto', monospace;
            font-size: 0.95rem;
            letter-spacing: 0.8px;
            word-spacing: 3px;
        }
        
        /* Kan grubu özel formatı */
        .blood-type-card .info-value {
            font-size: 1.5rem;
            font-weight: 800;
            text-align: center;
        }
        
        /* Responsive tasarım */
        @media (max-width: 768px) {
            .admin-info-card .card-body {
                padding: 1rem;
            }
            
            .admin-info-card .icon-container {
                width: 40px;
                height: 40px;
            }
            
            .admin-info-card .icon-container i {
                font-size: 1.2rem !important;
            }
            
            .iban-card .info-value {
                font-size: 0.85rem;
                letter-spacing: 0.5px;
                word-spacing: 2px;
            }
            
            .blood-type-card .info-value {
                font-size: 1.3rem;
            }
        }
        
        /* Modern Social Media Platform Buttons */
        .social-platforms-grid .social-platform-btn {
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid #e9ecef;
            transition: all 0.2s;
            position: relative;
        }
        
        .social-platforms-grid .social-platform-btn:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.15);
        }
        
        .social-platforms-grid .social-platform-btn i {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }
        
        .social-platforms-grid .social-platform-btn.selected {
            border-color: #28a745;
            background-color: #d4edda;
            color: #155724;
        }
        
        .social-platforms-grid .social-platform-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Selected Social Media Items */
        .selected-social-medias .social-media-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .selected-social-medias .social-media-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .selected-social-medias .platform-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .selected-social-medias .platform-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .selected-social-medias .platform-name {
            font-weight: 600;
            color: #495057;
        }
        
        .selected-social-medias .remove-platform {
            margin-left: auto;
            border: none;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        
        .selected-social-medias .remove-platform:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .selected-social-medias .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.2s;
        }
        
        .selected-social-medias .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }
        
        /* WhatsApp özel input stilleri */
        .whatsapp-phone-input.is-valid {
            border-color: #25d366 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2325d366' d='m2.3 6.73.19-.19-.25-.17c-.16-.12-.29-.24-.41-.4-.12-.16-.22-.33-.3-.52-.08-.19-.13-.39-.13-.6s.05-.41.13-.6c.08-.19.18-.36.3-.52.12-.16.25-.28.41-.4l.25-.17-.19-.19c-.22-.22-.47-.42-.75-.59-.28-.17-.58-.31-.9-.42s-.65-.16-1-.16c-.69 0-1.33.13-1.92.4s-1.11.63-1.56 1.08c-.45.45-.81.97-1.08 1.56s-.4 1.23-.4 1.92c0 .35.05.68.16 1s.25.62.42.9c.17.28.37.53.59.75l.19.19.17-.25c.12-.16.24-.29.4-.41.16-.12.33-.22.52-.3.19-.08.39-.13.6-.13s.41.05.6.13c.19.08.36.18.52.3.16.12.28.25.41.4l.25.17z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .whatsapp-phone-input.is-invalid {
            border-color: #dc3545 !important;
        }
        
        /* Platform specific colors */
        .platform-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color: white; }
        .platform-x { background: #1da1f2; color: white; }
        .platform-linkedin { background: #0077b5; color: white; }
        .platform-facebook { background: #1877f2; color: white; }
        .platform-youtube { background: #ff0000; color: white; }
        .platform-tiktok { background: #000000; color: white; }
        .platform-whatsapp { background: #25d366; color: white; }
        .platform-website { background: #17a2b8; color: white; }
        .platform-snapchat { background: #fffc00; color: #000; }
        .platform-discord { background: #5865f2; color: white; }
        .platform-telegram { background: #0088cc; color: white; }
        .platform-twitch { background: #9146ff; color: white; }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .social-platforms-grid .social-platform-btn {
                height: 70px;
            }
            
            .social-platforms-grid .social-platform-btn i {
                font-size: 1.3rem;
            }
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
                                            if (!empty($profile['photo_data']) && !$profile['photo_hidden']) {
                                                $photoData = json_decode($profile['photo_data'], true);
                                            }
                                            
                                            if ($photoData && isset($photoData['filename'])) {
                                                $baseName = pathinfo($photoData['filename'], PATHINFO_FILENAME);
                                                echo '<picture>';
                                                echo '<source srcset="' . getBasePath() . '/public/uploads/profiles/thumb/' . $baseName . '.webp" type="image/webp">';
                                                echo '<img src="' . getBasePath() . '/public/uploads/profiles/thumb/' . $photoData['filename'] . '" ';
                                                echo 'alt="' . htmlspecialchars($profile['name']) . ' profil fotoğrafı" ';
                                                echo 'class="profile-photo-admin" loading="lazy">';
                                                echo '</picture>';
                                            } else {
                                                echo '<img src="' . getBasePath() . '/assets/images/default-profile.svg" alt="Varsayılan profil" class="profile-photo-admin" loading="lazy">';
                                                if ($profile['photo_hidden']) {
                                                    echo '<small class="text-muted d-block">Fotoğraf Gizli</small>';
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $profile['id']; ?></td>
                                        <td><?php echo htmlspecialchars($profile['name']); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($profile['created_at'])); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($profile['updated_at'])); ?></td>
                                        <td>
                                            <?php
                                            // QR Pool'dan profil için atanmış QR kodlarını getir
                                            $qrPoolQuery = "SELECT pool_id, qr_code_id, edit_token, edit_code, status, created_at, assigned_at 
                                                          FROM qr_pool WHERE profile_id = ? AND status IN ('assigned', 'delivered') 
                                                          ORDER BY assigned_at DESC";
                                            $qrPoolStmt = $connection->prepare($qrPoolQuery);
                                            $qrPoolStmt->bind_param("i", $profile['id']);
                                            $qrPoolStmt->execute();
                                            $qrPoolResult = $qrPoolStmt->get_result();
                                            ?>
                                            <button class="btn btn-sm btn-primary" onclick="editProfile(<?php echo $profile['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php
                                            // Profil için aktif QR kodunu al
                                            $qrQuery = "SELECT id FROM qr_codes WHERE profile_id = ? ORDER BY created_at DESC LIMIT 1";
                                            $qrStmt = $connection->prepare($qrQuery);
                                            $qrStmt->bind_param("i", $profile['id']);
                                            $qrStmt->execute();
                                            $qrResult = $qrStmt->get_result();
                                            $qrRow = $qrResult->fetch_assoc();
                                            
                                            if ($qrRow): ?>
                                            <a href="<?= getBasePath() ?>/profile.php?qr_id=<?= htmlspecialchars($qrRow['id']) ?>"
                                               class="btn btn-sm btn-info"
                                               target="_blank"
                                               title="Profili yeni sekmede görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php else: ?>
                                            <button class="btn btn-sm btn-info" onclick="viewProfile(<?php echo $profile['id']; ?>)" title="Profil önizlemesi">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProfile(<?php echo $profile['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php 
                                                    // QR Pool'dan profil için atanmış QR kodlarını tekrar getir (dropdown için)
                                                    $qrPoolDropdownQuery = "SELECT pool_id, qr_code_id, edit_token, edit_code, status, created_at, assigned_at 
                                                                          FROM qr_pool WHERE profile_id = ? AND status IN ('assigned', 'delivered') 
                                                                          ORDER BY assigned_at DESC";
                                                    $qrPoolDropdownStmt = $connection->prepare($qrPoolDropdownQuery);
                                                    $qrPoolDropdownStmt->bind_param("i", $profile['id']);
                                                    $qrPoolDropdownStmt->execute();
                                                    $qrPoolDropdownResult = $qrPoolDropdownStmt->get_result();
                                                    
                                                    $hasAssignedQR = ($qrPoolDropdownResult->num_rows > 0);
                                                    while ($qrPool = $qrPoolDropdownResult->fetch_assoc()): 
                                                    ?>
                                                    <li class="dropdown-header">
                                                        <small class="text-muted">Atanmış QR: <?= htmlspecialchars($qrPool['pool_id']) ?></small>
                                                    </li>
                                                    <li><a class="dropdown-item" href="<?= getBasePath() ?>/public/qr_codes/<?php echo $qrPool['qr_code_id']; ?>.png" download>
                                                        <i class="fas fa-download"></i> QR İndir (<?= htmlspecialchars($qrPool['pool_id']) ?>)
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="<?= getBasePath() ?>/profile.php?qr_id=<?= htmlspecialchars($qrPool['qr_code_id']) ?>" target="_blank">
                                                        <i class="fas fa-external-link-alt"></i> QR ile Profili Aç
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="<?= getBasePath() ?>/edit/<?= htmlspecialchars($qrPool['edit_token']) ?>" target="_blank">
                                                        <i class="fas fa-edit"></i> Edit Sayfası
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li class="px-3">
                                                        <small class="text-muted">
                                                            <strong>Edit Kodu:</strong> <?= htmlspecialchars($qrPool['edit_code']) ?><br>
                                                            <strong>Status:</strong> 
                                                            <span class="badge bg-<?= $qrPool['status'] === 'assigned' ? 'warning' : 'success' ?>">
                                                                <?= ucfirst($qrPool['status']) ?>
                                                            </span><br>
                                                            <strong>Atanma:</strong> <?= date('d.m.Y H:i', strtotime($qrPool['assigned_at'])) ?>
                                                        </small>
                                                    </li>
                                                    <?php endwhile; ?>
                                                    
                                                    <?php if (!$hasAssignedQR): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="createQRForProfile(<?php echo $profile['id']; ?>)">
                                                        <i class="fas fa-plus"></i> Yeni QR Oluştur
                                                    </a></li>
                                                    <?php else: ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-primary" href="#" onclick="createQRForProfile(<?php echo $profile['id']; ?>)">
                                                        <i class="fas fa-plus"></i> Ek QR Oluştur
                                                    </a></li>
                                                    <?php endif; ?>
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
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>                        <div class="mb-3">
                            <label for="bio" class="form-label">Biyografi</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon Numarası</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #e9ecef; color: #495057; font-weight: 500;">+90</span>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="5XX XXX XX XX" 
                                       pattern="^5[0-9]{9}$" 
                                       maxlength="10"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0,10);"
                                       title="5 ile başlayan 10 haneli telefon numarası giriniz">
                            </div>
                            <div class="form-text">5 ile başlayan 10 haneli telefon numarası (örn: 5321234567)</div>
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Profil Fotoğrafı</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sosyal Medya Hesapları (isteğe bağlı)</label>
                            
                            <!-- Sosyal Medya Platform Seçimi -->
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Platform Ekle</h6>
                                    <div class="row g-2 social-platforms-grid">
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="instagram" data-container="socialLinksContainer">
                                                <i class="fab fa-instagram text-danger"></i>
                                                <span class="d-block small">Instagram</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="x" data-container="socialLinksContainer">
                                                <i class="fab fa-twitter" style="color: #1da1f2;"></i>
                                                <span class="d-block small">X</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="linkedin" data-container="socialLinksContainer">
                                                <i class="fab fa-linkedin text-primary"></i>
                                                <span class="d-block small">LinkedIn</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="facebook" data-container="socialLinksContainer">
                                                <i class="fab fa-facebook text-primary"></i>
                                                <span class="d-block small">Facebook</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="youtube" data-container="socialLinksContainer">
                                                <i class="fab fa-youtube text-danger"></i>
                                                <span class="d-block small">YouTube</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="tiktok" data-container="socialLinksContainer">
                                                <i class="fab fa-tiktok text-dark"></i>
                                                <span class="d-block small">TikTok</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="whatsapp" data-container="socialLinksContainer">
                                                <i class="fab fa-whatsapp text-success"></i>
                                                <span class="d-block small">WhatsApp</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="website" data-container="socialLinksContainer">
                                                <i class="fas fa-globe text-info"></i>
                                                <span class="d-block small">Website</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="snapchat" data-container="socialLinksContainer">
                                                <i class="fab fa-snapchat text-warning"></i>
                                                <span class="d-block small">Snapchat</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="discord" data-container="socialLinksContainer">
                                                <i class="fab fa-discord text-primary"></i>
                                                <span class="d-block small">Discord</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="telegram" data-container="socialLinksContainer">
                                                <i class="fab fa-telegram text-info"></i>
                                                <span class="d-block small">Telegram</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="twitch" data-container="socialLinksContainer">
                                                <i class="fab fa-twitch text-purple"></i>
                                                <span class="d-block small">Twitch</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seçilen Sosyal Medya Hesapları -->
                            <div id="socialLinksContainer" class="selected-social-medias">
                                <!-- Dinamik olarak eklenecek -->
                            </div>
                        </div>
                        
                        <!-- İban ve Kan Grubu Alanları -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="iban" class="form-label">İban <small class="text-muted">(Opsiyonel)</small></label>
                                    <input type="text" class="form-control" id="iban" name="iban" 
                                           placeholder="TR00 0000 0000 0000 0000 0000 00" 
                                           pattern="^TR[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{2}$"
                                           maxlength="32">
                                    <div class="form-text">TR ile başlayan 26 haneli İban numarası</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_type" class="form-label">Kan Grubu <small class="text-muted">(Opsiyonel)</small></label>
                                    <select class="form-select" id="blood_type" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        <option value="A+">A Rh+</option>
                                        <option value="A-">A Rh-</option>
                                        <option value="B+">B Rh+</option>
                                        <option value="B-">B Rh-</option>
                                        <option value="AB+">AB Rh+</option>
                                        <option value="AB-">AB Rh-</option>
                                        <option value="0+">0 Rh+</option>
                                        <option value="0-">0 Rh-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="theme" class="form-label">Profil Teması</label>
                            <select class="form-select" id="theme" name="theme" onchange="updateThemePreview()">
                                <?php
                                $themesQuery = "SELECT * FROM themes WHERE is_active = 1 ORDER BY theme_title";
                                $themesResult = $connection->query($themesQuery);
                                if ($themesResult) {
                                    while ($theme = $themesResult->fetch_assoc()):
                                        // Charset sorunu için özel işlem
                                        $themeTitle = mb_convert_encoding($theme['theme_title'], 'UTF-8', 'UTF-8');
                                        // Default tema seçili olsun
                                        $isSelected = ($theme['theme_name'] === 'default') ? 'selected' : '';
                                ?>
                                <option value="<?php echo htmlspecialchars($theme['theme_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-background-color="<?php echo htmlspecialchars($theme['background_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-text-color="<?php echo htmlspecialchars($theme['text_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-accent-color="<?php echo htmlspecialchars($theme['accent_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-card-background="<?php echo htmlspecialchars($theme['card_background'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-font-family="<?php echo htmlspecialchars($theme['font_family'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-button-style="<?php echo htmlspecialchars($theme['button_style'], ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo $isSelected; ?>>
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
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #e9ecef; color: #495057; font-weight: 500;">+90</span>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" 
                                       placeholder="5XX XXX XX XX" 
                                       pattern="^5[0-9]{9}$" 
                                       maxlength="10"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0,10);"
                                       title="5 ile başlayan 10 haneli telefon numarası giriniz">
                            </div>
                            <div class="form-text">5 ile başlayan 10 haneli telefon numarası (örn: 5321234567)</div>
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
                            <label class="form-label">Sosyal Medya Hesapları (isteğe bağlı)</label>
                            
                            <!-- Sosyal Medya Platform Seçimi -->
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Platform Ekle</h6>
                                    <div class="row g-2 social-platforms-grid">
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="instagram" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-instagram text-danger"></i>
                                                <span class="d-block small">Instagram</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="x" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-twitter" style="color: #1da1f2;"></i>
                                                <span class="d-block small">X</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="linkedin" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-linkedin text-primary"></i>
                                                <span class="d-block small">LinkedIn</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="facebook" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-facebook text-primary"></i>
                                                <span class="d-block small">Facebook</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="youtube" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-youtube text-danger"></i>
                                                <span class="d-block small">YouTube</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="tiktok" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-tiktok text-dark"></i>
                                                <span class="d-block small">TikTok</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="whatsapp" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-whatsapp text-success"></i>
                                                <span class="d-block small">WhatsApp</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="website" data-container="edit_socialLinksContainer">
                                                <i class="fas fa-globe text-info"></i>
                                                <span class="d-block small">Website</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="snapchat" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-snapchat text-warning"></i>
                                                <span class="d-block small">Snapchat</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="discord" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-discord text-primary"></i>
                                                <span class="d-block small">Discord</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="telegram" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-telegram text-info"></i>
                                                <span class="d-block small">Telegram</span>
                                            </button>
                                        </div>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="twitch" data-container="edit_socialLinksContainer">
                                                <i class="fab fa-twitch text-purple"></i>
                                                <span class="d-block small">Twitch</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seçilen Sosyal Medya Hesapları -->
                            <div id="edit_socialLinksContainer" class="selected-social-medias">
                                <!-- Dinamik olarak eklenecek -->
                            </div>
                        </div>
                        
                        <!-- İban ve Kan Grubu Alanları - Düzenleme -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_iban" class="form-label">İban <small class="text-muted">(Opsiyonel)</small></label>
                                    <input type="text" class="form-control" id="edit_iban" name="iban" 
                                           placeholder="TR00 0000 0000 0000 0000 0000 00" 
                                           pattern="^TR[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{2}$"
                                           maxlength="32">
                                    <div class="form-text">TR ile başlayan 26 haneli İban numarası</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_blood_type" class="form-label">Kan Grubu <small class="text-muted">(Opsiyonel)</small></label>
                                    <select class="form-select" id="edit_blood_type" name="blood_type">
                                        <option value="">Seçiniz</option>
                                        <option value="A+">A Rh+</option>
                                        <option value="A-">A Rh-</option>
                                        <option value="B+">B Rh+</option>
                                        <option value="B-">B Rh-</option>
                                        <option value="AB+">AB Rh+</option>
                                        <option value="AB-">AB Rh-</option>
                                        <option value="0+">0 Rh+</option>
                                        <option value="0-">0 Rh-</option>
                                    </select>
                                </div>
                            </div>
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
                        <p><strong>Telefon:</strong> <span id="view_phone"></span> <small id="view_phone_hidden" class="badge bg-secondary ms-2" style="display: none;">Gizli</small></p>
                        
                        <!-- İban Bilgisi - Modern Kart Tasarımı -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-12">
                                    <div id="view_iban_container" class="admin-info-card iban-card" style="display: none;">
                                        <div class="card shadow-sm h-100" style="border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="icon-container me-3">
                                                    <i class="fas fa-university" style="color: #fff; font-size: 1.5rem;"></i>
                                                </div>
                                                <div class="info-content flex-grow-1">
                                                    <div class="info-label" style="color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">
                                                        İBAN
                                                    </div>
                                                    <div id="view_iban" class="info-value" style="font-family: 'Segoe UI', 'Roboto', monospace; color: #fff; font-weight: 600; font-size: 1rem; letter-spacing: 0.5px; line-height: 1.2;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="view_iban_empty" class="text-muted fst-italic">İban bilgisi belirtilmemiş</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kan Grubu Bilgisi - Modern Kart Tasarımı -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-12">
                                    <div id="view_blood_type_container" class="admin-info-card blood-type-card" style="display: none;">
                                        <div class="card shadow-sm h-100" style="border: none; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
                                            <div class="card-body d-flex align-items-center">
                                                <div class="icon-container me-3">
                                                    <i class="fas fa-heartbeat" style="color: #fff; font-size: 1.5rem;"></i>
                                                </div>
                                                <div class="info-content flex-grow-1">
                                                    <div class="info-label" style="color: rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 500; margin-bottom: 4px;">
                                                        Kan Grubu
                                                    </div>
                                                    <div id="view_blood_type" class="info-value" style="color: #fff; font-weight: 700; font-size: 1.4rem; line-height: 1;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="view_blood_type_empty" class="text-muted fst-italic">Kan grubu bilgisi belirtilmemiş</span>
                                </div>
                            </div>
                        </div>
                        
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
    <script src="<?= getBasePath() ?>/assets/js/image-cleanup.js"></script>
    <script src="<?= getBasePath() ?>/assets/js/profile-manager.js"></script>
    <script>
    // Base path tanımlayalım
    const BASE_PATH = '<?= getBasePath() ?>';
    
    // Profil düzenleme modalını aç ve verileri doldur
    function editProfile(id) {
        $.ajax({
            url: BASE_PATH + '/admin/api/profile.php',
            method: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#edit_id').val(res.profile.id);
                    $('#edit_name').val(res.profile.name);
                    $('#edit_bio').val(res.profile.bio);
                    
                    // Telefon numarasını formatla - +90 prefixi olmadan göster
                    let phone = res.profile.phone || '';
                    if (phone.startsWith('+90')) {
                        phone = phone.substring(3);
                    } else if (phone.startsWith('90')) {
                        phone = phone.substring(2);
                    }
                    $('#edit_phone').val(phone);
                    
                    $('#edit_theme').val(res.profile.theme);
                    $('#edit_current_photo_url').val(res.profile.photo_url);
                    
                    // İban ve Kan Grubu değerlerini doldur
                    $('#edit_iban').val(res.profile.iban || '');
                    $('#edit_blood_type').val(res.profile.blood_type || '');
                    
                    // Mevcut foto gösterimi (photo_data kullanarak)
                    const photoContainer = $('#edit_current_photo_container');
                    if (res.profile.photo_data) {
                        try {
                            const photoData = JSON.parse(res.profile.photo_data);
                            if (photoData.filename) {
                                photoContainer.html(`
                                    <div class="d-flex align-items-center">
                                        <picture>
                                            <source srcset="${BASE_PATH}/public/uploads/profiles/thumb/${photoData.filename.replace(/\.[^/.]+$/, '')}.webp" type="image/webp">
                                            <img src="${BASE_PATH}/public/uploads/profiles/thumb/${photoData.filename}" 
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
                        // Eski format için fallback - URL'yi düzelt
                        let photoUrl = res.profile.photo_url;
                        // Sadece BASE_PATH kullan, path fix'lere gerek yok
                        if (!photoUrl.startsWith('http') && !photoUrl.startsWith(BASE_PATH)) {
                            if (photoUrl.startsWith('/')) {
                                photoUrl = BASE_PATH + photoUrl;
                            } else {
                                photoUrl = BASE_PATH + '/' + photoUrl;
                            }
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
                    
                    // Sosyal medya linklerini doldur - Modern versiyon
                    let links = [];
                    try { 
                        links = JSON.parse(res.profile.social_links); 
                    } catch(e) {
                        console.log('Social links parse error:', e);
                    }
                    
                    // Dizi değilse objeden diziye çevir
                    if (!Array.isArray(links) && typeof links === 'object' && links !== null) {
                        links = Object.entries(links).map(([platform, url]) => ({ platform, url }));
                    }
                    
                    // Container'ı temizle
                    const container = $('#edit_socialLinksContainer');
                    container.html('');
                    
                    // Tüm platform butonlarını aktif yap
                    $('.social-platform-btn[data-container="edit_socialLinksContainer"]')
                        .removeClass('disabled')
                        .prop('disabled', false);
                    
                    // Mevcut linkleri ekle
                    if (Array.isArray(links) && links.length > 0) {
                        links.forEach(link => {
                            if (link.platform && link.url && socialMediaPlatforms[link.platform]) {
                                addModernSocialMediaItem(link.platform, link.url, 'edit_socialLinksContainer');
                                
                                // Bu platform butonunu disabled yap
                                $(`.social-platform-btn[data-platform="${link.platform}"][data-container="edit_socialLinksContainer"]`)
                                    .addClass('disabled')
                                    .prop('disabled', true);
                            }
                        });
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
        
        // Telefon numarasına +90 prefixi ekle
        const phoneInput = document.getElementById('edit_phone');
        if (phoneInput.value && phoneInput.value.length === 10) {
            formData.set('phone', '+90' + phoneInput.value);
        }
        
        // Modern sosyal medya linklerini topla
        let links = [];
        $('#edit_socialLinksContainer .social-media-item').each(function() {
            let platform = $(this).data('platform');
            let url = $(this).find('input').val();
            
            // WhatsApp için özel URL formatlaması
            if (platform === 'whatsapp' && url) {
                // Telefon numarasını tam formata çevir
                if (url.length === 10 && url.startsWith('5')) {
                    url = '+90' + url; // +90 prefix'i ekle
                }
            }
            
            if (platform && url) {
                links.push({ platform, url });
            }
        });
        
        formData.append('social_links', JSON.stringify(links));
        formData.append('action', 'update');
        
        $.ajax({
            url: BASE_PATH + '/admin/api/profile.php',
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
            url: BASE_PATH + '/admin/api/profile.php',
            method: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#view_name').text(res.profile.name);
                    $('#view_bio').text(res.profile.bio);
                    $('#view_phone').text(res.profile.phone);
                    
                    // Telefon gizlilik durumunu göster
                    if (res.profile.phone_hidden == 1) {
                        $('#view_phone_hidden').show();
                    } else {
                        $('#view_phone_hidden').hide();
                    }
                    
                    // İban gösterimi - Modern ve okunabilir
                    if (res.profile.iban && res.profile.iban.trim() !== '') {
                        const iban = res.profile.iban.trim().replace(/\s/g, ''); // Boşlukları temizle
                        // İban'ı 4'lü gruplar halinde formatla
                        const formattedIban = iban.length > 4 ? iban.match(/.{1,4}/g).join(' ') : iban;
                        $('#view_iban').text(formattedIban);
                        $('#view_iban_container').show();
                        $('#view_iban_empty').hide();
                    } else {
                        $('#view_iban_container').hide();
                        $('#view_iban_empty').show();
                    }
                    
                    // Kan grubu gösterimi - Modern ve büyük font
                    if (res.profile.blood_type && res.profile.blood_type.trim() !== '') {
                        $('#view_blood_type').text(res.profile.blood_type.trim());
                        $('#view_blood_type_container').show();
                        $('#view_blood_type_empty').hide();
                    } else {
                        $('#view_blood_type_container').hide();
                        $('#view_blood_type_empty').show();
                    }
                    $('#view_theme').text(res.profile.theme);
                    
                    // Profil fotoğrafı gösterimi (photo_data kullanarak)
                    const photoContainer = $('#view_photo_container');
                    if (res.profile.photo_data) {
                        try {
                            const photoData = JSON.parse(res.profile.photo_data);
                            if (photoData.filename) {
                                photoContainer.html(`
                                    <picture>
                                        <source srcset="${BASE_PATH}/public/uploads/profiles/medium/${photoData.filename.replace(/\.[^/.]+$/, '')}.webp" type="image/webp">
                                        <img src="${BASE_PATH}/public/uploads/profiles/medium/${photoData.filename}" 
                                             alt="${res.profile.name} profil fotoğrafı" 
                                             class="img-thumbnail profile-photo-preview" 
                                             loading="lazy">
                                    </picture>
                                `);
                            } else {
                                photoContainer.html(`<img src="${BASE_PATH}/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">`);
                            }
                        } catch(e) {
                            console.error('Photo data parse error:', e);
                            photoContainer.html(`<img src="${BASE_PATH}/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">`);
                        }
                    } else if (res.profile.photo_url) {
                        // Eski format için fallback - URL'yi düzelt
                        let photoUrl = res.profile.photo_url;
                        if (!photoUrl.startsWith('http') && !photoUrl.startsWith(BASE_PATH)) {
                            if (photoUrl.startsWith('/')) {
                                photoUrl = BASE_PATH + photoUrl;
                            } else {
                                photoUrl = BASE_PATH + '/' + photoUrl;
                            }
                        }
                        photoContainer.html(`<img src="${photoUrl}" alt="${res.profile.name} profil fotoğrafı" class="img-thumbnail profile-photo-preview">`);
                    } else {
                        photoContainer.html(`<img src="${BASE_PATH}/assets/images/default-profile.svg" alt="Varsayılan profil" class="img-thumbnail profile-photo-preview">`);
                    }
                    
                    // Sosyal medya linkleri
                    let links = [];
                    console.log('Raw social_links from database:', res.profile.social_links);
                    try { links = JSON.parse(res.profile.social_links); } catch(e) {
                        console.error('JSON parse error for social_links:', e);
                    }
                    console.log('Parsed links:', links);
                    
                    if (!Array.isArray(links) && typeof links === 'object' && links !== null) {
                        links = Object.entries(links).map(([platform, url]) => ({ platform, url }));
                        console.log('Converted object to array:', links);
                    }
                    
                    let html = '';
                    if (Array.isArray(links) && links.length > 0) {
                        html += '<div class="mb-2"><strong>Sosyal Medya:</strong></div>';
                        links.forEach(link => {
                            console.log('Processing link:', link);
                            if (link.platform && link.url && socialMediaPlatforms[link.platform]) {
                                const platform = socialMediaPlatforms[link.platform];
                                let displayUrl = link.url;
                                let displayName = platform.name;
                                
                                // WhatsApp için özel formatla
                                if (link.platform === 'whatsapp') {
                                    // +90 ile başlıyorsa güzel formatla
                                    if (link.url.startsWith('+90')) {
                                        const phone = link.url.substring(3);
                                        displayName = `WhatsApp (+90 ${phone.substring(0,3)} ${phone.substring(3,6)} ${phone.substring(6,8)} ${phone.substring(8,10)})`;
                                    } else {
                                        displayName = `WhatsApp (${link.url})`;
                                    }
                                    displayUrl = `https://wa.me/${link.url.replace('+', '')}`;
                                }
                                // Website için özel kontrol
                                else if (link.platform === 'website') {
                                    displayUrl = link.url.startsWith('http') ? link.url : 'https://' + link.url;
                                }
                                // Diğer platformlar için base URL + kullanıcı adı
                                else {
                                    displayUrl = platform.baseUrl + link.url.replace(platform.prefix, '');
                                }
                                
                                console.log('Adding link:', { platform: link.platform, displayName, displayUrl });
                                html += `<div class="mb-1">
                                    <a href="${displayUrl}" target="_blank" class="text-decoration-none">
                                        <i class="${platform.icon} me-2" style="color: var(--bs-primary);"></i>
                                        ${displayName}
                                    </a>
                                </div>`;
                            } else {
                                console.warn('Skipping invalid link:', link);
                            }
                        });
                    } else {
                        console.log('No social links found or empty array');
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
                url: BASE_PATH + '/admin/api/profile.php',
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
            fetch(BASE_PATH + '/admin/api/qr.php?action=delete', {
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
    
    // Modern Social Media Management - Admin Panel
    const socialMediaPlatforms = {
        instagram: {
            name: 'Instagram',
            icon: 'fab fa-instagram',
            prefix: '@',
            baseUrl: 'https://instagram.com/',
            placeholder: 'kullanici_adi',
            color: 'platform-instagram'
        },
        x: {
            name: 'X',
            icon: 'fab fa-twitter',
            prefix: '@',
            baseUrl: 'https://x.com/',
            placeholder: 'kullanici_adi',
            color: 'platform-x'
        },
        linkedin: {
            name: 'LinkedIn',
            icon: 'fab fa-linkedin',
            prefix: '',
            baseUrl: 'https://linkedin.com/in/',
            placeholder: 'profil-adi',
            color: 'platform-linkedin'
        },
        facebook: {
            name: 'Facebook',
            icon: 'fab fa-facebook',
            prefix: '',
            baseUrl: 'https://facebook.com/',
            placeholder: 'profil.adi',
            color: 'platform-facebook'
        },
        youtube: {
            name: 'YouTube',
            icon: 'fab fa-youtube',
            prefix: '',
            baseUrl: 'https://youtube.com/@',
            placeholder: 'kanal_adi',
            color: 'platform-youtube'
        },
        tiktok: {
            name: 'TikTok',
            icon: 'fab fa-tiktok',
            prefix: '@',
            baseUrl: 'https://tiktok.com/@',
            placeholder: 'kullanici_adi',
            color: 'platform-tiktok'
        },
        whatsapp: {
            name: 'WhatsApp',
            icon: 'fab fa-whatsapp',
            prefix: '+90',
            baseUrl: 'https://wa.me/',
            placeholder: '5551234567',
            color: 'platform-whatsapp',
            validation: 'phone',
            helpText: '10 haneli telefon numarası (5XX XXX XX XX)'
        },
        website: {
            name: 'Website',
            icon: 'fas fa-globe',
            prefix: '',
            baseUrl: '',
            placeholder: 'https://website.com',
            color: 'platform-website'
        },
        snapchat: {
            name: 'Snapchat',
            icon: 'fab fa-snapchat',
            prefix: '@',
            baseUrl: 'https://snapchat.com/add/',
            placeholder: 'kullanici_adi',
            color: 'platform-snapchat'
        },
        discord: {
            name: 'Discord',
            icon: 'fab fa-discord',
            prefix: '',
            baseUrl: 'https://discord.gg/',
            placeholder: 'sunucu_davet_kodu',
            color: 'platform-discord'
        },
        telegram: {
            name: 'Telegram',
            icon: 'fab fa-telegram',
            prefix: '@',
            baseUrl: 'https://t.me/',
            placeholder: 'kullanici_adi',
            color: 'platform-telegram'
        },
        twitch: {
            name: 'Twitch',
            icon: 'fab fa-twitch',
            prefix: '',
            baseUrl: 'https://twitch.tv/',
            placeholder: 'kanal_adi',
            color: 'platform-twitch'
        }
    };

    // Modern sosyal medya platform butonları için event listener
    $(document).on('click', '.social-platform-btn', function() {
        const platform = $(this).data('platform');
        const container = $(this).data('container');
        
        // Platform zaten ekli mi kontrol et
        if ($(`#${container} .social-media-item[data-platform="${platform}"]`).length > 0) {
            showToast('Bu platform zaten eklenmiş!', 'warning');
            return;
        }
        
        addModernSocialMediaItem(platform, '', container);
        
        // Butonu disabled yap
        $(this).addClass('disabled').prop('disabled', true);
    });

    // Modern sosyal medya item ekleme fonksiyonu
    function addModernSocialMediaItem(platformKey, value = '', containerId = 'socialLinksContainer') {
        const platform = socialMediaPlatforms[platformKey];
        if (!platform) return;
        
        const container = $(`#${containerId}`);
        const itemId = `social-${platformKey}-${Date.now()}`;
        
        // WhatsApp için özel input alanı
        let inputField = '';
        if (platformKey === 'whatsapp') {
            // WhatsApp telefon numarasını +90 prefix'i olmadan göster
            let phoneValue = value;
            if (phoneValue.startsWith('+90')) {
                phoneValue = phoneValue.substring(3);
            } else if (phoneValue.startsWith('90')) {
                phoneValue = phoneValue.substring(2);
            }
            
            inputField = `
                <div class="input-group">
                    <span class="input-group-text">${platform.prefix}</span>
                    <input type="tel" class="form-control whatsapp-phone-input" 
                           placeholder="${platform.placeholder}" 
                           value="${phoneValue}"
                           data-platform="${platformKey}"
                           maxlength="10"
                           pattern="5[0-9]{9}"
                           title="5 ile başlayan 10 haneli telefon numarası">
                </div>
                <small class="text-muted mt-1 d-block">${platform.helpText}</small>
            `;
        } else {
            inputField = `
                <div class="input-group">
                    <span class="input-group-text">${platform.prefix}</span>
                    <input type="text" class="form-control" 
                           placeholder="${platform.placeholder}" 
                           value="${value}"
                           data-platform="${platformKey}">
                </div>
            `;
        }
        
        const itemHtml = `
            <div class="social-media-item" data-platform="${platformKey}" id="${itemId}">
                <div class="platform-header">
                    <div class="platform-icon ${platform.color}">
                        <i class="${platform.icon}"></i>
                    </div>
                    <span class="platform-name">${platform.name}</span>
                    <button type="button" class="remove-platform" onclick="removeModernSocialMediaItem('${itemId}', '${platformKey}', '${containerId}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${inputField}
            </div>
        `;
        
        container.append(itemHtml);
        
        // WhatsApp input'u için özel event listener ekle
        if (platformKey === 'whatsapp') {
            $(`#${itemId} .whatsapp-phone-input`).on('input', function() {
                formatWhatsAppInput(this);
            });
        }
    }

    // Modern sosyal medya item silme fonksiyonu
    function removeModernSocialMediaItem(itemId, platformKey, containerId) {
        $(`#${itemId}`).remove();
        
        // Platform butonunu tekrar aktif yap
        $(`.social-platform-btn[data-platform="${platformKey}"][data-container="${containerId}"]`)
            .removeClass('disabled')
            .prop('disabled', false);
    }

    // Profil oluşturma fonksiyonu güncelleme
    function createProfile() {
        var form = document.getElementById('createProfileForm');
        var formData = new FormData(form);
        
        // Telefon numarasına +90 prefixi ekle
        const phoneInput = document.getElementById('phone');
        if (phoneInput.value && phoneInput.value.length === 10) {
            formData.set('phone', '+90' + phoneInput.value);
        }
        
        // Modern sosyal medya linklerini topla
        let links = [];
        $('#socialLinksContainer .social-media-item').each(function() {
            let platform = $(this).data('platform');
            let url = $(this).find('input').val();
            
            // WhatsApp için özel URL formatlaması
            if (platform === 'whatsapp' && url) {
                // Telefon numarasını tam formata çevir
                if (url.length === 10 && url.startsWith('5')) {
                    url = '+90' + url; // +90 prefix'i ekle
                }
            }
            
            if (platform && url) {
                links.push({ platform, url });
            }
        });
        
        formData.append('social_links', JSON.stringify(links));
        formData.append('action', 'create');
        
        $.ajax({
            url: BASE_PATH + '/admin/api/profile.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    let message = 'Profil başarıyla oluşturuldu!';
                    
                    // QR kodu da oluşturulduysa mesaja ekle
                    if (res.qr_id) {
                        message += ' QR kodu da otomatik oluşturuldu.';
                    } else if (res.qr_error) {
                        message += ' (QR kodu oluşturulamadı: ' + res.qr_error + ')';
                    }
                    
                    showToast(message, 'success');
                    $('#createProfileModal').modal('hide');
                    location.reload();
                } else {
                    showToast('Profil oluşturulurken bir hata oluştu: ' + (res.message || ''), 'danger');
                }
            },
            error: function() {
                showToast('Sunucu hatası!', 'danger');
            }
        });
    }        // Eski addSocialLink fonksiyonunu modern versiyonla değiştirme
        function addSocialLink(containerId = 'socialLinksContainer', platform = '', url = '') {
            // Eğer modern versiyon kullanılıyorsa, modern fonksiyonu çağır
            if (platform && socialMediaPlatforms[platform]) {
                addModernSocialMediaItem(platform, url, containerId);
                return;
            }
            
            // Eski versiyon için fallback - boş platform seçimi
            const platforms = Object.keys(socialMediaPlatforms);
            const defaultPlatform = platforms[0];
            addModernSocialMediaItem(defaultPlatform, url, containerId);
        }

        // Modal temizleme fonksiyonları
        $('#createProfileModal').on('hidden.bs.modal', function() {
            // Form temizle
            $('#createProfileForm')[0].reset();
            
            // Sosyal medya container'ını temizle
            $('#socialLinksContainer').html('');
            
            // Tüm platform butonlarını aktif yap
            $('.social-platform-btn[data-container="socialLinksContainer"]')
                .removeClass('disabled')
                .prop('disabled', false);
        });

        $('#editProfileModal').on('hidden.bs.modal', function() {
            // Sosyal medya container'ını temizle
            $('#edit_socialLinksContainer').html('');
            
            // Tüm platform butonlarını aktif yap
            $('.social-platform-btn[data-container="edit_socialLinksContainer"]')
                .removeClass('disabled')
                .prop('disabled', false);
        });

        // Tema önizleme fonksiyonu
        function updateThemePreview() {
            const themeSelect = document.getElementById('theme');
            const selectedOption = themeSelect.options[themeSelect.selectedIndex];
            const previewCard = document.getElementById('preview-card');
            const previewButton = document.getElementById('preview-button');
            const themePreview = document.getElementById('theme-preview');

            if (selectedOption && selectedOption.value) {
                // Tema verilerini al
                const backgroundColor = selectedOption.getAttribute('data-background-color') || '#ffffff';
                const textColor = selectedOption.getAttribute('data-text-color') || '#333333';
                const accentColor = selectedOption.getAttribute('data-accent-color') || '#007bff';
                const cardBackground = selectedOption.getAttribute('data-card-background') || '#ffffff';
                const fontFamily = selectedOption.getAttribute('data-font-family') || 'Arial, sans-serif';
                const buttonStyle = selectedOption.getAttribute('data-button-style') || 'rounded';

                // Tema önizleme alanını güncelle
                themePreview.style.background = backgroundColor;
                themePreview.style.color = textColor;
                themePreview.style.fontFamily = fontFamily;

                // Kart stilini güncelle
                previewCard.style.background = cardBackground;
                previewCard.style.color = textColor;
                previewCard.style.fontFamily = fontFamily;

                // Buton stilini güncelle
                previewButton.style.backgroundColor = accentColor;
                previewButton.style.borderColor = accentColor;
                previewButton.style.color = '#ffffff';
                
                // Buton şekli
                if (buttonStyle === 'rounded') {
                    previewButton.style.borderRadius = '25px';
                } else if (buttonStyle === 'square') {
                    previewButton.style.borderRadius = '4px';
                } else {
                    previewButton.style.borderRadius = '8px';
                }
            }
        }

        // Modal açıldığında tema önizlemesini başlat
        $('#createProfileModal').on('shown.bs.modal', function() {
            // Default tema seçili olduğunda önizlemeyi güncelle
            updateThemePreview();
        });

        // Telefon numarası formatlaması
        function formatPhoneInput(input) {
            // Sadece rakam kabul et
            input.value = input.value.replace(/[^0-9]/g, '');
            
            // 10 haneden fazla girişi engelle
            if (input.value.length > 10) {
                input.value = input.value.substring(0, 10);
            }
            
            // 5 ile başlamasını zorunlu kıl
            if (input.value.length > 0 && !input.value.startsWith('5')) {
                input.value = '5' + input.value.substring(1);
            }
        }

        // WhatsApp telefon numarası formatlaması
        function formatWhatsAppInput(input) {
            // Sadece rakam kabul et
            input.value = input.value.replace(/[^0-9]/g, '');
            
            // 10 haneden fazla girişi engelle
            if (input.value.length > 10) {
                input.value = input.value.substring(0, 10);
            }
            
            // 5 ile başlamasını zorunlu kıl
            if (input.value.length > 0 && !input.value.startsWith('5')) {
                input.value = '5' + input.value.substring(1);
            }
            
            // Gerçek zamanlı doğrulama gösterimi
            const isValid = input.value.length === 10 && input.value.startsWith('5');
            if (input.value.length > 0) {
                if (isValid) {
                    $(input).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(input).removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                $(input).removeClass('is-valid is-invalid');
            }
        }

        // Telefon inputları için olay dinleyicileri
        $(document).ready(function() {
            // Profil oluşturma telefon inputu
            $('#phone').on('input', function() {
                formatPhoneInput(this);
            });
            
            // Profil düzenleme telefon inputu
            $('#edit_phone').on('input', function() {
                formatPhoneInput(this);
            });
        });

        // Sayfa yüklendiğinde DataTable'ı başlat
        $(document).ready(function() {
            // DataTable'ı başlat - destroy seçeneği ile çakışmaları önle
            if ($('#profilesTable').length) {
                $('#profilesTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json'
                    },
                    order: [[4, 'desc']], // Son güncelleme tarihine göre sırala
                    pageLength: 25,
                    responsive: true,
                    destroy: true // Çakışmaları önle
                });
            }
        });
    </script>
</body>
</html>
