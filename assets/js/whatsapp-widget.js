/**
 * WhatsApp Widget JavaScript
 * Modüler WhatsApp destek widget'ı
 */

class WhatsAppWidget {
    constructor(config = {}) {
        this.config = {
            phoneNumber: config.phoneNumber || '905349334631',
            message: config.message || 'Merhaba! Kişisel QR sistemi hakkında bilgi almak istiyorum. Yardımcı olabilir misiniz?',
            tooltipText: config.tooltipText || 'Merhaba! Size nasıl yardımcı olabilirim? 💬',
            buttonText: config.buttonText || 'Yardım',
            position: config.position || 'bottom-right', // bottom-right, bottom-left, top-right, top-left
            showOnPages: config.showOnPages || ['index'], // hangi sayfalarda gösterilecek
            hideOnModals: config.hideOnModals !== false, // modal açıldığında gizlensin mi
            analytics: config.analytics !== false // analytics tracking
        };
        
        this.init();
    }
    
    init() {
        // Sayfa kontrolü
        if (!this.shouldShowOnCurrentPage()) {
            return;
        }
        
        this.createWidget();
        this.bindEvents();
    }
    
    shouldShowOnCurrentPage() {
        const currentPage = this.getCurrentPageName();
        console.log('WhatsApp Widget - Current page:', currentPage);
        console.log('WhatsApp Widget - Show on pages:', this.config.showOnPages);
        return this.config.showOnPages.includes(currentPage) || this.config.showOnPages.includes('*');
    }
    
    getCurrentPageName() {
        const path = window.location.pathname;
        const filename = path.split('/').pop();
        
        console.log('WhatsApp Widget - Full path:', path);
        console.log('WhatsApp Widget - Filename:', filename);
        
        // Ana sayfa kontrolleri
        if (!filename || filename === '' || filename === '/' || path.endsWith('/')) {
            return 'index';
        }
        
        // index.php kontrolü
        if (filename === 'index.php') {
            return 'index';
        }
        
        return filename.replace('.php', '').replace('.html', '') || 'index';
    }
    
    createWidget() {
        console.log('WhatsApp Widget - Creating widget...');
        
        const widget = document.createElement('div');
        widget.className = 'whatsapp-widget';
        widget.id = 'whatsappWidget';
        
        const positionClass = this.getPositionClass();
        widget.classList.add(positionClass);
        
        widget.innerHTML = `
            <div class="whatsapp-button" onclick="window.whatsappWidget.openWhatsApp()">
                <i class="fab fa-whatsapp"></i>
                <span class="whatsapp-text">${this.config.buttonText}</span>
            </div>
            <div class="whatsapp-tooltip">
                ${this.config.tooltipText}
            </div>
        `;
        
        document.body.appendChild(widget);
        
        console.log('WhatsApp Widget - Widget created and added to DOM');
        
        // Global referans oluştur
        window.whatsappWidget = this;
    }
    
    getPositionClass() {
        const positions = {
            'bottom-right': 'whatsapp-bottom-right',
            'bottom-left': 'whatsapp-bottom-left',
            'top-right': 'whatsapp-top-right',
            'top-left': 'whatsapp-top-left'
        };
        
        return positions[this.config.position] || 'whatsapp-bottom-right';
    }
    
    bindEvents() {
        if (this.config.hideOnModals) {
            this.bindModalEvents();
        }
    }
    
    bindModalEvents() {
        // Bootstrap modal events
        document.addEventListener('show.bs.modal', () => {
            this.hide();
        });
        
        document.addEventListener('hidden.bs.modal', () => {
            this.show();
        });
        
        // Generic modal events
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', () => this.hide());
            modal.addEventListener('hidden.bs.modal', () => this.show());
        });
    }
    
    openWhatsApp() {
        const whatsappUrl = `https://wa.me/${this.config.phoneNumber}?text=${encodeURIComponent(this.config.message)}`;
        
        // Analytics tracking
        if (this.config.analytics && typeof gtag !== 'undefined') {
            gtag('event', 'whatsapp_widget_click', {
                'event_category': 'engagement',
                'event_label': 'help_request',
                'page': this.getCurrentPageName()
            });
        }
        
        // Console log for debugging
        console.log('WhatsApp Widget clicked:', {
            phone: this.config.phoneNumber,
            message: this.config.message,
            page: this.getCurrentPageName()
        });
        
        // WhatsApp'ı yeni sekmede aç
        window.open(whatsappUrl, '_blank');
    }
    
    show() {
        const widget = document.getElementById('whatsappWidget');
        if (widget) {
            widget.style.display = 'block';
        }
    }
    
    hide() {
        const widget = document.getElementById('whatsappWidget');
        if (widget) {
            widget.style.display = 'none';
        }
    }
    
    destroy() {
        const widget = document.getElementById('whatsappWidget');
        if (widget) {
            widget.remove();
        }
        
        if (window.whatsappWidget === this) {
            delete window.whatsappWidget;
        }
    }
    
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
    }
}

// Auto-initialize widget when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('WhatsApp Widget - DOMContentLoaded fired, initializing...');
    
    // Default configuration - istediğiniz zaman değiştirebilirsiniz
    new WhatsAppWidget({
        phoneNumber: '905349334631',
        message: 'Merhaba! Kişisel QR sistemi hakkında bilgi almak istiyorum. Yardımcı olabilir misiniz?',
        tooltipText: 'Merhaba! Size nasıl yardımcı olabilirim? 💬',
        buttonText: 'Yardım',
        showOnPages: ['*'], // TÜM sayfalarda göster (debug için)
        hideOnModals: true,
        analytics: true
    });
});
