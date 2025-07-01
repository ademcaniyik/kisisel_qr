// Sosyal medya platform tanımları
const socialPlatforms = {
    instagram: { name: 'Instagram', placeholder: 'kullanici_adi', icon: 'fab fa-instagram' },
    x: { name: 'X', placeholder: 'kullanici_adi', icon: 'fab fa-x-twitter' },
    linkedin: { name: 'LinkedIn', placeholder: 'profil-adi', icon: 'fab fa-linkedin' },
    facebook: { name: 'Facebook', placeholder: 'profil.adi', icon: 'fab fa-facebook' },
    youtube: { name: 'YouTube', placeholder: 'kanal_adi', icon: 'fab fa-youtube' },
    tiktok: { name: 'TikTok', placeholder: 'kullanici_adi', icon: 'fab fa-tiktok' },
    whatsapp: { name: 'WhatsApp', placeholder: '905551234567', icon: 'fab fa-whatsapp' },
    website: { name: 'Website', placeholder: 'https://website.com', icon: 'fas fa-globe' },
    snapchat: { name: 'Snapchat', placeholder: 'kullanici_adi', icon: 'fab fa-snapchat' },
    discord: { name: 'Discord', placeholder: 'sunucu_davet_kodu', icon: 'fab fa-discord' },
    telegram: { name: 'Telegram', placeholder: 'kullanici_adi', icon: 'fab fa-telegram' },
    twitch: { name: 'Twitch', placeholder: 'kanal_adi', icon: 'fab fa-twitch' }
};

$(document).ready(function() {
    // Tema önizleme için event binding
    if (document.getElementById('theme')) {
        document.getElementById('theme').addEventListener('change', updateThemePreview);
        updateThemePreview();
    }
    if (document.getElementById('edit_theme')) {
        document.getElementById('edit_theme').addEventListener('change', updateEditThemePreview);
        updateEditThemePreview();
    }
});

