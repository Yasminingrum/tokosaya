@extends('layouts.app')

@section('title', 'Keranjang Belanja - TokoSaya')
@section('description', 'Kelola produk di keranjang belanja Anda sebelum melakukan checkout')

@push('styles')
<style>
    .cart-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .product-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    .quantity-input {
        width: 80px;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }

    .quantity-btn {
        width: 35px;
        height: 35px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        color: #374151;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .quantity-btn:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .price-text {
        font-weight: 600;
        color: #1f2937;
        font-size: 1.1rem;
    }

    .summary-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        position: sticky;
        top: 100px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .summary-row:not(:last-child) {
        border-bottom: 1px solid #f3f4f6;
    }

    .summary-total {
        font-weight: 700;
        font-size: 1.2rem;
        color: #1f2937;
    }

    .empty-cart {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-cart-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .coupon-form {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .recently-viewed {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .recently-viewed-item {
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s;
    }

    .recently-viewed-item:hover {
        transform: translateY(-2px);
        color: inherit;
        text-decoration: none;
    }

    .recently-viewed-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Keranjang Belanja</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold mb-2">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>
                Keranjang Belanja
            </h1>
            @if($cartItems && $cartItems->count() > 0)
                <p class="text-muted">Anda memiliki {{ $cartItems->sum('quantity') }} item dalam keranjang</p>
            @endif
        </div>
    </div>

    @if($cartItems && $cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <!-- Select All & Clear Cart -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">
                            Pilih Semua
                        </label>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                        <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
                    </button>
                </div>

                <!-- Cart Items List -->
                <div id="cart-items-container">
                    @foreach($cartItems as $item)
                        <div class="cart-item p-3" data-item-id="{{ $item->id }}">
                            <div class="row align-items-center">
                                <!-- Checkbox -->
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                                    </div>
                                </div>

                                <!-- Product Image -->
                                <div class="col-auto">
                                    @if($item->product->images && $item->product->images->count() > 0)
                                        <img src="{{ $item->product->images->first()->image_url }}"
                                             alt="{{ $item->product->name }}"
                                             class="product-image">
                                    @else
                                        <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="col">
                                    <h6 class="fw-semibold mb-1">
                                        <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none text-dark">
                                            {{ $item->product->name }}
                                        </a>
                                    </h6>

                                    @if($item->variant)
                                        <small class="text-muted">Varian: {{ $item->variant->variant_name }} - {{ $item->variant->variant_value }}</small>
                                    @endif

                                    <div class="mt-2">
                                        <span class="price-text">
                                            Rp {{ number_format($item->unit_price_cents / 100, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Stock Status -->
                                    @php
                                        $stock = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
                                    @endphp
                                    @if($stock < $item->quantity)
                                        <div class="text-danger small mt-1">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Stok tidak mencukupi (tersisa {{ $stock }})
                                        </div>
                                    @elseif($stock <= 5)
                                        <div class="text-warning small mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Stok terbatas (tersisa {{ $stock }})
                                        </div>
                                    @endif
                                </div>

                                <!-- Quantity Controls -->
                                <div class="col-auto">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="quantity-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                               class="form-control quantity-input"
                                               value="{{ $item->quantity }}"
                                               min="1"
                                               max="{{ $stock }}"
                                               onchange="updateQuantity({{ $item->id }}, this.value)">
                                        <button type="button" class="quantity-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Item Total -->
                                <div class="col-auto text-end">
                                    <div class="price-text" id="item-total-{{ $item->id }}">
                                        Rp {{ number_format($item->total_price_cents / 100, 0, ',', '.') }}
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="removeItem({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>

                    <!-- Coupon Form -->
                    <div class="coupon-form">
                        <label class="form-label small fw-semibold">Kode Kupon</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon-code" placeholder="Masukkan kode kupon">
                            <button class="btn btn-outline-primary" type="button" onclick="applyCoupon()">
                                Terapkan
                            </button>
                        </div>
                        <div id="coupon-message" class="mt-2"></div>
                    </div>

                    <!-- Summary Details -->
                    <div class="summary-row">
                        <span>Subtotal ({{ $summary['item_count'] }} item)</span>
                        <span id="subtotal">{{ $summary['formatted']['subtotal'] }}</span>
                    </div>

                    @if(isset($summary['applied_coupon']) && $summary['applied_coupon'])
                        <div class="summary-row text-success">
                            <span>
                                Diskon ({{ $summary['applied_coupon']['code'] }})
                                <button type="button" class="btn btn-link btn-sm p-0 text-danger" onclick="removeCoupon()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                            <span id="discount">-{{ $summary['formatted']['discount'] }}</span>
                        </div>
                    @endif

                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span id="shipping">{{ $summary['formatted']['shipping'] ?? 'Rp 0' }}</span>
                    </div>

                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="total">{{ $summary['formatted']['total'] }}</span>
                    </div>

                    <!-- Checkout Button -->
                    @auth
                        <button type="button" class="btn btn-primary w-100 btn-lg mt-3" onclick="proceedToCheckout()">
                            <i class="fas fa-credit-card me-2"></i>
                            Lanjut ke Pembayaran
                        </button>
                    @else
                        <div class="text-center mt-3">
                            <p class="text-muted small mb-2">Silakan login untuk melanjutkan</p>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    @endauth

                    <!-- Continue Shopping -->
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>

        <!-- Recently Viewed Products -->
        @if($recentlyViewed && $recentlyViewed->count() > 0)
            <div class="recently-viewed">
                <h5 class="fw-bold mb-3">Produk yang Baru Dilihat</h5>
                <div class="row g-3">
                    @foreach($recentlyViewed as $product)
                        <div class="col-lg-3 col-md-4 col-6">
                            <a href="{{ route('products.show', $product) }}" class="recently-viewed-item d-block">
                                @if($product->images && $product->images->count() > 0)
                                    <img src="{{ $product->images->first()->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="recently-viewed-image mx-auto d-block">
                                @else
                                    <div class="recently-viewed-image mx-auto d-block bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <h6 class="small fw-semibold">{{ Str::limit($product->name, 30) }}</h6>
                                <p class="small text-primary mb-0">Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        <!-- Empty Cart -->
        <div class="row">
            <div class="col-12">
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Keranjang Belanja Kosong</h4>
                    <p class="mb-4">Sepertinya Anda belum menambahkan produk apapun ke keranjang belanja.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Mulai Belanja
                    </a>
                </div>

                <!-- Recently Viewed Products for Empty Cart -->
                @if($recentlyViewed && $recentlyViewed->count() > 0)
                    <div class="recently-viewed">
                        <h5 class="fw-bold mb-3 text-center">Produk yang Baru Anda Lihat</h5>
                        <div class="row g-3 justify-content-center">
                            @foreach($recentlyViewed as $product)
                                <div class="col-lg-3 col-md-4 col-6">
                                    <a href="{{ route('products.show', $product) }}" class="recently-viewed-item d-block">
                                        @if($product->images && $product->images->count() > 0)
                                            <img src="{{ $product->images->first()->image_url }}"
                                                 alt="{{ $product->name }}"
                                                 class="recently-viewed-image mx-auto d-block">
                                        @else
                                            <div class="recently-viewed-image mx-auto d-block bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <h6 class="small fw-semibold text-center">{{ Str::limit($product->name, 30) }}</h6>
                                        <p class="small text-primary mb-0 text-center">Rp {{ number_format($product->price_cents / 100, 0, ',', '.') }}</p>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Update select all based on individual checkboxes
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(itemCheckboxes).every(cb => !cb.checked);

                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            });
        });
    }
});

