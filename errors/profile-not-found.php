<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Bulunamadı - Kişisel QR</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Aradığınız profil bulunamadı. Yeni bir QR profil oluşturmak için hemen başlayın!">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            max-width: 600px;
            text-align: center;
            padding: 2rem;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-icon {
            font-size: 5rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .btn-custom {
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
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
            transform: translateY(-2px);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            text-align: center;
            padding: 1rem;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .feature-text {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 500;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 70%;
            right: 10%;
            width: 120px;
            height: 120px;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 10%;
            left: 15%;
            width: 60px;
            height: 60px;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @media (max-width: 768px) {
            .error-card {
                padding: 2rem 1rem;
                margin: 1rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-custom {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="error-card">
            <div class="error-icon pulse">
                <i class="fas fa-user-slash"></i>
            </div>
            
            <h1 class="error-title">Profil Bulunamadı</h1>
            
            <p class="error-message">
                Aradığınız QR profili mevcut değil veya kaldırılmış olabilir. 
                <br>Merak etmeyin, hemen yeni bir profil oluşturabilirsiniz!
            </p>

            <div class="action-buttons">
                <a href="../index.php" class="btn-custom btn-primary-custom">
                    <i class="fas fa-plus me-2"></i>Yeni Profil Oluştur
                </a>
                <a href="../index.php#demo" class="btn-custom btn-outline-custom">
                    <i class="fas fa-play me-2"></i>Demo'yu İzle
                </a>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="feature-text">QR Kod</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="feature-text">12+ Tema</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <div class="feature-text">Sosyal Medya</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="feature-text">Mobil Uyumlu</div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sayfa yüklenince animasyon efekti
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.error-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.8s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });

        // Butona tıklandığında analitik
        document.querySelectorAll('.btn-custom').forEach(btn => {
            btn.addEventListener('click', function() {
                // Google Analytics veya başka tracking için
                console.log('404 Profile Error - Button clicked:', this.textContent.trim());
            });
        });
    </script>
</body>
</html>
