# Kişisel QR Profil Sistemi - Proje Dokümantasyonu

## 📖 Genel Bakış

Bu sistem, kullanıcıların dijital kartvizitlerini QR kod aracılığıyla paylaşmalarını sağlayan modern bir web uygulamasıdır. PHP tabanlı olup, güvenlik, SEO ve performans odaklı geliştirilmiştir.

## 🏗️ Sistem Mimarisi

### Klasör Yapısı
```
kisisel_qr_canli/
├── admin/                  # Yönetim paneli
│   ├── api/               # API endpoints
│   │   ├── profile.php    # Profil CRUD işlemleri
│   │   ├── qr.php        # QR kod yönetimi
│   │   └── stats.php     # İstatistik API
│   ├── templates/         # Ortak şablonlar
│   ├── dashboard.php      # Ana panel
│   ├── profiles.php       # Profil yönetimi
│   └── settings.php       # Ayarlar
├── assets/                # Statik dosyalar
│   ├── css/              # Stil dosyaları
│   ├── js/               # JavaScript
│   └── images/           # Resimler
├── config/               # Yapılandırma
│   ├── database.php      # Veritabanı bağlantısı
│   └── security.php      # Güvenlik ayarları
├── includes/             # Yardımcı sınıflar
│   ├── ImageOptimizer.php # Resim optimizasyon
│   ├── QRManager.php     # QR kod yönetimi
│   └── utilities.php     # Yardımcı fonksiyonlar
├── public/               # Genel erişilebilir dosyalar
│   ├── qr_codes/         # QR kod resimleri
│   └── uploads/          # Yüklenen dosyalar
├── logs/                 # Log dosyaları
├── profile.php           # Profil görüntüleme
├── index.php            # Ana sayfa
└── redirect.php         # QR yönlendirme
```

## 🔧 Temel Özellikler

### ✅ Tamamlanan Özellikler

#### 1. Güvenlik ve Yapılandırma
- **Güvenli Session Yönetimi**: HTTP-only cookies, secure flags
- **CSRF Koruması**: Tüm form işlemlerinde token kontrolü
- **Rate Limiting**: API isteklerinde hız sınırlaması
- **Input Sanitization**: XSS ve SQL injection koruması
- **Error Handling**: Production ortamında güvenli hata yönetimi

#### 2. SEO ve Performans
- **Meta Tags**: Open Graph, Twitter Card, JSON-LD structured data
- **Sitemap**: Dinamik XML sitemap üretimi
- **robots.txt**: Arama motoru optimizasyonu
- **Canonical URLs**: Tekrarlanan içerik önleme
- **Responsive Design**: Mobil uyumlu tasarım

#### 3. Gelişmiş Resim Sistemi
- **Otomatik Optimizasyon**: JPEG/WebP format desteği
- **Responsive Images**: 3 farklı boyut (thumb, medium, large)
- **Lazy Loading**: Intersection Observer API ile
- **WebP Support**: Browser desteğine göre format seçimi
- **Cleanup System**: Kullanılmayan resimlerin otomatik temizlenmesi

#### 4. Admin Panel
- **Modern Arayüz**: Bootstrap 5 tabanlı responsive tasarım
- **Real-time Validation**: Form validasyonu ve önizleme
- **Modal Sistemler**: Edit/view işlemleri için popup'lar
- **Image Preview**: Yükleme öncesi görsel önizleme
- **Statistics Dashboard**: Detaylı istatistik paneli

#### 5. Tema Sistemi
- **12 Hazır Tema**: Modern, cyberpunk, doğa, retro vb.
- **Özelleştirilebilir Renkler**: CSS custom properties ile
- **Premium Fontlar**: Inter, Poppins, Montserrat desteği
- **Animation Effects**: Hover, glow, shimmer efektleri

#### 6. Sosyal Medya Entegrasyonu
- **25+ Platform Desteği**: Instagram, Twitter, LinkedIn vb.
- **Otomatik URL Formatting**: Platform bazlı link düzeltme
- **Icon System**: Font Awesome tabanlı modern ikonlar
- **Deep Linking**: Platform uygulamalarına doğrudan yönlendirme

## 🔌 API Dokümantasyonu

### Authentication
Tüm admin API endpoints için oturum doğrulaması gereklidir:
```php
$_SESSION['admin_logged_in'] === true
```

