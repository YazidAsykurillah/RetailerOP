@extends('frontend.layouts.app')

@section('title', 'About Us')
@section('meta_description', 'Learn about Siskha Store - our story, values, and commitment to timeless fashion and sustainable luxury.')

@section('styles')
<style>
    /* Hero Split Section */
    .about-hero {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 80vh;
        margin-top: 80px;
    }

    .about-hero-image {
        position: relative;
        overflow: hidden;
    }

    .about-hero-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .about-hero-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: var(--space-3xl);
        background: var(--color-gray-50);
    }

    .about-hero-eyebrow {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: var(--color-primary);
        margin-bottom: var(--space-md);
    }

    .about-hero-title {
        font-family: var(--font-serif);
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 300;
        color: var(--color-black);
        margin-bottom: var(--space-lg);
        line-height: 1.1;
    }

    .about-hero-text {
        font-size: 1.125rem;
        font-weight: 300;
        color: var(--color-gray-600);
        line-height: 1.8;
        max-width: 480px;
    }

    /* Values Section */
    .values-section {
        padding: var(--space-3xl) 8%;
        background: var(--color-white);
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--space-2xl);
        margin-top: var(--space-2xl);
    }

    .value-card {
        text-align: center;
        padding: var(--space-xl);
    }

    .value-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto var(--space-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--color-gray-100);
        border-radius: 50%;
        color: var(--color-primary);
    }

    .value-icon svg {
        width: 28px;
        height: 28px;
    }

    .value-title {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 400;
        color: var(--color-black);
        margin-bottom: var(--space-sm);
    }

    .value-text {
        font-size: 0.9375rem;
        font-weight: 300;
        color: var(--color-gray-600);
        line-height: 1.7;
    }

    /* Story Section */
    .story-section {
        background: var(--color-primary-dark);
        color: var(--color-white);
        padding: var(--space-3xl) 8%;
    }

    .story-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-3xl);
        align-items: center;
    }

    .story-content {
        padding-right: var(--space-xl);
    }

    .story-eyebrow {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: var(--space-md);
    }

    .story-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 2.5rem);
        font-weight: 300;
        margin-bottom: var(--space-lg);
        line-height: 1.2;
    }

    .story-text {
        font-size: 1rem;
        font-weight: 300;
        line-height: 1.9;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: var(--space-lg);
    }

    .story-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4/5;
    }

    .story-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Stats Section */
    .stats-section {
        padding: var(--space-3xl) 8%;
        background: var(--color-gray-50);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-xl);
        text-align: center;
    }

    .stat-item {
        padding: var(--space-lg);
    }

    .stat-number {
        font-family: var(--font-serif);
        font-size: 3.5rem;
        font-weight: 300;
        color: var(--color-primary-dark);
        margin-bottom: var(--space-xs);
    }

    .stat-label {
        font-size: 0.875rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-gray-600);
    }

    /* Team Section */
    .team-section {
        padding: var(--space-3xl) 8%;
    }

    .team-intro {
        text-align: center;
        max-width: 600px;
        margin: 0 auto var(--space-2xl);
    }

    .team-intro-text {
        font-size: 1.125rem;
        font-weight: 300;
        color: var(--color-gray-600);
        line-height: 1.8;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .about-hero {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .about-hero-image {
            height: 50vh;
        }

        .about-hero-content {
            padding: var(--space-2xl);
        }

        .values-grid {
            grid-template-columns: 1fr;
        }

        .story-grid {
            grid-template-columns: 1fr;
        }

        .story-image {
            order: -1;
            aspect-ratio: 16/9;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-number {
            font-size: 2.5rem;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-hero-image">
            <img src="{{ asset('images/hero-fashion.png') }}" alt="Siskha Store - Our Story">
        </div>
        <div class="about-hero-content fade-in">
            <p class="about-hero-eyebrow">Our Story</p>
            <h1 class="about-hero-title">Where Elegance Meets Simplicity</h1>
            <p class="about-hero-text">
                Founded in 2026, Siskha was born from a passion for timeless fashion and 
                a belief that true luxury lies in simplicity. We create pieces that transcend 
                trends and seasons, designed for those who appreciate the art of understated elegance.
            </p>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="section-header fade-in">
            <p class="section-eyebrow">What We Stand For</p>
            <h2 class="section-title">Our Values</h2>
        </div>

        <div class="values-grid">
            <div class="value-card fade-in">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <h3 class="value-title">Quality Craftsmanship</h3>
                <p class="value-text">
                    Every piece is meticulously crafted using premium materials and traditional techniques, 
                    ensuring exceptional quality that lasts for years.
                </p>
            </div>

            <div class="value-card fade-in">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </div>
                <h3 class="value-title">Timeless Design</h3>
                <p class="value-text">
                    We design pieces that transcend fleeting trends, creating a wardrobe that remains 
                    elegant and relevant season after season.
                </p>
            </div>

            <div class="value-card fade-in">
                <div class="value-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <h3 class="value-title">Sustainable Luxury</h3>
                <p class="value-text">
                    We're committed to responsible practices, from ethically sourced materials 
                    to sustainable production methods that respect our planet.
                </p>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story-section">
        <div class="story-grid">
            <div class="story-content fade-in">
                <p class="story-eyebrow">The Journey</p>
                <h2 class="story-title">From Vision to Reality</h2>
                <p class="story-text">
                    What started as a small atelier has grown into a destination for those 
                    seeking refined fashion. Our journey began with a simple belief: that 
                    clothing should be an extension of one's character, not a mask to hide behind.
                </p>
                <p class="story-text">
                    Today, we continue to honor that vision by creating collections that speak 
                    to the discerning individual—pieces that are as comfortable as they are elegant, 
                    as practical as they are beautiful.
                </p>
            </div>
            <div class="story-image fade-in">
                <img src="{{ asset('images/category-women.png') }}" alt="Siskha Store Journey">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item fade-in">
                <p class="stat-number">250+</p>
                <p class="stat-label">Products</p>
            </div>
            <div class="stat-item fade-in">
                <p class="stat-number">15K+</p>
                <p class="stat-label">Customers</p>
            </div>
            <div class="stat-item fade-in">
                <p class="stat-number">50+</p>
                <p class="stat-label">Artisans</p>
            </div>
            <div class="stat-item fade-in">
                <p class="stat-number">100%</p>
                <p class="stat-label">Quality</p>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="section-header fade-in">
            <p class="section-eyebrow">The People</p>
            <h2 class="section-title">Behind Siskha</h2>
        </div>
        <div class="team-intro fade-in">
            <p class="team-intro-text">
                Our team of dedicated designers, artisans, and fashion enthusiasts work tirelessly 
                to bring you collections that embody our commitment to elegance and quality. 
                Together, we're redefining what it means to dress with intention.
            </p>
        </div>
    </section>
@endsection
