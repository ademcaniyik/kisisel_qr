function updateThemePreview(themeData) {
    const preview = document.getElementById('theme-preview');
    if (!preview) return;

    // Tema verilerini ayarla
    preview.style.backgroundColor = themeData.background_color;
    preview.style.color = themeData.text_color;
    preview.style.fontFamily = themeData.font_family;
    preview.style.transition = 'all 0.3s ease';
    preview.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    preview.style.border = `1px solid ${adjustColor(themeData.background_color, -20)}`;

    // Örnek kartın stilini güncelle
    const previewCard = document.getElementById('preview-card');
    if (previewCard) {
        previewCard.style.backgroundColor = themeData.card_background;
        previewCard.style.transition = 'all 0.3s ease';
        previewCard.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
        previewCard.style.border = `1px solid ${adjustColor(themeData.card_background, -10)}`;
        
        // Kart başlığı ve metin stilini güncelle
        const cardTitle = previewCard.querySelector('.card-title');
        if (cardTitle) {
            cardTitle.style.color = themeData.text_color;
            cardTitle.style.fontWeight = '600';
            cardTitle.style.marginBottom = '1rem';
        }

        const cardText = previewCard.querySelector('.card-text');
        if (cardText) {
            cardText.style.color = adjustColor(themeData.text_color, -20);
            cardText.style.fontSize = '0.95rem';
            cardText.style.lineHeight = '1.6';
        }
        
        // Buton stilini güncelle
        const previewButton = document.getElementById('preview-button');
        if (previewButton) {
            previewButton.className = 'btn'; // Reset class
            previewButton.style.backgroundColor = themeData.accent_color;
            previewButton.style.color = '#ffffff';
            previewButton.style.border = 'none';
            previewButton.style.transition = 'all 0.3s ease';
            previewButton.style.padding = '0.5rem 1.5rem';
            previewButton.style.fontSize = '0.95rem';
            previewButton.style.cursor = 'pointer';
            
            // Hover efekti
            previewButton.onmouseover = function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            };
            
            previewButton.onmouseout = function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            };
            
            switch(themeData.button_style) {
                case 'rounded':
                    previewButton.style.borderRadius = '10px';
                    break;
                case 'pill':
                    previewButton.style.borderRadius = '50px';
                    break;
                case 'flat':
                    previewButton.style.borderRadius = '0';
                    previewButton.style.borderBottom = `3px solid ${adjustColor(themeData.accent_color, -30)}`;
                    break;
                case 'soft':
                    previewButton.style.borderRadius = '8px';
                    previewButton.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                    previewButton.style.backgroundColor = adjustColor(themeData.accent_color, 20);
                    break;
                case 'gradient':
                    const color = themeData.accent_color;
                    previewButton.style.background = `linear-gradient(45deg, ${color}, ${adjustColor(color, 40)})`;
                    previewButton.style.borderRadius = '8px';
                    break;
                case 'modern':
                    previewButton.style.borderRadius = '12px';
                    previewButton.style.textTransform = 'uppercase';
                    previewButton.style.letterSpacing = '1px';
                    previewButton.style.fontWeight = '600';
                    previewButton.style.fontSize = '0.85rem';
                    break;
            }
        }
    }

    // Tema ismi ve açıklama ekle
    const themeName = document.createElement('div');
    themeName.className = 'theme-name';
    themeName.innerHTML = `<small class="text-muted">Seçili Tema: ${document.getElementById('theme').options[document.getElementById('theme').selectedIndex].text}</small>`;
    themeName.style.position = 'absolute';
    themeName.style.bottom = '8px';
    themeName.style.right = '12px';
    themeName.style.fontSize = '0.8rem';
    
    // Varsa eski tema ismini kaldır
    const oldThemeName = preview.querySelector('.theme-name');
    if (oldThemeName) oldThemeName.remove();
    
    preview.appendChild(themeName);
}

function adjustColor(color, amount) {
    const hex = color.replace('#', '');
    const r = Math.min(255, Math.max(0, parseInt(hex.substring(0,2), 16) + amount));
    const g = Math.min(255, Math.max(0, parseInt(hex.substring(2,4), 16) + amount));
    const b = Math.min(255, Math.max(0, parseInt(hex.substring(4,6), 16) + amount));
    return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

// Tema değişikliğini dinle
document.addEventListener('DOMContentLoaded', function() {
    const themeSelect = document.getElementById('theme');
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const themeData = {
                background_color: selectedOption.dataset.backgroundColor,
                text_color: selectedOption.dataset.textColor,
                accent_color: selectedOption.dataset.accentColor,
                card_background: selectedOption.dataset.cardBackground,
                font_family: selectedOption.dataset.fontFamily,
                button_style: selectedOption.dataset.buttonStyle
            };
            updateThemePreview(themeData);
        });
    }
});