### Profile API (`/admin/api/profile.php`)
| Action | Method | Parameters | Açıklama |
|--------|--------|------------|----------|
| `create` | POST | name, bio, phone, photo, social_links | Yeni profil oluştur |
| `update` | POST | id, name, bio, phone, photo, social_links | Profil güncelle |
| `delete` | POST | id | Profil sil |
| `get` | GET | id | Profil detayları |
| `search` | POST | search, status, theme, page | Profil ara/filtrele |

### QR API (`/admin/api/qr.php`)
| Action | Method | Parameters | Açıklama |
|--------|--------|------------|----------|
| `create` | POST | profile_id | QR kod oluştur |
| `delete` | POST | id | QR kod sil |
| `list` | GET | profile_id | Profile ait QR kodları |

### Stats API (`/admin/api/stats.php`)
| Action | Method | Parameters | Açıklama |
|--------|--------|------------|----------|
| `dashboard` | GET | - | Dashboard istatistikleri |
| `profile` | GET | profile_id | Profil istatistikleri |
| `qr` | GET | qr_id | QR kod istatistikleri |

## 🎨 Tema Sistemi

### Mevcut Temalar
1. **Modern Minimal** (default) - Mor-mavi gradient
2. **Gece Modu** (dark) - Koyu tema, neon vurgular
3. **Doğa** (nature) - Yeşil tonları
4. **Cyberpunk** (cyberpunk) - Futuristik neon
5. **Altın Lüks** (gold) - Premium görünüm
6. **Pastel** (pastel) - Yumuşak renkler
7. **Retro** (retro) - 80'ler tarzı
8. **Ocean** (ocean) - Mavi tonları
9. **Sunset** (sunset) - Turuncu-pembe
10. **Forest** (forest) - Orman yeşili
11. **Royal** (royal) - Mor-altın
12. **Monochrome** (mono) - Siyah-beyaz

### Tema Ekleme
```php
// CSS'de yeni tema
.theme-yeni-tema {
    --background-color: #yourcolor;
    --text-color: #yourcolor;
    --accent-color: #yourcolor;
}

// PHP'de tema mapping
'yeni-tema' => 'yeni-tema'
```

## 🛠️ Kurulum ve Yapılandırma

### Gereksinimler
- PHP 7.4+
- MySQL 5.7+
- GD Extension (resim işleme)
- OpenSSL Extension (güvenlik)

### Kurulum Adımları
1. **Dosyaları sunucuya yükle**
2. **Veritabanını oluştur** (`database/schema.sql`)
3. **Yapılandırma dosyalarını düzenle** (`config/database.php`)
4. **Admin kullanıcı oluştur** (`create_admin.php`)
5. **Dizin izinlerini ayarla** (uploads, logs klasörleri 755)

### Güvenlik Ayarları
```php
// config/security.php
$isProduction = (getenv('APP_ENV') === 'production');
```

Environment değişkenleri:
- `APP_ENV=production` - Production modu
- `DB_HOST` - Veritabanı sunucusu
- `DB_NAME` - Veritabanı adı
- `DB_USER` - Kullanıcı adı
- `DB_PASS` - Şifre

## 🔍 Maintenance ve Monitoring

### Log Takibi
```bash
# Error logları
tail -f logs/error_log

# Access logları (sunucu logları)
tail -f /var/log/apache2/access.log
```

### Database Maintenance
```sql
-- Kullanılmayan profilleri temizle
DELETE FROM profiles WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR) AND status = 'inactive';

-- Orphaned files temizleme
SELECT photo_url FROM profiles WHERE photo_url IS NOT NULL;
```

### Performance Monitoring
- **Image Optimization**: Otomatik WebP dönüşümü
- **Database Indexing**: Sık sorgulanan alanlarda index
- **CDN Ready**: Statik dosyalar için CDN desteği hazır

## 🚨 Sorun Giderme

### Yaygın Sorunlar

#### 1. Session Hatası
```
Warning: ini_set(): Session ini settings cannot be changed when a session is active
```
**Çözüm**: `config/security.php` session ayarları session_start() öncesi yapılandırılmıştır.

#### 2. Photo_data Boşalması
Profil düzenleme sırasında fotoğraf değiştirilmezse photo_data korunur.

#### 3. Meta Tag Hataları
Array-to-string conversion hataları düzeltilmiştir.

### Debug Modu
```php
// Development ortamında
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Gelecek Planları

### Geliştirilecek Özellikler
- [ ] Multi-language support
- [ ] Advanced analytics dashboard  
- [ ] Bulk profile import/export
- [ ] Custom domain support
- [ ] Mobile app integration
- [ ] API v2 with REST standards

---

**Son Güncelleme**: 27 Haziran 2025  
**Versiyon**: 2.0  
**Durum**: Production Ready ✅
