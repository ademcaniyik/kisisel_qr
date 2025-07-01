/**
 * Kişisel QR Landing Page - Main JavaScript File
 * Optimized and modularized from index.php
 */

// Landing Page Main Module
const LandingPage = {
    // Initialize all components
    init() {
        this.initNavbar();
        this.initSmoothScrolling();
        this.initAnimations();
        this.initOrderSystem();
        this.initWhatsAppHandlers();
        this.initSocialMediaSystem();
        this.initPhoneNumberSystem();
        this.initLocationSystem();
    },

    // Navbar functionality
    initNavbar() {
        const navbar = document.querySelector('.navbar, .glassmorphism-header');
        
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                    navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
                } else {
                    navbar.classList.remove('scrolled');
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    navbar.style.boxShadow = 'none';
                }
            });
        }

        // Mobile menu auto-close
        const navLinks = document.querySelectorAll('.nav-link');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse?.classList.contains('show')) {
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    navbarToggler?.click();
                }
            });
        });
    },

    // Smooth scrolling
    initSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    },

    // Animation system
    initAnimations() {
        // Counter animation
        this.animateCounters = () => {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                const increment = target / 100;
                let current = 0;

                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        if (counter.textContent.includes('%')) {
                            counter.textContent = Math.ceil(current) + '%';
                        } else if (counter.textContent.includes('+')) {
                            counter.textContent = Math.ceil(current) + '+';
                        } else {
                            counter.textContent = Math.ceil(current);
                        }
                        setTimeout(updateCounter, 50);
                    } else {
                        counter.textContent = counter.textContent; // Reset to original
                    }
                };
                updateCounter();
            });
        };

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-on-scroll');

                    // Trigger counter animation when stats section is visible
                    if (entry.target.classList.contains('stats')) {
                        this.animateCounters();
                    }
                }
            });
        }, observerOptions);

        // Observe sections for animation
        document.querySelectorAll('section, .feature-card, .testimonial-card, .pricing-card').forEach(el => {
            observer.observe(el);
        });
    }
};

