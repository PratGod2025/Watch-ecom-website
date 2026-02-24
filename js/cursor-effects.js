/**
 * Time-Hub – Antigravity Cursor Effects
 * - Custom red cursor ring + dot
 * - Particle trail on mouse move
 * - Floating orbs that drift upward (antigravity) and avoid the cursor
 */

(function () {
    'use strict';

    /* ── Custom Cursor Elements ── */
    const cursorRing = document.createElement('div');
    cursorRing.id = 'th-cursor-ring';
    const cursorDot  = document.createElement('div');
    cursorDot.id  = 'th-cursor-dot';
    document.body.appendChild(cursorRing);
    document.body.appendChild(cursorDot);

    const style = document.createElement('style');
    style.innerHTML = `
        *, *::before, *::after { cursor: none !important; }

        #th-cursor-ring {
            position: fixed;
            width: 36px; height: 36px;
            border: 2px solid rgba(192,57,43,.7);
            border-radius: 50%;
            pointer-events: none;
            z-index: 999999;
            transform: translate(-50%,-50%);
            transition: transform .08s ease, width .2s, height .2s, border-color .2s;
            mix-blend-mode: normal;
            box-shadow: 0 0 12px rgba(192,57,43,.4);
        }
        #th-cursor-ring.hovering {
            width: 54px; height: 54px;
            border-color: rgba(201,168,76,.9);
            box-shadow: 0 0 20px rgba(201,168,76,.5);
        }
        #th-cursor-dot {
            position: fixed;
            width: 7px; height: 7px;
            background: #c0392b;
            border-radius: 50%;
            pointer-events: none;
            z-index: 999999;
            transform: translate(-50%,-50%);
            box-shadow: 0 0 6px #c0392b;
        }

        /* Particles */
        .th-particle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 999998;
            transform: translate(-50%,-50%);
            animation: th-float-up linear forwards;
        }
        @keyframes th-float-up {
            0%   { opacity: 1;   transform: translate(-50%,-50%) scale(1); }
            100% { opacity: 0;   transform: translate(calc(-50% + var(--dx)), calc(-50% - 80px)) scale(0); }
        }

        /* Floating ambient orbs */
        .th-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 999990;
            animation: th-drift linear infinite;
            opacity: .18;
        }
        @keyframes th-drift {
            0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
            10%  { opacity: .18; }
            90%  { opacity: .18; }
            100% { transform: translateY(-120px) translateX(var(--orb-dx)); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    let mx = -200, my = -200;
    let ringX = -200, ringY = -200;

    /* ── Mouse tracking ── */
    document.addEventListener('mousemove', e => {
        mx = e.clientX;
        my = e.clientY;
        cursorDot.style.left  = mx + 'px';
        cursorDot.style.top   = my + 'px';
        spawnParticle(mx, my);
    });

    /* ── Smooth ring follow ── */
    function animateRing() {
        ringX += (mx - ringX) * 0.14;
        ringY += (my - ringY) * 0.14;
        cursorRing.style.left = ringX + 'px';
        cursorRing.style.top  = ringY + 'px';
        requestAnimationFrame(animateRing);
    }
    animateRing();

    /* ── Hover effects on interactive elements ── */
    document.addEventListener('mouseover', e => {
        if (e.target.matches('a, button, input, select, textarea, label, .product-card, .home-card, .pay-card')) {
            cursorRing.classList.add('hovering');
        }
    });
    document.addEventListener('mouseout', e => {
        if (e.target.matches('a, button, input, select, textarea, label, .product-card, .home-card, .pay-card')) {
            cursorRing.classList.remove('hovering');
        }
    });

    /* ── Particle trail ── */
    let lastParticle = 0;
    function spawnParticle(x, y) {
        const now = Date.now();
        if (now - lastParticle < 40) return;
        lastParticle = now;

        const p = document.createElement('div');
        p.className = 'th-particle';

        const size = 4 + Math.random() * 6;
        const hue  = Math.random() < 0.6 ? 'rgba(192,57,43,' : 'rgba(201,168,76,';
        const alpha = (0.5 + Math.random() * 0.5).toFixed(2);
        const dx = (Math.random() - 0.5) * 40;
        const dur = 0.5 + Math.random() * 0.6;

        p.style.cssText = `
            left:${x}px; top:${y}px;
            width:${size}px; height:${size}px;
            background:${hue}${alpha});
            --dx:${dx}px;
            animation-duration:${dur}s;
            box-shadow:0 0 ${size}px ${hue}0.6);
        `;
        document.body.appendChild(p);
        setTimeout(() => p.remove(), dur * 1000);
    }

    /* ── Floating ambient orbs (antigravity effect) ── */
    const ORB_COLORS = [
        'rgba(192,57,43,1)',
        'rgba(201,168,76,1)',
        'rgba(139,26,18,1)',
        'rgba(220,80,60,1)',
    ];

    function spawnOrb() {
        const orb = document.createElement('div');
        orb.className = 'th-orb';
        const size = 10 + Math.random() * 30;
        const x    = Math.random() * window.innerWidth;
        const dx   = (Math.random() - 0.5) * 160;
        const dur  = 5 + Math.random() * 10;
        const color = ORB_COLORS[Math.floor(Math.random() * ORB_COLORS.length)];
        orb.style.cssText = `
            left:${x}px; bottom:-20px;
            width:${size}px; height:${size}px;
            background:${color};
            --orb-dx:${dx}px;
            animation-duration:${dur}s;
            filter:blur(${size * 0.3}px);
        `;
        document.body.appendChild(orb);
        setTimeout(() => orb.remove(), dur * 1000);
    }

    // Spawn orbs continuously
    setInterval(spawnOrb, 1200);
    for (let i = 0; i < 5; i++) setTimeout(spawnOrb, i * 300);

    /* ── Repel effect: nearby elements drift away from cursor ── */
    const repelEls = [];
    function registerRepelTargets() {
        document.querySelectorAll('.product-card, .home-card, .stat-card').forEach(el => {
            if (!repelEls.includes(el)) repelEls.push(el);
        });
    }
    setTimeout(registerRepelTargets, 500);

    let repelTick = 0;
    document.addEventListener('mousemove', () => {
        if (++repelTick % 3 !== 0) return;  // throttle
        repelEls.forEach(el => {
            const r = el.getBoundingClientRect();
            const ex = r.left + r.width / 2;
            const ey = r.top  + r.height / 2;
            const dist = Math.hypot(mx - ex, my - ey);
            if (dist < 160) {
                const force = (160 - dist) / 160 * 8;
                const angle = Math.atan2(ey - my, ex - mx);
                const tx = Math.cos(angle) * force;
                const ty = Math.sin(angle) * force;
                el.style.transform = `translate(${tx}px, ${ty}px)`;
                el.style.transition = 'transform .1s ease';
            } else {
                el.style.transform = '';
                el.style.transition = 'transform .4s ease';
            }
        });
    });
})();
