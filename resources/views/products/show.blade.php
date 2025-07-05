@extends('layouts.app')

@section('title', $product->name . ' - TokoSaya')
@section('meta_description', Str::limit($product->description, 150))

@push('styles')
<style>
    .product-gallery {
        position: sticky;
        top: 20px;
    }

    .main-image {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
        cursor: zoom-in;
    }

    .main-image img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .main-image:hover img {
        transform: scale(1.05);
    }

    .image-thumbnails {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }

    .thumbnail {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.2s;
    }

    .thumbnail.active {
        border-color: #2563eb;
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info {
        padding-left: 2rem;
    }

    .product-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .rating-stars {
        display: flex;
        gap: 0.25rem;
    }

    .rating-text {
        color: #64748b;
        font-size: 0.9rem;
    }

    .product-price {
        margin-bottom: 1.5rem;
    }

    .current-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2563eb;
    }

    .original-price {
        font-size: 1.25rem;
        color: #94a3b8;
        text-decoration: line-through;
        margin-left: 0.5rem;
    }

    .discount-badge {
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-left: 1rem;
    }

    .stock-status {
        margin-bottom: 1.5rem;
    }

    .stock-available {
        color: #059669;
        font-weight: 600;
    }

    .stock-low {
        color: #d97706;
        font-weight: 600;
    }

    .stock-out {
        color: #dc2626;
        font-weight: 600;
    }

    .product-variants {
        margin-bottom: 2rem;
    }

    .variant-group {
        margin-bottom: 1.5rem;
    }

    .variant-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #374151;
    }

    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .variant-option {
        padding: 0.5rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }

    .variant-option.active {
        border-color: #2563eb;
        background: #dbeafe;
        color: #2563eb;
    }

    .variant-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f9fafb;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .quantity-input {
        display: flex;
        align-items: center;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .quantity-btn {
        background: #f8fafc;
        border: none;
        padding: 0.75rem;
        cursor: pointer;
        transition: background 0.2s;
        min-width: 45px;
    }

    .quantity-btn:hover {
        background: #e2e8f0;
    }

    .quantity-value {
        padding: 0.75rem 1rem;
        border: none;
        text-align: center;
        min-width: 60px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .btn-add-cart {
        flex: 1;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-wishlist {
        padding: 1rem;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 8px;
        color: #6b7280;
        transition: all 0.2s;
    }

    .btn-wishlist:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    .btn-wishlist.active {
        border-color: #ef4444;
        background: #ef4444;
        color: white;
    }

    .product-features {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .feature-item:last-child {
        margin-bottom: 0;
    }

    .feature-icon {
        width: 20px;
        color: #2563eb;
    }

    .product-tabs {
        margin-top: 3rem;
    }

    .tab-content {
        padding: 2rem 0;
    }

    .description-content {
        line-height: 1.8;
        color: #374151;
    }

    .description-content h3 {
        color: #1e293b;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .description-content ul {
        padding-left: 1.5rem;
    }

    .description-content li {
        margin-bottom: 0.5rem;
    }

    .specifications-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .spec-row {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
    }

    .spec-row:last-child {
        border-bottom: none;
    }

    .spec-label {
        background: #f8fafc;
        padding: 1rem;
        font-weight: 600;
        min-width: 200px;
        color: #374151;
    }

    .spec-value {
        padding: 1rem;
        flex: 1;
        color: #1e293b;
    }

    .reviews-section {
        margin-top: 2rem;
    }

    .review-summary {
        background: #f8fafc;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .rating-overview {
        text-align: center;
        margin-bottom: 2rem;
    }

    .overall-rating {
        font-size: 3rem;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 0.5rem;
    }

    .rating-breakdown {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .rating-bar-fill {
        flex: 1;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .rating-bar-progress {
        height: 100%;
        background: #f59e0b;
        transition: width 0.3s ease;
    }

    .review-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.5rem 0;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .review-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .reviewer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .review-content {
        line-height: 1.6;
        color: #374151;
        margin-bottom: 1rem;
    }

    .review-images {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .review-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
    }

    .review-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .review-actions {
        display: flex;
        gap: 1rem;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .review-action {
        cursor: pointer;
        transition: color 0.2s;
    }

    .review-action:hover {
        color: #2563eb;
    }

    .related-products {
        margin-top: 4rem;
    }

    .zoom-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .zoom-image {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }

    .zoom-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .product-info {
            padding-left: 0;
            margin-top: 2rem;
        }

        .product-title {
            font-size: 1.5rem;
        }

        .current-price {
            font-size: 2rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-add-cart {
            order: 1;
        }

        .btn-wishlist {
            order: 2;
        }

        .main-image img {
            height: 300px;
        }

        .spec-row {
            flex-direction: column;
        }

        .spec-label {
            min-width: auto;
            border-bottom: 1px solid #e5e7eb;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5" x-data="productDetail()">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image" @click="openImageZoom()">
                    <img :src="selectedImage" :alt="productName" id="mainImage">

                    @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="discount-badge">
                            -{{ number_format((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100) }}%
                        </span>
                    </div>
                    @endif

                    @if($product->stock_quantity <= 0)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-danger">Out of Stock</span>
                    </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($product->images && $product->images->count() > 1)
                <div class="image-thumbnails">
                    @foreach($product->images as $index => $image)
                    <div class="thumbnail" :class="{ 'active': selectedImageIndex === {{ $index }} }"
                         @click="selectImage({{ $index }}, '{{ $image->image_url }}')">
                        <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? $product->name }}">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-info">
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
                <div class="product-rating">
                    <div class="rating-stars">
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
                    <span class="rating-text">
                        {{ number_format($product->rating_average, 1) }} ({{ $product->rating_count }} reviews)
                    </span>
                    <a href="#reviews" class="text-decoration-none ms-2">See all reviews</a>
                </div>

                <!-- Price -->
                <div class="product-price">
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
                <div class="stock-status">
                    @if($product->stock_quantity > 10)
                        <span class="stock-available">
                            <i class="fas fa-check-circle me-2"></i>In Stock ({{ $product->stock_quantity }} available)
                        </span>
                    @elseif($product->stock_quantity > 0)
                        <span class="stock-low">
                            <i class="fas fa-exclamation-triangle me-2"></i>Only {{ $product->stock_quantity }} left in stock
                        </span>
                    @else
                        <span class="stock-out">
                            <i class="fas fa-times-circle me-2"></i>Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Variants -->
                @if($product->variants && $product->variants->count() > 0)
                <div class="product-variants">
                    @php
                        $variantGroups = $product->variants->groupBy('variant_name');
                    @endphp

                    @foreach($variantGroups as $variantName => $variants)
                    <div class="variant-group">
                        <div class="variant-title">{{ $variantName }}:</div>
                        <div class="variant-options">
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
                                @if($variant->stock_quantity <= 0)
                                    <small class="text-danger ms-1">(Out of Stock)</small>
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
                    <label class="form-label fw-bold mb-0">Quantity:</label>
                    <div class="quantity-input">
                        <button class="quantity-btn" @click="decreaseQuantity()" :disabled="quantity <= 1">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-value" x-model="quantity"
                               min="1" :max="maxQuantity" @change="validateQuantity()">
                        <button class="quantity-btn" @click="increaseQuantity()" :disabled="quantity >= maxQuantity">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <span class="text-muted ms-2" x-show="maxQuantity > 0">
                        Max: <span x-text="maxQuantity"></span>
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    @if($product->stock_quantity > 0)
                    <button class="btn btn-primary btn-add-cart"
                            @click="addToCart()"
                            :disabled="loading || maxQuantity <= 0">
                        <span x-show="loading">
                            <i class="fas fa-spinner fa-spin me-2"></i>Adding...
                        </span>
                    </button>
                    @else
                    <button class="btn btn-secondary btn-add-cart" disabled>
                        <i class="fas fa-ban me-2"></i>Out of Stock
                    </button>
                    @endif

                    <button class="btn-wishlist"
                            :class="{ 'active': inWishlist }"
                            @click="toggleWishlist()"
                            title="Add to Wishlist">
                        <i :class="inWishlist ? 'fas fa-heart' : 'far fa-heart'"></i>
                    </button>
                </div>

                <!-- Buy Now Button -->
                @if($product->stock_quantity > 0)
                <div class="mb-3">
                    <button class="btn btn-warning w-100 py-3 fw-bold" @click="buyNow()">
                        <i class="fas fa-bolt me-2"></i>Buy Now
                    </button>
                </div>
                @endif

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
                <div class="product-share mt-3">
                    <label class="form-label fw-bold">Share this product:</label>
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
                    <h3>Key Features</h3>
                    <p>{{ $product->short_description }}</p>
                    @endif
                </div>
            </div>

            <!-- Specifications Tab -->
            <div class="tab-pane fade" id="specifications" role="tabpanel">
                <div class="specifications-table">
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
                                {{ $product->length_mm }}mm x {{ $product->width_mm }}mm x {{ $product->height_mm }}mm
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="reviews-section">
                    @if($product->rating_count > 0)
                    <!-- Review Summary -->
                    <div class="review-summary">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="rating-overview">
                                    <div class="overall-rating">{{ number_format($product->rating_average, 1) }}</div>
                                    <div class="rating-stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product->rating_average))
                                                <i class="fas fa-star text-warning fs-5"></i>
                                            @elseif($i - 0.5 <= $product->rating_average)
                                                <i class="fas fa-star-half-alt text-warning fs-5"></i>
                                            @else
                                                <i class="far fa-star text-muted fs-5"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-muted">Based on {{ $product->rating_count }} reviews</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="rating-breakdown">
                                    @for($rating = 5; $rating >= 1; $rating--)
                                    @php
                                        $ratingCount = $product->reviews->where('rating', $rating)->count();
                                        $percentage = $product->rating_count > 0 ? ($ratingCount / $product->rating_count) * 100 : 0;
                                    @endphp
                                    <div class="rating-bar">
                                        <span class="rating-label">{{ $rating }} stars</span>
                                        <div class="rating-bar-fill">
                                            <div class="rating-bar-progress" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="rating-count">{{ $ratingCount }}</span>
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
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
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
                                <div class="review-date text-muted">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                            </div>

                            @if($review->title)
                            <h6 class="review-title fw-bold mb-2">{{ $review->title }}</h6>
                            @endif

                            <div class="review-content">
                                {{ $review->review }}
                            </div>

                            @if($review->images)
                            <div class="review-images">
                                @foreach(json_decode($review->images, true) ?? [] as $image)
                                <div class="review-image" @click="openImageZoom('{{ $image }}')">
                                    <img src="{{ $image }}" alt="Review image">
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <div class="review-actions">
                                <span class="review-action" @click="likeReview({{ $review->id }})">
                                    <i class="far fa-thumbs-up me-1"></i>Helpful ({{ $review->helpful_count }})
                                </span>
                                @if($review->is_verified)
                                <span class="text-success">
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
                            <a href="{{ route('login') }}">Login</a> to write a review
                        </p>
                    </div>
                    @endauth
                </div>
            </div>

            <!-- Shipping Tab -->
            <div class="tab-pane fade" id="shipping" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Shipping Information</h4>
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
                        <h4>Return Policy</h4>
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
    <div class="related-products">
        <h3 class="fw-bold mb-4">Related Products</h3>
        <div class="row">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                @include('components.product-card', ['product' => $relatedProduct])
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Image Zoom Overlay -->
    <div class="zoom-overlay" id="zoomOverlay" @click="closeImageZoom()">
        <div class="zoom-close" @click="closeImageZoom()">×</div>
        <img class="zoom-image" id="zoomImage" src="" alt="">
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
                    <div class="mb-3">
                        <label class="form-label">Rating *</label>
                        <div class="rating-input">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="star-input far fa-star" data-rating="{{ $i }}"
                               @click="setReviewRating({{ $i }})"
                               :class="{ 'fas text-warning': reviewRating >= {{ $i }} }"></i>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Review Title</label>
                        <input type="text" class="form-control" x-model="reviewTitle"
                               placeholder="Summarize your review">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Review *</label>
                        <textarea class="form-control" rows="4" x-model="reviewText"
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
                            <i class="fas fa-spinner fa-spin me-2"></i>Submitting...
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
        productName: '{{ $product->name }}',
        basePrice: {{ $product->price_cents }},
        currentPrice: {{ $product->price_cents }},
        baseStock: {{ $product->stock_quantity }},
        maxQuantity: {{ $product->stock_quantity }},

        // UI state
        selectedImageIndex: 0,
        selectedImage: '{{ $product->primary_image ?? asset("images/placeholder.jpg") }}',
        selectedVariant: null,
        quantity: 1,
        loading: false,
        inWishlist: false,

        // Review state
        reviewRating: 0,
        reviewTitle: '',
        reviewText: '',
        reviewImages: [],
        reviewSubmitting: false,

        init() {
            // Initialize with first image
            @if($product->images && $product->images->count() > 0)
            this.selectedImage = '{{ $product->images->first()->image_url }}';
            @endif

            // Check if in wishlist
            this.checkWishlistStatus();
        },

        selectImage(index, imageUrl) {
            this.selectedImageIndex = index;
            this.selectedImage = imageUrl;
        },

        selectVariant(variantId, priceAdjustment, stock) {
            this.selectedVariant = variantId;
            this.currentPrice = this.basePrice + priceAdjustment;
            this.maxQuantity = stock;

            // Adjust quantity if it exceeds new max
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
            if (this.quantity < 1) {
                this.quantity = 1;
            } else if (this.quantity > this.maxQuantity) {
                this.quantity = this.maxQuantity;
            }
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
            product_id: {{ $product->id }}
        })
    }).catch(error => {
        console.error('View tracking error:', error);
    });
});

// Handle browser back button
window.addEventListener('popstate', function(e) {
    // Handle any state changes if needed
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // ESC to close zoom overlay
    if (e.key === 'Escape') {
        const overlay = document.getElementById('zoomOverlay');
        if (overlay.style.display === 'flex') {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
});
</script>
@endpushapplication/json'
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
                window.location.href = '{{ route("checkout.index") }}';
            }
        },

        async toggleWishlist() {
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
        },

        async checkWishlistStatus() {
            try {
                const response = await fetch(`{{ route("wishlist.check") }}?product_id=${this.productId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                this.inWishlist = data.in_wishlist || false;
            } catch (error) {
                // Ignore error - user might not be logged in
            }
        },

        shareProduct(platform) {
            const url = window.location.href;
            const text = `Check out this amazing product: ${this.productName}`;

            let shareUrl = '';

            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
                    break;
            }

            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        },

        copyProductLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showNotification('Product link copied to clipboard!', 'success');
            });
        },

        openImageZoom(imageUrl = null) {
            const overlay = document.getElementById('zoomOverlay');
            const zoomImage = document.getElementById('zoomImage');

            zoomImage.src = imageUrl || this.selectedImage;
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        },

        closeImageZoom() {
            const overlay = document.getElementById('zoomOverlay');
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        },

        // Review functions
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

                    // Reset form
                    this.reviewRating = 0;
                    this.reviewTitle = '';
                    this.reviewText = '';
                    this.reviewImages = [];

                    // Close modal
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

                const miniCartContainer = document.getElementById('mini-cart-container');
                if (miniCartContainer && data.html) {
                    miniCartContainer.innerHTML = data.html;
                }

                // Update cart count
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = data.count || 0;
                });
            } catch (error) {
                console.error('Mini cart update error:', error);
            }
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    };
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // Track product view
    fetch('{{ route("products.track-view") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: {{ $product->id }}
        })
    }).catch(error => {
        console.error('View tracking error:', error);
    });
});
</script>
@endpush
