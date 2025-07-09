<!-- Modern Minimalist Header - Versi Terkonsistensi -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/50 transition-all duration-300"
        x-data="{
            mobileMenuOpen: false,
            searchOpen: false,
            userMenuOpen: false,
            cartOpen: false,
            searchQuery: '',
            searchResults: [],
            searchLoading: false,
            cartCount: 0,
            wishlistCount: 0
        }"
        :class="{ 'shadow-lg': $store.ui.scrolled }">

    @push('styles')
    <style>
        /* Variabel CSS yang konsisten dengan home.blade.php */
        :root {
            --primary: #f8bbd9;
            --primary-dark: #f4a6cd;
            --primary-light: #fce7f1;
            --teal: #5fb3b4;
            --teal-dark: #4a9b9c;
            --teal-light: #b8e0e1;
            --cream: #fef7f0;
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --radius-lg: 1rem;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Tombol yang konsisten */
        .btn-primary {
            background-color: var(--primary);
            color: var(--text-dark);
            border-radius: var(--radius-lg);
            transition: var(--transition);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Nav link styles */
        .nav-link {
            color: var(--text-medium);
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            color: var(--teal);
        }

        .nav-link.active {
            color: var(--teal);
            font-weight: 500;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--teal));
            border-radius: 1px;
        }

        /* Badge styles */
        .badge-count {
            position: absolute;
            top: -0.5rem;
            right: -0.5rem;
            background-color: var(--teal);
            color: white;
            border-radius: 9999px;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.625rem;
            font-weight: 600;
        }
    </style>
    @endpush

    <!-- Top Bar -->
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
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-[var(--teal)] to-[var(--primary-dark)] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-store text-white text-lg"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-bold text-[var(--text-dark)]">
                            Toko<span class="text-[var(--teal)]">Saya</span>
                        </h1>
                        <p class="text-xs text-[var(--text-light)] -mt-1">Belanja Mudah & Terpercaya</p>
                    </div>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 max-w-xl mx-8 relative hidden lg:block">
                <div class="relative">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch()"
                           @focus="searchOpen = true"
                           @click.away="searchOpen = false"
                           placeholder="Cari produk, brand, atau kategori..."
                           class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[var(--teal)] transition-all duration-300">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-[var(--text-light)]"></i>

                    <!-- Search Results -->
                    <div x-show="searchOpen && (searchResults.length > 0 || searchLoading)"
                         x-transition
                         class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 mt-2 max-h-96 overflow-y-auto">

                        <!-- Loading State -->
                        <div x-show="searchLoading" class="p-6 text-center">
                            <i class="fas fa-spinner fa-spin text-[var(--teal)] text-xl mb-2"></i>
                            <p class="text-[var(--text-medium)]">Mencari...</p>
                        </div>

                        <!-- Results -->
                        <template x-for="result in searchResults" :key="result.id">
                            <a :href="result.url"
                               class="flex items-center p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors"
                               @click="searchOpen = false">
                                <img :src="result.image" :alt="result.name" class="w-12 h-12 object-cover rounded-lg mr-4">
                                <div class="flex-1">
                                    <div class="font-medium text-[var(--text-dark)]" x-text="result.name"></div>
                                    <div class="text-[var(--teal)] font-semibold" x-text="result.price"></div>
                                </div>
                                <i class="fas fa-chevron-right text-[var(--text-light)]"></i>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Search -->
                <button @click="searchOpen = !searchOpen"
                        class="lg:hidden p-2 text-[var(--text-medium)] hover:text-[var(--teal)] transition-colors">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <!-- Wishlist -->
                <div class="relative">
                    <a href="{{ route('wishlist.index') }}"
                       class="p-2 text-[var(--text-medium)] hover:text-red-500 transition-colors group">
                        <div class="relative">
                            <i class="fas fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                            @auth
                            <span x-show="wishlistCount > 0" class="badge-count" x-text="wishlistCount"></span>
                            @endauth
                        </div>
                    </a>
                </div>

                <!-- Shopping Cart -->
                @if(!auth()->check() || (auth()->check() && auth()->user()->canUseCart()))
                <div class="relative">
                    <button @click="cartOpen = !cartOpen"
                            class="p-2 text-[var(--text-medium)] hover:text-[var(--teal)] transition-colors group relative">
                        <div class="relative">
                            <i class="fas fa-shopping-bag text-lg group-hover:scale-110 transition-transform"></i>
                            <span x-show="cartCount > 0" class="badge-count" x-text="cartCount"></span>
                        </div>
                    </button>

                    <!-- Mini Cart -->
                    <div x-show="cartOpen"
                         @click.away="cartOpen = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-80 bg-white border border-gray-200 rounded-2xl shadow-xl z-50">

                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-[var(--text-dark)]">Keranjang Belanja</h3>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-8 text-center">
                                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                                <p class="text-[var(--text-medium)] mb-4">Keranjang Anda kosong</p>
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
                            class="flex items-center space-x-2 p-2 text-[var(--text-medium)] hover:text-[var(--teal)] transition-colors">
                        @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                             alt="{{ auth()->user()->first_name }}"
                             class="w-8 h-8 rounded-full object-cover">
                        @else
                        <div class="w-8 h-8 bg-gradient-to-br from-[var(--teal)] to-[var(--primary-dark)] rounded-full flex items-center justify-center">
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
                        <div class="p-4 bg-gradient-to-r from-[var(--teal)] to-[var(--primary-dark)] text-white rounded-t-2xl">
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
                               class="flex items-center px-4 py-3 text-[var(--text-medium)] hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-user w-5 text-[var(--teal)] mr-3"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('orders.index') }}"
                               class="flex items-center px-4 py-3 text-[var(--text-medium)] hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-shopping-bag w-5 text-green-600 mr-3"></i>
                                <span>Pesanan Saya</span>
                            </a>
                            <a href="{{ route('wishlist.index') }}"
                               class="flex items-center px-4 py-3 text-[var(--text-medium)] hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-heart w-5 text-red-600 mr-3"></i>
                                <span>Wishlist</span>
                            </a>

                            @if(auth()->user()->hasRole(['admin', 'staff']))
                            <hr class="my-2">
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-3 text-[var(--text-medium)] hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-cog w-5 text-indigo-600 mr-3"></i>
                                <span>Admin Panel</span>
                            </a>
                            @endif

                            <hr class="my-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full px-4 py-3 text-[var(--text-medium)] hover:bg-red-50 hover:text-red-600 transition-colors">
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
                           class="text-sm font-medium text-[var(--text-medium)] hover:text-[var(--teal)] transition-colors">
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
                        class="lg:hidden p-2 text-[var(--text-medium)] hover:text-[var(--teal)] transition-colors">
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
                   class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[var(--teal)] transition-all">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)]"></i>
        </div>
    </div>

    <!-- Desktop Navigation Links -->
    <div class="bg-white border-t border-gray-100 hidden lg:block">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-between py-3">
                <!-- Main Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                       class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        Produk
                    </a>
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        Kategori
                    </a>
                    <a href="{{ route('brands.index') }}"
                       class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                        Brand
                    </a>
                    <a href="{{ route('about') }}"
                       class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                        Tentang
                    </a>
                    <a href="{{ route('contact') }}"
                       class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
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
               class="flex items-center px-6 py-3 text-[var(--text-medium)] hover:bg-gray-50 hover:text-[var(--teal)] transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 text-[var(--teal)] border-r-2 border-[var(--teal)]' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-home w-5 mr-3"></i>
                Beranda
            </a>
            <a href="{{ route('products.index') }}"
               class="flex items-center px-6 py-3 text-[var(--text-medium)] hover:bg-gray-50 hover:text-[var(--teal)] transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-50 text-[var(--teal)] border-r-2 border-[var(--teal)]' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-box w-5 mr-3"></i>
                Produk
            </a>
            <a href="{{ route('categories.index') }}"
               class="flex items-center px-6 py-3 text-[var(--text-medium)] hover:bg-gray-50 hover:text-[var(--teal)] transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-[var(--teal)] border-r-2 border-[var(--teal)]' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-tags w-5 mr-3"></i>
                Kategori
            </a>
            <a href="{{ route('brands.index') }}"
               class="flex items-center px-6 py-3 text-[var(--text-medium)] hover:bg-gray-50 hover:text-[var(--teal)] transition-colors {{ request()->routeIs('brands.*') ? 'bg-blue-50 text-[var(--teal)] border-r-2 border-[var(--teal)]' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-store w-5 mr-3"></i>
                Brand
            </a>

            @guest
            <hr class="my-4">
            <a href="{{ route('login') }}"
               class="flex items-center px-6 py-3 text-[var(--teal)] hover:bg-blue-50 transition-colors font-medium"
               @click="mobileMenuOpen = false">
                <i class="fas fa-sign-in-alt w-5 mr-3"></i>
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="flex items-center px-6 py-3 text-[var(--text-medium)] hover:bg-gray-50 hover:text-[var(--teal)] transition-colors"
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
// Fungsi yang konsisten dengan home.blade.php
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

