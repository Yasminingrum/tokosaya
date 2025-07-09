@extends('layouts.app')

@section('title', 'Brand Terpercaya - TokoSaya')

@section('meta')
<meta name="description" content="Jelajahi koleksi brand terpercaya di TokoSaya. Temukan produk berkualitas dari brand ternama dengan jaminan keaslian dan layanan terbaik.">
<meta name="keywords" content="brand terpercaya, brand ternama, produk original, kualitas terjamin">
<meta property="og:title" content="Brand Terpercaya - TokoSaya">
<meta property="og:description" content="Jelajahi koleksi brand terpercaya di TokoSaya">
<meta property="og:image" content="{{ asset('images/brands-og.jpg') }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl lg:text-6xl font-bold mb-6" data-aos="fade-up">
                Brand <span class="text-yellow-400">Terpercaya</span>
            </h1>
            <p class="text-xl lg:text-2xl mb-8 opacity-90" data-aos="fade-up" data-aos-delay="200">
                Koleksi lengkap dari brand-brand ternama dengan jaminan keaslian dan kualitas terbaik
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-400">{{ $statistics['total_brands'] }}+</div>
                    <div class="text-sm opacity-80">Brand Resmi</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-400">{{ number_format($statistics['total_products']) }}+</div>
                    <div class="text-sm opacity-80">Produk Original</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-400">{{ number_format($statistics['happy_customers']) }}+</div>
                    <div class="text-sm opacity-80">Pelanggan Puas</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="bg-gray-50 py-8 sticky top-0 z-40 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <form action="{{ route('products.brand') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari brand favorit Anda..."
                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Sort Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button"
                            @click="open = !open"
                            class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sort"></i>
                        <span>{{ $sortOptions[request('sort', 'name_asc')] ?? 'Urutkan' }}</span>
                        <i class="fas fa-chevron-down text-sm transform transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute top-full right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        @foreach($sortOptions as $value => $label)
                        <button type="submit"
                                name="sort"
                                value="{{ $value }}"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors {{ request('sort') === $value ? 'bg-blue-50 text-blue-600' : '' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Filter Toggle -->
                <button type="button"
                        @click="$refs.filters.classList.toggle('hidden')"
                        class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
            </form>

            <!-- Advanced Filters -->
            <div x-ref="filters" class="mt-6 p-6 bg-white border border-gray-200 rounded-lg {{ request()->hasAny(['category', 'min_products']) ? '' : 'hidden' }}">
                <form action="{{ route('products.brand') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Product Count Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Produk</label>
                            <select name="min_products" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="10" {{ request('min_products') == '10' ? 'selected' : '' }}>10+ Produk</option>
                                <option value="50" {{ request('min_products') == '50' ? 'selected' : '' }}>50+ Produk</option>
                                <option value="100" {{ request('min_products') == '100' ? 'selected' : '' }}>100+ Produk</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('products.brand') }}" class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Brands Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            @if($brands->count() > 0)
            <!-- Results Info -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
                <div class="text-gray-600">
                    Menampilkan {{ $brands->firstItem() ?? 0 }} - {{ $brands->lastItem() ?? 0 }} dari {{ $brands->total() }} brand
                    @if(request('search'))
                    untuk "<strong>{{ request('search') }}</strong>"
                    @endif
                </div>

                <div class="flex items-center gap-2 mt-4 sm:mt-0">
                    <span class="text-sm text-gray-500">Tampilan:</span>
                    <button onclick="setViewMode('grid')"
                            class="p-2 border border-gray-200 rounded-lg transition-colors view-toggle {{ request('view', 'grid') === 'grid' ? 'bg-blue-600 text-white' : 'hover:bg-gray-50' }}">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button onclick="setViewMode('list')"
                            class="p-2 border border-gray-200 rounded-lg transition-colors view-toggle {{ request('view') === 'list' ? 'bg-blue-600 text-white' : 'hover:bg-gray-50' }}">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Brands Grid/List -->
            <div id="brands-container" class="{{ request('view', 'grid') === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }}">
                @foreach($brands as $brand)
                @if(request('view', 'grid') === 'grid')
                <!-- Grid View -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group" data-aos="fade-up">
                    <div class="relative">
                        <!-- Brand Logo -->
                        <div class="aspect-w-16 aspect-h-10 bg-gray-100 flex items-center justify-center p-6">
                            @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="max-h-20 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ substr($brand->name, 0, 1) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Product Count Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $brand->product_count }} Produk
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                            {{ $brand->name }}
                        </h3>

                        @if($brand->description)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit($brand->description, 120) }}
                        </p>
                        @endif

                        <!-- Brand Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 py-3 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-600">{{ $brand->products_count ?? 0 }}</div>
                                <div class="text-xs text-gray-500">Produk</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600">
                                    {{ number_format($brand->avg_rating ?? 0, 1) }}
                                </div>
                                <div class="text-xs text-gray-500">Rating</div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('brands.show', $brand->slug) }}"
                           class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Lihat Produk
                        </a>
                    </div>
                </div>
                @else
                <!-- List View -->
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden" data-aos="fade-up">
                    <div class="flex items-center p-6">
                        <!-- Brand Logo -->
                        <div class="flex-shrink-0 mr-6">
                            @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="w-16 h-16 object-contain">
                            @else
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-white">{{ substr($brand->name, 0, 1) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Brand Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $brand->name }}</h3>
                                    @if($brand->description)
                                    <p class="text-gray-600 text-sm mb-3 max-w-2xl">
                                        {{ Str::limit($brand->description, 150) }}
                                    </p>
                                    @endif

                                    <!-- Tags -->
                                    <div class="flex flex-wrap gap-2">
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                            {{ $brand->product_count }} Produk
                                        </span>
                                        @if($brand->is_verified)
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1"></i>Terverifikasi
                                        </span>
                                        @endif
                                        @if($brand->avg_rating >= 4.5)
                                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-star mr-1"></i>Top Rated
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Stats and Action -->
                                <div class="text-right ml-6">
                                    <div class="text-sm text-gray-500 mb-2">Rating: {{ number_format($brand->avg_rating ?? 0, 1) }}/5</div>
                                    <a href="{{ route('brands.show', $brand->slug) }}"
                                       class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Lihat Produk
                                        <i class="fas fa-arrow-right text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $brands->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>

            @else
            <!-- Empty State -->
            <div class="text-center py-16" data-aos="fade-up">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-store text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Brand Tidak Ditemukan</h3>
                    <p class="text-gray-600 mb-8">
                        @if(request('search'))
                        Tidak ada brand yang ditemukan untuk pencarian "<strong>{{ request('search') }}</strong>".
                        @else
                        Belum ada brand yang tersedia saat ini.
                        @endif
                    </p>

                    @if(request()->hasAny(['search', 'category', 'min_products']))
                    <div class="space-y-3">
                        <a href="{{ route('products.brand') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-refresh"></i>
                            Lihat Semua Brand
                        </a>
                        <div class="text-sm text-gray-500">
                            atau coba kata kunci yang berbeda
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Featured Categories -->
@if($featuredCategories->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    Jelajahi Berdasarkan Kategori
                </h2>
                <p class="text-xl text-gray-600">
                    Temukan brand favorit berdasarkan kategori produk yang Anda cari
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($featuredCategories as $category)
                <a href="{{ route('products.brand', ['category' => $category->id]) }}"
                   class="group bg-white rounded-lg p-6 text-center hover:shadow-lg transition-all duration-300"
                   data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    @if($category->icon)
                    <div class="w-16 h-16 mx-auto mb-4 text-3xl text-blue-600 group-hover:scale-110 transition-transform">
                        <i class="{{ $category->icon }}"></i>
                    </div>
                    @endif
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->brands_count }} Brand</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                Dapatkan Update Brand Terbaru
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Berlangganan newsletter untuk mendapatkan informasi brand baru dan penawaran eksklusif
            </p>

            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                @csrf
                <input type="email"
                       name="email"
                       placeholder="Masukkan email Anda"
                       class="flex-1 px-6 py-3 rounded-lg text-gray-900 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                       required>
                <button type="submit"
                        class="bg-yellow-400 text-gray-900 px-8 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition-colors">
                    Berlangganan
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.view-toggle.active {
    background-color: #2563eb;
    color: white;
}

