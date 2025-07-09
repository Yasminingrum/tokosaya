@extends('layouts.app')

@section('title', 'Pencarian: ' . ($query ?? 'Cari Produk') . ' - TokoSaya')

@section('meta')
<meta name="description" content="Temukan produk yang Anda cari di TokoSaya. {{ $query ? 'Hasil pencarian untuk: ' . $query : 'Jutaan produk dari brand terpercaya dengan harga terbaik.' }}">
<meta name="keywords" content="cari produk, {{ $query ?? 'pencarian' }}, toko online, belanja online">
<meta property="og:title" content="Pencarian: {{ $query ?? 'Cari Produk' }} - TokoSaya">
<meta property="og:description" content="Temukan produk yang Anda cari di TokoSaya">
@endsection

@section('content')
<!-- Search Header -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6" data-aos="fade-up">
                @if($query)
                Hasil Pencarian
                @else
                Cari Produk
                @endif
            </h1>

            @if($query)
            <p class="text-xl lg:text-2xl mb-8 opacity-90" data-aos="fade-up" data-aos-delay="200">
                Menampilkan hasil untuk: <span class="font-semibold text-yellow-400">"{{ $query }}"</span>
            </p>
            @else
            <p class="text-xl lg:text-2xl mb-8 opacity-90" data-aos="fade-up" data-aos-delay="200">
                Temukan produk yang Anda inginkan dari jutaan pilihan
            </p>
            @endif

            <!-- Enhanced Search Form -->
            <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="400">
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ $query }}"
                           placeholder="Cari produk, brand, atau kategori..."
                           class="w-full pl-6 pr-16 py-4 text-lg rounded-xl text-gray-900 focus:ring-4 focus:ring-yellow-400 focus:outline-none"
                           autocomplete="off"
                           id="main-search">
                    <button type="submit"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Search Suggestions Dropdown -->
                    <div id="search-suggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden mt-2">
                        <!-- Dynamic suggestions will be inserted here -->
                    </div>
                </div>
            </form>

            @if($query && $totalResults > 0)
            <div class="mt-8 flex flex-wrap justify-center gap-4 text-sm" data-aos="fade-up" data-aos-delay="600">
                <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-2">
                    <span class="font-semibold">{{ number_format($totalResults) }}</span> produk ditemukan
                </div>
                @if($searchTime)
                <div class="bg-white/10 backdrop-blur-md rounded-lg px-4 py-2">
                    dalam <span class="font-semibold">{{ $searchTime }}s</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Filters and Sort Bar -->
