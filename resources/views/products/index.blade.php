@extends('layouts.app')

@section('title', 'Katalog Produk - TokoSaya')

@section('description', 'Jelajahi katalog produk lengkap TokoSaya dengan berbagai kategori dan merek terpercaya. Temukan produk favorit Anda dengan harga terbaik.')

@push('styles')
<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
        will-change: transform;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
    .product-image {
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
        transition: opacity 0.3s ease;
    }
    .product-image.loading {
        opacity: 0.7;
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
        z-index: 2;
    }
    .rating-stars {
        color: #ffc107;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #6c757d;
    }
    .filter-tag {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        color: #1976d2;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        margin: 0.25rem;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .filter-tag:hover {
        background: #bbdefb;
    }
    .filter-tag .btn-close {
        font-size: 0.7rem;
        margin-left: 0.5rem;
        opacity: 0.6;
    }
    .filter-tag .btn-close:hover {
        opacity: 1;
    }

    /* Prevent layout shifts */
    .card-img-top {
        aspect-ratio: 1;
        object-fit: cover;
    }

    /* Smooth loading states */
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }
        .product-image {
            height: 150px;
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
                    <form method="GET" class="row g-3" id="filterForm">
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
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name ?? 'Kategori Tanpa Nama' }}
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
                                @if(isset($brands) && $brands->count() > 0)
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                                {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name ?? 'Merek Tanpa Nama' }}
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

                    @if(request('category_id') && isset($categories) && $categories->count() > 0)
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

                    @if(request('brand_id') && isset($brands) && $brands->count() > 0)
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
                    @if(isset($products) && method_exists($products, 'total') && $products->total() > 0)
                        <p class="text-muted mb-0">
                            Menampilkan {{ $products->firstItem() ?? 1 }}-{{ $products->lastItem() ?? $products->count() }}
                            dari {{ number_format($products->total()) }} produk
                        </p>
                    @elseif(isset($products) && $products->count() > 0)
                        <p class="text-muted mb-0">
                            Menampilkan {{ $products->count() }} produk
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
                        <img src="{{ $product->primary_image ?? asset('images/placeholder-product.jpg') }}"
                             class="card-img-top product-image"
                             alt="{{ $product->name ?? 'Produk' }}"
                             loading="lazy"
                             onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">

                        @if(isset($product->compare_price_cents) && isset($product->price_cents) && $product->compare_price_cents > $product->price_cents)
                            @php
                                $discount = round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100);
                            @endphp
                            <span class="badge badge-discount">-{{ $discount }}%</span>
                        @endif

                        @if($product->featured ?? false)
                            <span class="badge bg-warning text-dark position-absolute" style="top: 10px; left: 10px;">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Category & Brand -->
                        <div class="mb-2">
                            @if(isset($product->category) && $product->category)
                                <small class="text-muted">
                                    {{ $product->category->name ?? 'Kategori' }}
                                </small>
                            @endif
                            @if(isset($product->brand) && $product->brand)
                                <small class="text-muted"> • {{ $product->brand->name ?? 'Merek' }}</small>
                            @endif
                        </div>

                        <!-- Product Name -->
                        <h6 class="card-title">
                            {{-- PERBAIKAN: Fix routing untuk detail product --}}
                            <a href="{{ route('products.show', ['product' => $product->id]) }}"
                               class="text-decoration-none text-dark">
                                {{ Str::limit($product->name ?? 'Produk Tanpa Nama', 50) }}
                            </a>
                        </h6>

                        <!-- Rating -->
                        @if(isset($product->rating_average) && $product->rating_average > 0)
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
                                    ({{ number_format($product->rating_count ?? 0) }})
                                </small>
                            </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-2">
                            <div class="price fw-bold text-primary">
                                Rp {{ number_format(($product->price_cents ?? 0) / 100, 0, ',', '.') }}
                            </div>
                            @if(isset($product->compare_price_cents) && $product->compare_price_cents > ($product->price_cents ?? 0))
                                <small class="price-original">
                                    Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                                </small>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-3">
                            @if(($product->stock_quantity ?? 0) > 0)
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
                            <div class="d-grid gap-2">
                                @if(($product->stock_quantity ?? 0) > 0)
                                    <button class="btn btn-primary btn-sm"
                                            onclick="addToCart({{ $product->id }})"
                                            data-product-id="{{ $product->id }}">
                                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fas fa-times"></i> Stok Habis
                                    </button>
                                @endif

                                <div class="row g-1">
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary btn-sm w-100"
                                                onclick="addToWishlist({{ $product->id }})"
                                                data-product-id="{{ $product->id }}">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        {{-- PERBAIKAN: Fix routing untuk detail product --}}
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}"
                                           class="btn btn-outline-info btn-sm w-100">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(method_exists($products, 'hasPages') && $products->hasPages())
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
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    // Remove Filter Function
    function removeFilter(filterName) {
        const url = new URL(window.location);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    }

    // UPDATED: Add to Cart Function - No more flickering
    function addToCart(productId) {
        if (!productId) return;

        const button = document.querySelector(`button[data-product-id="${productId}"]`);
        if (button.classList.contains('loading')) return;

        // Add loading state
        button.classList.add('loading');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambah...';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('Produk berhasil ditambahkan ke keranjang!', 'success');
                updateCartCount();

                // Show success state briefly
                button.innerHTML = '<i class="fas fa-check"></i> Berhasil!';
                button.classList.add('btn-success');
                button.classList.remove('btn-primary');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                }, 2000);
            } else {
                showToast(data.message || 'Gagal menambahkan produk ke keranjang', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        })
        .finally(() => {
            button.classList.remove('loading');
            if (button.innerHTML.includes('Menambah...')) {
                button.innerHTML = originalText;
            }
        });
    }

    // UPDATED: Add to Wishlist Function with loading state
    function addToWishlist(productId) {
        if (!productId) return;

        @guest
        // Show login prompt for guests
        showLoginPrompt('Silakan login terlebih dahulu untuk menambahkan produk ke wishlist.');
        return;
        @endguest

        @auth
        const button = document.querySelector(`button[data-product-id="${productId}"][onclick*="addToWishlist"]`);
        if (button && button.classList.contains('loading')) return;

        if (button) {
            button.classList.add('loading');
            const originalIcon = button.querySelector('i').className;
            button.querySelector('i').className = 'fas fa-spinner fa-spin';
        }

        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');

                if (button) {
                    const icon = button.querySelector('i');
                    if (data.added) {
                        icon.className = 'fas fa-heart text-danger';
                        button.classList.add('btn-outline-danger');
                        button.classList.remove('btn-outline-primary');
                    } else {
                        icon.className = 'far fa-heart';
                        button.classList.add('btn-outline-primary');
                        button.classList.remove('btn-outline-danger');
                    }
                }
            } else {
                showToast(data.message || 'Gagal menambahkan ke wishlist', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        })
        .finally(() => {
            if (button) {
                button.classList.remove('loading');
            }
        });
        @endauth
    }

    // Show login prompt for guests
    function showLoginPrompt(message) {
        const loginModal =
            <div class="modal fade" id="loginPromptModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Login Diperlukan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        ;

        // Remove existing modal if any
        const existingModal = document.getElementById('loginPromptModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to DOM and show
        document.body.insertAdjacentHTML('beforeend', loginModal);
        const modal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
        modal.show();

        // Remove modal from DOM after hiding
        document.getElementById('loginPromptModal').addEventListener('hidden.bs.modal', () => {
            document.getElementById('loginPromptModal').remove();
        });
    }

    // Show Toast Notification
    function showToast(message, type = 'info') {
        const toastHtml =
            <div class="toast align-items-center text-bg-${type} border-0 position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        ;

        document.body.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.body.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Remove from DOM after hiding
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Update cart count smoothly
    function updateCartCount() {
        fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                // Animate count change
                const currentCount = parseInt(element.textContent) || 0;
                const newCount = data.count || 0;

                if (newCount !== currentCount) {
                    element.style.transform = 'scale(1.2)';
                    element.style.transition = 'transform 0.2s ease';

                    setTimeout(() => {
                        element.textContent = newCount;
                        element.style.transform = 'scale(1)';
                    }, 100);
                }
            });
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
        });
    }

    // UPDATED: Auto-submit form when filters change (debounced)
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('select[name="category_id"], select[name="brand_id"], select[name="sort"]');
        let submitTimeout;

        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                // Clear previous timeout
                if (submitTimeout) {
                    clearTimeout(submitTimeout);
                }

                // Add loading state to form
                const form = this.closest('form');
                form.style.opacity = '0.7';
                form.style.pointerEvents = 'none';

                // Submit after short delay to prevent multiple rapid submissions
                submitTimeout = setTimeout(() => {
                    this.form.submit();
                }, 300);
            });
        });

        // Initialize cart count on page load
        updateCartCount();

        // Prevent layout shift by setting image aspect ratio
        const images = document.querySelectorAll('.product-image');
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.classList.remove('loading');
            });

            img.addEventListener('error', function() {
                this.classList.remove('loading');
                this.src = '{{ asset('images/placeholder-product.jpg') }}';
            });
        });
    });
</script>
@endpush
