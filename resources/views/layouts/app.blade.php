<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'TokoSaya - E-commerce Terpercaya')</title>
    <meta name="description" content="@yield('description', 'TokoSaya adalah platform e-commerce terpercaya dengan ribuan produk berkualitas, pengiriman cepat, dan pelayanan terbaik.')">
    <meta name="keywords" content="@yield('keywords', 'e-commerce, online shop, tokosaya, belanja online, indonesia')">
    <meta name="author" content="TokoSaya">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'TokoSaya - E-commerce Terpercaya')">
    <meta property="og:description" content="@yield('og_description', 'Platform e-commerce terpercaya dengan ribuan produk berkualitas')">
    <meta property="og:image" content="@yield('og_image', asset('images/tokosaya-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'TokoSaya - E-commerce Terpercaya')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Platform e-commerce terpercaya')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/tokosaya-og.jpg'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- GLightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])

    <!-- Additional CSS -->
    @stack('styles')

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --font-sans: 'Inter', 'Segoe UI', 'Roboto', sans-serif;
            --font-display: 'Poppins', 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            line-height: 1.6;
        }

        .font-display {
            font-family: var(--font-display);
        }

        /* Loading spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner.show {
            display: flex;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Header styles */
        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }

        /* Cart badge */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Button styles */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Footer styles */
        .footer {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
        }

        .footer-link {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: white;
        }

        /* Search suggestions */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }

        .search-suggestion-item:hover {
            background-color: #f8fafc;
        }

        .search-suggestion-item:last-child {
            border-bottom: none;
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

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive utilities */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }

            .search-mobile {
                margin-top: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header -->
    <header>
        <!-- Top Bar -->
        <div class="bg-dark text-white py-2 d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>
                            <i class="fas fa-phone me-2"></i>+62 21 1234 5678
                            <span class="mx-3">|</span>
                            <i class="fas fa-envelope me-2"></i>info@tokosaya.com
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small>
                            @guest
                                <a href="{{ route('login') }}" class="text-white text-decoration-none me-3">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                                <a href="{{ route('register') }}" class="text-white text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Register
                                </a>
                            @else
                                <span class="me-3">
                                    <i class="fas fa-user me-1"></i>Halo, {{ Auth::user()->first_name }}!
                                </span>
                                <a href="{{ route('profile.index') }}" class="text-white text-decoration-none me-3">
                                    Profil
                                </a>
                                <a href="{{ route('logout') }}" class="text-white text-decoration-none"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endguest
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top" id="mainNavbar">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-store me-2"></i>TokoSaya
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Search Bar -->
                    <div class="mx-auto d-none d-lg-block" style="width: 400px;">
                        <div class="position-relative" x-data="searchComponent()">
                            <div class="input-group">
                                <input type="text"
                                       class="form-control"
                                       placeholder="Cari produk..."
                                       x-model="searchQuery"
                                       @input="searchProducts()"
                                       @focus="showSuggestions = true"
                                       @click.away="showSuggestions = false">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <!-- Search Suggestions -->
                            <div class="search-suggestions" x-show="showSuggestions && suggestions.length > 0">
                                <template x-for="suggestion in suggestions" :key="suggestion.id">
                                    <div class="search-suggestion-item" @click="selectProduct(suggestion)">
                                        <div class="d-flex align-items-center">
                                            <img :src="suggestion.image" :alt="suggestion.name"
                                                 class="me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-medium" x-text="suggestion.name"></div>
                                                <small class="text-muted" x-text="suggestion.price"></small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">
                                <i class="fas fa-box me-1"></i>Produk
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-tags me-1"></i>Kategori
                            </a>
                            <ul class="dropdown-menu">
                                @foreach(App\Models\Category::where('parent_id', null)->where('is_active', true)->orderBy('sort_order')->limit(8)->get() as $category)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('categories.show', $category->slug) }}">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} me-2"></i>
                                            @endif
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('categories.index') }}">Lihat Semua Kategori</a></li>
                            </ul>
                        </li>

                        @auth
                            <!-- Wishlist -->
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('wishlist.index') }}">
                                    <i class="fas fa-heart"></i>
                                    <span class="cart-badge" x-text="$store.wishlist.count" x-show="$store.wishlist.count > 0"></span>
                                </a>
                            </li>
                        @endauth

                        <!-- Shopping Cart -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge" x-text="$store.cart.count" x-show="$store.cart.count > 0"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
                                @include('cart.mini-cart')
                            </div>
                        </li>

                        @auth
                            <!-- User Menu -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->first_name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="fas fa-user me-2"></i>Profil Saya
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="fas fa-shopping-bag me-2"></i>Pesanan Saya
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.addresses') }}">
                                        <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                        <i class="fas fa-heart me-2"></i>Wishlist
                                    </a></li>
                                    @if(Auth::user()->role->name === 'admin' || Auth::user()->role->name === 'super_admin')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Admin Panel
                                        </a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Mobile Search -->
                    <div class="d-lg-none search-mobile mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari produk...">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                <div class="container">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                <div class="container">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
                <div class="container">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show m-0" role="alert">
                <div class="container">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container py-5">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 mb-4">
                    <h5 class="font-display fw-bold mb-3">
                        <i class="fas fa-store me-2"></i>TokoSaya
                    </h5>
                    <p class="mb-3">
                        Platform e-commerce terpercaya dengan ribuan produk berkualitas,
                        pengiriman cepat, dan pelayanan terbaik untuk seluruh Indonesia.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="footer-link fs-5">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="footer-link fs-5">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="footer-link fs-5">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="footer-link fs-5">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="footer-link fs-5">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Tentang Kami</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('about') }}" class="footer-link">Tentang TokoSaya</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="footer-link">Hubungi Kami</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Karir</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Blog</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Investor</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Layanan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Bantuan</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Metode Pembayaran</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Panduan Belanja</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Panduan Jual</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Lacak Pesanan</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('privacy') }}" class="footer-link">Kebijakan Privasi</a></li>
                        <li class="mb-2"><a href="{{ route('terms') }}" class="footer-link">Syarat & Ketentuan</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Kebijakan Return</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Hak Kekayaan Intelektual</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Kontak</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <small>+62 21 1234 5678</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <small>cs@tokosaya.com</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <small>Senin-Minggu 08:00-22:00</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <small>Jakarta, Indonesia</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-top border-secondary">
            <div class="container py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>&copy; {{ date('Y') }} TokoSaya. All rights reserved.</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small>Made with <i class="fas fa-heart text-danger"></i> in Indonesia</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle"
            id="backToTop"
            style="width: 50px; height: 50px; display: none; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- GLightbox -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Custom JS -->
    @vite(['resources/js/app.js'])

    <script>
        // Alpine.js Global Stores
        document.addEventListener('alpine:init', () => {
            // Cart Store
            Alpine.store('cart', {
                count: {{ app('App\Services\CartService')->getItemCount() }},
                total: {{ app('App\Services\CartService')->getTotal() }},

                updateCount(count) {
                    this.count = count;
                },

                updateTotal(total) {
                    this.total = total;
                }
            });

            // Wishlist Store
            Alpine.store('wishlist', {
                count: {{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }},

                updateCount(count) {
                    this.count = count;
                }
            });
        });

        // Search Component
        function searchComponent() {
            return {
                searchQuery: '',
                suggestions: [],
                showSuggestions: false,

                async searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.suggestions = [];
                        return;
                    }

                    try {
                        const response = await fetch(`/api/search-suggestions?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await response.json();
                        this.suggestions = data.suggestions || [];
                    } catch (error) {
                        console.error('Search error:', error);
                        this.suggestions = [];
                    }
                },

                selectProduct(product) {
                    window.location.href = `/products/${product.slug}`;
                }
            }
        }

        // Initialize AOS
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true
        });

        // Initialize GLightbox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            autoplayVideos: false
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });

        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Loading spinner functions
        function showLoading() {
            document.getElementById('loadingSpinner').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.remove('show');
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Form submission with loading
        document.querySelectorAll('form[data-loading="true"]').forEach(function(form) {
            form.addEventListener('submit', function() {
                showLoading();
            });
        });

        // AJAX helpers
        window.ajaxRequest = async function(url, options = {}) {
            showLoading();

            const defaultOptions = {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };

            try {
                const response = await fetch(url, { ...defaultOptions, ...options });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }

                return data;
            } catch (error) {
                console.error('AJAX Error:', error);
                throw error;
            } finally {
                hideLoading();
            }
        };

        // Notification system
        window.showNotification = function(message, type = 'success') {
            const alertClass = `alert-${type}`;
            const iconClass = type === 'success' ? 'fa-check-circle' :
                            type === 'error' ? 'fa-exclamation-circle' :
                            type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                    <i class="fas ${iconClass} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', alertHtml);

            // Auto remove after 5 seconds
            setTimeout(function() {
                const alert = document.querySelector('.alert:last-of-type');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        };

        // Cart functions
        window.addToCart = async function(productId, quantity = 1, variantId = null) {
            try {
                const data = await ajaxRequest('/cart/add', {
                    method: 'POST',
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity,
                        variant_id: variantId
                    })
                });

                // Update cart counter
                Alpine.store('cart').updateCount(data.cart_count);
                Alpine.store('cart').updateTotal(data.cart_total);

                showNotification('Produk berhasil ditambahkan ke keranjang!');

                return data;
            } catch (error) {
                showNotification('Gagal menambahkan produk ke keranjang: ' + error.message, 'error');
                throw error;
            }
        };

        window.removeFromCart = async function(itemId) {
            try {
                const data = await ajaxRequest(`/cart/remove/${itemId}`, {
                    method: 'DELETE'
                });

                // Update cart counter
                Alpine.store('cart').updateCount(data.cart_count);
                Alpine.store('cart').updateTotal(data.cart_total);

                showNotification('Produk berhasil dihapus dari keranjang!');

                return data;
            } catch (error) {
                showNotification('Gagal menghapus produk dari keranjang: ' + error.message, 'error');
                throw error;
            }
        };

        window.updateCartQuantity = async function(itemId, quantity) {
            try {
                const data = await ajaxRequest(`/cart/update/${itemId}`, {
                    method: 'PATCH',
                    body: JSON.stringify({ quantity: quantity })
                });

                // Update cart counter
                Alpine.store('cart').updateCount(data.cart_count);
                Alpine.store('cart').updateTotal(data.cart_total);

                return data;
            } catch (error) {
                showNotification('Gagal memperbarui kuantitas: ' + error.message, 'error');
                throw error;
            }
        };

        // Wishlist functions
        window.toggleWishlist = async function(productId) {
            try {
                const data = await ajaxRequest('/wishlist/toggle', {
                    method: 'POST',
                    body: JSON.stringify({ product_id: productId })
                });

                // Update wishlist counter
                Alpine.store('wishlist').updateCount(data.wishlist_count);

                const message = data.added ? 'Produk ditambahkan ke wishlist!' : 'Produk dihapus dari wishlist!';
                showNotification(message);

                return data;
            } catch (error) {
                showNotification('Gagal memperbarui wishlist: ' + error.message, 'error');
                throw error;
            }
        };

        // Product rating functions
        window.submitRating = async function(productId, rating, title, review) {
            try {
                const data = await ajaxRequest('/reviews', {
                    method: 'POST',
                    body: JSON.stringify({
                        product_id: productId,
                        rating: rating,
                        title: title,
                        review: review
                    })
                });

                showNotification('Review berhasil dikirim!');

                return data;
            } catch (error) {
                showNotification('Gagal mengirim review: ' + error.message, 'error');
                throw error;
            }
        };

        // Currency formatter
        window.formatCurrency = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        };

        // Image lazy loading
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy-load');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>

    <!-- Additional JavaScript -->
    @stack('scripts')
</body>
</html>
