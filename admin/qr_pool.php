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

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/QRPoolManager.php';
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
            
        case 'get_qr_list':
            $page = (int)($_POST['page'] ?? 1);
            $limit = (int)($_POST['limit'] ?? 20);
            $status = $_POST['status'] ?? 'all';
            $offset = ($page - 1) * $limit;
            
            $db = Database::getInstance();
            $whereClause = '';
            if ($status !== 'all') {
                $whereClause = "WHERE status = '" . $db->getConnection()->real_escape_string($status) . "'";
            }
            
            $totalQuery = "SELECT COUNT(*) as total FROM qr_pool $whereClause";
            $total = $db->query($totalQuery)->fetch_assoc()['total'];
            
            $qrQuery = "SELECT * FROM qr_pool $whereClause ORDER BY id DESC LIMIT $limit OFFSET $offset";
            $qrs = $db->query($qrQuery)->fetch_all(MYSQLI_ASSOC);
            
            echo json_encode([
                'success' => true,
                'qrs' => $qrs,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            exit();
            
        case 'download_qr_batch':
            $batchId = (int)($_POST['batch_id'] ?? 0);
            if ($batchId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Geçersiz batch ID']);
                exit();
            }
            
            $db = Database::getInstance();
            $qrs = $db->query("SELECT * FROM qr_pool WHERE batch_id = $batchId ORDER BY pool_id")->fetch_all(MYSQLI_ASSOC);
            
            if (empty($qrs)) {
                echo json_encode(['success' => false, 'error' => 'Batch bulunamadı']);
                exit();
            }
            
            // ZIP dosyası oluştur
            $zipPath = $qrPoolManager->createQRBatchZip($batchId);
            
            if ($zipPath) {
                echo json_encode([
                    'success' => true,
                    'download_url' => $zipPath
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'ZIP oluşturulamadı']);
            }
                        case 'update_batch_status':
                            $batchId = (int)($_POST['batch_id'] ?? 0);
                            $status = $_POST['status'] ?? '';
                            $result = $qrPoolManager->updateBatchStatus($batchId, $status);
                            echo json_encode($result);
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

                <!-- QR Pool Detayları -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="mb-0"><i class="fas fa-list me-2"></i>QR Listesi</h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="loadQRList('all')">Tümü</button>
                                    <button class="btn btn-outline-success" onclick="loadQRList('available')">Müsait</button>
                                    <button class="btn btn-outline-warning" onclick="loadQRList('assigned')">Atanmış</button>
                                    <button class="btn btn-outline-info" onclick="loadQRList('delivered')">Teslim</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="qrListContainer">
                                    <div class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                        <p class="mt-2">QR'lar yükleniyor...</p>
                                    </div>
                                </div>
                                <nav id="qrPagination" class="mt-3"></nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Hızlı İşlemler</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" onclick="showCreateBatchModal()">
                                        <i class="fas fa-plus me-2"></i>Yeni Batch Oluştur
                                    </button>
                                    <button class="btn btn-info" onclick="showQRGeneratorModal()">
                                        <i class="fas fa-qrcode me-2"></i>Tekil QR Oluştur
                                    </button>
                                    <button class="btn btn-success" onclick="exportAllQRs()">
                                        <i class="fas fa-download me-2"></i>Tüm QR'ları İndir
                                    </button>
                                    <button class="btn btn-warning" onclick="showBulkOperationsModal()">
                                        <i class="fas fa-cogs me-2"></i>Toplu İşlemler
                                    </button>
                                </div>
                                
                                <hr>
                                
                                <h6><i class="fas fa-chart-pie me-2"></i>Anlık İstatistikler</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between">
                                        <span>Müsait QR:</span>
                                        <span class="text-success" id="availableCount"><?= $stockStatus['available'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Atanmış QR:</span>
                                        <span class="text-warning" id="assignedCount"><?= $stockStatus['assigned'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Teslim Edilmiş:</span>
                                        <span class="text-info" id="deliveredCount"><?= $stockStatus['delivered'] ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Toplam:</span>
                                        <span id="totalCount"><?= $stockStatus['total'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batch Listesi -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Batch Yönetimi</h5>
                        <button class="btn btn-sm btn-primary" onclick="refreshBatches()">
                            <i class="fas fa-sync me-1"></i>Yenile
                        </button>
                    </div>
                    <div class="card-body"><?php if (empty($batches)): ?>
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
                                    <tbody id="batchTableBody">
                                        <?php foreach ($batches as $batch): ?>
                                            <tr id="batch-<?= $batch['id'] ?>">
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
                                                        $statusText = [
                                                            'planned' => 'Planlandı',
                                                            'ready_to_print' => 'Basıma Hazır',
                                                            'printed' => 'Basıldı',
                                                            'stocked' => 'Stokta'
                                                        ];
                                                        echo $statusText[$batch['status']] ?? $batch['status'];
                                                        ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y H:i', strtotime($batch['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" 
                                                                onclick="viewBatchDetails(<?= $batch['id'] ?>)"
                                                                title="Detayları Görüntüle">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-success" 
                                                                onclick="downloadBatchQRs(<?= $batch['id'] ?>)"
                                                                title="QR'ları İndir">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <?php if ($batch['status'] !== 'stocked'): ?>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-warning dropdown-toggle" 
                                                                        data-bs-toggle="dropdown" 
                                                                        title="Durum Değiştir">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" onclick="updateBatchStatus(<?= $batch['id'] ?>, 'ready_to_print')">Basıma Hazır</a></li>
                                                                    <li><a class="dropdown-item" onclick="updateBatchStatus(<?= $batch['id'] ?>, 'printed')">Basıldı</a></li>
                                                                    <li><a class="dropdown-item" onclick="updateBatchStatus(<?= $batch['id'] ?>, 'stocked')">Stokta</a></li>
                                                                </ul>
                                                            </div>
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

    <!-- Batch Detayları Modal -->
    <div class="modal fade" id="batchDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batch Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="batchDetailsContent">
                    <!-- Dinamik içerik -->
                </div>
            </div>
        </div>
    </div>

    <!-- Toplu İşlemler Modal -->
    <div class="modal fade" id="bulkOperationsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Toplu İşlemler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Toplu işlemler geri alınamaz. Dikkatli olun!
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İşlem Türü</label>
                        <select class="form-select" id="bulkOperation">
                            <option value="">İşlem seçin...</option>
                            <option value="reset_available">Tüm atanmış QR'ları müsait yap</option>
                            <option value="mark_delivered">Tüm atanmış QR'ları teslim edildi yap</option>
                            <option value="regenerate_codes">Edit kodlarını yeniden oluştur</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Filtre</label>
                        <select class="form-select" id="bulkFilter">
                            <option value="all">Tümü</option>
                            <option value="available">Sadece müsait QR'lar</option>
                            <option value="assigned">Sadece atanmış QR'lar</option>
                            <option value="delivered">Sadece teslim edilmiş QR'lar</option>
                        </select>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmBulkOperation">
                        <label class="form-check-label" for="confirmBulkOperation">
                            Bu işlemi yapmak istediğimden eminim
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-warning" onclick="executeBulkOperation()">
                        <i class="fas fa-cogs me-2"></i>İşlemi Yürüt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let currentStatus = 'all';

        // Sayfa yüklendiğinde QR listesini getir
        document.addEventListener('DOMContentLoaded', function() {
            loadQRList('all');
        });

        function showCreateBatchModal() {
            new bootstrap.Modal(document.getElementById('createBatchModal')).show();
        }

        function showBulkOperationsModal() {
            new bootstrap.Modal(document.getElementById('bulkOperationsModal')).show();
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

        async function loadQRList(status = 'all', page = 1) {
            currentStatus = status;
            currentPage = page;
            
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=get_qr_list&status=${status}&page=${page}&limit=20`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    displayQRList(result.qrs);
                    displayPagination(result.pagination);
                } else {
                    document.getElementById('qrListContainer').innerHTML = 
                        '<div class="alert alert-danger">QR listesi yüklenemedi: ' + result.error + '</div>';
                }
            } catch (error) {
                document.getElementById('qrListContainer').innerHTML = 
                    '<div class="alert alert-danger">Bir hata oluştu: ' + error.message + '</div>';
            }
        }

        function displayQRList(qrs) {
            const container = document.getElementById('qrListContainer');
            
            if (qrs.length === 0) {
                container.innerHTML = '<div class="text-center py-4"><p>Bu filtrerede QR bulunamadı.</p></div>';
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead><tr><th>Pool ID</th><th>QR Code ID</th><th>Durum</th><th>Batch</th><th>Profil</th><th>İşlemler</th></tr></thead><tbody>';
            
            qrs.forEach(qr => {
                const statusBadge = getStatusBadge(qr.status);
                const profileInfo = qr.profile_id ? `ID: ${qr.profile_id}` : 'Atanmamış';
                
                html += `<tr>
                    <td><code>${qr.pool_id}</code></td>
                    <td><code>${qr.qr_code_id}</code></td>
                    <td>${statusBadge}</td>
                    <td><small>${qr.batch_name || 'N/A'}</small></td>
                    <td><small>${profileInfo}</small></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-sm" onclick="viewQRDetails('${qr.qr_code_id}')" title="Detaylar">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyQRUrl('${qr.qr_code_id}')" title="URL Kopyala">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        function getStatusBadge(status) {
            const badges = {
                'available': '<span class="badge bg-success">Müsait</span>',
                'assigned': '<span class="badge bg-warning">Atanmış</span>',
                'delivered': '<span class="badge bg-info">Teslim</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function displayPagination(pagination) {
            const container = document.getElementById('qrPagination');
            
            if (pagination.pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<nav><ul class="pagination pagination-sm justify-content-center">';
            
            // Önceki sayfa
            if (pagination.page > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadQRList('${currentStatus}', ${pagination.page - 1})">Önceki</a>
                </li>`;
            }
            
            // Sayfa numaraları
            for (let i = Math.max(1, pagination.page - 2); i <= Math.min(pagination.pages, pagination.page + 2); i++) {
                const active = i === pagination.page ? 'active' : '';
                html += `<li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="loadQRList('${currentStatus}', ${i})">${i}</a>
                </li>`;
            }
            
            // Sonraki sayfa
            if (pagination.page < pagination.pages) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadQRList('${currentStatus}', ${pagination.page + 1})">Sonraki</a>
                </li>`;
            }
            
            html += '</ul></nav>';
            container.innerHTML = html;
        }

        async function downloadBatchQRs(batchId) {
            if (!confirm('Bu batch\'teki tüm QR\'ları indirmek istediğinizden emin misiniz?')) {
                return;
            }
            
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=download_qr_batch&batch_id=${batchId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // ZIP dosyasını indir
                    const link = document.createElement('a');
                    link.href = result.download_url;
                    link.download = '';
                    link.click();
                    
                    alert('QR batch ZIP dosyası hazırlandı ve indiriliyor...');
                } else {
                    alert('Hata: ' + result.error);
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        }

        async function updateBatchStatus(batchId, status) {
            if (!confirm('Batch durumunu güncellemek istediğinizden emin misiniz?')) {
                return;
            }
            
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=update_batch_status&batch_id=${batchId}&status=${status}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Batch durumu güncellendi!');
                    location.reload();
                } else {
                    alert('Hata: ' + result.error);
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        }

        function viewBatchDetails(batchId) {
            // Batch detaylarını modal'da göster
            const modal = new bootstrap.Modal(document.getElementById('batchDetailsModal'));
            const content = document.getElementById('batchDetailsContent');
            
            content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Detaylar yükleniyor...</p></div>';
            modal.show();
            
            setTimeout(() => {
                // Simüle edilmiş veri
                const batch = {
                    id: batchId,
                    name: 'Batch ' + batchId,
                    startId: 1000 + batchId,
                    endId: 1100 + batchId,
                    quantity: 100,
                    status: 'ready_to_print',
                    createdAt: '2023-10-01 12:00',
                    updatedAt: '2023-10-02 15:30',
                    qrCodes: [
                        {id: 1, code: 'QR1-' + batchId, status: 'available'},
                        {id: 2, code: 'QR2-' + batchId, status: 'assigned'},
                        {id: 3, code: 'QR3-' + batchId, status: 'delivered'}
                    ]
                };
                
                let html = `<h5>${batch.name} - Detaylar</h5>`;
                html += `<p><strong>QR Aralığı:</strong> ${batch.startId} - ${batch.endId}</p>`;
                html += `<p><strong>Miktar:</strong> ${batch.quantity}</p>`;
                html += `<p><strong>Durum:</strong> ${batch.status}</p>`;
                html += `<p><strong>Oluşturma Tarihi:</strong> ${batch.createdAt}</p>`;
                html += `<p><strong>Son Güncelleme:</strong> ${batch.updatedAt}</p>`;
                
                html += `<h6 class="mt-4">QR Kodları</h6>`;
                html += `<div class="table-responsive">`;
                html += `<table class="table table-sm table-hover">`;
                html += `<thead><tr><th>QR Kod ID</th><th>QR Kodu</th><th>Durum</th></tr></thead>`;
                html += `<tbody>`;
                
                batch.qrCodes.forEach(qr => {
                    html += `<tr>
                        <td><code>${qr.id}</code></td>
                        <td><code>${qr.code}</code></td>
                        <td>${getStatusBadge(qr.status)}</td>
                    </tr>`;
                });
                
                html += `</tbody></table></div>`;
                
                content.innerHTML = html;
            }, 1000);
        }

        function viewQRDetails(qrCodeId) {
            const profileUrl = `${window.location.origin}/qr/${qrCodeId}`;
            window.open(profileUrl, '_blank');
        }

        function copyQRUrl(qrCodeId) {
            const url = `${window.location.origin}/qr/${qrCodeId}`;
            navigator.clipboard.writeText(url).then(() => {
                alert('QR URL kopyalandı: ' + url);
            });
        }

        function exportAllQRs() {
            alert('Tüm QR\'ların dışa aktarma özelliği yakında geliştirilecek...');
        }

        function showQRGeneratorModal() {
            alert('Tekil QR oluşturma özelliği yakında geliştirilecek...');
        }

        function executeBulkOperation() {
            alert('Toplu işlemler yakında geliştirilecek...');
        }

        function refreshBatches() {
            location.reload();
        }

        // Anlık stok durumu güncellemesi
        setInterval(async function() {
            try {
                const response = await fetch('qr_pool.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=get_stock_status'
                });
                const status = await response.json();
                
                document.getElementById('availableCount').textContent = status.available;
                document.getElementById('assignedCount').textContent = status.assigned;
                document.getElementById('deliveredCount').textContent = status.delivered;
                document.getElementById('totalCount').textContent = status.total;
                
            } catch (error) {
                console.error('Stok durumu güncellenirken hata:', error);
            }
        }, 30000); // 30 saniyede bir güncelle
    </script>
</body>
</html>
