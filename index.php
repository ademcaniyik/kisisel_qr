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
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: var(--primary-color);
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.8);
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .payment-option input:checked+label .card {
            border-color: #0d6efd !important;
            background-color: #f8f9ff;
        }

        .payment-info {
            transition: all 0.3s ease;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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

        .toast-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
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
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-blue .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-nature {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
        }

        .theme-preview.theme-nature .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-nature .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-elegant {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
        }

        .theme-preview.theme-elegant .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-elegant .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-gold {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        .theme-preview.theme-gold .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-gold .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-purple {
            background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
            color: white;
        }

        .theme-preview.theme-purple .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-purple .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }

        .theme-preview.theme-dark .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-dark .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-ocean {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
        }

        .theme-preview.theme-ocean .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-ocean .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-minimal {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .theme-preview.theme-minimal .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-minimal .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-pastel {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            color: #2d3436;
        }

        .theme-preview.theme-pastel .preview-avatar {
            background: rgba(45, 52, 54, 0.1);
            color: #2d3436;
        }

        .theme-preview.theme-pastel .preview-social-btn {
            background: rgba(45, 52, 54, 0.1);
            color: #2d3436;
        }

        .theme-preview.theme-retro {
            background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
            color: white;
        }

        .theme-preview.theme-retro .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-retro .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-neon {
            background: linear-gradient(135deg, #00cec9 0%, #55a3ff 100%);
            color: white;
        }

        .theme-preview.theme-neon .preview-avatar {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .theme-preview.theme-neon .preview-social-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Dynamic Social Media Styles */
        .social-platforms-grid .social-platform-btn {
            border: 2px solid #e9ecef;
            padding: 15px 10px;
            transition: all 0.3s ease;
            background: white;
            border-radius: 12px;
            text-decoration: none;
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .social-platform-btn:hover {
            transform: translateY(-2px);
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
        }

        .social-platform-btn.selected {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white !important;
        }

        .social-platform-btn.selected i {
            color: white !important;
        }

        .social-platform-btn i {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .social-platform-btn span {
            font-weight: 500;
            font-size: 0.75rem;
        }

        .selected-social-medias .social-media-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .selected-social-medias .social-media-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(52, 152, 219, 0.1);
        }

        .social-media-item .platform-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .social-media-item .platform-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .social-media-item .platform-name {
            font-weight: 600;
            color: var(--secondary-color);
            flex: 1;
        }

        .social-media-item .remove-btn {
            border: none;
            background: #dc3545;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .social-media-item .remove-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .social-media-item .input-group {
            gap: 8px;
        }

        .social-media-item .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .social-media-item .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        }

        .username-prefix {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 10px 12px;
            color: #6c757d;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .url-preview {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #6c757d;
            word-break: break-all;
        }

        /* Platform specific colors */
        .platform-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); }
        .platform-x { 
            background: #1da1f2; 
            color: #ffffff;
        }
        
        .platform-x:hover { 
            background: #0d8bd9; 
        }

        /* X Platform Icon Color */
        .platform-x i {
            color: #ffffff !important;
        }

        /* X Platform Button Icon Color */
        .social-platform-btn[data-platform="x"] i {
            color: #1da1f2 !important;
        }
        .platform-linkedin { background: #0077b5; }
        .platform-facebook { background: #1877f2; }
        .platform-youtube { background: #ff0000; }
        .platform-tiktok { background: #000000; }
        .platform-whatsapp { background: #25d366; }
        .platform-website { background: #17a2b8; }
        .platform-snapchat { background: #fffc00; color: #000 !important; }
        .platform-discord { background: #5865f2; }
        .platform-telegram { background: #0088cc; }
        .platform-twitch { background: #9146ff; }

        .text-purple {
            color: #9146ff !important;
        }

        @media (max-width: 768px) {
            .social-platforms-grid .col-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .social-platform-btn {
                height: 70px;
                padding: 10px 5px;
            }
            
            .social-platform-btn i {
                font-size: 1.2rem;
            }
            
            .social-platform-btn span {
                font-size: 0.7rem;
            }
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
                                        <div class="input-group">
                                            <select class="form-select" id="customerCountryCode" style="max-width: 100px;">
                                                <option value="+90" data-max="10">🇹🇷 +90</option>
                                                <option value="+1" data-max="10">🇺🇸 +1</option>
                                                <option value="+44" data-max="10">🇬🇧 +44</option>
                                                <option value="+49" data-max="11">🇩🇪 +49</option>
                                                <option value="+33" data-max="9">🇫🇷 +33</option>
                                                <option value="+39" data-max="10">🇮🇹 +39</option>
                                                <option value="+34" data-max="9">🇪🇸 +34</option>
                                                <option value="+31" data-max="9">🇳🇱 +31</option>
                                                <option value="+32" data-max="9">🇧🇪 +32</option>
                                                <option value="+41" data-max="9">🇨🇭 +41</option>
                                                <option value="+43" data-max="11">🇦🇹 +43</option>
                                                <option value="+45" data-max="8">🇩🇰 +45</option>
                                                <option value="+46" data-max="9">🇸🇪 +46</option>
                                                <option value="+47" data-max="8">🇳🇴 +47</option>
                                                <option value="+7" data-max="10">🇷🇺 +7</option>
                                                <option value="+86" data-max="11">🇨🇳 +86</option>
                                                <option value="+81" data-max="11">🇯🇵 +81</option>
                                                <option value="+82" data-max="11">🇰🇷 +82</option>
                                                <option value="+91" data-max="10">🇮🇳 +91</option>
                                                <option value="+55" data-max="11">🇧🇷 +55</option>
                                                <option value="+52" data-max="10">🇲🇽 +52</option>
                                                <option value="+61" data-max="9">🇦🇺 +61</option>
                                                <option value="+64" data-max="9">🇳🇿 +64</option>
                                                <option value="+27" data-max="9">🇿🇦 +27</option>
                                                <option value="+971" data-max="9">🇦🇪 +971</option>
                                                <option value="+966" data-max="9">🇸🇦 +966</option>
                                            </select>
                                            <input type="tel" class="form-control" id="customerPhone" required placeholder="534 334 34 34" maxlength="11" oninput="formatPhoneNumberWithCountry(this)">
                                        </div>
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
                                        <div class="input-group">
                                            <select class="form-select" id="deliveryCountryCode" style="max-width: 100px;">
                                                <option value="+90" data-max="10">🇹🇷 +90</option>
                                                <option value="+1" data-max="10">🇺🇸 +1</option>
                                                <option value="+44" data-max="10">🇬🇧 +44</option>
                                                <option value="+49" data-max="11">🇩🇪 +49</option>
                                                <option value="+33" data-max="9">🇫🇷 +33</option>
                                                <option value="+39" data-max="10">🇮🇹 +39</option>
                                                <option value="+34" data-max="9">🇪🇸 +34</option>
                                                <option value="+31" data-max="9">🇳🇱 +31</option>
                                                <option value="+32" data-max="9">🇧🇪 +32</option>
                                                <option value="+41" data-max="9">🇨🇭 +41</option>
                                                <option value="+43" data-max="11">🇦🇹 +43</option>
                                                <option value="+45" data-max="8">🇩🇰 +45</option>
                                                <option value="+46" data-max="9">🇸🇪 +46</option>
                                                <option value="+47" data-max="8">🇳🇴 +47</option>
                                                <option value="+7" data-max="10">🇷🇺 +7</option>
                                                <option value="+86" data-max="11">🇨🇳 +86</option>
                                                <option value="+81" data-max="11">🇯🇵 +81</option>
                                                <option value="+82" data-max="11">🇰🇷 +82</option>
                                                <option value="+91" data-max="10">🇮🇳 +91</option>
                                                <option value="+55" data-max="11">🇧🇷 +55</option>
                                                <option value="+52" data-max="10">🇲🇽 +52</option>
                                                <option value="+61" data-max="9">🇦🇺 +61</option>
                                                <option value="+64" data-max="9">🇳🇿 +64</option>
                                                <option value="+27" data-max="9">🇿🇦 +27</option>
                                                <option value="+971" data-max="9">🇦🇪 +971</option>
                                                <option value="+966" data-max="9">🇸🇦 +966</option>
                                            </select>
                                            <input type="tel" class="form-control" id="deliveryPhone" required 
                                                   placeholder="534 933 46 31" maxlength="11" 
                                                   oninput="formatPhoneNumberWithCountry(this)">
                                        </div>
                                        <small class="form-text text-muted">Kargo teslim için iletişim</small>
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
                    message += `\n� *Teslimat Bilgileri:*\n`;
                    message += `�📍 *Adres:* ${fullAddress}\n`;
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
                message += `\n� *Teslimat Bilgileri:*\n`;
                message += `�📍 *Adres:* ${fullAddress}\n`;
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

        // Formatted phone number getter function
        function getFormattedPhoneNumber(type) {
            let countrySelect, phoneInput;
            
            if (type === 'customer') {
                countrySelect = document.getElementById('customerCountryCode');
                phoneInput = document.getElementById('customerPhone');
            } else if (type === 'delivery') {
                countrySelect = document.getElementById('deliveryCountryCode');
                phoneInput = document.getElementById('deliveryPhone');
            }
            
            if (!countrySelect || !phoneInput) return '';
            
            const countryCode = countrySelect.value;
            const phoneNumber = phoneInput.value.replace(/\D/g, ''); // Sadece rakamlar
            
            return phoneNumber ? countryCode + phoneNumber : '';
        }

        // Social Media Management Functions
        const socialMediaPlatforms = {
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

        // WhatsApp seçildiğinde telefon inputlarını sıfırla ve +90 seç
        function resetPhoneInputsForWhatsApp() {
            // Müşteri telefonu
            const customerCountryCode = document.getElementById('customerCountryCode');
            const customerPhone = document.getElementById('customerPhone');
            
            if (customerCountryCode && customerPhone) {
                customerCountryCode.value = '+90';
                updatePhoneInputByCountry('customer'); // Bu fonksiyon input'u sıfırlar ve placeholder'ı günceller
            }
            
            // Teslimat telefonu (eğer varsa)
            const deliveryCountryCode = document.getElementById('deliveryCountryCode');
            const deliveryPhone = document.getElementById('deliveryPhone');
            
            if (deliveryCountryCode && deliveryPhone) {
                deliveryCountryCode.value = '+90';
                updatePhoneInputByCountry('delivery'); // Bu fonksiyon input'u sıfırlar ve placeholder'ı günceller
            }
            
            showToast('WhatsApp için telefon alanları +90 Türkiye olarak ayarlandı!', 'success');
        }

        // Phone number formatting function with country code
        function formatPhoneNumberWithCountry(input) {
            let value = input.value;
            
            // Sadece rakamları al
            let numbers = value.replace(/\D/g, '');
            
            // İlgili country code dropdown'unu bul
            let countrySelect;
            if (input.id === 'customerPhone') {
                countrySelect = document.getElementById('customerCountryCode');
            } else if (input.id === 'deliveryPhone') {
                countrySelect = document.getElementById('deliveryCountryCode');
            }
            
            if (!countrySelect) return;
            
            // Seçilen ülkenin maksimum rakam sayısını al
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const maxDigits = parseInt(selectedOption.getAttribute('data-max')) || 10;
            
            // Maksimum rakam sayısını aşmasın
            if (numbers.length > maxDigits) {
                numbers = numbers.substring(0, maxDigits);
            }
            
            // Türkiye için özel formatlama: XXX XXX XX XX (ülke kodu dropdown'da)
            if (countrySelect.value === '+90' && numbers.length > 0) {
                let formatted = '';
                if (numbers.length > 0) {
                    formatted += numbers.substring(0, 3);
                    if (numbers.length > 3) {
                        formatted += ' ' + numbers.substring(3, 6);
                        if (numbers.length > 6) {
                            formatted += ' ' + numbers.substring(6, 8);
                            if (numbers.length > 8) {
                                formatted += ' ' + numbers.substring(8, 10);
                            }
                        }
                    }
                }
                input.value = formatted;
            } else {
                // Diğer ülkeler için basit formatlama
                input.value = numbers;
            }
        }

        // Phone input focus event - modern country code system
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize social media handlers
            initSocialMediaHandlers();
            
            // Initialize country code change handlers
            initCountryCodeHandlers();
            
            // Remove old +90 prefix system - now using dropdown only
            // Initialize default country code to +90 on page load
            const customerCountryCode = document.getElementById('customerCountryCode');
            const deliveryCountryCode = document.getElementById('deliveryCountryCode');
            
            if (customerCountryCode) {
                customerCountryCode.value = '+90';
                updatePhoneInputByCountry('customer');
            }
            
            // Handle delivery phone input (will be available after step 2)
            setTimeout(() => {
                if (deliveryCountryCode) {
                    deliveryCountryCode.value = '+90';
                    updatePhoneInputByCountry('delivery');
                }
            }, 1000);
        });

        // Country code change handlers
        function initCountryCodeHandlers() {
            // Customer country code handler
            const customerCountryCode = document.getElementById('customerCountryCode');
            if (customerCountryCode) {
                customerCountryCode.addEventListener('change', function() {
                    updatePhoneInputByCountry('customer');
                });
            }
            
            // Delivery country code handler (will be available after step 2)
            setTimeout(() => {
                const deliveryCountryCode = document.getElementById('deliveryCountryCode');
                if (deliveryCountryCode) {
                    deliveryCountryCode.addEventListener('change', function() {
                        updatePhoneInputByCountry('delivery');
                    });
                }
            }, 1000);
        }

        // Update phone input based on selected country
        function updatePhoneInputByCountry(type) {
            let countrySelect, phoneInput;
            
            if (type === 'customer') {
                countrySelect = document.getElementById('customerCountryCode');
                phoneInput = document.getElementById('customerPhone');
            } else if (type === 'delivery') {
                countrySelect = document.getElementById('deliveryCountryCode');
                phoneInput = document.getElementById('deliveryPhone');
            }
            
            if (!countrySelect || !phoneInput) return;
            
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const maxDigits = parseInt(selectedOption.getAttribute('data-max')) || 10;
            const countryCode = countrySelect.value;
            
            // Update maxlength and clear input
            phoneInput.maxLength = maxDigits;
            phoneInput.value = '';
            
            // Update placeholder based on country
            if (countryCode === '+90') {
                phoneInput.placeholder = '534 933 46 31';
            } else if (countryCode === '+1') {
                phoneInput.placeholder = '555 123 4567';
            } else if (countryCode === '+44') {
                phoneInput.placeholder = '20 7946 0958';
            } else if (countryCode === '+49') {
                phoneInput.placeholder = '30 12345678';
            } else if (countryCode === '+33') {
                phoneInput.placeholder = '1 23 45 67 89';
            } else {
                phoneInput.placeholder = 'Telefon numarası';
            }
            
            // Show toast notification
            const countryName = selectedOption.textContent.split(' ')[0];
            showToast(`Ülke ${countryName} ${countryCode} olarak ayarlandı`, 'info');
        }

        // Initialize theme preview when modal opens
        document.getElementById('orderModal').addEventListener('shown.bs.modal', function() {
            updateThemePreview();
        });
    </script>
</body>

</html>