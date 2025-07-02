/**
 * Analytics Tracking JavaScript
 * Site trafiği, kullanıcı davranışları ve conversion tracking
 */

class QRAnalytics {
    constructor() {
        this.sessionStartTime = Date.now();
        this.currentPage = window.location.pathname;
        this.isPageVisible = true;
        this.timeOnPage = 0;
        
        this.init();
    }
    
    init() {
        // Sayfa yüklendiğinde tracking başlat
        this.trackPageView();
        
        // Scroll tracking
        this.initScrollTracking();
        
        // Time on page tracking
        this.initTimeTracking();
        
        // Click tracking
        this.initClickTracking();
        
        // Form interaction tracking
        this.initFormTracking();
        
        // Before unload event
        this.initBeforeUnload();
        
        console.log('QRAnalytics initialized');
    }
    
    /**
     * Sayfa görüntülemeyi kaydet
     */
    trackPageView() {
        const data = {
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer || '',
            screen_resolution: `${screen.width}x${screen.height}`,
            viewport_size: `${window.innerWidth}x${window.innerHeight}`,
            user_agent: navigator.userAgent
        };
        
        this.sendEvent('page_view', 'page_loaded', data);
    }
    
    /**
     * Scroll tracking
     */
    initScrollTracking() {
        let maxScroll = 0;
        let scrollMilestones = [25, 50, 75, 100];
        let trackedMilestones = new Set();
        
        const trackScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = Math.round((scrollTop / docHeight) * 100);
            
            maxScroll = Math.max(maxScroll, scrollPercent);
            
            // Milestone tracking
            scrollMilestones.forEach(milestone => {
                if (scrollPercent >= milestone && !trackedMilestones.has(milestone)) {
                    trackedMilestones.add(milestone);
                    this.sendEvent('user_interaction', 'scroll_milestone', {
                        milestone: milestone,
                        max_scroll: maxScroll
                    });
                }
            });
        };
        
