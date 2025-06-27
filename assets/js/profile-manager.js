// Sosyal medya platform tanımları
const socialPlatforms = {
    facebook: { name: 'Facebook', placeholder: 'facebook.com/kullaniciadi' },
    twitter: { name: 'Twitter', placeholder: 'twitter.com/kullaniciadi' },
    instagram: { name: 'Instagram', placeholder: 'instagram.com/kullaniciadi' },
    linkedin: { name: 'LinkedIn', placeholder: 'linkedin.com/in/kullaniciadi' },
    github: { name: 'GitHub', placeholder: 'github.com/kullaniciadi' },
    youtube: { name: 'YouTube', placeholder: 'youtube.com/@kanal' }
};

$(document).ready(function() {
    $('#profilesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json'
        }
    });
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

// Sosyal medya bağlantısı ekleme fonksiyonu (githubicin ile birebir)
function addSocialLink(containerId = 'socialLinksContainer', platform = '', url = '') {
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
        option.textContent = value.name;
        if (platform === key) option.selected = true;
        select.appendChild(option);
    });
    const input = document.createElement('input');
    input.type = 'url';
    input.className = 'form-control';
    input.required = true;
    input.placeholder = platform && socialPlatforms[platform] ? socialPlatforms[platform].placeholder : socialPlatforms[Object.keys(socialPlatforms)[0]].placeholder;
    if (url) input.value = url;
    select.addEventListener('change', (e) => {
        const plat = e.target.value;
        if (plat in socialPlatforms) {
            input.placeholder = socialPlatforms[plat].placeholder;
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
        // Profil fotoğrafını ekle
        const photoInput = document.getElementById('photo');
        if (photoInput.files.length > 0) {
            formData.append('photo', photoInput.files[0]);
        }
        // Sosyal medya bağlantılarını ekle
        const socialLinks = {};
        const socialInputs = document.querySelectorAll('#socialLinksContainer .input-group');
        socialInputs.forEach(group => {
            const platform = group.querySelector('select').value;
            const url = group.querySelector('input[type="url"]').value;
            if (url) {
                socialLinks[platform] = url;
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
