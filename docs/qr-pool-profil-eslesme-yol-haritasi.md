# QR Pool ve Profil Eşleşme Sorunu - Analiz ve Çözüm Yol Haritası

## 1. Mevcut Sistem Analizi

### Veritabanı Yapısı
- **qr_pool**: QR havuzu, batch ile oluşturulan QR'lar burada tutuluyor. `profile_id` atanmışsa QR bir profile atanmış demektir.
- **profiles**: Kullanıcı profilleri.
- **qr_codes**: Her profile atanmış QR için kayıt tutuluyor. `id` (qr_code_id), `profile_id` ve diğer alanlar var.
- **profile.php**: QR ile profil açılırken, `qr_codes.id` ile `profiles.id` eşleşiyor.

### Akış
1. Admin panelde profil oluşturulunca:
   - QRPoolManager'dan bir QR atanıyor.
   - `qr_pool.profile_id` güncelleniyor.
   - `qr_codes` tablosuna kayıt ekleniyor (id: qr_code_id, profile_id: yeni profil id).
2. Profil sayfası açılırken:
   - `profile.php` dosyası, gelen qr_code_id ile `qr_codes` tablosunda arama yapıyor.
   - Eşleşen profile yönlendiriyor.

## 2. Sorunun Kaynağı
- QR havuzunda atanmış bir QR'ın linkine gidince "profil bulunamadı" hatası çıkıyor.
- Çünkü ya `qr_codes` tablosunda kayıt yok, ya da yanlış kayıt var.
- Veya profil oluşturulurken QR havuzundan atama yapılmıyor, yeni QR üretiliyor.

## 3. Çözüm Adımları

### A. Kod ve DB Senkronizasyonu
- `QRPoolManager::assignAvailableQR` fonksiyonu, hem `qr_pool` hem de `qr_codes` tablosuna doğru kayıt eklemeli.
- `qr_codes` tablosunda `id` (qr_code_id) ve `profile_id` birebir eşleşmeli.
- Profil oluşturulurken, QR havuzundan atama dışında yeni QR üretilmemeli.

### B. Test ve Doğrulama
1. QR havuzunda bir QR'ı profile ata.
2. `qr_codes` tablosunda şu kayıt olmalı:
   - `id` = atanmış qr_code_id
   - `profile_id` = ilgili profil id
3. `/qr/{qr_code_id}` linkine gidince profil açılmalı.

### C. Hatalı Kayıtları Temizle
- Eski, yanlış veya eksik `qr_codes` kayıtlarını sil.
- Test için yeni bir profil oluştur ve QR havuzundan atanıp atanmadığını kontrol et.

## 4. Önerilen Geliştirme Akışı

1. **Veritabanı Temizliği:**
   - `DELETE FROM qr_codes;`
   - `UPDATE qr_pool SET profile_id = NULL, status = 'available', assigned_at = NULL WHERE status = 'assigned';`
2. **Kod Kontrolü:**
   - `QRPoolManager::assignAvailableQR` fonksiyonunun hem `qr_pool` hem de `qr_codes` tablosuna doğru kayıt eklediğinden emin ol.
   - Profil oluşturma akışında başka bir yerde yeni QR üretilmediğinden emin ol.
3. **Test:**
   - Admin panelden yeni profil oluştur.
   - QR Pool'da atanmış QR'ın linkine git, profil açılıyor mu kontrol et.
   - `qr_codes` tablosunda yeni kayıt oluştu mu bak.

## 5. Sonuç
- Tüm bu adımlar uygulandığında, batch'ten atanmış bir QR ile profil açılır ve "profil bulunamadı" hatası ortadan kalkar.
- Kodda veya veritabanında başka bir tutarsızlık varsa, ilgili log ve kayıtlar incelenmeli.

---

> **Not:** Bu dosya, QR havuzu ve profil eşleşme sorununu çözmek için yol haritası ve analiz dokümanıdır. Geliştirme ve test adımlarını bu sırayla uygulayın.
