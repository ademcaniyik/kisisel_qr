# Teknik İmplementasyon Rehberi

## 🔧 Hemen Başlanabilecek Geliştirmeler

### 1. 📊 Real-Time Analytics Dashboard

#### Database Schema Güncellemeleri
```sql
-- Gelişmiş analytics için yeni tablolar
CREATE TABLE scan_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    qr_id VARCHAR(255),
    profile_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    country VARCHAR(2),
    city VARCHAR(100),
    device_type ENUM('mobile', 'desktop', 'tablet'),
    browser VARCHAR(50),
    referrer TEXT,
    scan_duration INT, -- seconds spent on profile
    actions_taken JSON, -- clicked links, downloaded vcard etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE daily_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT,
    date DATE,
    total_scans INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    bounce_rate DECIMAL(5,2),
    avg_duration DECIMAL(8,2),
    top_countries JSON,
    device_breakdown JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_profile_date (profile_id, date)
);
```

#### Backend API Endpoints
```php
// admin/api/analytics.php
<?php
class AnalyticsAPI {
    public function getDashboardStats($timeframe = '7d') {
        // Real-time dashboard data
    }
    
    public function getProfileAnalytics($profile_id, $period) {
        // Detailed profile analytics
    }
    
    public function getGeographicData($profile_id) {
        // Country/city breakdown
    }
    
    public function exportData($profile_id, $format = 'csv') {
        // Export analytics data
    }
}
```

#### Frontend Dashboard Widget
```javascript
// assets/js/analytics-dashboard.js
class AnalyticsDashboard {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.charts = {};
        this.websocket = null;
        this.init();
    }
    
    init() {
        this.setupRealTimeUpdates();
        this.loadInitialData();
        this.createCharts();
    }
    
    setupRealTimeUpdates() {
        // WebSocket for real-time updates
        this.websocket = new WebSocket('ws://localhost:8080/analytics');
        this.websocket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.updateCharts(data);
        };
    }
    
    createCharts() {
        // Chart.js implementation
        this.charts.scansChart = new Chart(ctx, {
            type: 'line',
            data: { /* scan data */ },
            options: { /* responsive options */ }
        });
        
        this.charts.geoChart = new Chart(ctx2, {
            type: 'doughnut',
            data: { /* geographic data */ }
        });
    }
}
```

---

### 2. 🎨 Gelişmiş Tema Editörü

#### CSS Custom Properties Expansion
```css
/* assets/css/theme-builder.css */
:root {
    /* Existing properties */
    --background-color: #f8f9fa;
    --text-color: #333333;
    --accent-color: #007bff;
    
    /* New customizable properties */
    --header-bg-type: 'solid'; /* solid, gradient, image */
    --header-bg-image: none;
    --header-bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-border-radius: 15px;
    --card-shadow: 0 8px 32px rgba(0,0,0,0.1);
    --button-style: 'rounded'; /* rounded, square, pill */
    --font-primary: 'Inter', sans-serif;
    --font-secondary: 'Poppins', sans-serif;
    --animation-speed: 0.3s;
    --layout-style: 'card'; /* card, minimal, business */
}

.theme-builder {
    display: grid;
    grid-template-columns: 300px 1fr 300px;
    gap: 20px;
    height: 100vh;
}

.theme-controls {
    background: white;
    padding: 20px;
    border-radius: 8px;
    overflow-y: auto;
}

.theme-preview {
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-picker-group {
    margin-bottom: 20px;
}

.color-picker {
    width: 100%;
    height: 40px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
```

#### Theme Builder JavaScript
```javascript
// assets/js/theme-builder.js
class ThemeBuilder {
    constructor() {
        this.currentTheme = {};
        this.previewFrame = null;
        this.controls = {};
        this.init();
    }
    
    init() {
        this.setupControls();
        this.setupPreview();
        this.loadGoogleFonts();
        this.bindEvents();
    }
    
    setupControls() {
        // Color pickers
        this.controls.backgroundColor = new ColorPicker('#bg-color-picker');
        this.controls.textColor = new ColorPicker('#text-color-picker');
        this.controls.accentColor = new ColorPicker('#accent-color-picker');
        
        // Font selector
        this.controls.fontFamily = new FontSelector('#font-selector');
        
        // Layout options
        this.controls.layoutStyle = new RadioGroup('#layout-options');
        
        // Background type selector
        this.controls.backgroundType = new Select('#bg-type-select');
    }
    
    updatePreview() {
        const css = this.generateCSS();
        this.previewFrame.contentDocument.querySelector('#dynamic-styles').innerHTML = css;
    }
    
    generateCSS() {
        return `
            :root {
                --background-color: ${this.currentTheme.backgroundColor};
                --text-color: ${this.currentTheme.textColor};
                --accent-color: ${this.currentTheme.accentColor};
                --font-primary: ${this.currentTheme.fontPrimary};
                --layout-style: ${this.currentTheme.layoutStyle};
            }
        `;
    }
    
    saveTheme() {
        const themeData = {
            name: this.controls.themeName.value,
            styles: this.currentTheme,
            timestamp: Date.now()
        };
        
        fetch('/admin/api/themes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'save', theme: themeData })
        });
    }
}
```

