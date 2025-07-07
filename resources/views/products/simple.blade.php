<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Products - TokoSaya</title>

    <!-- Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h2">All Products</h1>
                <p class="text-muted">Browse our collection</p>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}
                    of {{ $products->total() ?? 0 }} results
                </span>
            </div>
        </div>

        <!-- Simple Filter -->
        <div class="row mb-4">
            <div class="col-md-4">
                <form method="GET" action="{{ route('products.index') }}">
                    <input type="text" class="form-control" name="search"
                           placeholder="Search products..."
                           value="{{ request('search') }}">
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('products.index') }}">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ route('products.index') }}">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            @if($products->count() > 0)
                @foreach($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card product-card">
                        <div style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($product->name, 40) }}</h6>

                            <div class="mb-2">
                                <small class="text-muted">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </small>
                            </div>

                            <div class="mb-3">
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                                </span>
                                @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                                    <br>
                                    <small class="text-muted text-decoration-line-through">
                                        Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}
                                    </small>
                                @endif
                            </div>

                            <div class="d-grid">
                                @if($product->stock_quantity > 0)
                                    <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                        <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        Out of Stock
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No products found</h4>
                        <p class="text-muted">Try different search terms</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="d-flex justify-content-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function addToCart(productId) {
            const button = event.target;
            const originalText = button.innerHTML;

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

            // Simulate add to cart (replace with real implementation)
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check me-1"></i>Added!';
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.disabled = false;
                }, 1500);
            }, 1000);
        }

        console.log('Simple products page loaded successfully');
    </script>
</body>
</html>