// Order System Module
const OrderSystem = {
    currentStep: 1,
    
    showOrderForm() {
        const modal = new bootstrap.Modal(document.getElementById('orderModal'));
        modal.show();
    },

    nextStep() {
        // Validate step 1
        const form = document.getElementById('orderForm');
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (isValid) {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            this.currentStep = 2;
            this.initPaymentMethods();
        }
    },

    prevStep() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
        this.currentStep = 1;
    },

    initPaymentMethods() {
        const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
        const completeOrderBtn = document.getElementById('completeOrderBtn');

        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                // Hide all payment info sections
                document.getElementById('bankTransferInfo').style.display = 'none';
                document.getElementById('cashOnDeliveryInfo').style.display = 'none';

                // Show selected payment info
                if (this.value === 'bank_transfer') {
                    document.getElementById('bankTransferInfo').style.display = 'block';
                    completeOrderBtn.textContent = 'Ödeme Yaptım, Sipariş Ver';
                    completeOrderBtn.className = 'btn btn-success';
                } else if (this.value === 'cash_on_delivery') {
                    document.getElementById('cashOnDeliveryInfo').style.display = 'block';
                    completeOrderBtn.textContent = 'Sipariş Ver';
                    completeOrderBtn.className = 'btn btn-warning';
                }

                // Update label borders
                paymentMethods.forEach(p => {
                    const card = p.parentElement.querySelector('.card');
                    if (p.checked) {
                        card.classList.add('border-primary');
                        card.classList.remove('border-secondary');
                    } else {
                        card.classList.remove('border-primary');
                        card.classList.add('border-secondary');
                    }
                });
            });
        });

        // Trigger initial change
        const checked = document.querySelector('input[name="paymentMethod"]:checked');
        if (checked) {
            checked.dispatchEvent(new Event('change'));
        }
    },

    async completeOrder() {
        const completeOrderBtn = document.getElementById('completeOrderBtn');
        
        // Prevent double submission
        if (completeOrderBtn.disabled) {
            return;
        }
        
        completeOrderBtn.disabled = true;
        const originalContent = completeOrderBtn.innerHTML;
        
        completeOrderBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            İşleniyor...
        `;
        
        try {
            const formData = new FormData();
            
            // Collect form data
            const customerName = document.getElementById('customerName').value;
            const customerPhone = PhoneSystem.getFormattedPhoneNumber('customer');
            const customerBio = document.getElementById('customerBio').value;
            const customerTheme = document.getElementById('customerTheme').value;
            const customerIban = document.getElementById('customerIban').value;
            const customerBloodType = document.getElementById('customerBloodType').value;
            const themeText = document.getElementById('customerTheme').options[document.getElementById('customerTheme').selectedIndex].text;
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

            // Collect delivery data
            const deliveryData = LocationSystem.getDeliveryData();
            const citySelect = document.getElementById('deliveryCity');
            const districtSelect = document.getElementById('deliveryDistrict');
            const cityText = citySelect.options[citySelect.selectedIndex].text;
            const districtText = districtSelect.options[districtSelect.selectedIndex].text;
            const fullAddress = `${deliveryData.address}, ${districtText}/${cityText}`;

            // Collect social media links
            const socialMedia = [];
            const socialMediaData = SocialMediaSystem.getSocialMediaData();
            socialMediaData.forEach(item => {
                const platform = SocialMediaSystem.platforms[item.platform];
                if (platform) {
                    socialMedia.push(`${platform.name}: ${item.url}`);
                }
            });

            const shippingAddress = `${fullAddress}\nAlıcı: ${deliveryData.name}\nTelefon: ${deliveryData.phone}`;

            let specialRequests = '';
            if (customerBio) specialRequests += `Bio: ${customerBio}\n`;
            if (customerIban) specialRequests += `İban: ${customerIban}\n`;
            if (customerBloodType) specialRequests += `Kan Grubu: ${customerBloodType}\n`;
            if (socialMedia.length > 0) {
                specialRequests += `Sosyal Medya:\n${socialMedia.join('\n')}\n`;
            }
            specialRequests += `Ödeme Yöntemi: ${paymentMethod === 'bank_transfer' ? 'Banka Havalesi' : 'Kapıda Ödeme'}\n`;
            specialRequests += `Tema: ${themeText}`;

            // Add form data
            formData.append('customer_name', customerName);
            formData.append('customer_phone', customerPhone);
            formData.append('customer_email', '');
            formData.append('product_type', 'personal_qr');
            formData.append('product_name', '10x10 cm Şeffaf QR Sticker');
            formData.append('quantity', '1');
            formData.append('price', '200.00');
            formData.append('payment_method', paymentMethod);
            formData.append('special_requests', specialRequests);
            formData.append('shipping_address', shippingAddress);
            formData.append('whatsapp_sent', 'true');
            
            // Add photo file if selected
            const photoFile = document.getElementById('customerPhoto').files[0];
            if (photoFile) {
                formData.append('photo', photoFile);
            }

            // Submit order
            const response = await fetch('admin/api/orders.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                console.log('Sipariş başarıyla kaydedildi:', result.order_id);

                // Set profile link if available
                if (result.profile && result.profile.profile_url) {
                    const profileLink = document.getElementById('profileLink');
                    const fullUrl = result.profile.profile_url.startsWith('http') ?
                        result.profile.profile_url :
                        window.location.origin + window.location.pathname.replace('/index.php', '').replace(/\/$/, '') + '/' + result.profile.profile_url;

                    profileLink.href = fullUrl;
                    profileLink.style.display = 'inline-block';

                    profileLink.onclick = function(e) {
                        e.preventDefault();
                        window.location.href = fullUrl;
                        return false;
                    };
                }

                // Create WhatsApp message
                const whatsappMessage = WhatsAppSystem.createOrderMessage({
                    orderId: result.order_id,
                    customerName,
                    customerPhone,
                    fullAddress,
                    deliveryData,
                    customerBio,
                    themeText,
                    socialMedia,
                    profileSlug: result.profile?.profile_slug,
                    paymentMethod
                });

                // Update WhatsApp link
                document.getElementById('whatsappLink').href = whatsappMessage;

                // Show success step
                document.getElementById('step2').style.display = 'none';
                document.getElementById('step3').style.display = 'block';
                this.currentStep = 3;

            } else {
                console.error('Sipariş kaydedilirken hata:', result.message);
                Utils.showToast('Sipariş kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            }

        } catch (error) {
            console.error('Sipariş işlemi sırasında hata:', error);
            Utils.showToast('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        } finally {
            // Reset button
            completeOrderBtn.disabled = false;
            
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
            if (paymentMethod === 'cash_on_delivery') {
                completeOrderBtn.innerHTML = 'Sipariş Ver';
                completeOrderBtn.className = 'btn btn-warning';
            } else {
                completeOrderBtn.innerHTML = 'Ödeme Yaptım, Sipariş Ver';
                completeOrderBtn.className = 'btn btn-success';
            }
        }
    }
};

// WhatsApp System Module
const WhatsAppSystem = {
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // WhatsApp buttons event listeners
            document.querySelectorAll('[href*="wa.me"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    console.log('WhatsApp button clicked:', this.href);
                });
            });

            // Special handling for order WhatsApp link
            const whatsappLink = document.getElementById('whatsappLink');
            if (whatsappLink) {
                whatsappLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    WhatsAppSystem.generateMessage();
                });
            }
        });
    },

    generateMessage() {
        const customerName = document.getElementById('customerName')?.value || '';
        const customerPhone = PhoneSystem.getFormattedPhoneNumber('customer');
        const customerBio = document.getElementById('customerBio')?.value || '';

        if (!customerName && !customerPhone) {
            const generalMessage = `Merhaba! 🏷️ QR Sticker siparişi vermek istiyorum.\n\n📦 Ürün: 10x10 cm Şeffaf QR Sticker\n💰 Fiyat: 200 TL\n\nDetayları sizinle paylaşabilirim. Lütfen bana ulaşın.`;
            const whatsappURL = `https://wa.me/905349334631?text=${encodeURIComponent(generalMessage)}`;
            window.open(whatsappURL, '_blank');
            return;
        }

        // Generate detailed message with form data
        const deliveryData = LocationSystem.getDeliveryData();
        const citySelect = document.getElementById('deliveryCity');
        const districtSelect = document.getElementById('deliveryDistrict');
        
        let fullAddress = '';
        if (citySelect && districtSelect && deliveryData.address) {
            const cityText = citySelect.options[citySelect.selectedIndex]?.text || '';
            const districtText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
            fullAddress = `${deliveryData.address}, ${districtText}/${cityText}`;
        }

        const themeSelect = document.getElementById('customerTheme');
        const themeText = themeSelect ? themeSelect.options[themeSelect.selectedIndex].text : 'Varsayılan';

        const socialMedia = [];
        const socialMediaData = SocialMediaSystem.getSocialMediaData();
        socialMediaData.forEach(item => {
            const platform = SocialMediaSystem.platforms[item.platform];
            if (platform) {
                socialMedia.push(`${platform.name}: ${item.url}`);
            }
        });

        let message = `🏷️ *QR Sticker Siparişi*\n\n`;
        message += `👤 *Ad Soyad:* ${customerName}\n`;
        message += `📱 *Telefon:* ${customerPhone}\n`;
        if (fullAddress) {
            message += `\n📍 *Teslimat Bilgileri:*\n`;
            message += `📍 *Adres:* ${fullAddress}\n`;
            if (deliveryData.name) message += `👨‍💼 *Alıcı:* ${deliveryData.name}\n`;
            if (deliveryData.phone) message += `📞 *Alıcı Tel:* ${deliveryData.phone}\n`;
        }
        if (customerBio) message += `📝 *Bio:* ${customerBio}\n`;
        message += `🎨 *Tema:* ${themeText}\n`;
        if (socialMedia.length > 0) {
            message += `\n🌐 *Sosyal Medya:*\n${socialMedia.join('\n')}\n`;
        }
        message += `\n💰 *Tutar:* 200 TL\n`;
        message += `📦 *Ürün:* 10x10 cm Şeffaf QR Sticker\n`;
        message += `✅ *Ödeme Durumu:* Ödeme yapıldı\n\n`;
        message += `Siparişimi onaylayın lütfen 🙏`;

        const whatsappURL = `https://wa.me/905349334631?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');
    },

    createOrderMessage(data) {
        let message = `🏷️ *QR Sticker Siparişi* (#${data.orderId})\n\n`;
        message += `👤 *Ad Soyad:* ${data.customerName}\n`;
        message += `📱 *Telefon:* ${data.customerPhone}\n`;
        message += `\n📍 *Teslimat Bilgileri:*\n`;
        message += `📍 *Adres:* ${data.fullAddress}\n`;
        message += `👨‍💼 *Alıcı:* ${data.deliveryData.name}\n`;
        message += `📞 *Alıcı Tel:* ${data.deliveryData.phone}\n`;
        if (data.customerBio) message += `📝 *Bio:* ${data.customerBio}\n`;
        message += `🎨 *Tema:* ${data.themeText}\n`;
        if (data.socialMedia.length > 0) {
            message += `\n🌐 *Sosyal Medya:*\n${data.socialMedia.join('\n')}\n`;
        }

        if (data.profileSlug) {
            message += `\n🔗 *Profil:* https://acdisoftware.com.tr/kisisel_qr/profile.php?slug=${data.profileSlug}\n`;
        }

        message += `\n💰 *Tutar:* 200 TL\n`;
        message += `📦 *Ürün:* 10x10 cm Şeffaf QR Sticker\n`;
        message += `💳 *Ödeme Yöntemi:* ${data.paymentMethod === 'cash_on_delivery' ? 'Kapıda Ödeme' : 'Banka Havalesi'}\n`;
        if (data.paymentMethod === 'bank_transfer') {
            message += `✅ *Ödeme Durumu:* Ödeme yapıldı\n\n`;
        } else {
            message += `🚚 *Ödeme:* Teslim alırken ödenecek\n\n`;
        }
        message += `Siparişimi onaylayın lütfen 🙏`;

        return `https://wa.me/905349334631?text=${encodeURIComponent(message)}`;
    }
};