---

### 3. 📱 QR Kod Özelleştirme Sistemi

#### QR Generator Class Güncellemesi
```php
// includes/QRCustomizer.php
<?php
class QRCustomizer {
    private $qrCode;
    private $options;
    
    public function __construct() {
        $this->qrCode = new \chillerlan\QRCode\QRCode();
        $this->options = new \chillerlan\QRCode\QROptions();
    }
    
    public function generateCustomQR($data, $customOptions = []) {
        // Basic options
        $this->options->outputType = QRCode::OUTPUT_IMAGE_PNG;
        $this->options->eccLevel = QRCode::ECC_L;
        $this->options->scale = 10;
        $this->options->imageBase64 = false;
        
        // Custom styling
        if (isset($customOptions['foregroundColor'])) {
            $this->options->imageTransparent = false;
            // Color customization
        }
        
        if (isset($customOptions['logo'])) {
            return $this->addLogoToQR($data, $customOptions['logo']);
        }
        
        return $this->qrCode->render($data, $this->options);
    }
    
    private function addLogoToQR($data, $logoPath) {
        // Generate base QR
        $qrImage = imagecreatefromstring($this->qrCode->render($data));
        
        // Load logo
        $logo = imagecreatefromstring(file_get_contents($logoPath));
        
        // Calculate logo size (10% of QR code)
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);
        $logoWidth = $qrWidth * 0.1;
        $logoHeight = $qrHeight * 0.1;
        
        // Resize logo
        $logoResized = imagecreatetruecolor($logoWidth, $logoHeight);
        imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, 
                          $logoWidth, $logoHeight, imagesx($logo), imagesy($logo));
        
        // Center logo on QR
        $logoX = ($qrWidth - $logoWidth) / 2;
        $logoY = ($qrHeight - $logoHeight) / 2;
        
        // Add white background circle for logo
        $white = imagecolorallocate($qrImage, 255, 255, 255);
        imagefilledellipse($qrImage, $qrWidth/2, $qrHeight/2, $logoWidth*1.2, $logoHeight*1.2, $white);
        
        // Merge logo with QR
        imagecopy($qrImage, $logoResized, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);
        
        // Output as PNG
        ob_start();
        imagepng($qrImage);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        // Cleanup
        imagedestroy($qrImage);
        imagedestroy($logo);
        imagedestroy($logoResized);
        
        return $imageData;
    }
    
    public function generateSVG($data, $customOptions = []) {
        $this->options->outputType = QRCode::OUTPUT_MARKUP_SVG;
        // SVG specific options
        return $this->qrCode->render($data, $this->options);
    }
}
```

#### QR Customization Frontend
```javascript
// assets/js/qr-customizer.js
class QRCustomizer {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.preview = null;
        this.options = {
            foregroundColor: '#000000',
            backgroundColor: '#ffffff',
            logo: null,
            format: 'png',
            size: 300
        };
        this.init();
    }
    
    init() {
        this.createInterface();
        this.bindEvents();
        this.updatePreview();
    }
    
    createInterface() {
        this.container.innerHTML = `
            <div class="qr-customizer">
                <div class="qr-controls">
                    <div class="control-group">
                        <label>Foreground Color</label>
                        <input type="color" id="fg-color" value="#000000">
                    </div>
                    <div class="control-group">
                        <label>Background Color</label>
                        <input type="color" id="bg-color" value="#ffffff">
                    </div>
                    <div class="control-group">
                        <label>Logo Upload</label>
                        <input type="file" id="logo-upload" accept="image/*">
                    </div>
                    <div class="control-group">
                        <label>Size</label>
                        <select id="qr-size">
                            <option value="200">200px</option>
                            <option value="300" selected>300px</option>
                            <option value="500">500px</option>
                            <option value="1000">1000px</option>
                        </select>
                    </div>
                    <div class="control-group">
                        <label>Format</label>
                        <select id="qr-format">
                            <option value="png">PNG</option>
                            <option value="svg">SVG</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="qr-preview">
                    <div id="qr-preview-container"></div>
                    <button id="download-qr" class="btn btn-primary">Download QR</button>
                </div>
            </div>
        `;
    }
    
    bindEvents() {
        document.getElementById('fg-color').addEventListener('change', (e) => {
            this.options.foregroundColor = e.target.value;
            this.updatePreview();
        });
        
        document.getElementById('bg-color').addEventListener('change', (e) => {
            this.options.backgroundColor = e.target.value;
            this.updatePreview();
        });
        
        document.getElementById('logo-upload').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.options.logo = e.target.result;
                    this.updatePreview();
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('download-qr').addEventListener('click', () => {
            this.downloadQR();
        });
    }
    
    updatePreview() {
        const formData = new FormData();
        formData.append('action', 'generate_custom_qr');
        formData.append('profile_id', this.profileId);
        formData.append('options', JSON.stringify(this.options));
        
        fetch('/admin/api/qr.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const previewContainer = document.getElementById('qr-preview-container');
            previewContainer.innerHTML = `<img src="${url}" alt="QR Preview" style="max-width: 100%;">`;
        });
    }
    
    downloadQR() {
        // Implementation for downloading customized QR
    }
}
```

