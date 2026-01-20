<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Siskha Store - Where Elegance Meets Simplicity. Discover timeless fashion pieces crafted for the modern individual.')">
    
    <title>@yield('title', 'Siskha Store') - Luxury Fashion</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Reset & Base */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Brand Colors */
            --color-primary: #1a3a5c;
            --color-primary-dark: #0d2840;
            --color-primary-light: #2a5a8c;
            --color-black: #0a0a0a;
            --color-white: #ffffff;
            --color-gray-50: #fafafa;
            --color-gray-100: #f8f9fa;
            --color-gray-200: #e9ecef;
            --color-gray-300: #dee2e6;
            --color-gray-400: #ced4da;
            --color-gray-600: #6c757d;
            --color-gray-800: #343a40;
            
            /* Typography */
            --font-serif: 'Cormorant Garamond', Georgia, serif;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --space-xs: 0.5rem;
            --space-sm: 1rem;
            --space-md: 1.5rem;
            --space-lg: 2rem;
            --space-xl: 3rem;
            --space-2xl: 5rem;
            --space-3xl: 8rem;
            
            /* Transitions */
            --transition-fast: 0.2s ease;
            --transition-smooth: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--color-white);
            color: var(--color-black);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color var(--transition-fast);
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* Navigation */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: var(--space-md) var(--space-xl);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        }

        .nav-logo {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-primary-dark);
        }

        .nav-links {
            display: flex;
            gap: var(--space-lg);
            list-style: none;
        }

        .nav-links a {
            font-size: 0.875rem;
            font-weight: 400;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--color-gray-800);
            position: relative;
            padding-bottom: 2px;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background-color: var(--color-primary);
            transition: width var(--transition-smooth);
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--color-primary);
        }

        .nav-auth {
            display: flex;
            gap: var(--space-sm);
            align-items: center;
        }

        /* Mobile Menu Toggle */
        .nav-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 44px;
            height: 44px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1001;
        }

        .nav-toggle span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--color-primary-dark);
            margin: 3px 0;
            transition: all var(--transition-smooth);
        }

        .nav-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .nav-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 320px;
            height: 100vh;
            background: var(--color-white);
            z-index: 999;
            padding: 100px var(--space-xl) var(--space-xl);
            transition: right var(--transition-smooth);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu-links {
            list-style: none;
            margin-bottom: var(--space-xl);
        }

        .mobile-menu-links li {
            margin-bottom: var(--space-md);
        }

        .mobile-menu-links a {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 400;
            color: var(--color-black);
            transition: color var(--transition-fast);
        }

        .mobile-menu-links a:hover,
        .mobile-menu-links a.active {
            color: var(--color-primary);
        }

        .mobile-menu-auth {
            padding-top: var(--space-lg);
            border-top: 1px solid var(--color-gray-200);
        }

        .mobile-menu-auth .btn {
            width: 100%;
        }

        /* Overlay */
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity var(--transition-smooth);
        }

        .menu-overlay.active {
            opacity: 1;
        }

        /* Prevent body scroll when menu is open */
        body.menu-open {
            overflow: hidden;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: all var(--transition-smooth);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--color-gray-300);
            color: var(--color-gray-800);
        }

        .btn-outline:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .btn-primary {
            background: var(--color-primary-dark);
            color: var(--color-white);
        }

        .btn-primary:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(26, 58, 92, 0.3);
        }

        .btn-lg {
            padding: 1rem 2.5rem;
            font-size: 0.875rem;
        }

        /* Page Header */
        .page-header {
            padding-top: 140px;
            padding-bottom: var(--space-2xl);
            text-align: center;
            background: var(--color-gray-50);
        }

        .page-header-eyebrow {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--color-primary);
            margin-bottom: var(--space-sm);
        }

        .page-header-title {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 300;
            color: var(--color-black);
            margin-bottom: var(--space-md);
        }

        .page-header-subtitle {
            font-size: 1.125rem;
            font-weight: 300;
            color: var(--color-gray-600);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Section Commons */
        .section {
            padding: var(--space-3xl) 8%;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--space-2xl);
        }

        .section-eyebrow {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--color-primary);
            margin-bottom: var(--space-sm);
        }

        .section-title {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 300;
            color: var(--color-black);
            margin-bottom: var(--space-md);
        }

        .section-subtitle {
            font-size: 1rem;
            font-weight: 300;
            color: var(--color-gray-600);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            background: var(--color-black);
            color: var(--color-white);
            padding: var(--space-3xl) 8% var(--space-xl);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: var(--space-2xl);
            margin-bottom: var(--space-2xl);
        }

        .footer-brand {
            max-width: 300px;
        }

        .footer-logo {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-white);
            margin-bottom: var(--space-md);
        }

        .footer-tagline {
            font-size: 0.9375rem;
            font-weight: 300;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
        }

        .footer-title {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-white);
            margin-bottom: var(--space-md);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: var(--space-sm);
        }

        .footer-links a {
            font-size: 0.9375rem;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.6);
            transition: color var(--transition-fast);
        }

        .footer-links a:hover {
            color: var(--color-white);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: var(--space-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-copy {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.4);
        }

        .footer-social {
            display: flex;
            gap: var(--space-md);
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.6);
            transition: all var(--transition-fast);
        }

        .footer-social a:hover {
            border-color: var(--color-white);
            color: var(--color-white);
        }

        .footer-social svg {
            width: 18px;
            height: 18px;
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .nav-links {
                display: none;
            }

            .nav-toggle {
                display: flex;
            }

            .mobile-menu {
                display: block;
            }

            .menu-overlay {
                display: block;
            }

            .nav-auth {
                display: none;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: var(--space-xl);
            }
        }

        @media (max-width: 640px) {
            .nav {
                padding: var(--space-sm) var(--space-md);
            }

            .nav-logo {
                font-size: 1.25rem;
            }

            .section {
                padding: var(--space-2xl) var(--space-md);
            }

            .page-header {
                padding-top: 100px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: var(--space-md);
                text-align: center;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav" id="navbar">
        <a href="{{ url('/') }}" class="nav-logo">Siskha</a>
        
        <ul class="nav-links">
            <li><a href="{{ route('collections') }}" class="{{ request()->routeIs('collections') ? 'active' : '' }}">Collections</a></li>
            <li><a href="{{ route('new-arrivals') }}" class="{{ request()->routeIs('new-arrivals') ? 'active' : '' }}">New Arrivals</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
        </ul>

        <div class="nav-auth">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
                @endauth
            @endif
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <ul class="mobile-menu-links">
            <li><a href="{{ route('collections') }}" class="{{ request()->routeIs('collections') ? 'active' : '' }}">Collections</a></li>
            <li><a href="{{ route('new-arrivals') }}" class="{{ request()->routeIs('new-arrivals') ? 'active' : '' }}">New Arrivals</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
        </ul>
        <div class="mobile-menu-auth">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
                @endauth
            @endif
        </div>
    </div>

    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3 class="footer-logo">Siskha</h3>
                <p class="footer-tagline">
                    Where elegance meets simplicity. Discover timeless fashion 
                    pieces crafted for the modern individual.
                </p>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Collections</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('collections') }}">Women</a></li>
                    <li><a href="{{ route('collections') }}">Men</a></li>
                    <li><a href="{{ route('collections') }}">Accessories</a></li>
                    <li><a href="{{ route('new-arrivals') }}">Latest Arrivals</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Company</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Store Locations</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Support</h4>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">&copy; {{ date('Y') }} Siskha Store. All rights reserved.</p>
            <div class="footer-social">
                <a href="#" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        // Fade in on scroll
        const fadeElements = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(el => observer.observe(el));

        // Mobile Menu Toggle
        const navToggle = document.getElementById('navToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuOverlay = document.getElementById('menuOverlay');
        const body = document.body;

        function toggleMenu() {
            navToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            menuOverlay.classList.toggle('active');
            body.classList.toggle('menu-open');
        }

        function closeMenu() {
            navToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            body.classList.remove('menu-open');
        }

        navToggle.addEventListener('click', toggleMenu);
        menuOverlay.addEventListener('click', closeMenu);

        // Close menu when clicking a link
        const mobileMenuLinks = document.querySelectorAll('.mobile-menu-links a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
