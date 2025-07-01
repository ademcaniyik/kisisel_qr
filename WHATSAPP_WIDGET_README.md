# WhatsApp Widget Modülü

Bu modüler WhatsApp widget sistemi, web sitesine kolayca WhatsApp destek butonu eklemenizi sağlar.

## 📁 Dosya Yapısı

```
assets/
├── css/
│   └── whatsapp-widget.css    # Widget stilleri
└── js/
    └── whatsapp-widget.js     # Widget JavaScript kodu

includes/
└── WhatsAppWidget.php         # PHP helper sınıfı (opsiyonel)
```

## 🚀 Hızlı Kullanım

### 1. Basit Dahil Etme (HTML)

```html
<head>
    <!-- CSS -->
    <link href="assets/css/whatsapp-widget.css" rel="stylesheet">
</head>

<body>
    <!-- JavaScript -->
    <script src="assets/js/whatsapp-widget.js"></script>
</body>
```

Widget otomatik olarak sadece `index.php` sayfasında görünecektir.

### 2. PHP Helper ile Dahil Etme

```php
<?php
require_once 'includes/WhatsAppWidget.php';

// Basit kullanım
WhatsAppWidgetHelper::include();

// Özelleştirilmiş
WhatsAppWidgetHelper::include([
    'phoneNumber' => '905349334631',
    'message' => 'Özel mesajınız...',
    'showOnPages' => ['index', 'contact']
]);
?>
```

## ⚙️ Konfigürasyon

```javascript
new WhatsAppWidget({
    phoneNumber: '905349334631',
    message: 'Merhaba! Size nasıl yardımcı olabilirim?',
    tooltipText: 'Canlı destek için tıklayın! 💬',
    buttonText: 'Yardım',
    showOnPages: ['index', 'contact'],  // Hangi sayfalarda gösterilecek
    hideOnModals: true,                 // Modal açıldığında gizlensin mi
    analytics: true                     // Google Analytics tracking
});
```

## 🎨 Özelleştirme Seçenekleri

### Telefon Numarası
```javascript
phoneNumber: '905349334631'  // Ülke kodu ile birlikte
```

### Öntanımlı Mesaj
```javascript
message: 'Merhaba! Kişisel QR sistemi hakkında bilgi almak istiyorum.'
```

### Görünüm Sayfaları
```javascript
showOnPages: ['index']           // Sadece ana sayfa
showOnPages: ['index', 'about']  // Ana sayfa ve hakkımızda
showOnPages: ['*']               // Tüm sayfalar
```

### Pozisyon (CSS ile)
```css
.whatsapp-widget {
    bottom: 30px;    /* Alt mesafe */
    right: 30px;     /* Sağ mesafe */
    /* left: 30px;   Sol köşe için */
    /* top: 30px;    Üst köşe için */
}
```

## 🛠️ Gelişmiş Kullanım

### Widget'ı Programatik Kontrol

```javascript
// Widget referansı al
const widget = window.whatsappWidget;

// Göster/Gizle
widget.show();
widget.hide();

// Konfigürasyonu güncelle
widget.updateConfig({
    phoneNumber: 'yeni_numara',
    message: 'Yeni mesaj'
});

// Widget'ı tamamen kaldır
widget.destroy();
```

### Event Listening

```javascript
// WhatsApp tıklama olayını dinle
document.addEventListener('whatsapp_widget_click', function(e) {
    console.log('WhatsApp widget tıklandı:', e.detail);
});
```

## 📱 Responsive Tasarım

Widget otomatik olarak mobil cihazlarda daha küçük boyuta geçer:

- **Desktop**: 60px çap
- **Mobile**: 55px çap
- **Tooltip**: Otomatik pozisyon ayarı

## 🔧 Modül Yönetimi

### Widget'ı Kaldırma
1. CSS dosyasını kaldırın: `assets/css/whatsapp-widget.css`
2. JS dosyasını kaldırın: `assets/js/whatsapp-widget.js`
3. HTML'den link etiketlerini kaldırın

### Widget'ı Geçici Devre Dışı Bırakma
```javascript
// JavaScript'te
if (window.whatsappWidget) {
    window.whatsappWidget.destroy();
}
```

## 🎯 Örnek Senaryolar

### Sadece Ana Sayfada Göster
```javascript
new WhatsAppWidget({
    showOnPages: ['index']
});
```

### Tüm Sayfalarda Göster
```javascript
new WhatsAppWidget({
    showOnPages: ['*']
});
```

### Özel Mesaj ile
```javascript
new WhatsAppWidget({
    phoneNumber: '905349334631',
    message: 'Merhaba! Ürünleriniz hakkında bilgi almak istiyorum.',
    tooltipText: 'Satış desteği için tıklayın! 🛒'
});
```

### Modal'lar için Özel Davranış
```javascript
new WhatsAppWidget({
    hideOnModals: false  // Modal açıldığında gizleme
});
```

## 📊 Analytics

Google Analytics kullanıyorsanız, widget tıklamaları otomatik olarak track edilir:

```javascript
gtag('event', 'whatsapp_widget_click', {
    'event_category': 'engagement',
    'event_label': 'help_request',
    'page': 'index'
});
```

## 🔒 Güvenlik

- Telefon numarası client-side'da görünür (normal davranış)
- XSS koruması için mesajlar encode edilir
- HTTPS üzerinden WhatsApp'a yönlendirir

## 🐛 Sorun Giderme

### Widget Görünmüyor
1. CSS dosyası yüklenmiş mi kontrol edin
2. Console'da JavaScript hatası var mı bakın
3. `showOnPages` ayarını kontrol edin

### Modal ile Çakışma
```javascript
hideOnModals: true  // Bu seçeneği aktif edin
```

### Özel CSS Çakışması
```css
.whatsapp-widget {
    z-index: 9999 !important;
}
```

Bu modüler sistem sayesinde WhatsApp widget'ını kolayca yönetebilir, özelleştirebilir ve gerektiğinde kaldırabilirsiniz! 🚀
