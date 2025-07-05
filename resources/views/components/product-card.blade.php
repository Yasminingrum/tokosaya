@php
    $theme = $theme ?? 'light';
    $showWishlist = $showWishlist ?? true;
    $showQuickView = $showQuickView ?? true;
    $showAddToCart = $showAddToCart ?? true;
    $cardClass = $theme === 'dark' ? 'product-card-dark' : 'product-card';
@endphp

<div class="product-card {{ $cardClass }}" x-data="productCard({{ $product->id }})">
    <div class="product-image-container">
        <!-- Product Image -->
        <div class="product-image">
            @if($product->images->count() > 0)
                <img src="{{ $product->images->first()->image_url }}"
                     alt="{{ $product->name }}"
                     class="img-fluid product-main-image"
                     loading="lazy">

                @if($product->images->count() > 1)
                    <img src="{{ $product->images->skip(1)->first()->image_url }}"
                         alt="{{ $product->name }}"
                         class="img-fluid product-hover-image"
                         loading="lazy">
                @endif
            @else
                <div class="product-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            @endif
        </div>

        <!-- Product Badges -->
        <div class="product-badges">
            @if($product->featured)
                <span class="badge badge-featured">
                    <i class="fas fa-star me-1"></i>Unggulan
                </span>
            @endif

            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                @php
                    $discount = round((($product->compare_price_cents - $product->price_cents) / $product->compare_price_cents) * 100);
                @endphp
                <span class="badge badge-discount">
                    -{{ $discount }}%
                </span>
            @endif

            @if($product->created_at->diffInDays() <= 7)
                <span class="badge badge-new">Baru</span>
            @endif

            @if($product->stock_quantity <= $product->min_stock_level && $product->stock_quantity > 0)
                <span class="badge badge-limited">Stok Terbatas</span>
            @endif

            @if($product->stock_quantity == 0)
                <span class="badge badge-sold-out">Habis</span>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="product-actions">
            @if($showWishlist)
                <button class="btn-action btn-wishlist"
                        @click="toggleWishlist()"
                        :class="{ 'active': isInWishlist }"
                        title="Tambah ke Wishlist">
                    <i class="fas fa-heart"></i>
                </button>
            @endif

            @if($showQuickView)
                <button class="btn-action btn-quick-view"
                        @click="quickView()"
                        title="Lihat Cepat">
                    <i class="fas fa-eye"></i>
                </button>
            @endif

            <button class="btn-action btn-compare"
                    @click="addToCompare()"
                    title="Bandingkan">
                <i class="fas fa-balance-scale"></i>
            </button>
        </div>

        <!-- Stock Indicator -->
        @if($product->track_stock && $product->stock_quantity > 0)
            <div class="stock-indicator">
                @php
                    $stockPercentage = min(100, ($product->stock_quantity / max($product->max_stock_level, 1)) * 100);
                @endphp
                <div class="stock-bar">
                    <div class="stock-fill" style="width: {{ $stockPercentage }}%"></div>
                </div>
                <small class="stock-text">Tersisa {{ $product->stock_quantity }} pcs</small>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <!-- Category -->
        <div class="product-category">
            <a href="{{ route('categories.show', $product->category->slug) }}" class="category-link">
                {{ $product->category->name }}
            </a>
        </div>

        <!-- Product Name -->
        <h5 class="product-name">
            <a href="{{ route('products.show', $product->slug) }}" class="product-link">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h5>

        <!-- Brand -->
        @if($product->brand)
            <div class="product-brand">
                <a href="{{ route('brands.show', $product->brand->slug) }}" class="brand-link">
                    {{ $product->brand->name }}
                </a>
            </div>
        @endif

        <!-- Product Rating -->
        @if($product->rating_count > 0)
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
                    ({{ number_format($product->rating_count) }})
                </span>
            </div>
        @endif

        <!-- Product Description -->
        @if($product->short_description)
            <p class="product-description">
                {{ Str::limit($product->short_description, 80) }}
            </p>
        @endif

        <!-- Product Variants Preview -->
        @if($product->variants->count() > 0)
            <div class="product-variants">
                <small class="variants-label">Varian:</small>
                <div class="variants-preview">
                    @foreach($product->variants->take(3) as $variant)
                        <span class="variant-item" title="{{ $variant->variant_name }}: {{ $variant->variant_value }}">
                            {{ $variant->variant_value }}
                        </span>
                    @endforeach
                    @if($product->variants->count() > 3)
                        <span class="variant-more">+{{ $product->variants->count() - 3 }}</span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Product Price -->
        <div class="product-price">
            @if($product->compare_price_cents && $product->compare_price_cents > $product->price_cents)
                <span class="price-original">{{ format_currency($product->compare_price_cents) }}</span>
            @endif
            <span class="price-current">{{ format_currency($product->price_cents) }}</span>

            @if($product->variants->count() > 0)
                @php
                    $minVariantPrice = $product->variants->min('price_adjustment_cents');
                    $maxVariantPrice = $product->variants->max('price_adjustment_cents');
                @endphp
                @if($minVariantPrice != $maxVariantPrice)
                    <small class="price-range">
                        {{ format_currency($product->price_cents + $minVariantPrice) }} -
                        {{ format_currency($product->price_cents + $maxVariantPrice) }}
                    </small>
                @endif
            @endif
        </div>

        <!-- Product Actions -->
        <div class="product-card-actions">
            @if($product->stock_quantity > 0)
                @if($showAddToCart)
                    @if($product->variants->count() > 0)
                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-add-to-cart">
                            <i class="fas fa-eye me-2"></i>
                            Pilih Varian
                        </a>
                    @else
                        <button class="btn btn-primary btn-add-to-cart"
                                @click="addToCart()"
                                :disabled="addingToCart">
                            <i class="fas fa-shopping-cart me-2" x-show="!addingToCart"></i>
                            <i class="fas fa-spinner fa-spin me-2" x-show="addingToCart"></i>
                            <span x-text="addingToCart ? 'Menambah...' : 'Tambah ke Keranjang'"></span>
                        </button>
                    @endif
                @endif

                <button class="btn btn-outline-primary btn-buy-now"
                        @click="buyNow()">
                    <i class="fas fa-bolt me-2"></i>
                    Beli Sekarang
                </button>
            @else
                <button class="btn btn-secondary btn-out-of-stock" disabled>
                    <i class="fas fa-times me-2"></i>
                    Stok Habis
                </button>

                <button class="btn btn-outline-primary btn-notify"
                        @click="notifyWhenAvailable()">
                    <i class="fas fa-bell me-2"></i>
                    Beritahu Saya
                </button>
            @endif
        </div>

        <!-- Shipping Info -->
        <div class="shipping-info">
            <small class="text-muted">
                <i class="fas fa-truck me-1"></i>
                @if($product->weight_grams > 0)
                    Berat: {{ number_format($product->weight_grams / 1000, 1) }} kg
                @endif
                @if($product->digital)
                    <span class="text-success">
                        <i class="fas fa-download me-1"></i>Digital Download
                    </span>
                @else
                    Gratis ongkir min. {{ format_currency(10000000) }}
                @endif
            </small>
        </div>

        <!-- Social Proof -->
        @if($product->sale_count > 0)
            <div class="social-proof">
                <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ number_format($product->sale_count) }} terjual
                </small>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Product Card Base Styles */
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .product-card-dark {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .product-card-dark .product-info {
        color: white;
    }

    .product-card-dark .product-link,
    .product-card-dark .category-link,
    .product-card-dark .brand-link {
        color: white;
    }

    /* Product Image Container */
    .product-image-container {
        position: relative;
        overflow: hidden;
    }

    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #f8fafc;
    }

    .product-main-image,
    .product-hover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .product-hover-image {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
    }

    .product-card:hover .product-hover-image {
        opacity: 1;
    }

    .product-card:hover .product-main-image {
        transform: scale(1.05);
    }

    .product-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #94a3b8;
        font-size: 2rem;
    }

    /* Product Badges */
    .product-badges {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-featured {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .badge-discount {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .badge-new {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .badge-limited {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
    }

    .badge-sold-out {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    /* Quick Actions */
    .product-actions {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 8px;
        opacity: 0;
        transform: translateX(10px);
        transition: all 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateX(0);
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-action:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .btn-wishlist.active {
        background: #ef4444;
        color: white;
    }

    /* Stock Indicator */
    .stock-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 8px 12px;
        font-size: 0.75rem;
    }

    .stock-bar {
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .stock-fill {
        height: 100%;
        background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
        transition: width 0.3s ease;
    }

    .stock-text {
        font-size: 0.7rem;
        opacity: 0.9;
    }

    /* Product Info */
    .product-info {
        padding: 16px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        margin-bottom: 6px;
    }

    .category-link {
        font-size: 0.75rem;
        color: #64748b;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    .category-link:hover {
        color: var(--primary-color);
    }

    .product-name {
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 8px;
        flex-grow: 1;
    }

    .product-link {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .product-link:hover {
        color: var(--primary-color);
    }

    .product-brand {
        margin-bottom: 8px;
    }

    .brand-link {
        font-size: 0.8rem;
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
    }

    .brand-link:hover {
        color: var(--primary-color);
    }

    /* Product Rating */
    .product-rating {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .rating-stars {
        display: flex;
        gap: 2px;
    }

    .rating-stars i {
        font-size: 0.8rem;
    }

    .rating-text {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Product Description */
    .product-description {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    /* Product Variants */
    .product-variants {
        margin-bottom: 12px;
    }

    .variants-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
    }

    .variants-preview {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .variant-item {
        font-size: 0.7rem;
        background: #f1f5f9;
        color: #475569;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 500;
    }

    .variant-more {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Product Price */
    .product-price {
        margin-bottom: 16px;
    }

    .price-original {
        font-size: 0.8rem;
        color: #94a3b8;
        text-decoration: line-through;
        margin-right: 8px;
    }

    .price-current {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
        font-family: var(--font-display);
    }

    .price-range {
        display: block;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* Product Actions */
    .product-card-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .btn-add-to-cart,
    .btn-buy-now,
    .btn-out-of-stock,
    .btn-notify {
        font-size: 0.8rem;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-add-to-cart {
        background: var(--primary-color);
        color: white;
    }

    .btn-add-to-cart:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-buy-now {
        background: transparent;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }

    .btn-buy-now:hover {
        background: var(--primary-color);
        color: white;
    }

    .btn-out-of-stock {
        background: #94a3b8;
        color: white;
        cursor: not-allowed;
    }

    .btn-notify {
        background: transparent;
        color: #64748b;
        border: 1px solid #d1d5db;
    }

    .btn-notify:hover {
        background: #f8fafc;
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Shipping Info */
    .shipping-info {
        margin-bottom: 8px;
    }

    .shipping-info small {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
    }

    /* Social Proof */
    .social-proof small {
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .product-image {
            height: 160px;
        }

        .product-info {
            padding: 12px;
        }

        .product-name {
            font-size: 0.9rem;
        }

        .price-current {
            font-size: 1rem;
        }

        .product-actions {
            opacity: 1;
            transform: translateX(0);
        }

        .btn-action {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
    }

    /* Loading states */
    .btn-add-to-cart:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Animation enhancements */
    .product-card {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function productCard(productId) {
        return {
            productId: productId,
            isInWishlist: false,
            addingToCart: false,

            init() {
                this.checkWishlistStatus();
            },

            async checkWishlistStatus() {
                if (!window.Alpine.store('auth').isAuthenticated) return;

                try {
                    const response = await fetch(`/api/wishlist/check/${this.productId}`);
                    const data = await response.json();
                    this.isInWishlist = data.isInWishlist;
                } catch (error) {
                    console.error('Error checking wishlist status:', error);
                }
            },

            async toggleWishlist() {
                if (!window.Alpine.store('auth').isAuthenticated) {
                    window.location.href = '/login';
                    return;
                }

                try {
                    const response = await window.ajaxRequest('/wishlist/toggle', {
                        method: 'POST',
                        body: JSON.stringify({ product_id: this.productId })
                    });

                    this.isInWishlist = response.added;

                    // Update global wishlist count
                    Alpine.store('wishlist').updateCount(response.wishlist_count);

                    const message = response.added ?
                        'Produk ditambahkan ke wishlist!' :
                        'Produk dihapus dari wishlist!';
                    window.showNotification(message, 'success');
                } catch (error) {
                    window.showNotification('Gagal memperbarui wishlist: ' + error.message, 'error');
                }
            },

            async addToCart() {
                this.addingToCart = true;

                try {
                    const response = await window.addToCart(this.productId, 1);
                    window.showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                } catch (error) {
                    window.showNotification('Gagal menambahkan ke keranjang: ' + error.message, 'error');
                } finally {
                    this.addingToCart = false;
                }
            },

            quickView() {
                // Open quick view modal
                this.openQuickViewModal();
            },

            async openQuickViewModal() {
                try {
                    const response = await fetch(`/products/${this.productId}/quick-view`);
                    const html = await response.text();

                    // Create and show modal
                    const modal = document.createElement('div');
                    modal.innerHTML = html;
                    document.body.appendChild(modal);

                    // Initialize modal
                    const bootstrapModal = new bootstrap.Modal(modal.querySelector('.modal'));
                    bootstrapModal.show();

                    // Clean up on close
                    modal.addEventListener('hidden.bs.modal', () => {
                        modal.remove();
                    });
                } catch (error) {
                    console.error('Error opening quick view:', error);
                    window.showNotification('Gagal membuka preview produk', 'error');
                }
            },

            addToCompare() {
                // Add to comparison list
                const compareList = JSON.parse(localStorage.getItem('compareList') || '[]');

                if (compareList.includes(this.productId)) {
                    window.showNotification('Produk sudah ada dalam daftar perbandingan', 'warning');
                    return;
                }

                if (compareList.length >= 4) {
                    window.showNotification('Maksimal 4 produk dapat dibandingkan', 'warning');
                    return;
                }

                compareList.push(this.productId);
                localStorage.setItem('compareList', JSON.stringify(compareList));

                window.showNotification('Produk ditambahkan ke daftar perbandingan', 'success');

                // Trigger compare list update event
                window.dispatchEvent(new CustomEvent('compareListUpdated', {
                    detail: { count: compareList.length }
                }));
            },

            buyNow() {
                // Add to cart and redirect to checkout
                this.addToCart().then(() => {
                    window.location.href = '/checkout';
                });
            },

            async notifyWhenAvailable() {
                if (!window.Alpine.store('auth').isAuthenticated) {
                    window.location.href = '/login';
                    return;
                }

                try {
                    const response = await window.ajaxRequest('/products/notify-when-available', {
                        method: 'POST',
                        body: JSON.stringify({ product_id: this.productId })
                    });

                    window.showNotification('Kami akan memberitahu Anda ketika produk tersedia kembali!', 'success');
                } catch (error) {
                    window.showNotification('Gagal mendaftarkan notifikasi: ' + error.message, 'error');
                }
            }
        }
    }
</script>
@endpush
