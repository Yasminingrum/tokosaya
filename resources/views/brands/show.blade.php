@extends('layouts.app')

@section('title', $brand->name . ' - Brand Terpercaya di TokoSaya')

@section('meta')
<meta name="description" content="{{ $brand->description ?? 'Jelajahi koleksi lengkap produk ' . $brand->name . ' di TokoSaya. Produk original dengan kualitas terjamin dan harga terbaik.' }}">
<meta name="keywords" content="{{ $brand->name }}, produk {{ strtolower($brand->name) }}, original, terpercaya, kualitas">
<meta property="og:title" content="{{ $brand->name }} - Brand Terpercaya di TokoSaya">
<meta property="og:description" content="{{ $brand->description ?? 'Jelajahi koleksi lengkap produk ' . $brand->name }}">
<meta property="og:image" content="{{ $brand->logo ? asset('storage/' . $brand->logo) : asset('images/brand-default-og.jpg') }}">
<meta property="og:url" content="{{ route('brands.show', $brand->slug) }}">
@endsection

@section('content')
<!-- Brand Hero Section -->
<section class="bg-gradient-to-r from-gray-900 to-gray-700 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <!-- Brand Logo -->
                <div class="flex-shrink-0" data-aos="fade-right">
                    <div class="w-32 h-32 lg:w-48 lg:h-48 bg-white rounded-2xl p-6 flex items-center justify-center shadow-2xl">
                        @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}"
                             alt="{{ $brand->name }}"
                             class="max-w-full max-h-full object-contain">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-4xl lg:text-6xl font-bold text-white">{{ substr($brand->name, 0, 1) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Brand Info -->
                <div class="flex-1 text-center lg:text-left" data-aos="fade-left">
                    <div class="flex items-center justify-center lg:justify-start gap-3 mb-4">
                        <h1 class="text-4xl lg:text-6xl font-bold">{{ $brand->name }}</h1>
                        @if($brand->is_verified)
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1">
                            <i class="fas fa-check-circle"></i>
                            Verified
                        </span>
                        @endif
                    </div>

                    @if($brand->description)
                    <p class="text-xl lg:text-2xl mb-6 opacity-90 leading-relaxed">
                        {{ $brand->description }}
                    </p>
                    @endif

                    <!-- Brand Statistics -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-400">{{ $statistics['total_products'] }}</div>
                            <div class="text-sm opacity-80">Total Produk</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-400">{{ number_format($statistics['avg_rating'], 1) }}</div>
                            <div class="text-sm opacity-80">Rating Rata-rata</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-400">{{ number_format($statistics['total_reviews']) }}</div>
                            <div class="text-sm opacity-80">Total Review</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-400">{{ number_format($statistics['total_sales']) }}</div>
                            <div class="text-sm opacity-80">Produk Terjual</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#products"
                           class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-bag"></i>
                            Lihat Semua Produk
                        </a>
                        @if($brand->website)
                        <a href="{{ $brand->website }}"
                           target="_blank"
                           class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-gray-900 transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-external-link-alt"></i>
                            Website Resmi
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Navigation -->
@if($categories->count() > 0)
<section class="bg-white py-8 sticky top-0 z-40 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center gap-4 overflow-x-auto pb-2">
                <span class="text-gray-600 font-medium whitespace-nowrap">Kategori:</span>
                <a href="{{ route('brands.show', $brand->slug) }}"
                   class="px-4 py-2 rounded-lg transition-colors whitespace-nowrap {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua ({{ $statistics['total_products'] }})
                </a>
                @foreach($categories as $category)
                <a href="{{ route('brands.show', ['brand' => $brand->slug, 'category' => $category->id]) }}"
                   class="px-4 py-2 rounded-lg transition-colors whitespace-nowrap {{ request('category') == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $category->name }} ({{ $category->products_count }})
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Products Section -->
<section id="products" class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8" data-aos="fade-up">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Produk {{ $brand->name }}
                        @if(request('category'))
                        - {{ $categories->where('id', request('category'))->first()->name ?? '' }}
                        @endif
                    </h2>
                    <p class="text-gray-600">
                        Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                    </p>
                </div>

                <!-- Sort and Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <!-- Search -->
                    <form action="{{ route('brands.show', $brand->slug) }}" method="GET" class="flex-1 lg:flex-initial">
                        @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari produk..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </form>

                    <!-- Sort Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors min-w-[140px]">
                            <i class="fas fa-sort"></i>
                            <span class="text-sm">{{ $sortOptions[request('sort', 'name_asc')] ?? 'Urutkan' }}</span>
                            <i class="fas fa-chevron-down text-xs transform transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition
                             class="absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            @foreach($sortOptions as $value => $label)
                            <a href="{{ route('brands.show', array_merge(['brand' => $brand->slug], request()->query(), ['sort' => $value])) }}"
                               class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors {{ request('sort') === $value ? 'bg-blue-50 text-blue-600' : '' }}">
                                {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-1">
                        <button onclick="setViewMode('grid')"
                                class="p-2 rounded transition-colors view-toggle {{ request('view', 'grid') === 'grid' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                            <i class="fas fa-th-large text-sm"></i>
                        </button>
                        <button onclick="setViewMode('list')"
                                class="p-2 rounded transition-colors view-toggle {{ request('view') === 'list' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                            <i class="fas fa-list text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if($products->count() > 0)
            <!-- Products Grid -->
            <div id="products-container" class="{{ request('view', 'grid') === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }}">
                @foreach($products as $product)
                @include('components.product-card', ['product' => $product, 'viewMode' => request('view', 'grid')])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>

            @else
            <!-- Empty State -->
            <div class="text-center py-16" data-aos="fade-up">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Produk Tidak Ditemukan</h3>
                    <p class="text-gray-600 mb-8">
                        @if(request('search'))
                        Tidak ada produk dari {{ $brand->name }} yang ditemukan untuk pencarian "<strong>{{ request('search') }}</strong>".
                        @elseif(request('category'))
                        Belum ada produk {{ $brand->name }} dalam kategori {{ $categories->where('id', request('category'))->first()->name ?? 'ini' }}.
                        @else
                        Belum ada produk yang tersedia dari {{ $brand->name }}.
                        @endif
                    </p>

                    @if(request()->hasAny(['search', 'category']))
                    <div class="space-y-3">
                        <a href="{{ route('brands.show', $brand->slug) }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-refresh"></i>
                            Lihat Semua Produk
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

<!-- Related Brands Section -->
@if($relatedBrands->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Brand Lainnya</h2>
                <p class="text-xl text-gray-600">Jelajahi brand terpercaya lainnya di TokoSaya</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($relatedBrands as $relatedBrand)
                <a href="{{ route('brands.show', $relatedBrand->slug) }}"
                   class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition-all duration-300 group"
                   data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="w-16 h-16 mx-auto mb-4">
                        @if($relatedBrand->logo)
                        <img src="{{ asset('storage/' . $relatedBrand->logo) }}"
                             alt="{{ $relatedBrand->name }}"
                             class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="text-xl font-bold text-white">{{ substr($relatedBrand->name, 0, 1) }}</span>
                        </div>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                        {{ $relatedBrand->name }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ $relatedBrand->product_count }} Produk</p>
                </a>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('products.brand') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Lihat Semua Brand
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Brand Reviews Section -->
@if($reviews->count() > 0)
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Review Pelanggan</h2>
                <p class="text-xl text-gray-600">Apa kata pelanggan tentang produk {{ $brand->name }}</p>
            </div>

            <!-- Review Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8" data-aos="fade-up">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Overall Rating -->
                    <div class="text-center">
                        <div class="text-6xl font-bold text-blue-600 mb-2">{{ number_format($statistics['avg_rating'], 1) }}</div>
                        <div class="flex justify-center items-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($statistics['avg_rating']) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-gray-600">dari {{ number_format($statistics['total_reviews']) }} review</p>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="space-y-2">
                        @for($i = 5; $i >= 1; $i--)
                        @php
                        $ratingCount = $ratingBreakdown[$i] ?? 0;
                        $percentage = $statistics['total_reviews'] > 0 ? ($ratingCount / $statistics['total_reviews']) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-8">{{ $i }} ★</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-12">{{ $ratingCount }}</span>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-md p-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ substr($review->user->first_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $review->user->first_name }}</div>
                                <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>

                    <!-- Review Content -->
                    @if($review->title)
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                    @endif

                    <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ Str::limit($review->review, 150) }}</p>

                    <!-- Product Info -->
                    <div class="pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            Produk: <span class="font-medium">{{ $review->product->name }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('brands.reviews', $brand->slug) }}"
                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Lihat Semua Review
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Newsletter Subscription -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                Dapatkan Update Produk {{ $brand->name }}
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Berlangganan untuk mendapatkan notifikasi produk baru dan penawaran eksklusif dari {{ $brand->name }}
            </p>

            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                @csrf
                <input type="hidden" name="brand_id" value="{{ $brand->id }}">
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

