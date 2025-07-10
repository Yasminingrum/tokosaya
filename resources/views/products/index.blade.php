@extends('layouts.app')

@section('title', 'Shop - All Products | TokoSaya')
@section('description', 'Browse our wide selection of quality products at affordable prices. Find what you need with our advanced filters and search.')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
        color: white;
        padding: 60px 0;
        position: relative;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="white" stop-opacity="0.1"/><stop offset="100%" stop-color="white" stop-opacity="0"/></radialGradient></defs><circle cx="10" cy="10" r="10" fill="url(%23a)"/><circle cx="90" cy="10" r="10" fill="url(%23a)"/></svg>');
        opacity: 0.1;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }

    .page-subtitle {
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 2;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(255,255,255,0.7);
    }

    .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: white;
    }

    /* Filter Sidebar */
    .filter-sidebar {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .filter-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 20px;
        font-size: 1.1rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 10px;
    }

    .filter-group {
        margin-bottom: 25px;
    }

    .filter-group h6 {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .filter-option {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 5px 0;
    }

    .filter-option input[type="checkbox"],
    .filter-option input[type="radio"] {
        margin-right: 10px;
        accent-color: var(--primary-color);
    }

    .filter-option label {
        color: var(--secondary-color);
        font-size: 0.9rem;
        cursor: pointer;
        margin: 0;
        flex-grow: 1;
    }

    .price-range-inputs {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .price-input {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
        width: 100%;
    }

    .btn-apply-filter {
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        width: 100%;
        margin-top: 15px;
    }

    .btn-clear-filter {
        background: none;
        border: 1px solid var(--border-color);
        color: var(--secondary-color);
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-top: 10px;
        width: 100%;
    }

    /* Products Section */
    .products-section {
        padding: 40px 0;
    }

    .products-header {
        background: white;
        border-radius: 15px;
        padding: 20px 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }

    .results-info {
        color: var(--secondary-color);
        font-size: 0.95rem;
    }

    .sort-dropdown {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 15px;
        font-size: 0.9rem;
        background: white;
        min-width: 180px;
    }

    .view-toggle {
        display: flex;
        background: var(--light-gray);
        border-radius: 8px;
        padding: 4px;
    }

    .view-btn {
        background: none;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        color: var(--secondary-color);
        transition: all 0.3s ease;
    }

    .view-btn.active {
        background: white;
        color: var(--primary-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Product Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .products-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .product-list-item {
        background: white;
        border-radius: 15px;
        padding: 20px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        display: flex;
        gap: 20px;
    }

    .product-list-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .product-list-image {
        width: 120px;
        height: 120px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-list-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-list-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-list-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .product-list-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: var(--secondary-color);
    }

    .product-list-description {
        color: var(--secondary-color);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 15px;
    }

    .product-list-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        background: white;
        border-radius: 15px;
        padding: 15px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }

    .page-link {
        color: var(--secondary-color);
        border: none;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: var(--light-gray);
        color: var(--primary-color);
    }

    .page-item.active .page-link {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .filter-sidebar {
            position: static;
            margin-bottom: 20px;
        }

        .product-list-item {
            flex-direction: column;
            text-align: center;
        }

        .product-list-image {
            width: 100%;
            height: 200px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 2rem;
        }
    }

    /* Loading States */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* No Products State */
    .no-products {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        border: 1px solid var(--border-color);
    }

    .no-products-icon {
        font-size: 4rem;
        color: var(--secondary-color);
        margin-bottom: 20px;
    }

    .no-products h3 {
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .no-products p {
        color: var(--secondary-color);
        margin-bottom: 25px;
    }

    /* Active Filters */
    .active-filters {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        background: var(--light-gray);
        color: var(--dark-color);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 2px;
    }

    .filter-tag button {
        background: none;
        border: none;
        color: var(--secondary-color);
        margin-left: 8px;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Shop</li>
                    </ol>
                </nav>
                <h1 class="page-title">Shop All Products</h1>
                <p class="page-subtitle">Discover our wide selection of quality products at unbeatable prices</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="products-section">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="filter-sidebar">
                    <h5 class="filter-title">
                        <i class="fas fa-filter me-2"></i>
                        Filter Products
                    </h5>

                    <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                        <!-- Search Filter -->
                        <div class="filter-group">
                            <h6>Search</h6>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search products..."
                                   value="{{ request('search') }}">
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h6>Categories</h6>
                            @forelse($categories as $category)
                            <div class="filter-option">
                                <input type="radio" name="category_id" value="{{ $category->id }}"
                                       id="cat_{{ $category->id }}"
                                       {{ request('category_id') == $category->id ? 'checked' : '' }}>
                                <label for="cat_{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                            @empty
                            <p class="text-muted small">No categories available</p>
                            @endforelse
                            @if(request('category_id'))
                            <div class="filter-option">
                                <input type="radio" name="category_id" value="" id="cat_all">
                                <label for="cat_all">All Categories</label>
                            </div>
                            @endif
                        </div>

                        <!-- Brand Filter -->
                        @if($brands->count() > 0)
                        <div class="filter-group">
                            <h6>Brands</h6>
                            @foreach($brands->take(8) as $brand)
                            <div class="filter-option">
                                <input type="radio" name="brand_id" value="{{ $brand->id }}"
                                       id="brand_{{ $brand->id }}"
                                       {{ request('brand_id') == $brand->id ? 'checked' : '' }}>
                                <label for="brand_{{ $brand->id }}">{{ $brand->name }}</label>
                            </div>
                            @endforeach
                            @if(request('brand_id'))
                            <div class="filter-option">
                                <input type="radio" name="brand_id" value="" id="brand_all">
                                <label for="brand_all">All Brands</label>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Price Range -->
                        <div class="filter-group">
                            <h6>Price Range</h6>
                            <div class="price-range-inputs">
                                <input type="number" name="min_price" class="price-input"
                                       placeholder="Min" value="{{ request('min_price') }}">
                                <span>-</span>
                                <input type="number" name="max_price" class="price-input"
                                       placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn-apply-filter">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>

                        @if(request()->hasAny(['search', 'category_id', 'brand_id', 'min_price', 'max_price']))
                        <a href="{{ route('products.index') }}" class="btn-clear-filter">
                            <i class="fas fa-times me-2"></i>Clear All
                        </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Products Content -->
            <div class="col-lg-9 col-md-8">
                <!-- Products Header -->
                <div class="products-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="results-info">
                                @if($products->total() > 0)
                                    Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                                    @if(request('search'))
                                        for "<strong>{{ request('search') }}</strong>"
                                    @endif
                                @else
                                    No products found
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <!-- Sort Dropdown -->
                                <select name="sort" class="sort-dropdown" onchange="updateSort(this.value)">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                </select>

                                <!-- View Toggle -->
                                <div class="view-toggle">
                                    <button class="view-btn active" onclick="setView('grid')" data-view="grid">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="view-btn" onclick="setView('list')" data-view="list">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                @if(request()->hasAny(['search', 'category_id', 'brand_id', 'min_price', 'max_price']))
                <div class="active-filters">
                    <strong class="me-3">Active Filters:</strong>
                    @if(request('search'))
                        <span class="filter-tag">
                            Search: {{ request('search') }}
                            <button type="button" onclick="removeFilter('search')">×</button>
                        </span>
                    @endif
                    @if(request('category_id'))
                        <span class="filter-tag">
                            Category: {{ $categories->find(request('category_id'))->name ?? 'Unknown' }}
                            <button type="button" onclick="removeFilter('category_id')">×</button>
                        </span>
                    @endif
                    @if(request('brand_id'))
                        <span class="filter-tag">
                            Brand: {{ $brands->find(request('brand_id'))->name ?? 'Unknown' }}
                            <button type="button" onclick="removeFilter('brand_id')">×</button>
                        </span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="filter-tag">
                            Price: Rp {{ number_format(request('min_price', 0)) }} - Rp {{ number_format(request('max_price', 999999999)) }}
                            <button type="button" onclick="removeFilter('price')">×</button>
                        </span>
                    @endif
                </div>
                @endif

                <!-- Products Grid/List -->
                <div id="products-container">
                    @if($products->count() > 0)
                        <div class="products-grid" id="products-grid">
                            @foreach($products as $product)
                            <div class="product-card" data-product-id="{{ $product->id }}">
                                <div class="product-image">
                                    @if($product->featured)
                                    <div class="product-badge">HOT</div>
                                    @endif

                                    <a href="{{ route('products.show', $product->id) }}">
                                        <img src="{{ $product->images->first()->image_url ?? 'https://via.placeholder.com/300x300/e2e8f0/64748b?text=No+Image' }}"
                                             alt="{{ $product->name }}" loading="lazy">
                                    </a>

                                    @auth
                                    <button class="btn-wishlist position-absolute top-0 end-0 m-2"
                                            onclick="toggleWishlist({{ $product->id }})"
                                            title="Add to Wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    @endauth
                                </div>

                                <div class="product-info">
                                    <h5 class="product-title">
                                        <a href="{{ route('products.show', $product->id) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h5>

                                    @if($product->category || $product->brand)
                                    <div class="product-meta">
                                        @if($product->category)
                                        {{ $product->category->name }}
                                        @endif
                                        @if($product->brand)
                                        @if($product->category) • @endif{{ $product->brand->name }}
                                        @endif
                                    </div>
                                    @endif

                                    <div class="product-price">
                                        Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                        <span class="price-compare">Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}</span>
                                        @endif
                                    </div>

                                    @if($product->rating_average > 0)
                                    <div class="product-rating">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $product->rating_average)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $product->rating_average)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-text">({{ $product->rating_count }})</span>
                                    </div>
                                    @endif

                                    <div class="product-actions">
                                        @if($product->stock_quantity > 0)
                                            <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                                                <i class="fas fa-cart-plus me-1"></i>Cart
                                            </button>
                                        @else
                                            <button class="btn-add-cart" disabled>
                                                <i class="fas fa-times me-1"></i>Sold Out
                                            </button>
                                        @endif

                                        @auth
                                        <button class="btn-wishlist" onclick="toggleWishlist({{ $product->id }})" title="Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>
                                        @endauth

                                        <a href="{{ route('products.show', $product->id) }}" class="btn-detail" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <!-- No Products State -->
                        <div class="no-products">
                            <div class="no-products-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>No products found</h3>
                            <p>Sorry, we couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>View All Products
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="pagination-wrapper">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        @auth
        loadWishlistStatus();
        @endauth

        // Set initial view mode
        const savedView = localStorage.getItem('products_view') || 'grid';
        setView(savedView);
    });

    // Load wishlist status
    @auth
    function loadWishlistStatus() {
        const productIds = Array.from(document.querySelectorAll('[data-product-id]')).map(el => el.dataset.productId);

        if (productIds.length > 0) {
            fetch('/wishlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_ids: productIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.wishlist.forEach(productId => {
                        const heartIcon = document.querySelector(`[data-product-id="${productId}"] .btn-wishlist i`);
                        const heartBtn = document.querySelector(`[data-product-id="${productId}"] .btn-wishlist`);
                        if (heartIcon && heartBtn) {
                            heartIcon.classList.remove('far');
                            heartIcon.classList.add('fas');
                            heartBtn.classList.add('active');
                        }
                    });
                }
            })
            .catch(error => console.log('Error loading wishlist status:', error));
        }
    }
    @endauth

    // View toggle functionality
    function setView(view) {
        const container = document.getElementById('products-container');
        const grid = document.getElementById('products-grid');
        const viewBtns = document.querySelectorAll('.view-btn');

        // Update button states
        viewBtns.forEach(btn => {
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Update grid/list classes
        if (view === 'list') {
            grid.classList.remove('products-grid');
            grid.classList.add('products-list');
            // Convert cards to list items
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.add('product-list-item');
            });
        } else {
            grid.classList.remove('products-list');
            grid.classList.add('products-grid');
            // Convert back to cards
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('product-list-item');
            });
        }

        // Save preference
        localStorage.setItem('products_view', view);
    }

    // Sort functionality
    function updateSort(sortValue) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }

    // Remove filter functionality
    function removeFilter(filterType) {
        const url = new URL(window.location);

        if (filterType === 'search') {
            url.searchParams.delete('search');
        } else if (filterType === 'category_id') {
            url.searchParams.delete('category_id');
        } else if (filterType === 'brand_id') {
            url.searchParams.delete('brand_id');
        } else if (filterType === 'price') {
            url.searchParams.delete('min_price');
            url.searchParams.delete('max_price');
        }

        window.location.href = url.toString();
    }

    // Auto-submit filter form on radio change
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush
