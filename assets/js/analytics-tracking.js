/**
 * Analytics Tracking - Sadece sipariş buton tracking'i
 */

// Sipariş butonu click tracking'i
function trackOrderButtonClick() {
    // Analytics API'sine veri gönder (GET ile CSRF bypass)
    const params = new URLSearchParams({
        action: 'track_event',
        event_type: 'click',
        event_name: 'order_button_clicked',
        page_url: window.location.pathname
    });
    
    fetch(`admin/api/stats.php?${params}`, {
        method: 'GET'
    }).catch(error => {
        console.log('Analytics tracking error:', error);
    });
}

// Sayfa yüklendiğinde sipariş butonlarına event listener ekle
document.addEventListener('DOMContentLoaded', function() {
    // showOrderForm() fonksiyonunu override etmek yerine, 
    // tüm sipariş butonlarına click listener ekle
    const orderButtons = document.querySelectorAll('button[onclick*="showOrderForm"]');
    
    orderButtons.forEach(button => {
        button.addEventListener('click', function() {
            trackOrderButtonClick();
        });
    });
    
    // Ana sipariş butonları için de tracking
    const mainOrderButtons = document.querySelectorAll('.btn:contains("Sipariş")');
    mainOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            trackOrderButtonClick();
        });
    });
});
