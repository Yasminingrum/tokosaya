@extends('layouts.app')

@section('title', 'Bandingkan Produk - TokoSaya')

@section('meta')
<meta name="description" content="Bandingkan produk dengan mudah di TokoSaya. Lihat perbandingan harga, spesifikasi, dan fitur untuk membantu Anda memilih produk terbaik.">
<meta name="keywords" content="bandingkan produk, perbandingan harga, compare produk, pilih produk terbaik">
<meta property="og:title" content="Bandingkan Produk - TokoSaya">
<meta property="og:description" content="Bandingkan produk dengan mudah untuk memilih yang terbaik">
@endsection

@section('content')
<!-- Compare Header -->
<section class="bg-gradient-to-r from-green-600 to-blue-700 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-4xl lg:text-5xl font-bold mb-6" data-aos="fade-up">
                Bandingkan Produk
            </h1>
            <p class="text-xl lg:text-2xl mb-8 opacity-90" data-aos="fade-up" data-aos-delay="200">
                Pilih produk terbaik dengan perbandingan yang mudah dan detail
            </p>

            <!-- Quick Stats -->
            <div class="flex flex-wrap justify-center gap-6" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-2xl font-bold text-yellow-400" id="compare-count">{{ count($compareProducts) }}</div>
                    <div class="text-sm opacity-80">Produk Dipilih</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-2xl font-bold text-yellow-400">{{ $maxCompare ?? 4 }}</div>
                    <div class="text-sm opacity-80">Maks. Produk</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Compare Controls -->
<section class="bg-white py-6 shadow-sm sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <!-- Add Product Search -->
                <div class="flex-1 max-w-md">
                    <div class="relative" x-data="{ searching: false, results: [] }">
                        <input type="text"
                               placeholder="Cari produk untuk dibandingkan..."
                               class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               @input.debounce.300ms="searchProducts($event.target.value)"
                               @focus="searching = true">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

                        <!-- Search Results Dropdown -->
                        <div x-show="searching && results.length > 0"
                             @click.away="searching = false"
                             x-transition
                             class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-50 mt-1 max-h-64 overflow-y-auto">
                            <template x-for="product in results" :key="product.id">
                                <div class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                     @click="addToCompare(product)">
                                    <img :src="product.image" :alt="product.name" class="w-12 h-12 object-cover rounded mr-3">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900" x-text="product.name"></div>
                                        <div class="text-sm text-gray-500" x-text="product.price"></div>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button onclick="clearAllCompare()"
                            class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-red-600 transition-colors">
                        <i class="fas fa-trash"></i>
                        <span class="hidden sm:inline">Hapus Semua</span>
                    </button>

                    <button onclick="shareComparison()"
                            class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-share"></i>
                        <span class="hidden sm:inline">Bagikan</span>
                    </button>

                    <button onclick="printComparison()"
                            class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-print"></i>
                        <span class="hidden sm:inline">Cetak</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($compareProducts) > 0)
