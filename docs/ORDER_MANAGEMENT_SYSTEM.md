# Sipariş Yönetim Sistemi

Bu dokümantasyon, QR Sticker siparişlerinin yönetimi için oluşturulan sistemin kullanımını açıklar.

## 🎯 Özellikler

### Müşteri Tarafı (index.php)
- QR Sticker sipariş formu
- Tema seçimi ve önizleme
- Ödeme bilgileri
- WhatsApp entegrasyonu
- Otomatik veritabanı kaydı

### Admin Paneli
- Sipariş listesi ve yönetimi
- Durum güncelleme (Bekleyen → İşleniyor → Tamamlandı)
- Sipariş notları ekleme
- İstatistikler ve raporlama
- Filtreleme ve sayfalama

## 📋 Sipariş Durumları

| Durum | Açıklama | Renk |
|-------|----------|------|
| `pending` | Bekleyen sipariş | Sarı |
| `processing` | İşleniyor | Mavi |
| `completed` | Tamamlandı | Yeşil |
| `cancelled` | İptal edildi | Kırmızı |

## 🚀 Kurulum

### 1. Veritabanı Tablosu Oluşturma
```bash
php database/setup_orders.php
```

### 2. Test Siparişleri Oluşturma (İsteğe bağlı)
```bash
php database/create_test_orders.php
```

## 📊 Veritabanı Yapısı

### `orders` Tablosu
```sql
- id (PRIMARY KEY)
- customer_name (VARCHAR)
- customer_phone (VARCHAR)
- customer_email (VARCHAR)
- product_type (ENUM)
- product_name (VARCHAR)
- quantity (INT)
- price (DECIMAL)
- special_requests (TEXT)
- status (ENUM)
- order_date (TIMESTAMP)
- processed_date (TIMESTAMP)
- notes (TEXT)
- whatsapp_sent (BOOLEAN)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## 🔗 API Endpoints

### Sipariş Oluşturma
```
POST /admin/api/orders.php
Content-Type: application/json

{
    "customer_name": "Ahmet Yılmaz",
    "customer_phone": "05551234567",
    "customer_email": "ahmet@example.com",
    "product_type": "personal_qr",
    "product_name": "Şeffaf QR Sticker",
    "quantity": 1,
    "price": 200.00,
    "special_requests": "Tema: Deniz Mavisi",
    "whatsapp_sent": true
}
```

### Sipariş Yönetimi
```
POST /admin/api/orders-management.php
Content-Type: application/json

// Durum güncelleme
{
    "action": "update_status",
    "order_id": 1,
    "status": "completed"
}

// Not ekleme
{
    "action": "add_note",
    "order_id": 1,
    "note": "Kargo ile gönderildi"
}

// Silme
{
    "action": "delete",
    "order_id": 1
}
```

## 🎛️ Admin Panel Kullanımı

### Sipariş Listesi
- **URL:** `/admin/orders.php`
- **Filtreler:** Durum bazında filtreleme
- **Sayfalama:** Sayfa başına 20 sipariş
- **Arama:** Telefon numarası veya ad ile

### Sipariş İşlemleri
1. **Durum Değiştirme:** Bekleyen → İşleniyor → Tamamlandı
2. **Not Ekleme:** Sipariş hakkında admin notları
3. **Silme:** Kalıcı silme işlemi
4. **Detay Görüntüleme:** Özel istekler ve sosyal medya bilgileri

### Dashboard İstatistikleri
- Toplam sipariş sayısı
- Durum bazında dağılım
- Toplam gelir (tamamlanan siparişlerden)
- Günlük/haftalık sipariş sayıları

## 📱 WhatsApp Entegrasyonu

Sistem otomatik olarak WhatsApp mesaj formatı oluşturur:

```
🏷️ QR Sticker Siparişi (#123)

👤 Ad Soyad: Ahmet Yılmaz
📱 Telefon: 05551234567
📍 Adres: İstanbul, Kadıköy
📝 Bio: Yazılım geliştirici
🎨 Tema: Deniz Mavisi

🌐 Sosyal Medya:
Instagram: @ahmetyilmaz
LinkedIn: linkedin.com/in/ahmetyilmaz

💰 Tutar: 200 TL
📦 Ürün: Şeffaf QR Sticker
✅ Ödeme Durumu: Ödeme yapıldı

Siparişimi onaylayın lütfen 🙏
```

## 🔧 Teknik Detaylar

### Dosya Yapısı
```
/admin/
├── orders.php                 # Sipariş yönetim sayfası
├── api/
│   ├── orders.php            # Sipariş oluşturma API
│   └── orders-management.php # Sipariş yönetim API
├── templates/
│   └── sidebar.php           # Güncellenen sidebar
/includes/
└── OrderManager.php          # Sipariş yönetim sınıfı
/database/
├── create_orders_table.sql   # Tablo oluşturma SQL
├── setup_orders.php          # Kurulum script'i
└── create_test_orders.php    # Test verileri
```

### Güvenlik Özellikleri
- Session tabanlı admin kontrolü
- SQL injection koruması (prepared statements)
- XSS koruması (htmlspecialchars)
- CSRF token kontrolü
- Input validation

## 📈 İstatistikler

### Dashboard Metrikleri
- **Toplam Sipariş:** Tüm zamanlar
- **Bekleyen Siparişler:** İşlem bekleyen
- **Tamamlanan Siparişler:** Başarıyla teslim edilen
- **Toplam Gelir:** Tamamlanan siparişlerden elde edilen

### Filtreleme Seçenekleri
- Durum bazında (pending, processing, completed, cancelled)
- Tarih aralığı
- Sayfalama (20 sipariş/sayfa)

## 🛠️ Bakım ve Optimizasyon

### Performans
- Veritabanı indeksleri (status, order_date, customer_phone)
- Sayfalama ile büyük veri setleri
- Async JavaScript işlemleri

### Yedekleme
- Sipariş verilerinin düzenli yedeklenmesi
- Log dosyalarının arşivlenmesi

## 🎉 Başarıyla Tamamlandı!

Sipariş yönetim sistemi artık tam olarak çalışmaktadır:

1. ✅ Müşteriler index.php'den sipariş verebilir
2. ✅ Siparişler otomatik olarak veritabanına kaydedilir
3. ✅ Admin panelde tüm siparişler görünür
4. ✅ Sipariş durumları güncellenebilir
5. ✅ WhatsApp entegrasyonu çalışır
6. ✅ İstatistikler dashboard'da gösterilir

Admin paneline giriş yaparak `/admin/orders.php` sayfasından siparişlerinizi yönetebilirsiniz!
