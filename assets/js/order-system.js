/**
 * Order System JavaScript
 * Handles all order form functionality, payment methods, social media management, etc.
 */

// Order form functions
function showOrderForm() {
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}

function nextStep() {
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
        // Initialize payment method handling
        initPaymentMethods();
    }
}

function initPaymentMethods() {
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
    document.querySelector('input[name="paymentMethod"]:checked').dispatchEvent(new Event('change'));
}

function prevStep() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}

async function completeOrder() {
    // Butonu kontrol et ve devre dışı bırak
    const completeOrderBtn = document.getElementById('completeOrderBtn');
    if (completeOrderBtn.disabled) {
        return; // Zaten tıklanmış, işlemi durdur
    }
    
    // Butonu devre dışı bırak
    completeOrderBtn.disabled = true;
    const originalText = completeOrderBtn.innerHTML;
    completeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Siparişiniz Oluşturuluyor...';
    
    try {
        // Prepare form data for file upload
        const formData = new FormData();
        
        // Collect form data
        const customerName = document.getElementById('customerName').value;
        const customerPhone = getFormattedPhoneNumber('customer');
        const customerBio = document.getElementById('customerBio').value;
        const customerTheme = document.getElementById('customerTheme').value;
        const customerIban = document.getElementById('customerIban').value;
        const customerBloodType = document.getElementById('customerBloodType').value;
        const themeText = document.getElementById('customerTheme').options[document.getElementById('customerTheme').selectedIndex].text;
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

        // Collect delivery data
        const deliveryData = getDeliveryData();
        const citySelect = document.getElementById('deliveryCity');
        const districtSelect = document.getElementById('deliveryDistrict');
        const cityText = citySelect.options[citySelect.selectedIndex].text;
        const districtText = districtSelect.options[districtSelect.selectedIndex].text;
        const fullAddress = `${deliveryData.address}, ${districtText}/${cityText}`;

        // Collect social media links from new dynamic system
        const socialMedia = [];
        const socialMediaData = getSocialMediaData();
        socialMediaData.forEach(item => {
            const platform = socialMediaPlatforms[item.platform];
            if (platform) {
                socialMedia.push(`${platform.name}: ${item.url}`);
            }
        });

        // Create shipping address separately
        const shippingAddress = `${fullAddress}\nAlıcı: ${deliveryData.name}\nTelefon: ${deliveryData.phone}`;

        // Create special requests text (without shipping address)
        let specialRequests = '';
        if (customerBio) specialRequests += `Bio: ${customerBio}\n`;
        if (customerIban) specialRequests += `İban: ${customerIban}\n`;
        if (customerBloodType) specialRequests += `Kan Grubu: ${customerBloodType}\n`;
        if (socialMedia.length > 0) {
            specialRequests += `Sosyal Medya:\n${socialMedia.join('\n')}\n`;
        }
        specialRequests += `Ödeme Yöntemi: ${paymentMethod === 'bank_transfer' ? 'Banka Havalesi' : 'Kapıda Ödeme'}\n`;
        specialRequests += `Tema: ${themeText}`;

        // Add form data to FormData
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

        // Save order to database with file upload support
        const response = await fetch('admin/api/orders.php', {
            method: 'POST',
            body: formData // FormData kullanarak dosya gönderimi
        });

        const result = await response.json();

        if (result.success) {
            console.log('Sipariş başarıyla kaydedildi:', result.order_id);

            // Profil linki varsa ayarla
            if (result.profile && result.profile.profile_url) {
                const profileLink = document.getElementById('profileLink');
                // Tam URL oluştur
                const fullUrl = result.profile.profile_url.startsWith('http') ?
                    result.profile.profile_url :
                    window.location.origin + window.location.pathname.replace('/index.php', '').replace(/\/$/, '') + '/' + result.profile.profile_url;

                profileLink.href = fullUrl;
                profileLink.style.display = 'inline-block';

                // Click event listener ekle
                profileLink.onclick = function(e) {
                    e.preventDefault();
                    window.location.href = fullUrl;
                    return false;
                };

                console.log('Profil linki ayarlandı:', fullUrl);
            }

            // Create WhatsApp message
            let message = `🏷️ *QR Sticker Siparişi* (#${result.order_id})\n\n`;
            message += `👤 *Ad Soyad:* ${customerName}\n`;
            message += `📱 *Telefon:* ${customerPhone}\n`;
            message += `\n📦 *Teslimat Bilgileri:*\n`;
            message += `📍 *Adres:* ${fullAddress}\n`;
            message += `👨‍💼 *Alıcı:* ${deliveryData.name}\n`;
            message += `📞 *Alıcı Tel:* ${deliveryData.phone}\n`;
            if (customerBio) message += `📝 *Bio:* ${customerBio}\n`;
            message += `🎨 *Tema:* ${themeText}\n`;
            if (socialMedia.length > 0) {
                message += `\n🌐 *Sosyal Medya:*\n${socialMedia.join('\n')}\n`;
            }

            // Profil bilgisi varsa ekle
            if (result.profile && result.profile.profile_slug) {
                message += `\n🔗 *Profil:* https://acdisoftware.com.tr/kisisel_qr/profile.php?slug=${result.profile.profile_slug}\n`;
            }

            message += `\n💰 *Tutar:* 200 TL\n`;
            message += `📦 *Ürün:* 10x10 cm Şeffaf QR Sticker\n`;
            message += `💳 *Ödeme Yöntemi:* ${paymentMethod === 'cash_on_delivery' ? 'Kapıda Ödeme' : 'Banka Havalesi'}\n`;
            if (paymentMethod === 'bank_transfer') {
                message += `✅ *Ödeme Durumu:* Ödeme yapıldı\n\n`;
            } else {
                message += `🚚 *Ödeme:* Teslim alırken ödenecek\n\n`;
            }
            message += `Siparişimi onaylayın lütfen 🙏`;

            // Create WhatsApp link
            const whatsappNumber = '905349334631';
            const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;

            // Update WhatsApp link
            document.getElementById('whatsappLink').href = whatsappURL;

            // Show step 3
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'block';

            console.log('Order completed, WhatsApp link updated:', whatsappURL);
        } else {
            console.error('Sipariş kaydedilirken hata:', result.message);
            alert('Sipariş kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.');
            
            // Hata durumunda butonu tekrar aktif hale getir
            completeOrderBtn.disabled = false;
            completeOrderBtn.innerHTML = originalText;
        }

    } catch (error) {
        console.error('Sipariş işlemi sırasında hata:', error);
        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        
        // Hata durumunda butonu tekrar aktif hale getir
        completeOrderBtn.disabled = false;
        completeOrderBtn.innerHTML = originalText;
    }
}

