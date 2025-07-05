@extends('layouts.app')

@section('title', 'Shopping Cart - TokoSaya')
@section('meta_description', 'Review your selected items and proceed to checkout securely.')

@push('styles')
<style>
    .cart-container {
        min-height: 60vh;
    }

    .cart-item {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .cart-item-image {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-item-info {
        flex: 1;
        margin-left: 1.5rem;
    }

    .product-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .product-variant {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 1rem;
    }

    .original-price {
        color: #94a3b8;
        text-decoration: line-through;
        font-size: 1rem;
        margin-left: 0.5rem;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .quantity-input {
        display: flex;
        align-items: center;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .quantity-btn {
        background: #f8fafc;
        border: none;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: background 0.2s;
        color: #374151;
        font-weight: 600;
    }

    .quantity-btn:hover:not(:disabled) {
        background: #e2e8f0;
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-value {
        padding: 0.5rem 1rem;
        border: none;
        text-align: center;
        min-width: 60px;
        font-weight: 600;
        background: white;
    }

    .cart-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .btn-remove {
        color: #dc2626;
        background: none;
        border: none;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 4px;
    }

    .btn-remove:hover {
        background: #fef2f2;
        color: #b91c1c;
    }

    .btn-save-later {
        color: #059669;
        background: none;
        border: none;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 4px;
    }

    .btn-save-later:hover {
        background: #f0fdf4;
        color: #047857;
    }

    .cart-summary {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: sticky;
        top: 20px;
        height: fit-content;
    }

    .summary-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-row:last-child {
        border-bottom: none;
        padding-top: 1rem;
        margin-top: 0.5rem;
        border-top: 2px solid #e2e8f0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .summary-label {
        color: #64748b;
        font-weight: 500;
    }

    .summary-value {
        color: #1e293b;
        font-weight: 600;
    }

    .discount-value {
        color: #059669;
    }

    .total-value {
        color: #2563eb;
        font-size: 1.5rem;
    }

    .coupon-section {
        margin: 1.5rem 0;
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .coupon-input {
        display: flex;
        gap: 0.5rem;
    }

    .applied-coupon {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .coupon-info {
        color: #166534;
        font-weight: 500;
    }

    .btn-remove-coupon {
        color: #dc2626;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
    }

    .checkout-btn {
        width: 100%;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 1.5rem;
    }

    .continue-shopping {
        text-align: center;
        margin-top: 1rem;
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }

    .empty-cart i {
        font-size: 5rem;
        color: #cbd5e1;
        margin-bottom: 1.5rem;
    }

    .empty-cart h3 {
        color: #374151;
        margin-bottom: 1rem;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .cart-count {
        color: #64748b;
        font-size: 1rem;
    }

    .bulk-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .select-all {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #374151;
        font-weight: 500;
    }

    .cart-item-checkbox {
        margin-right: 1rem;
        transform: scale(1.2);
    }

    .saved-items {
        margin-top: 3rem;
    }

    .saved-item {
        background: #fefefe;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #2563eb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .security-badges {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin: 2rem 0;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .security-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #059669;
        font-size: 0.875rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .cart-item {
            flex-direction: column;
            text-align: center;
        }

        .cart-item-info {
            margin-left: 0;
            margin-top: 1rem;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .quantity-controls {
            justify-content: center;
            margin-top: 1rem;
        }

        .cart-actions {
            justify-content: center;
        }

        .cart-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .bulk-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .security-badges {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5" x-data="shoppingCart()">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>

    <div class="cart-container">
        <!-- Cart Header -->
        <div class="cart-header" x-show="cartItems.length > 0">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Shopping Cart</h1>
                <p class="cart-count mb-0" x-text="`${cartItems.length} item${cartItems.length !== 1 ? 's' : ''} in your cart`"></p>
            </div>

            <div class="bulk-actions">
                <label class="select-all">
                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()">
                    Select All
                </label>
                <button class="btn btn-outline-danger btn-sm" @click="removeSelected()"
                        x-show="selectedItems.length > 0" :disabled="loading">
                    <i class="fas fa-trash me-2"></i>Remove Selected (<span x-text="selectedItems.length"></span>)
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-5">
                    <div class="loading-spinner mx-auto mb-3"></div>
                    <p class="text-muted">Updating cart...</p>
                </div>

                <!-- Cart Items List -->
                <div x-show="!loading && cartItems.length > 0">
                    <template x-for="(item, index) in cartItems" :key="item.id">
                        <div class="cart-item d-flex">
                            <!-- Checkbox -->
                            <input type="checkbox" class="cart-item-checkbox"
                                   :value="item.id" x-model="selectedItems">

                            <!-- Product Image -->
                            <div class="cart-item-image">
                                <img :src="item.product.image || '{{ asset('images/placeholder.jpg') }}'"
                                     :alt="item.product.name" loading="lazy">
                            </div>

                            <!-- Product Info -->
                            <div class="cart-item-info">
                                <h3 class="product-name">
                                    <a :href="`/products/${item.product.slug}`" class="text-decoration-none text-dark">
                                        <span x-text="item.product.name"></span>
                                    </a>
                                </h3>

                                <div class="product-variant" x-show="item.variant" x-text="item.variant?.name"></div>

                                <div class="product-price">
                                    <span x-text="formatPrice(item.unit_price_cents)"></span>
                                    <span class="original-price" x-show="item.product.compare_price_cents > item.unit_price_cents"
                                          x-text="formatPrice(item.product.compare_price_cents)"></span>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="quantity-controls">
                                    <label class="form-label fw-medium mb-0">Qty:</label>
                                    <div class="quantity-input">
                                        <button class="quantity-btn" @click="updateQuantity(item.id, item.quantity - 1)"
                                                :disabled="item.quantity <= 1 || updating === item.id">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-value" :value="item.quantity"
                                               @change="updateQuantity(item.id, $event.target.value)"
                                               min="1" :max="item.product.stock_quantity">
                                        <button class="quantity-btn" @click="updateQuantity(item.id, item.quantity + 1)"
                                                :disabled="item.quantity >= item.product.stock_quantity || updating === item.id">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <!-- Stock Warning -->
                                    <div x-show="item.quantity >= item.product.stock_quantity" class="text-warning small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Max stock reached
                                    </div>
                                </div>

                                <!-- Item Actions -->
                                <div class="cart-actions">
                                    <button class="btn-save-later" @click="saveForLater(item.id)"
                                            :disabled="updating === item.id">
                                        <i class="far fa-bookmark me-1"></i>Save for Later
                                    </button>
                                    <button class="btn-remove" @click="removeItem(item.id)"
                                            :disabled="updating === item.id">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </div>
                            </div>

                            <!-- Item Total -->
                            <div class="ms-auto text-end">
                                <div class="fw-bold fs-5 text-primary" x-text="formatPrice(item.total_price_cents)"></div>
                                <div class="text-muted small" x-text="`${item.quantity} × ${formatPrice(item.unit_price_cents)}`"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty Cart -->
                <div x-show="!loading && cartItems.length === 0" class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4" x-show="cartItems.length > 0">
                <div class="cart-summary">
                    <h2 class="summary-title">Order Summary</h2>

                    <!-- Summary Rows -->
                    <div class="summary-row">
                        <span class="summary-label">Subtotal (<span x-text="getTotalQuantity()"></span> items)</span>
                        <span class="summary-value" x-text="formatPrice(subtotal)"></span>
                    </div>

                    <div class="summary-row" x-show="shippingCost > 0">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value" x-text="formatPrice(shippingCost)"></span>
                    </div>

                    <div class="summary-row" x-show="shippingCost === 0 && subtotal >= freeShippingThreshold">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value text-success">FREE</span>
                    </div>

                    <div class="summary-row" x-show="tax > 0">
                        <span class="summary-label">Tax</span>
                        <span class="summary-value" x-text="formatPrice(tax)"></span>
                    </div>

                    <div class="summary-row" x-show="discount > 0">
                        <span class="summary-label">Discount</span>
                        <span class="summary-value discount-value" x-text="`-${formatPrice(discount)}`"></span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label fw-bold">Total</span>
                        <span class="summary-value total-value" x-text="formatPrice(total)"></span>
                    </div>

                    <!-- Free Shipping Progress -->
                    <div x-show="subtotal < freeShippingThreshold" class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Free shipping progress</small>
                            <small class="text-muted" x-text="`${Math.round((subtotal / freeShippingThreshold) * 100)}%`"></small>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success"
                                 :style="`width: ${Math.min((subtotal / freeShippingThreshold) * 100, 100)}%`"></div>
                        </div>
                        <small class="text-muted">
                            Add <span class="fw-bold text-success" x-text="formatPrice(freeShippingThreshold - subtotal)"></span>
                            more for free shipping!
                        </small>
                    </div>

                    <!-- Coupon Section -->
                    <div class="coupon-section">
                        <div x-show="!appliedCoupon">
                            <label class="form-label fw-medium">Promo Code</label>
                            <div class="coupon-input">
                                <input type="text" class="form-control" placeholder="Enter coupon code"
                                       x-model="couponCode" @keyup.enter="applyCoupon()">
                                <button class="btn btn-outline-primary" @click="applyCoupon()"
                                        :disabled="!couponCode.trim() || applyingCoupon">
                                    <span x-show="!applyingCoupon">Apply</span>
                                    <span x-show="applyingCoupon">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div x-show="appliedCoupon" class="applied-coupon">
                            <div class="coupon-info">
                                <div class="fw-bold" x-text="appliedCoupon?.code"></div>
                                <div class="small" x-text="appliedCoupon?.description"></div>
                            </div>
                            <button class="btn-remove-coupon" @click="removeCoupon()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Security Badges -->
                    <div class="security-badges">
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Checkout</span>
                        </div>
                        <div class="security-badge">
                            <i class="fas fa-lock"></i>
                            <span>SSL Protected</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <button class="btn btn-primary checkout-btn" @click="proceedToCheckout()"
                            :disabled="cartItems.length === 0 || loading">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                    </button>

                    <div class="continue-shopping">
                        <a href="{{ route('products.index') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Items -->
        <div class="saved-items" x-show="savedItems.length > 0">
            <h3 class="fw-bold mb-3">Saved for Later (<span x-text="savedItems.length"></span>)</h3>

            <div class="row">
                <template x-for="item in savedItems" :key="item.id">
                    <div class="col-md-3 mb-3">
                        <div class="saved-item text-center">
                            <img :src="item.product.image || '{{ asset('images/placeholder.jpg') }}'"
                                 :alt="item.product.name" class="img-fluid mb-2" style="height: 120px; object-fit: cover;">
                            <h6 x-text="item.product.name" class="mb-2"></h6>
                            <p class="text-primary fw-bold mb-2" x-text="formatPrice(item.unit_price_cents)"></p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-primary" @click="moveToCart(item.id)">
                                    Move to Cart
                                </button>
                                <button class="btn btn-sm btn-outline-danger" @click="removeSavedItem(item.id)">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" x-show="loading">
        <div class="loading-spinner"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shoppingCart() {
    return {
        cartItems: [],
        savedItems: [],
        selectedItems: [],
        selectAll: false,
        loading: false,
        updating: null,

        // Pricing
        subtotal: 0,
        shippingCost: 0,
        tax: 0,
        discount: 0,
        total: 0,
        freeShippingThreshold: 50000000, // 500k in cents

        // Coupon
        couponCode: '',
        appliedCoupon: null,
        applyingCoupon: false,

        async init() {
            await this.loadCart();
            this.calculateTotals();
        },

        async loadCart() {
            this.loading = true;

            try {
                const response = await fetch('{{ route("cart.items") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.cartItems = data.items || [];
                    this.savedItems = data.saved_items || [];
                    this.appliedCoupon = data.applied_coupon || null;
                    this.calculateTotals();
                }
            } catch (error) {
                console.error('Load cart error:', error);
                this.showNotification('Failed to load cart', 'error');
            } finally {
                this.loading = false;
            }
        },

        async updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;

            this.updating = itemId;

            try {
                const response = await fetch('{{ route("cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        quantity: parseInt(newQuantity)
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update local state
                    const itemIndex = this.cartItems.findIndex(item => item.id === itemId);
                    if (itemIndex !== -1) {
                        this.cartItems[itemIndex].quantity = parseInt(newQuantity);
                        this.cartItems[itemIndex].total_price_cents = this.cartItems[itemIndex].unit_price_cents * parseInt(newQuantity);
                    }

                    this.calculateTotals();
                    this.updateMiniCart();
                } else {
                    throw new Error(data.message || 'Failed to update quantity');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.updating = null;
            }
        },

        async removeItem(itemId) {
            if (!confirm('Are you sure you want to remove this item?')) return;

            this.updating = itemId;

            try {
                const response = await fetch('{{ route("cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.cartItems = this.cartItems.filter(item => item.id !== itemId);
                    this.selectedItems = this.selectedItems.filter(id => id !== itemId);
                    this.calculateTotals();
                    this.updateMiniCart();
                    this.showNotification('Item removed from cart', 'success');
                } else {
                    throw new Error(data.message || 'Failed to remove item');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.updating = null;
            }
        },

        async removeSelected() {
            if (this.selectedItems.length === 0) return;

            const message = `Remove ${this.selectedItems.length} selected item${this.selectedItems.length > 1 ? 's' : ''}?`;

            if (!confirm(message)) return;

            this.loading = true;

            try {
                const response = await fetch('{{ route("cart.remove-multiple") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_ids: this.selectedItems
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.cartItems = this.cartItems.filter(item => !this.selectedItems.includes(item.id));
                    this.selectedItems = [];
                    this.selectAll = false;
                    this.calculateTotals();
                    this.updateMiniCart();
                    this.showNotification(`${data.removed_count} items removed from cart`, 'success');
                } else {
                    throw new Error(data.message || 'Failed to remove items');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        async saveForLater(itemId) {
            this.updating = itemId;

            try {
                const response = await fetch('{{ route("cart.save-for-later") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    const item = this.cartItems.find(item => item.id === itemId);
                    if (item) {
                        this.savedItems.push(item);
                        this.cartItems = this.cartItems.filter(item => item.id !== itemId);
                        this.selectedItems = this.selectedItems.filter(id => id !== itemId);
                        this.calculateTotals();
                        this.updateMiniCart();
                        this.showNotification('Item saved for later', 'success');
                    }
                } else {
                    throw new Error(data.message || 'Failed to save item');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.updating = null;
            }
        },

        async moveToCart(itemId) {
            try {
                const response = await fetch('{{ route("cart.move-to-cart") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    const item = this.savedItems.find(item => item.id === itemId);
                    if (item) {
                        this.cartItems.push(item);
                        this.savedItems = this.savedItems.filter(item => item.id !== itemId);
                        this.calculateTotals();
                        this.updateMiniCart();
                        this.showNotification('Item moved to cart', 'success');
                    }
                } else {
                    throw new Error(data.message || 'Failed to move item to cart');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        },

        async removeSavedItem(itemId) {
            if (!confirm('Remove this item from saved items?')) return;

            try {
                const response = await fetch('{{ route("cart.remove-saved") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.savedItems = this.savedItems.filter(item => item.id !== itemId);
                    this.showNotification('Item removed from saved items', 'success');
                } else {
                    throw new Error(data.message || 'Failed to remove saved item');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = this.cartItems.map(item => item.id);
            } else {
                this.selectedItems = [];
            }
        },

        async applyCoupon() {
            if (!this.couponCode.trim()) return;

            this.applyingCoupon = true;

            try {
                const response = await fetch('{{ route("cart.apply-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: this.couponCode.trim()
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.appliedCoupon = data.coupon;
                    this.couponCode = '';
                    this.calculateTotals();
                    this.showNotification('Coupon applied successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Invalid coupon code');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            } finally {
                this.applyingCoupon = false;
            }
        },

        async removeCoupon() {
            try {
                const response = await fetch('{{ route("cart.remove-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.appliedCoupon = null;
                    this.calculateTotals();
                    this.showNotification('Coupon removed', 'info');
                } else {
                    throw new Error(data.message || 'Failed to remove coupon');
                }
            } catch (error) {
                this.showNotification(error.message, 'error');
            }
        },

        calculateTotals() {
            // Calculate subtotal
            this.subtotal = this.cartItems.reduce((sum, item) => sum + item.total_price_cents, 0);

            // Calculate shipping
            this.shippingCost = this.subtotal >= this.freeShippingThreshold ? 0 : 1500000; // 15k shipping

            // Calculate tax (11% PPN)
            this.tax = Math.round(this.subtotal * 0.11);

            // Calculate discount from coupon
            this.discount = 0;
            if (this.appliedCoupon) {
                if (this.appliedCoupon.type === 'fixed') {
                    this.discount = this.appliedCoupon.value_cents;
                } else if (this.appliedCoupon.type === 'percentage') {
                    this.discount = Math.round(this.subtotal * (this.appliedCoupon.value_cents / 10000));
                }

                // Apply maximum discount limit if set
                if (this.appliedCoupon.maximum_discount_cents) {
                    this.discount = Math.min(this.discount, this.appliedCoupon.maximum_discount_cents);
                }
            }

            // Calculate total
            this.total = Math.max(0, this.subtotal + this.shippingCost + this.tax - this.discount);
        },

        getTotalQuantity() {
            return this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        },

        formatPrice(cents) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(cents / 100);
        },

        async proceedToCheckout() {
            if (this.cartItems.length === 0) return;

            // Store cart summary for checkout
            sessionStorage.setItem('checkoutSummary', JSON.stringify({
                subtotal: this.subtotal,
                shipping: this.shippingCost,
                tax: this.tax,
                discount: this.discount,
                total: this.total,
                appliedCoupon: this.appliedCoupon
            }));

            window.location.href = '{{ route("checkout.index") }}';
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

                // Update cart count in header
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

// Auto-save cart changes
let autoSaveTimeout;
function autoSaveCart() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        // Auto-save cart state to prevent data loss
        console.log('Auto-saving cart state...');
    }, 2000);
}

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        // Save cart state when page becomes hidden
        autoSaveCart();
    }
});

// Prevent accidental page refresh when cart has items
window.addEventListener('beforeunload', function(e) {
    const cartData = document.querySelector('[x-data="shoppingCart()"]');
    if (cartData && cartData.__x && cartData.__x.$data.cartItems.length > 0) {
        e.preventDefault();
        e.returnValue = 'You have items in your cart. Are you sure you want to leave?';
        return e.returnValue;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Track cart page view
    fetch('{{ route("analytics.track") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            event: 'cart_view',
            page: 'cart'
        })
    }).catch(error => {
        console.error('Analytics tracking error:', error);
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>
@endpush
