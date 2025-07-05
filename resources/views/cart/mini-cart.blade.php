@php
    $cart = app('App\Services\CartService');
    $cartItems = $cart->getCartItems();
    $cartTotal = $cart->getCartTotal();
    $cartCount = $cart->getCartItemCount();
@endphp

<div class="mini-cart-dropdown" x-data="miniCart()">
    <!-- Cart Header -->
    <div class="mini-cart-header">
        <h6 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>
            Keranjang Belanja
        </h6>
        <span class="cart-count-badge" x-text="cartCount">{{ $cartCount }}</span>
    </div>

    <!-- Cart Items -->
    <div class="mini-cart-body">
        <template x-if="cartItems.length === 0">
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p class="empty-cart-text">Keranjang Anda kosong</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                    Mulai Belanja
                </a>
            </div>
        </template>

        <template x-if="cartItems.length > 0">
            <div class="cart-items-list">
                <template x-for="item in cartItems" :key="item.id">
                    <div class="mini-cart-item">
                        <div class="item-image">
                            <img :src="item.product.image" :alt="item.product.name" class="img-fluid">
                        </div>

                        <div class="item-details">
                            <h6 class="item-name" x-text="item.product.name"></h6>

                            <template x-if="item.variant">
                                <small class="item-variant text-muted" x-text="item.variant.name + ': ' + item.variant.value"></small>
                            </template>

                            <div class="item-price">
                                <span class="price" x-text="formatCurrency(item.unit_price_cents)"></span>
                            </div>
                        </div>

                        <div class="item-quantity">
                            <div class="quantity-controls">
                                <button class="btn-quantity btn-decrease"
                                        @click="updateQuantity(item.id, item.quantity - 1)"
                                        :disabled="item.quantity <= 1">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value" x-text="item.quantity"></span>
                                <button class="btn-quantity btn-increase"
                                        @click="updateQuantity(item.id, item.quantity + 1)"
                                        :disabled="item.quantity >= item.product.stock_quantity">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="item-actions">
                            <button class="btn-remove"
                                    @click="removeItem(item.id)"
                                    title="Hapus dari keranjang">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Loading State -->
        <template x-if="loading">
            <div class="mini-cart-loading">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p>Memperbarui keranjang...</p>
            </div>
        </template>
    </div>

    <!-- Cart Footer -->
    <template x-if="cartItems.length > 0">
        <div class="mini-cart-footer">
            <!-- Subtotal -->
            <div class="cart-subtotal">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Subtotal:</strong>
                    <strong class="text-primary" x-text="formatCurrency(cartTotal)"></strong>
                </div>
                <small class="text-muted">Belum termasuk ongkos kirim</small>
            </div>

            <!-- Quick Actions -->
            <div class="cart-actions">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Lihat Keranjang
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-credit-card me-1"></i>
                            Checkout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recently Viewed / Recommendations -->
            <template x-if="recommendedProducts.length > 0">
                <div class="cart-recommendations">
                    <h6 class="recommendations-title">Anda mungkin suka:</h6>
                    <div class="recommendations-list">
                        <template x-for="product in recommendedProducts.slice(0, 2)" :key="product.id">
                            <div class="recommendation-item">
                                <div class="recommendation-image">
                                    <img :src="product.image" :alt="product.name" class="img-fluid">
                                </div>
                                <div class="recommendation-details">
                                    <small class="recommendation-name" x-text="product.name"></small>
                                    <div class="recommendation-price" x-text="formatCurrency(product.price_cents)"></div>
                                </div>
                                <button class="btn-add-recommendation"
                                        @click="addRecommendationToCart(product.id)"
                                        title="Tambah ke keranjang">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Shipping Calculator -->
            <div class="shipping-calculator">
                <div class="calculator-toggle" @click="showShippingCalculator = !showShippingCalculator">
                    <small class="text-muted">
                        <i class="fas fa-truck me-1"></i>
                        Cek ongkos kirim
                        <i class="fas fa-chevron-down ms-1" :class="{ 'rotate-180': showShippingCalculator }"></i>
                    </small>
                </div>

                <div class="calculator-form" x-show="showShippingCalculator" x-transition>
                    <div class="input-group input-group-sm mt-2">
                        <input type="text"
                               class="form-control"
                               placeholder="Kode pos tujuan"
                               x-model="shippingPostalCode"
                               maxlength="5">
                        <button class="btn btn-outline-secondary"
                                @click="calculateShipping()"
                                :disabled="shippingPostalCode.length !== 5">
                            Cek
                        </button>
                    </div>

                    <template x-if="shippingOptions.length > 0">
                        <div class="shipping-options mt-2">
                            <template x-for="option in shippingOptions" :key="option.id">
                                <div class="shipping-option">
                                    <small class="option-name" x-text="option.name"></small>
                                    <small class="option-price" x-text="formatCurrency(option.cost)"></small>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>