function generateWhatsAppMessage() {
    // Form data kontrolü
    const customerName = document.getElementById('customerName')?.value || '';
    const customerPhone = getFormattedPhoneNumber('customer');
    const customerBio = document.getElementById('customerBio')?.value || '';

    // Eğer form verileri yoksa genel mesaj oluştur
    if (!customerName && !customerPhone) {
        const generalMessage = `Merhaba! 🏷️ QR Sticker siparişi vermek istiyorum.\n\n📦 Ürün: 10x10 cm Şeffaf QR Sticker\n💰 Fiyat: 200 TL\n\nDetayları sizinle paylaşabilirim. Lütfen bana ulaşın.`;
        const whatsappNumber = '905349334631';
        const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(generalMessage)}`;
        window.open(whatsappURL, '_blank');
        return;
    }

    // Form verileri varsa detaylı mesaj oluştur
    const deliveryData = getDeliveryData();
    const citySelect = document.getElementById('deliveryCity');
    const districtSelect = document.getElementById('deliveryDistrict');
    
    let fullAddress = '';
    if (citySelect && districtSelect && deliveryData.address) {
        const cityText = citySelect.options[citySelect.selectedIndex]?.text || '';
        const districtText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
        fullAddress = `${deliveryData.address}, ${districtText}/${cityText}`;
    }
    const customerTheme = document.getElementById('customerTheme')?.value || 'default';
    const themeSelect = document.getElementById('customerTheme');
    const themeText = themeSelect ? themeSelect.options[themeSelect.selectedIndex].text : 'Varsayılan';

    // Sosyal medya linklerini topla
    const socialMedia = [];
    const socialMediaData = getSocialMediaData();
    socialMediaData.forEach(item => {
        const platform = socialMediaPlatforms[item.platform];
        if (platform) {
            socialMedia.push(`${platform.name}: ${item.url}`);
        }
    });

    // WhatsApp mesajını oluştur
    let message = `🏷️ *QR Sticker Siparişi*\n\n`;
    message += `👤 *Ad Soyad:* ${customerName}\n`;
    message += `📱 *Telefon:* ${customerPhone}\n`;
    if (fullAddress) {
        message += `\n📦 *Teslimat Bilgileri:*\n`;
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

    // WhatsApp linkini oluştur ve aç
    const whatsappNumber = '905349334631';
    const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappURL, '_blank');
}

// Theme preview function
function updateThemePreview() {
    const selectedTheme = document.getElementById('customerTheme').value;
    const previewElement = document.getElementById('themePreview');

    // Remove all theme classes
    previewElement.className = 'theme-preview';

    // Add selected theme class
    previewElement.classList.add('theme-' + selectedTheme);

    // Add smooth transition animation
    previewElement.style.transform = 'scale(0.95)';
    setTimeout(() => {
        previewElement.style.transform = 'scale(1)';
    }, 150);
}

// City-District Management
const districtData = {
    istanbul: ['Adalar', 'Arnavutköy', 'Ataşehir', 'Avcılar', 'Bağcılar', 'Bahçelievler', 'Bakırköy', 'Başakşehir', 'Bayrampaşa', 'Beşiktaş', 'Beykoz', 'Beylikdüzü', 'Beyoğlu', 'Büyükçekmece', 'Çatalca', 'Çekmeköy', 'Esenler', 'Esenyurt', 'Eyüpsultan', 'Fatih', 'Gaziosmanpaşa', 'Güngören', 'Kadıköy', 'Kağıthane', 'Kartal', 'Küçükçekmece', 'Maltepe', 'Pendik', 'Sancaktepe', 'Sarıyer', 'Silivri', 'Sultanbeyli', 'Sultangazi', 'Şile', 'Şişli', 'Tuzla', 'Ümraniye', 'Üsküdar', 'Zeytinburnu'],
    ankara: ['Akyurt', 'Altındağ', 'Ayaş', 'Bala', 'Beypazarı', 'Çamlıdere', 'Çankaya', 'Çubuk', 'Elmadağ', 'Etimesgut', 'Evren', 'Gölbaşı', 'Güdül', 'Haymana', 'Kalecik', 'Kazan', 'Keçiören', 'Kızılcahamam', 'Mamak', 'Nallıhan', 'Polatlı', 'Pursaklar', 'Sincan', 'Şereflikoçhisar', 'Yenimahalle'],
    izmir: ['Aliağa', 'Balçova', 'Bayındır', 'Bayraklı', 'Bergama', 'Beydağ', 'Bornova', 'Buca', 'Çeşme', 'Çiğli', 'Dikili', 'Foça', 'Gaziemir', 'Güzelbahçe', 'Karabağlar', 'Karaburun', 'Karşıyaka', 'Kemalpaşa', 'Kınık', 'Kiraz', 'Konak', 'Menderes', 'Menemen', 'Narlıdere', 'Ödemiş', 'Seferihisar', 'Selçuk', 'Tire', 'Torbalı', 'Urla'],
    bursa: ['Büyükorhan', 'Gemlik', 'Gürsu', 'Harmancık', 'İnegöl', 'İznik', 'Karacabey', 'Keles', 'Kestel', 'Mudanya', 'Mustafakemalpaşa', 'Nilüfer', 'Orhaneli', 'Orhangazi', 'Osmangazi', 'Yenişehir', 'Yıldırım'],
    antalya: ['Akseki', 'Aksu', 'Alanya', 'Demre', 'Döşemealtı', 'Elmalı', 'Finike', 'Gazipaşa', 'Gündoğmuş', 'İbradı', 'Kaş', 'Kemer', 'Kepez', 'Konyaaltı', 'Korkuteli', 'Kumluca', 'Manavgat', 'Muratpaşa', 'Serik'],
    adana: ['Aladağ', 'Ceyhan', 'Çukurova', 'Feke', 'İmamoğlu', 'Karaisalı', 'Karataş', 'Kozan', 'Pozantı', 'Saimbeyli', 'Sarıçam', 'Seyhan', 'Tufanbeyli', 'Yumurtalık', 'Yüreğir'],
    konya: ['Ahırlı', 'Akören', 'Akşehir', 'Altınekin', 'Beyşehir', 'Bozkır', 'Cihanbeyli', 'Çeltik', 'Çumra', 'Derbent', 'Derebucak', 'Doğanhisar', 'Emirgazi', 'Ereğli', 'Güneysinir', 'Hadim', 'Halkapınar', 'Hüyük', 'Ilgın', 'Kadınhanı', 'Karapınar', 'Karatay', 'Kulu', 'Meram', 'Selçuklu', 'Seydişehir', 'Taşkent', 'Tuzlukçu', 'Yalıhüyük', 'Yunak'],
    sanliurfa: ['Akçakale', 'Birecik', 'Bozova', 'Ceylanpınar', 'Eyyübiye', 'Haliliye', 'Harran', 'Hilvan', 'Karaköprü', 'Siverek', 'Suruç', 'Viranşehir'],
    gaziantep: ['Araban', 'İslahiye', 'Karkamış', 'Nizip', 'Nurdağı', 'Oğuzeli', 'Şahinbey', 'Şehitkamil', 'Yavuzeli'],
    kocaeli: ['Başiskele', 'Çayırova', 'Darıca', 'Derince', 'Dilovası', 'Gebze', 'Gölcük', 'İzmit', 'Kandıra', 'Karamürsel', 'Kartepe', 'Körfez'],
    mersin: ['Akdeniz', 'Anamur', 'Aydıncık', 'Bozyazı', 'Çamlıyayla', 'Erdemli', 'Gülnar', 'Mezitli', 'Mut', 'Silifke', 'Tarsus', 'Toroslar', 'Yenişehir'],
    diyarbakir: ['Bağlar', 'Bismil', 'Çermik', 'Çınar', 'Çüngüş', 'Dicle', 'Eğil', 'Ergani', 'Hani', 'Hazro', 'Kayapınar', 'Kocaköy', 'Kulp', 'Lice', 'Silvan', 'Sur', 'Yenişehir'],
    hatay: ['Altınözü', 'Antakya', 'Arsuz', 'Belen', 'Defne', 'Dörtyol', 'Erzin', 'Hassa', 'İskenderun', 'Kırıkhan', 'Kumlu', 'Payas', 'Reyhanlı', 'Samandağ', 'Yayladağı'],
    manisa: ['Ahmetli', 'Akhisar', 'Alaşehir', 'Demirci', 'Gölmarmara', 'Gördes', 'Kırkağaç', 'Köprübaşı', 'Kula', 'Salihli', 'Sarıgöl', 'Saruhanlı', 'Selendi', 'Soma', 'Şehzadeler', 'Turgutlu', 'Yunusemre'],
    kayseri: ['Akkışla', 'Bünyan', 'Develi', 'Felahiye', 'Hacılar', 'İncesu', 'Kocasinan', 'Melikgazi', 'Özvatan', 'Pınarbaşı', 'Sarıoğlan', 'Sarız', 'Talas', 'Tomarza', 'Yahyalı', 'Yeşilhisar'],
    samsun: ['19 Mayıs', 'Alaçam', 'Asarcık', 'Atakum', 'Ayvacık', 'Bafra', 'Canik', 'Çarşamba', 'Havza', 'İlkadım', 'Kavak', 'Ladik', 'Ondokuzmayıs', 'Salıpazarı', 'Tekkeköy', 'Terme', 'Vezirköprü', 'Yakakent'],
    balikesir: ['Ayvalık', 'Balya', 'Bandırma', 'Bigadiç', 'Burhaniye', 'Dursunbey', 'Edremit', 'Erdek', 'Gömeç', 'Gönen', 'Havran', 'İvrindi', 'Karesi', 'Kepsut', 'Manyas', 'Marmara', 'Susurluk', 'Şındırgı'],
    kahramanmaras: ['Afşin', 'Andırın', 'Çağlayancerit', 'Dulkadiroğlu', 'Ekinözü', 'Elbistan', 'Göksun', 'Nurhak', 'Onikişubat', 'Pazarcık', 'Türkoğlu'],
    van: ['Bahçesaray', 'Başkale', 'Çaldıran', 'Çatak', 'Edremit', 'Erciş', 'Gevaş', 'Gürpınar', 'İpekyolu', 'Muradiye', 'Özalp', 'Saray', 'Tuşba'],
    denizli: ['Acıpayam', 'Babadağ', 'Baklan', 'Bekilli', 'Beyağaç', 'Bozkurt', 'Buldan', 'Çal', 'Çameli', 'Çardak', 'Çivril', 'Güney', 'Honaz', 'Kale', 'Merkezefendi', 'Pamukkale', 'Sarayköy', 'Serinhisar', 'Tavas']
};

function updateDistricts() {
    const citySelect = document.getElementById('deliveryCity');
    const districtSelect = document.getElementById('deliveryDistrict');
    
    const selectedCity = citySelect.value;
    
    // İlçe seçimini sıfırla
    districtSelect.innerHTML = '<option value="">İlçe seçiniz</option>';
    
    if (selectedCity && districtData[selectedCity]) {
        // İlçeleri ekle
        districtData[selectedCity].forEach(district => {
            const option = document.createElement('option');
            option.value = district.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c');
            option.textContent = district;
            districtSelect.appendChild(option);
        });
        
        districtSelect.disabled = false;
    } else {
        districtSelect.disabled = true;
    }
}

function getDeliveryData() {
    return {
        city: document.getElementById('deliveryCity').value,
        district: document.getElementById('deliveryDistrict').value,
        address: document.getElementById('deliveryAddress').value,
        name: document.getElementById('deliveryName').value,
        phone: getFormattedPhoneNumber('delivery')
    };
}

// Social Media Management Functions
const socialMediaPlatforms = {
    instagram: {
        name: 'Instagram',
        icon: 'fab fa-instagram',
        prefix: '@',
        baseUrl: 'https://instagram.com/',
        placeholder: 'kullanıcı_adı',
        color: 'platform-instagram'
    },
    x: {
        name: 'X',
        icon: 'fab fa-twitter',
        prefix: '@',
        baseUrl: 'https://x.com/',
        placeholder: 'kullanıcı_adı',
        color: 'platform-x'
    },
    linkedin: {
        name: 'LinkedIn',
        icon: 'fab fa-linkedin',
        prefix: '',
        baseUrl: 'https://linkedin.com/in/',
        placeholder: 'profil-adı',
        color: 'platform-linkedin'
    },
    facebook: {
        name: 'Facebook',
        icon: 'fab fa-facebook',
        prefix: '',
        baseUrl: 'https://facebook.com/',
        placeholder: 'profil-adı',
        color: 'platform-facebook'
    },
    youtube: {
        name: 'YouTube',
        icon: 'fab fa-youtube',
        prefix: '',
        baseUrl: 'https://youtube.com/@',
        placeholder: 'kanal_adı',
        color: 'platform-youtube'
    },
    tiktok: {
        name: 'TikTok',
        icon: 'fab fa-tiktok',
        prefix: '@',
        baseUrl: 'https://tiktok.com/@',
        placeholder: 'kullanıcı_adı',
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
        placeholder: 'www.acdisoftware.com.tr',
        color: 'platform-website'
    },
    snapchat: {
        name: 'Snapchat',
        icon: 'fab fa-snapchat',
        prefix: '',
        baseUrl: 'https://snapchat.com/add/',
        placeholder: 'kullanıcı_adı',
        color: 'platform-snapchat'
    },
    discord: {
        name: 'Discord',
        icon: 'fab fa-discord',
        prefix: '#',
        baseUrl: '',
        placeholder: 'kullanıcı#1234',
        color: 'platform-discord'
    },
    telegram: {
        name: 'Telegram',
        icon: 'fab fa-telegram',
        prefix: '@',
        baseUrl: 'https://t.me/',
        placeholder: 'kullanıcı_adı',
        color: 'platform-telegram'
    },
    twitch: {
        name: 'Twitch',
        icon: 'fab fa-twitch',
        prefix: '',
        baseUrl: 'https://twitch.tv/',
        placeholder: 'kanal_adı',
        color: 'platform-twitch'
    }
};

let selectedSocialMedias = [];

function initSocialMediaHandlers() {
    document.querySelectorAll('.social-platform-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.dataset.platform;
            addSocialMediaPlatform(platform);
        });
    });
}

function addSocialMediaPlatform(platformKey) {
    console.log('Platform ekleniyor:', platformKey);
    console.log('Mevcut platforms:', Object.keys(socialMediaPlatforms));
    
    // WhatsApp platform seçildiğinde telefon inputlarını güncelle
    if (platformKey === 'whatsapp') {
        resetPhoneInputsForWhatsApp();
    }
    
    // Check if already added
    if (selectedSocialMedias.find(item => item.platform === platformKey)) {
        showToast('Bu platform zaten eklenmiş!', 'warning');
        return;
    }

    const platform = socialMediaPlatforms[platformKey];
    console.log('Platform bulundu:', platform);
    
    if (!platform) {
        console.error('Platform bulunamadı:', platformKey);
        return;
    }

    const socialMediaItem = {
        platform: platformKey,
        username: '',
        url: ''
    };

    // WhatsApp için otomatik telefon numarası doldur
    if (platformKey === 'whatsapp') {
        const phoneInput = document.getElementById('customerPhone');
        if (phoneInput && phoneInput.value) {
            const phoneNumber = phoneInput.value.replace(/\D/g, '');
            socialMediaItem.username = phoneNumber;
            socialMediaItem.url = generateSocialMediaUrl(platformKey, phoneNumber);
        }
    }

    selectedSocialMedias.push(socialMediaItem);
    renderSocialMediaItem(socialMediaItem, selectedSocialMedias.length - 1);
    updatePlatformButton(platformKey, true);
    
    // Eklenen sosyal medya input'una scroll yap
    setTimeout(() => {
        const newItem = document.querySelector('.social-media-item:last-child');
        if (newItem) {
            newItem.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Input alanına fokus ver
            const input = newItem.querySelector('.social-username-input');
            if (input) {
                input.focus();
            }
        }
    }, 100);
}

function renderSocialMediaItem(item, index) {
    const platform = socialMediaPlatforms[item.platform];
    const container = document.getElementById('selectedSocialMedias');
    
    // WhatsApp için özel placeholder
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
                <button type="button" class="remove-btn" onclick="removeSocialMediaPlatform(${index})">
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
                       oninput="updateSocialMediaUrl(${index})"
                       value="${item.username}">
            </div>
            <div class="url-preview" id="urlPreview${index}">
                ${generateSocialMediaUrl(item.platform, item.username)}
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function updateSocialMediaUrl(index) {
    const item = selectedSocialMedias[index];
    const input = document.querySelector(`input[data-index="${index}"]`);
    const platform = socialMediaPlatforms[item.platform];
    
    item.username = input.value;
    item.url = generateSocialMediaUrl(item.platform, item.username);
    
    const preview = document.getElementById(`urlPreview${index}`);
    preview.textContent = item.url || 'URL oluşturulacak...';
}

function generateSocialMediaUrl(platformKey, username) {
    if (!username) return '';
    
    const platform = socialMediaPlatforms[platformKey];
    
    switch(platformKey) {
        case 'whatsapp':
            // WhatsApp için sadece numara, temizlenmiş format
            const cleanNumber = username.replace(/\D/g, '');
            return cleanNumber ? platform.baseUrl + cleanNumber : '';
        case 'website':
            // Website için direkt URL
            return username.startsWith('http') ? username : 'https://' + username;
        case 'discord':
            // Discord için sadece username#tag
            return username;
        default:
            return platform.baseUrl + username;
    }
}

function removeSocialMediaPlatform(index) {
    const item = selectedSocialMedias[index];
    selectedSocialMedias.splice(index, 1);
    
    // Re-render all items with updated indices
    const container = document.getElementById('selectedSocialMedias');
    container.innerHTML = '';
    
    selectedSocialMedias.forEach((socialItem, newIndex) => {
        renderSocialMediaItem(socialItem, newIndex);
    });
    
    updatePlatformButton(item.platform, false);
}

function updatePlatformButton(platformKey, selected) {
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
}

function getSocialMediaData() {
    return selectedSocialMedias.filter(item => item.username.trim() !== '');
}

// Modern phone number formatting with country code dropdown
function formatPhoneNumber(type) {
    const countrySelect = document.getElementById(type + 'CountryCode');
    const phoneInput = document.getElementById(type + 'Phone');
    
    if (!countrySelect || !phoneInput) return;
    
    const countryCode = countrySelect.value;
    let phoneValue = phoneInput.value.replace(/\D/g, ''); // Sadece rakamları al
    
    // Ülke koduna göre formatla
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
            // Diğer ülkeler için basit format
            if (phoneValue.length > 12) phoneValue = phoneValue.substring(0, 12);
            break;
    }
    
    phoneInput.value = phoneValue;
}

function getFormattedPhoneNumber(type) {
    const countrySelect = document.getElementById(type + 'CountryCode');
    const phoneInput = document.getElementById(type + 'Phone');
    
    if (!countrySelect || !phoneInput) return '';
    
    const countryCode = countrySelect.value;
    const phoneNumber = phoneInput.value.replace(/\D/g, '');
    
    return phoneNumber ? countryCode + phoneNumber : '';
}

// Ülke kodu değiştiğinde placeholder'ı güncelle
function updatePhonePlaceholder(type) {
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
    
    // Mevcut numarayı yeni formata göre düzenle
    if (phoneInput.value) {
        formatPhoneNumber(type);
    }
}

// WhatsApp seçildiğinde telefon inputlarını güncelle
function resetPhoneInputsForWhatsApp() {
    // Müşteri telefonu için +90 seç
    const customerCountryCode = document.getElementById('customerCountryCode');
    const customerPhone = document.getElementById('customerPhone');
    
    if (customerCountryCode && customerPhone) {
        customerCountryCode.value = '+90';
        updatePhonePlaceholder('customer');
        customerPhone.focus();
    }
    
    // Teslimat telefonu için +90 seç (eğer varsa)
    const deliveryCountryCode = document.getElementById('deliveryCountryCode');
    const deliveryPhone = document.getElementById('deliveryPhone');
    
    if (deliveryCountryCode && deliveryPhone) {
        deliveryCountryCode.value = '+90';
        updatePhonePlaceholder('delivery');
    }
    
    showToast('WhatsApp için telefon alanları Türkiye (+90) olarak ayarlandı!', 'success');
}

// WhatsApp widget function
function openWhatsApp() {
    const phoneNumber = '905349334631';
    const message = 'Merhaba! Kişisel QR sistemi hakkında bilgi almak istiyorum. Yardımcı olabilir misiniz?';
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    
    // Analytics tracking (isteğe bağlı)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'whatsapp_widget_click', {
            'event_category': 'engagement',
            'event_label': 'help_request'
        });
    }
    
    // WhatsApp'ı yeni sekmede aç
    window.open(whatsappUrl, '_blank');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize social media handlers
    initSocialMediaHandlers();
    
    // Initialize phone number dropdowns
    const customerCountryCode = document.getElementById('customerCountryCode');
    const deliveryCountryCode = document.getElementById('deliveryCountryCode');
    
    // Add event listeners for country code changes
    if (customerCountryCode) {
        customerCountryCode.addEventListener('change', function() {
            updatePhonePlaceholder('customer');
        });
        // Set initial placeholder
        updatePhonePlaceholder('customer');
    }
    
    // Handle delivery phone dropdown (will be available after step 2)
    setTimeout(() => {
        const deliveryCountryCode = document.getElementById('deliveryCountryCode');
        if (deliveryCountryCode) {
            deliveryCountryCode.addEventListener('change', function() {
                updatePhonePlaceholder('delivery');
            });
            // Set initial placeholder
            updatePhonePlaceholder('delivery');
        }
    }, 1000);
});

// Initialize theme preview when modal opens
document.addEventListener('DOMContentLoaded', function() {
    const orderModal = document.getElementById('orderModal');
    if (orderModal) {
        orderModal.addEventListener('shown.bs.modal', function() {
            updateThemePreview();
        });
    }
});