<!-- Comparison Table -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Mobile Warning -->
            <div class="lg:hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2 text-yellow-800">
                    <i class="fas fa-info-circle"></i>
                    <span class="text-sm">Untuk pengalaman terbaik, gunakan layar yang lebih lebar atau putar perangkat Anda.</span>
                </div>
            </div>

            <!-- Comparison Table Container -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px]">
                        <!-- Product Images and Basic Info -->
                        <thead class="bg-gray-50">
                            <tr>
                                <td class="p-4 font-semibold text-gray-900 sticky left-0 bg-gray-50 z-10 min-w-[200px]">
                                    Produk
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center min-w-[250px]">
                                    <div class="relative group">
                                        <!-- Remove Button -->
                                        <button onclick="removeFromCompare({{ $product->id }})"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition-colors z-10">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <!-- Product Image -->
                                        <div class="mb-4">
                                            <img src="{{ $product->images->first()->image_url ?? asset('images/product-placeholder.jpg') }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-32 h-32 object-cover mx-auto rounded-lg group-hover:scale-105 transition-transform">
                                        </div>

                                        <!-- Product Name -->
                                        <h3 class="font-semibold text-gray-900 mb-2 text-sm leading-tight">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="hover:text-blue-600 transition-colors">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        <!-- Brand -->
                                        @if($product->brand)
                                        <div class="text-xs text-gray-500 mb-2">{{ $product->brand->name }}</div>
                                        @endif

                                        <!-- Rating -->
                                        <div class="flex items-center justify-center gap-1 mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= round($product->rating_average) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                            <span class="text-xs text-gray-500 ml-1">({{ $product->rating_count }})</span>
                                        </div>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Price Comparison -->
                            <tr class="border-t border-gray-200">
                                <td class="p-4 font-semibold text-gray-900 bg-gray-50 sticky left-0 z-10">
                                    <i class="fas fa-tag text-green-600 mr-2"></i>Harga
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center">
                                    <div class="space-y-1">
                                        <div class="text-xl font-bold text-green-600">
                                            {{ \App\Helpers\PriceHelper::format($product->price_cents) }}
                                        </div>
                                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                        <div class="text-sm text-gray-500 line-through">
                                            {{ \App\Helpers\PriceHelper::format($product->compare_price_cents) }}
                                        </div>
                                        <div class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full inline-block">
                                            Hemat {{ number_format((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100, 0) }}%
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Stock Status -->
                            <tr class="border-t border-gray-200 bg-gray-50">
                                <td class="p-4 font-semibold text-gray-900 sticky left-0 bg-gray-50 z-10">
                                    <i class="fas fa-boxes text-blue-600 mr-2"></i>Stok
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center">
                                    @if($product->stock_quantity > 0)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                        <i class="fas fa-check-circle"></i>
                                        Tersedia ({{ $product->stock_quantity }})
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                                        <i class="fas fa-times-circle"></i>
                                        Habis
                                    </span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>

                            <!-- Product Attributes -->
                            @if($attributeComparison->count() > 0)
                            @foreach($attributeComparison as $attribute)
                            <tr class="border-t border-gray-200 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="p-4 font-semibold text-gray-900 {{ $loop->even ? 'bg-gray-50' : '' }} sticky left-0 z-10">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ $attribute->name }}
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center">
                                    @php
                                    $value = $product->attributeValues->where('attribute_id', $attribute->id)->first();
                                    @endphp
                                    @if($value)
                                        @if($attribute->type === 'boolean')
                                            @if($value->value_boolean)
                                            <i class="fas fa-check text-green-600 text-lg"></i>
                                            @else
                                            <i class="fas fa-times text-red-600 text-lg"></i>
                                            @endif
                                        @elseif($attribute->type === 'number')
                                            <span class="font-medium">{{ number_format($value->value_number, 2) }}</span>
                                        @else
                                            <span class="text-gray-900">{{ $value->value_text }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                            @endif

                            <!-- Shipping -->
                            <tr class="border-t border-gray-200">
                                <td class="p-4 font-semibold text-gray-900 bg-gray-50 sticky left-0 z-10">
                                    <i class="fas fa-shipping-fast text-purple-600 mr-2"></i>Pengiriman
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center">
                                    <div class="space-y-1">
                                        @if($product->weight_grams)
                                        <div class="text-sm text-gray-600">
                                            Berat: {{ number_format($product->weight_grams / 1000, 2) }} kg
                                        </div>
                                        @endif
                                        @if($product->free_shipping_eligible)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                            Gratis Ongkir
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                            </tr>

                            <!-- Action Buttons -->
                            <tr class="border-t border-gray-200 bg-blue-50">
                                <td class="p-4 font-semibold text-gray-900 sticky left-0 bg-blue-50 z-10">
                                    <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>Aksi
                                </td>
                                @foreach($compareProducts as $product)
                                <td class="p-4 text-center">
                                    <div class="space-y-2">
                                        @if($product->stock_quantity > 0)
                                        <button onclick="addToCart({{ $product->id }})"
                                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            <i class="fas fa-cart-plus mr-1"></i>
                                            Tambah ke Keranjang
                                        </button>
                                        @else
                                        <button class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed text-sm font-medium">
                                            Stok Habis
                                        </button>
                                        @endif

                                        <div class="flex gap-1">
                                            <button onclick="addToWishlist({{ $product->id }})"
                                                    class="flex-1 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="flex-1 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs hover:bg-gray-50 transition-colors text-center">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Comparison Summary -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-up" data-aos-delay="200">
                <!-- Best Price -->
                @if($bestPrice)
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="font-semibold text-green-900 mb-2">Harga Terbaik</h3>
                    <p class="text-green-800 text-sm mb-3">{{ $bestPrice->name }}</p>
                    <p class="text-2xl font-bold text-green-600">{{ \App\Helpers\PriceHelper::format($bestPrice->price_cents) }}</p>
                </div>
                @endif

                <!-- Best Rating -->
                @if($bestRating)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="font-semibold text-yellow-900 mb-2">Rating Terbaik</h3>
                    <p class="text-yellow-800 text-sm mb-3">{{ $bestRating->name }}</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($bestRating->rating_average, 1) }}/5</p>
                </div>
                @endif

                <!-- Most Popular -->
                @if($mostPopular)
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-purple-500 text-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3 class="font-semibold text-purple-900 mb-2">Paling Populer</h3>
                    <p class="text-purple-800 text-sm mb-3">{{ $mostPopular->name }}</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($mostPopular->sale_count) }} terjual</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@else
