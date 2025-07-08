@extends('layouts.app')

@section('title', 'TokoSaya - Belanja Mudah, Terpercaya')
@section('description', 'Platform e-commerce modern dengan ribuan produk berkualitas, pengiriman cepat, dan pengalaman berbelanja terbaik.')

@section('content')
<!-- Hero Section - Minimalist -->
<section class="hero-modern">
    <div class="container mx-auto px-4 py-16 lg:py-24">
        <div class="max-w-6xl mx-auto">
            @forelse($banners->where('position', 'hero') as $banner)
            <div class="text-center space-y-6" data-aos="fade-up">
                @if($banner->subtitle)
                <p class="text-sm uppercase tracking-wider text-blue-600 font-medium">{{ $banner->subtitle }}</p>
                @endif
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                    {{ $banner->title }}
                </h1>
                @if($banner->description)
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ $banner->description }}
                </p>
                @endif
                @if($banner->link_url)
                <div class="pt-4">
                    <a href="{{ $banner->link_url }}" class="btn-primary-modern">
                        {{ $banner->link_text ?: 'Mulai Belanja' }}
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                @endif
            </div>
            @empty
            <!-- Default Hero -->
            <div class="text-center space-y-6" data-aos="fade-up">
                <p class="text-sm uppercase tracking-wider text-blue-600 font-medium">Selamat Datang di</p>
                <h1 class="text-5xl lg:text-7xl font-bold text-gray-900">
                    Toko<span class="text-blue-600">Saya</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Temukan produk berkualitas dengan pengalaman berbelanja yang tak terlupakan
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6">
                    <a href="{{ route('products.index') }}" class="btn-primary-modern">
                        Jelajahi Produk
                        <i class="fas fa-search ml-2"></i>
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn-secondary-modern">
                        Lihat Kategori
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Stats Section - Compact -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 max-w-4xl mx-auto">
            <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shipping-fast text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Pengiriman Cepat</h3>
                <p class="text-xs text-gray-600">Se-Indonesia</p>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">100% Aman</h3>
                <p class="text-xs text-gray-600">Terenkripsi</p>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-medal text-purple-600 text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Kualitas Terjamin</h3>
                <p class="text-xs text-gray-600">Original</p>
            </div>
            <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Support 24/7</h3>
                <p class="text-xs text-gray-600">Siap Membantu</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section - Modern Grid -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Kategori Populer</h2>
            <p class="text-gray-600">Temukan produk dari berbagai kategori terbaik</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6 max-w-6xl mx-auto">
            @foreach($featured_categories->take(8) as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="category-card-modern group"
               data-aos="fade-up"
               data-aos-delay="{{ $loop->index * 50 }}">
                <div class="category-icon-modern">
                    @if($category->image)
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-8 h-8 object-cover">
                    @else
                    <i class="{{ $category->icon ?: 'fas fa-tag' }} text-gray-700 text-lg group-hover:text-blue-600 transition-colors"></i>
                    @endif
                </div>
                <h3 class="text-xs font-medium text-gray-900 mt-3 group-hover:text-blue-600 transition-colors">
                    {{ $category->name }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($category->product_count) }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products - Minimalist Grid -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-8" data-aos="fade-up">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Produk Unggulan</h2>
                <p class="text-gray-600">Pilihan terbaik untuk Anda</p>
            </div>
            <a href="{{ route('products.index', ['featured' => 1]) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featured_products->take(8) as $product)
            <div class="product-card-modern group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <!-- Product Image -->
                <div class="relative overflow-hidden rounded-lg mb-4 aspect-square bg-gray-100">
                    @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->image_url }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                    </div>
                    @endif

                    <!-- Badges -->
                    @if($product->compare_price_cents > $product->price_cents)
                    <div class="absolute top-2 left-2">
                        <span class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full">
                            -{{ round(($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents * 100) }}%
                        </span>
                    </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-red-50 transition-colors">
                            <i class="fas fa-heart text-gray-400 hover:text-red-500 text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-2">
                    <h3 class="font-medium text-gray-900 text-sm leading-tight line-clamp-2 hover:text-blue-600 transition-colors">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>

                    <!-- Rating -->
                    <div class="flex items-center gap-1">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= $product->rating_average ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500">({{ $product->rating_count }})</span>
                    </div>

                    <!-- Price -->
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900">
                            Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                        </span>
                        @if($product->compare_price_cents > $product->price_cents)
                        <span class="text-xs text-gray-500 line-through">
                            Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>

                    <!-- Add to Cart -->
                    @if($product->status == 'active' && $product->stock_quantity > 0)
                    <button class="w-full bg-gray-900 text-white text-sm font-medium py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Tambah ke Keranjang
                    </button>
                    @else
                    <button class="w-full bg-gray-200 text-gray-500 text-sm font-medium py-2 rounded-lg cursor-not-allowed">
                        Stok Habis
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Newsletter Section - Minimal -->
<section class="py-16 bg-gray-900 text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-2xl mx-auto space-y-6" data-aos="fade-up">
            <h2 class="text-3xl font-bold">Dapatkan Update Terbaru</h2>
            <p class="text-gray-300">Berlangganan newsletter untuk penawaran eksklusif dan produk terbaru</p>

            <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" id="newsletterForm">
                @csrf
                <input type="email"
                       name="email"
                       placeholder="Masukkan email Anda"
                       class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Berlangganan
                </button>
            </form>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* Modern Minimalist Styles */
.hero-modern {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    position: relative;
    overflow: hidden;
}

.hero-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(59,130,246,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.5;
}

.btn-primary-modern {
    @apply inline-flex items-center px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5;
}

.btn-secondary-modern {
    @apply inline-flex items-center px-8 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200;
}

.category-card-modern {
    @apply p-6 text-center rounded-xl border border-gray-100 hover:border-blue-200 hover:shadow-sm transition-all duration-300 hover:-translate-y-1 bg-white;
}

.category-icon-modern {
    @apply w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center mx-auto group-hover:bg-blue-50 transition-colors;
}

.product-card-modern {
    @apply bg-white rounded-lg hover:shadow-md transition-all duration-300;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-modern h1 {
        @apply text-3xl;
    }

    .category-card-modern {
        @apply p-4;
    }

    .category-icon-modern {
        @apply w-12 h-12;
    }
}

/* Loading states */
.loading {
    @apply opacity-50 pointer-events-none;
}

/* Smooth animations */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced newsletter form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const email = this.querySelector('input[type="email"]').value;

            if (email) {
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                button.disabled = true;

                setTimeout(() => {
                    alert('Terima kasih! Anda telah berlangganan newsletter kami.');
                    this.reset();
                    button.innerHTML = 'Berlangganan';
                    button.disabled = false;
                }, 1500);
            }
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add to cart functionality
    document.querySelectorAll('button:contains("Tambah ke Keranjang")').forEach(button => {
        button.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menambahkan...';
            this.disabled = true;

            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check mr-2"></i>Ditambahkan';
                setTimeout(() => {
                    this.innerHTML = 'Tambah ke Keranjang';
                    this.disabled = false;
                }, 2000);
            }, 1000);
        });
    });
});
</script>
@endpush
