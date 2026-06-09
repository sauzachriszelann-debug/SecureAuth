<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureAuth — IT 322 Final Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;1,9..144,300&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --sand:     #f5f0e8;
            --cream:    #faf8f4;
            --clay:     #c9a882;
            --rust:     #b5541c;
            --espresso: #2d1f14;
            --bark:     #6b4c2e;
            --mist:     #e8e2d9;
            --sage:     #7a9178;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--espresso);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
            height: 64px;
            background: rgba(250, 248, 244, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--mist);
            transition: box-shadow 0.3s;
        }
        nav.scrolled { box-shadow: 0 4px 24px rgba(45,31,20,0.08); }

        .nav-logo {
            font-family: 'Fraunces', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--espresso);
            letter-spacing: -0.5px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .nav-logo span { color: var(--rust); }

        /* Desktop links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--bark);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }
        .nav-links a:hover { background: var(--sand); color: var(--espresso); }
        .nav-links .nav-cta {
            background: var(--espresso);
            color: var(--cream) !important;
            padding: 8px 20px;
            border-radius: 8px;
            transition: background 0.2s, transform 0.15s !important;
        }
        .nav-links .nav-cta:hover { background: var(--bark) !important; transform: translateY(-1px); }

        /* Hamburger button — hidden on desktop */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1.5px solid var(--mist);
            border-radius: 8px;
            cursor: pointer;
            padding: 0;
            transition: background 0.2s, border-color 0.2s;
            flex-shrink: 0;
        }
        .nav-hamburger:hover { background: var(--sand); border-color: var(--clay); }
        .nav-hamburger span {
            display: block;
            width: 18px;
            height: 1.5px;
            background: var(--espresso);
            border-radius: 2px;
            transition: transform 0.3s, opacity 0.3s;
            transform-origin: center;
        }
        /* X state */
        .nav-hamburger.open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
        .nav-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .nav-hamburger.open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

        /* Mobile drawer */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 64px;
            left: 0; right: 0;
            background: rgba(250, 248, 244, 0.98);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--mist);
            padding: 16px 20px 20px;
            z-index: 99;
            flex-direction: column;
            gap: 4px;
            transform: translateY(-8px);
            opacity: 0;
            transition: transform 0.25s ease, opacity 0.25s ease;
            pointer-events: none;
        }
        .mobile-menu.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: all;
        }
        .mobile-menu a {
            display: block;
            font-size: 0.92rem;
            font-weight: 500;
            color: var(--bark);
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .mobile-menu a:hover { background: var(--sand); color: var(--espresso); }
        .mobile-menu .mobile-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            background: var(--espresso);
            color: var(--cream) !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 13px 14px;
            gap: 8px;
        }
        .mobile-menu .mobile-cta:hover { background: var(--bark) !important; }
        .mobile-menu-divider {
            height: 1px;
            background: var(--mist);
            margin: 8px 0;
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 24px 60px;
            background-image: radial-gradient(circle, rgba(107,76,46,0.06) 1px, transparent 1px);
            background-size: 22px 22px;
        }
        .hero-inner { max-width: 680px; text-align: center; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: var(--sand);
            border: 1px solid var(--mist);
            border-radius: 100px;
            margin-bottom: 28px;
            animation: fadeUp 0.5s 0.1s both;
        }
        .badge-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--sage);
            animation: pulse 2s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
        .badge-text {
            font-family: 'DM Mono', monospace;
            font-size: 0.62rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(107,76,46,0.85);
        }
        .hero h1 {
            font-family: 'Fraunces', serif;
            font-size: clamp(3rem, 8vw, 5.5rem);
            font-weight: 700;
            color: var(--espresso);
            line-height: 1.0;
            letter-spacing: -2px;
            margin-bottom: 12px;
            animation: fadeUp 0.5s 0.2s both;
        }
        .hero h1 em { font-style: italic; font-weight: 300; color: var(--rust); }
        .hero-sub {
            font-size: 1rem;
            color: var(--bark);
            line-height: 1.7;
            max-width: 500px;
            margin: 0 auto 36px;
            animation: fadeUp 0.5s 0.3s both;
        }
        .hero-btns {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.5s 0.4s both;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--espresso);
            color: var(--cream);
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(45,31,20,0.2);
        }
        .btn-primary:hover { background: var(--bark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(45,31,20,0.22); }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: transparent;
            color: var(--espresso);
            border: 1.5px solid var(--mist);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 600;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s, transform 0.15s;
        }
        .btn-secondary:hover { border-color: var(--clay); background: var(--sand); transform: translateY(-2px); }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

        /* ── FEATURES ── */
        .features {
            padding: 80px 24px;
            background: #fff;
            border-top: 1px solid var(--mist);
            border-bottom: 1px solid var(--mist);
        }
        .features-inner { max-width: 960px; margin: 0 auto; }
        .section-eyebrow {
            font-family: 'DM Mono', monospace;
            font-size: 0.62rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--bark);
            font-weight: 600;
            text-align: center;
            margin-bottom: 12px;
        }
        .section-title {
            font-family: 'Fraunces', serif;
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: var(--espresso);
            margin-bottom: 8px;
            letter-spacing: -1px;
        }
        .section-sub {
            text-align: center;
            color: var(--bark);
            font-size: 0.9rem;
            margin-bottom: 52px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .feat-card {
            padding: 28px 24px;
            background: var(--sand);
            border: 1px solid var(--mist);
            border-radius: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(45,31,20,0.1); }
        .feat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            background: var(--espresso);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .feat-icon svg { width: 22px; height: 22px; stroke: var(--cream); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .feat-title {
            font-family: 'Fraunces', serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 6px;
        }
        .feat-desc { font-size: 0.85rem; color: var(--bark); line-height: 1.6; }

        /* ── TEAM ── */
        .team {
            padding: 80px 24px;
            background: var(--cream);
            background-image: radial-gradient(circle, rgba(107,76,46,0.05) 1px, transparent 1px);
            background-size: 22px 22px;
        }
        .team-inner { max-width: 800px; margin: 0 auto; }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 48px;
        }
        .member-card {
            background: #fff;
            border: 1px solid var(--mist);
            border-radius: 14px;
            padding: 28px 20px 22px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .member-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(45,31,20,0.09); }
        .member-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            margin: 0 auto 14px;
            overflow: hidden;
            border: 3px solid var(--mist);
            box-shadow: 0 4px 14px rgba(45,31,20,0.12);
            background: var(--sand);
            position: relative;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .member-card:hover .member-avatar { border-color: var(--clay); box-shadow: 0 6px 20px rgba(181,84,28,0.18); }
        .member-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .member-avatar .avatar-fallback {
            display: none;
            position: absolute;
            inset: 0;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--rust), var(--clay));
            font-family: 'Fraunces', serif;
            font-size: 1.5rem;
            color: #fff;
            font-weight: 700;
        }
        .member-avatar img.errored { display: none; }
        .member-avatar img.errored + .avatar-fallback { display: flex; }
        .member-name {
            font-family: 'Fraunces', serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--espresso);
            margin-bottom: 4px;
        }
        .member-role {
            font-family: 'DM Mono', monospace;
            font-size: 0.58rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--rust);
            font-weight: 500;
        }

        /* ── FOOTER ── */
        footer {
            padding: 28px 48px;
            border-top: 1px solid var(--mist);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .footer-logo { font-family: 'Fraunces', serif; font-size: 1rem; font-weight: 700; color: var(--espresso); }
        .footer-logo span { color: var(--rust); }
        .footer-text {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--bark);
            opacity: 0.7;
        }
        .footer-tags { display: flex; gap: 8px; flex-wrap: wrap; }
        .footer-tag {
            padding: 4px 10px;
            background: var(--sand);
            border: 1px solid var(--mist);
            border-radius: 100px;
            font-family: 'DM Mono', monospace;
            font-size: 0.58rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--bark);
        }

        .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.55s ease, transform 0.55s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            nav {
                padding: 0 20px;
            }

            /* Hide desktop links, show hamburger */
            .nav-links { display: none; }
            .nav-hamburger { display: flex; }
            .mobile-menu { display: flex; }

            .hero { padding-top: 120px; }
            .hero h1 { font-size: clamp(2.5rem, 10vw, 4rem); letter-spacing: -1px; }
            .hero-btns { flex-direction: column; align-items: stretch; }
            .hero-btns a { justify-content: center; }

            .features-grid { grid-template-columns: 1fr; }
            .team-grid { grid-template-columns: 1fr 1fr; }

            .section-title { font-size: 1.75rem; }

            footer {
                padding: 24px 20px;
                flex-direction: column;
                text-align: center;
                align-items: center;
            }
            .footer-tags { justify-content: center; }
        }

        @media (max-width: 420px) {
            .team-grid { grid-template-columns: 1fr; }
        }

        .nav-links a.active {
    background: var(--sand);
    color: var(--rust);
}
.mobile-menu a.active {
    background: var(--sand);
    color: var(--rust);
}
    </style>
