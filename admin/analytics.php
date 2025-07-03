<?php
session_start();

// Admin yetkisi kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

require_once '../includes/AnalyticsManager.php';

// Analytics manager
$analytics = new AnalyticsManager();

// Günlük istatistikleri otomatik güncelle (dashboard yüklendiğinde)
$analytics->autoUpdateDailyStats();

// Tarih aralığı
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Dashboard verilerini al
$dashboardData = $analytics->getDashboardData($startDate, $endDate);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analitikleri - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-container {
            padding: 25px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .page-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .date-filter-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
        }
        
        .date-filter-card h5 {
            color: #5a5c69;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .form-control, .btn {
            border-radius: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.visitors::before {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        
        .stat-card.unique::before {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }
        
        .stat-card.pageviews::before {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }
        
        .stat-card.orders::before {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
        }
        
        .stat-card.visitors .stat-icon {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        
        .stat-card.unique .stat-icon {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }
        
        .stat-card.pageviews .stat-icon {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }
        
        .stat-card.orders .stat-icon {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }
        
        .stat-card h3 {
            margin: 0 0 15px 0;
            color: #5a5c69;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stat-card .change {
            font-size: 14px;
            color: #858796;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
        }
        
        .chart-container h2 {
            color: #5a5c69;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }
        
        .chart-wrapper {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
            overflow-x: auto;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .custom-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            font-weight: 600;
            text-align: center;
            border: none;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .custom-table thead th:first-child {
            border-top-left-radius: 10px;
        }
        
        .custom-table thead th:last-child {
            border-top-right-radius: 10px;
        }
        
        .custom-table tbody td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 500;
            color: #5a5c69;
        }
        
        .custom-table tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #858796;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .analytics-container {
                padding: 15px;
            }
            
            .page-header {
                padding: 20px;
                text-align: center;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .chart-wrapper {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'templates/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="analytics-container">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="fas fa-chart-line me-3"></i>Site Analitikleri</h1>
                    <p>Sitenizin performansını ve kullanıcı davranışlarını detaylı olarak analiz edin</p>
                </div>
                
                <!-- Tarih Filtresi -->
                <div class="date-filter-card">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Tarih Aralığı Seçin</h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filtrele
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Özet İstatistikler -->
                <div class="stats-grid">
                    <div class="stat-card visitors">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Toplam Ziyaretçi</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['total_visitors'] ?? 0) ?></div>
                        <div class="change">Son <?= (strtotime($endDate) - strtotime($startDate)) / 86400 + 1 ?> gün</div>
                    </div>
                    <div class="stat-card unique">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3>Benzersiz Ziyaretçi</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['unique_visitors'] ?? 0) ?></div>
                        <div class="change">Tekrarsız kullanıcı</div>
                    </div>
                    <div class="stat-card pageviews">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Sayfa Görüntüleme</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['total_page_views'] ?? 0) ?></div>
                        <div class="change">Toplam görüntüleme</div>
                    </div>
                    <div class="stat-card orders">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Sipariş Tıklama</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['order_button_clicks'] ?? 0) ?></div>
                        <div class="change">Sipariş butonu tıklama</div>
                    </div>
                </div>
                
                <!-- Grafik Satırı -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h2><i class="fas fa-chart-area me-2"></i>Günlük Ziyaretçi Trendi</h2>
                            <div class="chart-wrapper">
                                <canvas id="dailyVisitorsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <h2><i class="fas fa-chart-bar me-2"></i>Sipariş Butonu Tıklama</h2>
                            <div class="chart-wrapper">
                                <canvas id="orderFunnelChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detaylı Tablo -->
                <div class="table-container">
                    <h2><i class="fas fa-table me-2"></i>Günlük Detaylar</h2>
                    <?php if (count($dashboardData['daily_stats']) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-day me-2"></i>Tarih</th>
                                <th><i class="fas fa-users me-2"></i>Ziyaretçi</th>
                                <th><i class="fas fa-eye me-2"></i>Sayfa Görüntüleme</th>
                                <th><i class="fas fa-shopping-cart me-2"></i>Sipariş Tıklama</th>
                                <th><i class="fas fa-clock me-2"></i>Ort. Süre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dashboardData['daily_stats'] as $day): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($day['stat_date'])) ?></td>
                                <td><?= number_format($day['total_visitors']) ?></td>
                                <td><?= number_format($day['total_page_views']) ?></td>
                                <td><?= number_format($day['order_button_clicks']) ?></td>
                                <td><?= number_format($day['avg_session_duration']) ?>s</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-chart-line fa-3x mb-3" style="color: #ddd;"></i>
                        <p>Seçilen tarih aralığında veri bulunamadı.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Günlük ziyaretçi grafiği
        const dailyData = <?= json_encode($dashboardData['daily_stats']) ?>;
        
        if (dailyData.length > 0) {
            const dailyLabels = dailyData.map(d => {
                const date = new Date(d.stat_date);
                return date.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' });
            });
            const dailyVisitors = dailyData.map(d => parseInt(d.total_visitors));
            
            const dailyCtx = document.getElementById('dailyVisitorsChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Günlük Ziyaretçi',
                        data: dailyVisitors,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                color: '#858796'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#858796'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            
            // Sipariş butonu tıklama grafiği
            const orderClicks = dailyData.map(d => parseInt(d.order_button_clicks));
            
            const orderCtx = document.getElementById('orderFunnelChart').getContext('2d');
            new Chart(orderCtx, {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Sipariş Butonu Tıklama',
                        data: orderClicks,
                        backgroundColor: 'rgba(246, 194, 62, 0.8)',
                        borderColor: '#f6c23e',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                color: '#858796'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#858796'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        } else {
            // Veri yoksa placeholder göster
            ['dailyVisitorsChart', 'orderFunnelChart'].forEach(chartId => {
                const ctx = document.getElementById(chartId).getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Veri Yok'],
                        datasets: [{
                            label: 'Veri Bulunamadı',
                            data: [0],
                            borderColor: '#ddd',
                            backgroundColor: 'rgba(221,221,221,0.1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        }
        
        // Sayfa yüklendiğinde animasyon
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 150);
            });
        });
    </script>
</body>
</html>
