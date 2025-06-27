// Tema değişkenlerini ayarla
function applyTheme() {
    const themeData = {
        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--background-color').trim(),
        textColor: getComputedStyle(document.documentElement).getPropertyValue('--text-color').trim(),
        accentColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim(),
        cardBackground: getComputedStyle(document.documentElement).getPropertyValue('--card-background').trim(),
        fontFamily: getComputedStyle(document.documentElement).getPropertyValue('--font-family').trim(),
        buttonStyle: document.body.dataset.buttonStyle || 'default'
    };

    // Sosyal medya bağlantılarına stil uygula
    const socialLinks = document.querySelectorAll('.social-link');
    socialLinks.forEach(link => {
        if (!link.classList.contains('facebook') && 
            !link.classList.contains('twitter') && 
            !link.classList.contains('instagram') && 
            !link.classList.contains('linkedin') && 
            !link.classList.contains('github') && 
            !link.classList.contains('youtube')) {
            
            link.classList.add(`button-${themeData.buttonStyle}`);
        }
    });

    // Telefon numarası butonuna stil uygula
    const phoneButton = document.querySelector('.phone-number');
    if (phoneButton) {
        phoneButton.classList.add(`phone-button-${themeData.buttonStyle}`);
    }

    console.log('Tema başarıyla uygulandı:', themeData);
}

// Sayfa yüklendiğinde temayı uygula
document.addEventListener('DOMContentLoaded', applyTheme);
