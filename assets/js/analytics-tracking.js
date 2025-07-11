/**
 * Analytics Tracking - Frontend event tracking
 */

(function() {
    'use strict';
    
    // Prevent multiple initialization
    if (window.AnalyticsInitialized) {
        console.log('🔍 Analytics: Already initialized, skipping...');
        return;
    }
    
    // Analytics tracking functions
    const Analytics = {
        
        // Track an event
        trackEvent: function(eventType, eventName, eventData) {
            console.log('🔍 Analytics: Tracking event', eventType, eventName, eventData);
            
            if (typeof eventData === 'undefined') {
                eventData = {};
            }
            
            // Add current page URL
            eventData.page_url = window.location.pathname;
            eventData.timestamp = new Date().toISOString();
            
            // Send to server via fetch
            fetch('admin/api/analytics.php', {
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
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Analytics: Event tracked successfully', data);
            })
            .catch(function(error) {
                // Silently fail to avoid breaking the page
                console.log('❌ Analytics tracking failed:', error);
            });
        },
        
        // Track order funnel step
        trackOrderFunnel: function(step, stepData) {
            console.log('🔍 Analytics: Tracking order funnel', step, stepData);
            
            if (typeof stepData === 'undefined') {
                stepData = {};
            }
            
            fetch('admin/api/analytics.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'track_order_funnel',
                    step: step,
                    step_data: stepData
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Analytics: Order funnel tracked successfully', data);
            })
            .catch(function(error) {
                console.log('❌ Order funnel tracking failed:', error);
            });
        },
        
        // Initialize tracking
        init: function() {
            console.log('🔍 Analytics: Initializing tracking system...');
            
            // Track page view
            this.trackEvent('page_view', 'page_loaded', {
                url: window.location.href,
                referrer: document.referrer
            });
            
            // Track order button clicks
            this.trackOrderButtons();
            
            console.log('✅ Analytics: Tracking system initialized');
        },
        
        // Track order button clicks
        trackOrderButtons: function() {
            const self = this;
            
            console.log('🔍 Analytics: Setting up order button tracking...');
            
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
            
            let buttonCount = 0;
            
            orderButtonSelectors.forEach(function(selector) {
                const buttons = document.querySelectorAll(selector);
                console.log(`🔍 Analytics: Found ${buttons.length} buttons for selector: ${selector}`);
                
                buttons.forEach(function(button) {
                    // Skip if button doesn't contain order-related text
                    const buttonText = (button.textContent || button.innerText || '').toLowerCase();
                    if (!buttonText.includes('sipariş') && !buttonText.includes('order') && !buttonText.includes('sticker')) {
                        console.log(`🔍 Analytics: Skipping button (no order text): ${buttonText}`);
                        return;
                    }
                    
                    // Prevent duplicate listeners - check if already has analytics listener
                    if (button.hasAttribute('data-analytics-tracked')) {
                        console.log(`🔍 Analytics: Button already tracked, skipping: ${buttonText}`);
                        return;
                    }
                    
                    console.log(`✅ Analytics: Adding click listener to button: ${buttonText}`);
                    buttonCount++;
                    
                    // Mark button as tracked
                    button.setAttribute('data-analytics-tracked', 'true');
                    
                    // Add click tracking with debounce
                    let lastClickTime = 0;
                    button.addEventListener('click', function(e) {
                        const currentTime = Date.now();
                        
                        // Prevent double clicks within 1 second
                        if (currentTime - lastClickTime < 1000) {
                            console.log('🔍 Analytics: Preventing duplicate click (too fast)');
                            return;
                        }
                        lastClickTime = currentTime;
                        
                        console.log('🔍 Analytics: Order button clicked!', button, buttonText);
                        
                        self.trackEvent('click', 'order_button_clicked', {
                            button_text: button.textContent || button.innerText || 'Order Button',
                            button_id: button.id || '',
                            button_class: button.className || '',
                            button_onclick: button.getAttribute('onclick') || '',
                            timestamp: new Date().toISOString()
                        });
                        
                        self.trackOrderFunnel('order_clicked', {
                            button_element: selector,
                            button_text: buttonText,
                            timestamp: new Date().toISOString()
                        });
                    });
                });
            });
            
            console.log(`✅ Analytics: Total order buttons tracked: ${buttonCount}`);
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Analytics.init();
            window.AnalyticsInitialized = true;
        });
    } else {
        Analytics.init();
        window.AnalyticsInitialized = true;
    }
    
    // Make Analytics available globally if needed
    window.Analytics = Analytics;
    
})();
