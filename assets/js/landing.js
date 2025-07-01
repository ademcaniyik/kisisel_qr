// Glassmorphism Navbar scroll efekti
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.glassmorphism-header');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });
    
    // Smooth scroll
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

    // Mobile menu auto-close
    const navLinks = document.querySelectorAll('.glassmorphism-header .nav-link');
    const navbarCollapse = document.querySelector('.glassmorphism-header .navbar-collapse');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse?.classList.contains('show')) {
                const navbarToggler = document.querySelector('.glassmorphism-header .navbar-toggler');
                navbarToggler?.click();
            }
        });
    });
});