// Sosyal medya bağlantısı ekleme fonksiyonu - Kullanıcı dostu username girişi
function addSocialLink(containerId = 'socialLinksContainer', platform = '', username = '') {
    const container = document.getElementById(containerId);
    if (!container) return;
    const linkDiv = document.createElement('div');
    linkDiv.className = 'input-group mb-2';
    const select = document.createElement('select');
    select.className = 'form-select';
    select.style.minWidth = '140px';
    select.style.maxWidth = '140px';
    Object.entries(socialPlatforms).forEach(([key, value]) => {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = `${value.name}`;
        if (platform === key) option.selected = true;
        select.appendChild(option);
    });
    const input = document.createElement('input');
    input.className = 'form-control';
    input.required = true;
    
    // Platform tipine göre input tipini ayarla - kullanıcı dostu
    if (platform === 'whatsapp') {
        input.type = 'tel';
        input.placeholder = '905551234567';
    } else if (platform === 'website') {
        input.type = 'url';
        input.placeholder = 'https://website.com';
    } else {
        input.type = 'text';
        input.placeholder = platform && socialPlatforms[platform] ? socialPlatforms[platform].placeholder : socialPlatforms[Object.keys(socialPlatforms)[0]].placeholder;
    }
    
    // Mevcut değeri ayarla - URL'den username çıkarma
    if (username) {
        if (platform === 'website' || platform === 'whatsapp') {
            input.value = username; // Website ve WhatsApp için tam değer
        } else {
            // Diğer platformlar için sadece username kısmını göster
            const cleanUsername = username.replace(/^https?:\/\/[^\/]+\//, '').replace(/\/$/, '');
            input.value = cleanUsername;
        }
    }
    
    select.addEventListener('change', (e) => {
        const plat = e.target.value;
        if (plat in socialPlatforms) {
            input.placeholder = socialPlatforms[plat].placeholder;
            
            // Platform'a göre input tipini güncelle
            if (plat === 'whatsapp') {
                input.type = 'tel';
                input.placeholder = '905551234567';
            } else if (plat === 'website') {
                input.type = 'url';
                input.placeholder = 'https://website.com';
            } else {
                input.type = 'text';
            }
        }
    });
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-outline-danger d-flex align-items-center justify-content-center';
    deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
    deleteButton.onclick = () => linkDiv.remove();
    linkDiv.appendChild(select);
    linkDiv.appendChild(input);
    linkDiv.appendChild(deleteButton);
    container.appendChild(linkDiv);
}

function updateEditThemePreview() {
    const select = document.getElementById('edit_theme');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const themePreview = document.getElementById('theme-preview');
    const previewCard = document.getElementById('preview-card');
    const previewButton = document.getElementById('preview-button');
    if (!themePreview || !previewCard || !previewButton) return;
    const backgroundColor = selectedOption.getAttribute('data-background-color');
    const textColor = selectedOption.getAttribute('data-text-color');
    const accentColor = selectedOption.getAttribute('data-accent-color');
    const cardBackground = selectedOption.getAttribute('data-card-background');
    const fontFamily = selectedOption.getAttribute('data-font-family');
    const buttonStyle = selectedOption.getAttribute('data-button-style');
    themePreview.style.setProperty('--bg-color', backgroundColor);
    themePreview.style.backgroundColor = backgroundColor;
    previewCard.style.setProperty('--card-bg', cardBackground);
    previewCard.style.backgroundColor = cardBackground;
    previewCard.style.color = textColor;
    if (fontFamily) previewCard.style.fontFamily = fontFamily;
    previewButton.className = 'btn theme-button';
    previewButton.style = '';
    if (buttonStyle === 'rounded') {
        previewButton.classList.add('btn-rounded');
        previewButton.style.backgroundColor = accentColor;
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = '#fff';
    } else if (buttonStyle === 'outlined') {
        previewButton.classList.add('btn-outline-primary');
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = accentColor;
    } else {
        previewButton.classList.add('btn-primary');
        previewButton.style.backgroundColor = accentColor;
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = '#fff';
    }
    const cardTitle = previewCard.querySelector('.card-title');
    const cardText = previewCard.querySelector('.card-text');
    if (cardTitle) cardTitle.style.color = textColor;
    if (cardText) cardText.style.color = textColor;
    previewButton.textContent = 'Sosyal Medya Profili';
}

// Profil oluşturma fonksiyonu (githubicin ile birebir)
async function createProfile() {
    try {
        showLoader();
        const form = document.getElementById('createProfileForm');
        const formData = new FormData();
        
        // Ana bilgileri ekle
        formData.append('name', document.getElementById('name').value);
        formData.append('bio', document.getElementById('bio').value);
        formData.append('phone', document.getElementById('phone').value);
        formData.append('theme', document.getElementById('theme').value);
        
        // IBAN ve Kan Grubu bilgilerini ekle
        formData.append('iban', document.getElementById('iban').value || '');
        formData.append('blood_type', document.getElementById('blood_type').value || '');
        
        // Profil fotoğrafını ekle
        const photoInput = document.getElementById('photo');
        if (photoInput.files.length > 0) {
            formData.append('photo', photoInput.files[0]);
        }
        
        // Sosyal medya bağlantılarını ekle - kullanıcı adlarını URL'e dönüştür
        const socialLinks = {};
        const socialInputs = document.querySelectorAll('#socialLinksContainer .input-group');
        socialInputs.forEach(group => {
            const platform = group.querySelector('select').value;
            const userInput = group.querySelector('input').value.trim();
            if (userInput) {
                // Platform tipine göre tam URL oluştur
                let fullUrl = userInput;
                if (platform === 'instagram') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://instagram.com/${userInput}`;
                } else if (platform === 'x') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://twitter.com/${userInput}`;
                } else if (platform === 'linkedin') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://linkedin.com/in/${userInput}`;
                } else if (platform === 'facebook') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://facebook.com/${userInput}`;
                } else if (platform === 'youtube') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://youtube.com/@${userInput}`;
                } else if (platform === 'tiktok') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://tiktok.com/@${userInput}`;
                } else if (platform === 'snapchat') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://snapchat.com/add/${userInput}`;
                } else if (platform === 'discord') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://discord.gg/${userInput}`;
                } else if (platform === 'telegram') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://t.me/${userInput}`;
                } else if (platform === 'twitch') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://twitch.tv/${userInput}`;
                } else if (platform === 'whatsapp') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://wa.me/${userInput}`;
                } else if (platform === 'website') {
                    fullUrl = userInput.startsWith('http') ? userInput : `https://${userInput}`;
                }
                socialLinks[platform] = fullUrl;
            }
        });
        formData.append('socialLinks', JSON.stringify(socialLinks));
        
        // Fetch API ile istek gönder
        const response = await fetch('/kisisel_qr/admin/api/create_profile.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        hideLoader();
        
        if (data.success) {
            showToast('Profil başarıyla oluşturuldu!', 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('Hata: ' + data.message, 'danger');
        }
    } catch (error) {
        hideLoader();
        console.error('Error:', error);
        showToast('Bir hata oluştu: ' + error, 'danger');
    }
}

