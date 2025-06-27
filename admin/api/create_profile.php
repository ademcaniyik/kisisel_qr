<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/utilities.php';

// Oturum kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Geçersiz metod']);
    exit();
}

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();

    // Form verilerini al
    $name = Utilities::sanitizeInput($_POST['name']);
    $bio = Utilities::sanitizeInput($_POST['bio']);
    $phone = Utilities::sanitizeInput($_POST['phone']);
    $theme = Utilities::sanitizeInput($_POST['theme'] ?? 'default');
    $socialLinks = json_decode($_POST['socialLinks'], true);
    $slug = Utilities::generateSlug();

    // Tema geçerliliğini kontrol et
    $themeCheckStmt = $connection->prepare("SELECT theme_name FROM themes WHERE theme_name = ?");
    $themeCheckStmt->bind_param("s", $theme);
    $themeCheckStmt->execute();
    if ($themeCheckStmt->get_result()->num_rows === 0) {
        $theme = 'default';
    }
    $themeCheckStmt->close();

    $socialLinksJson = json_encode($socialLinks);

    // Profil fotoğrafını yükle
    $photoUrl = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowedTypes)) {
            throw new Exception('Geçersiz dosya tipi. Sadece JPG, PNG ve GIF dosyaları kabul edilir.');
        }
        $fileName = uniqid() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
            $photoUrl = '/kisisel_qr_canli/public/uploads/profiles/' . $fileName;
        }
    }
    $stmt = $connection->prepare("INSERT INTO profiles (name, bio, phone, social_links, photo_url, slug, theme) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $bio, $phone, $socialLinksJson, $photoUrl, $slug, $theme);
    if ($stmt->execute()) {
        // Profil başarıyla oluşturulduysa, otomatik QR oluştur
        $newProfileId = $connection->insert_id;
        require_once __DIR__ . '/../../includes/QRManager.php';
        $qrManager = new QRManager();
        $qrResult = $qrManager->createQR($newProfileId);
        if ($qrResult['success']) {
            echo json_encode(['success' => true, 'message' => 'Profil ve QR başarıyla oluşturuldu']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Profil oluşturuldu fakat QR oluşturulamadı: ' . $qrResult['message']]);
        }
    } else {
        throw new Exception('Profil oluşturulurken bir hata oluştu');
    }
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
