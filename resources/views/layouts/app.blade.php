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

        /* Cart badge - FIXED STYLE */
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
            min-width: 20px;
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

        /* Responsive utilities */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 9999; justify-content: center; align-items: center;">
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
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       placeholder="Cari produk..."
                                       value="{{ request('q') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
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
                                @php
                                    $categories = \App\Models\Category::where('parent_id', null)
                                        ->where('is_active', true)
                                        ->orderBy('sort_order')
                                        ->limit(8)
                                        ->get();
                                @endphp
                                @foreach($categories as $category)
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
                            <!-- Cek apakah user adalah customer -->
                            @if(!auth()->user()->role || auth()->user()->role->name === 'customer')
                                <!-- Wishlist -->
                                <li class="nav-item">
                                    <a class="nav-link position-relative" href="{{ route('wishlist.index') }}">
                                        <i class="fas fa-heart"></i>
                                        @php
                                            $wishlistCount = auth()->user()->wishlists()->count();
                                        @endphp
                                        @if($wishlistCount > 0)
                                            <span class="cart-badge">{{ $wishlistCount }}</span>
                                        @endif
                                    </a>
                                </li>

                                <!-- 🛒 SHOPPING CART - WORKING VERSION -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="cart-badge" id="cartCount" style="display: none;">0</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px;">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
                                                </h6>
                                            </div>
                                            <div class="card-body" id="miniCartContent">
                                                <div class="text-center py-4">
                                                    <i class="fas fa-shopping-cart text-muted fs-1 mb-3"></i>
                                                    <p class="text-muted">Keranjang Anda kosong</p>
                                                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                                                        Mulai Berbelanja
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light text-center">
                                                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
                                                    Lihat Keranjang
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @else
                            <!-- Guest users can see cart -->
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="cart-badge" id="guestCartCount" style="display: none;">0</span>
                                </a>
                            </li>
                        @endauth

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
                                    @if(!auth()->user()->role || auth()->user()->role->name === 'customer')
                                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Pesanan Saya
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('profile.addresses.index') }}">
                                            <i class="fas fa-map-marker-alt me-2"></i>Alamat
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                            <i class="fas fa-heart me-2"></i>Wishlist
                                        </a></li>
                                    @endif
                                    @if(Auth::user()->role && Auth::user()->role->name === 'admin')
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
                    <div class="d-lg-none mt-3">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       placeholder="Cari produk..."
                                       value="{{ request('q') }}">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
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

    <!-- ========= LETAKKAN SCRIPT INI DI BAGIAN BAWAH SEBELUM </body> ========= -->
    <script>
        // Cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            loadCartCount();
        });

        function loadCartCount() {
            fetch('/cart/count')
                .then(response => response.json())
                .then(data => {
                    const cartBadge = document.getElementById('cartCount') || document.getElementById('guestCartCount');
                    if (cartBadge) {
                        const count = data.count || 0;
                        cartBadge.textContent = count;
                        cartBadge.style.display = count > 0 ? 'flex' : 'none';
                    }
                })
                .catch(error => {
                    console.log('Could not load cart count');
                });
        }

        // Global add to cart function
        window.addToCart = function(productId, quantity = 1) {
            fetch('/cart/add', {
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
                    loadCartCount(); // Reload cart count

                    // Show success message
                    if (typeof showNotification === 'function') {
                        showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                    } else {
                        alert('Produk berhasil ditambahkan ke keranjang!');
                    }
                } else {
                    alert(data.message || 'Gagal menambahkan produk ke keranjang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
            });
        };

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