<section class="bg-white py-6 sticky top-0 z-40 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <form action="{{ route('search') }}" method="GET" id="filter-form">
                <input type="hidden" name="q" value="{{ $query }}">

                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                    <!-- Quick Filters -->
                    <div class="flex flex-wrap gap-2 flex-1">
                        <span class="text-sm font-medium text-gray-700 self-center">Filter:</span>

                        <!-- Category Filter -->
                        <select name="category" onchange="document.getElementById('filter-form').submit()"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->products_count ?? 0 }})
                            </option>
                            @endforeach
                        </select>

                        <!-- Brand Filter -->
                        <select name="brand" onchange="document.getElementById('filter-form').submit()"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }} ({{ $brand->products_count ?? 0 }})
                            </option>
                            @endforeach
                        </select>

                        <!-- Price Range Filter -->
                        <select name="price_range" onchange="document.getElementById('filter-form').submit()"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Harga</option>
                            <option value="0-50000" {{ request('price_range') == '0-50000' ? 'selected' : '' }}>Di bawah Rp 50rb</option>
                            <option value="50000-100000" {{ request('price_range') == '50000-100000' ? 'selected' : '' }}>Rp 50rb - 100rb</option>
                            <option value="100000-250000" {{ request('price_range') == '100000-250000' ? 'selected' : '' }}>Rp 100rb - 250rb</option>
                            <option value="250000-500000" {{ request('price_range') == '250000-500000' ? 'selected' : '' }}>Rp 250rb - 500rb</option>
                            <option value="500000-1000000" {{ request('price_range') == '500000-1000000' ? 'selected' : '' }}>Rp 500rb - 1jt</option>
                            <option value="1000000-0" {{ request('price_range') == '1000000-0' ? 'selected' : '' }}>Di atas Rp 1jt</option>
                        </select>

                        <!-- Rating Filter -->
                        <select name="rating" onchange="document.getElementById('filter-form').submit()"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Rating</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ ⭐</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ ⭐</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ ⭐</option>
                        </select>

                        <!-- Availability Filter -->
                        <select name="availability" onchange="document.getElementById('filter-form').submit()"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="in_stock" {{ request('availability') == 'in_stock' ? 'selected' : '' }}>Tersedia</option>
                            <option value="featured" {{ request('availability') == 'featured' ? 'selected' : '' }}>Unggulan</option>
                        </select>
                    </div>

                    <!-- Sort and View Options -->
                    <div class="flex items-center gap-3">
                        <!-- Sort Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button"
                                    @click="open = !open"
                                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-sort text-gray-400"></i>
                                <span class="text-sm">{{ $sortOptions[request('sort', 'relevance')] ?? 'Relevansi' }}</span>
                                <i class="fas fa-chevron-down text-xs transform transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                                @foreach($sortOptions as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $value]) }}"
                                   class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors {{ request('sort') === $value ? 'bg-blue-50 text-blue-600' : '' }}">
                                    {{ $label }}
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- View Toggle -->
                        <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-1">
                            <button type="button" onclick="setViewMode('grid')"
                                    class="p-2 rounded transition-colors view-toggle {{ request('view', 'grid') === 'grid' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                                <i class="fas fa-th-large text-sm"></i>
                            </button>
                            <button type="button" onclick="setViewMode('list')"
                                    class="p-2 rounded transition-colors view-toggle {{ request('view') === 'list' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                                <i class="fas fa-list text-sm"></i>
                            </button>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['category', 'brand', 'price_range', 'rating', 'availability']))
                        <a href="{{ route('search', ['q' => $query]) }}"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-times mr-1"></i>Reset Filter
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            @if(!$query)
            <!-- No Search Query - Show Popular/Trending -->
            <div class="space-y-12">
                <!-- Trending Searches -->
                @if($trendingSearches->count() > 0)
                <div data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Pencarian Populer</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($trendingSearches as $trending)
                        <a href="{{ route('search', ['q' => $trending->query]) }}"
                           class="bg-gray-100 hover:bg-blue-100 px-4 py-2 rounded-full text-sm transition-colors">
                            {{ $trending->query }}
                            <span class="text-gray-500 ml-1">({{ number_format($trending->count) }})</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Popular Categories -->
                @if($popularCategories->count() > 0)
                <div data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Kategori Populer</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        @foreach($popularCategories as $category)
                        <a href="{{ route('search', ['q' => $category->name]) }}"
                           class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition-all duration-300 group">
                            @if($category->icon)
                            <div class="text-3xl text-blue-600 mb-3 group-hover:scale-110 transition-transform">
                                <i class="{{ $category->icon }}"></i>
                            </div>
                            @endif
                            <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">{{ number_format($category->products_count) }} produk</p>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recent Products -->
                @if($recentProducts->count() > 0)
                <div data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terbaru</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($recentProducts as $product)
                        @include('components.product-card', ['product' => $product, 'viewMode' => 'grid'])
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @elseif($products->count() > 0)
            <!-- Search Results -->
            <div class="space-y-6">
                <!-- Results Summary -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" data-aos="fade-up">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            Hasil Pencarian untuk "{{ $query }}"
                        </h2>
                        <p class="text-gray-600">
                            Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ number_format($products->total()) }} produk
                        </p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex items-center gap-2 text-sm">
                        <button onclick="saveSearch('{{ $query }}')"
                                class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="fas fa-bookmark"></i>
                            Simpan Pencarian
                        </button>
                        <button onclick="shareSearch()"
                                class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="fas fa-share"></i>
                            Bagikan
                        </button>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['category', 'brand', 'price_range', 'rating', 'availability']))
                <div class="flex flex-wrap items-center gap-2 p-4 bg-blue-50 rounded-lg" data-aos="fade-up">
                    <span class="text-sm font-medium text-blue-800">Filter aktif:</span>

                    @if(request('category'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Kategori: {{ $categories->where('id', request('category'))->first()->name ?? 'Unknown' }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif

                    @if(request('brand'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Brand: {{ $brands->where('id', request('brand'))->first()->name ?? 'Unknown' }}
                        <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}" class="hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif

                    @if(request('price_range'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Harga: {{ str_replace('-', ' - Rp ', 'Rp ' . request('price_range')) }}
                        <a href="{{ request()->fullUrlWithQuery(['price_range' => null]) }}" class="hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif

                    @if(request('rating'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Rating: {{ request('rating') }}+ ⭐
                        <a href="{{ request()->fullUrlWithQuery(['rating' => null]) }}" class="hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                </div>
                @endif

                <!-- Products Grid/List -->
                <div id="products-container" class="{{ request('view', 'grid') === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }}">
                    @foreach($products as $product)
                    @include('components.product-card', ['product' => $product, 'viewMode' => request('view', 'grid')])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center" data-aos="fade-up">
                    {{ $products->appends(request()->query())->links('vendor.pagination.default') }}
                </div>

                <!-- Search Suggestions -->
                @if($searchSuggestions->count() > 0)
                <div class="mt-12 p-6 bg-gray-50 rounded-lg" data-aos="fade-up">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mungkin Anda juga mencari:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($searchSuggestions as $suggestion)
                        <a href="{{ route('search', ['q' => $suggestion]) }}"
                           class="bg-white hover:bg-blue-50 px-4 py-2 rounded-lg text-sm border border-gray-200 transition-colors">
                            {{ $suggestion }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @else
            <!-- No Results -->
            <div class="text-center py-16" data-aos="fade-up">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Tidak Ada Hasil</h3>
                    <p class="text-gray-600 mb-8">
                        Pencarian untuk "<strong>{{ $query }}</strong>" tidak ditemukan.
                        <br>Coba gunakan kata kunci yang berbeda atau lebih umum.
                    </p>

                    <!-- Search Tips -->
                    <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="font-semibold text-blue-900 mb-3">Tips Pencarian:</h4>
                        <ul class="text-sm text-blue-800 space-y-2">
                            <li>• Gunakan kata kunci yang lebih umum</li>
                            <li>• Periksa ejaan kata kunci</li>
                            <li>• Coba gunakan sinonim atau kata serupa</li>
                            <li>• Kurangi jumlah kata kunci</li>
                        </ul>
                    </div>

                    <!-- Alternative Suggestions -->
                    @if($alternativeSuggestions->count() > 0)
                    <div class="mb-8">
                        <h4 class="font-semibold text-gray-900 mb-4">Mungkin maksud Anda:</h4>
                        <div class="flex flex-wrap gap-2 justify-center">
                            @foreach($alternativeSuggestions as $suggestion)
                            <a href="{{ route('search', ['q' => $suggestion]) }}"
                               class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg text-sm transition-colors">
                                {{ $suggestion }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Browse Categories -->
                    <div class="space-y-4">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                            Jelajahi Semua Produk
                        </a>
                        <div class="text-sm text-gray-500">
                            atau coba kategori di bawah ini
                        </div>
                    </div>

                    <!-- Popular Categories for No Results -->
                    @if($popularCategories->count() > 0)
                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($popularCategories->take(4) as $category)
                        <a href="{{ route('search', ['q' => $category->name]) }}"
                           class="bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-4 text-center transition-colors">
                            @if($category->icon)
                            <div class="text-2xl text-blue-600 mb-2">
                                <i class="{{ $category->icon }}"></i>
                            </div>
                            @endif
                            <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Recently Viewed -->
@if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center" data-aos="fade-up">
                Produk yang Baru Dilihat
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recentlyViewed as $product)
                @include('components.product-card', ['product' => $product, 'viewMode' => 'grid'])
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
/* Search suggestions styling */
#search-suggestions {
    max-height: 300px;
    overflow-y: auto;
}

#search-suggestions .suggestion-item {
    padding: 12px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

#search-suggestions .suggestion-item:hover,
#search-suggestions .suggestion-item.active {
    background-color: #f3f4f6;
}

#search-suggestions .suggestion-query {
    font-weight: 500;
    color: #1f2937;
}

#search-suggestions .suggestion-category {
    font-size: 0.875rem;
    color: #6b7280;
}

/* View mode transitions */
#products-container {
    transition: all 0.3s ease;
}

/* Filter chips */
.filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #eff6ff;
    color: #1d4ed8;
    padding: 0.5rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.filter-chip .remove-filter {
    color: #6b7280;
    transition: color 0.2s;
}

.filter-chip .remove-filter:hover {
    color: #1d4ed8;
}

/* Loading states */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-form > div {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Search suggestions functionality
let searchTimeout;
let currentSuggestionIndex = -1;

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('main-search');
    const suggestionsContainer = document.getElementById('search-suggestions');

    if (searchInput && suggestionsContainer) {
        searchInput.addEventListener('input', handleSearchInput);
        searchInput.addEventListener('keydown', handleKeyNavigation);
        document.addEventListener('click', hideSuggestions);
    }

    // Initialize view mode
    const urlParams = new URLSearchParams(window.location.search);
    const viewMode = urlParams.get('view') || 'grid';
    setViewMode(viewMode);
});

function handleSearchInput(e) {
    const query = e.target.value.trim();

    clearTimeout(searchTimeout);

    if (query.length >= 2) {
        searchTimeout = setTimeout(() => {
            fetchSearchSuggestions(query);
        }, 300);
    } else {
        hideSuggestions();
    }
}

function fetchSearchSuggestions(query) {
    fetch(`{{ route('products.search.suggestions') }}?q=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        displaySuggestions(data.suggestions || []);
    })
    .catch(error => {
        console.error('Error fetching suggestions:', error);
    });
}

function displaySuggestions(suggestions) {
    const container = document.getElementById('search-suggestions');

    if (suggestions.length === 0) {
        hideSuggestions();
        return;
    }

    container.innerHTML = suggestions.map((suggestion, index) =>
        `<div class="suggestion-item" data-index="${index}" onclick="selectSuggestion('${suggestion.query}')">
            <div class="suggestion-query">${highlightQuery(suggestion.query)}</div>
            ${suggestion.category ? `<div class="suggestion-category">dalam ${suggestion.category}</div>` : ''}
        </div>`
    ).join('');

    container.classList.remove('hidden');
    currentSuggestionIndex = -1;
}

function highlightQuery(text) {
    const query = document.getElementById('main-search').value.trim();
    if (!query) return text;

    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
}

function handleKeyNavigation(e) {
    const suggestions = document.querySelectorAll('.suggestion-item');

    if (suggestions.length === 0) return;

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, suggestions.length - 1);
            updateSuggestionHighlight(suggestions);
            break;

        case 'ArrowUp':
            e.preventDefault();
            currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
            updateSuggestionHighlight(suggestions);
            break;

        case 'Enter':
            e.preventDefault();
            if (currentSuggestionIndex >= 0) {
                const selectedSuggestion = suggestions[currentSuggestionIndex];
                const query = selectedSuggestion.querySelector('.suggestion-query').textContent;
                selectSuggestion(query);
            } else {
                e.target.form.submit();
            }
            break;

        case 'Escape':
            hideSuggestions();
            break;
    }
}

function updateSuggestionHighlight(suggestions) {
    suggestions.forEach((suggestion, index) => {
        suggestion.classList.toggle('active', index === currentSuggestionIndex);
    });
}

function selectSuggestion(query) {
    document.getElementById('main-search').value = query;
    hideSuggestions();
    document.getElementById('main-search').form.submit();
}

function hideSuggestions(e) {
    if (e && document.getElementById('main-search').contains(e.target)) return;

    document.getElementById('search-suggestions').classList.add('hidden');
    currentSuggestionIndex = -1;
}

// View mode toggle
function setViewMode(mode) {
    const container = document.getElementById('products-container');
    const toggles = document.querySelectorAll('.view-toggle');

    if (!container) return;

    if (mode === 'grid') {
        container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
    } else {
        container.className = 'space-y-4';
    }

    toggles.forEach(toggle => {
        toggle.classList.remove('bg-blue-600', 'text-white');
        toggle.classList.add('hover:bg-gray-100');
    });

    const activeToggle = document.querySelector(`.view-toggle:${mode === 'grid' ? 'first' : 'last'}-child`);
    if (activeToggle) {
        activeToggle.classList.add('bg-blue-600', 'text-white');
        activeToggle.classList.remove('hover:bg-gray-100');
    }

    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('view', mode);
    window.history.pushState({}, '', url);

    // Re-trigger animations
    AOS.refresh();
}

// Save search functionality
function saveSearch(query) {
    if (!query) return;

    // Get saved searches from localStorage
    let savedSearches = JSON.parse(localStorage.getItem('savedSearches') || '[]');

    // Add new search if not already saved
    if (!savedSearches.includes(query)) {
        savedSearches.unshift(query);
        savedSearches = savedSearches.slice(0, 10); // Keep only last 10
        localStorage.setItem('savedSearches', JSON.stringify(savedSearches));

        // Show success message
        showNotification('Pencarian berhasil disimpan!', 'success');
    } else {
        showNotification('Pencarian sudah tersimpan sebelumnya', 'info');
    }
}

// Share search functionality
function shareSearch() {
    const url = window.location.href;
    const query = '{{ $query }}';

    if (navigator.share) {
        navigator.share({
            title: `Hasil pencarian: ${query} - TokoSaya`,
            text: `Lihat hasil pencarian untuk "${query}" di TokoSaya`,
            url: url
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link berhasil disalin!', 'success');
        });
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
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
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Analytics tracking
function trackSearch(query, resultsCount) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'search', {
            search_term: query,
            results_count: resultsCount
        });
    }
}

// Track current search
@if($query && isset($totalResults))
trackSearch('{{ $query }}', {{ $totalResults }});
@endif

// Infinite scroll (optional)
let isLoading = false;
let hasMorePages = {{ isset($products) && $products->hasMorePages() ? 'true' : 'false' }};
let currentPage = {{ isset($products) ? $products->currentPage() : 1 }};

function loadMoreResults() {
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
        const newProducts = doc.querySelectorAll('#products-container > *');
        const container = document.getElementById('products-container');

        newProducts.forEach(product => {
            container.appendChild(product);
        });

        // Check if there are more pages
        const pagination = doc.querySelector('.pagination');
        hasMorePages = pagination && pagination.querySelector('.page-item:last-child:not(.disabled)');

        isLoading = false;
        AOS.refresh();
    })
    .catch(error => {
        console.error('Error loading more results:', error);
        isLoading = false;
    });
}

// Auto-load more on scroll (optional)
window.addEventListener('scroll', function() {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
        loadMoreResults();
    }
});
</script>
@endpush