// Inisialisasi data saat komponen dimuat
document.addEventListener('alpine:init', () => {
    Alpine.store('ui', {
        scrolled: false
    });

    // Load cart count
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            Alpine.store('cart', { count: data.count || 0 });
            document.querySelector('[x-data]').__x.$data.cartCount = data.count || 0;
        })
        .catch(() => {
            Alpine.store('cart', { count: 0 });
            document.querySelector('[x-data]').__x.$data.cartCount = 0;
        });

    // Load wishlist count for authenticated users
    @auth
    fetch('{{ route("wishlist.count") }}')
        .then(response => response.json())
        .then(data => {
            document.querySelector('[x-data]').__x.$data.wishlistCount = data.count || 0;
        })
        .catch(() => {
            document.querySelector('[x-data]').__x.$data.wishlistCount = 0;
        });
    @endauth
});

// Global function untuk update cart count
window.updateCartCount = function(count) {
    if (typeof Alpine !== 'undefined' && Alpine.store('cart')) {
        Alpine.store('cart').count = count;
    }
    document.querySelector('[x-data]').__x.$data.cartCount = count;
};

// Global function untuk update wishlist count
window.updateWishlistCount = function(count) {
    document.querySelector('[x-data]').__x.$data.wishlistCount = count;
};

// Scroll effects for header
window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;
    const header = document.querySelector('header');

    // Add/remove scrolled class
    if (currentScrollY > 50) {
        header.classList.add('scrolled');
        Alpine.store('ui', { scrolled: true });
    } else {
        header.classList.remove('scrolled');
        Alpine.store('ui', { scrolled: false });
    }
});

// Close mobile menu on resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        document.querySelector('[x-data]').__x.$data.mobileMenuOpen = false;
        document.querySelector('[x-data]').__x.$data.searchOpen = false;
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
});
</script>
@endpush
