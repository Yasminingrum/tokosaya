@extends('layouts.app')

@section('title', 'All Products - TokoSaya')

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .product-image {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        text-decoration: none !important;
    }
    .product-title:hover {
        color: #2563eb;
    }
    .price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2563eb;
    }
    .old-price {
        font-size: 0.9rem;
        color: #64748b;
        text-decoration: line-through;
    }
    .filters-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h2 fw-bold">All Products</h1>
            <p class="text-muted">Discover amazing products with great prices</p>
        </div>
        <div class="col-md-6 text-end">
            <span class="text-muted">
                Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}
                of {{ $products->total() ?? 0 }} results
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="filters-section">
                <h5 class="mb-3">Filters</h5>

                <form method="GET" action="{{ route('products.index') }}">
                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Search</label>
                        <input type="text" class="form-control" name="search"
                               value="{{ request('search') }}" placeholder="Search products...">
                    </div>

                    <!-- Categories -->
                    @if($categories->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Brands -->
                    @if($brands->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                        {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Sort -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear All</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Content -->
        <div class="col-lg-9">
            @if($products->count() > 0)
                <!-- Products Grid -->
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-md-6 col-xl-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <img src="{{ asset('images/placeholder.jpg') }}"
                                     class="product-image"
                                     alt="{{ $product->name }}">

                                @if($product->featured)
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-warning">Featured</span>
                                </div>
                                @endif

                                @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger">
                                        SALE
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2">
                                    <a href="#" class="product-title">
                                        {{ Str::limit($product->name, 50) }}
                                    </a>
                                </h6>

                                <div class="mb-2">
                                    <small class="text-muted">
                                        {{ $product->category->name ?? 'No Category' }}
                                        @if($product->brand)
                                            • {{ $product->brand->name }}
                                        @endif
                                    </small>
                                </div>

                                @if($product->rating_average > 0)
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->rating_average))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                    <small class="text-muted ms-1">({{ $product->rating_count }})</small>
                                </div>
                                @endif

                                <div class="mt-auto">
                                    <div class="mb-3">
                                        <div class="price">
                                            Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                                        </div>
                                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                        <div class="old-price">
                                            Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                                        </div>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-secondary btn-sm" title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </button>

                                        @if($product->stock_quantity > 0)
                                        <button class="btn btn-primary btn-sm flex-grow-1"
                                                onclick="addToCart({{ $product->id }})">
                                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                        @else
                                        <button class="btn btn-outline-secondary btn-sm flex-grow-1" disabled>
                                            Out of Stock
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h3>No products found</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simple add to cart function
function addToCart(productId) {
    const button = event.target;
    const originalText = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

    // Simulate API call
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check me-1"></i>Added!';
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
            button.disabled = false;
        }, 2000);
    }, 1000);
}

// Auto submit form on select change
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select[name="category_id"], select[name="brand_id"], select[name="sort"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