// Tema önizleme fonksiyonu (githubicin ile birebir)
function updateThemePreview() {
    const select = document.getElementById('theme');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const themePreview = document.getElementById('theme-preview');
    const previewCard = document.getElementById('preview-card');
    const previewButton = document.getElementById('preview-button');
    if (!themePreview || !previewCard || !previewButton) return;
    const backgroundColor = selectedOption.getAttribute('data-background-color');
    const textColor = selectedOption.getAttribute('data-text-color');
    const accentColor = selectedOption.getAttribute('data-accent-color');
    const cardBackground = selectedOption.getAttribute('data-card-background');
    const fontFamily = selectedOption.getAttribute('data-font-family');
    const buttonStyle = selectedOption.getAttribute('data-button-style');
    themePreview.style.setProperty('--bg-color', backgroundColor);
    themePreview.style.backgroundColor = backgroundColor;
    previewCard.style.setProperty('--card-bg', cardBackground);
    previewCard.style.backgroundColor = cardBackground;
    previewCard.style.color = textColor;
    if (fontFamily) previewCard.style.fontFamily = fontFamily;
    previewButton.className = 'btn theme-button';
    previewButton.style = '';
    if (buttonStyle === 'rounded') {
        previewButton.classList.add('btn-rounded');
        previewButton.style.backgroundColor = accentColor;
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = '#fff';
    } else if (buttonStyle === 'outlined') {
        previewButton.classList.add('btn-outline-primary');
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = accentColor;
    } else {
        previewButton.classList.add('btn-primary');
        previewButton.style.backgroundColor = accentColor;
        previewButton.style.borderColor = accentColor;
        previewButton.style.color = '#fff';
    }
    const cardTitle = previewCard.querySelector('.card-title');
    const cardText = previewCard.querySelector('.card-text');
    if (cardTitle) cardTitle.style.color = textColor;
    if (cardText) cardText.style.color = textColor;
    previewButton.textContent = 'Sosyal Medya Profili';
}

function showLoader() {
    // Yükleme animasyonu devre dışı
}
function hideLoader() {
    // Yükleme animasyonu devre dışı
}

// QR oluşturma fonksiyonu (githubicin ile birebir)
async function createQRForProfile(profileId) {
    try {
        showLoader();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('/kisisel_qr/admin/api/qr.php?action=create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `profileId=${profileId}&csrf_token=${encodeURIComponent(csrfToken)}`
        });
        const data = await response.json();
        hideLoader();
        if (data.success) {
            showToast('QR kod başarıyla oluşturuldu!', 'success');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('QR kod oluşturulurken bir hata oluştu: ' + data.message, 'danger');
        }
    } catch (error) {
        hideLoader();
        console.error('Error:', error);
        showToast('Bir hata oluştu!', 'danger');
    }
}

// Modal kapandığında formu temizle (create ve edit için)
$(function() {
    $('#createProfileModal').on('hidden.bs.modal', function () {
        $('#createProfileForm')[0].reset();
        // Hata mesajı veya özel alanlar varsa onları da temizle
        $('#socialLinksContainer').html('');
        // Tema önizleme sıfırlama
        if (typeof updateThemePreview === 'function') updateThemePreview();
    });
    $('#editProfileModal').on('hidden.bs.modal', function () {
        $('#editProfileForm')[0].reset();
        $('#edit_socialLinksContainer').html('');
        $('#edit_photo_preview').hide();
        if (typeof updateEditThemePreview === 'function') updateEditThemePreview();
    });
});

// Profil silme fonksiyonu
function deleteProfile(profileId) {
    if (confirm('Bu profili silmek istediğinize emin misiniz?')) {
        var formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', profileId);
        $.ajax({
            url: '/kisisel_qr/admin/api/profile.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    // Log admin action
                    fetch('/kisisel_qr/admin/api/log_action.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=delete_profile&id=${encodeURIComponent(profileId)}`
                    });
                    alert('Profil başarıyla silindi!');
                    location.reload();
                } else {
                    alert('Profil silinirken bir hata oluştu: ' + (res.message || ''));
                }
            },
            error: function() { alert('Sunucu hatası!'); }
        });
    }
}
