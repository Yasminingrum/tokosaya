@extends('layouts.app')

@section('title', $product->name . ' | TokoSaya')
@section('description', $product->short_description ?? strip_tags($product->description))

@push('styles')
<style>
    .product-detail-section {
        padding: 40px 0;
        background: var(--cream-color);
    }

    .breadcrumb {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: var(--secondary-color);
    }

    .breadcrumb-item a {
        color: var(--secondary-color);
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: var(--dark-color);
    }

    /* Product Gallery */
    .product-gallery {
        background: white;
        border-radius: 15px;
        padding: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .main-image {
        position: relative;
        margin-bottom: 20px;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
        background: var(--light-gray);
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .main-image:hover img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: var(--accent-color);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 2;
    }

    .image-thumbnails {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 5px;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s ease;
        flex-shrink: 0;
    }

    .thumbnail:hover,
    .thumbnail.active {
        border-color: var(--primary-color);
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info */
    .product-info {
        background: white;
        border-radius: 15px;
        padding: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .product-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .product-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: var(--secondary-color);
    }

    .product-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: var(--light-gray);
        border-radius: 10px;
    }

    .stars {
        color: var(--accent-color);
        font-size: 1.2rem;
    }

    .rating-text {
        color: var(--secondary-color);
        font-weight: 500;
    }

    .rating-breakdown {
        color: var(--secondary-color);
        font-size: 0.9rem;
    }

    .product-price {
        margin-bottom: 25px;
    }

    .price-current {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .price-compare {
        font-size: 1.5rem;
        color: var(--secondary-color);
        text-decoration: line-through;
        margin-left: 15px;
    }

    .price-save {
        background: #ef4444;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 15px;
    }

    .product-description {
        margin-bottom: 25px;
        color: var(--secondary-color);
        line-height: 1.6;
    }

    /* Stock Info */
    .stock-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
        padding: 15px;
        background: var(--light-gray);
        border-radius: 10px;
    }

    .stock-status {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .stock-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .in-stock .stock-indicator {
        background: #22c55e;
    }

    .low-stock .stock-indicator {
        background: #f59e0b;
    }

    .out-stock .stock-indicator {
        background: #ef4444;
    }

    /* Variants */
    .variant-section {
        margin-bottom: 25px;
    }

    .variant-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 12px;
    }

    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .variant-option {
        padding: 8px 16px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        background: white;
        color: var(--dark-color);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .variant-option:hover {
        border-color: var(--primary-color);
    }

    .variant-option.active {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
    }

    .variant-option:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Quantity Selector */
    .quantity-section {
        margin-bottom: 30px;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .quantity-controls {
        display: flex;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
    }

    .qty-btn {
        background: white;
        border: none;
        padding: 10px 15px;
        color: var(--dark-color);
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .qty-btn:hover {
        background: var(--light-gray);
    }

    .qty-input {
        border: none;
        padding: 10px 15px;
        text-align: center;
        width: 60px;
        background: white;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .btn-add-cart {
        flex: 2;
        background: var(--primary-color);
        border: none;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: background-color 0.3s ease;
    }

    .btn-add-cart:hover {
        background: var(--dark-color);
    }

    .btn-wishlist {
        flex: 0 0 auto;
        background: none;
        border: 2px solid var(--border-color);
        color: var(--secondary-color);
        padding: 15px 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-wishlist:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
    }

    .btn-wishlist.active {
        border-color: var(--accent-color);
        color: var(--accent-color);
        background: var(--cream-color);
    }

    .btn-buy-now {
        flex: 1;
        background: var(--accent-color);
        border: none;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Product Features */
    .product-features {
        background: var(--light-gray);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        color: var(--secondary-color);
        font-size: 0.9rem;
    }

    .feature-item:last-child {
        margin-bottom: 0;
    }

    .feature-icon {
        color: var(--accent-color);
        width: 20px;
    }

    /* Tabs */
    .product-tabs {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-top: 40px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .nav-tabs {
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 25px;
    }

    .nav-link {
        color: var(--secondary-color);
        border: none;
        padding: 15px 25px;
        font-weight: 500;
        border-radius: 0;
        background: none;
    }

    .nav-link.active {
        color: var(--primary-color);
        background: none;
        border-bottom: 3px solid var(--primary-color);
    }

    .tab-content {
        color: var(--secondary-color);
        line-height: 1.6;
    }

    /* Reviews */
    .review-item {
        padding: 20px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        margin-bottom: 20px;
        background: white;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .reviewer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
    }

    .reviewer-name {
        font-weight: 600;
        color: var(--dark-color);
    }

    .review-date {
        font-size: 0.85rem;
        color: var(--secondary-color);
    }

    .review-rating {
        color: var(--accent-color);
    }

    .review-content {
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .review-helpful {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.9rem;
        color: var(--secondary-color);
    }

    /* Related Products */
    .related-products {
        margin-top: 60px;
    }

    .related-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .product-title {
            font-size: 1.5rem;
        }

        .price-current {
            font-size: 2rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .quantity-selector {
            justify-content: space-between;
        }

        .product-meta {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endpush

@section('content')
<section class="product-detail-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
                @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-6 mb-4">
                <div class="product-gallery">
                    <div class="main-image">
                        @if($product->featured)
                        <div class="product-badge">HOT</div>
                        @endif
                        <img src="{{ $product->images->first()->image_url ?? 'https://via.placeholder.com/500x500/e2e8f0/64748b?text=No+Image' }}"
                             alt="{{ $product->name }}" id="mainImage">
                    </div>

                    @if($product->images->count() > 1)
                    <div class="image-thumbnails">
                        @foreach($product->images as $index => $image)
                        <div class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                             onclick="changeMainImage('{{ $image->image_url }}', this)">
                            <img src="{{ $image->image_url }}" alt="{{ $product->name }}">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <h1 class="product-title">{{ $product->name }}</h1>

                    <div class="product-meta">
                        @if($product->category)
                        <span><i class="fas fa-tag"></i> {{ $product->category->name }}</span>
                        @endif
                        @if($product->brand)
                        <span><i class="fas fa-building"></i> {{ $product->brand->name }}</span>
                        @endif
                        <span><i class="fas fa-barcode"></i> {{ $product->sku }}</span>
                    </div>

                    @if($avgRating > 0)
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $avgRating)
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-text">{{ number_format($avgRating, 1) }} / 5</span>
                        <span class="rating-breakdown">({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})</span>
                    </div>
                    @endif

                    <div class="product-price">
                        <span class="price-current">Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}</span>
                        @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                            <span class="price-compare">Rp {{ number_format($product->compare_price_cents / 100, 0, ',', '.') }}</span>
                            @php
                                $discount = round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100);
                            @endphp
                            <span class="price-save">Save {{ $discount }}%</span>
                        @endif
                    </div>

                    @if($product->short_description)
                    <div class="product-description">
                        {{ $product->short_description }}
                    </div>
                    @endif

                    <!-- Stock Info -->
                    <div class="stock-info">
                        @if($product->stock_quantity > 0)
                            @if($product->stock_quantity <= $product->min_stock_level)
                                <div class="stock-status low-stock">
                                    <span class="stock-indicator"></span>
                                    <span>Only {{ $product->stock_quantity }} left in stock!</span>
                                </div>
                            @else
                                <div class="stock-status in-stock">
                                    <span class="stock-indicator"></span>
                                    <span>In Stock ({{ $product->stock_quantity }} available)</span>
                                </div>
                            @endif
                        @else
                            <div class="stock-status out-stock">
                                <span class="stock-indicator"></span>
                                <span>Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    <!-- Variants -->
                    @if($product->variants->count() > 0)
                    <div class="variant-section">
                        <div class="variant-title">Choose Variant:</div>
                        <div class="variant-options">
                            @foreach($product->variants as $variant)
                            <button class="variant-option"
                                    data-variant-id="{{ $variant->id }}"
                                    data-price="{{ $variant->price_adjustment_cents }}"
                                    data-stock="{{ $variant->stock_quantity }}"
                                    {{ $variant->stock_quantity == 0 ? 'disabled' : '' }}>
                                {{ $variant->variant_name }}: {{ $variant->variant_value }}
                                @if($variant->price_adjustment_cents != 0)
                                    ({{ $variant->price_adjustment_cents > 0 ? '+' : '' }}Rp {{ number_format(abs($variant->price_adjustment_cents) / 100, 0, ',', '.') }})
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Quantity -->
                    @if($product->stock_quantity > 0)
                    <div class="quantity-section">
                        <div class="quantity-selector">
                            <span>Quantity:</span>
                            <div class="quantity-controls">
                                <button type="button" class="qty-btn" onclick="updateQuantity(-1)">−</button>
                                <input type="number" class="qty-input" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                                <button type="button" class="qty-btn" onclick="updateQuantity(1)">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="btn-add-cart" onclick="addToCartWithQuantity()">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>

                        @auth
                        <button class="btn-wishlist" onclick="toggleWishlist({{ $product->id }})" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        @endauth

                        <button class="btn-buy-now" onclick="buyNow()">
                            <i class="fas fa-bolt me-2"></i>Buy Now
                        </button>
                    </div>
                    @endif

                    <!-- Product Features -->
                    <div class="product-features">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt feature-icon"></i>
                            <span>1 Year Warranty</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shipping-fast feature-icon"></i>
                            <span>Free Shipping over Rp 250,000</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-undo-alt feature-icon"></i>
                            <span>30 Days Return Policy</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-headset feature-icon"></i>
                            <span>24/7 Customer Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">Description</a>
                </li>
                @if($product->attributeValues->count() > 0)
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#specifications">Specifications</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews ({{ $totalReviews }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#shipping">Shipping Info</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description">
                    {!! nl2br(e($product->description)) !!}
                </div>

                <!-- Specifications Tab -->
                @if($product->attributeValues->count() > 0)
                <div class="tab-pane fade" id="specifications">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            @foreach($product->attributeValues as $attributeValue)
                            <tr>
                                <td style="width: 30%; font-weight: 600;">{{ $attributeValue->attribute->name }}</td>
                                <td>
                                    @if($attributeValue->attribute->type === 'boolean')
                                        {{ $attributeValue->value_boolean ? 'Yes' : 'No' }}
                                    @elseif($attributeValue->attribute->type === 'number')
                                        {{ $attributeValue->value_number }}
                                    @else
                                        {{ $attributeValue->value_text }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    @if($product->reviews->count() > 0)
                        @foreach($product->reviews as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">{{ $review->user->first_name }} {{ Str::substr($review->user->last_name, 0, 1) }}.</div>
                                        <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            @if($review->title)
                            <div class="review-title mb-2">
                                <strong>{{ $review->title }}</strong>
                            </div>
                            @endif

                            <div class="review-content">
                                {{ $review->review }}
                            </div>

                            <div class="review-helpful">
                                <span>Was this helpful?</span>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-thumbs-up me-1"></i>Yes ({{ $review->helpful_count }})
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-star-o fa-3x text-muted mb-3"></i>
                            <h5>No reviews yet</h5>
                            <p class="text-muted">Be the first to review this product!</p>
                        </div>
                    @endif
                </div>

                <!-- Shipping Tab -->
                <div class="tab-pane fade" id="shipping">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <ul>
                                <li>Standard shipping: 3-5 business days</li>
                                <li>Express shipping: 1-2 business days</li>
                                <li>Free shipping on orders over Rp 250,000</li>
                                <li>Same day delivery available in Jakarta</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Return Policy</h6>
                            <ul>
                                <li>30-day return policy</li>
                                <li>Items must be in original condition</li>
                                <li>Free returns for defective products</li>
                                <li>Customer pays return shipping for exchanges</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="related-products">
            <h3 class="section-title">Related Products</h3>
            <div class="related-products-grid">
                @foreach($relatedProducts as $relatedProduct)
                <div class="product-card" data-product-id="{{ $relatedProduct->id }}">
                    <div class="product-image">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}">
                            <img src="{{ $relatedProduct->images->first()->image_url ?? 'https://via.placeholder.com/300x300/e2e8f0/64748b?text=No+Image' }}"
                                 alt="{{ $relatedProduct->name }}" loading="lazy">
                        </a>
                    </div>

                    <div class="product-info">
                        <h5 class="product-title">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" class="text-decoration-none text-dark">
                                {{ $relatedProduct->name }}
                            </a>
                        </h5>

                        <div class="product-price">
                            Rp {{ number_format($relatedProduct->price_cents / 100, 0, ',', '.') }}
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-add-cart flex-grow-1" onclick="addToCart({{ $relatedProduct->id }})">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    let selectedVariant = null;
    let basePrice = {{ $product->price_cents }};

    document.addEventListener('DOMContentLoaded', function() {
        @auth
        loadWishlistStatus();
        @endauth

        // Initialize variant selection
        document.querySelectorAll('.variant-option').forEach(option => {
            option.addEventListener('click', function() {
                if (!this.disabled) {
                    selectVariant(this);
                }
            });
        });

        // Track product view
        fetch('/api/products/track-view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_id: {{ $product->id }} })
        }).catch(error => console.log('Error tracking view:', error));
    });

    // Change main image
    function changeMainImage(imageUrl, thumbnail) {
        document.getElementById('mainImage').src = imageUrl;

        // Update thumbnail active state
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }

    // Select variant
    function selectVariant(option) {
        // Remove active class from all options
        document.querySelectorAll('.variant-option').forEach(opt => {
            opt.classList.remove('active');
        });

        // Add active class to selected option
        option.classList.add('active');

        // Store selected variant
        selectedVariant = {
            id: option.dataset.variantId,
            priceAdjustment: parseInt(option.dataset.priceAdjustment),
            stock: parseInt(option.dataset.stock)
        };

        // Update price
        updatePrice();

        // Update quantity max
        document.getElementById('quantity').max = selectedVariant.stock;

        // Update stock info
        updateStockInfo();
    }

    // Update price display
    function updatePrice() {
        const priceElement = document.querySelector('.price-current');
        const newPrice = basePrice + (selectedVariant ? selectedVariant.priceAdjustment : 0);
        priceElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(newPrice / 100);
    }

    // Update stock information
    function updateStockInfo() {
        const stockInfo = document.querySelector('.stock-info');
        const stock = selectedVariant ? selectedVariant.stock : {{ $product->stock_quantity }};
        const minStock = {{ $product->min_stock_level }};

        let html = '';
        if (stock > 0) {
            if (stock <= minStock) {
                html = `
                    <div class="stock-status low-stock">
                        <span class="stock-indicator"></span>
                        <span>Only ${stock} left in stock!</span>
                    </div>
                `;
            } else {
                html = `
                    <div class="stock-status in-stock">
                        <span class="stock-indicator"></span>
                        <span>In Stock (${stock} available)</span>
                    </div>
                `;
            }
        } else {
            html = `
                <div class="stock-status out-stock">
                    <span class="stock-indicator"></span>
                    <span>Out of Stock</span>
                </div>
            `;
        }

        stockInfo.innerHTML = html;
    }

    // Update quantity
    function updateQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        const newValue = currentValue + change;
        const maxStock = selectedVariant ? selectedVariant.stock : {{ $product->stock_quantity }};

        if (newValue >= 1 && newValue <= maxStock) {
            quantityInput.value = newValue;
        }
    }

    // Add to cart with quantity
    function addToCartWithQuantity() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};

        const data = {
            product_id: productId,
            quantity: quantity
        };

        if (selectedVariant) {
            data.variant_id = selectedVariant.id;
        }

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                showNotification('Product added to cart successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add product to cart', 'error');
        });
    }

    // Buy now functionality
    function buyNow() {
        // Add to cart first, then redirect to checkout
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};

        const data = {
            product_id: productId,
            quantity: quantity
        };

        if (selectedVariant) {
            data.variant_id = selectedVariant.id;
        }

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to checkout
                window.location.href = '{{ route("checkout.index") }}';
            } else {
                showNotification(data.message || 'Failed to process order', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to process order', 'error');
        });
    }

    // Load wishlist status
    @auth
    function loadWishlistStatus() {
        fetch('/wishlist/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ product_ids: [{{ $product->id }}] })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.wishlist.includes({{ $product->id }})) {
                const heartIcon = document.querySelector('.btn-wishlist i');
                const heartBtn = document.querySelector('.btn-wishlist');
                if (heartIcon && heartBtn) {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                    heartBtn.classList.add('active');
                }
            }
        })
        .catch(error => console.log('Error loading wishlist status:', error));
    }
    @endauth

    // Quantity input validation
    document.getElementById('quantity').addEventListener('input', function() {
        const value = parseInt(this.value);
        const maxStock = selectedVariant ? selectedVariant.stock : {{ $product->stock_quantity }};

        if (value < 1) {
            this.value = 1;
        } else if (value > maxStock) {
            this.value = maxStock;
        }
    });
</script>
@endpush
