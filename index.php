<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişisel QR - Dijital Kimliğinizi QR Kodla Taşıyın</title>
    <meta name="description" content="Profesyonel QR kod profil sistemi ile sosyal medya hesaplarınızı, iletişim bilgilerinizi ve tüm dijital varlığınızı tek QR kodda toplayın. Modern tasarım, özelleştirilebilir temalar ve kolay yönetim.">
    <meta name="keywords" content="QR kod, dijital kartvizit, sosyal medya profili, kişisel QR, profesyonel profil, digital business card, contactless sharing">
    <meta name="author" content="Kişisel QR">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://acdisoftware.com.tr/kisisel_qr/">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Kişisel QR - Dijital Kimliğinizi QR Kodla Taşıyın">
    <meta property="og:description" content="Profesyonel QR kod profil sistemi ile sosyal medya hesaplarınızı, iletişim bilgilerinizi ve tüm dijital varlığınızı tek QR kodda toplayın.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://acdisoftware.com.tr/kisisel_qr/">
    <meta property="og:image" content="https://acdisoftware.com.tr/kisisel_qr/assets/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Kişisel QR">
    <meta property="og:locale" content="tr_TR">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Kişisel QR - Dijital Kimliğinizi QR Kodla Taşıyın">
    <meta name="twitter:description" content="Profesyonel QR kod profil sistemi ile sosyal medya hesaplarınızı, iletişim bilgilerinizi ve tüm dijital varlığınızı tek QR kodda toplayın.">
    <meta name="twitter:image" content="https://acdisoftware.com.tr/kisisel_qr/assets/images/twitter-card.jpg">
    <meta name="twitter:site" content="@kisiselqr">
    <meta name="twitter:creator" content="@kisiselqr">

    <!-- Additional SEO Meta Tags -->
    <meta name="application-name" content="Kişisel QR">
    <meta name="msapplication-TileColor" content="#3498db">
    <meta name="msapplication-TileImage" content="https://acdisoftware.com.tr/kisisel_qr/assets/images/ms-tile-144x144.png">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="032x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    <meta name="theme-color" content="#3498db">

    <!-- CSS Dosyaları -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/landing.css" rel="stylesheet">
    
    <!-- Inline WhatsApp Widget CSS -->
    <style>
        /* WhatsApp Widget Styles */
        .whatsapp-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            cursor: pointer;
            animation: whatsapp-pulse 2s infinite;
        }

        .whatsapp-button {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .whatsapp-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.5);
        }

        .whatsapp-button i {
            font-size: 24px;
            z-index: 2;
        }

        .whatsapp-text {
            position: absolute;
            left: -80px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            color: #25d366;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            opacity: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .whatsapp-widget:hover .whatsapp-text {
            opacity: 1;
            left: -90px;
        }

        .whatsapp-tooltip {
            position: absolute;
            bottom: 70px;
            right: 0;
            background: white;
            color: #333;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            white-space: nowrap;
            pointer-events: none;
            border: 1px solid rgba(37, 211, 102, 0.2);
        }

        .whatsapp-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid white;
        }

        .whatsapp-widget:hover .whatsapp-tooltip {
            opacity: 1;
            transform: translateY(0);
        }

        /* WhatsApp Widget Animations */
        @keyframes whatsapp-pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Responsive WhatsApp Widget */
        @media (max-width: 768px) {
            .whatsapp-widget {
                bottom: 20px;
                right: 20px;
            }
            
            .whatsapp-button {
                width: 55px;
                height: 55px;
            }
            
            .whatsapp-button i {
                font-size: 22px;
            }
            
            .whatsapp-tooltip {
                font-size: 13px;
                padding: 10px 14px;
                right: -10px;
            }
        }

        /* Modal açıkken WhatsApp widget'ını gizle */
        body.modal-open .whatsapp-widget {
            display: none;
        }

        /* Toast notification styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 15px 20px;
            min-width: 280px;
            transform: translateX(350px);
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.toast-success {
            border-left-color: #28a745;
        }

        .toast-notification.toast-error {
            border-left-color: #dc3545;
        }

        .toast-notification.toast-warning {
            border-left-color: #ffc107;
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-content i {
            font-size: 18px;
            color: #007bff;
        }

        .toast-success .toast-content i {
            color: #28a745;
        }

        .toast-error .toast-content i {
            color: #dc3545;
        }

        .toast-warning .toast-content i {
            color: #ffc107;
        }

        /* Loading button styles */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!-- WhatsApp Widget (Modüler) -->
    <link href="assets/css/whatsapp-widget.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-qrcode me-2"></i>Kişisel QR
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Özellikler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#demo">Demo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Fiyatlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1>Dijital Kimliğinizi <span style="color: #ffd700;">QR Kodla</span> Taşıyın</h1>
                    <p class="lead">
                        🚀 Tek QR kodda tüm sosyal medya hesaplarınız, iletişim bilgileriniz ve dijital varlığınız.
                        Profesyonel profil sistemi ile markanızı güçlendirin!
                    </p>
                    <div class="cta-buttons">
                        <a href="https://acdisoftware.com.tr/kisisel_qr/qr/7d268b70" target="_blank" class="btn-hero btn-primary-hero">
                            <i class="fas fa-eye"></i>
                            Canlı Demo
                        </a>
                        <button class="btn-hero btn-outline-hero" onclick="showOrderForm()">
                            <i class="fas fa-shopping-cart"></i>
                            QR Sticker Sipariş (10x10 cm) - 200₺
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="demo-mockup">
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 600'><rect width='300' height='600' fill='%23f8f9fa'/><circle cx='150' cy='120' r='40' fill='%233498db'/><rect x='50' y='180' width='200' height='20' rx='10' fill='%232c3e50'/><rect x='70' y='220' width='160' height='15' rx='7' fill='%236c757d'/><rect x='30' y='260' width='240' height='40' rx='20' fill='%233498db'/><rect x='30' y='320' width='240' height='40' rx='20' fill='%2325d366'/><rect x='30' y='380' width='240' height='40' rx='20' fill='%231877f2'/></svg>" alt="Profile Demo" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Aktif Kullanıcı</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">12+</span>
                        <span class="stat-label">Tema Seçeneği</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">15+</span>
                        <span class="stat-label">Sosyal Platform</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Uptime</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-4">🎯 Neden Kişisel QR?</h2>
                <p class="lead text-muted">Modern dijital çağda öne çıkmanızı sağlayacak güçlü özellikler</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3>🎨 12+ Modern Tema</h3>
                        <p>Markanıza uygun profesyonel temalar. Karanlık, neon, minimal, altın ve daha fazlası! Kişiselleştirin ve öne çıkın.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3>🌐 Sosyal Medya Hub'ı</h3>
                        <p>Instagram, LinkedIn, WhatsApp, TikTok ve 15+ platform desteği. Tüm sosyal varlığınızı tek noktada toplayın.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>📱 Mobil Uyumlu</h3>
                        <p>Tüm cihazlarda mükemmel görünüm. Responsive tasarım ile her ekran boyutunda profesyonel deneyim.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>☎️ Direkt İletişim</h3>
                        <p>Modern telefon ve WhatsApp butonları. Tek tıkla arama ve mesajlaşma imkanı.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>📊 Detaylı İstatistikler</h3>
                        <p>QR kod taramalarını, tıklamaları ve ziyaretçi analizlerini takip edin. Verilerle büyüyün.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>🔒 Güvenli & Hızlı</h3>
                        <p>SSL şifreleme, güvenli hosting ve yıldırım hızında yükleme. Verileriniz bizimle güvende.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="demo">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-4">🚀 Nasıl Çalışır?</h2>
                <p class="lead text-muted">3 basit adımda profesyonel QR profili oluşturun</p>
                <div class="mt-4">
                    <a href="https://acdisoftware.com.tr/kisisel_qr/qr/7d268b70" target="_blank" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-eye me-2"></i>Canlı Demo Görün
                    </a>
                    <a href="#pricing" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Şimdi Sipariş Ver
                    </a>
                </div>
            </div>

            <div class="row g-4 align-items-center">
                <div class="col-lg-4">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <span style="font-size: 2rem; font-weight: bold;">1</span>
                        </div>
                        <h4>📝 Profil Oluşturun</h4>
                        <p>İsim, bio, telefon ve fotoğrafınızı ekleyin. Sosyal medya hesaplarınızı bağlayın.</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <span style="font-size: 2rem; font-weight: bold;">2</span>
                        </div>
                        <h4>🎨 Tema Seçin</h4>
                        <p>12+ profesyonel tema arasından markanıza en uygununu seçin ve kişiselleştirin.</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <span style="font-size: 2rem; font-weight: bold;">3</span>
                        </div>
                        <h4>📱 QR Sticker Alın</h4>
                        <p>10x10 cm boyutunda, şeffaf arka planlı, kaliteli QR sticker'ınızı sipariş edin. 1 hafta içinde kapınızda!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-4">💬 Kullanıcılarımız Ne Diyor?</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Müşterilerimle iletişim kurmak hiç bu kadar kolay olmamıştı. QR kodumu kartvizitimde kullanıyorum, harika!"
                        </div>
                        <div class="testimonial-author">- Ahmet K., Emlak Uzmanı</div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Etkinliklerde QR kodumu gösteriyorum, herkes hemen sosyal medya hesaplarıma ulaşabiliyor. Çok pratik!"
                        </div>
                        <div class="testimonial-author">- Zeynep M., Influencer</div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Restoranda masalara QR kod koyduk. Müşteriler menümüzü ve sosyal medyamızı kolayca buluyor."
                        </div>
                        <div class="testimonial-author">- Mehmet T., Restoran Sahibi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-4">💰 QR Sticker Siparişi</h2>
                <p class="lead text-muted">Şeffaf arka planlı, kaliteli QR sticker'ınızı sipariş edin</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="pricing-card featured">
                        <div class="badge bg-primary position-absolute top-0 start-50 translate-middle px-3 py-1">Özel Fiyat</div>
                        <h3>🏷️ QR Sticker</h3>
                        <div class="price">₺200<span class="price-period"></span></div>
                        <div class="text-center mb-3">
                            <span class="badge bg-info text-dark fs-6">📏 10x10 cm Boyutunda</span>
                        </div>
                        <ul class="list-unstyled mt-4">
                            <li>✅ Şeffaf arka planlı sticker</li>
                            <li>✅ 10x10 cm profesyonel boyut</li>
                            <li>✅ Kaliteli, dayanıklı malzeme</li>
                            <li>✅ 1 hafta içinde teslimat</li>
                            <li>✅ Kargo dahil</li>
                            <li>✅ Su geçirmez</li>
                            <li>✅ UV dayanımlı</li>
                            <li>✅ Kolay uygulama</li>
                        </ul>
                        <button class="btn btn-primary w-100 mt-3" onclick="showOrderForm()">
                            <i class="fas fa-shopping-cart me-2"></i>Şimdi Sipariş Ver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="cta">
        <div class="container">
            <h2>🎯 QR Sticker'ınızı Hemen Sipariş Edin!</h2>
            <p class="lead">
                10x10 cm boyutunda, kaliteli, şeffaf arka planlı QR sticker'ınız 1 hafta içinde kapınızda.
                Profesyonel görünüm, dayanıklı malzeme!
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <button class="btn-hero btn-primary-hero" onclick="showOrderForm()">
                    <i class="fas fa-shopping-cart"></i>
                    Şimdi Sipariş Ver - 200₺
                </button>
                <a href="https://wa.me/905349334631?text=Merhaba, QR sticker hakkında bilgi almak istiyorum."
                    target="_blank" class="btn-hero btn-outline-hero">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp'tan Sor
                </a>
            </div>

            <div class="row mt-5 text-center">
                <div class="col-md-4">
                    <h5>📧 E-posta</h5>
                    <p>ademcaniyik7@gmail.com</p>
                </div>
                <div class="col-md-4">
                    <h5>📱 WhatsApp</h5>
                    <p>+90 534 933 46 31</p>
                </div>
                <div class="col-md-4">
                    <h5>⏰ Çalışma Saatleri</h5>
                    <p>Pazartesi-Cuma: 09:00-18:00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">🏷️ QR Sticker Siparişi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="step1" class="order-step">
                        <h6>1. Bilgilerinizi Girin</h6>
                        <form id="orderForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerName" class="form-label">Ad Soyad *</label>
                                        <input type="text" class="form-control" id="customerName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerPhone" class="form-label">Telefon *</label>
                                        <div class="phone-input-container">
                                            <select class="form-control country-dropdown" id="customerCountryCode">
                                                <option value="+90" data-flag="🇹🇷">🇹🇷 +90</option>
                                                <option value="+1" data-flag="🇺🇸">🇺🇸 +1</option>
                                                <option value="+44" data-flag="🇬🇧">🇬🇧 +44</option>
                                                <option value="+49" data-flag="🇩🇪">🇩🇪 +49</option>
                                                <option value="+33" data-flag="🇫🇷">🇫🇷 +33</option>
                                                <option value="+971" data-flag="🇦🇪">🇦🇪 +971</option>
                                                <option value="+966" data-flag="🇸🇦">🇸🇦 +966</option>
                                                <option value="+7" data-flag="🇷🇺">🇷🇺 +7</option>
                                                <option value="+86" data-flag="🇨🇳">🇨🇳 +86</option>
                                                <option value="+91" data-flag="🇮🇳">🇮🇳 +91</option>
                                            </select>
                                            <input type="tel" class="form-control phone-number-input" id="customerPhone" required 
                                                   placeholder="555 555 55 55" 
                                                   oninput="formatPhoneNumber('customer')">
                                        </div>
                                        <small class="form-text text-muted">Telefon numaranızı ülke kodu ile birlikte giriniz</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customerPhoto" class="form-label">Profil Fotoğrafı (isteğe bağlı)</label>
                                <input type="file" class="form-control" id="customerPhoto" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="customerBio" class="form-label">Kısa Yazı (İsteğe bağlı)</label>
                                <textarea class="form-control" id="customerBio" rows="2" placeholder="Kendizi tanıtın..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sosyal Medya Hesapları (isteğe bağlı)</label>
                                
                                <!-- Sosyal Medya Platform Seçimi -->
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Platform Ekle</h6>
                                        <div class="row g-2 social-platforms-grid">
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="instagram">
                                                    <i class="fab fa-instagram text-danger"></i>
                                                    <span class="d-block small">Instagram</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="x">
                                                    <i class="fab fa-twitter" style="color: #1da1f2;"></i>
                                                    <span class="d-block small">X</span>
                                                </button>
                                            </div>

                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="linkedin">
                                                    <i class="fab fa-linkedin text-primary"></i>
                                                    <span class="d-block small">LinkedIn</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="facebook">
                                                    <i class="fab fa-facebook text-primary"></i>
                                                    <span class="d-block small">Facebook</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="youtube">
                                                    <i class="fab fa-youtube text-danger"></i>
                                                    <span class="d-block small">YouTube</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="tiktok">
                                                    <i class="fab fa-tiktok text-dark"></i>
                                                    <span class="d-block small">TikTok</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="whatsapp">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                    <span class="d-block small">WhatsApp</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="website">
                                                    <i class="fas fa-globe text-info"></i>
                                                    <span class="d-block small">Website</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="snapchat">
                                                    <i class="fab fa-snapchat text-warning"></i>
                                                    <span class="d-block small">Snapchat</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="discord">
                                                    <i class="fab fa-discord text-primary"></i>
                                                    <span class="d-block small">Discord</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="telegram">
                                                    <i class="fab fa-telegram text-info"></i>
                                                    <span class="d-block small">Telegram</span>
                                                </button>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <button type="button" class="btn btn-outline-secondary w-100 social-platform-btn" data-platform="twitch">
                                                    <i class="fab fa-twitch text-purple"></i>
                                                    <span class="d-block small">Twitch</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Seçilen Sosyal Medya Hesapları -->
                                <div id="selectedSocialMedias" class="selected-social-medias">
                                    <!-- Dinamik olarak eklenecek -->
                                </div>
                            </div>

                            <!-- İban ve Kan Grubu Alanları -->
                            <div class="mb-3">
                                <label class="form-label">Ek Bilgiler (isteğe bağlı)</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customerIban" class="form-label">İban</label>
                                            <input type="text" class="form-control" id="customerIban"
                                                placeholder="TR00 0000 0000 0000 0000 0000 00"
                                                pattern="^TR[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{2}$"
                                                maxlength="32">
                                            <small class="form-text text-muted">TR ile başlayan 26 haneli İban numarası</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customerBloodType" class="form-label">Kan Grubu</label>
                                            <select class="form-select" id="customerBloodType">
                                                <option value="">Seçiniz</option>
                                                <option value="A+">A Rh+</option>
                                                <option value="A-">A Rh-</option>
                                                <option value="B+">B Rh+</option>
                                                <option value="B-">B Rh-</option>
                                                <option value="AB+">AB Rh+</option>
                                                <option value="AB-">AB Rh-</option>
                                                <option value="0+">0 Rh+</option>
                                                <option value="0-">0 Rh-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="customerTheme" class="form-label">Tema Seçimi</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select" id="customerTheme" onchange="updateThemePreview()">
                                            <option value="default">Sade Temiz (Varsayılan)</option>
                                            <option value="blue">Deniz Mavisi</option>
                                            <option value="nature">Günbatımı Sıcak</option>
                                            <option value="elegant">Doğa Yeşil</option>
                                            <option value="gold">Altın Lüks</option>
                                            <option value="purple">Kraliyet Moru</option>
                                            <option value="dark">Karanlık Siyah</option>
                                            <option value="ocean">Sakura Pembe</option>
                                            <option value="minimal">Şık Mor</option>
                                            <option value="pastel">Pastel Rüya</option>
                                            <option value="retro">Retro Synthwave</option>
                                            <option value="neon">Neon Siber</option>
                                        </select>
                                        <small class="form-text text-muted">Profilinizde kullanılacak görsel tema</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="theme-preview-container">
                                            <label class="form-label">Tema Önizlemesi</label>
                                            <div id="themePreview" class="theme-preview theme-default">
                                                <div class="preview-header">
                                                    <div class="preview-avatar">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div class="preview-info">
                                                        <h6>Örnek Profil</h6>
                                                        <small>Yazılım Geliştirici</small>
                                                    </div>
                                                </div>
                                                <div class="preview-social">
                                                    <div class="preview-social-btn">
                                                        <i class="fab fa-instagram"></i>
                                                        <span>Instagram</span>
                                                    </div>
                                                    <div class="preview-social-btn">
                                                        <i class="fab fa-twitter"></i>
                                                        <span>X</span>
                                                    </div>
                                                    <div class="preview-social-btn">
                                                        <i class="fab fa-linkedin"></i>
                                                        <span>LinkedIn</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Devam Et</button>
                        </form>
                    </div>

                    <div id="step2" class="order-step" style="display: none;">
                        <h6>2. Teslimat & Ödeme Bilgileri</h6>
                        
                        <!-- Teslimat Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary me-2"></i>Teslimat Adresi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="deliveryCity" class="form-label">İl *</label>
                                        <select class="form-select" id="deliveryCity" required onchange="updateDistricts()">
                                            <option value="">İl seçiniz</option>
                                            <option value="istanbul">İstanbul</option>
                                            <option value="ankara">Ankara</option>
                                            <option value="izmir">İzmir</option>
                                            <option value="bursa">Bursa</option>
                                            <option value="antalya">Antalya</option>
                                            <option value="adana">Adana</option>
                                            <option value="konya">Konya</option>
                                            <option value="sanliurfa">Şanlıurfa</option>
                                            <option value="gaziantep">Gaziantep</option>
                                            <option value="kocaeli">Kocaeli</option>
                                            <option value="mersin">Mersin</option>
                                            <option value="diyarbakir">Diyarbakır</option>
                                            <option value="hatay">Hatay</option>
                                            <option value="manisa">Manisa</option>
                                            <option value="kayseri">Kayseri</option>
                                            <option value="samsun">Samsun</option>
                                            <option value="balikesir">Balıkesir</option>
                                            <option value="kahramanmaras">Kahramanmaraş</option>
                                            <option value="van">Van</option>
                                            <option value="denizli">Denizli</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="deliveryDistrict" class="form-label">İlçe *</label>
                                        <select class="form-select" id="deliveryDistrict" required disabled>
                                            <option value="">Önce il seçiniz</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="deliveryAddress" class="form-label">Detay Adres *</label>
                                    <textarea class="form-control" id="deliveryAddress" rows="3" required 
                                              placeholder="Mahalle, sokak, bina no, daire no vb. detayları yazınız..."></textarea>
                                    <small class="form-text text-muted">QR sticker'ınızın gönderileceği tam adres</small>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="deliveryName" class="form-label">Alıcı Adı *</label>
                                        <input type="text" class="form-control" id="deliveryName" required>
                                        <small class="form-text text-muted">Paketi teslim alacak kişi</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="deliveryPhone" class="form-label">Alıcı Telefonu *</label>
                                        <div class="phone-input-container">
                                            <select class="form-control country-dropdown" id="deliveryCountryCode">
                                                <option value="+90" data-flag="🇹🇷">🇹🇷 +90</option>
                                                <option value="+1" data-flag="🇺🇸">🇺🇸 +1</option>
                                                <option value="+44" data-flag="🇬🇧">🇬🇧 +44</option>
                                                <option value="+49" data-flag="🇩🇪">🇩🇪 +49</option>
                                                <option value="+33" data-flag="🇫🇷">🇫🇷 +33</option>
                                                <option value="+971" data-flag="🇦🇪">🇦🇪 +971</option>
                                                <option value="+966" data-flag="🇸🇦">🇸🇦 +966</option>
                                                <option value="+7" data-flag="🇷🇺">🇷🇺 +7</option>
                                                <option value="+86" data-flag="🇨🇳">🇨🇳 +86</option>
                                                <option value="+91" data-flag="🇮🇳">🇮🇳 +91</option>
                                            </select>
                                            <input type="tel" class="form-control phone-number-input" id="deliveryPhone" required 
                                                   placeholder="555 555 55 55" 
                                                   oninput="formatPhoneNumber('delivery')">
                                        </div>
                                        <small class="form-text text-muted">Kargo teslim için iletişim numarası</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ödeme Yöntemi -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-credit-card text-success me-2"></i>Ödeme Yöntemi</h6>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">Ödeme Yöntemini Seçin</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check payment-option">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bank_transfer" checked>
                                            <label class="form-check-label w-100" for="bankTransfer">
                                                <div class="card h-100 border-primary">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-university fa-2x text-primary mb-2"></i>
                                                        <h6>Banka Havalesi</h6>
                                                        <small class="text-muted">Hemen ödeme yapın</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check payment-option">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="cashOnDelivery" value="cash_on_delivery">
                                            <label class="form-check-label w-100" for="cashOnDelivery">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-truck fa-2x text-success mb-2"></i>
                                                        <h6>Kapıda Ödeme</h6>
                                                        <small class="text-muted">Teslim alırken ödeyin</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banka Havalesi Bilgileri -->
                        <div id="bankTransferInfo" class="card payment-info">
                            <div class="card-header">
                                <h6 class="mb-0">Ödeme Yapılacak Hesap</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Ad Soyad:</strong>
                                            <span id="payeeName">Ademcan İyik</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('payeeName')" title="Kopyala">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </p>
                                        <p><strong>Banka:</strong> QNB Finansbank</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>IBAN:</strong>
                                            <span id="ibanNumber">TR52 0011 1000 0000 0088 7455 51</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('ibanNumber')" title="Kopyala">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </p>
                                        <p><strong>Tutar:</strong> <span class="text-success fw-bold">200 TL</span></p>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ödeme yaptıktan sonra alt taraftaki butona tıklayarak sipariş verin.
                                </div>
                            </div>
                        </div>

                        <!-- Kapıda Ödeme Bilgileri -->
                        <div id="cashOnDeliveryInfo" class="card payment-info" style="display: none;">
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-truck me-2"></i>
                                    <strong>Kapıda Ödeme Seçtiniz</strong>
                                    <p class="mb-2 mt-2">Siparişiniz hazırlandıktan sonra kargo ile gönderilecek ve teslim alırken 200 TL ödeme yapacaksınız.</p>
                                    <small class="text-muted">• Kargo ücreti dahildir<br>• Sadece 200 TL nakit ödeme</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">Geri</button>
                            <button type="button" class="btn btn-success" id="completeOrderBtn" onclick="completeOrder()">Ödeme Yaptım, Sipariş Ver</button>
                        </div>
                    </div>

                    <div id="step3" class="order-step" style="display: none;">
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Siparişiniz Alındı!</h4>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-gift me-2"></i>Özel Hediye!</h6>
                                <p class="mb-2">Siparişinizle birlikte profiliniz de oluşturuldu!</p>
                                <a href="#" id="profileLink" class="btn btn-info btn-sm">
                                    <i class="fas fa-user me-2"></i>Profilimi Görüntüle
                                </a>
                            </div>
                            <p>WhatsApp üzerinden siparişinizi bildirin:</p>
                            <a href="#" id="whatsappLink" class="btn btn-success btn-lg mb-3" target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp'tan Sipariş Bildir
                            </a>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Profilinizi istediğiniz zaman düzenleyebilir, sosyal medya linklerinizi güncelleyebilirsiniz.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <h5><i class="fas fa-qrcode me-2"></i>Kişisel QR</h5>
                    <p>Dijital çağın modern kartviziti. QR kod ile profesyonel profil yönetimi.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6>Ürün</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features">Özellikler</a></li>
                        <li><a href="#pricing">Fiyatlar</a></li>
                        <li><a href="#demo">Demo</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Destek</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Yardım Merkezi</a></li>
                        <li><a href="#">Dokümantasyon</a></li>
                        <li><a href="#contact">İletişim</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Şirket</h6>
                    <ul class="list-unstyled">
                        <li><a href="hakkimizda.php">Hakkımızda</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Kariyer</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Yasal</h6>
                    <ul class="list-unstyled">
                        <li><a href="gizlilik-politikasi.php">Gizlilik Politikası</a></li>
                        <li><a href="kullanim-sartlari.php">Kullanım Şartları</a></li>
                        <li><a href="kvkk.php">KVKK</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: #555;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 Kişisel QR. Tüm hakları saklıdır.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Made with ❤️ in Acdisoftware</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Widget -->
    <div class="whatsapp-widget" id="whatsappWidget">
        <div class="whatsapp-button" onclick="openWhatsApp()">
            <i class="fab fa-whatsapp"></i>
            <span class="whatsapp-text">Yardım</span>
        </div>
        <div class="whatsapp-tooltip">
            Merhaba! Size nasıl yardımcı olabilirim? 💬
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Inline JavaScript (Restored from original) -->
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Contact form submission
        function handleFormSubmit(event) {
            event.preventDefault();
            // Handle form submission
            alert('Mesajınız alındı! En kısa sürede dönüş yapacağız.');
        }

        // Copy to clipboard function
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent || element.innerText;

            // Create a temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);

            // Select and copy the text
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Show success feedback
                const button = element.nextElementSibling;
                const originalIcon = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check text-success"></i>';

                setTimeout(() => {
                    button.innerHTML = originalIcon;
                }, 2000);

                // Optional: Show toast notification
                showToast('Kopyalandı!', 'success');
            } catch (err) {
                console.error('Kopyalama hatası:', err);
                showToast('Kopyalama başarısız!', 'error');
            }

            // Remove the temporary element
            document.body.removeChild(textarea);
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast element
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

            // Add to body
            document.body.appendChild(toast);

            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Hide and remove toast
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>

    <!-- Order System JavaScript -->
    <script src="assets/js/order-system.js"></script>
    </script>
</body>

</html>