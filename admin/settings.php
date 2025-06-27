<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/../includes/utilities.php';

// Oturum kontrolü
Utilities::requireLogin();

$db = Database::getInstance();
$connection = $db->getConnection();

// Ayarları kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Şifre değişikliği
    if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        
        // Mevcut şifreyi kontrol et
        $stmt = $connection->prepare("SELECT password FROM admins WHERE id = ?");
        $adminId = $_SESSION['admin_id'];
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if (password_verify($currentPassword, $admin['password'])) {
            // Yeni şifreyi güncelle
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $adminId);
            
            if ($stmt->execute()) {
                $message = ['type' => 'success', 'text' => 'Şifre başarıyla güncellendi.'];
            } else {
                $message = ['type' => 'danger', 'text' => 'Şifre güncellenirken bir hata oluştu.'];
            }
        } else {
            $message = ['type' => 'danger', 'text' => 'Mevcut şifre yanlış.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Kişisel QR Sistemi</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= getBasePath() ?>/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= getBasePath() ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= getBasePath() ?>/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= getBasePath() ?>/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="<?= getBasePath() ?>/assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="p-3">
                    <h5>Kişisel QR Sistemi</h5>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/kisisel_qr/admin/dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/kisisel_qr/admin/profiles.php">
                                <i class="fas fa-user-circle me-2"></i>Profiller
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/kisisel_qr/admin/settings.php">
                                <i class="fas fa-cog me-2"></i>Ayarlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/kisisel_qr/admin/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Çıkış
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="container">
                    <h2 class="mb-4">Ayarlar</h2>

                    <?php if (isset($message)): ?>
                    <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Şifre Değiştir</h5>
                            <form method="POST" action="" class="mt-3">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mevcut Şifre</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Yeni Şifre</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Şifre eşleşme kontrolü
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Şifreler eşleşmiyor!');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