        // Throttled scroll event
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(trackScroll, 250);
        });
    }
    
    /**
     * Time tracking
     */
    initTimeTracking() {
        // Page visibility API
        document.addEventListener('visibilitychange', () => {
            this.isPageVisible = !document.hidden;
            
            if (!this.isPageVisible) {
                this.sendTimeOnPage();
            } else {
                this.sessionStartTime = Date.now();
            }
        });
        
        // Periyodik time tracking
        setInterval(() => {
            if (this.isPageVisible) {
                this.timeOnPage += 30; // 30 saniye
                
                // Her 2 dakikada bir kaydet
                if (this.timeOnPage % 120 === 0) {
                    this.sendEvent('user_interaction', 'time_on_page', {
                        time_spent: this.timeOnPage,
                        page_url: this.currentPage
                    });
                }
            }
        }, 30000);
    }
    
    /**
     * Click tracking
     */
    initClickTracking() {
        // Önemli butonları track et
        document.addEventListener('click', (e) => {
            const target = e.target.closest('button, a, [onclick]');
            if (!target) return;
            
            const trackingData = {
                element_type: target.tagName.toLowerCase(),
                element_text: target.textContent?.trim() || '',
                element_id: target.id || '',
                element_class: target.className || '',
                page_url: window.location.href
            };
            
            // Özel tracking'ler
            if (target.id === 'orderBtn' || target.textContent?.includes('Sipariş Ver')) {
                this.trackOrderButtonClick(trackingData);
            } else if (target.id === 'whatsappWidget' || target.href?.includes('wa.me')) {
                this.trackWhatsAppClick(trackingData);
            } else if (target.closest('.social-platform-btn')) {
                this.trackSocialMediaAdd(trackingData);
            } else if (target.id === 'completeOrderBtn') {
                this.trackOrderComplete(trackingData);
            }
            
            // Genel click tracking
            this.sendEvent('user_interaction', 'click', trackingData);
        });
    }
    
    /**
     * Form interaction tracking
     */
    initFormTracking() {
        // Form başlangıcı
        document.addEventListener('focus', (e) => {
            if (e.target.matches('input, textarea, select')) {
                const form = e.target.closest('form');
                if (form && !form.dataset.tracked) {
                    form.dataset.tracked = 'true';
                    this.sendEvent('form_interaction', 'form_started', {
                        form_id: form.id || '',
                        form_class: form.className || '',
                        first_field: e.target.name || e.target.id || ''
                    });
                }
            }
        }, true);
        
        // Form field changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('input, textarea, select')) {
                this.sendEvent('form_interaction', 'field_completed', {
                    field_name: e.target.name || e.target.id || '',
                    field_type: e.target.type || e.target.tagName.toLowerCase(),
                    has_value: !!e.target.value
                });
            }
        });
        
        // Form submission
        document.addEventListener('submit', (e) => {
            const form = e.target;
            this.sendEvent('form_interaction', 'form_submitted', {
                form_id: form.id || '',
                form_action: form.action || '',
                form_method: form.method || 'get'
            });
        });
    }
    
    /**
     * Before unload tracking
     */
    initBeforeUnload() {
        window.addEventListener('beforeunload', () => {
            this.sendTimeOnPage();
            this.sendEvent('user_interaction', 'page_exit', {
                time_on_page: this.timeOnPage,
                exit_type: 'beforeunload'
            });
        });
    }
    
    /**
     * Sipariş butonu click tracking
     */
    trackOrderButtonClick(elementData) {
        this.sendEvent('conversion', 'order_button_clicked', {
            ...elementData,
            time_to_click: Date.now() - this.sessionStartTime
        });
        
        // Order funnel tracking
        this.sendFunnelEvent('order_clicked');
    }
    
    /**
     * WhatsApp click tracking
     */
    trackWhatsAppClick(elementData) {
        this.sendEvent('user_interaction', 'whatsapp_clicked', elementData);
    }
    
    /**
     * Social media platform ekleme tracking
     */
    trackSocialMediaAdd(elementData) {
        const platform = elementData.element_class?.match(/platform-(\w+)/)?.[1] || 'unknown';
        this.sendEvent('user_interaction', 'social_platform_added', {
            ...elementData,
            platform: platform
        });
    }
    
    /**
     * Sipariş tamamlama tracking
     */
    trackOrderComplete(elementData) {
        this.sendEvent('conversion', 'order_completed', {
            ...elementData,
            total_time: Date.now() - this.sessionStartTime
        });
        
        // Order funnel tracking
        this.sendFunnelEvent('order_completed');
    }
    
    /**
     * Order step tracking
     */
    trackOrderStep(step, stepData = {}) {
        this.sendEvent('conversion', `order_${step}_completed`, stepData);
        this.sendFunnelEvent(`${step}_completed`, stepData);
    }
    
    /**
     * Theme değişikliği tracking
     */
    trackThemeChange(themeName) {
        this.sendEvent('user_interaction', 'theme_changed', {
            theme_name: themeName,
            page_url: window.location.href
        });
    }
    
    /**
     * Payment method seçimi tracking
     */
    trackPaymentMethodSelect(paymentMethod) {
        this.sendEvent('conversion', 'payment_method_selected', {
            payment_method: paymentMethod
        });
    }
    
    /**
     * Event gönder
     */
    sendEvent(eventType, eventName, eventData = {}) {
        const data = {
            event_type: eventType,
            event_name: eventName,
            event_data: eventData,
            timestamp: Date.now(),
            page_url: window.location.href
        };
        
        // Async olarak gönder
        this.sendToServer('track_event', data);
    }
    
    /**
     * Funnel event gönder
     */
    sendFunnelEvent(step, stepData = {}) {
        const data = {
            step: step,
            step_data: stepData,
            timestamp: Date.now()
        };
        
        this.sendToServer('track_funnel', data);
    }
    
    /**
     * Time on page gönder
     */
    sendTimeOnPage() {
        if (this.timeOnPage > 0) {
            this.sendEvent('user_interaction', 'time_on_page_final', {
                total_time: this.timeOnPage,
                page_url: this.currentPage
            });
        }
    }
    
    /**
     * Server'a veri gönder
     */
    sendToServer(action, data) {
        // Beacon API kullan (sayfa kapanırken bile çalışır)
        if (navigator.sendBeacon) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('data', JSON.stringify(data));
            
            navigator.sendBeacon('/admin/api/analytics.php', formData);
        } else {
            // Fallback: fetch ile async
            fetch('/admin/api/analytics.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    data: data
                })
            }).catch(err => {
                console.warn('Analytics tracking failed:', err);
            });
        }
    }
}

// Global analytics instance
let qrAnalytics;

// DOM yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    qrAnalytics = new QRAnalytics();
    
    // Global fonksiyonları tanımla
    window.trackOrderStep = function(step, stepData) {
        if (qrAnalytics) {
            qrAnalytics.trackOrderStep(step, stepData);
        }
    };
    
    window.trackThemeChange = function(themeName) {
        if (qrAnalytics) {
            qrAnalytics.trackThemeChange(themeName);
        }
    };
    
    window.trackPaymentMethod = function(paymentMethod) {
        if (qrAnalytics) {
            qrAnalytics.trackPaymentMethodSelect(paymentMethod);
        }
    };
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QRAnalytics;
}