/* Custom pagination styles */
.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.pagination .page-link {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}

.pagination .page-item.active .page-link {
    background-color: #2563eb;
    border-color: #2563eb;
    color: white;
}

.pagination .page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
// View mode toggle functionality
function setViewMode(mode) {
    const container = document.getElementById('brands-container');
    const toggles = document.querySelectorAll('.view-toggle');

    // Update container classes
    if (mode === 'grid') {
        container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
    } else {
        container.className = 'space-y-4';
    }

    // Update toggle buttons
    toggles.forEach(toggle => {
        toggle.classList.remove('bg-blue-600', 'text-white');
        toggle.classList.add('hover:bg-gray-50');
    });

    event.target.classList.add('bg-blue-600', 'text-white');
    event.target.classList.remove('hover:bg-gray-50');

    // Update URL parameter
    const url = new URL(window.location);
    url.searchParams.set('view', mode);
    window.history.pushState({}, '', url);

    // Re-trigger AOS animations
    AOS.refresh();
}

// Search functionality with debounce
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                // Auto-submit search after 500ms of no typing
                // this.form.submit();
            }
        }, 500);
    });
}

// Filter toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize view mode from URL
    const urlParams = new URLSearchParams(window.location.search);
    const viewMode = urlParams.get('view') || 'grid';

    if (viewMode === 'list') {
        setViewMode('list');
    }

    // Brand card hover effects
    const brandCards = document.querySelectorAll('.group');
    brandCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Infinite scroll for better UX (optional)
let isLoading = false;
let hasMorePages = {{ $brands->hasMorePages() ? 'true' : 'false' }};
let currentPage = {{ $brands->currentPage() }};

function loadMoreBrands() {
    if (isLoading || !hasMorePages) return;

    isLoading = true;
    currentPage++;

    const url = new URL(window.location);
    url.searchParams.set('page', currentPage);

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newBrands = doc.querySelectorAll('#brands-container > div');
        const container = document.getElementById('brands-container');

        newBrands.forEach(brand => {
            container.appendChild(brand);
        });

        // Check if there are more pages
        const pagination = doc.querySelector('.pagination');
        hasMorePages = pagination && pagination.querySelector('.page-item:last-child:not(.disabled)');

        isLoading = false;
        AOS.refresh();
    })
    .catch(error => {
        console.error('Error loading more brands:', error);
        isLoading = false;
    });
}

// Scroll to load more (optional feature)
window.addEventListener('scroll', function() {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
        loadMoreBrands();
    }
});
</script>
@endpush
