@extends('frontend.layouts.app')

@section('title', 'Collections')
@section('meta_description', 'Explore our curated fashion collections. Discover timeless pieces for Women, Men, and Accessories at Siskha Store.')

@section('styles')
<style>
    /* Collections Grid */
    .collections-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--space-lg);
        padding: 0 8%;
        margin-bottom: var(--space-3xl);
    }

    .collection-card {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3/4;
        cursor: pointer;
    }

    .collection-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-smooth);
    }

    .collection-card:hover img {
        transform: scale(1.05);
    }

    .collection-card::before {
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

    .collection-card:hover::before {
        background: linear-gradient(to top, rgba(26, 58, 92, 0.8) 0%, rgba(26, 58, 92, 0.2) 100%);
    }

    .collection-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: var(--space-lg);
        z-index: 2;
        color: var(--color-white);
    }

    .collection-name {
        font-family: var(--font-serif);
        font-size: 2rem;
        font-weight: 400;
        margin-bottom: var(--space-xs);
    }

    .collection-count {
        font-size: 0.875rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        opacity: 0.8;
    }

    .collection-description {
        font-size: 0.9375rem;
        line-height: 1.6;
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transition: all var(--transition-smooth);
        margin-top: var(--space-sm);
    }

    .collection-card:hover .collection-description {
        opacity: 0.9;
        max-height: 100px;
    }

    /* Featured Section */
    .featured-section {
        background: var(--color-gray-50);
        padding: var(--space-3xl) 8%;
    }

    .featured-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-2xl);
        align-items: center;
    }

    .featured-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4/5;
    }

    .featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured-content {
        padding: var(--space-xl);
    }

    .featured-eyebrow {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: var(--color-primary);
        margin-bottom: var(--space-md);
    }

    .featured-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 2.5rem);
        font-weight: 300;
        color: var(--color-black);
        margin-bottom: var(--space-lg);
        line-height: 1.2;
    }

    .featured-text {
        font-size: 1rem;
        font-weight: 300;
        color: var(--color-gray-600);
        line-height: 1.8;
        margin-bottom: var(--space-xl);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .collections-grid {
            grid-template-columns: 1fr;
        }

        .collection-card {
            aspect-ratio: 16/9;
        }

        .featured-grid {
            grid-template-columns: 1fr;
        }

        .featured-image {
            aspect-ratio: 16/9;
        }
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <header class="page-header">
        <p class="page-header-eyebrow fade-in">Explore</p>
        <h1 class="page-header-title fade-in">Our Collections</h1>
        <p class="page-header-subtitle fade-in">
            Discover our thoughtfully curated collections, each designed to bring elegance and sophistication to your wardrobe.
        </p>
    </header>

    <!-- Collections Grid -->
    <section class="section">
        <div class="collections-grid">
            <div class="collection-card fade-in">
                <img src="{{ asset('images/category-women.png') }}" alt="Women's Collection">
                <div class="collection-content">
                    <h2 class="collection-name">Women</h2>
                    <p class="collection-count">120+ Pieces</p>
                    <p class="collection-description">
                        Elegant silhouettes and timeless designs crafted for the modern woman.
                    </p>
                </div>
            </div>

            <div class="collection-card fade-in">
                <img src="{{ asset('images/category-men.png') }}" alt="Men's Collection">
                <div class="collection-content">
                    <h2 class="collection-name">Men</h2>
                    <p class="collection-count">85+ Pieces</p>
                    <p class="collection-description">
                        Refined tailoring and sophisticated essentials for the discerning gentleman.
                    </p>
                </div>
            </div>

            <div class="collection-card fade-in">
                <img src="{{ asset('images/category-accessories.png') }}" alt="Accessories Collection">
                <div class="collection-content">
                    <h2 class="collection-name">Accessories</h2>
                    <p class="collection-count">60+ Pieces</p>
                    <p class="collection-description">
                        Curated accessories to complete your look with understated luxury.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Collection -->
    <section class="featured-section">
        <div class="featured-grid">
            <div class="featured-image fade-in">
                <img src="{{ asset('images/hero-fashion.png') }}" alt="Seasonal Collection">
            </div>
            <div class="featured-content fade-in">
                <p class="featured-eyebrow">Seasonal Highlight</p>
                <h2 class="featured-title">The Essentials Collection</h2>
                <p class="featured-text">
                    Our Essentials Collection represents the foundation of a timeless wardrobe. 
                    Each piece is designed with versatility in mind, allowing you to create 
                    countless combinations while maintaining an air of effortless sophistication.
                </p>
                <p class="featured-text">
                    From perfectly tailored blazers to flowing silk blouses, every garment 
                    is crafted using premium materials and meticulous attention to detail.
                </p>
            </div>
        </div>
    </section>
@endsection