@push('styles')
<style>
    /* Mini Cart Dropdown */
    .mini-cart-dropdown {
        width: 350px;
        max-height: 500px;
        display: flex;
        flex-direction: column;
        font-size: 0.9rem;
    }

    /* Cart Header */
    .mini-cart-header {
        padding: 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mini-cart-header h6 {
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .cart-count-badge {
        background: var(--primary-color);
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Cart Body */
    .mini-cart-body {
        flex-grow: 1;
        overflow-y: auto;
        max-height: 300px;
    }

    /* Empty Cart */
    .empty-cart {
        padding: 2rem 1rem;
        text-align: center;
    }

    .empty-cart-icon {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-cart-text {
        color: #64748b;
        margin-bottom: 1rem;
    }

    /* Cart Items */
    .cart-items-list {
        padding: 0.5rem 0;
    }

    .mini-cart-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        gap: 0.75rem;
        transition: background-color 0.2s ease;
    }

    .mini-cart-item:hover {
        background-color: #f8fafc;
    }

    .mini-cart-item:last-child {
        border-bottom: none;
    }

    /* Item Image */
    .item-image {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        background: #f1f5f9;
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Item Details */
    .item-details {
        flex-grow: 1;
        min-width: 0;
    }

    .item-name {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        color: #1e293b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-variant {
        display: block;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .item-price .price {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.85rem;
    }

    /* Item Quantity */
    .item-quantity {
        flex-shrink: 0;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: #f8fafc;
        border-radius: 6px;
        padding: 2px;
    }

    .btn-quantity {
        width: 24px;
        height: 24px;
        border: none;
        background: transparent;
        color: #64748b;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-quantity:hover:not(:disabled) {
        background: var(--primary-color);
        color: white;
    }

    .btn-quantity:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-value {
        min-width: 20px;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #1e293b;
    }

    /* Item Actions */
    .item-actions {
        flex-shrink: 0;
    }

    .btn-remove {
        width: 24px;
        height: 24px;
        border: none;
        background: transparent;
        color: #94a3b8;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-remove:hover {
        background: #fef2f2;
        color: #ef4444;
    }

    /* Loading State */
    .mini-cart-loading {
        padding: 2rem 1rem;
        text-align: center;
    }

    .loading-spinner {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    /* Cart Footer */
    .mini-cart-footer {
        padding: 1rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .cart-subtotal {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .cart-subtotal strong {
        font-size: 0.95rem;
    }

    .cart-subtotal small {
        font-size: 0.75rem;
    }

    /* Cart Actions */
    .cart-actions {
        margin-bottom: 1rem;
    }

    .cart-actions .btn {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
    }

    /* Recommendations */
    .cart-recommendations {
        margin-bottom: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
    }

    .recommendations-title {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #475569;
    }

    .recommendations-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .recommendation-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        background: white;
        border-radius: 6px;
        border: 1px solid #f1f5f9;
    }

    .recommendation-image {
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 4px;
        overflow: hidden;
    }

    .recommendation-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .recommendation-details {
        flex-grow: 1;
        min-width: 0;
    }

    .recommendation-name {
        display: block;
        font-size: 0.75rem;
        line-height: 1.2;
        margin-bottom: 0.125rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .recommendation-price {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .btn-add-recommendation {
        width: 20px;
        height: 20px;
        border: none;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-add-recommendation:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Shipping Calculator */
    .shipping-calculator {
        padding-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
    }

    .calculator-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .calculator-toggle:hover {
        color: var(--primary-color);
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .calculator-form .input-group-sm .form-control {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    .calculator-form .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .shipping-options {
        background: white;
        border-radius: 4px;
        border: 1px solid #f1f5f9;
    }

    .shipping-option {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.75rem;
    }

    .shipping-option:last-child {
        border-bottom: none;
    }

    .option-name {
        color: #64748b;
    }

    .option-price {
        font-weight: 600;
        color: var(--primary-color);
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .mini-cart-dropdown {
            width: 300px;
        }

        .mini-cart-item {
            padding: 0.5rem 0.75rem;
            gap: 0.5rem;
        }

        .item-image {
            width: 40px;
            height: 40px;
        }

        .item-name {
            font-size: 0.8rem;
        }

        .mini-cart-footer {
            padding: 0.75rem;
        }
    }

    /* Animation enhancements */
    .mini-cart-item {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(20px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Scrollbar styling */
    .mini-cart-body::-webkit-scrollbar {
        width: 4px;
    }

    .mini-cart-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .mini-cart-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .mini-cart-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@push('scripts')
<script>
    function miniCart() {
        return {
            cartItems: @json($cartItems),
            cartTotal: {{ $cartTotal }},
            cartCount: {{ $cartCount }},
            loading: false,
            recommendedProducts: [],
            showShippingCalculator: false,
            shippingPostalCode: '',
            shippingOptions: [],

            init() {
                this.loadRecommendations();

                // Listen for cart updates
                window.addEventListener('cartUpdated', (event) => {
                    this.refreshCart();
                });

                // Listen for global cart store updates
                this.$watch('$store.cart.count', (value) => {
                    this.cartCount = value;
                });

                this.$watch('$store.cart.total', (value) => {
                    this.cartTotal = value;
                });
            },

            async refreshCart() {
                try {
                    const response = await fetch('/api/cart');
                    const data = await response.json();

                    this.cartItems = data.items;
                    this.cartTotal = data.total;
                    this.cartCount = data.count;
                } catch (error) {
                    console.error('Error refreshing cart:', error);
                }
            },

            async updateQuantity(itemId, newQuantity) {
                if (newQuantity < 1) {
                    this.removeItem(itemId);
                    return;
                }

                this.loading = true;

                try {
                    const response = await window.updateCartQuantity(itemId, newQuantity);

                    // Update local state
                    const itemIndex = this.cartItems.findIndex(item => item.id === itemId);
                    if (itemIndex !== -1) {
                        this.cartItems[itemIndex].quantity = newQuantity;
                        this.cartItems[itemIndex].total_price_cents = this.cartItems[itemIndex].unit_price_cents * newQuantity;
                    }

                    this.cartTotal = response.cart_total;
                    this.cartCount = response.cart_count;

                } catch (error) {
                    window.showNotification('Gagal memperbarui kuantitas: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async removeItem(itemId) {
                this.loading = true;

                try {
                    const response = await window.removeFromCart(itemId);

                    // Remove from local state
                    this.cartItems = this.cartItems.filter(item => item.id !== itemId);
                    this.cartTotal = response.cart_total;
                    this.cartCount = response.cart_count;

                } catch (error) {
                    window.showNotification('Gagal menghapus item: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async addRecommendationToCart(productId) {
                try {
                    await window.addToCart(productId, 1);
                    this.refreshCart();
                } catch (error) {
                    window.showNotification('Gagal menambahkan produk: ' + error.message, 'error');
                }
            },

            async loadRecommendations() {
                if (this.cartItems.length === 0) return;

                try {
                    const productIds = this.cartItems.map(item => item.product_id);
                    const response = await fetch('/api/recommendations/cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ product_ids: productIds })
                    });

                    const data = await response.json();
                    this.recommendedProducts = data.recommendations || [];
                } catch (error) {
                    console.error('Error loading recommendations:', error);
                }
            },

            async calculateShipping() {
                if (this.shippingPostalCode.length !== 5) return;

                try {
                    const response = await fetch('/api/shipping/calculate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            postal_code: this.shippingPostalCode,
                            cart_items: this.cartItems
                        })
                    });

                    const data = await response.json();
                    this.shippingOptions = data.shipping_options || [];
                } catch (error) {
                    console.error('Error calculating shipping:', error);
                    window.showNotification('Gagal menghitung ongkos kirim', 'error');
                }
            },

            formatCurrency(cents) {
                return window.formatCurrency ? window.formatCurrency(cents / 100) : 'Rp ' + (cents / 100).toLocaleString('id-ID');
            }
        }
    }

    // Auto-refresh cart every 5 minutes to sync with server
    setInterval(async () => {
        const miniCartComponent = document.querySelector('[x-data*="miniCart"]');
        if (miniCartComponent && miniCartComponent._x_dataStack) {
            const data = miniCartComponent._x_dataStack[0];
            if (data && typeof data.refreshCart === 'function') {
                await data.refreshCart();
            }
        }
    }, 300000); // 5 minutes

    // Handle cart dropdown show/hide
    document.addEventListener('DOMContentLoaded', function() {
        const cartDropdown = document.querySelector('.nav-item.dropdown');
        const miniCartDropdown = document.querySelector('.mini-cart-dropdown');

        if (cartDropdown && miniCartDropdown) {
            cartDropdown.addEventListener('shown.bs.dropdown', function() {
                // Refresh cart when dropdown is shown
                const miniCartComponent = miniCartDropdown.querySelector('[x-data*="miniCart"]');
                if (miniCartComponent && miniCartComponent._x_dataStack) {
                    const data = miniCartComponent._x_dataStack[0];
                    if (data && typeof data.refreshCart === 'function') {
                        data.refreshCart();
                    }
                }
            });
        }
    });
</script>
@endpush
