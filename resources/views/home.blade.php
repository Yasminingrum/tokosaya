@extends('layouts.app')

@section('title', 'TokoSaya - E-commerce Terpercaya Indonesia')
@section('description', 'Belanja online mudah dan aman di TokoSaya. Ribuan produk berkualitas, harga terbaik, pengiriman cepat ke seluruh Indonesia.')
@section('keywords', 'tokosaya, e-commerce, belanja online, toko online, indonesia, murah, berkualitas')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container-fluid p-0">
        <!-- Main Hero Carousel -->
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                @forelse($banners->where('position', 'hero') as $banner)
                    <div class="swiper-slide">
                        <div class="hero-slide" style="background-image: url('{{ $banner->image }}');">
                            <div class="hero-overlay"></div>
                            <div class="container">
                                <div class="row align-items-center min-vh-75">
                                    <div class="col-lg-6 col-md-8">
                                        <div class="hero-content text-white" data-aos="fade-up">
                                            @if($banner->subtitle)
                                                <p class="hero-subtitle">{{ $banner->subtitle }}</p>
                                            @endif
                                            <h1 class="hero-title font-display">{{ $banner->title }}</h1>
                                            @if($banner->description)
                                                <p class="hero-description">{{ $banner->description }}</p>
                                            @endif
                                            @if($banner->link_url)
                                                <div class="hero-actions mt-4">
                                                    <a href="{{ $banner->link_url }}" class="btn btn-primary btn-lg">
                                                        {{ $banner->link_text ?: 'Belanja Sekarang' }}
                                                        <i class="fas fa-arrow-right ms-2"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Default Hero Slide -->
                    <div class="swiper-slide">
                        <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="container">
                                <div class="row align-items-center min-vh-75">
                                    <div class="col-lg-6 col-md-8">
                                        <div class="hero-content text-white" data-aos="fade-up">
                                            <p class="hero-subtitle">Selamat Datang di</p>
                                            <h1 class="hero-title font-display">TokoSaya</h1>
                                            <p class="hero-description">
                                                Platform e-commerce terpercaya dengan ribuan produk berkualitas,
                                                harga terbaik, dan pengiriman cepat ke seluruh Indonesia.
                                            </p>
                                            <div class="hero-actions mt-4">
                                                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg me-3">
                                                    Mulai Belanja
                                                    <i class="fas fa-shopping-bag ms-2"></i>
                                                </a>
                                                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                                                    Lihat Kategori
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 d-none d-lg-block">
                                        <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                                            <img src="{{ asset('images/hero-shopping.svg') }}" alt="Online Shopping" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Navigation -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="feature-title">Pengiriman Cepat</h5>
                    <p class="feature-description">Kirim ke seluruh Indonesia dengan berbagai pilihan ekspedisi terpercaya</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="feature-title">100% Aman</h5>
                    <p class="feature-description">Transaksi dilindungi dengan sistem keamanan berlapis dan enkripsi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h5 class="feature-title">Kualitas Terjamin</h5>
                    <p class="feature-description">Produk berkualitas dari seller terpercaya dengan garansi resmi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="feature-title">Customer Service 24/7</h5>
                    <p class="feature-description">Tim support siap membantu Anda kapan saja melalui berbagai channel</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Kategori Populer</h2>
            <p class="section-description">Temukan produk dari berbagai kategori pilihan</p>
        </div>

        <div class="row g-4">
            @foreach($featured_categories as $category)
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                        <div class="category-image">
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="img-fluid">
                            @else
                                <div class="category-placeholder">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }}"></i>
                                    @else
                                        <i class="fas fa-tag"></i>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="category-content">
                            <h5 class="category-name">{{ $category->name }}</h5>
                            <p class="category-count">{{ number_format($category->product_count) }} produk</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                Lihat Semua Kategori
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Produk Unggulan</h2>
            <p class="section-description">Pilihan terbaik dengan kualitas premium dan harga terjangkau</p>
        </div>

        <div class="row g-4">
            @foreach($featured_products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="product-card">
                        <div class="product-badge">
                            @if($product->status == 'discontinued')
                                <span class="badge bg-danger">Habis</span>
                            @elseif($product->compare_price_cents > $product->price_cents)
                                <span class="badge bg-success">Diskon {{ round(($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents * 100) }}%</span>
                            @endif
                        </div>
                        <div class="product-image">
                            <a href="{{ route('products.show', $product->slug) }}">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}" class="img-fluid">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </a>
                        </div>
                        <div class="product-content">
                            <div class="product-category">
                                {{ $product->category->name }}
                            </div>
                            <h3 class="product-name">
                                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="product-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->rating_average)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="rating-count">({{ $product->rating_count }})</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price">Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}</span>
                                @if($product->compare_price_cents > $product->price_cents)
                                    <span class="compare-price">Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="product-stock">
                                @if($product->stock_quantity <= 0)
                                    <span class="text-danger">Stok Habis</span>
                                @elseif($product->stock_quantity <= $product->min_stock_level)
                                    <span class="text-warning">Stok Terbatas</span>
                                @else
                                    <span class="text-success">Tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div class="product-actions">
                            @if($product->status == 'active' && $product->stock_quantity > 0)
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-shopping-cart"></i> Beli
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-ban"></i> Tidak Tersedia
                                </button>
                            @endif
                            <a href="{{ route('wishlist.add', $product->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('products.index', ['featured' => 1]) }}" class="btn btn-primary">
                Lihat Semua Produk Unggulan
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Flash Sale Section -->
@if($flashSaleProducts->count() > 0)
<section class="flash-sale-section py-5 bg-danger text-white">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">
                <i class="fas fa-bolt me-2"></i>Flash Sale
            </h2>
            <p class="section-description">Penawaran terbatas! Buruan sebelum kehabisan</p>

            <!-- Countdown Timer -->
            <div class="flash-sale-timer mt-3">
                <div class="countdown-display">
                    <div class="countdown-item">
                        <span class="countdown-number" id="days">00</span>
                        <span class="countdown-label">Hari</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="hours">00</span>
                        <span class="countdown-label">Jam</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="minutes">00</span>
                        <span class="countdown-label">Menit</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="seconds">00</span>
                        <span class="countdown-label">Detik</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper flash-sale-swiper">
            <div class="swiper-wrapper">
                @foreach($flashSaleProducts as $product)
                    <div class="swiper-slide">
                        @include('components.product-card', ['product' => $product, 'theme' => 'dark'])
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif

