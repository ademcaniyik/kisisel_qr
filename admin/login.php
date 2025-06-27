<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/utilities.php';

// Zaten giriş yapmış kullanıcıyı dashboard'a yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /kisisel_qr_canli/admin/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Utilities::sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $db = Database::getInstance();
            $connection = $db->getConnection();
            
            // Debug bilgisi
            error_log("Giriş denemesi - Kullanıcı: " . $username);
            
            $stmt = $connection->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            if (!$stmt) {
                throw new Exception("Sorgu hazırlanamadı: " . $connection->error);
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Debug bilgisi
                error_log("Veritabanından gelen şifre hash'i: " . $user['password']);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    
                    // Son giriş zamanını güncelle
                    $updateStmt = $connection->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                    $updateStmt->bind_param("i", $user['id']);
                    $updateStmt->execute();
                    
                    // Debug bilgisi
                    error_log("Başarılı giriş: " . $username);
                      header('Location: /kisisel_qr_canli/admin/dashboard.php');
                    exit();
                }
            }
            
            // Eğer buraya kadar geldiyse, giriş başarısız olmuştur
            error_log("Giriş başarısız - Kullanıcı: " . $username);
            $error = "Geçersiz kullanıcı adı veya şifre!";
            
        } catch (Exception $e) {
            error_log("Giriş hatası: " . $e->getMessage());
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    } else {
        $error = 'Lütfen tüm alanları doldurun!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi - Kişisel QR Sistemi</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr_canli/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr_canli/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr_canli/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr_canli/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="/kisisel_qr_canli/assets/css/login.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h2>Yönetici Girişi</h2>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </form>
        <div class="text-center mt-3">
            <a href="/" class="text-decoration-none">Ana Sayfaya Dön</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
