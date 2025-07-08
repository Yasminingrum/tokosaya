<!-- Main Header -->
<header class="bg-white shadow-md sticky top-0 z-50" x-data="{
    mobileMenuOpen: false,
    searchOpen: false,
    cartOpen: false,
    userMenuOpen: false,
    categoriesOpen: false,
    searchQuery: '',
    searchResults: [],
    searchLoading: false
}">
    <!-- Top Bar -->
    <div class="bg-gray-900 text-white py-2 hidden lg:block">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center text-sm">
                <!-- Left Side -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-phone text-blue-400"></i>
                        <span>Hubungi Kami: <a href="tel:+6280412345678" class="hover:text-blue-400 transition-colors">0804-1-234-5678</a></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-envelope text-blue-400"></i>
                        <span>Email: <a href="mailto:info@tokosaya.id" class="hover:text-blue-400 transition-colors">info@tokosaya.id</a></span>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-truck text-green-400"></i>
                        <span>Gratis Ongkir min. Rp 250rb</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-yellow-400"></i>
                        <span>Garansi 100% Original</span>
                    </div>

                    <!-- Social Media -->
                    <div class="flex items-center space-x-2 border-l border-gray-600 pl-4">
                        <span class="text-xs">Ikuti Kami:</span>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-700 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-store text-white text-lg"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-700 bg-clip-text text-transparent">
                            TokoSaya
                        </h1>
                        <p class="text-xs text-gray-500 -mt-1">Belanja Mudah & Terpercaya</p>
                    </div>
                </a>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-2xl mx-8 relative">
                <form action="{{ route('search.index') }}" method="GET" class="flex w-full relative">
                    <div class="relative flex-1">
                        <input type="text"
                               name="q"
                               x-model="searchQuery"
                               @input.debounce.300ms="performSearch()"
                               @focus="searchOpen = true"
                               placeholder="Cari produk, brand, atau kategori..."
                               class="w-full pl-12 pr-4 py-3 text-sm border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                        <!-- Search Suggestions Dropdown -->
                        <div x-show="searchOpen && (searchResults.length > 0 || searchLoading)"
                             @click.away="searchOpen = false"
                             x-transition
                             class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-xl z-50 mt-1 max-h-96 overflow-y-auto">

                            <!-- Loading State -->
                            <div x-show="searchLoading" class="p-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Mencari...
                            </div>

                            <!-- Search Results -->
                            <template x-for="result in searchResults" :key="result.id">
                                <a :href="result.url"
                                   class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors"
                                   @click="searchOpen = false">
                                    <img :src="result.image" :alt="result.name" class="w-12 h-12 object-cover rounded mr-3">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900 text-sm" x-text="result.name"></div>
                                        <div class="text-blue-600 font-semibold text-sm" x-text="result.price"></div>
                                        <div class="text-xs text-gray-500" x-text="result.category"></div>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </a>
                            </template>

                            <!-- View All Results -->
                            <div x-show="searchQuery.length > 0" class="p-3 border-t border-gray-200 bg-gray-50">
                                <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(searchQuery)}`"
                                   class="flex items-center justify-center text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors"
                                   @click="searchOpen = false">
                                    <i class="fas fa-search mr-2"></i>
                                    Lihat semua hasil pencarian
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-r-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search"></i>
                        <span class="hidden xl:inline ml-2">Cari</span>
                    </button>
                </form>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Search Toggle -->
                <button @click="searchOpen = !searchOpen"
                        class="lg:hidden p-2 text-gray-600 hover:text-blue-600 transition-colors">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <!-- Compare Button -->
                <div class="relative">
                    <a href="{{ route('compare.index') }}"
                       class="flex items-center space-x-2 p-2 text-gray-600 hover:text-blue-600 transition-colors group">
                        <div class="relative">
                            <i class="fas fa-balance-scale text-lg group-hover:scale-110 transition-transform"></i>
                            <span class="compare-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">
                                0
                            </span>
                        </div>
                        <span class="hidden xl:inline text-sm font-medium">Bandingkan</span>
                    </a>
                </div>

                <!-- Wishlist Button -->
                <div class="relative">
                    <a href="{{ route('wishlist.index') }}"
                       class="flex items-center space-x-2 p-2 text-gray-600 hover:text-red-600 transition-colors group">
                        <div class="relative">
                            <i class="fas fa-heart text-lg group-hover:scale-110 transition-transform"></i>
                            @auth
                            <span class="wishlist-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center {{ auth()->user()->wishlists->count() > 0 ? '' : 'hidden' }}">
                                {{ auth()->user()->wishlists->count() }}
                            </span>
                            @endauth
                        </div>
                        <span class="hidden xl:inline text-sm font-medium">Wishlist</span>
                    </a>
                </div>

                <!-- Shopping Cart -->
                <div class="relative" x-data="{ cartItems: {{ auth()->check() && auth()->user()->shoppingCart ? auth()->user()->shoppingCart->items->count() : 0 }} }">
                    <button @click="cartOpen = !cartOpen"
                            class="flex items-center space-x-2 p-2 text-gray-600 hover:text-blue-600 transition-colors group relative">
                        <div class="relative">
                            <i class="fas fa-shopping-cart text-lg group-hover:scale-110 transition-transform"></i>
                            <span x-show="cartItems > 0"
                                  x-text="cartItems"
                                  class="cart-count absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            </span>
                        </div>
                        <span class="hidden xl:inline text-sm font-medium">Keranjang</span>
                    </button>

                    <!-- Cart Dropdown -->
                    <div x-show="cartOpen"
                         @click.away="cartOpen = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50">

                        <div class="p-4 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">Keranjang Belanja</h3>
                        </div>

                        @auth
                        @if(auth()->user()->shoppingCart && auth()->user()->shoppingCart->items->count() > 0)
                        <div class="max-h-64 overflow-y-auto">
                            @foreach(auth()->user()->shoppingCart->items->take(3) as $item)
                            <div class="flex items-center p-4 border-b border-gray-100 last:border-b-0">
                                <img src="{{ $item->product->images->first()->image_url ?? asset('images/product-placeholder.jpg') }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-12 h-12 object-cover rounded mr-3">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 text-sm leading-tight">
                                        {{ Str::limit($item->product->name, 40) }}
                                    </h4>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $item->quantity }}x {{ \App\Helpers\PriceHelper::format($item->unit_price_cents) }}
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-blue-600">
                                    {{ \App\Helpers\PriceHelper::format($item->total_price_cents) }}
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-semibold text-gray-900">Total:</span>
                                <span class="font-bold text-blue-600 text-lg">
                                    {{ \App\Helpers\PriceHelper::format(auth()->user()->shoppingCart->total_cents) }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('cart.index') }}"
                                   class="block w-full bg-gray-200 text-gray-900 text-center py-2 rounded-lg hover:bg-gray-300 transition-colors text-sm"
                                   @click="cartOpen = false">
                                    Lihat Keranjang
                                </a>
                                <a href="{{ route('checkout.index') }}"
                                   class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                   @click="cartOpen = false">
                                    Checkout
                                </a>
                            </div>
                        </div>
                        @else
                        <div class="p-8 text-center">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Keranjang Anda kosong</p>
                            <a href="{{ route('products.index') }}"
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                               @click="cartOpen = false">
                                Mulai Berbelanja
                            </a>
                        </div>
                        @endif
                        @else
                        <div class="p-8 text-center">
                            <i class="fas fa-user-lock text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Login untuk melihat keranjang</p>
                            <a href="{{ route('login') }}"
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                               @click="cartOpen = false">
                                Login
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>

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
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-xl z-50">

                        <!-- User Info -->
                        <div class="p-4 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-t-lg">
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
                                <i class="fas fa-user-circle w-5 text-blue-600 mr-3"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('orders.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-shopping-bag w-5 text-green-600 mr-3"></i>
                                <span>Pesanan Saya</span>
                                @if(auth()->user()->orders()->whereIn('status', ['pending', 'confirmed'])->count() > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->orders()->whereIn('status', ['pending', 'confirmed'])->count() }}
                                </span>
                                @endif
                            </a>
                            <a href="{{ route('profile.addresses.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-map-marker-alt w-5 text-purple-600 mr-3"></i>
                                <span>Alamat Saya</span>
                            </a>
                            <a href="{{ route('wishlist.index') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-heart w-5 text-red-600 mr-3"></i>
                                <span>Wishlist</span>
                                @if(auth()->user()->wishlists->count() > 0)
                                <span class="ml-auto text-xs text-gray-500">
                                    {{ auth()->user()->wishlists->count() }}
                                </span>
                                @endif
                            </a>
                            <a href="{{ route('profile.notifications') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-bell w-5 text-yellow-600 mr-3"></i>
                                <span>Notifikasi</span>
                                @if(auth()->user()->notifications()->where('is_read', false)->count() > 0)
                                <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->notifications()->where('is_read', false)->count() }}
                                </span>
                                @endif
                            </a>

                            @if(auth()->user()->hasRole(['admin', 'staff']))
                            <hr class="my-2">
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                               @click="userMenuOpen = false">
                                <i class="fas fa-cog w-5 text-indigo-600 mr-3"></i>
                                <span>Panel Admin</span>
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
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors text-sm font-medium hidden sm:inline-block">
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
    </div>

    <!-- Mobile Search Bar -->
    <div x-show="searchOpen"
         x-transition
         class="lg:hidden bg-white border-t border-gray-200 p-4">
        <form action="{{ route('search.index') }}" method="GET" class="flex">
            <div class="relative flex-1">
                <input type="text"
                       name="q"
                       placeholder="Cari produk..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-3 rounded-r-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Categories Navigation -->
    <div class="bg-gray-100 border-t border-gray-200 hidden lg:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <!-- Categories Dropdown -->
                <div class="relative">
                    <button @click="categoriesOpen = !categoriesOpen"
                            class="flex items-center space-x-2 bg-blue-600 text-white px-6 py-3 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-bars"></i>
                        <span class="font-medium">Semua Kategori</span>
                        <i class="fas fa-chevron-down text-sm transform transition-transform"
                           :class="{ 'rotate-180': categoriesOpen }"></i>
                    </button>

                    <!-- Categories Dropdown Menu -->
                    <div x-show="categoriesOpen"
                         @click.away="categoriesOpen = false"
                         x-transition
                         class="absolute top-full left-0 mt-0 w-80 bg-white border border-gray-200 shadow-xl z-50 max-h-96 overflow-y-auto">

                        @php
                        $mainCategories = \App\Models\Category::where('parent_id', null)
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->with(['children' => function($query) {
                                $query->where('is_active', true)->orderBy('sort_order');
                            }])
                            ->take(10)
                            ->get();
                        @endphp

                        @foreach($mainCategories as $category)
                        <div class="group relative">
                            <a href="{{ route('categories.show', $category->slug) }}"
                               class="flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100 last:border-b-0"
                               @click="categoriesOpen = false">
                                <div class="flex items-center space-x-3">
                                    @if($category->icon)
                                    <i class="{{ $category->icon }} text-blue-600 w-5"></i>
                                    @endif
                                    <span>{{ $category->name }}</span>
                                </div>
                                @if($category->children->count() > 0)
                                <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                @endif
                            </a>

                            <!-- Subcategories -->
                            @if($category->children->count() > 0)
                            <div class="absolute left-full top-0 w-64 bg-white border border-gray-200 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                @foreach($category->children as $subCategory)
                                <a href="{{ route('categories.show', $subCategory->slug) }}"
                                   class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100 last:border-b-0"
                                   @click="categoriesOpen = false">
                                    {{ $subCategory->name }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach

                        <!-- View All Categories -->
                        <div class="p-4 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('categories.index') }}"
                               class="block text-center text-blue-600 hover:text-blue-700 font-medium transition-colors"
                               @click="categoriesOpen = false">
                                Lihat Semua Kategori
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Navigation Links -->
                <nav class="flex-1 ml-8">
                    <ul class="flex items-center space-x-8">
                        <li>
                            <a href="{{ route('home') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('home') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('products.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('brands.index') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('brands.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Brand
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categories.index') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('categories.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('about') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Tentang
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}"
                               class="py-3 text-gray-700 hover:text-blue-600 transition-colors font-medium {{ request()->routeIs('contact') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                Kontak
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Special Offers -->
                <div class="flex items-center space-x-4 text-sm">
                    <a href="{{ route('products.index', ['featured' => 1]) }}"
                       class="flex items-center space-x-1 text-orange-600 hover:text-orange-700 transition-colors">
                        <i class="fas fa-fire"></i>
                        <span class="font-medium">Produk Unggulan</span>
                    </a>
                    <a href="{{ route('products.index', ['sale' => 1]) }}"
                       class="flex items-center space-x-1 text-red-600 hover:text-red-700 transition-colors">
                        <i class="fas fa-tags"></i>
                        <span class="font-medium">Diskon</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen"
         x-transition
         class="lg:hidden bg-white border-t border-gray-200">

        <!-- Mobile Categories -->
        <div class="border-b border-gray-200">
            <button @click="categoriesOpen = !categoriesOpen"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                <span class="font-medium">Kategori Produk</span>
                <i class="fas fa-chevron-down transform transition-transform"
                   :class="{ 'rotate-180': categoriesOpen }"></i>
            </button>

            <div x-show="categoriesOpen" x-transition class="bg-gray-50">
                @foreach($mainCategories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                   class="flex items-center px-8 py-2 text-gray-600 hover:text-blue-600 transition-colors"
                   @click="mobileMenuOpen = false">
                    @if($category->icon)
                    <i class="{{ $category->icon }} w-5 mr-3 text-blue-600"></i>
                    @endif
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="py-2">
            <a href="{{ route('home') }}"
               class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-home w-5 mr-3"></i>
                Beranda
            </a>
            <a href="{{ route('products.index') }}"
               class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('products.*') ? 'text-blue-600 bg-blue-50' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-shopping-bag w-5 mr-3"></i>
                Produk
            </a>
            <a href="{{ route('brands.index') }}"
               class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors {{ request()->routeIs('brands.*') ? 'text-blue-600 bg-blue-50' : '' }}"
               @click="mobileMenuOpen = false">
                <i class="fas fa-store w-5 mr-3"></i>
                Brand
            </a>
            <a href="{{ route('compare.index') }}"
               class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
               @click="mobileMenuOpen = false">
                <i class="fas fa-balance-scale w-5 mr-3"></i>
                Bandingkan Produk
            </a>

            @guest
            <hr class="my-2">
            <a href="{{ route('login') }}"
               class="block px-4 py-3 text-blue-600 hover:bg-blue-50 transition-colors font-medium"
               @click="mobileMenuOpen = false">
                <i class="fas fa-sign-in-alt w-5 mr-3"></i>
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors"
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
// Search functionality
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

// Update cart count
function updateCartCount(count) {
    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Update compare count
function updateCompareCount(count) {
    document.querySelectorAll('.compare-count').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// Initialize compare count from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const compareProducts = JSON.parse(localStorage.getItem('compareProducts') || '[]');
    updateCompareCount(compareProducts.length);
});

// Listen for storage changes to update compare count
window.addEventListener('storage', function(e) {
    if (e.key === 'compareProducts') {
        const compareProducts = JSON.parse(e.newValue || '[]');
        updateCompareCount(compareProducts.length);
    }
});

// Mobile menu scroll prevention
document.addEventListener('alpine:init', () => {
    Alpine.effect(() => {
        if (Alpine.store('mobileMenuOpen')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });
});

// Search keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
        }
    }
});

// Notification system for header actions
window.showHeaderNotification = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white text-sm transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    }`;
    notification.textContent = message;

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
};
</script>
@endpush
