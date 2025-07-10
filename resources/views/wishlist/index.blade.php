@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<style>
    .wishlist-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .wishlist-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .wishlist-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .wishlist-subtitle {
        color: #666;
        font-size: 1rem;
    }

    .wishlist-item {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
        background: white;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .product-image-placeholder {
        width: 80px;
        height: 80px;
        background-color: #f3f4f6;
        border-radius: 8px;
        margin-right: 20px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .product-info {
        flex: 1;
        margin-right: 20px;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }

    .product-name a {
        text-decoration: none;
        color: #333;
    }

    .product-name a:hover {
        color: #007bff;
    }

    .product-category {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-right: 15px;
    }

    .stock-status {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stock-in {
        color: #28a745;
    }

    .stock-out {
        color: #dc3545;
    }

    .product-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-disabled {
        background-color: #6c757d;
        color: white;
        cursor: not-allowed;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        padding: 8px;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .empty-wishlist {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        color: #ddd;
    }

    .suggested-products {
        margin-top: 50px;
    }

    .suggested-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .product-card-placeholder {
        width: 100%;
        height: 200px;
        background-color: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .product-card-body {
        padding: 15px;
    }

    .clear-all {
        text-align: center;
        margin-top: 20px;
    }

    .clear-all button {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .clear-all button:hover {
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .wishlist-item {
            flex-direction: column;
            text-align: center;
        }

        .product-image, .product-image-placeholder {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .product-info {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }
</style>

<script>
function moveToCartAjax(productId) {
    const button = event.target;
    const originalText = button.textContent;

    button.disabled = true;
    button.textContent = 'Moving...';

    // Call cart add directly
    fetch('/cart/add', {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove from wishlist via AJAX
            return fetch(`/wishlist/remove/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
        } else {
            throw new Error(data.message);
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from page
            button.closest('.wishlist-item').remove();
            updateCartCount();
            updateWishlistCount();
            showNotification('Product moved to cart successfully!', 'success');
        }
    })
    .catch(error => {
        showNotification('Error: ' + error.message, 'error');
        button.disabled = false;
        button.textContent = originalText;
    });
}
</script>

<div class="wishlist-container">
    <!-- Header -->
    <div class="wishlist-header">
        <h1 class="wishlist-title">My Wishlist</h1>
        <p class="wishlist-subtitle">{{ $totalItems }} {{ $totalItems == 1 ? 'item' : 'items' }} saved for later</p>
    </div>

    @if($wishlistItems->count() > 0)
        <!-- Wishlist Items -->
        <div class="wishlist-items">
            @foreach($wishlistItems as $item)
                <div class="wishlist-item">
                    <!-- Product Image -->
                    @if($item->product->images->first())
                        <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}"
                             alt="{{ $item->product->name }}"
                             class="product-image">
                    @else
                        <div class="product-image-placeholder">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- Product Info -->
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="{{ route('products.show', $item->product->slug) }}">
                                {{ $item->product->name }}
                            </a>
                        </h3>

                        @if($item->product->category)
                            <p class="product-category">{{ $item->product->category->name }}</p>
                        @endif

                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 15px;">
                            <!-- Price -->
                            <div class="product-price">
                                Rp {{ number_format($item->product->price_cents / 100, 0, ',', '.') }}
                            </div>

                            <!-- Stock Status -->
                            @if($item->product->stock_quantity > 0)
                                <span class="stock-status stock-in">In Stock</span>
                            @else
                                <span class="stock-status stock-out">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="product-actions">
                        <!-- Add to Cart Button -->
                        @if($item->product->stock_quantity > 0)
                            <form action="{{ route('wishlist.move_to_cart', $item->product) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Add to Cart
                                </button>
                            </form>
                        @else
                            <button disabled class="btn btn-disabled">
                                Out of Stock
                            </button>
                        @endif

                        <!-- Remove Button -->
                        <form action="{{ route('wishlist.remove', $item->product) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Remove this item from wishlist?')"
                                    title="Remove from wishlist">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Clear All Button -->
        @if($wishlistItems->count() > 1)
            <div class="clear-all">
                <form action="{{ route('wishlist.clear') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to clear your entire wishlist?')">
                        Clear All Items
                    </button>
                </form>
            </div>
        @endif

    @else
        <!-- Empty Wishlist -->
        <div class="empty-wishlist">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 100%; height: 100%;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px; color: #333;">Your Wishlist is Empty</h3>
            <p style="color: #666; margin-bottom: 25px;">Save items you love and find them here later</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Continue Shopping
            </a>
        </div>
    @endif

    <!-- Suggested Products -->
    @if($suggestedProducts && $suggestedProducts->count() > 0)
        <div class="suggested-products">
            <h2 class="suggested-title">You Might Also Like</h2>
            <div class="products-grid">
                @foreach($suggestedProducts as $product)
                    <div class="product-card">
                        @if($product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                 alt="{{ $product->name }}">
                        @else
                            <div class="product-card-placeholder">
                                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="product-card-body">
                            <h3 style="font-weight: 600; margin-bottom: 10px; color: #333;">
                                <a href="{{ route('products.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p style="font-size: 1.1rem; font-weight: bold; color: #333; margin-bottom: 15px;">
                                Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}
                            </p>
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    Add to Wishlist
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