<!-- Empty State -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center" data-aos="fade-up">
            <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-balance-scale text-4xl text-gray-400"></i>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-4">Belum Ada Produk untuk Dibandingkan</h2>
            <p class="text-xl text-gray-600 mb-8">
                Mulai bandingkan produk dengan menambahkan produk ke daftar perbandingan Anda
            </p>

            <!-- How to Use -->
            <div class="bg-blue-50 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-blue-900 mb-4">Cara Menggunakan Fitur Perbandingan:</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-800">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                        <div>Cari dan pilih produk yang ingin dibandingkan</div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
                        <div>Lihat perbandingan harga, spesifikasi, dan fitur</div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</div>
                        <div>Pilih produk terbaik sesuai kebutuhan Anda</div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    <i class="fas fa-shopping-bag"></i>
                    Jelajahi Produk
                </a>

                <div class="text-sm text-gray-500">
                    atau gunakan fitur pencarian di atas untuk menemukan produk
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Similar Products Suggestions -->
@if(count($compareProducts) > 0 && $similarProducts->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center" data-aos="fade-up">
                Produk Serupa Lainnya
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($similarProducts as $product)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="relative">
                        <img src="{{ $product->images->first()->image_url ?? asset('images/product-placeholder.jpg') }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform">

                        <!-- Quick Compare Button -->
                        <button onclick="addToCompare({{ $product }})"
                                class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 text-sm leading-tight">
                            {{ Str::limit($product->name, 60) }}
                        </h3>

                        <div class="flex items-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= round($product->rating_average) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                            <span class="text-xs text-gray-500 ml-1">({{ $product->rating_count }})</span>
                        </div>

                        <div class="text-lg font-bold text-blue-600 mb-3">
                            {{ \App\Helpers\PriceHelper::format($product->price_cents) }}
                        </div>

                        <button onclick="addToCompare({{ $product }})"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fas fa-balance-scale mr-1"></i>
                            Tambah ke Perbandingan
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
/* Sticky header for comparison table */
.comparison-table th {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
}

/* Responsive table styling */
.comparison-table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.comparison-table-container::-webkit-scrollbar {
    height: 8px;
}

.comparison-table-container::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.comparison-table-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.comparison-table-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .comparison-table {
        font-size: 12px;
    }

    .comparison-table td,
    .comparison-table th {
        padding: 8px 4px;
    }
}

/* Animation for adding/removing products */
.product-fade-in {
    animation: fadeInUp 0.3s ease-out;
}

.product-fade-out {
    animation: fadeOutDown 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOutDown {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

/* Highlight differences */
.comparison-highlight-best {
    background-color: #dcfce7 !important;
    border: 2px solid #16a34a;
}

.comparison-highlight-worst {
    background-color: #fef2f2 !important;
    border: 2px solid #dc2626;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .comparison-table td,
    .comparison-table th {
        min-width: 150px;
        padding: 8px;
    }

    .comparison-table img {
        width: 60px;
        height: 60px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Compare functionality
let compareProducts = @json($compareProducts->pluck('id')->toArray());
const maxCompare = {{ $maxCompare ?? 4 }};

// Initialize Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('productSearch', () => ({
        searching: false,
        results: [],

        async searchProducts(query) {
            if (query.length < 2) {
                this.results = [];
                return;
            }

            try {
                const response = await fetch(`{{ route('api.products.search') }}?q=${encodeURIComponent(query)}&limit=5`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                this.results = data.products || [];
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            }
        },

        addToCompare(product) {
            this.searching = false;
            this.results = [];
            addToCompare(product);
        }
    }));
});

