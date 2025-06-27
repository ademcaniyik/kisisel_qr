<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';
require_once __DIR__ . '/../includes/utilities.php';

// Oturum kontrolü
Utilities::requireLogin();

$db = Database::getInstance();
$connection = $db->getConnection();

// QR kodu ID'si
$qr_id = isset($_GET['qr_id']) ? $_GET['qr_id'] : null;

// İstatistikleri al
$query = "
    SELECT 
        ss.*,
        p.name as profile_name,
        q.id as qr_code
    FROM scan_statistics ss
    LEFT JOIN qr_codes q ON ss.qr_id = q.id
    LEFT JOIN profiles p ON q.profile_id = p.id
    " . ($qr_id ? "WHERE ss.qr_id = ?" : "") . "
    ORDER BY ss.scan_time DESC";

$stmt = $connection->prepare($query);
if ($qr_id) {
    $stmt->bind_param("s", $qr_id);
}
$stmt->execute();
$result = $stmt->get_result();
$statistics = [];
while ($row = $result->fetch_assoc()) {
    $statistics[] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tarama İstatistikleri - Kişisel QR Sistemi</title>
    
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
            <?php include __DIR__ . '/templates/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>QR Tarama İstatistikleri</h2>
                        <?php if ($qr_id): ?>
                            <a href="scan_statistics.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Tüm İstatistikler
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- İstatistik Tablosu -->
                    <div class="card">
                        <div class="card-body">
                            <table id="statisticsTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>QR Kodu</th>
                                        <th>Profil</th>
                                        <th>Tarama Zamanı</th>
                                        <th>IP Adresi</th>
                                        <th>Cihaz Bilgisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statistics as $stat): ?>
                                        <?php 
                                            $deviceInfo = json_decode($stat['device_info'], true);
                                            $userAgent = isset($deviceInfo['user_agent']) ? $deviceInfo['user_agent'] : $stat['user_agent'];
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="scan_statistics.php?qr_id=<?= htmlspecialchars($stat['qr_code']) ?>">
                                                    <?= htmlspecialchars($stat['qr_code']) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($stat['profile_name']) ?></td>
                                            <td><?= htmlspecialchars($stat['scan_time']) ?></td>
                                            <td><?= htmlspecialchars($stat['ip_address']) ?></td>
                                            <td class="device-info" title="<?= htmlspecialchars($userAgent) ?>">
                                                <?= htmlspecialchars($userAgent) ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#statisticsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
                },
                "order": [[2, "desc"]], // Tarama zamanına göre sırala
                "pageLength": 25
            });
        });
    </script>
</body>
</html>
