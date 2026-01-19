@extends('frontend.layouts.app')

@section('title', 'New Arrivals')
@section('meta_description', 'Discover the latest arrivals at Siskha Store. Fresh styles and new additions to our luxury fashion collection.')

@section('styles')
<style>
    /* Products Grid */
    .products-section {
        padding: var(--space-3xl) 8%;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-xl);
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

    .product-category {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--color-gray-600);
        margin-bottom: var(--space-xs);
    }

    .product-name {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        font-weight: 400;
        color: var(--color-black);
        margin-bottom: var(--space-xs);
        transition: color var(--transition-fast);
    }

    .product-card:hover .product-name {
        color: var(--color-primary);
    }

    .product-material {
        font-size: 0.875rem;
        color: var(--color-gray-600);
    }

    /* Filter Section */
    .filter-section {
        display: flex;
        justify-content: center;
        gap: var(--space-lg);
        padding: var(--space-lg) 8%;
        border-bottom: 1px solid var(--color-gray-200);
    }

    .filter-btn {
        padding: var(--space-xs) var(--space-md);
        font-size: 0.875rem;
        font-weight: 400;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--color-gray-600);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast);
        position: relative;
    }

    .filter-btn::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--color-primary);
        transition: width var(--transition-smooth);
    }

    .filter-btn:hover,
    .filter-btn.active {
        color: var(--color-primary);
    }

    .filter-btn.active::after {
        width: 100%;
    }

    /* Announcement Banner */
    .announcement {
        background: var(--color-primary-dark);
        color: var(--color-white);
        text-align: center;
        padding: var(--space-xl) 8%;
        margin-top: var(--space-2xl);
    }

    .announcement-title {
        font-family: var(--font-serif);
        font-size: 1.75rem;
        font-weight: 300;
        margin-bottom: var(--space-sm);
    }

    .announcement-text {
        font-size: 1rem;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .products-grid {
            grid-template-columns: 1fr;
        }

        .filter-section {
            flex-wrap: wrap;
        }
    }
</style>
@endsection

@section('content')
    <!-- Page Header -->
    <header class="page-header">
        <p class="page-header-eyebrow fade-in">Just Arrived</p>
        <h1 class="page-header-title fade-in">New Arrivals</h1>
        <p class="page-header-subtitle fade-in">
            Discover our latest additions, fresh from the design studio. Each piece embodies our commitment to timeless elegance.
        </p>
    </header>

    <!-- Filter Section -->
    <div class="filter-section">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Women</button>
        <button class="filter-btn">Men</button>
        <button class="filter-btn">Accessories</button>
    </div>

    <!-- Products Grid -->
    <section class="products-section">
        <div class="products-grid">
            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-men.png') }}" alt="Classic Navy Blazer">
                    <span class="product-badge">New</span>
                </div>
                <p class="product-category">Menswear</p>
                <h3 class="product-name">Classic Navy Blazer</h3>
                <p class="product-material">Premium Italian Wool</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-women.png') }}" alt="Silk Evening Dress">
                    <span class="product-badge">New</span>
                </div>
                <p class="product-category">Womenswear</p>
                <h3 class="product-name">Silk Evening Dress</h3>
                <p class="product-material">100% Mulberry Silk</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-accessories.png') }}" alt="Leather Watch Set">
                    <span class="product-badge">New</span>
                </div>
                <p class="product-category">Accessories</p>
                <h3 class="product-name">Leather Watch Set</h3>
                <p class="product-material">Genuine Italian Leather</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/hero-fashion.png') }}" alt="Cashmere Cardigan">
                    <span class="product-badge">New</span>
                </div>
                <p class="product-category">Knitwear</p>
                <h3 class="product-name">Cashmere Cardigan</h3>
                <p class="product-material">Mongolian Cashmere</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-women.png') }}" alt="Tailored Trousers">
                </div>
                <p class="product-category">Womenswear</p>
                <h3 class="product-name">Tailored Trousers</h3>
                <p class="product-material">Wool Blend</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-men.png') }}" alt="Oxford Shirt">
                </div>
                <p class="product-category">Menswear</p>
                <h3 class="product-name">Oxford Shirt</h3>
                <p class="product-material">Egyptian Cotton</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/category-accessories.png') }}" alt="Leather Belt">
                </div>
                <p class="product-category">Accessories</p>
                <h3 class="product-name">Leather Belt</h3>
                <p class="product-material">Full-grain Leather</p>
            </div>

            <div class="product-card fade-in">
                <div class="product-image">
                    <img src="{{ asset('images/hero-fashion.png') }}" alt="Merino Sweater">
                </div>
                <p class="product-category">Knitwear</p>
                <h3 class="product-name">Merino Sweater</h3>
                <p class="product-material">Australian Merino Wool</p>
            </div>
        </div>
    </section>

    <!-- Announcement -->
    <div class="announcement fade-in">
        <h2 class="announcement-title">New pieces added weekly</h2>
        <p class="announcement-text">Follow us on social media to stay updated on our latest arrivals</p>
    </div>
@endsection

@section('scripts')
<script>
    // Simple filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
</script>
@endsection
