<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Siskha Store - Where Elegance Meets Simplicity. Discover timeless fashion pieces crafted for the modern individual.">
    
    <title>{{ config('app.name', 'Siskha Store') }} - Luxury Fashion</title>

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
            background: linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(255,255,255,0));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .nav.scrolled {
            background: rgba(255,255,255,0.98);
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

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--color-primary);
        }

        .nav-auth {
            display: flex;
            gap: var(--space-sm);
            align-items: center;
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

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: var(--space-2xl);
            padding-left: 8%;
            background: var(--color-white);
        }

        .hero-eyebrow {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--color-primary);
            margin-bottom: var(--space-md);
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.2s;
        }

        .hero-title {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--color-black);
            margin-bottom: var(--space-lg);
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.4s;
        }

        .hero-title span {
            display: block;
            font-style: italic;
            font-weight: 400;
            color: var(--color-primary-dark);
        }

        .hero-tagline {
            font-size: 1.125rem;
            font-weight: 300;
            color: var(--color-gray-600);
            max-width: 420px;
            line-height: 1.8;
            margin-bottom: var(--space-xl);
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.6s;
        }

        .hero-cta {
            display: flex;
            gap: var(--space-sm);
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
            animation-delay: 0.8s;
        }

        .hero-image {
            position: relative;
            overflow: hidden;
            background: var(--color-gray-100);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.05);
            animation: heroZoom 1.5s ease forwards;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 58, 92, 0.1) 0%, transparent 60%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-decoration {
            position: absolute;
            bottom: var(--space-xl);
            left: var(--space-xl);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--color-gray-600);
        }

        .hero-decoration::before {
            content: '';
            width: 40px;
            height: 1px;
            background: var(--color-gray-300);
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

        /* Categories Section */
        .categories {
            background: var(--color-gray-50);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--space-lg);
        }

        .category-card {
            position: relative;
            overflow: hidden;
            aspect-ratio: 3/4;
            cursor: pointer;
        }

        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-smooth);
        }

        .category-card:hover img {
            transform: scale(1.05);
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(10, 10, 10, 0.7) 0%, transparent 60%);
            z-index: 1;
            transition: background var(--transition-smooth);
        }

        .category-card:hover::before {
            background: linear-gradient(to top, rgba(26, 58, 92, 0.8) 0%, rgba(26, 58, 92, 0.2) 100%);
        }

        .category-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: var(--space-lg);
            z-index: 2;
            color: var(--color-white);
        }

        .category-name {
            font-family: var(--font-serif);
            font-size: 1.75rem;
            font-weight: 400;
            margin-bottom: var(--space-xs);
        }

        .category-count {
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .category-link {
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
            margin-top: var(--space-sm);
            font-size: 0.8125rem;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--color-white);
            opacity: 0;
            transform: translateY(10px);
            transition: all var(--transition-smooth);
        }

        .category-card:hover .category-link {
            opacity: 1;
            transform: translateY(0);
        }

        .category-link svg {
            width: 16px;
            height: 16px;
            transition: transform var(--transition-fast);
        }

        .category-link:hover svg {
            transform: translateX(4px);
        }

        /* Featured Products */
        .featured-products {
            background: var(--color-white);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--space-lg);
        }

        .product-card {
            position: relative;
            cursor: pointer;
        }

        .product-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 3/4;
            background: var(--color-gray-100);
            margin-bottom: var(--space-md);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-smooth);
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: var(--space-sm);
            left: var(--space-sm);
            padding: 0.25rem 0.75rem;
            font-size: 0.6875rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: var(--color-primary-dark);
            color: var(--color-white);
        }

        .product-name {
            font-family: var(--font-serif);
            font-size: 1.125rem;
            font-weight: 400;
            color: var(--color-black);
            margin-bottom: var(--space-xs);
        }

        .product-category {
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--color-gray-600);
            margin-bottom: var(--space-xs);
        }

        .product-price {
            font-size: 1rem;
            font-weight: 500;
            color: var(--color-primary-dark);
        }

        /* About Section */
        .about {
            background: var(--color-primary-dark);
            color: var(--color-white);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 80vh;
        }

        .about-image {
            position: relative;
            overflow: hidden;
        }

        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .about-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: var(--space-3xl);
        }

        .about-eyebrow {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: var(--space-md);
        }

        .about-title {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 300;
            line-height: 1.2;
            margin-bottom: var(--space-lg);
        }

        .about-text {
            font-size: 1rem;
            font-weight: 300;
            line-height: 1.9;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: var(--space-xl);
            max-width: 500px;
        }

        .about-stats {
            display: flex;
            gap: var(--space-2xl);
            margin-top: var(--space-lg);
            padding-top: var(--space-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            text-align: left;
        }

        .stat-number {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            font-weight: 300;
            color: var(--color-white);
            margin-bottom: var(--space-xs);
        }

        .stat-label {
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
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

        @keyframes heroZoom {
            from { transform: scale(1.1); }
            to { transform: scale(1); }
        }

        /* Scroll animations */
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
        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
                grid-template-rows: 50vh auto;
            }

            .hero-image {
                order: -1;
            }

            .hero-content {
                padding: var(--space-xl);
                text-align: center;
                align-items: center;
            }

            .hero-tagline {
                max-width: 100%;
            }

            .hero-cta {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
            }

            .btn {
                width: 100%;
            }

            .nav-links {
                display: none;
            }

            .hero-decoration {
                display: none;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .category-card {
                aspect-ratio: 16/9;
            }

            .about {
                grid-template-columns: 1fr;
            }

            .about-image {
                height: 50vh;
            }

            .about-content {
                padding: var(--space-2xl);
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

            .hero-content {
                padding: var(--space-lg);
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-tagline {
                font-size: 1rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .about-stats {
                flex-direction: column;
                gap: var(--space-lg);
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
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav" id="navbar">
        <a href="{{ url('/') }}" class="nav-logo">Siskha</a>
        
        <ul class="nav-links">
            <li><a href="#categories">Collections</a></li>
            <li><a href="#products">New Arrivals</a></li>
            <li><a href="#about">About</a></li>
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
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <p class="hero-eyebrow">Luxury Fashion</p>
            <h1 class="hero-title">
                Where Elegance
                <span>Meets Simplicity</span>
            </h1>
            <p class="hero-tagline">
                Discover timeless pieces crafted with precision and passion. 
                Each design tells a story of sophistication, made for those 
                who appreciate the art of understated luxury.
            </p>
            <div class="hero-cta">
                <a href="#categories" class="btn btn-primary">Explore Collection</a>
                <a href="#about" class="btn btn-outline">Our Story</a>
            </div>

            <div class="hero-decoration">
                <span>Est. 2026</span>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hero-fashion.png') }}" alt="Luxury Fashion - Premium fabrics and timeless design">
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section categories" id="categories">
        <div class="section-header fade-in">
            <p class="section-eyebrow">Our Collections</p>
            <h2 class="section-title">Curated Categories</h2>
            <p class="section-subtitle">Explore our thoughtfully curated collections designed for every occasion</p>
        </div>

        <div class="categories-grid">
            <div class="category-card fade-in">
                <img src="{{ asset('images/category-women.png') }}" alt="Women's Collection">
                <div class="category-content">
                    <h3 class="category-name">Women</h3>
                    <p class="category-count">120+ Items</p>
                    <a href="#" class="category-link">
                        View Collection
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="category-card fade-in">
                <img src="{{ asset('images/category-men.png') }}" alt="Men's Collection">
                <div class="category-content">
                    <h3 class="category-name">Men</h3>
                    <p class="category-count">85+ Items</p>
                    <a href="#" class="category-link">
                        View Collection
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="category-card fade-in">
                <img src="{{ asset('images/category-accessories.png') }}" alt="Accessories Collection">
                <div class="category-content">
                    <h3 class="category-name">Accessories</h3>
                    <p class="category-count">60+ Items</p>
                    <a href="#" class="category-link">
                        View Collection
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section featured-products" id="products">
        <div class="section-header fade-in">
            <p class="section-eyebrow">New Arrivals</p>
            <h2 class="section-title">Latest Collection</h2>
            <p class="section-subtitle">Fresh styles just arrived, handpicked for the season</p>
        </div>

        <div class="products-grid">
            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-men.png') }}" alt="Classic Navy Blazer">
                    <span class="product-badge">New</span>
                </div>
                <p class="product-category">Menswear</p>
                <h3 class="product-name">Classic Navy Blazer</h3>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-women.png') }}" alt="Silk Evening Dress">
                </div>
                <p class="product-category">Womenswear</p>
                <h3 class="product-name">Silk Evening Dress</h3>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-accessories.png') }}" alt="Leather Watch Set">
                    <span class="product-badge">Bestseller</span>
                </div>
                <p class="product-category">Accessories</p>
                <h3 class="product-name">Leather Watch Set</h3>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/hero-fashion.png') }}" alt="Cashmere Cardigan">
                </div>
                <p class="product-category">Knitwear</p>
                <h3 class="product-name">Cashmere Cardigan</h3>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-image">
            <img src="{{ asset('images/hero-fashion.png') }}" alt="Siskha Store - Our Story">
        </div>
        <div class="about-content fade-in">
            <p class="about-eyebrow">Our Story</p>
            <h2 class="about-title">Crafting Elegance Since 2026</h2>
            <p class="about-text">
                At Siskha, we believe that true luxury lies in simplicity. Every piece in our collection 
                is thoughtfully designed to transcend seasons and trends, offering timeless elegance 
                that speaks to the discerning individual.
            </p>
            <p class="about-text">
                Our commitment to quality craftsmanship and sustainable practices ensures that each 
                garment not only looks exceptional but feels exceptional—made to be cherished for years to come.
            </p>
            <div class="about-stats">
                <div class="stat-item">
                    <p class="stat-number">250+</p>
                    <p class="stat-label">Products</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">15K+</p>
                    <p class="stat-label">Customers</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">100%</p>
                    <p class="stat-label">Quality</p>
                </div>
            </div>
        </div>
    </section>

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
                    <li><a href="#">Women</a></li>
                    <li><a href="#">Men</a></li>
                    <li><a href="#">Accessories</a></li>
                    <li><a href="#">Latest Arrivals</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Company</h4>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Store Locations</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-title">Support</h4>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
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
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

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

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