/* Smooth scrolling for anchor links */
html {
    scroll-behavior: smooth;
}

/* Category navigation scrollbar styling */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* View toggle hover effects */
.view-toggle {
    transition: all 0.2s ease;
}

.view-toggle:hover {
    transform: scale(1.05);
}

/* Rating stars animation */
.fas.fa-star {
    transition: color 0.2s ease;
}

/* Product card hover animations */
.product-card {
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-2px);
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
    const container = document.getElementById('products-container');
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
        toggle.classList.add('hover:bg-gray-100');
    });

    event.target.classList.add('bg-blue-600', 'text-white');
    event.target.classList.remove('hover:bg-gray-100');

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
                this.form.submit();
            }
        }, 1000);
    });
}

// Smooth scroll to products section
document.addEventListener('DOMContentLoaded', function() {
    // Handle anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.getBoundingClientRect().top + window.pageYOffset - 100;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Initialize view mode from URL
    const urlParams = new URLSearchParams(window.location.search);
    const viewMode = urlParams.get('view') || 'grid';

    if (viewMode === 'list') {
        const listToggle = document.querySelector('.view-toggle:last-child');
        if (listToggle) {
            listToggle.click();
        }
    }

    // Product card animations
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;

        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Lazy loading for better performance
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    }
});

// Enhanced search with suggestions
function setupSearchSuggestions() {
    const searchInput = document.querySelector('input[name="search"]');
    if (!searchInput) return;

    let suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden';
    searchInput.parentNode.appendChild(suggestionsContainer);

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length >= 2) {
            // Simulate API call for suggestions
            setTimeout(() => {
                const suggestions = [
                    `${query} smartphone`,
                    `${query} laptop`,
                    `${query} accessories`,
                    `${query} case`,
                    `${query} charger`
                ].filter(s => s.toLowerCase().includes(query.toLowerCase()));

                if (suggestions.length > 0) {
                    suggestionsContainer.innerHTML = suggestions.map(suggestion =>
                        `<div class="p-2 hover:bg-gray-50 cursor-pointer text-sm" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`
                    ).join('');
                    suggestionsContainer.classList.remove('hidden');
                } else {
                    suggestionsContainer.classList.add('hidden');
                }
            }, 300);
        } else {
            suggestionsContainer.classList.add('hidden');
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.parentNode.contains(e.target)) {
            suggestionsContainer.classList.add('hidden');
        }
    });
}

function selectSuggestion(suggestion) {
    const searchInput = document.querySelector('input[name="search"]');
    searchInput.value = suggestion;
    document.querySelector('.absolute.top-full').classList.add('hidden');
    searchInput.form.submit();
}

// Initialize search suggestions
setupSearchSuggestions();

// Analytics tracking
function trackBrandView() {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'view_brand', {
            brand_name: '{{ $brand->name }}',
            brand_id: {{ $brand->id }},
            total_products: {{ $statistics['total_products'] }}
        });
    }
}

// Track brand view on page load
document.addEventListener('DOMContentLoaded', trackBrandView);
</script>
@endpush
