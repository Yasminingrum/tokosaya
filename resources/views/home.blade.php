@extends('layouts.app')

@section('title', 'TokoSaya - Belanja Mudah, Terpercaya')
@section('description', 'Platform e-commerce modern dengan ribuan produk berkualitas, pengiriman cepat, dan pengalaman berbelanja terbaik.')

@push('styles')
<style>
    /* Base Styles */
    :root {
        --primary: #f8bbd9;
        --primary-dark: #f4a6cd;
        --primary-light: #fce7f1;
        --primary-accent: #e899c2;
        --teal: #5fb3b4;
        --teal-dark: #4a9b9c;
        --teal-light: #b8e0e1;
        --teal-accent: #7bc5c6;
        --cream: #fef7f0;
        --cream-dark: #f9ede3;
        --cream-light: #fffcf9;
        --beige: #f5ebe0;
        --text-dark: #2d3748;
        --text-medium: #4a5568;
        --text-light: #718096;
        --text-muted: #a0aec0;
        --success: #68d391;
        --warning: #fbb040;
        --danger: #fc8181;
        --font-primary: 'Poppins', 'Inter', system-ui, sans-serif;
        --font-secondary: 'Inter', system-ui, sans-serif;
        --space-xs: 0.25rem;
        --space-sm: 0.5rem;
        --space-md: 1rem;
        --space-lg: 1.5rem;
        --space-xl: 2rem;
        --space-2xl: 3rem;
        --space-3xl: 4rem;
        --radius-sm: 0.5rem;
        --radius-md: 0.75rem;
        --radius-lg: 1rem;
        --radius-xl: 1.5rem;
        --radius-2xl: 2rem;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
        --shadow-xl: 0 12px 32px rgba(0, 0, 0, 0.16);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.2s ease-out;
    }

    body {
        font-family: var(--font-primary);
        font-weight: 400;
        line-height: 1.6;
        color: var(--text-dark);
        background-color: var(--cream);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-sm);
        font-family: var(--font-primary);
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1;
        border: none;
        border-radius: var(--radius-lg);
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        padding: 0.75rem 1.5rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1rem;
        border-radius: var(--radius-xl);
    }

    .btn-primary {
        background-color: var(--primary);
        color: var(--text-dark);
        box-shadow: var(--shadow-sm);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        color: var(--text-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-outline-teal {
        background-color: transparent;
        color: var(--teal);
        border: 2px solid var(--teal);
    }

    .btn-outline-teal:hover {
        background-color: var(--teal);
        color: white;
        border-color: var(--teal-dark);
    }

    .btn-teal {
        background-color: var(--teal);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .btn-teal:hover {
        background-color: var(--teal-dark);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* Hero Section */
    .hero {
        background-color: var(--cream-light);
        padding: var(--space-3xl) 0;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23f8bbd9" stroke-width="0.5" opacity="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.6;
        pointer-events: none;
    }

    .hero-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-3xl);
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .hero-text {
        max-width: 500px;
    }

    .hero-subtitle {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--teal);
        margin-bottom: var(--space-md);
    }

    .hero-title {
        font-weight: 700;
        font-size: clamp(2rem, 5vw, 3.5rem);
        line-height: 1.1;
        margin-bottom: var(--space-lg);
        color: var(--text-dark);
    }

    .hero-title .highlight {
        color: var(--primary-dark);
    }

    .hero-description {
        font-size: 1.125rem;
        color: var(--text-medium);
        margin-bottom: var(--space-xl);
        line-height: 1.6;
    }

    .hero-actions {
        display: flex;
        gap: var(--space-md);
        flex-wrap: wrap;
    }

    /* Features Section */
    .features {
        padding: var(--space-3xl) 0;
        background-color: white;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-xl);
    }

    .feature-item {
        text-align: center;
        padding: var(--space-xl);
        border-radius: var(--radius-xl);
        transition: var(--transition);
        position: relative;
    }

    .feature-item:hover {
        transform: translateY(-4px);
    }

    .feature-icon {
        width: 4rem;
        height: 4rem;
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-lg);
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .feature-item:nth-child(1) .feature-icon {
        background-color: var(--primary-light);
        color: var(--primary-dark);
    }

    .feature-item:nth-child(2) .feature-icon {
        background-color: var(--teal-light);
        color: var(--teal-dark);
    }

    .feature-item:nth-child(3) .feature-icon {
        background-color: #fef3cd;
        color: var(--warning);
    }

    .feature-item:nth-child(4) .feature-icon {
        background-color: #d4edda;
        color: var(--success);
    }

    .feature-item:hover .feature-icon {
        transform: scale(1.1);
    }

    .feature-title {
        font-weight: 600;
        font-size: 1.125rem;
        color: var(--text-dark);
        margin-bottom: var(--space-sm);
    }

    .feature-description {
        color: var(--text-medium);
        font-size: 0.875rem;
    }

    /* Section Styles */
    .section {
        padding: var(--space-3xl) 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: var(--space-2xl);
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: var(--space-md);
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: var(--text-medium);
        line-height: 1.6;
    }

    /* Categories and Products Grid */
    .categories-grid, .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-lg);
    }

    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    /* Category Card */
    .category-card {
        background-color: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--cream-dark);
        transition: var(--transition);
        overflow: hidden;
        text-align: center;
        padding: var(--space-xl);
        position: relative;
        text-decoration: none;
        display: block;
        color: inherit;
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        text-decoration: none;
        color: inherit;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--teal));
        opacity: 0;
        transition: var(--transition);
    }

    .category-card:hover::before {
        opacity: 1;
    }

    .category-icon {
        width: 4rem;
        height: 4rem;
        background-color: var(--primary-light);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-lg);
        transition: var(--transition);
        color: var(--primary-dark);
        font-size: 1.5rem;
    }

    .category-card:hover .category-icon {
        background-color: var(--teal);
        color: white;
        transform: scale(1.1);
    }

    .category-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-dark);
        margin-bottom: var(--space-sm);
    }

    .category-count {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    /* Product Card */
    .product-card {
        background-color: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--cream-dark);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-6px);
    }

    .product-image {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background-color: var(--cream);
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-info {
        padding: var(--space-lg);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: var(--space-xs);
        font-weight: 500;
    }

    .product-title {
        font-weight: 600;
        font-size: 0.95rem;
        line-height: 1.4;
        color: var(--text-dark);
        margin-bottom: var(--space-sm);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        margin-bottom: var(--space-lg);
        flex-wrap: wrap;
    }

    .product-price-current {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--teal);
    }

    .product-price-original {
        font-size: 0.875rem;
        color: var(--text-muted);
        text-decoration: line-through;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: var(--space-xs);
        margin-bottom: var(--space-sm);
    }

    .product-rating .stars {
        display: flex;
        gap: 1px;
    }

    .product-rating .star {
        color: #fbbf24;
        font-size: 0.8rem;
    }

    .product-rating .count {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.3rem 0.8rem;
        border-radius: var(--radius-lg);
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge-danger {
        background-color: var(--danger);
        color: white;
    }

    .badge-success {
        background-color: var(--success);
        color: white;
    }

    /* Newsletter Section */
    .newsletter {
        background: linear-gradient(135deg, var(--teal), var(--teal-dark));
        color: white;
        padding: var(--space-3xl) 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .newsletter-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: var(--space-2xl);
            text-align: center;
        }

        .hero {
            padding: var(--space-2xl) 0;
        }

        .section {
            padding: var(--space-2xl) 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .features-grid {
            grid-template-columns: 1fr;
            gap: var(--space-lg);
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <p class="hero-subtitle">Selamat Datang di</p>
                <h1 class="hero-title">
                    Toko<span class="highlight">Saya</span>
                </h1>
                <p class="hero-description">
                    Temukan produk berkualitas dengan pengalaman berbelanja yang tak terlupakan.
                    Platform e-commerce terpercaya dengan ribuan produk pilihan.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i>
                        Jelajahi Produk
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-teal btn-lg">
                        <i class="fas fa-tags"></i>
                        Lihat Kategori
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>TokoSaya</h3>
                    <p>Belanja Mudah & Terpercaya</p>
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: var(--space-md);">
                        {{ number_format($featured_products->count() + $new_arrivals->count() + $best_sellers->count()) }}+ Produk •
                        50.000+ Customer • Rating 4.8/5
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="feature-title">Pengiriman Cepat</h3>
                <p class="feature-description">Gratis ongkir minimal Rp 250rb ke seluruh Indonesia</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">100% Aman</h3>
                <p class="feature-description">Transaksi terenkripsi dan data pribadi terlindungi</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h3 class="feature-title">Kualitas Terjamin</h3>
                <p class="feature-description">Produk 100% original dengan garansi resmi</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title">Support 24/7</h3>
                <p class="feature-description">Tim customer service siap membantu kapan saja</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Jelajahi Kategori</h2>
            <p class="section-subtitle">Temukan produk sesuai kebutuhan Anda dalam berbagai kategori pilihan</p>
        </div>
        <div class="categories-grid">
            @forelse($featured_categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                <div class="category-icon">
                    @if($category->icon)
                        <i class="{{ $category->icon }}"></i>
                    @else
                        <i class="fas fa-tag"></i>
                    @endif
                </div>
                <h3 class="category-name">{{ $category->name }}</h3>
                <p class="category-count">{{ $category->product_count ?? 0 }} produk</p>
            </a>
            @empty
            <!-- Default categories if no database categories -->
            <a href="{{ route('categories.index') }}" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3 class="category-name">Elektronik</h3>
                <p class="category-count">0 produk</p>
            </a>
            <a href="{{ route('categories.index') }}" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3 class="category-name">Fashion</h3>
                <p class="category-count">0 produk</p>
            </a>
            <a href="{{ route('categories.index') }}" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h3 class="category-name">Rumah Tangga</h3>
                <p class="category-count">0 produk</p>
            </a>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Products Section -->
@if($featured_products && $featured_products->count() > 0)
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produk Unggulan</h2>
            <p class="section-subtitle">Produk pilihan terbaik dengan kualitas premium dan harga terjangkau</p>
        </div>
        <div class="products-grid">
            @foreach($featured_products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                             alt="{{ $product->images->first()->alt_text ?? $product->name }}"
                             loading="lazy">
                    @else
                        <img src="https://via.placeholder.com/400x400/f8bbd9/2d3748?text=No+Image"
                             alt="{{ $product->name }}"
                             loading="lazy">
                    @endif

                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                    <div class="product-badge" style="position: absolute; top: 1rem; left: 1rem; z-index: 10;">
                        <span class="badge badge-danger">
                            Sale -{{ round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100) }}%
                        </span>
                    </div>
                    @endif
                </div>
                <div class="product-info">
                    @if($product->category)
                    <p class="product-category">{{ $product->category->name }}</p>
                    @endif

                    <h3 class="product-title">
                        <a href="{{ route('products.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                            {{ $product->name }}
                        </a>
                    </h3>

                    @if($product->rating_average > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->rating_average))
                                    <i class="fas fa-star star"></i>
                                @elseif($i <= ceil($product->rating_average))
                                    <i class="fas fa-star-half-alt star"></i>
                                @else
                                    <i class="far fa-star star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="count">({{ $product->rating_count }})</span>
                    </div>
                    @endif

                    <div class="product-price">
                        <span class="product-price-current">
                            Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                        </span>
                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                        <span class="product-price-original">
                            Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>

                    <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: var(--space-2xl);">
            <a href="{{ route('products.index') }}" class="btn btn-teal btn-lg">
                <i class="fas fa-eye"></i>
                Lihat Semua Produk
            </a>
        </div>
    </div>
</section>
@endif

<!-- New Arrivals Section -->
@if($new_arrivals && $new_arrivals->count() > 0)
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produk Terbaru</h2>
            <p class="section-subtitle">Koleksi terbaru yang baru saja tiba di TokoSaya</p>
        </div>
        <div class="products-grid">
            @foreach($new_arrivals as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                             alt="{{ $product->images->first()->alt_text ?? $product->name }}"
                             loading="lazy">
                    @else
                        <img src="https://via.placeholder.com/400x400/5fb3b4/ffffff?text=No+Image"
                             alt="{{ $product->name }}"
                             loading="lazy">
                    @endif

                    <div class="product-badge" style="position: absolute; top: 1rem; left: 1rem; z-index: 10;">
                        <span class="badge badge-success">New</span>
                    </div>
                </div>
                <div class="product-info">
                    @if($product->category)
                    <p class="product-category">{{ $product->category->name }}</p>
                    @endif

                    <h3 class="product-title">
                        <a href="{{ route('products.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                            {{ $product->name }}
                        </a>
                    </h3>

                    @if($product->rating_average > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->rating_average))
                                    <i class="fas fa-star star"></i>
                                @elseif($i <= ceil($product->rating_average))
                                    <i class="fas fa-star-half-alt star"></i>
                                @else
                                    <i class="far fa-star star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="count">({{ $product->rating_count }})</span>
                    </div>
                    @endif

                    <div class="product-price">
                        <span class="product-price-current">
                            Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                        </span>
                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                        <span class="product-price-original">
                            Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>

                    <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Best Sellers Section -->
@if($best_sellers && $best_sellers->count() > 0)
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produk Terlaris</h2>
            <p class="section-subtitle">Produk dengan penjualan tertinggi dan paling digemari pelanggan</p>
        </div>
        <div class="products-grid">
            @foreach($best_sellers as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                             alt="{{ $product->images->first()->alt_text ?? $product->name }}"
                             loading="lazy">
                    @else
                        <img src="https://via.placeholder.com/400x400/f8bbd9/2d3748?text=No+Image"
                             alt="{{ $product->name }}"
                             loading="lazy">
                    @endif
                </div>
                <div class="product-info">
                    @if($product->category)
                    <p class="product-category">{{ $product->category->name }}</p>
                    @endif

                    <h3 class="product-title">
                        <a href="{{ route('products.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                            {{ $product->name }}
                        </a>
                    </h3>

                    @if($product->rating_average > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->rating_average))
                                    <i class="fas fa-star star"></i>
                                @elseif($i <= ceil($product->rating_average))
                                    <i class="fas fa-star-half-alt star"></i>
                                @else
                                    <i class="far fa-star star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="count">({{ $product->rating_count }})</span>
                    </div>
                    @endif

                    <div class="product-price">
                        <span class="product-price-current">
                            Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                        </span>
                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                        <span class="product-price-original">
                            Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>

                    <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="container">
        <div class="newsletter-content">
            <h2>Dapatkan Update Terbaru</h2>
            <p>Berlangganan newsletter untuk penawaran eksklusif dan produk terbaru</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <input type="email" name="email" placeholder="Masukkan email Anda" required>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                    Berlangganan
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Cart functionality
let cartCount = 0;

function addToCart(productId, quantity = 1) {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambahkan...';
    button.disabled = true;

    // AJAX call to add to cart
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(data.cart_count || cartCount + 1);

            // Show success notification
            showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');

            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        } else {
            throw new Error(data.message || 'Gagal menambahkan produk');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal menambahkan produk ke keranjang', 'error');

        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function updateCartCount(count) {
    cartCount = count;
    // Update cart badge in header if exists
    const cartBadges = document.querySelectorAll('.cart-count, .badge-count');
    cartBadges.forEach(badge => {
        if (badge.closest('a[href*="cart"]')) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        color: white;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

    if (type === 'success') {
        notification.style.backgroundColor = 'var(--success)';
    } else if (type === 'error') {
        notification.style.backgroundColor = 'var(--danger)';
    } else {
        notification.style.backgroundColor = 'var(--teal)';
    }

    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Load cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCartCount();

    // Newsletter form handling
    const newsletterForm = document.querySelector('form[action*="newsletter"]');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            button.disabled = true;

            // Let form submit naturally, reset button after delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 3000);
        });
    }
});

function loadCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            updateCartCount(data.count || 0);
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
}

// Smooth scrolling animation for hero section
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Animate elements on scroll
document.querySelectorAll('.feature-item, .product-card, .category-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    observer.observe(el);
});

// Add to wishlist functionality (if user is logged in)
function toggleWishlist(productId) {
    @auth
    const button = event.target.closest('.wishlist-btn');
    const icon = button.querySelector('i');

    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                showNotification('Ditambahkan ke wishlist', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                showNotification('Dihapus dari wishlist', 'info');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal mengubah wishlist', 'error');
    });
    @else
    showNotification('Silakan login terlebih dahulu', 'error');
    setTimeout(() => {
        window.location.href = '{{ route("login") }}';
    }, 1500);
    @endauth
}

// Product quick view functionality
function quickView(productId) {
    // You can implement a modal quick view here
    window.location.href = `/products/${productId}`;
}

// Search functionality
function performSearch(query) {
    if (query.length < 2) {
        showNotification('Masukkan minimal 2 karakter untuk pencarian', 'error');
        return;
    }

    window.location.href = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;
}

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Header scroll effect
let lastScrollY = window.scrollY;
window.addEventListener('scroll', () => {
    const header = document.querySelector('.header');

    if (header) {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    lastScrollY = window.scrollY;
});

// Handle newsletter success/error messages
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif

@if($errors->any())
    @foreach($errors->all() as $error)
        showNotification('{{ $error }}', 'error');
    @endforeach
@endif
</script>
@endpush
