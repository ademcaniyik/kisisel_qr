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
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--secondary-color);
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,0 1000,300 1000,1000 0,700"/></svg>');
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero .lead {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--primary-color);
        }
        
        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.8);
        }
        
        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        /* Features Section */
        .features {
            padding: 100px 0;
            background: white;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary-color);
        }
        
        .feature-card p {
            color: #6c757d;
            line-height: 1.6;
        }
        
        /* Statistics Section */
        .stats {
            padding: 80px 0;
            background: var(--light-bg);
        }
        
        .stat-item {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            display: block;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        /* Demo Section */
        .demo {
            padding: 100px 0;
            background: white;
        }
        
        .demo-mockup {
            max-width: 400px;
            margin: 0 auto;
            position: relative;
        }
        
        .phone-mockup {
            background: var(--secondary-color);
            border-radius: 30px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .phone-screen {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 9/19.5;
        }
        
        /* Testimonials */
        .testimonials {
            padding: 100px 0;
            background: var(--gradient-2);
            color: white;
        }
        
        .testimonial-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .testimonial-text {
            font-size: 1.2rem;
            font-style: italic;
            margin-bottom: 20px;
        }
        
        .testimonial-author {
            font-weight: 600;
        }
        
        /* Pricing */
        .pricing {
            padding: 100px 0;
            background: white;
        }
        
        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .pricing-card.featured {
            border-color: var(--primary-color);
            transform: scale(1.05);
        }
        
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
        }
        
        .price-period {
            color: #6c757d;
            font-size: 1rem;
        }
        
        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: var(--gradient-3);
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta .lead {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer h5 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .footer a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero .lead {
                font-size: 1.1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .features {
                padding: 60px 0;
            }
            
            .feature-card {
                margin-bottom: 30px;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-on-scroll {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Order Modal */
        .order-step {
            min-height: 400px;
        }
        
        .payment-option {
            position: relative;
        }
        
        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .payment-option label {
            cursor: pointer;
            display: block;
            transition: all 0.3s ease;
        }
        
        .payment-option label:hover .card {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .payment-option input:checked + label .card {
            border-color: #0d6efd !important;
            background-color: #f8f9ff;
        }
        
        .payment-info {
            transition: all 0.3s ease;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            background: var(--gradient-1);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .toast-notification.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .toast-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .toast-error {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
        }
        
        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toast-content i {
            font-size: 1.2rem;
        }

        /* Theme Preview Styles */
        .theme-preview-container {
            position: relative;
        }
        
        .theme-preview {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            background: white;
            transition: all 0.3s ease;
            min-height: 200px;
        }
        
        .preview-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .preview-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }
        
        .preview-info h6 {
            margin: 0;
            font-weight: 600;
        }
        
        .preview-info small {
            opacity: 0.8;
        }
        
        .preview-social {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .preview-social-btn {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .preview-social-btn i {
            margin-right: 8px;
            width: 20px;
        }

        /* Theme-specific styles */
        .theme-preview.theme-default {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2c3e50;
        }
        .theme-preview.theme-default .preview-avatar {
            background: #3498db;
            color: white;
        }
        .theme-preview.theme-default .preview-social-btn {
            background: #3498db;
            color: white;
        }

        .theme-preview.theme-blue {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        .theme-preview.theme-blue .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-blue .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-nature {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
        }
        .theme-preview.theme-nature .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-nature .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-elegant {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
        }
        .theme-preview.theme-elegant .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-elegant .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-gold {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }
        .theme-preview.theme-gold .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-gold .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-purple {
            background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
            color: white;
        }
        .theme-preview.theme-purple .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-purple .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        .theme-preview.theme-dark .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-dark .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-ocean {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
        }
        .theme-preview.theme-ocean .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-ocean .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-minimal {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .theme-preview.theme-minimal .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-minimal .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-pastel {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            color: #2d3436;
        }
        .theme-preview.theme-pastel .preview-avatar {
            background: rgba(45,52,54,0.1);
            color: #2d3436;
        }
        .theme-preview.theme-pastel .preview-social-btn {
            background: rgba(45,52,54,0.1);
            color: #2d3436;
        }

        .theme-preview.theme-retro {
            background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
            color: white;
        }
        .theme-preview.theme-retro .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-retro .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .theme-preview.theme-neon {
            background: linear-gradient(135deg, #00cec9 0%, #55a3ff 100%);
            color: white;
        }
        .theme-preview.theme-neon .preview-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .theme-preview.theme-neon .preview-social-btn {
            background: rgba(255,255,255,0.2);
            color: white;
        }
    </style>
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
                        <a href="https://acdisoftware.com.tr/kisisel_qr/qr/2b536102" target="_blank" class="btn-hero btn-primary-hero">
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
                    <a href="https://acdisoftware.com.tr/kisisel_qr/qr/2b536102" target="_blank" class="btn btn-primary btn-lg me-3">
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
                                        <input type="tel" class="form-control" id="customerPhone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customerAddress" class="form-label">Teslimat Adresi *</label>
                                <textarea class="form-control" id="customerAddress" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="customerPhoto" class="form-label">Profil Fotoğrafı (isteğe bağlı)</label>
                                <input type="file" class="form-control" id="customerPhoto" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="customerBio" class="form-label">Kısa Bio</label>
                                <textarea class="form-control" id="customerBio" rows="2" placeholder="Kendizi tanıtın..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sosyal Medya Hesapları</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="url" class="form-control mb-2" id="instagram" placeholder="Instagram URL">
                                        <input type="url" class="form-control mb-2" id="twitter" placeholder="Twitter URL">
                                        <input type="url" class="form-control mb-2" id="linkedin" placeholder="LinkedIn URL">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="url" class="form-control mb-2" id="facebook" placeholder="Facebook URL">
                                        <input type="url" class="form-control mb-2" id="youtube" placeholder="YouTube URL">
                                        <input type="url" class="form-control mb-2" id="website" placeholder="Website URL">
                                    </div>
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
                                                        <span>Twitter</span>
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
                        <h6>2. Ödeme Yöntemi</h6>
                        <div class="card mb-3">
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
                        <li><a href="#">Hakkımızda</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Kariyer</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6>Yasal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Gizlilik Politikası</a></li>
                        <li><a href="#">Kullanım Şartları</a></li>
                        <li><a href="#">KVKK</a></li>
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

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = 'none';
            }
        });

        // Counter animation
        function animateCounters() {
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
        }

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
                        animateCounters();
                    }
                }
            });
        }, observerOptions);

        // Observe sections for animation
        document.querySelectorAll('section, .feature-card, .testimonial-card, .pricing-card').forEach(el => {
            observer.observe(el);
        });

        // WhatsApp button click tracking and fallback
        document.addEventListener('DOMContentLoaded', function() {
            // WhatsApp buttons event listeners
            document.querySelectorAll('[href*="wa.me"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Analytics tracking can be added here
                    console.log('WhatsApp button clicked:', this.href);
                });
            });
            
            // Special handling for order WhatsApp link
            const whatsappLink = document.getElementById('whatsappLink');
            if (whatsappLink) {
                whatsappLink.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default link behavior
                    generateWhatsAppMessage();
                });
            }
        });

        // Form submission handling (if needed)
        function handleFormSubmit(event) {
            event.preventDefault();
            // Handle form submission
            alert('Mesajınız alındı! En kısa sürede dönüş yapacağız.');
        }

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
            try {
                // Collect form data
                const customerName = document.getElementById('customerName').value;
                const customerPhone = document.getElementById('customerPhone').value;
                const customerAddress = document.getElementById('customerAddress').value;
                const customerBio = document.getElementById('customerBio').value;
                const customerTheme = document.getElementById('customerTheme').value;
                const customerIban = document.getElementById('customerIban').value;
                const customerBloodType = document.getElementById('customerBloodType').value;
                const themeText = document.getElementById('customerTheme').options[document.getElementById('customerTheme').selectedIndex].text;
                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
                
                // Collect social media links
                const socialMedia = [];
                const platforms = ['instagram', 'twitter', 'linkedin', 'facebook', 'youtube', 'website'];
                platforms.forEach(platform => {
                    const url = document.getElementById(platform).value;
                    if (url) socialMedia.push(`${platform.charAt(0).toUpperCase() + platform.slice(1)}: ${url}`);
                });
                
                // Create special requests text
                let specialRequests = '';
                if (customerAddress) specialRequests += `Adres: ${customerAddress}\n`;
                if (customerBio) specialRequests += `Bio: ${customerBio}\n`;
                if (customerIban) specialRequests += `İban: ${customerIban}\n`;
                if (customerBloodType) specialRequests += `Kan Grubu: ${customerBloodType}\n`;
                if (socialMedia.length > 0) {
                    specialRequests += `Sosyal Medya:\n${socialMedia.join('\n')}\n`;
                }
                specialRequests += `Ödeme Yöntemi: ${paymentMethod === 'bank_transfer' ? 'Banka Havalesi' : 'Kapıda Ödeme'}\n`;
                specialRequests += `Tema: ${themeText}`;
                
                // Prepare order data
                const orderData = {
                    customer_name: customerName,
                    customer_phone: customerPhone,
                    customer_email: '', // Email alanı yoksa boş bırak
                    product_type: 'personal_qr',
                    product_name: '10x10 cm Şeffaf QR Sticker',
                    quantity: 1,
                    price: 200.00,
                    payment_method: paymentMethod,
                    special_requests: specialRequests,
                    whatsapp_sent: true
                };
                
                // Save order to database
                const response = await fetch('admin/api/orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    console.log('Sipariş başarıyla kaydedildi:', result.order_id);
                    
                    // Profil linki varsa ayarla
                    if (result.profile && result.profile.profile_url) {
                        const profileLink = document.getElementById('profileLink');
                        // Tam URL oluştur
                        const fullUrl = result.profile.profile_url.startsWith('http') 
                            ? result.profile.profile_url 
                            : window.location.origin + window.location.pathname.replace('/index.php', '').replace(/\/$/, '') + '/' + result.profile.profile_url;
                        
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
                    message += `📍 *Adres:* ${customerAddress}\n`;
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
                }
                
            } catch (error) {
                console.error('Sipariş işlemi sırasında hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        }

        function generateWhatsAppMessage() {
            // Form data kontrolü
            const customerName = document.getElementById('customerName')?.value || '';
            const customerPhone = document.getElementById('customerPhone')?.value || '';
            const customerAddress = document.getElementById('customerAddress')?.value || '';
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
            const customerTheme = document.getElementById('customerTheme')?.value || 'default';
            const themeSelect = document.getElementById('customerTheme');
            const themeText = themeSelect ? themeSelect.options[themeSelect.selectedIndex].text : 'Varsayılan';
            
            // Sosyal medya linklerini topla
            const socialMedia = [];
            const platforms = ['instagram', 'twitter', 'linkedin', 'facebook', 'youtube', 'website'];
            platforms.forEach(platform => {
                const element = document.getElementById(platform);
                if (element && element.value) {
                    socialMedia.push(`${platform.charAt(0).toUpperCase() + platform.slice(1)}: ${element.value}`);
                }
            });
            
            // WhatsApp mesajını oluştur
            let message = `🏷️ *QR Sticker Siparişi*\n\n`;
            message += `👤 *Ad Soyad:* ${customerName}\n`;
            message += `📱 *Telefon:* ${customerPhone}\n`;
            message += `📍 *Adres:* ${customerAddress}\n`;
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
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
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

        // Initialize theme preview when modal opens
        document.getElementById('orderModal').addEventListener('shown.bs.modal', function () {
            updateThemePreview();
        });
    </script>
</body>
</html>
