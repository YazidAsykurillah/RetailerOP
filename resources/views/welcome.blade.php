<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Siskha Store - Modern POS & Inventory Management System for retail businesses.">
    
    <title>{{ config('app.name', 'Siskha Store') }} - POS & Inventory Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            --color-accent: #3b82f6;
            --color-accent-light: #60a5fa;
            --color-black: #0a0a0a;
            --color-white: #ffffff;
            --color-gray-50: #f9fafb;
            --color-gray-100: #f3f4f6;
            --color-gray-200: #e5e7eb;
            --color-gray-300: #d1d5db;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-gray-700: #374151;
            --color-gray-800: #1f2937;
            --color-gray-900: #111827;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            --gradient-hero: linear-gradient(135deg, #1a3a5c 0%, #2a5a8c 50%, #3b82f6 100%);
            
            /* Typography */
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --space-xs: 0.5rem;
            --space-sm: 1rem;
            --space-md: 1.5rem;
            --space-lg: 2rem;
            --space-xl: 3rem;
            --space-2xl: 5rem;
            
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
            color: var(--color-gray-800);
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

        /* Navigation */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: var(--space-sm) var(--space-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary-dark);
            display: flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .nav-logo svg {
            width: 32px;
            height: 32px;
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
            padding: 0.625rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--color-gray-300);
            color: var(--color-gray-700);
        }

        .btn-outline:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-gray-50);
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: var(--color-white);
            box-shadow: 0 2px 8px rgba(26, 58, 92, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(26, 58, 92, 0.35);
        }

        .btn-lg {
            padding: 0.875rem 2rem;
            font-size: 1rem;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px var(--space-lg) var(--space-2xl);
            background: var(--gradient-hero);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .hero-content {
            max-width: 800px;
            text-align: center;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease forwards;
        }

        .hero-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-white);
            margin-bottom: var(--space-md);
            backdrop-filter: blur(4px);
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 700;
            color: var(--color-white);
            line-height: 1.1;
            margin-bottom: var(--space-md);
        }

        .hero-title span {
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 1.25rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.85);
            max-width: 600px;
            margin: 0 auto var(--space-xl);
            line-height: 1.7;
        }

        .hero-cta {
            display: flex;
            gap: var(--space-sm);
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-cta .btn-primary {
            background: var(--color-white);
            color: var(--color-primary-dark);
        }

        .hero-cta .btn-primary:hover {
            background: var(--color-gray-100);
        }

        .hero-cta .btn-outline {
            border-color: rgba(255, 255, 255, 0.4);
            color: var(--color-white);
        }

        .hero-cta .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--color-white);
        }

        /* Features Section */
        .features {
            padding: var(--space-2xl) var(--space-lg);
            background: var(--color-white);
        }

        .features-header {
            text-align: center;
            margin-bottom: var(--space-2xl);
        }

        .features-eyebrow {
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--color-primary);
            margin-bottom: var(--space-sm);
        }

        .features-title {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            color: var(--color-gray-900);
            margin-bottom: var(--space-sm);
        }

        .features-subtitle {
            font-size: 1.125rem;
            color: var(--color-gray-500);
            max-width: 500px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--space-lg);
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            padding: var(--space-lg);
            background: var(--color-gray-50);
            border-radius: 16px;
            border: 1px solid var(--color-gray-100);
            transition: all var(--transition-smooth);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            border-color: var(--color-primary-light);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-primary);
            border-radius: 12px;
            margin-bottom: var(--space-md);
        }

        .feature-icon svg {
            width: 28px;
            height: 28px;
            color: var(--color-white);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-gray-900);
            margin-bottom: var(--space-xs);
        }

        .feature-description {
            font-size: 0.9375rem;
            color: var(--color-gray-500);
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: var(--color-gray-900);
            color: var(--color-white);
            padding: var(--space-xl) var(--space-lg);
            text-align: center;
        }

        .footer-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .footer-logo {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--space-sm);
        }

        .footer-copy {
            font-size: 0.875rem;
            color: var(--color-gray-400);
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

        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                padding: var(--space-sm);
            }

            .nav-logo {
                font-size: 1.25rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.8125rem;
            }

            .hero {
                padding: 100px var(--space-sm) var(--space-xl);
            }

            .hero-description {
                font-size: 1.0625rem;
            }

            .features {
                padding: var(--space-xl) var(--space-sm);
            }

            .feature-card {
                padding: var(--space-md);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            SISKHA
        </div>
        <div class="nav-auth">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">✨ Modern Retail Solution</div>
            <h1 class="hero-title">
                POS & Inventory<br><span>Management System</span>
            </h1>
            <p class="hero-description">
                Streamline your retail operations with our comprehensive point-of-sale and inventory management platform. Built for efficiency, designed for growth.
            </p>
            <div class="hero-cta">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login to Dashboard</a>
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-header">
            <div class="features-eyebrow">Features</div>
            <h2 class="features-title">Everything You Need</h2>
            <p class="features-subtitle">Powerful tools to manage your retail business efficiently</p>
        </div>
        
        <div class="features-grid">
            <!-- POS -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <h3 class="feature-title">Point of Sale</h3>
                <p class="feature-description">Fast and intuitive POS interface with product search, variants, discounts, and flexible payment options.</p>
            </div>

            <!-- Inventory -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <h3 class="feature-title">Inventory Management</h3>
                <p class="feature-description">Track stock levels, manage stock-in/out operations, and monitor inventory movement history.</p>
            </div>

            <!-- Products -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <h3 class="feature-title">Product Catalog</h3>
                <p class="feature-description">Manage products with categories, brands, variants, images, and flexible pricing options.</p>
            </div>

            <!-- Suppliers -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 3h15v13H1z"></path>
                        <path d="M16 8h4l3 3v5h-7V8z"></path>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <h3 class="feature-title">Supplier Management</h3>
                <p class="feature-description">Keep track of suppliers and link them to stock movements for better procurement tracking.</p>
            </div>

            <!-- Transactions -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Transaction History</h3>
                <p class="feature-description">Complete transaction records with detailed receipts, filters, and printable invoices.</p>
            </div>

            <!-- Users & Roles -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Users & Permissions</h3>
                <p class="feature-description">Role-based access control with customizable permissions for secure multi-user operation.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">SISKHA</div>
            <p class="footer-copy">&copy; {{ date('Y') }} SISKHA. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
