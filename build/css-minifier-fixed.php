<?php
/**
 * CSS Minification and Optimization Tool - Fixed Version
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
     * Improved minify CSS content
     */
    public function minifyCSS($css) {
        // Preserve important content patterns
        $preservePatterns = [];
        $preserveId = 0;
        
        // Preserve content inside quotes (for URLs, etc.)
        $css = preg_replace_callback('/(["\'])(?:(?!\1)[^\\\\]|\\\\.)*\1/', function($matches) use (&$preservePatterns, &$preserveId) {
            $id = "__PRESERVE_{$preserveId}__";
            $preservePatterns[$id] = $matches[0];
            $preserveId++;
            return $id;
        }, $css);
        
        // Remove comments but preserve IE hacks
        $css = preg_replace('/\/\*(?![*!])(.*?)\*\//', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
        
        // Remove trailing semicolon before closing brace
        $css = preg_replace('/;+}/', '}', $css);
        
        // Remove leading zeros from decimal numbers (safely)
        $css = preg_replace('/([\s:,])(0+)\.([0-9]+)([a-z%]+)/i', '$1.$3$4', $css);
        
        // Remove units from zero values (but be careful with timing functions)
        $css = preg_replace('/([\s:,])0+(px|em|rem|%|pt|pc|in|mm|cm|ex)(?=[\s;}])/i', '${1}0', $css);
        
        // Optimize hex colors
        $css = preg_replace('/#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3/i', '#$1$2$3', $css);
        
        // Remove empty rules
        $css = preg_replace('/[^}]+{[\s]*}/', '', $css);
        
        // Final cleanup
        $css = trim($css);
        
        // Restore preserved patterns
        foreach ($preservePatterns as $id => $content) {
            $css = str_replace($id, $content, $css);
        }
        
        return $css;
    }
    
    /**
     * Extract critical CSS
     */
    public function extractCriticalCSS($css) {
        $lines = explode("\n", $css);
        $critical = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            foreach ($this->criticalPatterns as $pattern) {
                if (strpos($line, $pattern) !== false) {
                    $critical[] = $line;
                    break;
                }
            }
        }
        
        return implode("\n", $critical);
    }
    
    /**
     * Format bytes for display
     */
    public function formatBytes($bytes) {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
    
    /**
     * Main optimization process
     */
    public function optimize() {
        echo "🚀 Starting CSS Optimization...\n";
        
        $cssFiles = [
            'dashboard.css',
            'error.css', 
            'image-enhancements.css',
            'landing.css',
            'login.css',
            'profile-edit.css',
            'profile-page.css',
            'profile-themes.css',
            'social-buttons.css',
            'whatsapp-widget.css'
        ];
        
        $bundles = [
            'critical' => [],
            'main' => [],
            'page-specific' => []
        ];
        
        $results = [
            'critical' => ['original' => 0, 'minified' => 0, 'saved' => 0],
            'main' => ['original' => 0, 'minified' => 0, 'saved' => 0],
            'page-specific' => ['original' => 0, 'minified' => 0, 'saved' => 0],
            'individual' => ['original' => 0, 'minified' => 0, 'saved' => 0]
        ];
        
        $totalOriginal = 0;
        $totalMinified = 0;
        
        // Process each CSS file
        foreach ($cssFiles as $file) {
            $filePath = $this->sourceDir . $file;
            
            if (!file_exists($filePath)) {
                continue;
            }
            
            $originalCSS = file_get_contents($filePath);
            $originalSize = strlen($originalCSS);
            $minifiedCSS = $this->minifyCSS($originalCSS);
            $minifiedSize = strlen($minifiedCSS);
            
            // Save individual minified file
            $minFileName = str_replace('.css', '.min.css', $file);
            file_put_contents($this->outputDir . $minFileName, $minifiedCSS);
            
            // Update stats
            $totalOriginal += $originalSize;
            $totalMinified += $minifiedSize;
            $results['individual']['original'] += $originalSize;
            $results['individual']['minified'] += $minifiedSize;
            $results['individual']['saved'] += $originalSize - $minifiedSize;
            
            // Categorize for bundles
            if (in_array($file, ['landing.css', 'social-buttons.css', 'whatsapp-widget.css'])) {
                $bundles['main'][] = $minifiedCSS;
                $results['main']['original'] += $originalSize;
                $results['main']['minified'] += $minifiedSize;
                $results['main']['saved'] += $originalSize - $minifiedSize;
                
                // Extract critical CSS from main files
                $critical = $this->extractCriticalCSS($originalCSS);
                if (!empty($critical)) {
                    $bundles['critical'][] = $this->minifyCSS($critical);
                    $criticalSize = strlen($critical);
                    $results['critical']['original'] += $criticalSize;
                    $results['critical']['minified'] += strlen($this->minifyCSS($critical));
                    $results['critical']['saved'] += $criticalSize - strlen($this->minifyCSS($critical));
                }
            } else {
                $bundles['page-specific'][] = $minifiedCSS;
                $results['page-specific']['original'] += $originalSize;
                $results['page-specific']['minified'] += $minifiedSize;
                $results['page-specific']['saved'] += $originalSize - $minifiedSize;
            }
        }
        
        // Create bundles
        file_put_contents($this->outputDir . 'critical.min.css', implode('', $bundles['critical']));
        file_put_contents($this->outputDir . 'main.min.css', implode('', $bundles['main']));
        file_put_contents($this->outputDir . 'page-specific.min.css', implode('', $bundles['page-specific']));
        
        // Generate optimized HTML includes
        $this->generateOptimizedIncludes($bundles);
        
        $totalSaved = $totalOriginal - $totalMinified;
        $savedPercentage = $totalOriginal > 0 ? round(($totalSaved / $totalOriginal) * 100, 1) : 0;
        
        // Display results
        echo "✅ CSS Optimization Complete!\n\n";
        echo "📊 RESULTS:\n";
        echo "==========================================\n";
        echo "Total Original Size: " . $this->formatBytes($totalOriginal) . "\n";
        echo "Total Minified Size: " . $this->formatBytes($totalMinified) . "\n";
        echo "Total Saved: " . $this->formatBytes($totalSaved) . " ({$savedPercentage}%)\n";
        echo "\n📦 BUNDLES CREATED:\n";
        echo "==========================================\n";
        foreach ($results as $bundleName => $bundle) {
            if ($bundleName === 'individual') continue;
            $saved = $bundle['original'] > 0 ? round(($bundle['saved'] / $bundle['original']) * 100, 1) : 0;
            echo "• {$bundleName}.min.css: " . $this->formatBytes($bundle['saved']) . " saved ({$saved}%)\n";
        }
        
        echo "\n📁 Files created in: assets/css/min/\n";
        echo "📄 HTML includes generated: optimized-includes.html\n";
    }
    
    /**
     * Generate optimized HTML includes
     */
    public function generateOptimizedIncludes($bundles) {
        $criticalCSS = implode('', $bundles['critical']);
        
        $html = <<<HTML
<!-- Optimized CSS Loading -->
<!-- Critical CSS (inline for instant render) -->
<style>
{$criticalCSS}
</style>

<!-- Main CSS (preload + defer) -->
<link rel="preload" href="assets/css/min/main.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="assets/css/min/main.min.css"></noscript>

<!-- Page-specific CSS (lazy load) -->
<link rel="stylesheet" href="assets/css/min/page-specific.min.css" media="print" onload="this.media='all'">

HTML;
        
        file_put_contents($this->outputDir . 'optimized-includes.html', $html);
    }
}

// Run the optimizer
$optimizer = new CSSMinifier();
$optimizer->optimize();
?>