// Social Media System Module
const SocialMediaSystem = {
    platforms: {
        instagram: {
            name: 'Instagram',
            icon: 'fab fa-instagram',
            prefix: '@',
            baseUrl: 'https://instagram.com/',
            placeholder: 'kullanici_adi',
            color: 'platform-instagram'
        },
        x: {
            name: 'X',
            icon: 'fab fa-twitter',
            prefix: '@',
            baseUrl: 'https://x.com/',
            placeholder: 'kullanici_adi',
            color: 'platform-x'
        },
        linkedin: {
            name: 'LinkedIn',
            icon: 'fab fa-linkedin',
            prefix: '',
            baseUrl: 'https://linkedin.com/in/',
            placeholder: 'profil-adi',
            color: 'platform-linkedin'
        },
        facebook: {
            name: 'Facebook',
            icon: 'fab fa-facebook',
            prefix: '',
            baseUrl: 'https://facebook.com/',
            placeholder: 'profil.adi',
            color: 'platform-facebook'
        },
        youtube: {
            name: 'YouTube',
            icon: 'fab fa-youtube',
            prefix: '',
            baseUrl: 'https://youtube.com/@',
            placeholder: 'kanal_adi',
            color: 'platform-youtube'
        },
        tiktok: {
            name: 'TikTok',
            icon: 'fab fa-tiktok',
            prefix: '@',
            baseUrl: 'https://tiktok.com/@',
            placeholder: 'kullanici_adi',
            color: 'platform-tiktok'
        },
        whatsapp: {
            name: 'WhatsApp',
            icon: 'fab fa-whatsapp',
            prefix: '+90',
            baseUrl: 'https://wa.me/',
            placeholder: '5xxxxxxxxx',
            color: 'platform-whatsapp'
        },
        website: {
            name: 'Website',
            icon: 'fas fa-globe',
            prefix: 'https://',
            baseUrl: '',
            placeholder: 'ornek.com',
            color: 'platform-website'
        },
        snapchat: {
            name: 'Snapchat',
            icon: 'fab fa-snapchat',
            prefix: '',
            baseUrl: 'https://snapchat.com/add/',
            placeholder: 'kullanici_adi',
            color: 'platform-snapchat'
        },
        discord: {
            name: 'Discord',
            icon: 'fab fa-discord',
            prefix: '#',
            baseUrl: '',
            placeholder: 'kullanici#1234',
            color: 'platform-discord'
        },
        telegram: {
            name: 'Telegram',
            icon: 'fab fa-telegram',
            prefix: '@',
            baseUrl: 'https://t.me/',
            placeholder: 'kullanici_adi',
            color: 'platform-telegram'
        },
        twitch: {
            name: 'Twitch',
            icon: 'fab fa-twitch',
            prefix: '',
            baseUrl: 'https://twitch.tv/',
            placeholder: 'kanal_adi',
            color: 'platform-twitch'
        }
    },

    selectedPlatforms: [],

    init() {
        document.querySelectorAll('.social-platform-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const platform = btn.dataset.platform;
                this.addPlatform(platform);
            });
        });
    },

    addPlatform(platformKey) {
        if (platformKey === 'whatsapp') {
            PhoneSystem.resetForWhatsApp();
        }
        
        if (this.selectedPlatforms.find(item => item.platform === platformKey)) {
            Utils.showToast('Bu platform zaten eklenmiş!', 'warning');
            return;
        }

        const platform = this.platforms[platformKey];
        if (!platform) {
            console.error('Platform bulunamadı:', platformKey);
            return;
        }

        const socialMediaItem = {
            platform: platformKey,
            username: '',
            url: ''
        };

        if (platformKey === 'whatsapp') {
            const phoneInput = document.getElementById('customerPhone');
            if (phoneInput && phoneInput.value) {
                const phoneNumber = phoneInput.value.replace(/\D/g, '');
                socialMediaItem.username = phoneNumber;
                socialMediaItem.url = this.generateUrl(platformKey, phoneNumber);
            }
        }

        this.selectedPlatforms.push(socialMediaItem);
        this.renderItem(socialMediaItem, this.selectedPlatforms.length - 1);
        this.updatePlatformButton(platformKey, true);
        
        setTimeout(() => {
            const newItem = document.querySelector('.social-media-item:last-child');
            if (newItem) {
                newItem.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                const input = newItem.querySelector('.social-username-input');
                if (input) input.focus();
            }
        }, 100);
    },

    renderItem(item, index) {
        const platform = this.platforms[item.platform];
        const container = document.getElementById('selectedSocialMedias');
        
        let placeholder = platform.placeholder;
        let inputType = 'text';
        if (item.platform === 'whatsapp') {
            placeholder = 'Telefon numarası (örn: 905551234567)';
            inputType = 'tel';
        }
        
        const itemHtml = `
            <div class="social-media-item" data-index="${index}">
                <div class="platform-header">
                    <div class="platform-icon ${platform.color}">
                        <i class="${platform.icon}"></i>
                    </div>
                    <span class="platform-name">${platform.name}</span>
                    <button type="button" class="remove-btn" onclick="SocialMediaSystem.removePlatform(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="input-group">
                    ${platform.prefix && item.platform !== 'whatsapp' ? `<span class="username-prefix">${platform.prefix}</span>` : ''}
                    <input type="${inputType}" 
                           class="form-control social-username-input" 
                           data-platform="${item.platform}"
                           data-index="${index}"
                           placeholder="${placeholder}"
                           oninput="SocialMediaSystem.updateUrl(${index})"
                           value="${item.username}">
                </div>
                <div class="url-preview" id="urlPreview${index}">
                    ${this.generateUrl(item.platform, item.username)}
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', itemHtml);
    },

    updateUrl(index) {
        const item = this.selectedPlatforms[index];
        const input = document.querySelector(`input[data-index="${index}"]`);
        
        item.username = input.value;
        item.url = this.generateUrl(item.platform, item.username);
        
        const preview = document.getElementById(`urlPreview${index}`);
        preview.textContent = item.url || 'URL oluşturulacak...';
    },

    generateUrl(platformKey, username) {
        if (!username) return '';
        
        const platform = this.platforms[platformKey];
        
        switch(platformKey) {
            case 'whatsapp':
                const cleanNumber = username.replace(/\D/g, '');
                return cleanNumber ? platform.baseUrl + cleanNumber : '';
            case 'website':
                return username.startsWith('http') ? username : 'https://' + username;
            case 'discord':
                return username;
            default:
                return platform.baseUrl + username;
        }
    },

    removePlatform(index) {
        const item = this.selectedPlatforms[index];
        this.selectedPlatforms.splice(index, 1);
        
        const container = document.getElementById('selectedSocialMedias');
        container.innerHTML = '';
        
        this.selectedPlatforms.forEach((socialItem, newIndex) => {
            this.renderItem(socialItem, newIndex);
        });
        
        this.updatePlatformButton(item.platform, false);
    },

    updatePlatformButton(platformKey, selected) {
        const btn = document.querySelector(`[data-platform="${platformKey}"]`);
        if (btn) {
            if (selected) {
                btn.classList.add('selected');
                btn.disabled = true;
            } else {
                btn.classList.remove('selected');
                btn.disabled = false;
            }
        }
    },

    getSocialMediaData() {
        return this.selectedPlatforms.filter(item => item.username.trim() !== '');
    }
};

// Phone Number System Module
const PhoneSystem = {
    formatPhoneNumber(type) {
        const countrySelect = document.getElementById(type + 'CountryCode');
        const phoneInput = document.getElementById(type + 'Phone');
        
        if (!countrySelect || !phoneInput) return;
        
        const countryCode = countrySelect.value;
        let phoneValue = phoneInput.value.replace(/\D/g, '');
        
        switch (countryCode) {
            case '+90': // Türkiye
                if (phoneValue.length > 10) phoneValue = phoneValue.substring(0, 10);
                if (phoneValue.length > 0) {
                    phoneValue = phoneValue.substring(0, 3) + 
                               (phoneValue.length > 3 ? ' ' + phoneValue.substring(3, 6) : '') +
                               (phoneValue.length > 6 ? ' ' + phoneValue.substring(6, 8) : '') +
                               (phoneValue.length > 8 ? ' ' + phoneValue.substring(8, 10) : '');
                }
                break;
            case '+1': // ABD/Kanada
                if (phoneValue.length > 10) phoneValue = phoneValue.substring(0, 10);
                if (phoneValue.length > 0) {
                    phoneValue = '(' + phoneValue.substring(0, 3) + ')' +
                               (phoneValue.length > 3 ? ' ' + phoneValue.substring(3, 6) : '') +
                               (phoneValue.length > 6 ? '-' + phoneValue.substring(6, 10) : '');
                }
                break;
            case '+44': // İngiltere
                if (phoneValue.length > 10) phoneValue = phoneValue.substring(0, 10);
                if (phoneValue.length > 0) {
                    phoneValue = phoneValue.substring(0, 4) + 
                               (phoneValue.length > 4 ? ' ' + phoneValue.substring(4, 7) : '') +
                               (phoneValue.length > 7 ? ' ' + phoneValue.substring(7, 10) : '');
                }
                break;
            default:
                if (phoneValue.length > 12) phoneValue = phoneValue.substring(0, 12);
                break;
        }
        
        phoneInput.value = phoneValue;
    },

    getFormattedPhoneNumber(type) {
        const countrySelect = document.getElementById(type + 'CountryCode');
        const phoneInput = document.getElementById(type + 'Phone');
        
        if (!countrySelect || !phoneInput) return '';
        
        const countryCode = countrySelect.value;
        const phoneNumber = phoneInput.value.replace(/\D/g, '');
        
        return phoneNumber ? countryCode + phoneNumber : '';
    },

    updatePlaceholder(type) {
        const countrySelect = document.getElementById(type + 'CountryCode');
        const phoneInput = document.getElementById(type + 'Phone');
        
        if (!countrySelect || !phoneInput) return;
        
        const countryCode = countrySelect.value;
        
        const placeholders = {
            '+90': '555 555 55 55',
            '+1': '(555) 123-4567',
            '+44': '7700 900123',
            '+49': '30 12345678',
            '+33': '1 42 34 56 78',
            '+971': '50 123 4567',
            '+966': '50 123 4567',
            '+7': '495 123 4567',
            '+86': '138 0013 8000',
            '+91': '98765 43210'
        };
        
        phoneInput.placeholder = placeholders[countryCode] || 'Telefon numarası';
        
        if (phoneInput.value) {
            this.formatPhoneNumber(type);
        }
    },

    resetForWhatsApp() {
        const customerCountryCode = document.getElementById('customerCountryCode');
        const customerPhone = document.getElementById('customerPhone');
        
        if (customerCountryCode && customerPhone) {
            customerCountryCode.value = '+90';
            this.updatePlaceholder('customer');
            customerPhone.focus();
        }
        
        const deliveryCountryCode = document.getElementById('deliveryCountryCode');
        const deliveryPhone = document.getElementById('deliveryPhone');
        
        if (deliveryCountryCode && deliveryPhone) {
            deliveryCountryCode.value = '+90';
            this.updatePlaceholder('delivery');
        }
        
        Utils.showToast('WhatsApp için telefon alanları Türkiye (+90) olarak ayarlandı!', 'success');
    },

    init() {
        const customerCountryCode = document.getElementById('customerCountryCode');
        if (customerCountryCode) {
            customerCountryCode.addEventListener('change', () => {
                this.updatePlaceholder('customer');
            });
            this.updatePlaceholder('customer');
        }
        
        setTimeout(() => {
            const deliveryCountryCode = document.getElementById('deliveryCountryCode');
            if (deliveryCountryCode) {
                deliveryCountryCode.addEventListener('change', () => {
                    this.updatePlaceholder('delivery');
                });
                this.updatePlaceholder('delivery');
            }
        }, 1000);
    }
};

// Location System Module
const LocationSystem = {
    districtData: {
        istanbul: ['Adalar', 'Arnavutköy', 'Ataşehir', 'Avcılar', 'Bağcılar', 'Bahçelievler', 'Bakırköy', 'Başakşehir', 'Bayrampaşa', 'Beşiktaş', 'Beykoz', 'Beylikdüzü', 'Beyoğlu', 'Büyükçekmece', 'Çatalca', 'Çekmeköy', 'Esenler', 'Esenyurt', 'Eyüpsultan', 'Fatih', 'Gaziosmanpaşa', 'Güngören', 'Kadıköy', 'Kağıthane', 'Kartal', 'Küçükçekmece', 'Maltepe', 'Pendik', 'Sancaktepe', 'Sarıyer', 'Silivri', 'Sultanbeyli', 'Sultangazi', 'Şile', 'Şişli', 'Tuzla', 'Ümraniye', 'Üsküdar', 'Zeytinburnu'],
        ankara: ['Akyurt', 'Altındağ', 'Ayaş', 'Bala', 'Beypazarı', 'Çamlıdere', 'Çankaya', 'Çubuk', 'Elmadağ', 'Etimesgut', 'Evren', 'Gölbaşı', 'Güdül', 'Haymana', 'Kalecik', 'Kazan', 'Keçiören', 'Kızılcahamam', 'Mamak', 'Nallıhan', 'Polatlı', 'Pursaklar', 'Sincan', 'Şereflikoçhisar', 'Yenimahalle'],
        izmir: ['Aliağa', 'Balçova', 'Bayındır', 'Bayraklı', 'Bergama', 'Beydağ', 'Bornova', 'Buca', 'Çeşme', 'Çiğli', 'Dikili', 'Foça', 'Gaziemir', 'Güzelbahçe', 'Karabağlar', 'Karaburun', 'Karşıyaka', 'Kemalpaşa', 'Kınık', 'Kiraz', 'Konak', 'Menderes', 'Menemen', 'Narlıdere', 'Ödemiş', 'Seferihisar', 'Selçuk', 'Tire', 'Torbalı', 'Urla'],
        bursa: ['Büyükorhan', 'Gemlik', 'Gürsu', 'Harmancık', 'İnegöl', 'İznik', 'Karacabey', 'Keles', 'Kestel', 'Mudanya', 'Mustafakemalpaşa', 'Nilüfer', 'Orhaneli', 'Orhangazi', 'Osmangazi', 'Yenişehir', 'Yıldırım'],
        antalya: ['Akseki', 'Aksu', 'Alanya', 'Demre', 'Döşemealtı', 'Elmalı', 'Finike', 'Gazipaşa', 'Gündoğmuş', 'İbradı', 'Kaş', 'Kemer', 'Kepez', 'Konyaaltı', 'Korkuteli', 'Kumluca', 'Manavgat', 'Muratpaşa', 'Serik'],
        adana: ['Aladağ', 'Ceyhan', 'Çukurova', 'Feke', 'İmamoğlu', 'Karaisalı', 'Karataş', 'Kozan', 'Pozantı', 'Saimbeyli', 'Sarıçam', 'Seyhan', 'Tufanbeyli', 'Yumurtalık', 'Yüreğir'],
        konya: ['Ahırlı', 'Akören', 'Akşehir', 'Altınekin', 'Beyşehir', 'Bozkır', 'Cihanbeyli', 'Çeltik', 'Çumra', 'Derbent', 'Derebucak', 'Doğanhisar', 'Emirgazi', 'Ereğli', 'Güneysinir', 'Hadim', 'Halkapınar', 'Hüyük', 'Ilgın', 'Kadınhanı', 'Karapınar', 'Karatay', 'Kulu', 'Meram', 'Selçuklu', 'Seydişehir', 'Taşkent', 'Tuzlukçu', 'Yalıhüyük', 'Yunak']
    },

    updateDistricts() {
        const citySelect = document.getElementById('deliveryCity');
        const districtSelect = document.getElementById('deliveryDistrict');
        
        const selectedCity = citySelect.value;
        
        districtSelect.innerHTML = '<option value="">İlçe seçiniz</option>';
        
        if (selectedCity && this.districtData[selectedCity]) {
            this.districtData[selectedCity].forEach(district => {
                const option = document.createElement('option');
                option.value = district.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c');
                option.textContent = district;
                districtSelect.appendChild(option);
            });
            
            districtSelect.disabled = false;
        } else {
            districtSelect.disabled = true;
        }
    },

    getDeliveryData() {
        return {
            city: document.getElementById('deliveryCity').value,
            district: document.getElementById('deliveryDistrict').value,
            address: document.getElementById('deliveryAddress').value,
            name: document.getElementById('deliveryName').value,
            phone: PhoneSystem.getFormattedPhoneNumber('delivery')
        };
    }
};

// Theme System Module
const ThemeSystem = {
    updatePreview() {
        const selectedTheme = document.getElementById('customerTheme').value;
        const previewElement = document.getElementById('themePreview');

        previewElement.className = 'theme-preview';
        previewElement.classList.add('theme-' + selectedTheme);

        previewElement.style.transform = 'scale(0.95)';
        setTimeout(() => {
            previewElement.style.transform = 'scale(1)';
        }, 150);
    }
};

// Utility Functions Module
const Utils = {
    copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent || element.innerText;

        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);

        textarea.select();
        textarea.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');

            const button = element.nextElementSibling;
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-success"></i>';

            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 2000);

            this.showToast('Kopyalandı!', 'success');
        } catch (err) {
            console.error('Kopyalama hatası:', err);
            this.showToast('Kopyalama başarısız!', 'error');
        }

        document.body.removeChild(textarea);
    },

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        let icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        else if (type === 'error') icon = 'fa-exclamation-circle';
        else if (type === 'warning') icon = 'fa-exclamation-triangle';
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    },

    handleFormSubmit(event) {
        event.preventDefault();
        alert('Mesajınız alındı! En kısa sürede dönüş yapacağız.');
    }
};

// Global Functions (for backward compatibility)
window.showOrderForm = () => OrderSystem.showOrderForm();
window.nextStep = () => OrderSystem.nextStep();
window.prevStep = () => OrderSystem.prevStep();
window.completeOrder = () => OrderSystem.completeOrder();
window.generateWhatsAppMessage = () => WhatsAppSystem.generateMessage();
window.copyToClipboard = (elementId) => Utils.copyToClipboard(elementId);
window.updateThemePreview = () => ThemeSystem.updatePreview();
window.updateDistricts = () => LocationSystem.updateDistricts();
window.handleFormSubmit = (event) => Utils.handleFormSubmit(event);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    LandingPage.init();
    OrderSystem.currentStep = 1;
    WhatsAppSystem.init();
    SocialMediaSystem.init();
    PhoneSystem.init();
    
    // Initialize theme preview when modal opens
    const orderModal = document.getElementById('orderModal');
    if (orderModal) {
        orderModal.addEventListener('shown.bs.modal', function() {
            ThemeSystem.updatePreview();
        });
    }
});
