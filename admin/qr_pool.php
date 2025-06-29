<?php
/**
 * QR Pool Yönetim Sayfası
 * Admin panelinde QR havuzu yönetimi
 */

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../includes/QRPoolManager.php';
$qrPoolManager = new QRPoolManager();

// Stok durumunu al
$stockStatus = $qrPoolManager->getStockStatus();
$batches = $qrPoolManager->getBatches();

// Ajax istekleri için
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_batch':
            $quantity = (int)($_POST['quantity'] ?? 100);
            $result = $qrPoolManager->createNewBatch($quantity);
            echo json_encode($result);
            exit();
            
        case 'get_stock_status':
            echo json_encode($qrPoolManager->getStockStatus());
            exit();
            
        default:
            echo json_encode(['success' => false, 'error' => 'Bilinmeyen işlem']);
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Pool Yönetimi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .stock-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stock-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .batch-card {
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-planned { background-color: #fef3c7; color: #92400e; }
        .status-ready_to_print { background-color: #dbeafe; color: #1e40af; }
        .status-printed { background-color: #d1fae5; color: #065f46; }
        .status-stocked { background-color: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'templates/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-layer-group me-2"></i>QR Pool Yönetimi</h1>
                    <button class="btn btn-primary" onclick="showCreateBatchModal()">
                        <i class="fas fa-plus me-2"></i>Yeni Batch Oluştur
                    </button>
                </div>

                <!-- Stok Durumu Kartları -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stock-card">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-cubes fa-2x"></i>
                                </div>
                                <div>
                                    <div class="stock-number"><?= $stockStatus['total'] ?></div>
                                    <div>Toplam QR</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stock-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div>
                                    <div class="stock-number"><?= $stockStatus['available'] ?></div>
                                    <div>Müsait QR</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stock-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-user-tag fa-2x"></i>
                                </div>
                                <div>
                                    <div class="stock-number"><?= $stockStatus['assigned'] ?></div>
                                    <div>Atanmış QR</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stock-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-shipping-fast fa-2x"></i>
                                </div>
                                <div>
                                    <div class="stock-number"><?= $stockStatus['delivered'] ?></div>
                                    <div>Teslim Edilmiş</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Uyarısı -->
                <?php if ($stockStatus['stock_warning']): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Stok Uyarısı!</strong> Müsait QR sayısı 20'nin altına düştü. Yeni batch hazırlamanız önerilir.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Batch Listesi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Batch Listesi</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($batches)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Henüz hiç batch oluşturulmamış.</p>
                                <button class="btn btn-primary" onclick="showCreateBatchModal()">
                                    İlk Batch'i Oluştur
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Batch Adı</th>
                                            <th>QR Aralığı</th>
                                            <th>Miktar</th>
                                            <th>Durum</th>
                                            <th>Oluşturma Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batches as $batch): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($batch['batch_name']) ?></strong></td>
                                                <td>
                                                    <code><?= htmlspecialchars($batch['pool_start_id']) ?></code>
                                                    -
                                                    <code><?= htmlspecialchars($batch['pool_end_id']) ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= $batch['quantity'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-<?= $batch['status'] ?>">
                                                        <?php
                                                        switch ($batch['status']) {
                                                            case 'planned': echo 'Planlandı'; break;
                                                            case 'ready_to_print': echo 'Basıma Hazır'; break;
                                                            case 'printed': echo 'Basıldı'; break;
                                                            case 'stocked': echo 'Stokta'; break;
                                                            default: echo $batch['status'];
                                                        }
                                                        ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y H:i', strtotime($batch['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="viewBatch(<?= $batch['id'] ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($batch['status'] !== 'stocked'): ?>
                                                            <button class="btn btn-outline-success" onclick="updateBatchStatus(<?= $batch['id'] ?>, 'printed')">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yeni Batch Modal -->
    <div class="modal fade" id="createBatchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni QR Batch Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createBatchForm">
                        <div class="mb-3">
                            <label for="batchQuantity" class="form-label">QR Miktarı</label>
                            <input type="number" class="form-control" id="batchQuantity" value="100" min="50" max="500">
                            <div class="form-text">Önerilen miktar: 100 QR</div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Yeni batch oluşturulduktan sonra fiziksel QR sticker'larını bastırmayı unutmayın.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="createBatch()">
                        <i class="fas fa-plus me-2"></i>Batch Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showCreateBatchModal() {
            new bootstrap.Modal(document.getElementById('createBatchModal')).show();
        }

        async function createBatch() {
            const quantity = document.getElementById('batchQuantity').value;
            
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=create_batch&quantity=${quantity}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Batch başarıyla oluşturuldu: ' + result.batch_name);
                    location.reload();
                } else {
                    alert('Hata: ' + result.error);
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        }

        function viewBatch(batchId) {
            // Batch detaylarını göster
            alert('Batch detayları geliştirilecek...');
        }

        function updateBatchStatus(batchId, status) {
            if (confirm('Batch durumunu güncellemek istediğinizden emin misiniz?')) {
                // Batch durumu güncelleme
                alert('Durum güncelleme geliştirilecek...');
            }
        }

        // Sayfa yüklendiğinde stok durumunu kontrol et
        setInterval(async function() {
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=get_stock_status'
                });
                const status = await response.json();
                
                // Stok durumu güncellemesi (DOM manipülasyonu)
                document.querySelector('.stock-number').textContent = status.total;
                // Diğer stock number'ları da güncellenebilir
                
            } catch (error) {
                console.error('Stok durumu güncellenirken hata:', error);
            }
        }, 30000); // 30 saniyede bir güncelle
    </script>
</body>
</html>
