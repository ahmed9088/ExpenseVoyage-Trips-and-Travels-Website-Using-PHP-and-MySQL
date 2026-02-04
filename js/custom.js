/**
 * ExpenseVoyage - Premium 'Midnight Luxe' Interactions
 * Hand-crafted with cinematic animations and smooth transitions.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize Cinematic Background (Particles)
    if (document.getElementById('particles-js')) {
        particlesJS('particles-js', {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 1200 } },
                color: { value: '#d4af37' }, // Gold theme
                shape: { type: 'circle' },
                opacity: {
                    value: 0.3,
                    random: true,
                    anim: { enable: true, speed: 0.5, opacity_min: 0.1, sync: false }
                },
                size: {
                    value: 2,
                    random: true,
                    anim: { enable: true, speed: 2, size_min: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 180,
                    color: '#d4af37',
                    opacity: 0.1,
                    width: 0.5
                },
                move: {
                    enable: true,
                    speed: 0.8,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'bubble' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                },
                modes: {
                    bubble: { distance: 200, size: 4, duration: 2, opacity: 0.5, speed: 3 },
                    push: { particles_nb: 3 }
                }
            },
            retina_detect: true
        });
    }

    // 2. Navbar Cinematic Transparency
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('glass-panel');
            navbar.style.padding = '10px 0';
        } else {
            navbar.classList.remove('glass-panel');
            navbar.style.padding = '20px 0';
        }
    });

    // 3. Smooth Reveal on Scroll
    const revealElements = document.querySelectorAll('.animate-on-scroll');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', entry.target.dataset.animation || 'animate__fadeInUp');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => revealObserver.observe(el));

    // 4. Parallax Hero Effect
    const heroContent = document.querySelector('.hero-section .container');
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (heroContent && scrolled < window.innerHeight) {
            heroContent.style.transform = `translateY(${scrolled * 0.4}px)`;
            heroContent.style.opacity = 1 - (scrolled / window.innerHeight);
        }
    });
});