</head>
<body>

    <nav id="navbar">
        <a href="#" class="nav-logo">Secure<span>Auth</span></a>

        <!-- Desktop links -->
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#team">Team</a></li>
            <li><a href="register.php" class="nav-cta">Get Started</a></li>
        </ul>

        <!-- Hamburger (mobile only) -->
        <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Mobile menu drawer -->
    <div class="mobile-menu" id="mobileMenu" role="dialog" aria-label="Navigation menu">
        <a href="#features" class="mobile-link">Features</a>
        <a href="#team" class="mobile-link">Team</a>
        <div class="mobile-menu-divider"></div>
        <a href="register.php" class="mobile-cta">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Get Started
        </a>
    </div>

    <main>
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-badge">
                    <div class="badge-dot"></div>
                    <span class="badge-text">IT 322 &nbsp;·&nbsp; Information Assurance and Security</span>
                </div>
                <h1>Secure<em>Auth</em></h1>
                <p class="hero-sub">
                    A secure user authentication system implementing SHA-256 hashing,
                    salt, pepper, and real-time password strength validation — built for IT 322.
                </p>
                <div class="hero-btns">
                    <a href="register.php" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Create an Account
                    </a>
                    <a href="login.php" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Sign In
                    </a>
                </div>
            </div>
        </section>

        <section class="features" id="features">
            <div class="features-inner">
                <p class="section-eyebrow reveal">Security Features</p>
                <h2 class="section-title reveal">Built with cybersecurity in mind</h2>
                <p class="section-sub reveal">Every layer of this system was designed to protect user credentials.</p>
                <div class="features-grid">
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                        <div class="feat-title">SHA-256 Hashing</div>
                        <div class="feat-desc">Passwords are transformed into a 64-character hex digest. Never stored in plain text.</div>
                    </div>
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/></svg></div>
                        <div class="feat-title">Unique Salt</div>
                        <div class="feat-desc">A random 64-char salt is generated per user, defeating rainbow table attacks.</div>
                    </div>
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                        <div class="feat-title">Server Pepper</div>
                        <div class="feat-desc">A secret pepper stored only in source code adds a second layer — invisible to DB leaks.</div>
                    </div>
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                        <div class="feat-title">Strength Meter</div>
                        <div class="feat-desc">Real-time password evaluation against 5 criteria: length, case, digits, and symbols.</div>
                    </div>
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></div>
                        <div class="feat-title">PDO + No SQL Injection</div>
                        <div class="feat-desc">All queries use prepared statements via PDO — fully protected against SQL injection.</div>
                    </div>
                    <div class="feat-card reveal">
                        <div class="feat-icon"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M12 22C6.48 22 2 17.52 2 12S6.48 2 12 2s10 4.48 10 10-4.48 10-10 10z"/></svg></div>
                        <div class="feat-title">Timing-Safe Login</div>
                        <div class="feat-desc">Uses hash_equals() to prevent timing-based side-channel attacks during verification.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="team" id="team">
            <div class="team-inner">
                <p class="section-eyebrow reveal">The Team</p>
                <h2 class="section-title reveal">Group Members</h2>
                <p class="section-sub reveal">IT 322 — Information Assurance and Security &nbsp;·&nbsp; Final Project</p>
                <div class="team-grid">
                    <div class="member-card reveal">
                        <div class="member-avatar">
                            <img src="maano.png" alt="Shaina Ma-ano" onerror="this.classList.add('errored')">
                            <div class="avatar-fallback">S</div>
                        </div>
                        <div class="member-name">Shaina Ma-ano</div>
                        <div class="member-role">Member</div>
                    </div>
                    <div class="member-card reveal">
                        <div class="member-avatar">
                            <img src="me3.png" alt="Chriszel Ann Sauza" onerror="this.classList.add('errored')">
                            <div class="avatar-fallback">C</div>
                        </div>
                        <div class="member-name">Chriszel Ann Sauza</div>
                        <div class="member-role">Member</div>
                    </div>
                    <div class="member-card reveal">
                        <div class="member-avatar">
                            <img src="gella2.png" alt="Arabella P. Gella" onerror="this.classList.add('errored')">
                            <div class="avatar-fallback">A</div>
                        </div>
                        <div class="member-name">Arabella P. Gella</div>
                        <div class="member-role">Member</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-logo">Secure<span>Auth</span></div>
        <div class="footer-tags">
            <span class="footer-tag">SHA-256</span>
            <span class="footer-tag">Salt + Pepper</span>
            <span class="footer-tag">PDO</span>
            <span class="footer-tag">IT 322</span>
            <span class="footer-tag">Information Assurance and Security 1</span>
        </div>
        <div class="footer-text">Final Project &nbsp;·&nbsp; June 2026</div>
    </footer>

    <script>
        // Navbar scroll shadow
        const nav = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            const isOpen = hamburger.classList.toggle('open');
            mobileMenu.classList.toggle('open', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen);
        });

        // Close menu when a link is clicked
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('open');
                mobileMenu.classList.remove('open');
                hamburger.setAttribute('aria-expanded', false);
            });
        });

        // Close on outside tap
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('open');
                mobileMenu.classList.remove('open');
                hamburger.setAttribute('aria-expanded', false);
            }
        });

        // Scroll reveal
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            let delay = 0;
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), delay * 80);
                    delay++;
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });
        reveals.forEach(el => observer.observe(el));

     // Active nav highlight on scroll
const sections = document.querySelectorAll('section[id]');
const navAnchors = document.querySelectorAll('.nav-links a[href^="#"], .mobile-menu a[href^="#"]');

function updateActiveNav() {
    const scrollY = window.scrollY + 80; // offset for fixed navbar height

    let current = '';
    sections.forEach(section => {
        const top = section.offsetTop;
        const height = section.offsetHeight;
        if (scrollY >= top && scrollY < top + height) {
            current = section.getAttribute('id');
        }
    });

    navAnchors.forEach(a => {
        a.classList.toggle('active', a.getAttribute('href') === '#' + current);
    });
}

window.addEventListener('scroll', updateActiveNav, { passive: true });
updateActiveNav(); // run once on load
    </script>
</body>
</html>