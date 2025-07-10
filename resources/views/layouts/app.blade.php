<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TokoSaya - Belanja Online Terpercaya')</title>
    <meta name="description" content="@yield('description', 'TokoSaya adalah platform e-commerce terpercaya dengan ribuan produk berkualitas dan harga terjangkau')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #6B4E3D;
            --secondary-color: #A4B494;
            --accent-color: #D2855C;
            --dark-color: #4A3429;
            --light-gray: #F5F2E8;
            --border-color: #E8DDD0;
            --cream-color: #F0EDC4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #4A3429;
            background-color: var(--cream-color);
        }

        /* Header Styles */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar {
            background: var(--dark-color);
            color: white;
            font-size: 0.875rem;
            padding: 8px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--dark-color) !important;
            text-decoration: none;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .search-form {
            max-width: 400px;
            position: relative;
        }

        .search-input {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 12px 50px 12px 20px;
            font-size: 0.95rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            color: white;
        }

        .cart-icon, .wishlist-icon {
            position: relative;
            color: var(--dark-color);
            font-size: 1.25rem;
            margin: 0 15px;
            text-decoration: none;
        }

        .badge-cart {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Category & Product Cards */
        .category-card, .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
        }

        .category-card:hover, .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .category-card {
            padding: 25px 15px;
            text-align: center;
        }

        .category-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.2rem;
        }

        .category-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .category-count {
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        .product-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
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

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--accent-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            line-height: 1.3;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-title a {
            color: inherit;
            text-decoration: none;
        }

        .product-title a:hover {
            color: var(--primary-color);
        }

        .product-meta {
            margin-bottom: 8px;
            font-size: 0.75rem;
            color: var(--secondary-color);
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .price-compare {
            font-size: 0.85rem;
            color: var(--secondary-color);
            text-decoration: line-through;
            margin-left: 8px;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 12px;
        }

        .stars {
            color: var(--accent-color);
            font-size: 0.8rem;
        }

        .rating-text {
            color: var(--secondary-color);
            font-size: 0.75rem;
        }

        /* Compact Product Actions */
        .product-actions {
            display: flex;
            gap: 6px;
            margin-top: 12px;
        }

        .btn-add-cart {
            flex: 1;
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
            transition: background-color 0.3s ease;
        }

        .btn-add-cart:hover {
            background: var(--dark-color);
        }

        .btn-add-cart:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
        }

        .btn-wishlist {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--secondary-color);
            padding: 8px 10px;
            border-radius: 6px;
            transition: all 0.3s ease;
            flex: 0 0 auto;
        }

        .btn-wishlist:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .btn-wishlist.active {
            border-color: var(--accent-color);
            color: var(--accent-color);
            background: var(--cream-color);
        }

        .btn-detail {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 8px 10px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            flex: 0 0 auto;
        }

        .btn-detail:hover {
            background: var(--dark-color);
        }

        /* Compact Grid */
        .product-grid-compact {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .products-grid-compact {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer-title {
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: white;
        }

        .social-icons a {
            color: var(--secondary-color);
            font-size: 1.25rem;
            margin-right: 15px;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .social-icons a:hover {
            color: var(--accent-color);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: var(--dark-color);
        }

        /* Utility Classes */
        .text-primary { color: var(--primary-color) !important; }
        .bg-light-gray { background-color: var(--light-gray) !important; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }

            .search-form {
                margin: 15px 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="fas fa-phone me-2"></i> +62 804-1-500-400</span>
                    <span class="ms-4"><i class="fas fa-envelope me-2"></i> info@tokosaya.id</span>
                </div>
                <div class="col-md-6 text-end">
                    @auth
                        <span>Hello, <strong>{{ auth()->user()->first_name }}</strong></span>
                        <a href="{{ route('logout') }}" class="text-white ms-3 text-decoration-none"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white text-decoration-none">Login</a>
                        <span class="mx-2">|</span>
                        <a href="{{ route('register') }}" class="text-white text-decoration-none">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light py-3">
                <a class="navbar-brand" href="{{ route('home') }}">TokoSaya</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">Shop</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Pages
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('about') }}">About</a></li>
                                <li><a class="dropdown-item" href="{{ route('contact') }}">Contact</a></li>
                                <li><a class="dropdown-item" href="{{ route('faq') }}">FAQ</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>

                    <!-- Search Form -->
                    <form class="search-form me-4" action="{{ route('search') }}" method="GET">
                        <input type="text" name="q" class="form-control search-input"
                               placeholder="I am Searching for..." value="{{ request('q') }}">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <!-- User Actions -->
                    <div class="d-flex align-items-center">
                        @auth
                            <a href="{{ route('wishlist.index') }}" class="wishlist-icon">
                                <i class="fas fa-heart"></i>
                                <span class="badge-cart" id="wishlist-count">0</span>
                            </a>
                        @endauth

                        <a href="{{ route('cart.index') }}" class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge-cart" id="cart-count">0</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-title">TokoSaya</h5>
                    <p class="text-muted">Platform e-commerce terpercaya dengan ribuan produk berkualitas dan harga terjangkau. Belanja mudah, aman, dan nyaman.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <a href="{{ route('about') }}" class="footer-link">About Us</a>
                    <a href="{{ route('contact') }}" class="footer-link">Contact</a>
                    <a href="{{ route('faq') }}" class="footer-link">FAQ</a>
                    <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="footer-link">Terms of Service</a>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Categories</h5>
                    <a href="#" class="footer-link">Fashion</a>
                    <a href="#" class="footer-link">Electronics</a>
                    <a href="#" class="footer-link">Home & Garden</a>
                    <a href="#" class="footer-link">Sports</a>
                    <a href="#" class="footer-link">Books</a>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="footer-title">Newsletter</h5>
                    <p class="text-muted">Subscribe to get updates on new products and offers</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="email" name="email" class="form-control me-2" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>

            <hr class="my-4" style="border-color: #475569;">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2025 TokoSaya. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <img src="https://via.placeholder.com/40x25/333/fff?text=VISA" alt="Visa" class="me-2">
                    <img src="https://via.placeholder.com/40x25/333/fff?text=MC" alt="Mastercard" class="me-2">
                    <img src="https://via.placeholder.com/40x25/333/fff?text=PAYPAL" alt="PayPal">
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Update cart and wishlist counts
        function updateCartCount() {
            fetch('/api/cart/count')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count || 0;
                })
                .catch(error => console.log('Error fetching cart count:', error));
        }

        function updateWishlistCount() {
            @auth
            fetch('/wishlist/count')
                .then(response => response.json())
                .then(data => {
                    const wishlistElement = document.getElementById('wishlist-count');
                    if (wishlistElement) {
                        wishlistElement.textContent = data.count || 0;
                    }
                })
                .catch(error => console.log('Error fetching wishlist count:', error));
            @endauth
        }

        // Add to cart function
        function addToCart(productId, quantity = 1) {
            const button = event.target;
            const originalText = button.textContent;

            // Disable button dan ubah text
            button.disabled = true;
            button.textContent = 'Adding...';

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount();
                    showNotification(data.message || 'Product added to cart successfully!', 'success');

                    // ✅ Berikan feedback visual yang lebih baik
                    button.textContent = 'Added!';
                    button.style.background = '#22c55e';

                    setTimeout(() => {
                        button.textContent = originalText;
                        button.style.background = '';
                        button.disabled = false;
                    }, 2000);
                } else {
                    showNotification(data.message || 'Failed to add product to cart', 'error');
                    button.disabled = false;
                    button.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
            });
        }

        // Toggle wishlist function
        function toggleWishlist(productId) {
            @auth
            fetch(`/wishlist/toggle/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update heart icons
                    const hearts = document.querySelectorAll(`[onclick="toggleWishlist(${productId})"] i`);
                    hearts.forEach(heart => {
                        if (data.in_wishlist) {
                            heart.classList.remove('far');
                            heart.classList.add('fas');
                        } else {
                            heart.classList.remove('fas');
                            heart.classList.add('far');
                        }
                    });

                    // Update wishlist count
                    updateWishlistCount();
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => showNotification('Network error', 'error'));
            @else
            window.location.href = '{{ route("login") }}';
            @endauth
        }

        // Simple notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
        });

        // Show Laravel session messages
        @if(session('success'))
            showNotification('{{ session("success") }}', 'success');
        @endif
        @if(session('error'))
            showNotification('{{ session("error") }}', 'error');
        @endif
        @if(session('warning'))
            showNotification('{{ session("warning") }}', 'warning');
        @endif
        @if(session('info'))
            showNotification('{{ session("info") }}', 'info');
        @endif
    </script>

    @stack('scripts')
</body>
</html>
