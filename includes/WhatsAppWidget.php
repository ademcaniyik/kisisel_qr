<?php
/**
 * WhatsApp Widget Include Helper
 * WhatsApp widget'ını sayfalara kolayca dahil etmek için
 */

class WhatsAppWidgetHelper {
    
    /**
     * WhatsApp widget'ını sayfaya dahil et
     * @param array $config Widget konfigürasyonu
     */
    public static function include($config = []) {
        $defaultConfig = [
            'phoneNumber' => '905349334631',
            'message' => 'Merhaba! Kişisel QR sistemi hakkında bilgi almak istiyorum. Yardımcı olabilir misiniz?',
            'tooltipText' => 'Merhaba! Size nasıl yardımcı olabilirim? 💬',
            'buttonText' => 'Yardım',
            'showOnPages' => ['index'],
            'hideOnModals' => true,
            'analytics' => true
        ];
        
        $config = array_merge($defaultConfig, $config);
        
        self::includeCss();
        self::includeJs($config);
    }
    
    /**
     * CSS dosyasını dahil et
     */
    private static function includeCss() {
        echo '<link rel="stylesheet" href="assets/css/whatsapp-widget.css">' . "\n";
    }
    
    /**
     * JavaScript dosyasını dahil et
     */
    private static function includeJs($config) {
        echo '<script src="assets/js/whatsapp-widget.js"></script>' . "\n";
        echo '<script>' . "\n";
        echo 'document.addEventListener("DOMContentLoaded", function() {' . "\n";
        echo '    new WhatsAppWidget(' . json_encode($config, JSON_UNESCAPED_UNICODE) . ');' . "\n";
        echo '});' . "\n";
        echo '</script>' . "\n";
    }
    
    /**
     * Widget'ı belirli sayfalarda göster
     */
    public static function includeForPages($pages = ['index'], $config = []) {
        $config['showOnPages'] = $pages;
        self::include($config);
    }
    
    /**
     * Widget'ı sadece ana sayfada göster
     */
    public static function includeOnHomepage($config = []) {
        self::includeForPages(['index'], $config);
    }
}
?>