---

### 4. 🔄 Dynamic QR Codes (URL Shortener)

#### Database Schema
```sql
CREATE TABLE dynamic_qrs (
    id VARCHAR(10) PRIMARY KEY, -- Short ID
    profile_id INT,
    original_url TEXT,
    clicks INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id)
);

CREATE TABLE qr_redirects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dynamic_qr_id VARCHAR(10),
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT,
    country VARCHAR(2),
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dynamic_qr_id) REFERENCES dynamic_qrs(id)
);
```

#### URL Shortener Implementation
```php
// includes/URLShortener.php
<?php
class URLShortener {
    private $db;
    private $baseUrl;
    
    public function __construct($db, $baseUrl) {
        $this->db = $db;
        $this->baseUrl = $baseUrl;
    }
    
    public function createShortUrl($profileId, $expiresAt = null) {
        $shortId = $this->generateShortId();
        $originalUrl = $this->baseUrl . '/profile.php?id=' . $profileId;
        
        $stmt = $this->db->prepare("
            INSERT INTO dynamic_qrs (id, profile_id, original_url, expires_at) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("siss", $shortId, $profileId, $originalUrl, $expiresAt);
        
        if ($stmt->execute()) {
            return $this->baseUrl . '/r/' . $shortId;
        }
        
        return false;
    }
    
    private function generateShortId($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $shortId = '';
            for ($i = 0; $i < $length; $i++) {
                $shortId .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while ($this->shortIdExists($shortId));
        
        return $shortId;
    }
    
    private function shortIdExists($shortId) {
        $stmt = $this->db->prepare("SELECT id FROM dynamic_qrs WHERE id = ?");
        $stmt->bind_param("s", $shortId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public function redirect($shortId) {
        // Track the click
        $this->trackClick($shortId);
        
        // Get the original URL
        $stmt = $this->db->prepare("
            SELECT original_url, expires_at, is_active 
            FROM dynamic_qrs 
            WHERE id = ?
        ");
        $stmt->bind_param("s", $shortId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false; // Not found
        }
        
        $row = $result->fetch_assoc();
        
        // Check if expired
        if ($row['expires_at'] && strtotime($row['expires_at']) < time()) {
            return false; // Expired
        }
        
        // Check if active
        if (!$row['is_active']) {
            return false; // Inactive
        }
        
        return $row['original_url'];
    }
    
    private function trackClick($shortId) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // Update click count
        $stmt = $this->db->prepare("UPDATE dynamic_qrs SET clicks = clicks + 1 WHERE id = ?");
        $stmt->bind_param("s", $shortId);
        $stmt->execute();
        
        // Log detailed click info
        $stmt = $this->db->prepare("
            INSERT INTO qr_redirects (dynamic_qr_id, ip_address, user_agent, referrer) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $shortId, $ip, $userAgent, $referrer);
        $stmt->execute();
    }
}
```

#### Redirect Handler
```php
// r.php (redirect handler)
<?php
require_once 'config/database.php';
require_once 'includes/URLShortener.php';

$shortId = $_GET['id'] ?? '';

if (empty($shortId)) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

$db = Database::getInstance();
$shortener = new URLShortener($db->getConnection(), 'https://yourdomain.com');

$originalUrl = $shortener->redirect($shortId);

if ($originalUrl === false) {
    header("HTTP/1.0 404 Not Found");
    include 'errors/404.php';
    exit();
}

header("Location: " . $originalUrl, true, 302);
exit();
```

---

### 5. 📧 Email ve Notification Sistemi

#### Email Template System
```php
// includes/EmailService.php
<?php
class EmailService {
    private $templates;
    private $smtp;
    
    public function __construct() {
        $this->loadTemplates();
        $this->setupSMTP();
    }
    
    public function sendProfileShared($email, $profileData) {
        $template = $this->templates['profile_shared'];
        $html = $this->renderTemplate($template, $profileData);
        
        return $this->sendEmail($email, 'Your profile has been shared!', $html);
    }
    
    public function sendAnalyticsReport($email, $profileId, $period = 'weekly') {
        $analytics = $this->getAnalyticsData($profileId, $period);
        $template = $this->templates['analytics_report'];
        $html = $this->renderTemplate($template, $analytics);
        
        return $this->sendEmail($email, "Analytics Report - {$period}", $html);
    }
    
    private function renderTemplate($template, $data) {
        $html = $template;
        foreach ($data as $key => $value) {
            $html = str_replace("{{$key}}", $value, $html);
        }
        return $html;
    }
}
```

---

Bu teknik implementasyon rehberi, projenizin bir sonraki seviyeye taşınması için gereken tüm detayları içermektedir. Her özellik için kod örnekleri, database şemaları ve frontend implementasyonları verilmiştir.

Hangi özelliği öncelikli olarak geliştirmek istediğinizi belirtirseniz, o konuda daha detaylı implementasyon adımları sağlayabilirim.
