<?php
/**
 * Dinamik Sitemap.xml Oluşturucu
 * Bu dosya sitemap.xml dosyasını dinamik olarak oluşturur
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Ana sayfa
    echo '  <url>' . "\n";
    echo '    <loc>https://yourdomain.com/</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";
    
    // Profil sayfaları (slug ile)
    $stmt = $connection->prepare("
        SELECT p.slug, p.updated_at, p.created_at, p.is_active
        FROM profiles p 
        WHERE p.is_active = 1 AND p.slug IS NOT NULL AND p.slug != ''
        ORDER BY p.updated_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($profile = $result->fetch_assoc()) {
        $lastmod = $profile['updated_at'] ? date('Y-m-d', strtotime($profile['updated_at'])) : date('Y-m-d', strtotime($profile['created_at']));
        
        echo '  <url>' . "\n";
        echo '    <loc>https://yourdomain.com/profile.php?slug=' . htmlspecialchars($profile['slug']) . '</loc>' . "\n";
        echo '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
        echo '    <changefreq>weekly</changefreq>' . "\n";
        echo '    <priority>0.8</priority>' . "\n";
        echo '  </url>' . "\n";
    }
    
    // QR kodları ile erişilebilen profiller
    $stmt = $connection->prepare("
        SELECT q.id as qr_id, p.updated_at, p.created_at, p.is_active
        FROM qr_codes q
        JOIN profiles p ON q.profile_id = p.id
        WHERE q.is_active = 1 AND p.is_active = 1
        ORDER BY p.updated_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($qr = $result->fetch_assoc()) {
        $lastmod = $qr['updated_at'] ? date('Y-m-d', strtotime($qr['updated_at'])) : date('Y-m-d', strtotime($qr['created_at']));
        
        echo '  <url>' . "\n";
        echo '    <loc>https://yourdomain.com/profile.php?qr_id=' . htmlspecialchars($qr['qr_id']) . '</loc>' . "\n";
        echo '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
        echo '    <changefreq>monthly</changefreq>' . "\n";
        echo '    <priority>0.7</priority>' . "\n";
        echo '  </url>' . "\n";
    }
    
    echo '</urlset>' . "\n";
    
} catch (Exception $e) {
    error_log('Sitemap oluşturma hatası: ' . $e->getMessage());
    http_response_code(500);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url>' . "\n";
    echo '    <loc>https://yourdomain.com/</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";
    echo '</urlset>' . "\n";
}
?>
