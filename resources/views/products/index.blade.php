@extends('layouts.app')

@section('title', 'All Products - TokoSaya')
@section('meta_description', 'Browse our complete collection of products with advanced filters, competitive prices, and fast shipping.')

@push('styles')
<style>
    .product-filters {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filter-section {
        margin-bottom: 1.5rem;
    }

    .filter-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .price-range-slider {
        margin: 1rem 0;
    }

    .price-display {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-weight: 500;
        color: #2563eb;
    }

    .filter-checkbox {
        margin-bottom: 0.5rem;
    }

    .filter-checkbox input[type="checkbox"] {
        margin-right: 0.5rem;
        transform: scale(1.1);
    }

    .filter-checkbox label {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .product-count {
        color: #64748b;
        font-size: 0.85rem;
    }

    .sort-options {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }

    .view-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .product-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .grid-3 {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .grid-4 {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }

    .list-view {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .product-item-list {
        display: flex;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .product-item-list:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .product-image-list {
        width: 200px;
        height: 150px;
        overflow: hidden;
        position: relative;
    }

    .product-image-list img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-item-list:hover .product-image-list img {
        transform: scale(1.05);
    }

    .product-info-list {
        flex: 1;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-title-list {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .product-description-list {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-meta-list {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .product-rating-list {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .product-actions-list {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .no-products {
        text-align: center;
        padding: 3rem 1rem;
        color: #64748b;
    }

    .no-products i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .filter-toggle {
        display: none;
        background: #2563eb;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        width: 100%;
    }

    @media (max-width: 768px) {
        .filter-toggle {
            display: block;
        }

        .filters-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100vh;
            background: white;
            z-index: 1000;
            transition: left 0.3s ease;
            overflow-y: auto;
            padding: 1rem;
        }

        .filters-sidebar.show {
            left: 0;
        }

        .filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }

        .filter-overlay.show {
            display: block;
        }

        .sort-options {
            flex-direction: column;
            gap: 1rem;
        }

        .view-toggle {
            justify-content: center;
        }

        .product-image-list {
            width: 120px;
            height: 100px;
        }

        .product-info-list {
            padding: 1rem;
        }

        .product-title-list {
            font-size: 1rem;
        }
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
    }

    .load-more-btn {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .load-more-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .load-more-btn:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Products</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2 fw-bold text-dark mb-2">All Products</h1>
            <p class="text-muted mb-0">Discover amazing products with great prices</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex align-items-center justify-content-end gap-3">
                <span class="text-muted">
                    Showing <span x-text="displayResults"></span> results
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <!-- Mobile Filter Toggle -->
            <button class="filter-toggle" @click="toggleFilters()">
                <i class="fas fa-filter me-2"></i>Filters
            </button>

            <!-- Filter Overlay (Mobile) -->
            <div class="filter-overlay" :class="{ 'show': showFilters }" @click="toggleFilters()"></div>

            <aside class="filters-sidebar" :class="{ 'show': showFilters }" x-data="productFilters()">
                <div class="d-flex justify-content-between align-items-center mb-3 d-lg-none">
                    <h5 class="mb-0">Filters</h5>
                    <button class="btn-close" @click="toggleFilters()"></button>
                </div>

                <div class="product-filters">
                    <!-- Search -->
                    <div class="filter-section">
                        <label class="filter-title">Search Products</label>
                        <input type="text" class="form-control" placeholder="Search products..."
                               x-model="filters.search" @input.debounce.300ms="applyFilters()">
                    </div>

                    <!-- Categories -->
                    <div class="filter-section">
                        <label class="filter-title">Categories</label>
                        @if(isset($categories) && $categories->count())
                            @foreach($categories as $category)
                            <div class="filter-checkbox">
                                <label>
                                    <input type="checkbox" value="{{ $category->id }}"
                                           x-model="filters.categories" @change="applyFilters()">
                                    <span>{{ $category->name }}</span>
                                    <span class="product-count">({{ $category->products_count ?? 0 }})</span>
                                </label>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <label class="filter-title">Price Range</label>
                        <div class="price-range-slider">
                            <input type="range" class="form-range" min="0" max="10000000" step="50000"
                                   x-model="filters.priceMin" @input="applyFilters()">
                            <input type="range" class="form-range" min="0" max="10000000" step="50000"
                                   x-model="filters.priceMax" @input="applyFilters()">
                        </div>
                        <div class="price-display">
                            <span x-text="formatPrice(filters.priceMin)"></span>
                            <span x-text="formatPrice(filters.priceMax)"></span>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="filter-section">
                        <label class="filter-title">Brands</label>
                        @if(isset($brands) && $brands->count())
                            @foreach($brands->take(10) as $brand)
                            <div class="filter-checkbox">
                                <label>
                                    <input type="checkbox" value="{{ $brand->id }}"
                                           x-model="filters.brands" @change="applyFilters()">
                                    <span>{{ $brand->name }}</span>
                                    <span class="product-count">({{ $brand->products_count ?? 0 }})</span>
                                </label>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="filter-section">
                        <label class="filter-title">Availability</label>
                        <div class="filter-checkbox">
                            <label>
                                <input type="checkbox" value="in_stock"
                                       x-model="filters.stock" @change="applyFilters()">
                                <span>In Stock</span>
                            </label>
                        </div>
                        <div class="filter-checkbox">
                            <label>
                                <input type="checkbox" value="on_sale"
                                       x-model="filters.onSale" @change="applyFilters()">
                                <span>On Sale</span>
                            </label>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="filter-section">
                        <label class="filter-title">Customer Rating</label>
                        @for($i = 5; $i >= 1; $i--)
                        <div class="filter-checkbox">
                            <label>
                                <input type="checkbox" value="{{ $i }}"
                                       x-model="filters.rating" @change="applyFilters()">
                                <span>
                                    @for($star = 1; $star <= 5; $star++)
                                        @if($star <= $i)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                    & Up
                                </span>
                            </label>
                        </div>
                        @endfor
                    </div>

                    <!-- Clear Filters -->
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary w-100" @click="clearFilters()">
                            <i class="fas fa-times me-2"></i>Clear All Filters
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Products Content -->
        <div class="col-lg-9">
            <!-- Sort and View Options -->
            <div class="sort-options d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <label class="form-label mb-0 fw-medium">Sort by:</label>
                    <select class="form-select" style="width: auto;" x-model="filters.sortBy" @change="applyFilters()">
                        <option value="newest">Newest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="name">Name A-Z</option>
                    </select>
                </div>

                <div class="view-toggle">
                    <button class="view-btn" :class="{ 'active': viewMode === 'grid-3' }" @click="setViewMode('grid-3')">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" :class="{ 'active': viewMode === 'grid-4' }" @click="setViewMode('grid-4')">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" :class="{ 'active': viewMode === 'list' }" @click="setViewMode('list')">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Active Filters -->
            <div x-show="hasActiveFilters()" class="active-filters mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted fw-medium">Active filters:</span>
                    <!-- Add active filter tags here -->
                    <button class="btn btn-sm btn-outline-danger" @click="clearFilters()">
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Products Grid/List -->
            <div x-show="!loading"
                 :class="{
                     'product-grid grid-3': viewMode === 'grid-3',
                     'product-grid grid-4': viewMode === 'grid-4',
                     'list-view': viewMode === 'list'
                 }">

                <!-- Grid View Products -->
                <template x-if="viewMode !== 'list'">
                    <div class="product-grid" :class="viewMode">
                        @if(isset($products) && count($products))
                            @foreach($products as $product)
                            <div class="product-item" x-show="!loading">
                                @include('components.product-card', ['product' => $product])
                            </div>
                            @endforeach
                        @endif
                    </div>
                </template>

                <!-- List View Products -->
                <template x-if="viewMode === 'list'">
                    <div class="list-view">
                        @if(isset($products) && count($products))
                            @foreach($products as $product)
                            <div class="product-item-list" x-show="!loading">
                                <div class="product-image-list">
                                    <img src="{{ $product->primary_image ?? asset('images/placeholder.jpg') }}"
                                         alt="{{ $product->name }}" loading="lazy">
                                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-danger">
                                                -{{ number_format((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-info-list">
                                    <div>
                                        <h3 class="product-title-list">
                                            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        <p class="product-description-list">
                                            {{ $product->short_description ?? Str::limit($product->description, 120) }}
                                        </p>

                                        <div class="product-meta-list">
                                            <span class="badge bg-light text-dark">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                            @if($product->brand)
                                                <span class="text-muted">{{ $product->brand->name }}</span>
                                            @endif
                                            <div class="product-rating-list">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($product->rating_average))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i - 0.5 <= $product->rating_average)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span class="text-muted ms-1">({{ $product->rating_count }})</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-actions-list">
                                        <div class="product-price">
                                            <span class="fw-bold text-primary fs-5">
                                                {{ format_currency($product->price_cents) }}
                                            </span>
                                            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                                <span class="text-muted text-decoration-line-through ms-2">
                                                    {{ format_currency($product->compare_price_cents) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-secondary btn-sm"
                                                    onclick="addToWishlist({{ $product->id }})"
                                                    title="Add to Wishlist">
                                                <i class="far fa-heart"></i>
                                            </button>

                                            @if($product->stock_quantity > 0)
                                                <button class="btn btn-primary"
                                                        onclick="addToCart({{ $product->id }})"
                                                        :disabled="loading">
                                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary" disabled>
                                                    Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </template>
            </div>

            <!-- Loading Skeleton -->
            <div x-show="loading" class="product-grid grid-3">
                @for($i = 1; $i <= 9; $i++)
                <div class="card">
                    <div class="loading-skeleton" style="height: 200px;"></div>
                    <div class="card-body">
                        <div class="loading-skeleton mb-2" style="height: 20px; width: 80%;"></div>
                        <div class="loading-skeleton mb-2" style="height: 15px; width: 60%;"></div>
                        <div class="loading-skeleton" style="height: 25px; width: 40%;"></div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- No Products -->
            @if(isset($products) && $products->count() === 0)
            <div class="no-products">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>Try adjusting your filters or search terms</p>
                <button class="btn btn-primary" @click="clearFilters()">Clear Filters</button>
            </div>
            @endif

            <!-- Pagination -->
            @if(isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
            <div class="pagination-wrapper">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif

            <!-- Load More (for AJAX pagination) -->
            <div class="text-center mt-4" x-show="canLoadMore">
                <button class="load-more-btn" @click="loadMore()" :disabled="loading">
                    <span x-show="!loading">Load More Products</span>
                    <span x-show="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i>Loading...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simplified product filtering functionality
    function productFilters() {
        return {
            filters: {
                search: '',
                categories: [],
                brands: [],
                priceMin: 0,
                priceMax: 10000000,
                rating: [],
                stock: [],
                onSale: false,
                sortBy: 'newest'
            },
            loading: false,
            viewMode: 'grid-3',
            showFilters: false,
            displayResults: '{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? 0 }}',

            init() {
                this.loadUrlParams();
                this.loading = false; // Set loading selesai saat init
            },

            toggleFilters() {
                this.showFilters = !this.showFilters;
            },

            setViewMode(mode) {
                this.viewMode = mode;
                localStorage.setItem('productViewMode', mode);
            },

            formatPrice(cents) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(cents / 100);
            },

            hasActiveFilters() {
                return this.filters.search !== '' ||
                       this.filters.categories.length > 0 ||
                       this.filters.brands.length > 0 ||
                       this.filters.priceMin > 0 ||
                       this.filters.priceMax < 10000000 ||
                       this.filters.rating.length > 0 ||
                       this.filters.stock.length > 0 ||
                       this.filters.onSale;
            },

            clearFilters() {
                this.filters = {
                    search: '',
                    categories: [],
                    brands: [],
                    priceMin: 0,
                    priceMax: 10000000,
                    rating: [],
                    stock: [],
                    onSale: false,
                    sortBy: 'newest'
                };
                this.applyFilters();
            },

            applyFilters() {
                if (this.loading) return; // Prevent multiple requests

                this.loading = true;

                const params = new URLSearchParams();

                // Add filters to params
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.categories.length) params.append('category_id', this.filters.categories[0]);
                if (this.filters.brands.length) params.append('brand_id', this.filters.brands[0]);
                if (this.filters.priceMin > 0) params.append('min_price', this.filters.priceMin / 100);
                if (this.filters.priceMax < 10000000) params.append('max_price', this.filters.priceMax / 100);
                if (this.filters.sortBy !== 'newest') params.append('sort', this.filters.sortBy);

                // Simple page reload with new params (lebih reliable)
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = newUrl;
            },

            loadUrlParams() {
                const params = new URLSearchParams(window.location.search);

                if (params.get('search')) this.filters.search = params.get('search');
                if (params.get('category_id')) this.filters.categories = [params.get('category_id')];
                if (params.get('brand_id')) this.filters.brands = [params.get('brand_id')];
                if (params.get('min_price')) this.filters.priceMin = parseInt(params.get('min_price')) * 100;
                if (params.get('max_price')) this.filters.priceMax = parseInt(params.get('max_price')) * 100;
                if (params.get('sort')) this.filters.sortBy = params.get('sort');

                // Load saved view mode
                const savedViewMode = localStorage.getItem('productViewMode');
                if (savedViewMode) this.viewMode = savedViewMode;
            }
        };
    }

    // Simplified addToCart function
    function addToCart(productId) {
        const button = event.target;
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                button.innerHTML = '<i class="fas fa-check me-1"></i>Added!';
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.disabled = false;
                }, 2000);
            } else {
                throw new Error(data.message || 'Failed to add to cart');
            }
        })
        .catch(error => {
            console.error('Add to cart error:', error);
            button.innerHTML = originalText;
            button.disabled = false;
            alert('Error adding to cart. Please try again.');
        });
    }

    // Simplified addToWishlist function
    function addToWishlist(productId) {
        const button = event.target.closest('button');
        const icon = button.querySelector('i');

        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.added) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-danger');
                } else {
                    icon.classList.remove('fas', 'text-danger');
                    icon.classList.add('far');
                }
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            alert('Please login to use wishlist');
        });
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Products page loaded successfully');
    });
</script>
@endpush
