@extends('layouts.app')

@section('title', 'TokoSaya - Belanja Online Terpercaya')
@section('description', 'Temukan ribuan produk berkualitas dengan harga terjangkau. Belanja mudah, aman, dan nyaman di TokoSaya.')

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="white" stop-opacity="0.1"/><stop offset="100%" stop-color="white" stop-opacity="0"/></radialGradient></defs><circle cx="10" cy="10" r="10" fill="url(%23a)"/><circle cx="90" cy="10" r="10" fill="url(%23a)"/></svg>');
        opacity: 0.1;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 30px;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .btn-hero {
        background: var(--accent-color);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: transform 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
        z-index: 2;
    }

    .btn-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        color: white;
    }

    /* Stats Section */
    .stats-section {
        background: white;
        margin-top: -50px;
        position: relative;
        z-index: 10;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .stat-item {
        text-align: center;
        padding: 30px 20px;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        display: block;
    }

    .stat-label {
        color: var(--secondary-color);
        font-weight: 500;
        margin-top: 5px;
    }

    /* Banner Carousel */
    .banner-carousel {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .banner-slide {
        position: relative;
        height: 400px;
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        display: flex;
        align-items: center;
        color: white;
        background-size: cover;
        background-position: center;
    }

    .banner-slide::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1;
    }

    .banner-content {
        z-index: 2;
        position: relative;
    }

    .banner-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .banner-subtitle {
        font-size: 1.1rem;
        margin-bottom: 25px;
        opacity: 0.9;
    }

    .btn-banner {
        background: var(--accent-color);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .btn-banner:hover {
        transform: translateY(-2px);
        color: white;
    }

    /* Features Section */
    .feature-item {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 15px;
        transition: transform 0.3s ease;
        border: 1px solid var(--border-color);
        height: 100%;
    }

    .feature-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 2rem;
    }

    .feature-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .feature-description {
        color: var(--secondary-color);
        line-height: 1.6;
    }

    /* Layout Grids */
    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        margin: 60px 0;
        border: none;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    /* Newsletter Section */
    .newsletter-section {
        background: linear-gradient(135deg, var(--dark-color) 0%, var(--primary-color) 100%);
        color: white;
        padding: 60px 0;
        border-radius: 20px;
        margin: 60px 0;
    }

    .newsletter-form {
        max-width: 400px;
        margin: 0 auto;
    }

    .newsletter-input {
        border: none;
        border-radius: 25px;
        padding: 15px 20px;
        font-size: 1rem;
        width: 100%;
        margin-bottom: 15px;
    }

    .btn-newsletter {
        background: var(--accent-color);
        border: none;
        color: white;
        padding: 15px 30px;
        border-radius: 25px;
        font-weight: 600;
        width: 100%;
    }

    /* Product Specific Styles */
    .price-display {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .price-compare {
        font-size: 1rem;
        color: var(--secondary-color);
        text-decoration: line-through;
        margin-left: 10px;
    }

    .product-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .banner-title {
            font-size: 2rem;
        }

        .stat-number {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">Belanja Online Terpercaya</h1>
                <p class="hero-subtitle">Temukan ribuan produk berkualitas dengan harga terjangkau. Belanja mudah, aman, dan nyaman.</p>
                <a href="{{ route('products.index') }}" class="btn-hero">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Mulai Belanja
                </a>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="https://via.placeholder.com/500x400/2563eb/ffffff?text=TokoSaya" alt="TokoSaya" class="img-fluid" style="border-radius: 20px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="container">
    <div class="stats-section">
        <div class="row">
            <div class="col-lg-4">
                <div class="stat-item">
                    <span class="stat-number">{{ number_format($stats['total_products'] ?? 0) }}</span>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stat-item">
                    <span class="stat-number">{{ number_format($stats['total_categories'] ?? 0) }}</span>
                    <div class="stat-label">Categories</div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stat-item">
                    <span class="stat-number">{{ number_format($stats['in_stock'] ?? 0) }}</span>
                    <div class="stat-label">In Stock</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Banners Section -->
@if(isset($banners) && $banners->count() > 0)
<section class="container my-5">
    <div id="heroCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $index === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach($banners as $index => $banner)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <div class="banner-slide" style="background-image: url('{{ $banner->image ?? 'https://via.placeholder.com/1200x400/667eea/ffffff?text=Banner' }}');">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="banner-content">
                                    <h2 class="banner-title">{{ $banner->title }}</h2>
                                    <p class="banner-subtitle">{{ $banner->subtitle }}</p>
                                    @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" class="btn-banner">
                                        {{ $banner->link_text ?? 'Shop Now' }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="container my-5 py-5">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        @forelse($categories as $category)
        <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
            <div class="category-card">
                <div class="category-icon">
                    @if($category->icon)
                        <i class="{{ $category->icon }}"></i>
                    @else
                        <i class="fas fa-tag"></i>
                    @endif
                </div>
                <h5 class="category-name">{{ $category->name }}</h5>
                <p class="category-count">{{ $category->products_count ?? 0 }} products</p>
            </div>
        </a>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">No categories available.</p>
        </div>
        @endforelse
    </div>
</section>

<hr class="section-divider">

<!-- Featured Products Section -->
<section class="container my-5 py-5">
    <h2 class="section-title">Featured Products</h2>
    <div class="product-grid">
        @forelse($featuredProducts as $product)
        <div class="product-card" data-product-id="{{ $product->id }}">
            <div class="product-image">
                @if($product->featured)
                <div class="product-badge">HOT</div>
                @endif

                <img src="{{ $product->images->first()->image_url ?? 'https://via.placeholder.com/300x300/e2e8f0/64748b?text=No+Image' }}"
                     alt="{{ $product->name }}" loading="lazy">

                <!-- Wishlist Button -->
                @auth
                <button class="btn-wishlist position-absolute top-0 end-0 m-2"
                        onclick="toggleWishlist({{ $product->id }})"
                        title="Add to Wishlist">
                    <i class="far fa-heart"></i>
                </button>
                @endauth
            </div>

            <div class="product-info">
                <h5 class="product-title">
                    <a href="{{ route('products.show', $product->slug) }}">
                        {{ $product->name }}
                    </a>
                </h5>

                @if($product->category || $product->brand)
                <div class="product-meta">
                    @if($product->category)
                    {{ $product->category->name }}
                    @endif
                    @if($product->brand)
                    @if($product->category) • @endif{{ $product->brand->name }}
                    @endif
                </div>
                @endif

                <div class="product-price">
                    Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                    <span class="price-compare">Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}</span>
                    @endif
                </div>

                @if($product->rating_average > 0)
                <div class="product-rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $product->rating_average)
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $product->rating_average)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="rating-text">({{ $product->rating_count }})</span>
                </div>
                @endif

                <div class="product-actions">
                    @if($product->stock_quantity > 0)
                        <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                            <i class="fas fa-cart-plus me-1"></i>Cart
                        </button>
                    @else
                        <button class="btn-add-cart" disabled>
                            <i class="fas fa-times me-1"></i>Sold Out
                        </button>
                    @endif

                    @auth
                    <button class="btn-wishlist" onclick="toggleWishlist({{ $product->id }})" title="Wishlist">
                        <i class="far fa-heart"></i>
                    </button>
                    @endauth

                    <a href="{{ route('products.show', $product->slug) }}" class="btn-detail" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">No featured products available.</p>
        </div>
        @endforelse
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
            View All Products <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</section>

<hr class="section-divider">

<!-- Features Section -->
<section class="container my-5 py-5 bg-light-gray" style="border-radius: 20px;">
    <h2 class="section-title">Why Choose TokoSaya?</h2>
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h5 class="feature-title">Free Shipping</h5>
                <p class="feature-description">Free shipping for orders over Rp 250,000. Fast and reliable delivery to your doorstep.</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h5 class="feature-title">24/7 Support</h5>
                <p class="feature-description">Our customer support team is available 24/7 to help you with any questions or concerns.</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 class="feature-title">Secure Payments</h5>
                <p class="feature-description">Your payment information is protected with bank-level security and encryption.</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <h5 class="feature-title">Easy Returns</h5>
                <p class="feature-description">Not satisfied? Return your items within 30 days for a full refund or exchange.</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="container">
    <div class="newsletter-section">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h3 class="mb-3">Stay Updated with Our Newsletter</h3>
                <p class="mb-4">Get the latest updates on new products, special offers, and exclusive deals.</p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                    @csrf
                    <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-newsletter">
                        <i class="fas fa-paper-plane me-2"></i>Subscribe Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-advance carousel
        const carousel = document.querySelector('#heroCarousel');
        if (carousel) {
            const carouselInstance = new bootstrap.Carousel(carousel, {
                interval: 5000,
                ride: 'carousel'
            });
        }

        // Load wishlist status for products
        @auth
        loadWishlistStatus();
        @endauth
    });

    // Load wishlist status for current products
    @auth
    function loadWishlistStatus() {
        const productIds = Array.from(document.querySelectorAll('[data-product-id]')).map(el => el.dataset.productId);

        if (productIds.length > 0) {
            fetch('/wishlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_ids: productIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.wishlist.forEach(productId => {
                        const heartIcon = document.querySelector(`[data-product-id="${productId}"] .btn-wishlist i`);
                        const heartBtn = document.querySelector(`[data-product-id="${productId}"] .btn-wishlist`);
                        if (heartIcon && heartBtn) {
                            heartIcon.classList.remove('far');
                            heartIcon.classList.add('fas');
                            heartBtn.classList.add('active');
                        }
                    });
                }
            })
            .catch(error => console.log('Error loading wishlist status:', error));
        }
    }
    @endauth
</script>
@endpush
