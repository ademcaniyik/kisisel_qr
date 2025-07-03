<?php
// filepath: c:\xampp\htdocs\kisisel_qr_canli\errors\profile-not-found.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Bulunamadı - Kişisel QR</title>
    <meta name="description" content="Aradığınız QR profili bulunamadı. Kişisel QR ile yeni bir profil oluşturun.">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 120px; height: 120px; left: 35%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 100px; height: 100px; left: 60%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 40px; height: 40px; left: 80%; animation-delay: 3s; }
        .particle:nth-child(6) { width: 70px; height: 70px; left: 90%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            33% { transform: translateY(-30px) rotate(120deg); opacity: 1; }
            66% { transform: translateY(-20px) rotate(240deg); opacity: 0.8; }
        }

        /* Main Container */
        .error-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 60px 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 100%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* QR Code Animation */
        .qr-icon {
            font-size: 120px;
            color: #667eea;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
            position: relative;
        }

        .qr-icon::after {
            content: '❌';
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 40px;
            color: #ff6b6b;
            animation: bounce 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Typography */
        .error-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .error-subtitle {
            font-size: 1.3rem;
            color: #7f8c8d;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .error-description {
            font-size: 1.1rem;
            color: #5a6c7d;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .btn-custom {
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: 2px solid transparent;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        }

        /* Features List */
        .features-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .features-list li {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: #5a6c7d;
        }

        .features-list li i {
            color: #4ecdc4;
            font-size: 1.2rem;
        }

        /* Search Box */
        .search-container {
            margin: 30px 0;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 50px;
            font-size: 1.1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-card {
                padding: 40px 30px;
                margin: 20px;
            }

            .error-title {
                font-size: 2rem;
            }

            .error-subtitle {
                font-size: 1.1rem;
            }

            .qr-icon {
                font-size: 80px;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }

        /* WhatsApp Widget */
        .whatsapp-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366, #128c7e);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
            animation: whatsapp-pulse 2s infinite;
        }

        .whatsapp-btn:hover {
            transform: scale(1.1);
            color: white;
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.5);
        }

        @keyframes whatsapp-pulse {
            0% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3); }
            50% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.6); }
            100% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3); }
        }
    </style>
</head>
<body>
    <!-- Animated Background Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Main Content -->
    <div class="error-container">
        <div class="error-card">
            <!-- QR Icon with Error -->
            <div class="qr-icon">
                <i class="fas fa-qrcode"></i>
            </div>

            <!-- Error Title & Description -->
            <h1 class="error-title">Profil Bulunamadı</h1>
            <h2 class="error-subtitle">🔍 Bu QR kod geçerli değil</h2>
            <p class="error-description">
                Aradığınız QR profili bulunamadı veya kaldırılmış olabilir. 
                Belki yanlış QR kodu taratmış olabilirsiniz?
            </p>

            <!-- Action Buttons -->
            <div class="btn-container">
                <a href="../index.php" class="btn-custom btn-primary-custom">
                    <i class="fas fa-home"></i>
                    Ana Sayfaya Dön
                </a>
                <a href="../index.php#pricing" class="btn-custom btn-outline-custom">
                    <i class="fas fa-plus"></i>
                    Yeni Profil Oluştur
                </a>
            </div>

            <!-- Features -->
            <ul class="features-list">
                <li>
                    <i class="fas fa-shield-alt"></i>
                    <span>Güvenli QR kod sistemi</span>
                </li>
                <li>
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobil uyumlu profiller</span>
                </li>
                <li>
                    <i class="fas fa-palette"></i>
                    <span>12+ özelleştirilebilir tema</span>
                </li>
                <li>
                    <i class="fas fa-chart-line"></i>
                    <span>Detaylı istatistikler</span>
                </li>
            </ul>

            <!-- Contact Info -->
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-question-circle me-1"></i>
                    Sorun yaşıyorsanız bizimle iletişime geçin: 
                    <strong>+90 534 933 46 31</strong>
                </small>
            </div>
        </div>
    </div>

    <!-- WhatsApp Widget -->
    <div class="whatsapp-widget">
        <a href="https://wa.me/905349334631?text=Merhaba, QR profil konusunda yardım istiyorum." 
           target="_blank" class="whatsapp-btn" title="WhatsApp Destek">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search Profile Function
        function searchProfile() {
            const searchValue = document.getElementById('searchInput').value.trim();
            if (searchValue) {
                // Redirect to profile search
                window.location.href = `../qr/${encodeURIComponent(searchValue)}`;
            } else {
                alert('Lütfen aranacak profil adını girin.');
            }
        }

        // Enter key search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProfile();
            }
        });

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate card entrance
            const card = document.querySelector('.error-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 200);

            // Add hover effects to particles
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                particle.addEventListener('mouseenter', function() {
                    this.style.transform = `scale(1.2) translateY(-20px) rotate(${index * 60}deg)`;
                });
                
                particle.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });
        });

        // Auto-focus search input
        setTimeout(() => {
            document.getElementById('searchInput').focus();
        }, 1000);
    </script>
</body>
</html>