/**
 * Image Cleanup and Optimization Utilities
 * Frontend için resim temizleme ve optimizasyon yardımcıları
 */

class ImageCleanupUtility {
    constructor() {
        this.cleanupEndpoint = '/kisisel_qr/admin/api/profile.php';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.observerOptions = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        };
        this.lazyImageObserver = null;
        this.init();
    }

    init() {
        this.setupLazyLoading();
        this.setupImageErrorHandling();
        this.setupFormValidation();
    }

    /**
     * Lazy loading için Intersection Observer setup
     */
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            this.lazyImageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const picture = img.closest('picture');
                        
                        if (picture) {
                            // Picture element için source'ları aktif et
                            picture.querySelectorAll('source').forEach(source => {
                                if (source.dataset.srcset) {
                                    source.srcset = source.dataset.srcset;
                                    source.removeAttribute('data-srcset');
                                }
                            });
                        }
                        
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            }, this.observerOptions);

            this.observeLazyImages();
        }
    }

    /**
     * Lazy loading için resimleri gözlemle
     */
    observeLazyImages() {
        const lazyImages = document.querySelectorAll('img[data-src], img.lazy');
        lazyImages.forEach(img => {
            this.lazyImageObserver.observe(img);
        });
    }

    /**
     * Resim hata durumları için fallback
     */
    setupImageErrorHandling() {
        document.addEventListener('error', (event) => {
            if (event.target.tagName === 'IMG') {
                const img = event.target;
                if (!img.classList.contains('fallback-applied')) {
                    img.src = '/kisisel_qr/assets/images/default-profile.svg';
                    img.classList.add('fallback-applied');
                    img.alt = 'Varsayılan profil resmi';
                }
            }
        }, true);
    }

    /**
     * Form validation için resim kontrolü
     */
    setupFormValidation() {
        const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.validateImageFile(e));
        });
    }

    /**
     * Resim dosyası doğrulaması
     */
    validateImageFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (file.size > maxSize) {
            this.showMessage('Dosya boyutu 5MB\'dan büyük olamaz!', 'error');
            event.target.value = '';
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            this.showMessage('Sadece JPEG, PNG, GIF ve WebP dosyaları kabul edilir!', 'error');
            event.target.value = '';
            return false;
        }

        return true;
    }

    /**
     * Eski profil resimlerini temizle
     */
    async cleanupOldImages(profileId, newPhotoData) {
        try {
            const formData = new FormData();
            formData.append('action', 'cleanup_old_images');
            formData.append('profile_id', profileId);
            formData.append('new_photo_data', JSON.stringify(newPhotoData));
            
            if (this.csrfToken) {
                formData.append('csrf_token', this.csrfToken);
            }

            const response = await fetch(this.cleanupEndpoint, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('Eski resimler temizlendi:', result.cleaned_files);
            } else {
                console.warn('Resim temizleme uyarısı:', result.message);
            }
        } catch (error) {
            console.error('Resim temizleme hatası:', error);
        }
    }

    /**
     * WebP desteği kontrolü
     */
    supportsWebP() {
        return new Promise((resolve) => {
            const webP = new Image();
            webP.onload = webP.onerror = () => {
                resolve(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    /**
     * Responsive resim HTML'i oluştur
     */
    generateResponsiveImageHtml(filename, alt = '', cssClasses = '', sizes = 'auto') {
        if (!filename) {
            return `<img src="/kisisel_qr/assets/images/default-profile.svg" alt="${alt}" class="${cssClasses}" loading="lazy">`;
        }

        const baseName = filename.replace(/\.[^/.]+$/, '');
        const extension = filename.split('.').pop();
        
        // Sizes attribute
        if (sizes === 'auto') {
            sizes = "(max-width: 150px) 150px, (max-width: 300px) 300px, 600px";
        }

        const webpSrcset = [
            `/kisisel_qr/public/uploads/profiles/thumb/${baseName}.webp 150w`,
            `/kisisel_qr/public/uploads/profiles/medium/${baseName}.webp 300w`,
            `/kisisel_qr/public/uploads/profiles/large/${baseName}.webp 600w`
        ].join(', ');

        const jpegSrcset = [
            `/kisisel_qr/public/uploads/profiles/thumb/${filename} 150w`,
            `/kisisel_qr/public/uploads/profiles/medium/${filename} 300w`,
            `/kisisel_qr/public/uploads/profiles/large/${filename} 600w`
        ].join(', ');

        return `
            <picture>
                <source srcset="${webpSrcset}" sizes="${sizes}" type="image/webp">
                <source srcset="${jpegSrcset}" sizes="${sizes}" type="image/jpeg">
                <img src="/kisisel_qr/public/uploads/profiles/medium/${filename}" 
                     alt="${alt}" 
                     class="${cssClasses}" 
                     loading="lazy">
            </picture>
        `;
    }

    /**
     * Kullanıcı mesajları
     */
    showMessage(message, type = 'info') {
        // Bootstrap toast kullan
        const toastEl = document.getElementById('mainToast');
        if (toastEl) {
            const toastBody = document.getElementById('mainToastBody');
            const toast = new bootstrap.Toast(toastEl);
            
            toastBody.textContent = message;
            toastEl.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : type} border-0`;
            toast.show();
        } else {
            // Fallback alert
            alert(message);
        }
    }

    /**
     * Image lazy loading için manual trigger
     */
    refreshLazyImages() {
        if (this.lazyImageObserver) {
            this.observeLazyImages();
        }
    }

    /**
     * Temizlik - observer'ı disconnect et
     */
    destroy() {
        if (this.lazyImageObserver) {
            this.lazyImageObserver.disconnect();
        }
    }
}

// Global instance
window.imageCleanupUtility = new ImageCleanupUtility();

// jQuery plugin olarak da kullanılabilir hale getir
if (typeof jQuery !== 'undefined') {
    jQuery.fn.setupImageOptimization = function() {
        this.each(function() {
            const img = $(this);
            if (img.is('img[data-src]')) {
                window.imageCleanupUtility.lazyImageObserver?.observe(this);
            }
        });
        return this;
    };
}