// Add product to comparison
function addToCompare(product) {
    // Check if already in comparison
    if (compareProducts.includes(product.id)) {
        showNotification('Produk sudah ada dalam perbandingan', 'warning');
        return;
    }

    // Check maximum limit
    if (compareProducts.length >= maxCompare) {
        showNotification(`Maksimal ${maxCompare} produk dapat dibandingkan`, 'warning');
        return;
    }

    // Add to localStorage
    let savedCompare = JSON.parse(localStorage.getItem('compareProducts') || '[]');
    if (!savedCompare.includes(product.id)) {
        savedCompare.push(product.id);
        localStorage.setItem('compareProducts', JSON.stringify(savedCompare));
    }

    // Update page
    compareProducts.push(product.id);
    updateCompareCount();

    // Redirect to compare page if not already there
    if (!window.location.pathname.includes('/compare')) {
        window.location.href = '{{ route("compare.index") }}';
    } else {
        // Reload page to show updated comparison
        window.location.reload();
    }

    showNotification('Produk berhasil ditambahkan ke perbandingan', 'success');
}

// Remove product from comparison
function removeFromCompare(productId) {
    if (confirm('Hapus produk ini dari perbandingan?')) {
        // Remove from localStorage
        let savedCompare = JSON.parse(localStorage.getItem('compareProducts') || '[]');
        savedCompare = savedCompare.filter(id => id !== productId);
        localStorage.setItem('compareProducts', JSON.stringify(savedCompare));

        // Update local array
        compareProducts = compareProducts.filter(id => id !== productId);
        updateCompareCount();

        // Reload page
        window.location.reload();

        showNotification('Produk berhasil dihapus dari perbandingan', 'success');
    }
}

// Clear all products from comparison
function clearAllCompare() {
    if (confirm('Hapus semua produk dari perbandingan?')) {
        localStorage.removeItem('compareProducts');
        compareProducts = [];
        updateCompareCount();
        window.location.reload();
        showNotification('Semua produk berhasil dihapus dari perbandingan', 'success');
    }
}

// Update compare count display
function updateCompareCount() {
    const countElement = document.getElementById('compare-count');
    if (countElement) {
        countElement.textContent = compareProducts.length;
    }

    // Update compare button in header if exists
    const headerCompareBtn = document.querySelector('.header-compare-count');
    if (headerCompareBtn) {
        headerCompareBtn.textContent = compareProducts.length;
        headerCompareBtn.style.display = compareProducts.length > 0 ? 'inline-block' : 'none';
    }
}

// Add to cart functionality
async function addToCart(productId) {
    try {
        const response = await fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification('Produk berhasil ditambahkan ke keranjang', 'success');
            updateCartCount(data.cart_count);
        } else {
            showNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
        }
    } catch (error) {
        console.error('Cart error:', error);
        showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
    }
}

// Add to wishlist functionality
async function addToWishlist(productId) {
    try {
        const response = await fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId
            })
        });

        const data = await response.json();

        if (data.success) {
            const message = data.added ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist';
            showNotification(message, 'success');
        } else {
            showNotification(data.message || 'Gagal mengubah wishlist', 'error');
        }
    } catch (error) {
        console.error('Wishlist error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}

// Share comparison functionality
function shareComparison() {
    const url = window.location.href;
    const title = 'Perbandingan Produk - TokoSaya';
    const text = 'Lihat perbandingan produk ini di TokoSaya';

    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link perbandingan berhasil disalin!', 'success');
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('Link perbandingan berhasil disalin!', 'success');
        });
    }
}

