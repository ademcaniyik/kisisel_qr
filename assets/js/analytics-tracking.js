/**
 * Analytics Tracking - Frontend event tracking
 */

(function() {
    'use strict';
    
    // Analytics tracking functions
    const Analytics = {
        
        // Track an event
        trackEvent: function(eventType, eventName, eventData) {
            if (typeof eventData === 'undefined') {
                eventData = {};
            }
            
            // Add current page URL
            eventData.page_url = window.location.pathname;
            eventData.timestamp = new Date().toISOString();
            
            // Send to server via fetch
            fetch('/admin/api/stats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'track_event',
                    event_type: eventType,
                    event_name: eventName,
                    event_data: eventData
                })
            }).catch(function(error) {
                // Silently fail to avoid breaking the page
                console.log('Analytics tracking failed:', error);
            });
        },
        
        // Track order funnel step
        trackOrderFunnel: function(step, stepData) {
            if (typeof stepData === 'undefined') {
                stepData = {};
            }
            
            fetch('/admin/api/stats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'track_order_funnel',
                    step: step,
                    step_data: stepData
                })
            }).catch(function(error) {
                console.log('Order funnel tracking failed:', error);
            });
        },
        
        // Initialize tracking
        init: function() {
            // Track page view
            this.trackEvent('page_view', 'page_loaded', {
                url: window.location.href,
                referrer: document.referrer
            });
            
            // Track order button clicks
            this.trackOrderButtons();
        },
        
        // Track order button clicks
        trackOrderButtons: function() {
            const self = this;
            
            // Find order buttons - multiple selectors to catch different buttons
            const orderButtonSelectors = [
                '.order-button',
                '.btn-order',
                'button[onclick*="showOrderForm"]',
                'button[onclick*="order"]',
                'a[href*="order"]',
                '.order-btn',
                '#orderBtn',
                'button.order',
                '.cta-button',
                '.btn-outline-hero',
                '.btn-primary-hero'
            ];
            
            orderButtonSelectors.forEach(function(selector) {
                const buttons = document.querySelectorAll(selector);
                buttons.forEach(function(button) {
                    // Skip if button doesn't contain order-related text
                    const buttonText = (button.textContent || button.innerText || '').toLowerCase();
                    if (!buttonText.includes('sipariş') && !buttonText.includes('order') && !buttonText.includes('sticker')) {
                        return;
                    }
                    
                    button.addEventListener('click', function(e) {
                        self.trackEvent('click', 'order_button_clicked', {
                            button_text: button.textContent || button.innerText || 'Order Button',
                            button_id: button.id || '',
                            button_class: button.className || '',
                            button_onclick: button.getAttribute('onclick') || ''
                        });
                        
                        self.trackOrderFunnel('order_clicked', {
                            button_element: selector,
                            button_text: buttonText
                        });
                    });
                });
            });
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Analytics.init();
        });
    } else {
        Analytics.init();
    }
    
    // Make Analytics available globally if needed
    window.Analytics = Analytics;
    
})();
