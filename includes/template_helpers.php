<?php
// Ortak sayfa başı (head) ve açılış
function renderPageHeader($title, $css = []) {
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?=htmlspecialchars($title)?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <?php foreach($css as $href): ?>
            <link href="<?=htmlspecialchars($href)?>" rel="stylesheet">
        <?php endforeach; ?>
    </head>
    <body>
    <?php
}

// Ortak sayfa sonu (scriptler ve kapanış)
function renderPageFooter($js = []) {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php foreach($js as $src): ?>
        <script src="<?=htmlspecialchars($src)?>"></script>
    <?php endforeach; ?>
    </body>
    </html>
    <?php
}
