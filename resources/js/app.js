import './bootstrap';

/* ============================================================
   ASTRAGO MARKET — Storefront Effects
   NOTE: Alpine is NOT imported here on purpose. Livewire 3 ships
   its own Alpine (window.Alpine) with its built-in plugins
   (navigate, morph, etc.). Overriding window.Alpine with a bare
   standalone Alpine breaks Livewire's SPA navigator
   ("Alpine.navigate is not a function") and kills every
   wire: interaction. Keep Livewire's Alpine; only extend it via
   Alpine.plugin AFTER Livewire has booted (see livewire:init).
   ============================================================ */

let particles = [];
let pMouse = { x: -1000, y: -1000 };
let particleCanvas = null;
let particleCtx = null;
let pRunning = false;

/* ── Particle Star Background (singleton, re-binds canvas) ── */
function initParticleCanvas() {
    const canvas = document.getElementById('particle-canvas');
    if (!canvas) return;

    // If already running on this exact canvas, just keep going.
    if (particleCanvas === canvas && pRunning) return;

    // Heavy O(n²) particle animation is a mobile CPU killer: on smaller screens
    // use far fewer particles and no connecting lines.
    const isMobile = window.innerWidth < 768;
    const PARTICLE_COUNT = isMobile ? 18 : 60;
    const CONNECT_DIST = 140;

    // Canvas was replaced or first run — (re)init.
    particleCanvas = canvas;
    particleCtx = canvas.getContext('2d');
    particles = [];
    pMouse = { x: -1000, y: -1000 };

    function resize() {
        if (!particleCanvas) return;
        // Cap device-pixel-ratio at 2 to avoid a giant canvas on 4K screens.
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        particleCanvas.width = window.innerWidth * dpr;
        particleCanvas.height = window.innerHeight * dpr;
        particleCanvas.style.width = window.innerWidth + 'px';
        particleCanvas.style.height = window.innerHeight + 'px';
        particleCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }
    resize();
    window.removeEventListener('resize', resize);
    window.addEventListener('resize', resize);

    document.addEventListener('mousemove', (e) => {
        pMouse.x = e.clientX;
        pMouse.y = e.clientY;
    });

    class Particle {
        constructor() { this.reset(); }
        reset() {
            this.x = Math.random() * (particleCanvas?.width || 1000);
            this.y = Math.random() * (particleCanvas?.height || 1000);
            this.size = Math.random() * 2 + 0.5;
            this.speedX = (Math.random() - 0.5) * 0.3;
            this.speedY = (Math.random() - 0.5) * 0.3;
            this.opacity = Math.random() * 0.5 + 0.2;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x < 0 || this.x > particleCanvas.width) this.speedX *= -1;
            if (this.y < 0 || this.y > particleCanvas.height) this.speedY *= -1;
            const dx = pMouse.x - this.x;
            const dy = pMouse.y - this.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 150) {
                this.x -= dx * 0.005;
                this.y -= dy * 0.005;
            }
        }
        draw() {
            particleCtx.beginPath();
            particleCtx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            particleCtx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
            particleCtx.fill();
        }
    }

    for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push(new Particle());
    }

    function connectParticles() {
        // Connecting lines are O(n²). Skip them entirely on mobile for smoothness.
        if (isMobile) return;
        for (let a = 0; a < particles.length; a++) {
            for (let b = a + 1; b < particles.length; b++) {
                const dx = particles[a].x - particles[b].x;
                const dy = particles[a].y - particles[b].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECT_DIST) {
                    const opacity = (1 - dist / CONNECT_DIST) * 0.12;
                    particleCtx.beginPath();
                    particleCtx.strokeStyle = `rgba(107, 70, 193, ${opacity})`;
                    particleCtx.lineWidth = 0.5;
                    particleCtx.moveTo(particles[a].x, particles[a].y);
                    particleCtx.lineTo(particles[b].x, particles[b].y);
                    particleCtx.stroke();
                }
            }
        }
    }

    function animate() {
        if (!pRunning) return;                      // outer loop killed
        if (!particleCtx || !particleCanvas || !particleCanvas.isConnected) return;
        particleCtx.clearRect(0, 0, particleCanvas.width, particleCanvas.height);
        particles.forEach(p => { p.update(); p.draw(); });
        connectParticles();
        requestAnimationFrame(animate);
    }

    pRunning = true;
    animate();
}

/* ── Scroll Reveal ──
   Robust: any element already inside (or near) the viewport is
   revealed immediately. Rest get an IntersectionObserver.
   This is what guarantees nothing stays invisible after a
   back-navigation restore. */
let revealObserver = null;

function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal, .stagger-reveal');
    if (!elements.length) return;

    // Reuse a single observer if it exists.
    if (!revealObserver) {
        revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
    }

    elements.forEach(el => {
        // Content is visible by default. We only attach the "animating"
        // class (which temporarily fades + slides) to elements that are
        // BELOW the fold, so they get a nice reveal when scrolled into view.
        const rect = el.getBoundingClientRect();
        const vpH = window.innerHeight || document.documentElement.clientHeight;
        if (rect.top < vpH && rect.bottom > 0) {
            // Already on screen: keep fully visible (no animation).
            el.classList.remove('animating');
            el.classList.add('revealed');
        } else {
            // Below the fold: mark for animation, then observe.
            el.classList.add('animating');
            el.classList.remove('revealed');
            revealObserver.observe(el);
        }
    });

    // Hard guarantee: after a short delay, remove the "animating" state for
    // anything still on screen so nothing can ever remain hidden.
    setTimeout(() => {
        const vpH = window.innerHeight || document.documentElement.clientHeight;
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < vpH && rect.bottom > 0) {
                el.classList.add('revealed');
                el.classList.remove('animating');
            }
        });
    }, 1200);
}

/* ── Hero Parallax on Mouse (single handler) ── */
let parallaxHandler = null;
function initParallax() {
    if (!document.querySelector('[data-parallax]')) return;
    if (parallaxHandler) document.removeEventListener('mousemove', parallaxHandler);
    parallaxHandler = (e) => {
        const el = document.querySelector('[data-parallax]');
        if (!el) return;
        const x = (e.clientX / window.innerWidth - 0.5) * 20;
        const y = (e.clientY / window.innerHeight - 0.5) * 20;
        el.style.transform = `translate(${x}px, ${y}px)`;
    };
    document.addEventListener('mousemove', parallaxHandler);
}

/* ── Master runner: first load AND every SPA navigation ── */
function runStorefrontEffects() {
    initParticleCanvas();
    initScrollReveal();
    initParallax();
}

runStorefrontEffects();                            // initial page load

/* Re-run after every Livewire SPA navigation.
   We hook multiple signals (belt and suspenders) so effects
   always reinitialize regardless of how Livewire reports it. */
function bindNavigation() {
    if (typeof Livewire !== 'undefined' && Livewire.hook) {
        Livewire.hook('navigated', () => runStorefrontEffects());
    }
    document.addEventListener('livewire:navigated', runStorefrontEffects);
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) runStorefrontEffects(); // bfcache restore
    });
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    bindNavigation();
} else {
    document.addEventListener('DOMContentLoaded', bindNavigation);
}

document.addEventListener('livewire:init', () => {
    if (typeof Livewire !== 'undefined' && Livewire.hook) {
        Livewire.hook('navigated', () => runStorefrontEffects());
    }
});
