<?php
/**
 * CSS Minification and Optimization Tool
 * Optimizes CSS files for better performance
 */

class CSSMinifier {
    
    private $sourceDir = '../assets/css/';
    private $outputDir = '../assets/css/min/';
    
    // Critical CSS patterns (above-the-fold)
    private $criticalPatterns = [
        'navbar', 'hero', 'glassmorphism', 'navigation', 'header',
        'body', 'font', ':root', '*', 'html'
    ];
    
    public function __construct() {
        // Fix paths for the build directory context
        $this->sourceDir = realpath(dirname(__FILE__) . '/' . $this->sourceDir) . '/';
        $this->outputDir = dirname(__FILE__) . '/' . $this->outputDir;
        
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Minify CSS content
     */
    public function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove whitespace around specific characters
        $css = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;', ', ', ' ,'], 
                          ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $css);
        
        // Remove last semicolon before closing brace
        $css = preg_replace('/;+}/', '}', $css);
        
        // Optimize colors
        $css = preg_replace('/#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3/i', '#$1$2$3', $css);
        
        // Remove leading zeros
        $css = preg_replace('/([\s:,]0+\.?)([0-9]*[a-z%]+)/i', '$1.$2', $css);
        
        // Remove trailing zeros
        $css = preg_replace('/([0-9])\.?0+(px|em|rem|%|pt|pc|in|mm|cm|ex)/i', '$1$2', $css);
        
        // Convert 0px to 0
        $css = preg_replace('/\b0(px|em|rem|%|pt|pc|in|mm|cm|ex)\b/', '0', $css);
        
