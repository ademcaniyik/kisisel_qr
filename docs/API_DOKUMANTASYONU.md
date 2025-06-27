# API Dokümantasyonu

Bu doküman, sistem API endpoints'lerinin detaylarını içerir.

## 🔐 Authentication

Tüm admin API endpoints için session authentication gereklidir:
```php
$_SESSION['admin_logged_in'] === true
```

## 📋 Profile API (`/admin/api/profile.php`)

### Supported Actions

| Action | Method | Parameters | Description |
|--------|--------|------------|-------------|
| `create` | POST | name, bio, phone, photo, social_links, theme | Create new profile |
| `update` | POST | id + fields to update | Update existing profile |
| `delete` | POST | id | Delete profile and related files |
| `get` | GET | id | Get profile details |
| `search` | POST | search, status, theme, page | Search/filter profiles |
| `get_slug` | GET | id | Get or generate profile slug |

### Example Requests

#### Create Profile
```javascript
const formData = new FormData();
formData.append('action', 'create');
formData.append('name', 'John Doe');
formData.append('bio', 'Software Developer');
formData.append('phone', '+1234567890');
formData.append('photo', fileInput.files[0]);
formData.append('social_links', JSON.stringify({
    'instagram': 'johndoe',
    'linkedin': 'johndoe'
}));

fetch('/admin/api/profile.php', {
    method: 'POST',
    body: formData
});
```

#### Update Profile
```javascript
const formData = new FormData();
formData.append('action', 'update');
formData.append('id', '1');
formData.append('name', 'John Smith');
// Note: If no photo is uploaded, existing photo_data is preserved

fetch('/admin/api/profile.php', {
    method: 'POST',
    body: formData
});
```

### Response Format
```json
{
    "success": true|false,
    "message": "Operation result message",
    "data": {...} // Additional data if applicable
}
```

## 🎯 QR API (`/admin/api/qr.php`)

### Supported Actions

| Action | Method | Parameters | Description |
|--------|--------|------------|-------------|
| `create` | POST | profile_id | Generate QR code for profile |
| `delete` | POST | id | Delete specific QR code |
| `list` | GET | profile_id | List QR codes for profile |

### Example Usage
```javascript
// Create QR Code
fetch('/admin/api/qr.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=create&profile_id=1'
});

// Delete QR Code  
fetch('/admin/api/qr.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=delete&id=abc123'
});
```

## 📊 Stats API (`/admin/api/stats.php`)

### Supported Actions

| Action | Method | Parameters | Description |
|--------|--------|------------|-------------|
| `dashboard` | GET | - | Get dashboard statistics |
| `profile_stats` | GET | profile_id | Get profile scan statistics |
| `qr_stats` | GET | qr_id | Get QR code scan statistics |

### Response Examples

#### Dashboard Stats
```json
{
    "success": true,
    "data": {
        "total_profiles": 150,
        "total_qr_codes": 200,
        "total_scans": 1250,
        "recent_scans": [...]
    }
}
```

## 🛡️ Security Features

- **Rate Limiting**: 60 requests per minute per IP
- **CSRF Protection**: Available but optional (can be enabled)
- **Input Sanitization**: All inputs are sanitized
- **File Upload Security**: Type and size validation for images
- **Session Management**: Secure session handling

## 🖼️ Image Optimization

The system automatically optimizes uploaded images:
- **Formats**: JPEG (quality 85%) and WebP (quality 80%)
- **Sizes**: Original, thumb (150x150), medium (300x300), large (600x600)
- **Cleanup**: Automatic removal of old files when updating/deleting

## 🚨 Error Handling

### HTTP Status Codes
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `401` - Unauthorized (not logged in)
- `403` - Forbidden (CSRF token invalid)
- `405` - Method Not Allowed
- `429` - Too Many Requests (rate limited)
- `500` - Internal Server Error

### Error Response Format
```json
{
    "success": false,
    "message": "Error description",
    "error": "Technical error details" // Only in development
}
```

---

**Last Updated**: June 27, 2025  
**Version**: 2.0
action=list&profile_id=1&csrf_token=...
```
#### QR Kod Oluşturma
```
POST /admin/api/qr.php
action=create&profileId=1&csrf_token=...
```
#### QR Kod Silme
```
POST /admin/api/qr.php
action=delete&id=QR_ID&csrf_token=...
```

---

## 3. İstatistik API (`admin/api/stats.php`)

### Temel Kullanım
İstatistik sorguları için `action` parametresi kullanılır.

### Örnek İstekler
#### Genel İstatistik
```
POST /admin/api/stats.php
action=general&csrf_token=...
```
#### Profil Bazlı İstatistik
```
POST /admin/api/stats.php
action=profile&profile_id=1&csrf_token=...
```

---

## Hata Formatı
Başarısız isteklerde örnek hata yanıtı:
```
{
  "success": false,
  "message": "Açıklama"
}
```

## Başarı Formatı
Başarılı isteklerde örnek yanıt:
```
{
  "success": true,
  "data": {...}
}
```

---

Daha fazla detay ve parametreler için ilgili PHP dosyalarındaki açıklamalara bakınız.
