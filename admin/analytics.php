<?php
/**
 * Analytics Dashboard - Admin Panel
 * Site trafiği ve kullanıcı davranışı analizi
 */

// Hata ayıklama için
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session'ı başlat (security.php'yi bypass et)
session_start();

// Database bağlantısını dahil et
require_once '../config/database.php';
require_once '../includes/AnalyticsManager.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

try {
    $analytics = new AnalyticsManager();

    // Tarih filtreleri
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');

    // Günlük istatistikleri hesapla (bugün için)
    $analytics->calculateDailyStats(date('Y-m-d'));

    // Dashboard verilerini al
    $dashboardData = $analytics->getDashboardData($startDate, $endDate);
    $totalStats = $dashboardData['total_stats'];
    $dailyStats = $dashboardData['daily_stats'];
    $popularPages = $dashboardData['popular_pages'];
    
} catch (Exception $e) {
    echo "<h1>Analytics Hatası</h1>";
    echo "<p>Hata: " . $e->getMessage() . "</p>";
    echo "<p>Dosya: " . $e->getFile() . "</p>";
    echo "<p>Satır: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "<a href='dashboard.php'>Dashboard'a dön</a>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analytics - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-left: 4px solid #007bff;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .conversion-rate {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .bounce-rate {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
        }
        
        .avg-session {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            min-height: 400px;
            max-height: 500px;
        }
        
        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .page-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .page-item:last-child {
            border-bottom: none;
        }
        
        .progress-thin {
            height: 6px;
        }
        
        /* Sayfa yüksekliği kontrolü */
        .main-content {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        canvas {
            max-width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <?php include 'templates/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="row mb-4">
                <div class="col">
                    <div class="analytics-card">
                        <h1 class="mb-0">
                            <i class="fas fa-chart-line me-3"></i>
                            Site Analytics
                        </h1>
                        <p class="mb-0 mt-2 opacity-75">
                            <?php echo date('d M Y', strtotime($startDate)); ?> - <?php echo date('d M Y', strtotime($endDate)); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Tarih Filtreleri -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="filter-card">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filtrele
                                </button>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <a href="?start_date=<?php echo date('Y-m-d'); ?>&end_date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">Bugün</a>
                                    <a href="?start_date=<?php echo date('Y-m-d', strtotime('-7 days')); ?>&end_date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">7 Gün</a>
                                    <a href="?start_date=<?php echo date('Y-m-d', strtotime('-30 days')); ?>&end_date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">30 Gün</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Ana İstatistikler -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($totalStats['total_visitors'] ?? 0); ?></div>
                        <div class="stat-label">Toplam Ziyaretçi</div>
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            <?php echo number_format($totalStats['unique_visitors'] ?? 0); ?> benzersiz
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($totalStats['total_page_views'] ?? 0); ?></div>
                        <div class="stat-label">Sayfa Görüntüleme</div>
                        <small class="text-muted">
                            <i class="fas fa-eye me-1"></i>
                            Avg: <?php echo $totalStats['total_visitors'] > 0 ? number_format($totalStats['total_page_views'] / $totalStats['total_visitors'], 1) : 0; ?> sayfa/ziyaret
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($totalStats['order_button_clicks'] ?? 0); ?></div>
                        <div class="stat-label">Sipariş Butonuna Tıklama</div>
                        <small class="text-muted">
                            <i class="fas fa-mouse-pointer me-1"></i>
                            <?php 
                            $clickRate = $totalStats['total_visitors'] > 0 ? ($totalStats['order_button_clicks'] / $totalStats['total_visitors']) * 100 : 0;
                            echo number_format($clickRate, 1); 
                            ?>% tıklama oranı
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($totalStats['orders_completed'] ?? 0); ?></div>
                        <div class="stat-label">Tamamlanan Sipariş</div>
                        <small class="text-muted">
                            <i class="fas fa-check-circle me-1"></i>
                            <?php echo number_format($totalStats['orders_started'] ?? 0); ?> başlayan sipariş
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Conversion Metrikleri -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card conversion-rate text-white">
                        <div class="stat-number"><?php echo number_format($totalStats['avg_conversion_rate'] ?? 0, 2); ?>%</div>
                        <div class="stat-label text-white-50">Conversion Rate</div>
                        <small class="text-white-75">
                            <i class="fas fa-arrow-up me-1"></i>
                            Ziyaretçi → Sipariş
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bounce-rate text-white">
                        <div class="stat-number"><?php echo number_format($totalStats['avg_bounce_rate'] ?? 0, 1); ?>%</div>
                        <div class="stat-label text-white-50">Bounce Rate</div>
                        <small class="text-white-75">
                            <i class="fas fa-arrow-left me-1"></i>
                            Tek sayfa ziyaretleri
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card avg-session text-white">
                        <div class="stat-number">
                            <?php 
                            $avgDuration = $totalStats['avg_session_duration'] ?? 0;
                            echo gmdate("i:s", $avgDuration); 
                            ?>
                        </div>
                        <div class="stat-label text-white-50">Ortalama Oturum Süresi</div>
                        <small class="text-white-75">
                            <i class="fas fa-clock me-1"></i>
                            dakika:saniye
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Grafikler -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-area me-2"></i>
                            Günlük Ziyaretçi Trendi
                        </h5>
                        <canvas id="visitorsChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-pie me-2"></i>
                            Conversion Funnel
                        </h5>
                        <canvas id="funnelChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Conversion Funnel Detayları -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-funnel-dollar me-2"></i>
                            Sipariş Funnel Analizi
                        </h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Ziyaretçiler</span>
                                <span class="fw-bold"><?php echo number_format($totalStats['total_visitors'] ?? 0); ?></span>
                            </div>
                            <div class="progress progress-thin mb-3">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Sipariş Butonuna Tıklama</span>
                                <span class="fw-bold"><?php echo number_format($totalStats['order_button_clicks'] ?? 0); ?></span>
                            </div>
                            <div class="progress progress-thin mb-3">
                                <div class="progress-bar bg-info" style="width: <?php echo $totalStats['total_visitors'] > 0 ? ($totalStats['order_button_clicks'] / $totalStats['total_visitors']) * 100 : 0; ?>%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Sipariş Başlatma</span>
                                <span class="fw-bold"><?php echo number_format($totalStats['orders_started'] ?? 0); ?></span>
                            </div>
                            <div class="progress progress-thin mb-3">
                                <div class="progress-bar bg-warning" style="width: <?php echo $totalStats['total_visitors'] > 0 ? ($totalStats['orders_started'] / $totalStats['total_visitors']) * 100 : 0; ?>%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Sipariş Tamamlama</span>
                                <span class="fw-bold"><?php echo number_format($totalStats['orders_completed'] ?? 0); ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-success" style="width: <?php echo $totalStats['total_visitors'] > 0 ? ($totalStats['orders_completed'] / $totalStats['total_visitors']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <h5 class="mb-3">
                            <i class="fas fa-list-ol me-2"></i>
                            En Popüler Sayfalar
                        </h5>
                        <?php foreach ($popularPages as $index => $page): ?>
                            <div class="page-item">
                                <div>
                                    <strong><?php echo $index + 1; ?>.</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($page['page_url']); ?></span>
                                </div>
                                <span class="badge bg-primary"><?php echo number_format($page['visits']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug için verileri konsola yazdır
        console.log('Daily Stats:', <?php echo json_encode($dailyStats); ?>);
        console.log('Total Stats:', <?php echo json_encode($totalStats); ?>);
        
        // Ziyaretçi trendi grafiği
        const ctx1 = document.getElementById('visitorsChart').getContext('2d');
        const visitorsChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: [<?php 
                    $labels = array_map(function($stat) {
                        return "'" . date('d M', strtotime($stat['stat_date'])) . "'";
                    }, $dailyStats);
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    label: 'Ziyaretçiler',
                    data: [<?php 
                        $data = array_map(function($stat) {
                            return $stat['total_visitors'];
                        }, $dailyStats);
                        echo implode(',', $data);
                    ?>],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true
                }, {
                    label: 'Siparişler',
                    data: [<?php 
                        $data = array_map(function($stat) {
                            return $stat['orders_completed'];
                        }, $dailyStats);
                        echo implode(',', $data);
                    ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Conversion funnel grafiği
        const ctx2 = document.getElementById('funnelChart').getContext('2d');
        const funnelChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Sipariş Tamamlanan', 'Sipariş Başlayan', 'Sadece Ziyaret'],
                datasets: [{
                    data: [
                        <?php echo $totalStats['orders_completed'] ?? 0; ?>,
                        <?php echo ($totalStats['orders_started'] ?? 0) - ($totalStats['orders_completed'] ?? 0); ?>,
                        <?php echo ($totalStats['total_visitors'] ?? 0) - ($totalStats['orders_started'] ?? 0); ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
