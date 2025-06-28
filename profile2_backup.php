<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahmet Yılmaz - Kişisel QR Profil</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Ahmet Yılmaz - Yazılım Geliştirici ve UI/UX Tasarımcısı. Dijital kartvizit ve iletişim bilgileri.">
    <meta name="keywords" content="Ahmet Yılmaz, yazılım geliştirici, UI/UX, dijital kartvizit, QR kod">
    <meta name="author" content="Ahmet Yılmaz">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Ahmet Yılmaz - Kişisel QR Profil">
    <meta property="og:description" content="Yazılım Geliştirici ve UI/UX Tasarımcısı">
    <meta property="og:type" content="profile">
    <meta property="og:image" content="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Modern CSS Reset & Variables */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Base Colors */
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            
            /* Neutral Colors */
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Typography */
            --font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-family-alt: 'Poppins', 'SF Pro Display', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            
            /* Transitions */
            --transition: all 0.2s ease;
            --transition-slow: all 0.3s ease;
        }

        /* Theme Variations */
        body.theme-clean {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --accent-color: #3b82f6;
            --accent-light: #dbeafe;
        }

        body.theme-dark {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #475569;
            --accent-color: #60a5fa;
            --accent-light: #1e40af;
        }

        body.theme-gradient {
            --bg-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-secondary: rgba(255, 255, 255, 0.1);
            --bg-card: rgba(255, 255, 255, 0.15);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.9);
            --text-muted: rgba(255, 255, 255, 0.7);
            --border-color: rgba(255, 255, 255, 0.2);
            --accent-color: #fbbf24;
            --accent-light: rgba(251, 191, 36, 0.2);
        }

        body.theme-nature {
            --bg-primary: #f0f8f0;
            --bg-secondary: #e8f5e8;
            --bg-card: #ffffff;
            --text-primary: #1a4a1a;
            --text-secondary: #2d5a2d;
            --text-muted: #6b8e6b;
            --border-color: #c3e6c3;
            --accent-color: #22c55e;
            --accent-light: #dcfce7;
        }

        body.theme-sunset {
            --bg-primary: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            --bg-secondary: rgba(255, 255, 255, 0.2);
            --bg-card: rgba(255, 255, 255, 0.25);
            --text-primary: #7c2d12;
            --text-secondary: #9a3412;
            --text-muted: #c2410c;
            --border-color: rgba(255, 255, 255, 0.3);
            --accent-color: #f97316;
            --accent-light: rgba(249, 115, 22, 0.2);
        }

        /* Base Styles */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-family);
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--bg-primary);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Container */
        .profile-container {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: var(--bg-secondary);
            position: relative;
            overflow: hidden;
        }

        /* Background Pattern */
        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 300px;
            background: var(--bg-primary);
            clip-path: polygon(0 0, 100% 0, 100% 70%, 0 100%);
            z-index: 0;
        }

        /* Header Section */
        .profile-header {
            position: relative;
            z-index: 1;
            padding: var(--spacing-3xl) var(--spacing-xl) var(--spacing-xl);
            text-align: center;
        }

        /* Profile Photo */
        .profile-photo-container {
            position: relative;
            display: inline-block;
            margin-bottom: var(--spacing-xl);
        }

        .profile-photo {
            width: 140px;
            height: 140px;
            border-radius: var(--radius-full);
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: var(--shadow-xl);
            transition: var(--transition-slow);
        }

        .profile-photo:hover {
            transform: scale(1.05);
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }

        .profile-photo-ring {
            position: absolute;
            inset: -8px;
            border: 2px solid var(--accent-color);
            border-radius: var(--radius-full);
            opacity: 0;
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        /* Name & Title */
        .profile-name {
            font-family: var(--font-family-alt);
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
            letter-spacing: -0.02em;
        }

        .profile-title {
            font-size: 1.1rem;
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: var(--spacing-lg);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-bio {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: var(--spacing-2xl);
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Main Content */
        .profile-content {
            padding: 0 var(--spacing-xl) var(--spacing-3xl);
            position: relative;
            z-index: 1;
        }

        /* Section Styling */
        .profile-section {
            margin-bottom: var(--spacing-2xl);
        }

        .section-title {
            font-family: var(--font-family-alt);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .section-title i {
            color: var(--accent-color);
            font-size: 1.1rem;
        }

        /* Contact Cards */
        .contact-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .contact-card:hover::before {
            left: 100%;
        }

        .contact-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent-color);
        }

        .contact-card-content {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--white);
            flex-shrink: 0;
        }

        .contact-phone .contact-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .contact-email .contact-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .contact-whatsapp .contact-icon { background: linear-gradient(135deg, #25d366, #128c7e); }

        .contact-details {
            flex: 1;
        }

        .contact-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 2px;
        }

        .contact-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .contact-arrow {
            color: var(--text-muted);
            font-size: 1rem;
            transition: var(--transition);
        }

        .contact-card:hover .contact-arrow {
            transform: translateX(4px);
            color: var(--accent-color);
        }

        /* Social Links Grid */
        .social-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: var(--spacing-md);
        }

        .social-link {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--spacing-sm);
            transition: var(--transition);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .social-link::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--accent-color);
            opacity: 0;
            transition: var(--transition);
            transform: scale(0);
            border-radius: var(--radius-lg);
        }

        .social-link:hover::after {
            opacity: 0.1;
            transform: scale(1);
        }

        .social-link:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent-color);
        }

        .social-icon {
            font-size: 1.5rem;
            color: var(--accent-color);
            transition: var(--transition);
            position: relative;
            z-index: 1;
        }

        .social-link:hover .social-icon {
            transform: scale(1.2);
        }

        .social-label {
            font-size: 0.875rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        /* Info Cards (IBAN & Blood Type) */
        .info-cards {
            display: grid;
            gap: var(--spacing-md);
        }

        .info-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: var(--spacing-lg);
            border: 1px solid var(--border-color);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-color);
            transition: var(--transition);
        }

        .info-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--spacing-sm);
        }

        .info-card-label {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-card-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'Courier New', monospace;
            word-break: break-all;
            position: relative;
            z-index: 1;
        }

        .copy-btn {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            position: relative;
            z-index: 1;
        }

        .copy-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .copy-btn:active {
            transform: scale(0.95);
        }

        /* Blood Type Special Styling */
        .blood-type-card .info-card-value {
            font-size: 1.5rem;
            color: var(--danger);
            font-family: var(--font-family-alt);
        }

        .blood-type-card::before {
            background: var(--danger);
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: var(--spacing-xl);
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--gray-900);
            color: var(--white);
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Theme Switcher */
        .theme-switcher {
            position: fixed;
            top: var(--spacing-xl);
            right: var(--spacing-xl);
            background: var(--bg-card);
            border-radius: var(--radius-full);
            padding: var(--spacing-sm);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            z-index: 100;
        }

        .theme-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin: 2px;
        }

        .theme-btn.clean { background: #3b82f6; color: white; }
        .theme-btn.dark { background: #1e293b; color: white; }
        .theme-btn.gradient { background: linear-gradient(45deg, #667eea, #764ba2); color: white; }
        .theme-btn.nature { background: #22c55e; color: white; }
        .theme-btn.sunset { background: linear-gradient(45deg, #ff9a9e, #fecfef); color: #7c2d12; }

        .theme-btn:hover {
            transform: scale(1.1);
        }

        .theme-btn.active {
            transform: scale(1.2);
            box-shadow: 0 0 0 2px var(--accent-color);
        }

        /* Footer */
        .profile-footer {
            text-align: center;
            padding: var(--spacing-xl);
            color: var(--text-muted);
            font-size: 0.875rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-card);
        }

        .qr-footer-ad {
            background: linear-gradient(135deg, var(--accent-color), var(--primary-dark));
            color: var(--white);
            padding: var(--spacing-lg);
            text-align: center;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .qr-footer-ad:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-color));
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .profile-container {
                max-width: 100%;
            }
            
            .profile-header {
                padding: var(--spacing-2xl) var(--spacing-lg) var(--spacing-lg);
            }
            
            .profile-content {
                padding: 0 var(--spacing-lg) var(--spacing-2xl);
            }
            
            .profile-name {
                font-size: 1.75rem;
            }
            
            .social-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .theme-switcher {
                position: relative;
                top: auto;
                right: auto;
                margin: var(--spacing-lg) auto;
                display: flex;
                justify-content: center;
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

        .profile-header > * {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .profile-header > *:nth-child(1) { animation-delay: 0.1s; }
        .profile-header > *:nth-child(2) { animation-delay: 0.2s; }
        .profile-header > *:nth-child(3) { animation-delay: 0.3s; }
        .profile-header > *:nth-child(4) { animation-delay: 0.4s; }

        .profile-section {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .profile-section:nth-child(1) { animation-delay: 0.5s; }
        .profile-section:nth-child(2) { animation-delay: 0.6s; }
        .profile-section:nth-child(3) { animation-delay: 0.7s; }

        /* Ripple Effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .ripple:active:before {
            width: 300px;
            height: 300px;
        }
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --border-color: rgba(255, 255, 255, 0.2);
            --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 20px;
            --border-radius-sm: 12px;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Theme Variations */
        .theme-dark {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --background-gradient: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            --card-background: rgba(45, 55, 72, 0.95);
            --text-primary: #f7fafc;
            --text-secondary: #e2e8f0;
            --text-muted: #a0aec0;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        .theme-ocean {
            --primary-color: #0077be;
            --secondary-color: #1e40af;
            --background-gradient: linear-gradient(135deg, #0077be 0%, #1e40af 100%);
        }

        .theme-sunset {
            --primary-color: #f093fb;
            --secondary-color: #f5576c;
            --background-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .theme-forest {
            --primary-color: #38a169;
            --secondary-color: #2f855a;
            --background-gradient: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        }

        .theme-minimal {
            --primary-color: #f8f9fa;
            --secondary-color: #e9ecef;
            --background-gradient: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            --card-background: rgba(255, 255, 255, 0.9);
            --text-primary: #212529;
            --text-secondary: #495057;
            --text-muted: #6c757d;
        }

        body {
            font-family: var(--font-family);
            background: var(--background-gradient);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--background-gradient);
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: 50px 50px;
            animation: float 20s infinite linear;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translateY(0px) translateX(0px); }
            33% { transform: translateY(-10px) translateX(5px); }
            66% { transform: translateY(5px) translateX(-5px); }
            100% { transform: translateY(0px) translateX(0px); }
        }

        /* Container */
        .profile-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        /* Profile Card */
        .profile-card {
            background: var(--card-background);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-heavy);
            border: 1px solid var(--border-color);
            overflow: hidden;
            position: relative;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Profile Header */
        .profile-header {
            text-align: center;
            padding: 40px 30px 30px;
            position: relative;
        }

        .profile-photo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-medium);
            transition: transform 0.3s ease;
        }

        .profile-photo:hover {
            transform: scale(1.05);
        }

        .profile-photo-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 30px;
            height: 30px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            box-shadow: var(--shadow-light);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .profile-title {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-weight: 500;
        }

        .profile-bio {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Contact Section */
        .contact-section {
            padding: 0 30px 30px;
        }

        .contact-buttons {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }

        .contact-btn {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .contact-btn:hover::before {
            left: 100%;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            background: rgba(255, 255, 255, 0.15);
        }

        .contact-btn-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
        }

        .contact-btn-content {
            flex: 1;
        }

        .contact-btn-title {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 2px;
        }

        .contact-btn-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .contact-btn-arrow {
            color: var(--text-muted);
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .contact-btn:hover .contact-btn-arrow {
            transform: translateX(5px);
        }

        /* Social Media */
        .social-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
            text-align: center;
        }

        .social-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 15px;
        }

        .social-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
            position: relative;
        }

        .social-link:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
            background: rgba(255, 255, 255, 0.15);
        }

        .social-link i {
            font-size: 20px;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        .social-link span {
            font-size: 11px;
            font-weight: 500;
            text-align: center;
        }

        /* Info Cards */
        .info-section {
            margin-bottom: 20px;
        }

        .info-cards {
            display: grid;
            gap: 15px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .info-card-icon {
            width: 35px;
            height: 35px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 14px;
        }

        .info-card-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-card-content {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
            word-break: break-all;
        }

        .copy-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 10px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }

        .blood-type-card .info-card-content {
            font-size: 24px;
            font-weight: 700;
            font-family: var(--font-family);
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-medium);
            font-size: 14px;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(-10px);
        }

        /* Footer */
        .profile-footer {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 12px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-brand:hover {
            color: var(--primary-color);
        }

        .footer-brand i {
            margin-right: 8px;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                padding: 15px;
            }

            .profile-header {
                padding: 30px 20px 20px;
            }

            .contact-section {
                padding: 0 20px 20px;
            }

            .profile-name {
                font-size: 24px;
            }

            .social-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            color: var(--text-primary);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-medium);
        }

        /* Pulse animation for theme toggle */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .theme-toggle.pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body class="theme-minimal">
    <!-- Theme Toggle Button -->
    <button class="theme-toggle pulse" onclick="toggleTheme()" title="Tema Değiştir">
        <i class="fas fa-palette"></i>
    </button>

    <div class="profile-container">
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-photo-container">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face" 
                         alt="Ahmet Yılmaz profil fotoğrafı" 
                         class="profile-photo">
                    <div class="profile-photo-badge">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
                
                <h1 class="profile-name">Ahmet Yılmaz</h1>
                <p class="profile-title">Yazılım Geliştirici & Teknoloji Uzmanı</p>
                <p class="profile-bio">
                    10+ yıllık deneyim ile modern web teknolojileri ve mobil uygulama geliştirme alanında uzmanlaşmış, 
                    yaratıcı çözümler üreten bir yazılım geliştiricisi.
                </p>
            </div>

            <!-- Contact Section -->
            <div class="contact-section">
                <div class="contact-buttons">
                    <a href="tel:+905551234567" class="contact-btn">
                        <div class="contact-btn-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-btn-content">
                            <span class="contact-btn-title">Telefon ile Ara</span>
                            <span class="contact-btn-subtitle">+90 555 123 45 67</span>
                        </div>
                        <div class="contact-btn-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="mailto:ahmet.yilmaz@example.com" class="contact-btn">
                        <div class="contact-btn-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-btn-content">
                            <span class="contact-btn-title">E-posta Gönder</span>
                            <span class="contact-btn-subtitle">ahmet.yilmaz@example.com</span>
                        </div>
                        <div class="contact-btn-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="https://wa.me/905551234567" class="contact-btn" target="_blank">
                        <div class="contact-btn-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-btn-content">
                            <span class="contact-btn-title">WhatsApp</span>
                            <span class="contact-btn-subtitle">Mesaj Gönder</span>
                        </div>
                        <div class="contact-btn-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Social Media -->
                <div class="social-section">
                    <h3 class="section-title">Sosyal Medya</h3>
                    <div class="social-grid">
                        <a href="https://linkedin.com/in/ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                            <span>LinkedIn</span>
                        </a>
                        <a href="https://github.com/ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-github"></i>
                            <span>GitHub</span>
                        </a>
                        <a href="https://instagram.com/ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="https://x.com/ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="https://youtube.com/@ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </a>
                        <a href="https://behance.net/ahmetyilmaz" class="social-link" target="_blank">
                            <i class="fab fa-behance"></i>
                            <span>Behance</span>
                        </a>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="info-section">
                    <h3 class="section-title">Ek Bilgiler</h3>
                    <div class="info-cards">
                        <!-- IBAN Card -->
                        <div class="info-card iban-card">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="info-card-title">IBAN</div>
                            </div>
                            <div class="info-card-content" id="iban-text">
                                TR33 0006 1005 1978 6457 8413 26
                            </div>
                            <button class="copy-btn" onclick="copyToClipboard('iban-text', 'IBAN kopyalandı!')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>

                        <!-- Blood Type Card -->
                        <div class="info-card blood-type-card">
                            <div class="info-card-header">
                                <div class="info-card-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div class="info-card-title">Kan Grubu</div>
                            </div>
                            <div class="info-card-content" style="color: #e53e3e;">
                                A RH+
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="profile-footer">
                <a href="#" class="footer-brand">
                    <i class="fas fa-qrcode"></i>
                    Kişisel QR ile oluşturuldu
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        // Theme data
        const themes = [
            { name: 'minimal', label: 'Minimal' },
            { name: 'dark', label: 'Karanlık' },
            { name: 'ocean', label: 'Okyanus' },
            { name: 'sunset', label: 'Günbatımı' },
            { name: 'forest', label: 'Orman' }
        ];

        let currentThemeIndex = 0;

        // Theme toggle function
        function toggleTheme() {
            currentThemeIndex = (currentThemeIndex + 1) % themes.length;
            const newTheme = themes[currentThemeIndex];
            
            // Update body class
            document.body.className = `theme-${newTheme.name}`;
            
            // Show toast
            showToast(`Tema: ${newTheme.label}`);
            
            // Add pulse effect to theme toggle
            const toggleBtn = document.querySelector('.theme-toggle');
            toggleBtn.classList.remove('pulse');
            setTimeout(() => toggleBtn.classList.add('pulse'), 100);
        }

        // Copy to clipboard function
        function copyToClipboard(elementId, message) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            // Create temporary textarea
            const textarea = document.createElement('textarea');
            textarea.value = text.replace(/\s/g, ''); // Remove spaces for IBAN
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Show toast
            showToast(message);
        }

        // Show toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add entrance animation to cards
            const cards = document.querySelectorAll('.info-card, .contact-btn, .social-link');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });

            // Add floating animation to theme toggle
            setTimeout(() => {
                document.querySelector('.theme-toggle').classList.add('pulse');
            }, 2000);
        });

        // Smooth scrolling for better UX
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

        // Add ripple effect to buttons
        function addRippleEffect(e) {
            const button = e.currentTarget;
            const ripple = document.createElement('span');
            
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Add ripple style
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Add ripple effect to interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.contact-btn, .social-link, .copy-btn').forEach(button => {
                button.style.position = 'relative';
                button.style.overflow = 'hidden';
                button.addEventListener('click', addRippleEffect);
            });
        });
    </script>
</body>

</html>