// Update item quantity
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) return;

    const button = event.target.closest('button');
    const originalContent = button ? button.innerHTML : '';

    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    const updateUrl = '{{ route("cart.update", ":item") }}'.replace(':item', itemId);
    fetch(updateUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            quantity: parseInt(newQuantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update item total
            document.getElementById(`item-total-${itemId}`).textContent = data.item_total;

            // Update cart total
            document.getElementById('total').textContent = data.cart_total;

            // Update quantity input
            const quantityInput = document.querySelector(`input[onchange*="${itemId}"]`);
            if (quantityInput) {
                quantityInput.value = newQuantity;
            }

            // Update cart count in header
            updateCartCount();

            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal memperbarui keranjang', 'danger');
    })
    .finally(() => {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    });
}

// Remove item from cart
function removeItem(itemId) {
    if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
        return;
    }

    const removeUrl = '{{ route("cart.remove", ":item") }}'.replace(':item', itemId);
    fetch(removeUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                itemElement.remove();
            }

            // Check if cart is empty
            const remainingItems = document.querySelectorAll('.cart-item');
            if (remainingItems.length === 0) {
                location.reload(); // Reload to show empty cart view
            } else {
                // Update totals
                updateCartSummary();
            }

            // Update cart count in header
            updateCartCount();

            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal menghapus item', 'danger');
    });
}

// Clear entire cart
function clearCart() {
    if (!confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        return;
    }

    fetch('{{ route("cart.clear") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal mengosongkan keranjang', 'danger');
    });
}

// Apply coupon
function applyCoupon() {
    const couponCode = document.getElementById('coupon-code').value.trim();
    const messageDiv = document.getElementById('coupon-message');

    if (!couponCode) {
        messageDiv.innerHTML = '<small class="text-danger">Masukkan kode kupon</small>';
        return;
    }

    fetch('{{ route("cart.coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = `<small class="text-success"><i class="fas fa-check me-1"></i>${data.message}</small>`;
            // Reload page to show updated summary
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.innerHTML = `<small class="text-danger"><i class="fas fa-times me-1"></i>${data.message}</small>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<small class="text-danger">Gagal menerapkan kupon</small>';
    });
}

// Remove applied coupon
function removeCoupon() {
    fetch('{{ route("cart.coupon.remove") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Proceed to checkout
function proceedToCheckout() {
    window.location.href = '{{ route("checkout.index") }}';
}

// Update cart summary
function updateCartSummary() {
    // This would typically recalculate totals
    // For now, we'll just reload the page for simplicity
    location.reload();
}

// Show notification function
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '100px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';

    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            const alert = new bootstrap.Alert(alertDiv);
            alert.close();
        }
    }, 5000);
}
</script>
@endpush
