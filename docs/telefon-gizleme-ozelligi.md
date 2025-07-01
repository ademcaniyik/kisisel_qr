# Telefon Numarası Gizleme Özelliği

## Genel Bakış
Bu özellik, kullanıcıların telefon numaralarını herkese açık profillerinde gizlemelerine olanak tanır. Telefon numarası hala sistemde saklanır ancak sadece kullanıcı ve yöneticiler tarafından görülebilir.

## Özellikler

### Kullanıcı Tarafı
- **Profil Düzenleme**: Kullanıcılar edit sayfasında "Telefon numaram profilimde görünmesin" seçeneği ile telefon numaralarını gizleyebilir
- **Görsel Geri Bildirim**: Checkbox seçili olduğunda mavi arka plan rengi ile vurgulanır
- **Bilgilendirme**: Kullanıcıya bu özelliğin ne işe yaradığı açıkça belirtilir

### Herkese Açık Profil
- Telefon numarası gizli olarak işaretlenmişse, ziyaretçiler telefon numarasını göremez
- JSON-LD yapılandırılmış verilerde de telefon numarası gizlenir
- Meta etiketlerinde telefon numarası gözükmez

### Yönetici Paneli
- Yöneticiler tüm telefon numaralarını görebilir
- Gizli telefon numaraları yanında "Gizli" badge'i gösterilir
- API yanıtlarında `phone_hidden` alanı bulunur

## Teknik Detaylar

### Veritabanı
- `profiles` tablosuna `phone_hidden` TINYINT(1) sütunu eklenmiştir
- Varsayılan değer 0 (gizli değil)

### Dosya Değişiklikleri
1. **edit.php**: Telefon gizleme checkbox'ı ve form işleme
2. **includes/UserProfileManager.php**: Profil güncelleme mantığı
3. **profile.php**: Herkese açık görünümde telefon gizleme
4. **admin/api/profile.php**: Admin API'sinde phone_hidden alanı
5. **admin/profiles.php**: Admin panelinde gizlilik göstergesi
6. **assets/css/profile-edit.css**: Görsel iyileştirmeler

### Güvenlik
- Form verisi doğrulanır ve sanitize edilir
- SQL injection koruması mevcuttur
- Yetkilendirme kontrolleri yapılır

## Kullanım

### Kullanıcı İçin
1. Profil düzenleme sayfasına gidin
2. "Telefon numaram profilimde görünmesin" checkbox'ını işaretleyin
3. Profili kaydedin
4. Artık telefon numaranız herkese açık profilinizde gözükmeyecektir

### Yönetici İçin
- Yönetici panelinde tüm profilleri görüntülerken gizli telefon numaraları "Gizli" badge'i ile işaretlenir
- API çağrılarında `phone_hidden` alanı döndürülür

## Test Senaryoları
1. Telefon numarasını gizleme/gösterme
2. Herkese açık profilde görünürlük kontrolü
3. Yönetici panelinde gizlilik göstergesi
4. JSON-LD ve meta tag güncellemeleri
5. Form validasyonu ve hata işleme
