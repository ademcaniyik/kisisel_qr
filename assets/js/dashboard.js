// Dashboard istatistikleri ve grafikleri
document.addEventListener('DOMContentLoaded', function() {
    // İstatistik kartlarını güncelle
    updateStatCards();
    
    // Grafikleri yükle
    loadCharts();
    
    // Her 5 dakikada bir güncelle
    setInterval(updateStatCards, 300000);
});

// İstatistik kartlarını güncelleme
function updateStatCards() {
    fetch('/kisisel_qr/admin/api/get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-profiles').textContent = data.totalProfiles;
            document.getElementById('total-scans').textContent = data.totalScans;
            document.getElementById('today-scans').textContent = data.todayScans;
            document.getElementById('active-profiles').textContent = data.activeProfiles;
        })
        .catch(error => console.error('Hata:', error));
}

// Grafikleri yükleme
function loadCharts() {
    // Tarama İstatistikleri Grafiği
    fetch('/kisisel_qr/admin/api/get_scan_stats.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('scanChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tarama Sayısı',
                        data: data.values,
                        borderColor: 'rgb(75, 192, 192)',
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
        });

    // Cihaz Dağılımı Grafiği
    fetch('/kisisel_qr/admin/api/get_device_stats.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('deviceChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
}

// Tarih formatlama yardımcı fonksiyonu
function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
