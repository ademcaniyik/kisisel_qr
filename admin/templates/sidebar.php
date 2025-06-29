<?php
// Aktif sayfayı belirle
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-2 col-sm-12 sidebar">
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admin Panel</h5>
            <button class="btn btn-link d-md-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <hr>
        <div class="collapse d-md-block" id="sidebarMenu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'profiles.php' ? 'active' : ''; ?>" href="profiles.php">
                        <i class="fas fa-users me-2"></i>Profiller
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="fas fa-shopping-cart me-2"></i>Siparişler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'qr_pool.php' ? 'active' : ''; ?>" href="qr_pool.php">
                        <i class="fas fa-layer-group me-2"></i>QR Pool
                        <?php
                        // Stok uyarısı için badge
                        if (file_exists(__DIR__ . '/../../includes/QRPoolManager.php')) {
                            require_once __DIR__ . '/../../includes/QRPoolManager.php';
                            try {
                                $qrPoolManager = new QRPoolManager();
                                $stockStatus = $qrPoolManager->getStockStatus();
                                if ($stockStatus['stock_warning']) {
                                    echo '<span class="badge bg-warning ms-2">!</span>';
                                }
                            } catch (Exception $e) {
                                // Hata durumunda badge gösterme
                            }
                        }
                        ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'scan_statistics.php' ? 'active' : ''; ?>" href="scan_statistics.php">
                        <i class="fas fa-chart-bar me-2"></i>Tarama İstatistikleri
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'logs.php' ? 'active' : ''; ?>" href="logs.php">
                        <i class="fas fa-file-alt me-2"></i>İşlem Logları
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// Mobil görünümde menü açıldığında otomatik kapanma
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth < 768) {
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const bsCollapse = new bootstrap.Collapse(sidebarMenu, {toggle: false});
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                bsCollapse.hide();
            });
        });
    }
});
</script>
