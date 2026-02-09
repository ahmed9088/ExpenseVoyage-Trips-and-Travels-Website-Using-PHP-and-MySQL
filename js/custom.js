/**
 * ExpenseVoyage - Interactions & Theme Controller
 */

document.addEventListener('DOMContentLoaded', function () {

    // 1. Theme Controller (Dark/Light Mode)
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const cursor = document.querySelector('.luxury-cursor');

    // Custom Cursor Movement
    document.addEventListener('mousemove', (e) => {
        if (cursor) {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
        }
    });

    // Cursor Hover Effects
    document.querySelectorAll('a, button, .bento-item').forEach(el => {
        el.addEventListener('mouseenter', () => cursor?.classList.add('cursor-hover'));
        el.addEventListener('mouseleave', () => cursor?.classList.remove('cursor-hover'));
    });

    // Check saved preference
    const currentTheme = localStorage.getItem('theme') || 'dark';
    if (currentTheme === 'light') {
        body.classList.add('light-mode');
        if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    }

    if (themeToggle) {
        console.log("Theme toggle found, initializing...");
        themeToggle.addEventListener('click', () => {
            console.log("Switching theme...");
            body.classList.toggle('light-mode');
            const theme = body.classList.contains('light-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', theme);

            // Update icon
            themeToggle.innerHTML = theme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
        });
    } else {
        console.error("Theme toggle button NOT found!");
    }

    // Scroll to Top Progress Logic
    const progressPath = document.querySelector('.progress-ring__circle');
    const scrollToTopBtn = document.querySelector('.scroll-to-top');

    if (progressPath) {
        const pathLength = progressPath.getTotalLength();
        progressPath.style.strokeDasharray = `${pathLength} ${pathLength}`;
        progressPath.style.strokeDashoffset = pathLength;

        const updateProgress = () => {
            const scroll = window.pageYOffset;
            const height = document.documentElement.scrollHeight - window.innerHeight;
            const progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;

            if (scroll > 100) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        };

        window.addEventListener('scroll', updateProgress);

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Quick Chip Active State Logic
    const chips = document.querySelectorAll('.chip');
    chips.forEach(chip => {
        chip.addEventListener('click', function () {
            chips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // 2. Navbar Scroll Effect
    const navbar = document.getElementById('mainNav');

    function handleScroll() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll();

    // 3. Parallax Hero Background
    const heroBg = document.getElementById('hero-bg');
    const heroContent = document.querySelector('.hero-section .container');

    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (heroBg && scrolled < window.innerHeight) {
            heroBg.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
        if (heroContent && scrolled < window.innerHeight) {
            heroContent.style.opacity = 1 - (scrolled / 700);
            heroContent.style.transform = `translateY(${scrolled * 0.2}px)`;
        }
    });

    // 4. Reveal on Scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                if (entry.target.dataset.animation) {
                    entry.target.classList.add('animate__animated', entry.target.dataset.animation);
                }
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll, .reveal-up').forEach(el => {
        revealObserver.observe(el);
    });

    // 5. Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
