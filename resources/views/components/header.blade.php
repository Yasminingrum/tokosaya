<!-- Modern Minimalist Header -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/50 transition-all duration-300"
        x-data="{
            mobileMenuOpen: false,
            searchOpen: false,
            userMenuOpen: false,
            cartOpen: false,
            searchQuery: '',
            searchResults: [],
            searchLoading: false
        }"
        :class="{ 'shadow-lg': $store.ui.scrolled }">

    <!-- Top Bar - Simplified -->
    <div class="bg-gray-900 text-white py-2 hidden lg:block">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center text-sm">
                <div class="flex items-center space-x-6">
                    <span class="flex items-center">
                        <i class="fas fa-shipping-fast text-green-400 mr-2"></i>
                        Gratis Ongkir min. Rp 250rb
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-400 mr-2"></i>
                        100% Original
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="tel:+6280412345678" class="hover:text-blue-400 transition-colors">
                        <i class="fas fa-phone mr-1"></i>0804-1-234-5678
                    </a>
                    <div class="flex space-x-2">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo - Modern -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-store text-white text-lg"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-bold text-gray-900">
                            Toko<span class="text-blue-600">Saya</span>
                        </h1>
                        <p class="text-xs text-gray-500 -mt-1">Belanja Mudah & Terpercaya</p>
                    </div>
                </a>
            </div>

            <!-- Search Bar - Modern Design -->
            <div class="flex-1 max-w-xl mx-8 relative hidden lg:block">
                <div class="relative">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch()"
                           @focus="searchOpen = true"
                           @click.away="searchOpen = false"
                           placeholder="Cari produk, brand, atau kategori..."
                           class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                    <!-- Search Results Dropdown -->
                    <div x-show="searchOpen && (searchResults.length > 0 || searchLoading)"
                         x-transition
                         class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 mt-2 max-h-96 overflow-y-auto">

                        <!-- Loading State -->
                        <div x-show="searchLoading" class="p-6 text-center">
                            <i class="fas fa-spinner fa-spin text-blue-500 text-xl mb-2"></i>
                            <p class="text-gray-500">Mencari...</p>
                        </div>

                        <!-- Search Results -->
                        <template x-for="result in searchResults" :key="result.id">
                            <a :href="result.url"
                               class="flex items-center p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors"
                               @click="searchOpen = false">
                                <img :src="result.image" :alt="result.name" class="w-12 h-12 object-cover rounded-lg mr-4">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900" x-text="result.name"></div>
                                    <div class="text-blue-600 font-semibold" x-text="result.price"></div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Actions - Minimalist -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Search -->
                <button @click="searchOpen = !searchOpen"
                        class="lg:hidden p-2 text-gray-600 hover:text-blue-600 transition-colors">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <!-- Wishlist -->
                <div class="relative">
                    <a href="{{ route('wishlist.index') }}"
                       class="p-2 text-gray-600 hover:text-red-500 transition-colors group">
                        <div class="relative">
                            <i class="fas fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                            @auth
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center {{ auth()->user()->wishlists->count() > 0 ? '' : 'hidden' }}">
                                {{ auth()->user()->wishlists->count() }}
                            </span>
                            @endauth
                        </div>
                    </a>
                </div>

                <!-- Shopping Cart -->
                @if(!auth()->check() || (auth()->check() && auth()->user()->canUseCart()))
                <div class="relative">
                    <button @click="cartOpen = !cartOpen"
                            class="p-2 text-gray-600 hover:text-blue-600 transition-colors group relative">
                        <div class="relative">
                            <i class="fas fa-shopping-bag text-lg group-hover:scale-110 transition-transform"></i>
                            <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                                  x-show="$store.cart.count > 0"
                                  x-text="$store.cart.count">
                            </span>
                        </div>
                    </button>

                    <!-- Mini Cart Dropdown -->
                    <div x-show="cartOpen"
                         @click.away="cartOpen = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-80 bg-white border border-gray-200 rounded-2xl shadow-xl z-50">

                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">Keranjang Belanja</h3>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            <!-- Cart items would be loaded here -->
                            <div class="p-8 text-center">
                                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 mb-4">Keranjang Anda kosong</p>
                                <a href="{{ route('products.index') }}"
                                   class="btn-primary text-sm"
                                   @click="cartOpen = false">
                                    Mulai Berbelanja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- User Menu -->
                <div class="relative">
                    @auth
                    <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center space-x-2 p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="{{ auth()->user()->first_name }}"
                             class="w-8 h-8 rounded-full object-cover">
                        @else
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">
                                {{ substr(auth()->user()->first_name, 0, 1) }}
                            </span>
                        </div>
                        @endif
                        <span class="hidden xl:inline text-sm font-medium">{{ auth()->user()->first_name }}</span>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl z-50">

                        <!-- User Info -->
                        <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-2xl">
                            <div class="flex items-center space-x-3">
                                @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                     alt="{{ auth()->user()->first_name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                                @else
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <span class="text-white text-lg font-semibold">
                                        {{ substr(auth()->user()->first_name, 0, 1) }}
                                    </span>
                                </div>
                                @endif
                                <div>
                                    <div class="font-semibold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                    <div class="text-sm opacity-90">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('profile.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-user w-5 text-blue-600 mr-3"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('orders.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-shopping-bag w-5 text-green-600 mr-3"></i>
                                <span>Pesanan Saya</span>
                            </a>
                            <a href="{{ route('wishlist.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-heart w-5 text-red-600 mr-3"></i>
                                <span>Wishlist</span>
                            </a>

                            @if(auth()->user()->hasRole(['admin', 'staff']))
                            <hr class="my-2">
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-cog w-5 text-indigo-600 mr-3"></i>
                                <span>Admin Panel</span>
                            </a>
                            @endif

                            <hr class="my-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5 text-red-600 mr-3"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <!-- Guest User -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn-primary text-sm py-2 px-4">
                            Daftar
                        </a>
                    </div>
                    @endauth
                </div>

                <!-- Mobile Menu Toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 text-gray-600 hover:text-blue-600 transition-colors">
                    <i class="fas fa-bars text-lg" x-show="!mobileMenuOpen"></i>
                    <i class="fas fa-times text-lg" x-show="mobileMenuOpen"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Search -->
    <div x-show="searchOpen"
         x-transition
         class="lg:hidden bg-white border-t border-gray-200 p-4">
        <div class="relative">
            <input type="text"
                   placeholder="Cari produk..."
                   class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Navigation Links - Minimalist -->
    <div class="bg-white border-t border-gray-100 hidden lg:block">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-between py-3">
                <!-- Main Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                       class="nav-link {{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Beranda
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="nav-link {{ request()->routeIs('products.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Produk
                    </a>
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Kategori
                    </a>
                    <a href="{{ route('brands.index') }}"
                       class="nav-link {{ request()->routeIs('brands.*') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Brand
                    </a>
                    <a href="{{ route('about') }}"
                       class="nav-link {{ request()->routeIs('about') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Tentang
                    </a>
                    <a href="{{ route('contact') }}"
                       class="nav-link {{ request()->routeIs('contact') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
                        Kontak
                    </a>
                </div>

                <!-- Special Offers -->
                <div class="flex items-center space-x-6 text-sm">
                    <a href="{{ route('products.index', ['featured' => 1]) }}"
                       class="flex items-center space-x-2 text-orange-600 hover:text-orange-700 transition-colors font-medium">
                        <i class="fas fa-star"></i>
                        <span>Unggulan</span>
                    </a>
                    <a href="{{ route('products.index', ['sale' => 1]) }}"
                       class="flex items-center space-x-2 text-red-600 hover:text-red-700 transition-colors font-medium">
                        <i class="fas fa-tag"></i>
                        <span>Diskon</span>
                    </a>
                    <a href="{{ route('products.index', ['new' => 1]) }}"
                       class="flex items-center space-x-2 text-green-600 hover:text-green-700 transition-colors font-medium">
                        <i class="fas fa-sparkles"></i>
                        <span>Terbaru</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen"
         x-transition
         class="lg:hidden bg-white border-t border-gray-200">

        <!-- Mobile Navigation Links -->
        <div class="py-4 space-y-1">
            <a href="{{ route('home') }}"
               class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-home w-5 mr-3"></i>
                Beranda
            </a>
            <a href="{{ route('products.index') }}"
               class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-box w-5 mr-3"></i>
                Produk
            </a>
            <a href="{{ route('categories.index') }}"
               class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-tags w-5 mr-3"></i>
                Kategori
            </a>
            <a href="{{ route('brands.index') }}"
               class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('brands.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-store w-5 mr-3"></i>
                Brand
            </a>

            @guest
            <hr class="my-4">
            <a href="{{ route('login') }}"
               class="flex items-center px-6 py-3 text-blue-600 hover:bg-blue-50 transition-colors font-medium"
               @click="mobileMenuOpen = false">
                <i class="fas fa-sign-in-alt w-5 mr-3"></i>
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
               @click="mobileMenuOpen = false">
                <i class="fas fa-user-plus w-5 mr-3"></i>
                Daftar
            </a>
            @endguest
        </div>
    </div>
</header>

@push('scripts')
<script>
// Modern header functionality
function performSearch() {
    if (this.searchQuery.length < 2) {
        this.searchResults = [];
        this.searchLoading = false;
        return;
    }

    this.searchLoading = true;

    fetch(`{{ route('api.search.suggestions') }}?q=${encodeURIComponent(this.searchQuery)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        this.searchResults = data.suggestions || [];
        this.searchLoading = false;
    })
    .catch(error => {
        console.error('Search error:', error);
        this.searchResults = [];
        this.searchLoading = false;
    });
}

// Scroll effects for header
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;
    const header = document.querySelector('header');

    // Add/remove scrolled class
    if (currentScrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }

    // Hide/show header on scroll (optional)
    if (currentScrollY > lastScrollY && currentScrollY > 200) {
        header.style.transform = 'translateY(-100%)';
    } else {
        header.style.transform = 'translateY(0)';
    }

    lastScrollY = currentScrollY;
});

// Close mobile menu on resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        Alpine.store('ui', { mobileMenuOpen: false });
    }
});

// Search keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[placeholder*="Cari"]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // Escape to close search results
    if (e.key === 'Escape') {
        const searchContainer = document.querySelector('[x-data]');
        if (searchContainer) {
            searchContainer.__x.$data.searchOpen = false;
        }
    }
});

// Cart counter update
function updateCartCount(count) {
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Wishlist counter update
function updateWishlistCount(count) {
    document.querySelectorAll('.wishlist-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Initialize header on page load
document.addEventListener('DOMContentLoaded', () => {
    // Load cart count
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => updateCartCount(data.count))
        .catch(() => updateCartCount(0));

    // Load wishlist count for authenticated users
    if (document.querySelector('meta[name="user-authenticated"]')) {
        fetch('/api/wishlist/count')
            .then(response => response.json())
            .then(data => updateWishlistCount(data.count))
            .catch(() => updateWishlistCount(0));
    }
});

// Header notification system
function showHeaderNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' :
        'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Export for global use
window.showHeaderNotification = showHeaderNotification;
window.updateCartCount = updateCartCount;
window.updateWishlistCount = updateWishlistCount;
</script>
@endpush

@push('styles')
<style>
/* Modern Header Styles */
.nav-link {
    @apply text-sm font-medium transition-colors duration-200 relative;
}

.nav-link:hover {
    @apply text-blue-600;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -12px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 2px;
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    border-radius: 1px;
}

/* Search input focus styles */
input[type="text"]:focus {
    @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(0)) {
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
    }
}

/* Mobile menu animation */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mobile-menu {
    animation: slideDown 0.2s ease-out;
}

/* Cart/Wishlist badge styles */
.badge-count {
    @apply absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium;
    font-size: 0.625rem;
    line-height: 1;
}

/* Search results hover effect */
.search-result-item:hover {
    @apply bg-gray-50;
}

/* Header scroll effect */
.scrolled {
    @apply shadow-lg;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .nav-link.active::after {
        display: none;
    }
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    /* Add dark mode styles if needed */
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .nav-link {
        @apply border-b border-transparent;
    }

    .nav-link:hover {
        @apply border-blue-600;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .nav-link,
    .btn-primary,
    .group-hover\:scale-110 {
        transition: none !important;
    }

    .nav-link.active::after {
        transition: none !important;
    }
}
</style>
@endpush
