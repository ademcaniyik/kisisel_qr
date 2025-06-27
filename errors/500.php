<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunucu Hatası</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/kisisel_qr_canli/assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/kisisel_qr_canli/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/kisisel_qr_canli/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/kisisel_qr_canli/assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">
    
    <link href="/kisisel_qr_canli/assets/css/error.css" rel="stylesheet">
</head>
<body>
    <div class="error-code">500</div>
    <h1>Geçici Bir Sorun Oluştu</h1>
    <p>Sistem şu anda bakımda. Kısa süre içinde normale dönecektir.</p>
    <p>Sorun devam ederse, lütfen daha sonra tekrar deneyin.</p>
    <div class="error-details">
        <p><small>Hata ID: <?php echo substr(md5(time()), 0, 8); ?></small></p>
        <p><small>Zaman: <?php echo date('Y-m-d H:i:s'); ?></small></p>
    </div>
    <a href="/" class="btn-home">Ana Sayfaya Dön</a>
</body>
</html>
