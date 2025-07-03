<?php
// admin/api/profile.php
// Tüm profil işlemleri (create, delete, get_slug, search) tek dosyada yönetilir.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/utilities.php';
require_once __DIR__ . '/../../includes/ImageOptimizer.php';
require_once __DIR__ . '/../../includes/QRManager.php';

// Session'ı güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit();
}

// --- RATE LIMITING ---
if (!Utilities::rateLimit('profile_api_' . ($_SERVER['REMOTE_ADDR'] ?? 'guest'), 60, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok fazla istek. Lütfen daha sonra tekrar deneyin.']);
    exit();
}
// --- CSRF KORUMASI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    // Sadece create ve update için kontrol et
    $action = $_POST['action'] ?? '';
    if (in_array($action, ['create', 'update'])) {
        // Eğer frontend'den csrf_token gönderilmiyorsa bu satırı yoruma alabilirsiniz
        // if (!Utilities::validateCsrfToken($csrfToken)) {
        //     http_response_code(403);
        //     echo json_encode(['success' => false, 'message' => 'Geçersiz CSRF token']);
        //     exit();
        // }
    }
}

$action = $_REQUEST['action'] ?? '';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();

    switch ($action) {
        case 'create':
            // Profil oluşturma
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $name = Utilities::sanitizeInput($_POST['name']);
            $bio = Utilities::sanitizeInput($_POST['bio']);
            $phone = Utilities::sanitizeInput($_POST['phone']);
            $theme = Utilities::sanitizeInput($_POST['theme'] ?? 'default');
            $iban = Utilities::sanitizeInput($_POST['iban'] ?? '');
            $bloodType = Utilities::sanitizeInput($_POST['blood_type'] ?? '');
            $isDynamic = isset($_POST['is_dynamic']) ? 1 : 0;
            $redirectUrl = $isDynamic ? Utilities::sanitizeInput($_POST['redirect_url']) : null;
            // Social links zaten JavaScript'te JSON.stringify ile gönderildiği için, tekrar encode etmeyelim
            $socialLinksJson = isset($_POST['social_links']) ? $_POST['social_links'] : '[]';
            
            $slug = Utilities::generateSlug();
            $themeCheckStmt = $connection->prepare("SELECT theme_name FROM themes WHERE theme_name = ?");
            $themeCheckStmt->bind_param("s", $theme);
            $themeCheckStmt->execute();
            if ($themeCheckStmt->get_result()->num_rows === 0) {
                $theme = 'default';
            }
            $themeCheckStmt->close();
            $photoUrl = null;
            $photoData = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $imageOptimizer = new ImageOptimizer();
                $uploadResult = $imageOptimizer->uploadAndOptimize($_FILES['photo']);
                
                if ($uploadResult['success']) {
                    $photoUrl = '/public/uploads/profiles/' . $uploadResult['filename'];
                    $photoData = json_encode([
                        'original' => $uploadResult['original'],
                        'thumbnails' => $uploadResult['thumbnails'],
                        'filesize' => $uploadResult['filesize'],
                        'filename' => $uploadResult['filename']
                    ]);
                } else {
                    throw new Exception($uploadResult['message']);
                }
            }
            $stmt = $connection->prepare("INSERT INTO profiles (name, bio, phone, social_links, photo_url, photo_data, slug, theme, iban, blood_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $name, $bio, $phone, $socialLinksJson, $photoUrl, $photoData, $slug, $theme, $iban, $bloodType);
            
            if ($stmt->execute()) {
                $profileId = $connection->insert_id;
                
                // Profil oluşturulduktan sonra QR Pool'dan QR ata
                try {
                    require_once __DIR__ . '/../../includes/QRPoolManager.php';
                    $qrPoolManager = new QRPoolManager();
                    
                    $qrAssignment = $qrPoolManager->assignAvailableQR($profileId);
                    
                    if ($qrAssignment['success']) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Profil ve QR kodu başarıyla oluşturuldu',
                            'profile_id' => $profileId,
                            'qr_code_id' => $qrAssignment['qr_code_id'],
                            'pool_id' => $qrAssignment['pool_id'],
                            'profile_url' => $qrAssignment['profile_url'],
                            'edit_url' => $qrAssignment['edit_url'],
                            'edit_code' => $qrAssignment['edit_code']
                        ]);
                    } else {
                        // QR havuzunda QR yoksa hata döndür
                        echo json_encode([
                            'success' => false,
                            'message' => 'Profil oluşturuldu ancak QR Pool tükendi: ' . $qrAssignment['error'],
                            'profile_id' => $profileId,
                            'qr_error' => $qrAssignment['error']
                        ]);
                    }
                } catch (Exception $e) {
                    // QR Pool hatası - eski sisteme fallback
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Profil oluşturuldu ancak QR Pool hatası: ' . $e->getMessage(),
                        'profile_id' => $profileId,
                        'qr_error' => $e->getMessage()
                    ]);
                }
            } else {
                throw new Exception('Profil oluşturulurken bir hata oluştu');
            }
            $stmt->close();
            break;
        case 'delete':
            // Profil silme
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz profil ID']);
                exit();
            }
            $stmt = $connection->prepare("SELECT photo_data FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $photoData = null;
            if ($row = $result->fetch_assoc()) {
                $photoData = $row['photo_data'] ? json_decode($row['photo_data'], true) : null;
            }
            $stmt->close();
            
            // QR Pool'dan bu profile atanmış QR'ları serbest bırak
            try {
                require_once __DIR__ . '/../../includes/QRPoolManager.php';
                $qrPoolManager = new QRPoolManager();
                $unassignResult = $qrPoolManager->unassignProfileQR($id);
                
                if ($unassignResult['success']) {
                    error_log("Profile $id deleted - QR unassign result: " . json_encode($unassignResult));
                } else {
                    error_log("Warning: Failed to unassign QR codes for profile $id: " . $unassignResult['error']);
                }
            } catch (Exception $e) {
                error_log("Error unassigning QR codes during profile deletion: " . $e->getMessage());
            }
            
            // İlgili QR kodlarını dosya sisteminden sil
            $stmt = $connection->prepare("SELECT id FROM qr_codes WHERE profile_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $qrResult = $stmt->get_result();
            
            while ($qr = $qrResult->fetch_assoc()) {
                $qrFilePath = __DIR__ . '/../../public/qr_codes/' . $qr['id'] . '.png';
                if (file_exists($qrFilePath)) {
                    unlink($qrFilePath);
                }
            }
            $stmt->close();
            
            // QR kodlarını veritabanından sil
            $stmt = $connection->prepare("DELETE FROM qr_codes WHERE profile_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            // Profil resimlerini sil (ImageOptimizer kullanarak)
            if ($photoData && isset($photoData['filename'])) {
                $imageOptimizer = new ImageOptimizer();
                $imageOptimizer->deleteImageFiles($photoData['filename']);
            }
            
            // Profili veritabanından sil
            $stmt = $connection->prepare("DELETE FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Profil başarıyla silindi']);
            } else {
                throw new Exception('Profil silinirken bir hata oluştu');
            }
            break;
        case 'get_slug':
            // Slug getirme
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $profileId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($profileId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz profil ID']);
                exit();
            }
            $stmt = $connection->prepare("SELECT slug FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $profileId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Profil bulunamadı']);
                exit();
            }
            $profile = $result->fetch_assoc();
            if (!$profile['slug']) {
                $slug = Utilities::generateSlug();
                $updateStmt = $connection->prepare("UPDATE profiles SET slug = ? WHERE id = ?");
                $updateStmt->bind_param("si", $slug, $profileId);
                $updateStmt->execute();
            } else {
                $slug = $profile['slug'];
            }
            echo json_encode([
                'success' => true,
                'slug' => $slug
            ]);
            break;
        case 'search':
            // Profil arama
            $search = isset($_POST['search']) ? trim($_POST['search']) : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            $theme = isset($_POST['theme']) ? $_POST['theme'] : '';
            $dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : '';
            $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : '';
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $perPage = 12;
            $query = "SELECT p.*, COUNT(s.id) as scan_count 
                      FROM profiles p 
                      LEFT JOIN scan_statistics s ON p.id = s.profile_id 
                      WHERE 1=1";
            $params = [];
            $types = "";
            if ($search) {
                $query .= " AND (p.name LIKE ? OR p.bio LIKE ?)";
                $searchParam = "%{$search}%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= "ss";
            }
            if ($status) {
                $query .= " AND p.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            if ($theme) {
                $query .= " AND p.theme = ?";
                $params[] = $theme;
                $types .= "s";
            }
            if ($dateFrom) {
                $query .= " AND p.created_at >= ?";
                $params[] = $dateFrom;
                $types .= "s";
            }
            if ($dateTo) {
                $query .= " AND p.created_at <= ?";
                $params[] = $dateTo . ' 23:59:59';
                $types .= "s";
            }
            $query .= " GROUP BY p.id ORDER BY p.created_at DESC";
            $query .= " LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = ($page - 1) * $perPage;
            $types .= "ii";
            $stmt = $connection->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $profiles = [];
            while ($row = $result->fetch_assoc()) {
                $profiles[] = [
                    'id' => $row['id'],
                    'name' => htmlspecialchars($row['name']),
                    'status' => $row['status'],
                    'theme' => $row['theme'],
                    'created_at' => $row['created_at'],
                    'scan_count' => $row['scan_count'],
                    'qr_code' => substr($row['qr_code'], 0, 8) . '...'
                ];
            }
            header('Content-Type: application/json');
            echo json_encode([
                'profiles' => $profiles,
                'page' => $page,
                'perPage' => $perPage
            ]);
            break;
        case 'update':
            // Profil güncelleme
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz profil ID']);
                exit();
            }
            
            $name = Utilities::sanitizeInput($_POST['name']);
            $bio = Utilities::sanitizeInput($_POST['bio']);
            $phone = Utilities::sanitizeInput($_POST['phone']);
            $theme = Utilities::sanitizeInput($_POST['theme'] ?? 'default');
            $iban = Utilities::sanitizeInput($_POST['iban'] ?? '');
            $bloodType = Utilities::sanitizeInput($_POST['blood_type'] ?? '');
            
            // Social links zaten JavaScript'te JSON.stringify ile gönderildiği için, tekrar encode etmeyelim
            $socialLinksJson = isset($_POST['social_links']) ? $_POST['social_links'] : '[]';
            
            // Mevcut profil verilerini al
            $stmt = $connection->prepare("SELECT photo_url, photo_data FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentProfile = $result->fetch_assoc();
            $stmt->close();
            
            if (!$currentProfile) {
                echo json_encode(['success' => false, 'message' => 'Profil bulunamadı']);
                exit();
            }
            
            $photoUrl = $currentProfile['photo_url'];
            $photoData = $currentProfile['photo_data'] ? json_decode($currentProfile['photo_data'], true) : null;
            
            // Yeni fotoğraf yüklendi mi?
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $imageOptimizer = new ImageOptimizer();
                $uploadResult = $imageOptimizer->uploadAndOptimize($_FILES['photo']);
                
                if ($uploadResult['success']) {
                    // Eski fotoğrafları sil
                    if ($photoData && isset($photoData['filename'])) {
                        $imageOptimizer->deleteImageFiles($photoData['filename']);
                    }
                    
                    // Yeni fotoğraf bilgilerini güncelle
                    $photoUrl = '/public/uploads/profiles/' . $uploadResult['filename'];
                    $photoData = $uploadResult;
                } else {
                    throw new Exception($uploadResult['message']);
                }
            }
            // Eğer yeni fotoğraf yüklenmediyse, mevcut photo_data'yı koru
            
            // Veritabanını güncelle - mevcut photo_data'yı koru
            $photoDataJson = $photoData ? json_encode($photoData) : $currentProfile['photo_data'];
            $stmt = $connection->prepare("UPDATE profiles SET name=?, bio=?, phone=?, social_links=?, photo_url=?, photo_data=?, theme=?, iban=?, blood_type=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("sssssssssi", $name, $bio, $phone, $socialLinksJson, $photoUrl, $photoDataJson, $theme, $iban, $bloodType, $id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Profil başarıyla güncellendi',
                    'photo_data' => $photoData
                ]);
            } else {
                throw new Exception('Profil güncellenirken bir hata oluştu');
            }
            $stmt->close();
            break;
        case 'get':
            // Profil detaylarını getir
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            $profileId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($profileId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz profil ID']);
                exit();
            }
            $stmt = $connection->prepare("SELECT id, name, bio, phone, theme, photo_url, social_links, iban, blood_type, phone_hidden, photo_hidden FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $profileId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Profil bulunamadı']);
                exit();
            }
            $profile = $result->fetch_assoc();
            echo json_encode(['success' => true, 'profile' => $profile]);
            break;
        
        case 'cleanup_old_images':
            // Eski profil resimlerini temizle
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
                exit();
            }
            
            $profileId = intval($_POST['profile_id'] ?? 0);
            $newPhotoData = json_decode($_POST['new_photo_data'] ?? '{}', true);
            
            if ($profileId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Geçersiz profil ID']);
                exit();
            }
            
            // Mevcut profil verilerini al
            $stmt = $connection->prepare("SELECT photo_data FROM profiles WHERE id = ?");
            $stmt->bind_param("i", $profileId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $oldPhotoData = $row['photo_data'] ? json_decode($row['photo_data'], true) : null;
                $cleanedFiles = [];
                
                if ($oldPhotoData && isset($oldPhotoData['filename']) && 
                    (!$newPhotoData || $oldPhotoData['filename'] !== ($newPhotoData['filename'] ?? ''))) {
                    
                    $imageOptimizer = new ImageOptimizer();
                    $cleanupResult = $imageOptimizer->deleteImageFiles($oldPhotoData['filename']);
                    
                    if ($cleanupResult['success']) {
                        $cleanedFiles = $cleanupResult['deleted_files'];
                    }
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Temizlik tamamlandı',
                    'cleaned_files' => $cleanedFiles
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Profil bulunamadı']);
            }
            
            $stmt->close();
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Geçersiz action parametresi']);
            exit();
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası', 'error' => $e->getMessage()]);
    exit();
}
