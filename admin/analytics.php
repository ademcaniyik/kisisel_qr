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
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-container {
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #007cba;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .date-filter {
            margin-bottom: 20px;
        }
        .date-filter input {
            margin: 0 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .date-filter button {
            padding: 5px 15px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'templates/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="analytics-container">
                <h1>Site Analitikleri</h1>
                
                <!-- Tarih Filtresi -->
                <div class="date-filter">
                    <form method="GET">
                        <label>Başlangıç Tarihi:</label>
                        <input type="date" name="start_date" value="<?= $startDate ?>">
                        <label>Bitiş Tarihi:</label>
                        <input type="date" name="end_date" value="<?= $endDate ?>">
                        <button type="submit">Filtrele</button>
                    </form>
                </div>
                
                <!-- Özet İstatistikler -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Toplam Ziyaretçi</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['total_visitors'] ?? 0) ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Benzersiz Ziyaretçi</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['unique_visitors'] ?? 0) ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Toplam Sayfa Görüntüleme</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['total_page_views'] ?? 0) ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Sipariş Butonu Tıklama</h3>
                        <div class="number"><?= number_format($dashboardData['total_stats']['order_button_clicks'] ?? 0) ?></div>
                    </div>
                </div>
                
                <!-- Günlük İstatistikler Grafiği -->
                <div class="chart-container">
                    <h2>Günlük Ziyaretçi Trendi</h2>
                    <canvas id="dailyVisitorsChart" width="400" height="150"></canvas>
                </div>
                
                <!-- Sipariş Funnel Grafiği -->
                <div class="chart-container">
                    <h2>Sipariş Butonu Tıklama Trendi</h2>
                    <canvas id="orderFunnelChart" width="400" height="150"></canvas>
                </div>
                
                <!-- Detaylı Tablo -->
                <div class="chart-container">
                    <h2>Günlük Detaylar</h2>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 10px; border: 1px solid #ddd;">Tarih</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Ziyaretçi</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Sayfa Görüntüleme</th>
                                <th style="padding: 10px; border: 1px solid #ddd;">Sipariş Butonu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dashboardData['daily_stats'] as $day): ?>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= $day['stat_date'] ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($day['total_visitors']) ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($day['total_page_views']) ?></td>
                                <td style="padding: 10px; border: 1px solid #ddd;"><?= number_format($day['order_button_clicks']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Günlük ziyaretçi grafiği
        const dailyData = <?= json_encode($dashboardData['daily_stats']) ?>;
        
        if (dailyData.length > 0) {
            const dailyLabels = dailyData.map(d => d.stat_date);
            const dailyVisitors = dailyData.map(d => parseInt(d.total_visitors));
            
            const dailyCtx = document.getElementById('dailyVisitorsChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Günlük Ziyaretçi',
                        data: dailyVisitors,
                        borderColor: '#007cba',
                        backgroundColor: 'rgba(0, 124, 186, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
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
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