        return trim($css);
    }
    
    /**
     * Extract critical CSS
     */
    public function extractCriticalCSS($css) {
        $lines = explode('}', $css);
        $critical = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            foreach ($this->criticalPatterns as $pattern) {
                if (stripos($line, $pattern) !== false) {
                    $critical .= $line . '}';
                    break;
                }
            }
        }
        
        return $critical;
    }
    
    /**
     * Create CSS bundles
     */
    public function createBundles() {
        $results = [];
        
        // 1. Critical CSS Bundle (inline)
        $criticalFiles = ['landing.css'];
        $criticalCSS = '';
        
        foreach ($criticalFiles as $file) {
            $path = $this->sourceDir . $file;
            if (file_exists($path)) {
                $css = file_get_contents($path);
                $criticalCSS .= $this->extractCriticalCSS($css);
            }
        }
        
        $criticalMinified = $this->minifyCSS($criticalCSS);
        file_put_contents($this->outputDir . 'critical.min.css', $criticalMinified);
        $results['critical'] = [
            'original' => strlen($criticalCSS),
            'minified' => strlen($criticalMinified),
            'saved' => strlen($criticalCSS) - strlen($criticalMinified)
        ];
        
        // 2. Main CSS Bundle (defer)
        $mainFiles = ['landing.css', 'social-buttons.css', 'whatsapp-widget.css'];
        $mainCSS = '';
        
        foreach ($mainFiles as $file) {
            $path = $this->sourceDir . $file;
            if (file_exists($path)) {
                $css = file_get_contents($path);
                // Remove critical parts already included
                $nonCritical = str_replace($this->extractCriticalCSS($css), '', $css);
                $mainCSS .= $nonCritical;
            }
        }
        
        $mainMinified = $this->minifyCSS($mainCSS);
        file_put_contents($this->outputDir . 'main.min.css', $mainMinified);
        $results['main'] = [
            'original' => strlen($mainCSS),
            'minified' => strlen($mainMinified),
            'saved' => strlen($mainCSS) - strlen($mainMinified)
        ];
        
        // 3. Page-specific Bundle (lazy)
        $pageFiles = ['profile-themes.css', 'profile-page.css', 'profile-edit.css', 'image-enhancements.css'];
        $pageCSS = '';
        
        foreach ($pageFiles as $file) {
            $path = $this->sourceDir . $file;
            if (file_exists($path)) {
                $pageCSS .= file_get_contents($path);
            }
        }
        
        $pageMinified = $this->minifyCSS($pageCSS);
        file_put_contents($this->outputDir . 'page-specific.min.css', $pageMinified);
        $results['page-specific'] = [
            'original' => strlen($pageCSS),
            'minified' => strlen($pageMinified),
            'saved' => strlen($pageCSS) - strlen($pageMinified)
        ];
        
        // 4. Individual minified files
        $allFiles = glob($this->sourceDir . '*.css');
        foreach ($allFiles as $file) {
            $filename = basename($file);
            $css = file_get_contents($file);
            $minified = $this->minifyCSS($css);
            
            $minFilename = str_replace('.css', '.min.css', $filename);
            file_put_contents($this->outputDir . $minFilename, $minified);
            
            $results['individual'][$filename] = [
                'original' => strlen($css),
                'minified' => strlen($minified),
                'saved' => strlen($css) - strlen($minified)
            ];
        }
        
        return $results;
    }
    
    /**
     * Generate optimized HTML includes
     */
    public function generateHTMLIncludes() {
        $critical = file_get_contents($this->outputDir . 'critical.min.css');
        
        $html = "<!-- Optimized CSS Loading -->\n";
        $html .= "<!-- Critical CSS (inline for instant render) -->\n";
        $html .= "<style>\n" . $critical . "\n</style>\n\n";
        
        $html .= "<!-- Main CSS (preload + defer) -->\n";
        $html .= '<link rel="preload" href="assets/css/min/main.min.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        $html .= '<noscript><link rel="stylesheet" href="assets/css/min/main.min.css"></noscript>' . "\n\n";
        
        $html .= "<!-- Page-specific CSS (lazy load) -->\n";
        $html .= '<link rel="stylesheet" href="assets/css/min/page-specific.min.css" media="print" onload="this.media=\'all\'">' . "\n";
        
        file_put_contents($this->outputDir . 'optimized-includes.html', $html);
        
        return $html;
    }
    
    /**
     * Run optimization
     */
    public function optimize() {
        echo "🚀 Starting CSS Optimization...\n\n";
        
        $results = $this->createBundles();
        $htmlIncludes = $this->generateHTMLIncludes();
        
        // Calculate total savings
        $totalOriginal = 0;
        $totalMinified = 0;
        
        foreach ($results as $bundleName => $bundle) {
            if ($bundleName === 'individual') {
                foreach ($bundle as $file => $stats) {
                    $totalOriginal += $stats['original'];
                    $totalMinified += $stats['minified'];
                }
            } else {
                $totalOriginal += $bundle['original'];
                $totalMinified += $bundle['minified'];
            }
        }
        
        $totalSaved = $totalOriginal - $totalMinified;
        $savedPercentage = $totalOriginal > 0 ? round(($totalSaved / $totalOriginal) * 100, 1) : 0;
        
        // Display results
        echo "✅ CSS Optimization Complete!\n\n";
        echo "📊 RESULTS:\n";
        echo "==========================================\n";
        echo "Total Original Size: " . $this->formatBytes($totalOriginal) . "\n";
        echo "Total Minified Size: " . $this->formatBytes($totalMinified) . "\n";
        echo "Total Saved: " . $this->formatBytes($totalSaved) . " ({$savedPercentage}%)\n\n";
        
        echo "📦 BUNDLES CREATED:\n";
        echo "==========================================\n";
        foreach ($results as $bundleName => $bundle) {
            if ($bundleName === 'individual') continue;
            $saved = $bundle['original'] > 0 ? round(($bundle['saved'] / $bundle['original']) * 100, 1) : 0;
            echo "• {$bundleName}.min.css: " . $this->formatBytes($bundle['saved']) . " saved ({$saved}%)\n";
        }
        
        echo "\n📁 Files created in: assets/css/min/\n";
        echo "📄 HTML includes generated: optimized-includes.html\n";
        
        return $results;
    }
    
    private function formatBytes($bytes) {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}

// Run optimization
if (php_sapi_name() === 'cli' || isset($_GET['optimize'])) {
    $minifier = new CSSMinifier();
    $results = $minifier->optimize();
    
    if (!php_sapi_name() === 'cli') {
        echo "<pre>";
        print_r($results);
        echo "</pre>";
    }
}
?>
