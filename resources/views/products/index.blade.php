@extends('layouts.app')

@section('title', 'Katalog Produk - TokoSaya')

@section('description', 'Jelajahi katalog produk lengkap TokoSaya dengan berbagai kategori dan merek terpercaya. Temukan produk favorit Anda dengan harga terbaik.')

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .product-image {
        height: 160px;
        object-fit: cover;
        background: #f8f9fa;
    }
    .price-original {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 0.9em;
    }
    .price-sale {
        color: #dc3545;
        font-weight: bold;
    }
    .badge-discount {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
    }
    .rating-stars {
        color: #ffc107;
    }
    .filter-sidebar {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    .filter-tag {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        color: #1976d2;
        border-radius: 15px;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        margin: 0.25rem;
        display: inline-flex;
        align-items: center;
    }
    .filter-tag .btn-close {
        font-size: 0.7rem;
        margin-left: 0.5rem;
    }
    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">Katalog Produk</h1>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <label class="form-label">Cari Produk</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nama produk..."
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Brand Filter -->
                        <div class="col-md-3">
                            <label class="form-label">Merek</label>
                            <select name="brand_id" class="form-select">
                                <option value="">Semua Merek</option>
                                @if(isset($brands))
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                                {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="col-md-2">
                            <label class="form-label">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                                    Terbaru
                                </option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                                    Terpopuler
                                </option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                    Harga Terendah
                                </option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                    Harga Tertinggi
                                </option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>
                                    Nama A-Z
                                </option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'category_id', 'brand_id', 'sort']))
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'category_id', 'brand_id']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center">
                    <small class="text-muted me-2">Filter aktif:</small>

                    @if(request('search'))
                        <span class="filter-tag">
                            <i class="fas fa-search me-1"></i>
                            "{{ request('search') }}"
                            <button type="button" class="btn-close" onclick="removeFilter('search')"></button>
                        </span>
                    @endif

                    @if(request('category_id') && isset($categories))
                        @php
                            $selectedCategory = $categories->firstWhere('id', request('category_id'));
                        @endphp
                        @if($selectedCategory)
                            <span class="filter-tag">
                                <i class="fas fa-tag me-1"></i>
                                {{ $selectedCategory->name }}
                                <button type="button" class="btn-close" onclick="removeFilter('category_id')"></button>
                            </span>
                        @endif
                    @endif

                    @if(request('brand_id') && isset($brands))
                        @php
                            $selectedBrand = $brands->firstWhere('id', request('brand_id'));
                        @endphp
                        @if($selectedBrand)
                            <span class="filter-tag">
                                <i class="fas fa-copyright me-1"></i>
                                {{ $selectedBrand->name }}
                                <button type="button" class="btn-close" onclick="removeFilter('brand_id')"></button>
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Results Info -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if(isset($products) && $products->total() > 0)
                        <p class="text-muted mb-0">
                            Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }}
                            dari {{ number_format($products->total()) }} produk
                        </p>
                    @else
                        <p class="text-muted mb-0">Tidak ada produk ditemukan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    @if(isset($products) && $products->count() > 0)
        <div class="product-grid mb-4">
            @foreach($products as $product)
                <div class="card product-card">
                    <div class="position-relative">
                        <img src="{{ asset('images/placeholder-product.jpg') }}"
                             class="card-img-top product-image"
                             alt="{{ $product->name }}"
                             loading="lazy">

                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                            @php
                                $discount = round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100);
                            @endphp
                            <span class="badge badge-discount">-{{ $discount }}%</span>
                        @endif

                        @if($product->featured)
                            <span class="badge bg-warning text-dark position-absolute" style="top: 10px; left: 10px;">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Category & Brand -->
                        <div class="mb-2">
                            @if($product->category)
                                <small class="text-muted">
                                    <a href="{{ route('categories.show', $product->category->slug) }}"
                                       class="text-decoration-none">
                                        {{ $product->category->name }}
                                    </a>
                                </small>
                            @endif
                            @if($product->brand)
                                <small class="text-muted"> • {{ $product->brand->name }}</small>
                            @endif
                        </div>

                        <!-- Product Name -->
                        <h6 class="card-title">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="text-decoration-none text-dark">
                                {{ Str::limit($product->name, 50) }}
                            </a>
                        </h6>

                        <!-- Rating -->
                        @if($product->rating_average > 0)
                            <div class="mb-2">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->rating_average))
                                            <i class="fas fa-star"></i>
                                        @elseif($i == ceil($product->rating_average) && $product->rating_average - floor($product->rating_average) >= 0.5)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    ({{ number_format($product->rating_count) }})
                                </small>
                            </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-2">
                            <div class="price">
                                Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                            </div>
                            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                <small class="price-original">
                                    Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                                </small>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-3">
                            @if($product->stock_quantity > 0)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Stok: {{ $product->stock_quantity }}
                                </small>
                            @else
                                <small class="text-danger">
                                    <i class="fas fa-times-circle"></i> Stok Habis
                                </small>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-auto">
                            <div class="d-grid gap-1">
                                @if($product->stock_quantity > 0)
                                    <button class="btn btn-primary btn-sm"
                                            onclick="addToCart({{ $product->id }})">
                                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fas fa-times"></i> Stok Habis
                                    </button>
                                @endif

                                <div class="d-flex gap-1">
                                    <button class="btn btn-outline-primary btn-sm flex-fill"
                                            onclick="addToWishlist({{ $product->id }})">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="btn btn-outline-info btn-sm flex-fill">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted">Tidak Ada Produk Ditemukan</h3>
            <p class="text-muted mb-4">
                Maaf, tidak ada produk yang sesuai dengan kriteria pencarian Anda.
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Lihat Semua Produk
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Remove Filter Function
    function removeFilter(filterName) {
        const url = new URL(window.location);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }

    // Add to Cart Function
    function addToCart(productId) {
        if (!productId) return;

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Produk berhasil ditambahkan ke keranjang!', 'success');
                updateCartCount();
            } else {
                showToast(data.message || 'Gagal menambahkan produk ke keranjang', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        });
    }

    // Add to Wishlist Function
    function addToWishlist(productId) {
        if (!productId) return;

        @auth
        fetch('/api/wishlist/toggle/' + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal menambahkan ke wishlist', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endauth
    }

    // Show Toast Notification
    function showToast(message, type = 'info') {
        const toastHtml = `
            <div class="toast align-items-center text-bg-${type} border-0 position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.body.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Remove from DOM after hiding
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Auto-submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('select[name="category_id"], select[name="brand_id"], select[name="sort"]');

        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
