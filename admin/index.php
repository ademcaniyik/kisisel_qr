<?php
require_once '../config/database.php';
require_once '../includes/utilities.php';

// Oturum kontrolü
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /kisisel_qr_canli/admin/login.php');
    exit;
}

$pageTitle = "Dashboard - Kişisel QR Yönetim Sistemi";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr_canli/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr_canli/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr_canli/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr_canli/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/admin.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/dashboard.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/landing.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/profile-themes.css" rel="stylesheet">
    <link href="/kisisel_qr_canli/assets/css/social-buttons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- İstatistik Kartları -->
                <div class="row dashboard-stats">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-users stat-icon"></i>
                            <div class="stat-value" id="total-profiles">-</div>
                            <div class="stat-label">Toplam Profil</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-qrcode stat-icon"></i>
                            <div class="stat-value" id="total-scans">-</div>
                            <div class="stat-label">Toplam Tarama</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-clock stat-icon"></i>
                            <div class="stat-value" id="today-scans">-</div>
                            <div class="stat-label">Bugünkü Tarama</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-card">
                            <i class="fas fa-check-circle stat-icon"></i>
                            <div class="stat-value" id="active-profiles">-</div>
                            <div class="stat-label">Aktif Profil</div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Kartları -->
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <div class="chart-card">
                            <h3 class="chart-title">Tarama İstatistikleri</h3>
                            <canvas id="scanChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="chart-card">
                            <h3 class="chart-title">Cihaz Dağılımı</h3>
                            <canvas id="deviceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Eylemler -->
                <div class="row quick-actions">
                    <div class="col-md-6 mb-4">
                        <h3>Hızlı Eylemler</h3>
                        <div class="action-card">
                            <a href="profiles.php" class="d-flex align-items-center text-decoration-none text-dark">
                                <i class="fas fa-plus-circle action-icon"></i>
                                <span>Yeni Profil Oluştur</span>
                            </a>
                        </div>
                        <div class="action-card">
                            <a href="profiles.php" class="d-flex align-items-center text-decoration-none text-dark">
                                <i class="fas fa-list action-icon"></i>
                                <span>Profilleri Yönet</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/kisisel_qr/assets/js/dashboard.js"></script>
</body>
</html>
