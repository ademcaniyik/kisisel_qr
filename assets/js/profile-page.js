/**
 * Profile Page JavaScript Functions
 * Modern, optimized and modular approach
 */

// Profile Page Module
const ProfilePage = {
    
    // Initialize the profile page
    init() {
        this.setupWebPSupport();
        this.setupImageHandling();
        this.setupLazyLoading();
        this.bindEvents();
    },

    // WebP support detection
    setupWebPSupport() {
        function supportsWebP() {
            return new Promise((resolve) => {
                const webP = new Image();
                webP.onload = webP.onerror = () => resolve(webP.height === 2);
                webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
            });
        }

        // Add no-webp class if WebP is not supported
        supportsWebP().then((supported) => {
            if (!supported) {
                document.documentElement.classList.add('no-webp');
            }
        });
    },

    // Enhanced error handling for profile photo
    setupImageHandling() {
        const profilePhoto = document.querySelector('.profile-photo');
        if (profilePhoto) {
            profilePhoto.addEventListener('error', function() {
                this.classList.add('error');
                this.alt = 'Profil fotoğrafı yüklenemedi';
                if (!this.src.includes('default-profile.svg')) {
                    this.src = '/kisisel_qr/assets/images/default-profile.svg';
                }
            });

            profilePhoto.addEventListener('load', function() {
                this.classList.add('loaded');
                this.classList.remove('error');
            });
        }
    },

    // Intersection Observer for additional lazy loading
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },

    // Bind events
    bindEvents() {
        // Footer click event
        const footer = document.querySelector('.qr-footer-ad');
        if (footer) {
            footer.addEventListener('click', () => {
                window.open('https://acdisoftware.com.tr/kisisel_qr', '_blank');
            });
        }
    }
};

// Clipboard utilities
const ClipboardUtils = {
    
    // Copy to clipboard function with feedback
    copyToClipboard(text, message = 'Kopyalandı!', feedbackId = null) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => this.showCopyFeedback(message, feedbackId))
                .catch(() => this.fallbackCopyText(text, message, feedbackId));
        } else {
            this.fallbackCopyText(text, message, feedbackId);
        }
    },

    // Fallback copy method for older browsers
    fallbackCopyText(text, message, feedbackId) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            this.showCopyFeedback(message, feedbackId);
        } catch (err) {
            this.showCopyFeedback('Kopyalama başarısız!', feedbackId);
        }
        
        textArea.remove();
    },

    // Show copy feedback
    showCopyFeedback(message, feedbackId) {
        if (feedbackId) {
            // Use specific feedback element
            const feedback = document.getElementById(feedbackId);
            if (feedback) {
                feedback.querySelector('span').textContent = message;
                feedback.classList.add('show');
                setTimeout(() => {
                    feedback.classList.remove('show');
                }, 2000);
                return;
            }
        }

        // Fallback to general toast
        this.showToast(message);
    },

    // Show toast notification
    showToast(message) {
        // Remove existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--accent-color, #007bff);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
};

// IBAN specific functions
const IBANUtils = {
    
    // Copy IBAN function
    copyIban() {
        const ibanElement = document.getElementById('iban-number');
        if (!ibanElement) {
            console.error('IBAN element not found');
            return;
        }

        const ibanText = ibanElement.textContent.replace(/\s/g, ''); // Remove spaces
        
        // Use clipboard utilities
        ClipboardUtils.copyToClipboard(ibanText, 'IBAN başarıyla kopyalandı!');
    }
};

// Global functions for backward compatibility
function copyToClipboard(text, message, feedbackId) {
    ClipboardUtils.copyToClipboard(text, message, feedbackId);
}

function showToast(message) {
    ClipboardUtils.showToast(message);
}

function copyIban() {
    IBANUtils.copyIban();
}

// Legacy fallback functions
function fallbackCopyText(text, message, feedbackId) {
    ClipboardUtils.fallbackCopyText(text, message, feedbackId);
}

function showCopyFeedback(message, feedbackId) {
    ClipboardUtils.showCopyFeedback(message, feedbackId);
}

function fallbackCopyTextToClipboard(text) {
    ClipboardUtils.fallbackCopyText(text, 'IBAN başarıyla kopyalandı!');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    ProfilePage.init();
});

// Export for module usage (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ProfilePage,
        ClipboardUtils,
        IBANUtils
    };
}
