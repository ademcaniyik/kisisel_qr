<?php
// logs/admin_actions.log görüntüleyici
session_start();
require_once __DIR__ . '/../includes/utilities.php';
Utilities::requireLogin();

$logFile = __DIR__ . '/../logs/admin_actions.log';
$logs = [];
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($lines) as $line) {
        $logs[] = $line;
    }
}
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin İşlem Logları</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr_canli/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr_canli/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr_canli/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr_canli/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Admin İşlem Logları</h2>
    <div class="card">
        <div class="card-body" style="max-height:500px; overflow:auto; font-family:monospace;">
            <?php if (empty($logs)): ?>
                <div class="alert alert-info">Kayıtlı log bulunamadı.</div>
            <?php else: ?>
                <ul class="list-group">
                <?php foreach ($logs as $log): ?>
                    <li class="list-group-item py-1 px-2"><?php echo htmlspecialchars($log); ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
