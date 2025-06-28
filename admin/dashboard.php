<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';

// Session'ı güvenli şekilde başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charset ayarı
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/../includes/utilities.php';
require_once __DIR__ . '/../includes/QRManager.php';
require_once __DIR__ . '/../includes/OrderManager.php';

// Oturum kontrolü
Utilities::requireLogin();

$db = Database::getInstance();
$connection = $db->getConnection();

// Charset kontrolü
$connection->set_charset("utf8mb4");
$qrManager = new QRManager();
$orderManager = new OrderManager();

// QR kodlarını al
$query = "SELECT 
    qr.*, 
    p.name as profile_name,
    (SELECT COUNT(*) FROM scan_statistics WHERE qr_id = qr.id) as total_scans,
    (SELECT COUNT(*) FROM scan_statistics WHERE qr_id = qr.id AND scan_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_24h_scans,
    (SELECT COUNT(*) FROM scan_statistics WHERE qr_id = qr.id AND scan_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as last_7d_scans
FROM qr_codes qr 
LEFT JOIN profiles p ON qr.profile_id = p.id 
ORDER BY qr.created_at DESC";
$result = $db->query($query);
$qrCodes = [];
while ($row = $result->fetch_assoc()) {
    $qrCodes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli - Kişisel QR Sistemi</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= getBasePath() ?>/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= getBasePath() ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= getBasePath() ?>/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= getBasePath() ?>/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="<?= getBasePath() ?>/assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'templates/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="container">
                    <h2 class="mb-4">Dashboard</h2>

                    <!-- İstatistik Kartları -->
                    <div class="row mb-4">
                        <?php
                        // Toplam istatistikleri al
                        $totalScans = $db->query("SELECT COUNT(*) as total FROM scan_statistics")->fetch_assoc()['total'];
                        $totalProfiles = $db->query("SELECT COUNT(*) as total FROM profiles")->fetch_assoc()['total'];
                        $totalQRCodes = $db->query("SELECT COUNT(*) as total FROM qr_codes")->fetch_assoc()['total'];
                        $today = date('Y-m-d');
                        $todayScans = $db->query("SELECT COUNT(*) as total FROM scan_statistics WHERE DATE(scan_time) = '$today'")->fetch_assoc()['total'];
                        $lastWeekScans = $db->query("SELECT COUNT(*) as total FROM scan_statistics WHERE scan_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
                        
                        // Sipariş istatistiklerini al
                        try {
                            $orderStats = $orderManager->getDashboardStats();
                        } catch (Exception $e) {
                            // Sipariş tablosu henüz yoksa varsayılan değerler
                            $orderStats = [
                                'total_orders' => 0,
                                'pending_orders' => 0,
                                'completed_orders' => 0,
                                'today_orders' => 0,
                                'total_revenue' => 0
                            ];
                        }
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-qrcode me-2"></i>QR Kodları</h5>
                                    <p class="card-text display-6"><?php echo $totalQRCodes; ?></p>
                                    <small>Toplam oluşturulan QR kod sayısı</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-user me-2"></i>Profiller</h5>
                                    <p class="card-text display-6"><?php echo $totalProfiles; ?></p>
                                    <small>Toplam profil sayısı</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>Toplam Tarama</h5>
                                    <p class="card-text display-6"><?php echo $totalScans; ?></p>
                                    <small>Tüm zamanlar</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-clock me-2"></i>Bugünkü Taramalar</h5>
                                    <p class="card-text display-6"><?php echo $todayScans; ?></p>
                                    <small>Son 24 saat içinde</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-secondary text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-week me-2"></i>Haftalık Taramalar</h5>
                                    <p class="card-text display-6"><?php echo $lastWeekScans; ?></p>
                                    <small>Son 7 gün içinde</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sipariş İstatistikleri -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-shopping-cart me-2"></i>Toplam Sipariş</h5>
                                    <p class="card-text display-6"><?php echo $orderStats['total_orders']; ?></p>
                                    <small>Tüm siparişler</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-gradient-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-clock me-2"></i>Bugünkü Siparişler</h5>
                                    <p class="card-text display-6"><?php echo $orderStats['today_orders']; ?></p>
                                    <small>Son 24 saat içinde</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-gradient-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-check-circle me-2"></i>Tamamlanan</h5>
                                    <p class="card-text display-6"><?php echo $orderStats['completed_orders']; ?></p>
                                    <small>Başarıyla teslim edildi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card bg-gradient-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-hourglass-half me-2"></i>Bekleyen</h5>
                                    <p class="card-text display-6"><?php echo $orderStats['pending_orders']; ?></p>
                                    <small>İşlem bekliyor</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gelir ve İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-gradient-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-lira-sign me-2"></i>Toplam Gelir</h5>
                                    <p class="card-text display-6"><?php echo number_format($orderStats['total_revenue'], 0); ?> ₺</p>
                                    <small>Tamamlanan siparişlerden</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-gradient-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-week me-2"></i>Bu Ayki Siparişler</h5>
                                    <p class="card-text display-6"><?php echo $orderStats['this_month_orders']; ?></p>
                                    <small>Bu ay alınan siparişler</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stats-card bg-gradient-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>İşlem Oranı</h5>
                                    <p class="card-text display-6">
                                        <?php 
                                        $processedRate = $orderStats['total_orders'] > 0 ? 
                                            round(($orderStats['completed_orders'] / $orderStats['total_orders']) * 100) : 0;
                                        echo $processedRate; 
                                        ?>%
                                    </p>
                                    <small>Tamamlanma oranı</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Kodları Listesi -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">QR Kod Listesi</h5>
                        </div>
                        <div class="card-body">
                            <table id="qrTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>QR Kod ID</th>
                                        <th>Profil Adı</th>
                                        <th>Oluşturulma Tarihi</th>
                                        <th class="text-center">Toplam Tarama</th>
                                        <th class="text-center">Son 24 Saat</th>
                                        <th class="text-center">Son 7 Gün</th>
                                        <th class="text-center">Durum</th>
                                        <th class="text-center">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($qrCodes as $qr): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($qr['id']) ?></td>
                                            <td><?= htmlspecialchars($qr['profile_name']) ?></td>
                                            <td><?= date('d.m.Y H:i', strtotime($qr['created_at'])) ?></td>
                                            <td class="text-center"><?= $qr['total_scans'] ?? 0 ?></td>
                                            <td class="text-center"><?= $qr['last_24h_scans'] ?? 0 ?></td>
                                            <td class="text-center"><?= $qr['last_7d_scans'] ?? 0 ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= $qr['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= $qr['is_active'] ? 'Aktif' : 'Pasif' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= getBasePath() ?>/public/qr_codes/<?= htmlspecialchars($qr['id']) ?>.png"
                                                    class="btn btn-sm btn-success"
                                                    download="QR-<?= htmlspecialchars($qr['id']) ?>.png"
                                                    title="QR Kodu İndir">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="scan_statistics.php?qr_id=<?= htmlspecialchars($qr['id']) ?>"
                                                    class="btn btn-sm btn-info"
                                                    title="Tarama İstatistikleri">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="<?= getBasePath() ?>/redirect.php?qr_id=<?= htmlspecialchars($qr['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    target="_blank"
                                                    title="QR ile açılan sayfaya git">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
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
    </div> <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#qrTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json'
                },
                order: [
                    [2, 'desc']
                ], // Oluşturulma tarihine göre sırala
                columnDefs: [{
                        orderable: false,
                        targets: 7
                    } // İşlemler sütunu için sıralama kapalı
                ],
                pageLength: 25 // Sayfa başına gösterilecek kayıt sayısı
            });

            // QR silme butonu için event listener
            $('.delete-qr').on('click', function(e) {
                e.preventDefault();
                const qrId = $(this).data('qr-id');

                if (!qrId) {
                    alert('QR kod ID bulunamadı!');
                    return;
                }

                if (confirm('Bu QR kodu silmek istediğinize emin misiniz?')) {
                    $.ajax({
                        url: '/kisisel_qr/admin/api/delete_qr.php',
                        method: 'POST',
                        data: {
                            qrId: qrId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                window.location.reload();
                            } else {
                                alert('QR kod silinirken bir hata oluştu: ' + (response.message || 'Bilinmeyen hata'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Hata:', error);
                            alert('Bir hata oluştu! Lütfen tekrar deneyin.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>