/**
 * Analytics Tracking JavaScript
 * Kullanıcı davranışlarını ve site etkileşimlerini takip eder
 */

class AnalyticsTracker {
    constructor() {
        this.sessionStartTime = Date.now();
        this.apiEndpoint = 'admin/api/analytics.php';
        this.isTracking = true;
        this.pageViewTracked = false;
        
        // Page load'da başlat
        this.init();
    }
    
    init() {
        // Sayfa yüklendiğinde
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.trackPageView());
        } else {
            this.trackPageView();
        }
        
        // Sayfa kapatılırken session süresini kaydet
        window.addEventListener('beforeunload', () => this.trackSessionEnd());
        
        // Kullanıcı etkileşimlerini dinle
        this.setupEventListeners();
    }
    
    async trackPageView() {
        if (this.pageViewTracked) return;
        
        try {
            const page = window.location.pathname + window.location.search;
            await this.sendData('track_page_view', { page: page });
            this.pageViewTracked = true;
            console.log('📊 Page view tracked:', page);
        } catch (error) {
            console.error('Analytics tracking error:', error);
        }
    }
    
    async trackUserAction(action, details = null) {
        try {
            await this.sendData('track_user_action', {
                action_type: action,
                details: details
            });
            console.log('📊 User action tracked:', action, details);
        } catch (error) {
            console.error('Analytics tracking error:', error);
        }
    }
    
    async trackConversion(step, orderId = null) {
        try {
            await this.sendData('track_conversion', {
                step: step,
                order_id: orderId
            });
            console.log('📊 Conversion tracked:', step, orderId);
        } catch (error) {
            console.error('Analytics tracking error:', error);
        }
    }
    
    setupEventListeners() {
        // Sipariş butonu tıklamaları
        document.addEventListener('click', (e) => {
            // Sipariş ver butonu
            if (e.target.closest('#orderBtn') || e.target.closest('[onclick*="showOrderForm"]')) {
                this.trackUserAction('order_button_click');
                this.trackConversion('order_button_clicked');
            }
            
            // Sosyal medya butonları
            if (e.target.closest('.social-platform-btn')) {
                const platform = e.target.closest('.social-platform-btn').dataset.platform;
                this.trackUserAction('social_platform_selected', platform);
            }
            
            // WhatsApp widget
            if (e.target.closest('.whatsapp-widget') || e.target.closest('[onclick*="openWhatsApp"]')) {
                this.trackUserAction('whatsapp_widget_click');
            }
            
            // Tema değişimi
            if (e.target.closest('#customerTheme')) {
                this.trackUserAction('theme_changed');
            }
            
            // Dış linkler
            if (e.target.tagName === 'A' && e.target.href) {
                const url = new URL(e.target.href);
                if (url.hostname !== window.location.hostname) {
                    this.trackUserAction('external_link_click', url.hostname);
                }
            }
        });
        
        // Form submission'ları
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'orderForm') {
                this.trackUserAction('order_form_submitted');
            }
        });
        
        // Scroll tracking (sayfa sonuna kadar scroll)
        let maxScroll = 0;
        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                
                // %25, %50, %75, %100 milestone'larını takip et
                if ([25, 50, 75, 100].includes(scrollPercent)) {
                    this.trackUserAction('scroll_milestone', `${scrollPercent}%`);
                }
            }
        });
        
        // Video/media etkileşimleri (varsa)
        document.addEventListener('play', (e) => {
            if (e.target.tagName === 'VIDEO') {
                this.trackUserAction('video_play');
            }
        }, true);
        
        // Form field odaklanmaları (engagement tracking)
        document.addEventListener('focus', (e) => {
            if (e.target.form && e.target.form.id === 'orderForm') {
                this.trackUserAction('form_field_focus', e.target.name || e.target.id);
            }
        }, true);
    }
    
    async sendData(action, data) {
        if (!this.isTracking) return;
        
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            // Silent fail - analytics hatası sayfa işleyişini bozmamalı
            console.warn('Analytics request failed:', error);
            return null;
        }
    }
    
    trackSessionEnd() {
        const sessionDuration = Math.round((Date.now() - this.sessionStartTime) / 1000);
        this.trackUserAction('session_end', `${sessionDuration}s`);
    }
    
    // Order system integration
    trackOrderStep(step, orderId = null) {
        const stepMapping = {
            'modal_opened': 'order_modal_opened',
            'step1_completed': 'order_step1_completed',
            'step2_opened': 'order_step2_opened',
            'payment_method_selected': 'payment_method_selected',
            'order_completed': 'order_completed'
        };
        
        if (stepMapping[step]) {
            this.trackConversion(stepMapping[step], orderId);
        }
    }
    
    // E-commerce tracking
    trackPurchase(orderId, value, currency = 'TRY') {
        this.trackUserAction('purchase', JSON.stringify({
            order_id: orderId,
            value: value,
            currency: currency
        }));
        this.trackConversion('order_completed', orderId);
    }
    
    // Hata tracking
    trackError(error, context = null) {
        this.trackUserAction('javascript_error', JSON.stringify({
            message: error.message,
            stack: error.stack?.substring(0, 500), // Çok uzun olmasın
            context: context
        }));
    }
}

// Global error handling
window.addEventListener('error', (e) => {
    if (window.analyticsTracker) {
        window.analyticsTracker.trackError(e.error, 'global_error');
    }
});

// Promise rejection handling
window.addEventListener('unhandledrejection', (e) => {
    if (window.analyticsTracker) {
        window.analyticsTracker.trackError(new Error(e.reason), 'unhandled_promise');
    }
});

// Initialize analytics tracker
document.addEventListener('DOMContentLoaded', function() {
    window.analyticsTracker = new AnalyticsTracker();
    
    // Global erişim için
    window.trackAnalytics = {
        action: (action, details) => window.analyticsTracker.trackUserAction(action, details),
        conversion: (step, orderId) => window.analyticsTracker.trackConversion(step, orderId),
        orderStep: (step, orderId) => window.analyticsTracker.trackOrderStep(step, orderId),
        purchase: (orderId, value) => window.analyticsTracker.trackPurchase(orderId, value),
        error: (error, context) => window.analyticsTracker.trackError(error, context)
    };
    
    console.log('📊 Analytics tracking initialized');
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AnalyticsTracker;
}