<!-- Latest Products Section -->
<section class="latest-products-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Produk Terbaru</h2>
            <p class="section-description">Produk terbaru yang baru saja masuk ke TokoSaya</p>
        </div>

        <div class="row g-4">
            @foreach($latestProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-outline-primary">
                Lihat Produk Terbaru Lainnya
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Brands Section -->
@if($brands->count() > 0)
<section class="brands-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Brand Terpercaya</h2>
            <p class="section-description">Bekerja sama dengan brand-brand ternama</p>
        </div>

        <div class="swiper brands-swiper">
            <div class="swiper-wrapper align-items-center">
                @foreach($brands as $brand)
                    <div class="swiper-slide">
                        <div class="brand-item text-center" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            @if($brand->logo)
                                <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="brand-logo">
                            @else
                                <div class="brand-placeholder">
                                    <span>{{ $brand->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold">Dapatkan Update Terbaru</h3>
                <p class="text-white-50">Berlangganan newsletter untuk mendapatkan promo dan produk terbaru</p>
            </div>
            <div class="col-md-6">
                <!-- Option 1: Simple form tanpa route (JavaScript only) -->
                <form class="d-flex gap-2" id="newsletterForm">
                    @csrf
                    <input type="email" class="form-control" name="email" placeholder="Masukkan email Anda" required>
                    <button type="submit" class="btn btn-primary flex-shrink-0">Berlangganan</button>
                </form>

                <!-- Option 2: Form dengan route (jika sudah menambah route) -->
                <!--
                <form class="d-flex gap-2" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <input type="email" class="form-control" name="email" placeholder="Masukkan email Anda" required>
                    <button type="submit" class="btn btn-primary flex-shrink-0">Berlangganan</button>
                </form>
                -->
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Newsletter subscription (Simple JavaScript version)
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                // You can add AJAX call here to actually save the email
                alert('Terima kasih! Anda akan segera mendapatkan update dari kami.');
                this.reset();
            }
        });
    }
});
</script>
@endpush

