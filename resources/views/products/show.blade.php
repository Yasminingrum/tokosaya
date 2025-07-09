@extends('layouts.app')

@section('title', $product->name . ' - TokoSaya')
@section('meta_description', Str::limit($product->description, 150))

@push('styles')
<style>
    /* Base Styles */
    [x-cloak] {
        display: none !important;
    }

    .product-detail-container {
        opacity: 0;
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    @keyframes fadeIn {
        to { opacity: 1; }
    }

    /* Image Gallery */
    .product-gallery-container {
        position: sticky;
        top: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 20px;
    }

    .main-product-image {
        width: 100%;
        height: 400px;
        object-fit: contain;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }

    .main-product-image:hover {
        transform: scale(1.02);
    }

    .thumbnail-container {
        display: flex;
        gap: 12px;
        margin-top: 15px;
        padding: 5px 0;
        overflow-x: auto;
    }

    .product-thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 6px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        background: #f8f9fa;
    }

    .product-thumbnail.active {
        border-color: #0d6efd;
    }

    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info */
    .product-info-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 25px;
    }

    .product-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .product-price-container {
        margin: 20px 0;
    }

    .current-price {
        font-size: 2rem;
        font-weight: 700;
        color: #0d6efd;
    }

    .original-price {
        font-size: 1.25rem;
        color: #6c757d;
        text-decoration: line-through;
        margin-left: 8px;
    }

    .discount-badge {
        background-color: #dc3545;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-left: 10px;
    }

    .stock-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .in-stock {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .low-stock {
        background-color: #fff3cd;
        color: #664d03;
    }

    .out-of-stock {
        background-color: #f8d7da;
        color: #842029;
    }

    /* Variants */
    .variant-option {
        padding: 8px 15px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        margin-right: 8px;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .variant-option.active {
        border-color: #0d6efd;
        background-color: #e7f1ff;
        color: #0d6efd;
    }

    .variant-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }

    /* Quantity Selector */
    .quantity-selector {
        display: flex;
        align-items: center;
        margin: 25px 0;
    }

    .quantity-input-group {
        display: flex;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        margin: 0 15px;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .quantity-value {
        width: 50px;
        text-align: center;
        border: none;
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        font-weight: 500;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin: 25px 0;
    }

    .btn-add-cart {
        flex: 1;
        padding: 12px;
        font-weight: 500;
    }

    .btn-wishlist {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: white;
        color: #6c757d;
        transition: all 0.15s ease;
    }

    .btn-wishlist.active {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    /* Product Features */
    .product-features {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 25px 0;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }

    .feature-item:last-child {
        margin-bottom: 0;
    }

    .feature-icon {
        width: 20px;
        color: #0d6efd;
        margin-right: 10px;
    }

    /* Tabs */
    .product-tabs {
        margin-top: 40px;
    }

    .nav-tabs .nav-link {
        font-weight: 500;
        color: #495057;
    }

    .tab-content {
        padding: 25px 0;
    }

    /* Specifications */
    .spec-row {
        display: flex;
        border-bottom: 1px solid #dee2e6;
        padding: 12px 0;
    }

    .spec-label {
        width: 200px;
        font-weight: 500;
        color: #495057;
    }

    .spec-value {
        flex: 1;
        color: #212529;
    }

    /* Reviews */
    .review-summary {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .overall-rating {
        font-size: 3rem;
        font-weight: 700;
        color: #0d6efd;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .rating-bar-fill {
        flex: 1;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        margin: 0 10px;
    }

    .rating-bar-progress {
        height: 100%;
        background: #ffc107;
        border-radius: 4px;
    }

    .review-item {
        padding: 20px 0;
        border-bottom: 1px solid #e9ecef;
    }

    /* Zoom Overlay */
    .zoom-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .zoom-overlay.show {
        opacity: 1;
    }

    .zoom-image {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        transform: scale(0.9);
        transition: transform 0.2s ease;
    }

    .zoom-overlay.show .zoom-image {
        transform: scale(1);
    }

    .zoom-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .product-info-container {
            margin-top: 30px;
        }

        .main-product-image {
            height: 350px;
        }
    }

    @media (max-width: 768px) {
        .product-title {
            font-size: 1.5rem;
        }

        .current-price {
            font-size: 1.75rem;
        }

        .main-product-image {
            height: 300px;
        }

        .spec-row {
            flex-direction: column;
        }

        .spec-label {
            width: 100%;
            margin-bottom: 5px;
        }
    }

    @media (max-width: 576px) {
        .main-product-image {
            height: 250px;
        }

        .product-thumbnail {
            width: 60px;
            height: 60px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-wishlist {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 product-detail-container" x-data="productDetail()" x-cloak>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="product-gallery-container">
                <!-- Main Image -->
                <div class="position-relative">
                    <img :src="selectedImage"
                         :alt="productName"
                         class="main-product-image"
                         loading="eager"
                         @load="imageLoaded = true"
                         @click="openImageZoom()">

                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="discount-badge">
                            -{{ number_format((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100) }}%
                        </span>
                    </div>
                    @endif

                    @if($product->stock_quantity <= 0)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-dark">Out of Stock</span>
                    </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($product->images && $product->images->count() > 1)
                <div class="thumbnail-container">
                    @foreach($product->images as $index => $image)
                    <div class="product-thumbnail"
                         :class="{ 'active': selectedImageIndex === {{ $index }} }"
                         @click="selectImage({{ $index }}, '{{ $image->image_url }}')">
                        <img src="{{ $image->image_url }}"
                             alt="{{ $image->alt_text ?? $product->name }}"
                             loading="lazy">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-info-container">
                <!-- Product Title -->
                <h1 class="product-title">{{ $product->name }}</h1>

                <!-- Brand -->
                @if($product->brand)
                <div class="mb-3">
                    <span class="text-muted">Brand: </span>
                    <a href="{{ route('products.brand', $product->brand->slug) }}" class="text-decoration-none">
                        <strong>{{ $product->brand->name }}</strong>
                    </a>
                </div>
                @endif

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rating-stars me-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($product->rating_average))
                                <i class="fas fa-star text-warning"></i>
                            @elseif($i - 0.5 <= $product->rating_average)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @else
                                <i class="far fa-star text-muted"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted me-2">
                        {{ number_format($product->rating_average, 1) }} ({{ $product->rating_count }} reviews)
                    </span>
                    <a href="#reviews" class="text-decoration-none">See all reviews</a>
                </div>

                <!-- Price -->
                <div class="product-price-container">
                    <span class="current-price" x-text="formatPrice(currentPrice)">
                        {{ format_currency($product->price_cents) }}
                    </span>
                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                    <span class="original-price">
                        {{ format_currency($product->compare_price_cents) }}
                    </span>
                    <span class="discount-badge">
                        Save {{ format_currency($product->compare_price_cents - $product->price_cents) }}
                    </span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div>
                    <span x-show="maxQuantity > 10" class="stock-badge in-stock">
                        <i class="fas fa-check-circle me-1"></i>In Stock (<span x-text="maxQuantity"></span> available)
                    </span>
                    <span x-show="maxQuantity > 0 && maxQuantity <= 10" class="stock-badge low-stock">
                        <i class="fas fa-exclamation-triangle me-1"></i>Only <span x-text="maxQuantity"></span> left
                    </span>
                    <span x-show="maxQuantity <= 0" class="stock-badge out-of-stock">
                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                    </span>
                </div>

                <!-- Variants -->
                @if($product->variants && $product->variants->count() > 0)
                <div class="product-variants mt-4">
                    @php
                        $variantGroups = $product->variants->groupBy('variant_name');
                    @endphp

                    @foreach($variantGroups as $variantName => $variants)
                    <div class="mb-3">
                        <div class="fw-bold mb-2">{{ $variantName }}:</div>
                        <div class="d-flex flex-wrap">
                            @foreach($variants as $variant)
                            <div class="variant-option"
                                 :class="{
                                     'active': selectedVariant === {{ $variant->id }},
                                     'disabled': {{ $variant->stock_quantity <= 0 ? 'true' : 'false' }}
                                 }"
                                 @click="selectVariant({{ $variant->id }}, {{ $variant->price_adjustment_cents }}, {{ $variant->stock_quantity }})">
                                {{ $variant->variant_value }}
                                @if($variant->price_adjustment_cents != 0)
                                    <small class="text-muted ms-1">
                                        ({{ $variant->price_adjustment_cents > 0 ? '+' : '' }}{{ format_currency($variant->price_adjustment_cents) }})
                                    </small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Quantity Selector -->
                <div class="quantity-selector">
                    <label class="fw-bold me-3">Quantity:</label>
                    <div class="quantity-input-group">
                        <button class="quantity-btn" @click="decreaseQuantity()" :disabled="quantity <= 1">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-value" x-model.number="quantity"
                               min="1" :max="maxQuantity" @input="validateQuantity()">
                        <button class="quantity-btn" @click="increaseQuantity()" :disabled="quantity >= maxQuantity">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <span class="text-muted" x-show="maxQuantity > 0">
                        Max: <span x-text="maxQuantity"></span>
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-primary btn-add-cart"
                            @click="addToCart()"
                            :disabled="loading || maxQuantity <= 0"
                            x-show="maxQuantity > 0">
                        <span x-show="!loading">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </span>
                        <span x-show="loading">
                            <span class="loading-spinner me-2"></span>Adding...
                        </span>
                    </button>

                    <button class="btn btn-secondary btn-add-cart" disabled x-show="maxQuantity <= 0">
                        <i class="fas fa-ban me-2"></i>Out of Stock
                    </button>

                    <button class="btn-wishlist"
                            :class="{ 'active': inWishlist }"
                            @click="toggleWishlist()">
                        <i :class="inWishlist ? 'fas fa-heart' : 'far fa-heart'"></i>
                    </button>
                </div>

                <!-- Buy Now Button -->
                <div class="mb-3" x-show="maxQuantity > 0">
                    <button class="btn btn-warning w-100 py-3 fw-bold" @click="buyNow()" :disabled="loading">
                        <i class="fas fa-bolt me-2"></i>Buy Now
                    </button>
                </div>

                <!-- Product Features -->
                <div class="product-features">
                    <div class="feature-item">
                        <i class="fas fa-shipping-fast feature-icon"></i>
                        <span>Free shipping on orders over {{ format_currency(500000) }}</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-undo feature-icon"></i>
                        <span>30-day return policy</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <span>1 year warranty included</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-headset feature-icon"></i>
                        <span>24/7 customer support</span>
                    </div>
                </div>

                <!-- Share Buttons -->
                <div class="mt-4">
                    <label class="fw-bold mb-2">Share this product:</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" @click="shareProduct('facebook')">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm" @click="shareProduct('twitter')">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm" @click="shareProduct('whatsapp')">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="copyProductLink()">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="product-tabs">
        <ul class="nav nav-tabs nav-fill" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                        data-bs-target="#description" type="button" role="tab">
                    Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specifications-tab" data-bs-toggle="tab"
                        data-bs-target="#specifications" type="button" role="tab">
                    Specifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                        data-bs-target="#reviews" type="button" role="tab">
                    Reviews ({{ $product->rating_count }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab"
                        data-bs-target="#shipping" type="button" role="tab">
                    Shipping & Returns
                </button>
            </li>
        </ul>

        <div class="tab-content" id="productTabContent">
            <!-- Description Tab -->
            <div class="tab-pane fade show active" id="description" role="tabpanel">
                <div class="description-content">
                    {!! $product->description ?: '<p>No description available for this product.</p>' !!}

                    @if($product->short_description)
                    <h3 class="mt-4">Key Features</h3>
                    <p>{{ $product->short_description }}</p>
                    @endif
                </div>
            </div>

            <!-- Specifications Tab -->
            <div class="tab-pane fade" id="specifications" role="tabpanel">
                @if($product->attributes && $product->attributes->count() > 0)
                    @foreach($product->attributes as $attribute)
                    <div class="spec-row">
                        <div class="spec-label">{{ $attribute->attribute->name }}</div>
                        <div class="spec-value">
                            @if($attribute->attribute->type === 'boolean')
                                {{ $attribute->value_boolean ? 'Yes' : 'No' }}
                            @elseif($attribute->attribute->type === 'number')
                                {{ $attribute->value_number }}
                            @else
                                {{ $attribute->value_text }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Default specifications -->
                    <div class="spec-row">
                        <div class="spec-label">Brand</div>
                        <div class="spec-value">{{ $product->brand->name ?? 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Category</div>
                        <div class="spec-value">{{ $product->category->name ?? 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">SKU</div>
                        <div class="spec-value">{{ $product->sku }}</div>
                    </div>
                    @if($product->weight_grams > 0)
                    <div class="spec-row">
                        <div class="spec-label">Weight</div>
                        <div class="spec-value">{{ $product->weight_grams }}g</div>
                    </div>
                    @endif
                    @if($product->length_mm > 0 || $product->width_mm > 0 || $product->height_mm > 0)
                    <div class="spec-row">
                        <div class="spec-label">Dimensions</div>
                        <div class="spec-value">
                            {{ $product->length_mm }}mm × {{ $product->width_mm }}mm × {{ $product->height_mm }}mm
                        </div>
                    </div>
                    @endif
                @endif
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="reviews-section">
                    @if($product->rating_count > 0)
                    <!-- Review Summary -->
                    <div class="review-summary">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="overall-rating">{{ number_format($product->rating_average, 1) }}</div>
                                <div class="rating-stars mb-2 justify-content-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->rating_average))
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $product->rating_average)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-muted">Based on {{ $product->rating_count }} reviews</p>
                            </div>
                            <div class="col-md-8">
                                <div class="rating-breakdown">
                                    @for($rating = 5; $rating >= 1; $rating--)
                                    @php
                                        $ratingCount = $product->reviews->where('rating', $rating)->count();
                                        $percentage = $product->rating_count > 0 ? ($ratingCount / $product->rating_count) * 100 : 0;
                                    @endphp
                                    <div class="rating-bar">
                                        <span class="text-muted">{{ $rating }} stars</span>
                                        <div class="rating-bar-fill">
                                            <div class="rating-bar-progress" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-muted">{{ $ratingCount }}</span>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    @if($product->reviews && $product->reviews->count() > 0)
                    <div class="reviews-list">
                        @foreach($product->reviews->where('is_approved', true)->take(5) as $review)
                        <div class="review-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="reviewer-avatar me-3">
                                        {{ strtoupper(substr($review->user->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $review->user->first_name }} {{ substr($review->user->last_name, 0, 1) }}.</div>
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                            </div>

                            @if($review->title)
                            <h6 class="fw-bold mb-2">{{ $review->title }}</h6>
                            @endif

                            <div class="review-content mb-3">
                                {{ $review->review }}
                            </div>

                            @if($review->images)
                            <div class="review-images mb-3">
                                @foreach(json_decode($review->images, true) ?? [] as $image)
                                <div class="review-image" @click="openImageZoom('{{ $image }}')">
                                    <img src="{{ $image }}" alt="Review image" loading="lazy">
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <div class="review-actions">
                                <button class="btn btn-sm btn-outline-secondary me-2" @click="likeReview({{ $review->id }})">
                                    <i class="far fa-thumbs-up me-1"></i>Helpful ({{ $review->helpful_count }})
                                </button>
                                @if($review->is_verified)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Verified Purchase
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($product->reviews->where('is_approved', true)->count() > 5)
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary" @click="loadMoreReviews()">
                            Load More Reviews
                        </button>
                    </div>
                    @endif
                    @endif

                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fs-1 text-muted mb-3"></i>
                        <h4>No reviews yet</h4>
                        <p class="text-muted">Be the first to review this product!</p>
                    </div>
                    @endif

                    <!-- Write Review Button -->
                    @auth
                    <div class="text-center mt-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="fas fa-edit me-2"></i>Write a Review
                        </button>
                    </div>
                    @else
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            <a href="{{ route('login') }}" class="text-decoration-none">Login</a> to write a review
                        </p>
                    </div>
                    @endauth
                </div>
            </div>

            <!-- Shipping Tab -->
            <div class="tab-pane fade" id="shipping" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mb-3">Shipping Information</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-truck text-primary me-2"></i>
                                Free shipping on orders over {{ format_currency(500000) }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Standard delivery: 2-5 business days
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-rocket text-primary me-2"></i>
                                Express delivery: 1-2 business days (additional cost)
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                Nationwide delivery available
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3">Return Policy</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-undo text-success me-2"></i>
                                30-day return window
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-box text-success me-2"></i>
                                Items must be in original condition
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                Full refund or exchange available
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-shipping-fast text-success me-2"></i>
                                Free return shipping for defective items
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="related-products mt-5">
        <h3 class="fw-bold mb-4">Related Products</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-lg-3 col-md-4 col-sm-6">
                @include('components.product-card', ['product' => $relatedProduct])
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Image Zoom Overlay -->
    <div class="zoom-overlay" id="zoomOverlay" @click="closeImageZoom()">
        <div class="zoom-close" @click="closeImageZoom()">×</div>
        <img class="zoom-image" id="zoomImage" src="" alt="" loading="lazy">
    </div>
</div>

<!-- Review Modal -->
@auth
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form @submit.prevent="submitReview()">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Rating *</label>
                        <div class="rating-input">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="star-input far fa-star fa-2x me-1" data-rating="{{ $i }}"
                               @click="setReviewRating({{ $i }})"
                               :class="{ 'fas text-warning': reviewRating >= {{ $i }} }"></i>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Review Title</label>
                        <input type="text" class="form-control" x-model="reviewTitle"
                               placeholder="Summarize your review">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Your Review *</label>
                        <textarea class="form-control" rows="5" x-model="reviewText"
                                  placeholder="Share your experience with this product" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Add Photos (optional)</label>
                        <input type="file" class="form-control" multiple accept="image/*"
                               @change="handleReviewImages($event)">
                        <small class="text-muted">You can upload up to 5 photos</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" :disabled="reviewSubmitting">
                        <span x-show="!reviewSubmitting">Submit Review</span>
                        <span x-show="reviewSubmitting">
                            <span class="loading-spinner me-2"></span>Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth
@endsection

@push('scripts')
<script>
function productDetail() {
    return {
        // Product data
        productId: {{ $product->id }},
        productName: '{{ addslashes($product->name) }}',
        basePrice: {{ $product->price_cents }},
        baseStock: {{ $product->stock_quantity }},

        // UI state
        currentPrice: {{ $product->price_cents }},
        maxQuantity: {{ $product->stock_quantity }},
        selectedImageIndex: 0,
        selectedImage: '{{ $product->primary_image ?? asset("images/placeholder.jpg") }}',
        selectedVariant: null,
        quantity: 1,
        loading: false,
        inWishlist: false,
        imageLoaded: true,

        // Review state
        reviewRating: 0,
        reviewTitle: '',
        reviewText: '',
        reviewImages: [],
        reviewSubmitting: false,

        init() {
            @if($product->images && $product->images->count() > 0)
            this.selectedImage = '{{ $product->images->first()->image_url }}';
            @endif

            @auth
            this.checkWishlistStatus();
            @endauth

            this.trackProductView();
        },

        selectImage(index, imageUrl) {
            this.selectedImageIndex = index;
            this.selectedImage = imageUrl;
        },

        selectVariant(variantId, priceAdjustment, stock) {
            this.selectedVariant = variantId;
            this.currentPrice = this.basePrice + priceAdjustment;
            this.maxQuantity = stock;
            if (this.quantity > this.maxQuantity) {
                this.quantity = Math.max(1, this.maxQuantity);
            }
        },

        increaseQuantity() {
            if (this.quantity < this.maxQuantity) {
                this.quantity++;
            }
        },

        decreaseQuantity() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },

        validateQuantity() {
            const num = parseInt(this.quantity) || 1;
            this.quantity = Math.max(1, Math.min(num, this.maxQuantity));
        },

        formatPrice(cents) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(cents / 100);
        },

        async addToCart() {
            if (this.loading || this.maxQuantity <= 0) return;
            this.loading = true;

            try {
                const response = await fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        variant_id: this.selectedVariant,
                        quantity: this.quantity
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Product added to cart!', 'success');
                    this.updateMiniCart();
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        async buyNow() {
            await this.addToCart();
            if (!this.loading) {
                @guest
                sessionStorage.setItem('intended_url', '{{ route("checkout.index") }}');
                window.location.href = '{{ route("login") }}';
                @else
                window.location.href = '{{ route("checkout.index") }}';
                @endguest
            }
        },

        async toggleWishlist() {
            @guest
            this.showLoginPrompt('Please login to add products to your wishlist.');
            return;
            @endguest

            @auth
            try {
                const response = await fetch('{{ route("wishlist.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: this.productId
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.inWishlist = data.added;
                    this.showNotification(
                        data.added ? 'Added to wishlist!' : 'Removed from wishlist',
                        data.added ? 'success' : 'info'
                    );
                } else {
                    throw new Error(data.message || 'Failed to update wishlist');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
            @endauth
        },

        async checkWishlistStatus() {
            @auth
            try {
                const response = await fetch(`{{ route("wishlist.check") }}?product_id=${this.productId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                this.inWishlist = data.in_wishlist || false;
            } catch (error) {
                console.error('Wishlist check failed:', error);
            }
            @endauth
        },

        showLoginPrompt(message) {
            if (confirm(message + '\n\nWould you like to login now?')) {
                window.location.href = '{{ route("login") }}';
            }
        },

        shareProduct(platform) {
            const url = window.location.href;
            const text = `Check out this amazing product: ${this.productName}`;

            const shareUrls = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
                twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`,
                whatsapp: `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
            };

            if (shareUrls[platform]) {
                window.open(shareUrls[platform], '_blank', 'width=600,height=400');
            }
        },

        copyProductLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showNotification('Product link copied to clipboard!', 'success');
            }).catch(() => {
                this.showNotification('Failed to copy link', 'error');
            });
        },

        openImageZoom(imageUrl = null) {
            const overlay = document.getElementById('zoomOverlay');
            const zoomImage = document.getElementById('zoomImage');
            zoomImage.src = imageUrl || this.selectedImage;
            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                overlay.classList.add('show');
            });
            document.body.style.overflow = 'hidden';
        },

        closeImageZoom() {
            const overlay = document.getElementById('zoomOverlay');
            overlay.classList.remove('show');
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 200);
            document.body.style.overflow = 'auto';
        },

        setReviewRating(rating) {
            this.reviewRating = rating;
        },

        handleReviewImages(event) {
            const files = Array.from(event.target.files);
            if (files.length > 5) {
                this.showNotification('Maximum 5 images allowed', 'warning');
                return;
            }
            this.reviewImages = files;
        },

        async submitReview() {
            @guest
            this.showLoginPrompt('Please login to write a review.');
            return;
            @endguest

            @auth
            if (this.reviewRating === 0 || !this.reviewText.trim()) {
                this.showNotification('Please provide a rating and review text', 'warning');
                return;
            }

            this.reviewSubmitting = true;

            try {
                const formData = new FormData();
                formData.append('product_id', this.productId);
                formData.append('rating', this.reviewRating);
                formData.append('title', this.reviewTitle);
                formData.append('review', this.reviewText);

                this.reviewImages.forEach((file, index) => {
                    formData.append(`images[${index}]`, file);
                });

                const response = await fetch('{{ route("reviews.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Review submitted successfully! It will be published after moderation.', 'success');
                    this.reviewRating = 0;
                    this.reviewTitle = '';
                    this.reviewText = '';
                    this.reviewImages = [];
                    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                    modal.hide();
                } else {
                    throw new Error(data.message || 'Failed to submit review');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.reviewSubmitting = false;
            }
            @endauth
        },

        async updateMiniCart() {
            try {
                const response = await fetch('{{ route("cart.mini") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = data.count || 0;
                });
            } catch (error) {
                console.error('Mini cart update error:', error);
            }
        },

        async trackProductView() {
            try {
                await fetch('{{ route("products.track-view") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: this.productId })
                });
            } catch (error) {
                console.error('Tracking failed:', error);
            }
        },

        showNotification(message, type = 'info') {
            const existing = document.querySelectorAll('.notification');
            existing.forEach(el => el.remove());

            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible position-fixed notification`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(notification);

            requestAnimationFrame(() => {
                notification.classList.add('show');
            });

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    @auth
    const intendedUrl = sessionStorage.getItem('intended_url');
    if (intendedUrl) {
        sessionStorage.removeItem('intended_url');
        setTimeout(() => {
            if (confirm('Login successful! Continue to checkout?')) {
                window.location.href = intendedUrl;
            }
        }, 1000);
    }
    @endauth

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const overlay = document.getElementById('zoomOverlay');
            if (overlay && overlay.style.display === 'flex') {
                overlay.classList.remove('show');
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 200);
                document.body.style.overflow = 'auto';
            }
        }
    });
});
</script>
@endpush
