# Kişisel QR - Dijital Profil Sistemi

Modern ve kullanıcı dostu QR kod tabanlı dijital profil sistemi. Kullanıcılar kişiselleştirilmiş QR kodlarıyla sosyal medya hesaplarını, iletişim bilgilerini ve dijital varlıklarını tek QR kodda toplayabilirler.

## 🌟 Özellikler

### 💳 QR Sticker Sipariş Sistemi
- **10x10 cm Şeffaf QR Sticker** siparişi
- **Otomatik profil oluşturma** sipariş ile birlikte
- **Çoklu ödeme yöntemi**: Banka havalesi ve kapıda ödeme
- **WhatsApp entegrasyonu** ile sipariş bildirimi
- **Admin panel** ile sipariş yönetimi

### 👤 Profil Yönetimi
- **Kişiselleştirilebilir profiller** (Ad, telefon, email, bio)
- **5 farklı tema** seçeneği
- **Sosyal medya entegrasyonu** (Instagram, Twitter, LinkedIn, Facebook, YouTube, Website)
- **Profil resmi yükleme** ve otomatik optimizasyon
- **SEO dostu URL'ler** (/profile/kullanici-adi)

### 🎨 Görsel Tasarım
- **Modern ve responsive** tasarım
- **Bootstrap 5** framework
- **Font Awesome** ikonlar
- **Gradient** renk geçişleri
- **Animate.css** animasyonlar
- **Lazy loading** resim optimizasyonu

### 🛡️ Güvenlik
- **HTTPS** zorunluluğu
- **SQL injection** koruması
- **XSS** koruması
- **CSRF** token sistemi
- **Güvenli dosya yükleme**
- **Admin panel** kimlik doğrulama

### 📊 Admin Panel
- **Dashboard** ile genel istatistikler
- **Sipariş yönetimi** (listeleme, durum değiştirme, notlar)
- **Profil yönetimi**
- **QR kod istatistikleri**
- **Responsive** admin arayüzü

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Composer
- SSL Sertifikası

### Adım Adım Kurulum

1. **Projeyi klonlayın:**
```bash
git clone https://github.com/ademcaniyik/kisisel_qr.git
cd kisisel_qr
```

2. **Composer bağımlılıklarını yükleyin:**
```bash
composer install
```

3. **Veritabanını oluşturun:**
```sql
mysql -u root -p < database/production_setup.sql
```

4. **Çevre değişkenlerini ayarlayın:**
```bash
cp .env.example .env
# .env dosyasını düzenleyin
```

5. **Klasör izinlerini ayarlayın:**
```bash
chmod 755 public/qr_codes/
chmod 755 public/uploads/
chmod 755 logs/
```

6. **Admin kullanıcısı oluşturun:**
```
http://yourdomain.com/create_admin.php
```

Detaylı kurulum için: [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

## 📱 Kullanım

### Müşteri Perspektifi
1. Ana sayfada **"QR Sticker Sipariş Et"** butonuna tıklayın
2. Kişisel bilgilerinizi doldurun
3. Sosyal medya hesaplarınızı ekleyin
4. Ödeme yöntemini seçin (Banka havalesi/Kapıda ödeme)
5. Siparişinizi WhatsApp ile bildirin
6. QR kodunuz ve profiliniz otomatik oluşturulur

### Admin Perspektifi
1. `/admin/` paneline giriş yapın
2. Siparişleri görüntüleyin ve yönetin
3. Profilleri kontrol edin
4. İstatistikleri takip edin

## 🛠️ Teknoloji Stack

### Backend
- **PHP 8.2** - Sunucu tarafı programlama
- **MySQL** - Veritabanı
- **Composer** - Bağımlılık yönetimi
- **Dotenv** - Çevre değişkenleri

### Frontend
- **HTML5** / **CSS3** / **JavaScript**
- **Bootstrap 5** - CSS Framework
- **Font Awesome** - İkonlar
- **Animate.css** - Animasyonlar

### Kütüphaneler
- **chillerlan/php-qrcode** - QR kod üretimi
- **Intervention/Image** - Resim işleme (gelecek sürüm)

## 📁 Proje Yapısı

```
kisisel_qr/
├── admin/                 # Admin panel
│   ├── api/              # API endpoints
│   ├── templates/        # Admin templates
│   └── dashboard.php     # Ana admin sayfası
├── assets/               # CSS, JS, resimler
├── config/               # Konfigürasyon dosyaları
├── database/             # SQL dosyaları
├── includes/             # PHP sınıfları
├── public/               # Public dosyalar
│   ├── qr_codes/        # Üretilen QR kodlar
│   └── uploads/         # Yüklenen dosyalar
├── vendor/               # Composer bağımlılıkları
├── index.php            # Ana sayfa
├── profile.php          # Profil görüntüleme
└── README.md           # Bu dosya
```

## 🔧 API Endpoints

- **POST** `/admin/api/orders.php` - Yeni sipariş oluşturma
- **GET** `/admin/api/orders.php` - Sipariş listeleme
- **POST** `/admin/api/profile.php` - Profil işlemleri
- **GET** `/admin/api/stats.php` - İstatistikler

## 🎯 Özellik Roadmap

### v1.1 (Gelecek)
- [ ] E-posta bildirimleri
- [ ] SMS entegrasyonu
- [ ] Gelişmiş profil temaları
- [ ] QR kod analitikleri
- [ ] Toplu işlemler

### v1.2 (Uzun vadeli)
- [ ] Multi-language desteği
- [ ] API rate limiting
- [ ] Advanced caching
- [ ] Mobile app

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit edin (`git commit -m 'Add amazing feature'`)
4. Push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📄 Lisans

Bu proje [MIT License](LICENSE) altında lisanslanmıştır.

## 🌐 Demo

**Canlı Demo:** https://acdisoftware.com.tr/kisisel_qr/

## 📞 İletişim

- **Geliştirici:** Ademcan İyik
- **WhatsApp:** +90 534 933 46 31
- **Email:** info@acdisoftware.com.tr
- **Website:** https://acdisoftware.com.tr

## 🎉 Teşekkürler

Bu projeyi kullandığınız ve katkıda bulunduğunuz için teşekkürler!

---

⭐ **Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!**
