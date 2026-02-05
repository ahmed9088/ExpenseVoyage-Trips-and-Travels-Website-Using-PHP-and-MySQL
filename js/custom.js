/**
 * ExpenseVoyage - Elegant Light UI Interactions
 * Refined for speed, simplicity, and an advanced feel.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Scroll Progress Bar
    const progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressBar.style.width = scrolled + "%";
    });

    // 2. Navbar Refinement
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
            navbar.style.padding = '10px 0';
        } else {
            navbar.style.background = 'rgba(248, 250, 252, 0.8)';
            navbar.style.boxShadow = 'none';
            navbar.style.padding = '15px 0';
        }
    });

    // 3. Sophisticated Reveal on Scroll
    const revealElements = document.querySelectorAll('.animate-on-scroll');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.visibility = 'visible';
                entry.target.classList.add('animate__animated', entry.target.dataset.animation || 'animate__fadeInUp');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => {
        el.style.visibility = 'hidden'; // Hide initially
        revealObserver.observe(el);
    });

    // 4. Subtle Parallax Hero
    const heroContent = document.querySelector('.hero-section .container');
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (heroContent && scrolled < window.innerHeight) {
            heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
            heroContent.style.opacity = 1 - (scrolled / (window.innerHeight * 0.8));
        }
    });
});
