@extends('layouts.app')

@section('title', 'TokoSaya - Belanja Online Mudah dan Terpercaya')
@section('description', 'TokoSaya adalah platform e-commerce terpercaya dengan berbagai produk berkualitas, harga terbaik, dan pengiriman cepat ke seluruh Indonesia.')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">Belanja Online Mudah dan Terpercaya</h1>
                <p class="lead mb-4">Temukan berbagai produk berkualitas dengan harga terbaik. Pengiriman cepat ke seluruh Indonesia!</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>Lihat Kategori
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-stats mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold">{{ number_format($stats['total_products'] ?? 0) }}+</h3>
                                <p class="mb-0">Produk</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold">{{ number_format($stats['total_categories'] ?? 0) }}+</h3>
                                <p class="mb-0">Kategori</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <h3 class="fw-bold">{{ number_format($stats['in_stock'] ?? 0) }}+</h3>
                                <p class="mb-0">Stok Tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if(isset($categories) && $categories->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Kategori Populer</h2>
                <p class="text-muted">Jelajahi berbagai kategori produk pilihan</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($categories->take(6) as $category)
            <div class="col-lg-2 col-md-4 col-6">
                <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                    <div class="category-card h-100">
                        <div class="category-icon">
                            @switch($category->name)
                                @case('Elektronik')
                                    <i class="fas fa-laptop"></i>
                                    @break
                                @case('Fashion')
                                    <i class="fas fa-tshirt"></i>
                                    @break
                                @case('Kesehatan')
                                    <i class="fas fa-heartbeat"></i>
                                    @break
                                @case('Olahraga')
                                    <i class="fas fa-dumbbell"></i>
                                    @break
                                @case('Makanan')
                                    <i class="fas fa-utensils"></i>
                                    @break
                                @default
                                    <i class="fas fa-tags"></i>
                            @endswitch
                        </div>
                        <h6 class="fw-semibold mb-2">{{ $category->name }}</h6>
                        <small class="text-muted">{{ $category->products_count ?? 0 }} Produk</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                Lihat Semua Kategori <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">Produk Unggulan</h2>
                <p class="text-muted">Koleksi produk terbaik pilihan kami</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($featuredProducts->take(8) as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card product-card h-100 shadow-sm">
                    <div class="position-relative">
                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ $product->images->first()->image_url }}" class="product-image" alt="{{ $product->name }}">
                        @else
                            <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif

                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                            @php
                                $discount = round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100);
                            @endphp
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">-{{ $discount }}%</span>
                        @endif

                        @auth
                        <button class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 wishlist-btn"
                                data-product-id="{{ $product->id }}" title="Tambah ke Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        @endauth
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-semibold mb-2">
                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($product->name, 50) }}
                            </a>
                        </h6>

                        @if($product->category)
                        <small class="text-muted mb-2">{{ $product->category->name }}</small>
                        @endif

                        <div class="price-section mb-3">
                            <div class="price">Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}</div>
                            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                <div class="compare-price">Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}</div>
                            @endif
                        </div>

                        @if($product->rating_average > 0)
                        <div class="rating mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $product->rating_average)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <small class="text-muted ms-1">({{ $product->rating_count ?? 0 }})</small>
                        </div>
                        @endif

                        <div class="mt-auto">
                            @if($product->stock_quantity > 0)
                                <button class="btn btn-primary w-100 add-to-cart-btn"
                                        data-product-id="{{ $product->id }}">
                                    <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                </button>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times me-2"></i>Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shipping-fast fa-2x"></i>
                    </div>
                    <h5 class="fw-semibold">Pengiriman Cepat</h5>
                    <p class="text-muted mb-0">Pengiriman ke seluruh Indonesia dengan jaminan aman dan cepat</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-semibold">Transaksi Aman</h5>
                    <p class="text-muted mb-0">Sistem pembayaran yang aman dan terpercaya dengan berbagai metode</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-medal fa-2x"></i>
                    </div>
                    <h5 class="fw-semibold">Kualitas Terjamin</h5>
                    <p class="text-muted mb-0">Produk berkualitas tinggi dengan garansi resmi dari supplier terpercaya</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x"></i>
                    </div>
                    <h5 class="fw-semibold">Customer Support 24/7</h5>
                    <p class="text-muted mb-0">Tim customer service siap membantu Anda kapan saja</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-3">Dapatkan Info Terbaru!</h3>
                <p class="mb-4">Berlangganan newsletter kami untuk mendapatkan info produk terbaru, promo menarik, dan penawaran eksklusif.</p>
            </div>
            <div class="col-lg-6">
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Masukkan email Anda" required>
                    <button type="submit" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-paper-plane me-2"></i>Daftar
                    </button>
                </form>
                <small class="text-light opacity-75">Kami menghargai privasi Anda. Tidak ada spam!</small>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to Cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menambahkan...';

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Berhasil Ditambahkan';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');

                    // Update cart count
                    updateCartCount();

                    // Show success notification
                    showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');

                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                        this.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Gagal menambahkan produk');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                showNotification('Gagal menambahkan produk ke keranjang', 'danger');
            });
        });
    });

    // Wishlist functionality (for authenticated users)
    @auth
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');

            fetch('{{ route("wishlist.toggle", ":id") }}'.replace(':id', productId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                        showNotification('Produk ditambahkan ke wishlist', 'success');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                        showNotification('Produk dihapus dari wishlist', 'info');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Gagal memperbarui wishlist', 'danger');
            });
        });
    });
    @endauth
});

// Show notification function
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '100px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';

    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = new bootstrap.Alert(alertDiv);
        alert.close();
    }, 5000);
}
</script>
@endpush