// Print comparison functionality
function printComparison() {
    // Hide non-essential elements for printing
    const elementsToHide = document.querySelectorAll('.no-print, .sticky, .shadow-lg, .shadow-md');
    elementsToHide.forEach(el => {
        el.classList.add('hidden-for-print');
        el.style.display = 'none';
    });

    // Add print-specific styles
    const printStyle = document.createElement('style');
    printStyle.textContent = `
        @media print {
            body { font-size: 12px; }
            .comparison-table { width: 100%; }
            .comparison-table td, .comparison-table th {
                padding: 4px;
                border: 1px solid #000;
            }
            .comparison-table img {
                max-width: 40px;
                max-height: 40px;
            }
        }
    `;
    document.head.appendChild(printStyle);

    // Print
    window.print();

    // Restore hidden elements after printing
    setTimeout(() => {
        elementsToHide.forEach(el => {
            el.classList.remove('hidden-for-print');
            el.style.display = '';
        });
        document.head.removeChild(printStyle);
    }, 1000);
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' :
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
}

// Update cart count in header
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'inline-block' : 'none';
    });
}

// Highlight best/worst values in comparison
function highlightComparisons() {
    // Highlight best prices (lowest)
    const priceRows = document.querySelectorAll('tr:has(.text-green-600)');
    priceRows.forEach(row => {
        const prices = Array.from(row.querySelectorAll('td:not(:first-child)'))
            .map(td => {
                const priceText = td.querySelector('.text-green-600')?.textContent;
                if (priceText) {
                    return {
                        element: td,
                        value: parseInt(priceText.replace(/[^\d]/g, ''))
                    };
                }
                return null;
            })
            .filter(Boolean);

        if (prices.length > 1) {
            const minPrice = Math.min(...prices.map(p => p.value));
            const maxPrice = Math.max(...prices.map(p => p.value));

            prices.forEach(price => {
                if (price.value === minPrice) {
                    price.element.classList.add('comparison-highlight-best');
                } else if (price.value === maxPrice) {
                    price.element.classList.add('comparison-highlight-worst');
                }
            });
        }
    });

    // Highlight best ratings (highest)
    const ratingCells = document.querySelectorAll('.fas.fa-star').map(star => {
        return star.closest('td');
    }).filter(Boolean);

    // Add more highlighting logic as needed
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + P for print
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printComparison();
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('input[placeholder*="Cari produk"]');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.blur();
        }
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load compare products from localStorage
    const savedCompare = JSON.parse(localStorage.getItem('compareProducts') || '[]');
    compareProducts = savedCompare;
    updateCompareCount();

    // Highlight comparisons
    setTimeout(highlightComparisons, 500);

    // Auto-focus search input
    const searchInput = document.querySelector('input[placeholder*="Cari produk"]');
    if (searchInput && compareProducts.length < maxCompare) {
        setTimeout(() => searchInput.focus(), 1000);
    }

    // Add scroll behavior for mobile
    if (window.innerWidth < 768) {
        const table = document.querySelector('.overflow-x-auto');
        if (table) {
            let isScrolling = false;
            table.addEventListener('scroll', function() {
                if (!isScrolling) {
                    window.requestAnimationFrame(function() {
                        // Add scroll position indicator if needed
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            });
        }
    }
});

// Analytics tracking
function trackComparison(action, productId = null) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'compare_products', {
            action: action,
            product_id: productId,
            products_count: compareProducts.length
        });
    }
}

// Track page view
document.addEventListener('DOMContentLoaded', function() {
    trackComparison('view_comparison');
});

// Lazy loading for similar products images
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

// Auto-save compare state
window.addEventListener('beforeunload', function() {
    localStorage.setItem('compareProducts', JSON.stringify(compareProducts));
});

// Enhanced search with history
let searchHistory = JSON.parse(localStorage.getItem('compareSearchHistory') || '[]');

function addToSearchHistory(query) {
    if (query.length < 2) return;

    // Remove duplicates and add to front
    searchHistory = searchHistory.filter(item => item !== query);
    searchHistory.unshift(query);

    // Keep only last 5 searches
    searchHistory = searchHistory.slice(0, 5);
    localStorage.setItem('compareSearchHistory', JSON.stringify(searchHistory));
}

// Show search history when input is focused
document.addEventListener('focus', function(e) {
    if (e.target.matches('input[placeholder*="Cari produk"]') && searchHistory.length > 0) {
        // Implementation for showing search history dropdown
    }
}, true);
</script>
@endpush
