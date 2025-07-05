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
            @foreach($categories->take(8) as $category)
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
            @foreach($featuredProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    @include('components.product-card', ['product' => $product])
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
            <div class="flash-sale-timer mt-3" x-data="countdownTimer('2024-12-31 23:59:59')">
                <div class="countdown-display">
                    <div class="countdown-item">
                        <span class="countdown-number" x-text="days"></span>
                        <span class="countdown-label">Hari</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" x-text="hours"></span>
                        <span class="countdown-label">Jam</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" x-text="minutes"></span>
                        <span class="countdown-label">Menit</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" x-text="seconds"></span>
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
<section class="brands-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Brand Terpercaya</h2>
            <p class="section-description">Bekerja sama dengan brand-brand ternama</p>
        </div>

        <div class="swiper brands-swiper">
            <div class="swiper-wrapper align-items-center">
                @foreach($brands->take(10) as $brand)
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

<!-- Newsletter Section -->
<section class="newsletter-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="newsletter-content text-white">
                    <h3 class="newsletter-title font-display">Dapatkan Penawaran Terbaik!</h3>
                    <p class="newsletter-description">
                        Berlangganan newsletter kami dan jadilah yang pertama tahu tentang promo,
                        diskon, dan produk terbaru dari TokoSaya.
                    </p>
                    <div class="newsletter-benefits mt-4">
                        <div class="benefit-item">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Diskon eksklusif subscriber</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Info produk terbaru</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Flash sale preview</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="newsletter-form">
                    <form id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <input type="email"
                                       class="form-control"
                                       name="email"
                                       placeholder="Masukkan email Anda"
                                       required>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Berlangganan
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-white-50">
                                *Dengan berlangganan, Anda menyetujui untuk menerima email marketing dari kami.
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="section-title font-display">Apa Kata Mereka?</h2>
            <p class="section-description">Testimoni dari pelanggan setia TokoSaya</p>
        </div>

        <div class="swiper testimonials-swiper">
            <div class="swiper-wrapper">
                @for($i = 1; $i <= 6; $i++)
                    <div class="swiper-slide">
                        <div class="testimonial-card" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <div class="testimonial-content">
                                <div class="testimonial-rating">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                </div>
                                <p class="testimonial-text">
                                    "Pengalaman belanja yang luar biasa! Produk berkualitas, pengiriman cepat,
                                    dan customer service yang sangat responsif. Highly recommended!"
                                </p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-avatar">
                                    <img src="{{ asset('images/avatars/avatar-' . $i . '.jpg') }}" alt="Customer {{ $i }}">
                                </div>
                                <div class="author-info">
                                    <h6 class="author-name">Customer {{ $i }}</h6>
                                    <span class="author-location">Jakarta, Indonesia</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

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

    .hero-subtitle {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .hero-description {
        font-size: 1.2rem;
        line-height: 1.6;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .hero-actions .btn {
        border-radius: 50px;
        padding: 15px 30px;
        font-weight: 600;
        text-transform: none;
        transition: all 0.3s ease;
    }

    .hero-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .hero-image {
        text-align: center;
    }

    .hero-image img {
        max-width: 500px;
        height: auto;
        filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.2));
    }

    .min-vh-75 {
        min-height: 75vh;
    }

    /* Swiper Navigation */
    .swiper-pagination {
        bottom: 30px !important;
    }

    .swiper-pagination-bullet {
        background: white;
        opacity: 0.7;
        width: 12px;
        height: 12px;
    }

    .swiper-pagination-bullet-active {
        opacity: 1;
        background: var(--primary-color);
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        backdrop-filter: blur(10px);
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 20px;
    }

    /* Features Section */
    .feature-card {
        padding: 2rem;
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: var(--primary-color);
        font-size: 2rem;
    }

    .feature-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1e293b;
    }

    .feature-description {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* Section Headers */
    .section-header {
        max-width: 600px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .section-description {
        font-size: 1.1rem;
        color: #64748b;
        line-height: 1.6;
    }

    /* Category Cards */
    .category-card {
        display: block;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        color: inherit;
        text-decoration: none;
    }

    .category-image {
        height: 150px;
        overflow: hidden;
        position: relative;
    }

    .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .category-card:hover .category-image img {
        transform: scale(1.1);
    }

    .category-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .category-content {
        padding: 1.5rem;
        text-align: center;
    }

    .category-name {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .category-count {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
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

    .flash-sale-timer {
        position: relative;
        z-index: 2;
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

    .flash-sale-swiper {
        position: relative;
        z-index: 2;
        padding-bottom: 50px;
    }

    /* Newsletter Section */
    .newsletter-section {
        position: relative;
        overflow: hidden;
    }

    .newsletter-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .newsletter-description {
        font-size: 1.1rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .newsletter-benefits {
        list-style: none;
        padding: 0;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .benefit-item i {
        color: var(--warning-color);
    }

    .newsletter-form {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 2rem;
        backdrop-filter: blur(10px);
    }

    .newsletter-form .form-control {
        border: none;
        border-radius: 10px 0 0 10px;
        padding: 15px 20px;
        font-size: 1rem;
    }

    .newsletter-form .btn {
        border-radius: 0 10px 10px 0;
        padding: 15px 25px;
        font-weight: 600;
    }

    /* Testimonials Section */
    .testimonial-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .testimonial-rating {
        margin-bottom: 1rem;
    }

    .testimonial-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #64748b;
        font-style: italic;
        flex-grow: 1;
        margin-bottom: 1.5rem;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .author-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #1e293b;
    }

    .author-location {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Brands Section */
    .brands-swiper {
        padding: 2rem 0;
    }

    .brand-item {
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .brand-item:hover {
        transform: scale(1.05);
    }

    .brand-logo {
        max-width: 120px;
        max-height: 60px;
        width: auto;
        height: auto;
        filter: grayscale(100%);
        transition: filter 0.3s ease;
    }

    .brand-item:hover .brand-logo {
        filter: grayscale(0%);
    }

    .brand-placeholder {
        width: 120px;
        height: 60px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        margin: 0 auto;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .hero-title {
            font-size: 3rem;
        }
    }

    @media (max-width: 768px) {
        .hero-swiper {
            height: 70vh;
            min-height: 500px;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-description {
            font-size: 1rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .countdown-display {
            gap: 1rem;
        }

        .countdown-item {
            min-width: 60px;
            padding: 0.75rem;
        }

        .countdown-number {
            font-size: 1.5rem;
        }

        .newsletter-title {
            font-size: 1.8rem;
        }

        .newsletter-form {
            margin-top: 2rem;
            padding: 1.5rem;
        }

        .feature-card,
        .testimonial-card {
            padding: 1.5rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .category-image {
            height: 120px;
        }

        .category-content {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .hero-actions .btn {
            width: 100%;
            margin-bottom: 1rem;
        }

        .countdown-display {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .countdown-item {
            min-width: 50px;
            padding: 0.5rem;
        }

        .newsletter-form .input-group {
            flex-direction: column;
        }

        .newsletter-form .form-control,
        .newsletter-form .btn {
            border-radius: 10px;
        }

        .newsletter-form .btn {
            margin-top: 1rem;
        }
    }

    /* Loading animations */
    .loading-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize Swiper components
    document.addEventListener('DOMContentLoaded', function() {
        // Hero Swiper
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

        // Flash Sale Swiper
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

        // Testimonials Swiper
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

        // Brands Swiper
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
    });

    // Countdown Timer Component
    function countdownTimer(endDate) {
        return {
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0,

            init() {
                this.updateCountdown();
                setInterval(() => {
                    this.updateCountdown();
                }, 1000);
            },

            updateCountdown() {
                const now = new Date().getTime();
                const end = new Date(endDate).getTime();
                const distance = end - now;

                if (distance > 0) {
                    this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                } else {
                    this.days = this.hours = this.minutes = this.seconds = 0;
                }
            }
        }
    }

    // Newsletter Form Submission
    document.getElementById('newsletterForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        try {
            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            button.disabled = true;

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Terima kasih! Anda telah berlangganan newsletter kami.', 'success');
                form.reset();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showNotification('Maaf, terjadi kesalahan. Silakan coba lagi.', 'error');
            console.error('Newsletter subscription error:', error);
        } finally {
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });

    // Lazy Loading for Images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('loading-shimmer');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            img.classList.add('loading-shimmer');
            imageObserver.observe(img);
        });
    }

    // Scroll to Top on Page Load
    window.addEventListener('load', function() {
        window.scrollTo(0, 0);
    });

    // Performance optimization: Preload critical images
    function preloadCriticalImages() {
        const criticalImages = [
            '{{ asset("images/hero-shopping.svg") }}',
            // Add other critical images here
        ];

        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });
    }

    // Call preload function
    preloadCriticalImages();

    // Track user interactions for analytics
    function trackInteraction(action, category, label) {
        // Replace with your analytics tracking code
        console.log('Analytics:', { action, category, label });

        // Example: Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: category,
                event_label: label
            });
        }
    }

    // Track category clicks
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('.category-name').textContent;
            trackInteraction('click', 'category', categoryName);
        });
    });

    // Track product card interactions
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productName = this.querySelector('.product-name')?.textContent || 'Unknown';
            trackInteraction('click', 'product', productName);
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
