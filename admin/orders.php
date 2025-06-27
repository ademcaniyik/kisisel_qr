<?php
session_start();

// Admin giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/OrderManager.php';

try {
    $orderManager = new OrderManager();
    
    // Sayfalama parametreleri
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Filtreleme
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
    
    // Siparişleri getir
    $orders = $orderManager->getAllOrders($limit, $offset, $status);
    $totalCount = $orderManager->getOrderCount($status);
    $totalPages = ceil($totalCount / $limit);
    
    // Dashboard istatistikleri
    $stats = $orderManager->getDashboardStats();
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Yönetimi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .order-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .order-card.pending { border-left: 4px solid #ffc107; }
        .order-card.processing { border-left: 4px solid #0dcaf0; }
        .order-card.completed { border-left: 4px solid #198754; }
        .order-card.cancelled { border-left: 4px solid #dc3545; }
        
        .order-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .order-body {
            padding: 1rem;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .order-meta {
            display: flex;
            justify-content: between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .order-id {
            font-family: monospace;
            font-size: 0.8rem;
            color: #6c757d;
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        
        .customer-name {
            font-weight: 600;
            font-size: 1rem;
            color: #212529;
        }
        
        .order-details {
            font-size: 0.85rem;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.4rem;
            padding: 0.2rem 0;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
        }
        
        .detail-icon {
            width: 16px;
            color: #6c757d;
            margin-right: 0.5rem;
        }
        
        .detail-content {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .profile-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        
        .profile-link:hover {
            text-decoration: underline;
        }
        
        .phone-link {
            color: #198754;
            text-decoration: none;
            font-weight: 500;
        }
        
        .phone-link:hover {
            text-decoration: underline;
        }
        
        .shipping-address {
            background: #f8f9fa;
            border-left: 3px solid #17a2b8;
            padding: 0.5rem;
            border-radius: 0 4px 4px 0;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .price-highlight {
            font-weight: 700;
            color: #198754;
            font-size: 1rem;
        }
        
        .special-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }
        
        .notes-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }
        
        .order-actions {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e9ecef;
        }
        
        .order-actions .btn {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .stats-card.pending { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #8b4513; }
        .stats-card.processing { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #0c4a6e; }
        .stats-card.completed { background: linear-gradient(135deg, #d299c2 0%, #fef9d3 100%); color: #166534; }
        .stats-card.cancelled { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #991b1b; }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .order-meta {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .customer-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }
            
            .order-actions {
                justify-content: center;
            }
            
            .order-actions .btn {
                flex: 1;
                min-width: 0;
                font-size: 0.7rem;
                padding: 0.3rem 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .order-card {
                margin-bottom: 0.75rem;
            }
            
            .order-header {
                padding: 0.5rem 0.75rem;
            }
            
            .order-body {
                padding: 0.75rem;
            }
            
            .order-actions .btn {
                font-size: 0.65rem;
                padding: 0.25rem 0.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'templates/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Sipariş Yönetimi</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="?status=" class="btn btn-sm btn-outline-secondary <?php echo $status === null ? 'active' : ''; ?>">
                                Tümü (<?php echo $stats['total_orders']; ?>)
                            </a>
                            <a href="?status=pending" class="btn btn-sm btn-outline-warning <?php echo $status === 'pending' ? 'active' : ''; ?>">
                                Bekleyen (<?php echo $stats['pending_orders']; ?>)
                            </a>
                            <a href="?status=processing" class="btn btn-sm btn-outline-info <?php echo $status === 'processing' ? 'active' : ''; ?>">
                                İşleniyor (<?php echo $stats['processing_orders']; ?>)
                            </a>
                            <a href="?status=completed" class="btn btn-sm btn-outline-success <?php echo $status === 'completed' ? 'active' : ''; ?>">
                                Tamamlanan (<?php echo $stats['completed_orders']; ?>)
                            </a>
                            <a href="?status=cancelled" class="btn btn-sm btn-outline-danger <?php echo $status === 'cancelled' ? 'active' : ''; ?>">
                                İptal (<?php echo $stats['cancelled_orders']; ?>)
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- İstatistik Kartları -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Toplam Sipariş</h5>
                                <h2 class="mb-0"><?php echo $stats['total_orders']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card pending">
                            <div class="card-body text-center">
                                <h5 class="card-title"><i class="fas fa-clock me-2"></i>Bekleyen</h5>
                                <h2 class="mb-0"><?php echo $stats['pending_orders']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card completed">
                            <div class="card-body text-center">
                                <h5 class="card-title"><i class="fas fa-check-circle me-2"></i>Tamamlanan</h5>
                                <h2 class="mb-0"><?php echo $stats['completed_orders']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card processing">
                            <div class="card-body text-center">
                                <h5 class="card-title"><i class="fas fa-dollar-sign me-2"></i>Toplam Gelir</h5>
                                <h2 class="mb-0"><?php echo number_format($stats['total_revenue'], 2); ?> ₺</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Siparişler Listesi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Siparişler 
                            <?php if ($status): ?>
                                <span class="badge bg-secondary"><?php echo ucfirst($status); ?></span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Henüz sipariş bulunmuyor</h5>
                                <p class="text-muted">Yeni siparişler burda görünecektir.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card <?php echo $order['status']; ?>">
                                    <!-- Order Header -->
                                    <div class="order-header">
                                        <div class="order-meta">
                                            <div class="customer-info">
                                                <span class="customer-name">
                                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($order['customer_name']); ?>
                                                </span>
                                                <span class="badge status-badge bg-<?php 
                                                    echo $order['status'] === 'pending' ? 'warning' : 
                                                        ($order['status'] === 'processing' ? 'info' : 
                                                        ($order['status'] === 'completed' ? 'success' : 'danger')); 
                                                ?>">
                                                    <?php 
                                                    $statusLabels = [
                                                        'pending' => 'Bekliyor',
                                                        'processing' => 'İşleniyor', 
                                                        'completed' => 'Tamamlandı',
                                                        'cancelled' => 'İptal Edildi'
                                                    ];
                                                    echo $statusLabels[$order['status']] ?? $order['status']; 
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="order-id">#<?php echo $order['id']; ?></span>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Body -->
                                    <div class="order-body">
                                        <div class="order-details">
                                            <!-- Phone -->
                                            <div class="detail-row">
                                                <i class="fas fa-phone detail-icon"></i>
                                                <div class="detail-content">
                                                    <a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>" 
                                                       class="phone-link">
                                                        <?php echo htmlspecialchars($order['customer_phone']); ?>
                                                    </a>
                                                    <?php if ($order['whatsapp_sent']): ?>
                                                        <span class="badge bg-success">WhatsApp</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Payment Method -->
                                            <div class="detail-row">
                                                <i class="fas fa-credit-card detail-icon"></i>
                                                <div class="detail-content">
                                                    <span class="badge bg-<?php echo ($order['payment_method'] ?? 'bank_transfer') === 'cash_on_delivery' ? 'warning' : 'primary'; ?>">
                                                        <i class="fas fa-<?php echo ($order['payment_method'] ?? 'bank_transfer') === 'cash_on_delivery' ? 'truck' : 'university'; ?> me-1"></i>
                                                        <?php 
                                                        $paymentMethod = $order['payment_method'] ?? 'bank_transfer';
                                                        echo $paymentMethod === 'cash_on_delivery' ? 'Kapıda Ödeme' : 'Banka Havalesi'; 
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Shipping Address -->
                                            <?php 
                                            // Extract address from special_requests
                                            $shippingAddress = '';
                                            if ($order['special_requests'] && $order['special_requests'] !== '0') {
                                                $lines = explode("\n", $order['special_requests']);
                                                foreach ($lines as $line) {
                                                    $line = trim($line);
                                                    // Look for lines that start with "Adres:"
                                                    if (stripos($line, 'Adres:') === 0) {
                                                        $shippingAddress = trim(substr($line, 6)); // Remove "Adres: " prefix
                                                        break;
                                                    }
                                                    // Also check for lines that might contain address info
                                                    // (long lines that might be addresses)
                                                    if (strlen($line) > 20 && 
                                                        !stripos($line, 'Bio:') === 0 && 
                                                        !stripos($line, 'Sosyal') === 0 &&
                                                        !stripos($line, 'Tema:') === 0 &&
                                                        !stripos($line, 'http') === 0 &&
                                                        !stripos($line, 'www.') === 0) {
                                                        // This might be an address line
                                                        if (empty($shippingAddress)) {
                                                            $shippingAddress = $line;
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            if ($shippingAddress): ?>
                                                <div class="detail-row">
                                                    <i class="fas fa-shipping-fast detail-icon text-info"></i>
                                                    <div class="detail-content">
                                                        <div class="shipping-address">
                                                            <strong class="text-info">Kargo Adresi:</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($shippingAddress)); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Show that no address is available -->
                                                <div class="detail-row">
                                                    <i class="fas fa-shipping-fast detail-icon text-muted"></i>
                                                    <div class="detail-content">
                                                        <div class="shipping-address" style="border-left-color: #6c757d; background: #f8f9fa;">
                                                            <strong class="text-muted">Kargo Adresi:</strong><br>
                                                            <small class="text-muted">Adres bilgisi bulunamadı</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Profile -->
                                            <?php if ($order['profile_slug']): ?>
                                                <div class="detail-row">
                                                    <i class="fas fa-user-circle detail-icon"></i>
                                                    <div class="detail-content">
                                                        <a href="../profile.php?slug=<?php echo htmlspecialchars($order['profile_slug']); ?>" 
                                                           target="_blank" class="profile-link">
                                                            <?php echo htmlspecialchars($order['profile_slug']); ?>
                                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Product & Price -->
                                            <div class="detail-row">
                                                <i class="fas fa-box detail-icon"></i>
                                                <div class="detail-content">
                                                    <span><?php echo htmlspecialchars($order['product_name']); ?></span>
                                                    <span class="text-muted">(<?php echo $order['quantity']; ?> adet)</span>
                                                    <span class="ms-auto price-highlight">
                                                        <?php echo number_format($order['price'], 2); ?> ₺
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Special Requests -->
                                            <?php if ($order['special_requests']): ?>
                                                <div class="special-section">
                                                    <div class="mb-1">
                                                        <i class="fas fa-comment detail-icon"></i>
                                                        <strong>Özel İstekler:</strong>
                                                    </div>
                                                    <div><?php echo nl2br(htmlspecialchars($order['special_requests'])); ?></div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Admin Notes -->
                                            <?php if ($order['notes']): ?>
                                                <div class="notes-section">
                                                    <div class="mb-1">
                                                        <i class="fas fa-sticky-note detail-icon"></i>
                                                        <strong>Admin Notları:</strong>
                                                    </div>
                                                    <div><?php echo nl2br(htmlspecialchars($order['notes'])); ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="order-actions">
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <button class="btn btn-info btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'processing')">
                                                    <i class="fas fa-play me-1"></i>İşleme Al
                                                </button>
                                                <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                                    <i class="fas fa-check me-1"></i>Tamamla
                                                </button>
                                            <?php elseif ($order['status'] === 'processing'): ?>
                                                <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                                    <i class="fas fa-check me-1"></i>Tamamla
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'pending')">
                                                    <i class="fas fa-undo me-1"></i>Bekleyene Al
                                                </button>
                                            <?php elseif ($order['status'] === 'completed'): ?>
                                                <button class="btn btn-warning btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'processing')">
                                                    <i class="fas fa-undo me-1"></i>İşleme Al
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($order['status'] !== 'cancelled'): ?>
                                                <button class="btn btn-danger btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">
                                                    <i class="fas fa-times me-1"></i>İptal
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-secondary btn-sm" onclick="addOrderNote(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-edit me-1"></i>Not
                                            </button>
                                            
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-trash me-1"></i>Sil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Sayfalama -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Sipariş sayfalama">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $status ? '&status='.$status : ''; ?>">Önceki</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status ? '&status='.$status : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $status ? '&status='.$status : ''; ?>">Sonraki</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Not Ekleme Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sipariş Notu Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" id="orderNote" rows="4" placeholder="Sipariş için not ekleyin..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="saveOrderNote()">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentOrderId = null;
        
        async function updateOrderStatus(orderId, newStatus) {
            if (!confirm(`Siparişin durumunu "${getStatusLabel(newStatus)}" olarak değiştirmek istediğinizden emin misiniz?`)) {
                return;
            }
            
            try {
                const response = await fetch(`api/orders-management.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_status',
                        order_id: orderId,
                        status: newStatus
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        }
        
        function addOrderNote(orderId) {
            currentOrderId = orderId;
            document.getElementById('orderNote').value = '';
            new bootstrap.Modal(document.getElementById('noteModal')).show();
        }
        
        async function saveOrderNote() {
            const note = document.getElementById('orderNote').value.trim();
            if (!note) {
                alert('Lütfen bir not yazın.');
                return;
            }
            
            try {
                const response = await fetch(`api/orders-management.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add_note',
                        order_id: currentOrderId,
                        note: note
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        }
        
        async function deleteOrder(orderId) {
            if (!confirm('Bu siparişi kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
                return;
            }
            
            try {
                const response = await fetch(`api/orders-management.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        order_id: orderId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        }
        
        function getStatusLabel(status) {
            const labels = {
                'pending': 'Bekliyor',
                'processing': 'İşleniyor',
                'completed': 'Tamamlandı',
                'cancelled': 'İptal Edildi'
            };
            return labels[status] || status;
        }
    </script>
</body>
</html>