<!-- Customer Testimonials Section - FIXED -->
@if(isset($testimonials) && $testimonials && $testimonials->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="section-title font-display">Testimoni Pelanggan</h2>
                <p class="section-description">Testimoni dari pelanggan setia TokoSaya</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($testimonials as $testimonial)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card border-0 shadow-sm h-100 testimonial-card">
                    <div class="card-body">
                        <!-- Rating -->
                        <div class="testimonial-rating mb-3">
                            @for($i = 1; $i <= ($testimonial->rating ?? 5); $i++)
                            <i class="fas fa-star text-warning"></i>
                            @endfor
                        </div>

                        <!-- Review Text -->
                        <p class="testimonial-text">
                            "{{ $testimonial->review }}"
                        </p>

                        <!-- Author Info -->
                        <div class="testimonial-author d-flex align-items-center">
                            <div class="author-avatar me-3">
                                @if(isset($testimonial->user) && $testimonial->user->avatar)
                                <img src="{{ $testimonial->user->avatar }}" alt="{{ $testimonial->user->name }}" class="rounded-circle" width="50" height="50">
                                @elseif(isset($testimonial->avatar) && $testimonial->avatar)
                                <img src="{{ $testimonial->avatar }}" alt="{{ $testimonial->name ?? 'Customer' }}" class="rounded-circle" width="50" height="50">
                                @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    {{ strtoupper(substr($testimonial->user->name ?? $testimonial->name ?? 'C', 0, 1)) }}
                                </div>
                                @endif
                            </div>
                            <div class="author-info">
                                <h6 class="author-name mb-0">
                                    {{ $testimonial->user->name ?? $testimonial->name ?? 'Customer' }}
                                </h6>
                                <span class="author-location text-muted">
                                    {{ $testimonial->user->city ?? $testimonial->location ?? 'Indonesia' }}
                                </span>
                                @if(($testimonial->verified_purchase ?? false))
                                <br>
                                <span class="badge bg-success mt-1">
                                    <i class="fas fa-check"></i> Verified Purchase
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary">
                Lihat Testimoni Lainnya <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
@endif

@push('styles')
<style>
.testimonial-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

.testimonial-rating {
    margin-bottom: 1rem;
}

.testimonial-text {
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    color: #555;
    font-style: italic;
}

.author-name {
    font-weight: 600;
    color: #333;
}

.author-location {
    font-size: 0.9rem;
}
</style>
@endpush

@endsection

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        position: relative;
        overflow: hidden;
    }

    .hero-swiper {
        height: 100vh;
        min-height: 600px;
    }

    .hero-slide {
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Product Card Styles */
    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
    }

    .product-image {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .no-image-placeholder {
        width: 100%;
        height: 100%;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
        font-size: 3rem;
    }

    .product-content {
        padding: 1.5rem;
    }

    .product-category {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        height: 2.5rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-rating {
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
    }

    .product-price {
        margin-bottom: 0.75rem;
    }

    .current-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .compare-price {
        font-size: 0.9rem;
        text-decoration: line-through;
        color: #64748b;
        margin-left: 0.5rem;
    }

    .product-stock {
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }

    .product-actions {
        padding: 0 1.5rem 1.5rem;
        display: flex;
        gap: 0.5rem;
    }

    /* Flash Sale Section */
    .flash-sale-section {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        position: relative;
        overflow: hidden;
    }

    .flash-sale-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="flash-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23flash-pattern)"/></svg>');
        opacity: 0.5;
    }

    .countdown-display {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .countdown-item {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1rem;
        backdrop-filter: blur(10px);
        min-width: 80px;
    }

    .countdown-number {
        display: block;
        font-size: 2rem;
        font-weight: 700;
        font-family: var(--font-display);
    }

    .countdown-label {
        font-size: 0.8rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-swiper {
            height: 70vh;
            min-height: 500px;
        }

        .product-image {
            height: 150px;
        }

        .countdown-item {
            min-width: 60px;
            padding: 0.75rem;
        }

        .countdown-number {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .hero-swiper {
            height: 60vh;
            min-height: 400px;
        }

        .countdown-display {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .countdown-item {
            min-width: 50px;
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swipers
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });

        const flashSaleSwiper = new Swiper('.flash-sale-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.flash-sale-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                576: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                },
            },
        });

        const brandsSwiper = new Swiper('.brands-swiper', {
            slidesPerView: 2,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            breakpoints: {
                576: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });

        const testimonialsSwiper = new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.testimonials-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });

        // Countdown Timer for Flash Sale
        function updateCountdown() {
            // Set your flash sale end date here
            const endDate = new Date('{{ $flash_sale_end ?? now()->addDays(3)->format('Y-m-d H:i:s') }}').getTime();
            const now = new Date().getTime();
            const distance = endDate - now;

            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
            }
        }

        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call

        // Newsletter Form Submission
        document.getElementById('newsletterForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            try {
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                button.disabled = true;

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Terima kasih! Anda telah berlangganan newsletter kami.');
                    form.reset();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                alert(error.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.');
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    });
</script>
@endpush
