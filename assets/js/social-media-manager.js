// Sosyal medya platform bilgileri
const platforms = {
    facebook: {
        name: 'Facebook',
        icon: 'fab fa-facebook',
        placeholder: 'https://facebook.com/username'
    },
    x: {
        name: 'X',
        icon: 'fa-brands fa-x-twitter',
        placeholder: 'https://x.com/username'
    },
    instagram: {
        name: 'Instagram',
        icon: 'fab fa-instagram',
        placeholder: 'https://instagram.com/username'
    },
    linkedin: {
        name: 'LinkedIn',
        icon: 'fab fa-linkedin',
        placeholder: 'https://linkedin.com/in/username'
    },
    youtube: {
        name: 'YouTube',
        icon: 'fab fa-youtube',
        placeholder: 'https://youtube.com/c/username'
    },
    tiktok: {
        name: 'TikTok',
        icon: 'fab fa-tiktok',
        placeholder: 'https://tiktok.com/@username'
    },
    website: {
        name: 'Website',
        icon: 'fas fa-globe',
        placeholder: 'https://example.com'
    }
};

// URL formatını kontrol et
function validateURL(url, platform) {
    if (!url) return false;
    try {
        new URL(url);
        
        // Platform özel kontroller
        switch (platform) {
            case 'facebook':
                return url.includes('facebook.com');
            case 'x':
                return url.includes('x.com');
            case 'instagram':
                return url.includes('instagram.com');
            case 'linkedin':
                return url.includes('linkedin.com');
            case 'youtube':
                return url.includes('youtube.com');
            case 'tiktok':
                return url.includes('tiktok.com');
            default:
                return true; // website için genel URL kontrolü yeterli
        }
    } catch {
        return false;
    }
}

// Yeni sosyal medya bağlantısı ekle
function addSocialLink() {
    const container = document.getElementById('socialLinksContainer');
    const linkDiv = document.createElement('div');
    linkDiv.className = 'social-link-item mb-2';
    
    const select = document.createElement('select');
    select.className = 'form-select';
    select.name = 'social_links[platform][]';
    select.style.maxWidth = '200px';
    
    // Platform seçeneklerini ekle
    select.innerHTML = `
        <option value="">Platform Seçin</option>
        ${Object.entries(platforms).map(([value, platform]) => `
            <option value="${value}">
                ${platform.name}
            </option>
        `).join('')}
    `;

    const inputGroup = document.createElement('div');
    inputGroup.className = 'input-group';
    inputGroup.appendChild(select);

    const urlInput = document.createElement('input');
    urlInput.type = 'text';
    urlInput.className = 'form-control';
    urlInput.name = 'social_links[url][]';
    urlInput.placeholder = 'URL';

    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'btn btn-danger';
    removeButton.innerHTML = '<i class="fas fa-trash"></i>';
    removeButton.onclick = function() { removeSocialLink(this); };

    inputGroup.appendChild(urlInput);
    inputGroup.appendChild(removeButton);
    linkDiv.appendChild(inputGroup);
    container.appendChild(linkDiv);

    // Platform seçimi değiştiğinde placeholder'ı güncelle
    select.addEventListener('change', function() {
        const platform = platforms[this.value];
        if (platform) {
            urlInput.placeholder = platform.placeholder;
        }
    });

    // URL doğrulama
    urlInput.addEventListener('blur', function() {
        const platform = select.value;
        const isValid = validateURL(this.value, platform);
        
        if (!isValid && this.value) {
            this.classList.add('is-invalid');
            
            // Var olan hata mesajını kaldır
            const existingFeedback = inputGroup.querySelector('.invalid-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Yeni hata mesajı ekle
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.style.display = 'block';
            feedback.textContent = `Lütfen geçerli bir ${platforms[platform]?.name || 'website'} URL'si girin`;
            inputGroup.appendChild(feedback);
        } else {
            this.classList.remove('is-invalid');
            const feedback = inputGroup.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.remove();
            }
        }
    });

    // Animasyonlu giriş efekti
    linkDiv.style.opacity = '0';
    linkDiv.style.transform = 'translateY(20px)';
    linkDiv.style.transition = 'all 0.3s ease';
    
    setTimeout(() => {
        linkDiv.style.opacity = '1';
        linkDiv.style.transform = 'translateY(0)';
    }, 10);
}

// Sosyal medya bağlantısını kaldır
function removeSocialLink(button) {
    const linkItem = button.closest('.social-link-item');
    
    // Animasyonlu çıkış efekti
    linkItem.style.opacity = '0';
    linkItem.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        linkItem.remove();
    }, 300);
}
