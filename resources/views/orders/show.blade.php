@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="order-icon me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1">Order #{{ $order->order_number }}</h1>
                                    <p class="text-muted mb-0">
                                        Placed on {{ $order->created_at->format('F d, Y') }} at {{ $order->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2 mb-2">
                                {{ ucfirst($order->status) }}
                            </span>
                            <div class="d-flex flex-column gap-2">
                                @if($order->status === 'shipped')
                                    <a href="{{ route('orders.track', $order) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-truck me-1"></i>Track Order
                                    </a>
                                @endif
                                @if(in_array($order->status, ['delivered']))
                                    <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline-info btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Download Invoice
                                    </a>
                                @endif
                                @if(in_array($order->status, ['pending', 'confirmed']) && $order->canBeCancelled())
                                    <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder({{ $order->id }})">
                                        <i class="fas fa-times me-1"></i>Cancel Order
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Progress Timeline -->
    @if($order->status !== 'cancelled')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Progress</h6>
                </div>
                <div class="card-body">
                    <div class="order-timeline">
                        @php
                            $statuses = [
                                'pending' => ['icon' => 'clock', 'title' => 'Order Placed', 'desc' => 'Your order has been received'],
                                'confirmed' => ['icon' => 'check-circle', 'title' => 'Order Confirmed', 'desc' => 'Your order has been confirmed'],
                                'processing' => ['icon' => 'cog', 'title' => 'Processing', 'desc' => 'Your order is being prepared'],
                                'shipped' => ['icon' => 'truck', 'title' => 'Shipped', 'desc' => 'Your order is on the way'],
                                'delivered' => ['icon' => 'home', 'title' => 'Delivered', 'desc' => 'Your order has been delivered']
                            ];

                            $currentIndex = array_search($order->status, array_keys($statuses));
                        @endphp

                        <div class="timeline">
                            @foreach($statuses as $status => $info)
                                @php
                                    $index = array_search($status, array_keys($statuses));
                                    $isCompleted = $index <= $currentIndex;
                                    $isCurrent = $index === $currentIndex;
                                @endphp

                                <div class="timeline-item {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-{{ $info['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $info['title'] }}</h6>
                                        <p class="text-muted small mb-0">{{ $info['desc'] }}</p>
                                        @if($isCompleted)
                                            <small class="text-success">
                                                @switch($status)
                                                    @case('pending')
                                                        {{ $order->created_at->format('M d, Y H:i') }}
                                                        @break
                                                    @case('confirmed')
                                                        {{ $order->confirmed_at ? $order->confirmed_at->format('M d, Y H:i') : 'Processing...' }}
                                                        @break
                                                    @case('shipped')
                                                        {{ $order->shipped_at ? $order->shipped_at->format('M d, Y H:i') : 'Processing...' }}
                                                        @break
                                                    @case('delivered')
                                                        {{ $order->delivered_at ? $order->delivered_at->format('M d, Y H:i') : 'Processing...' }}
                                                        @break
                                                @endswitch
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Items ({{ $order->items->count() }} items)</h6>
                </div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                    <div class="border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="{{ $item->product->primary_image_url }}"
                                     alt="{{ $item->product_name }}"
                                     class="img-fluid rounded"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-1">
                                    <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none">
                                        {{ $item->product_name }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">SKU: {{ $item->product_sku }}</p>
                                @if($item->variant_name)
                                    <p class="text-muted small mb-0">Variant: {{ $item->variant_name }}</p>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="badge bg-light text-dark">Qty: {{ $item->quantity }}</span>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="price-info">
                                    <div class="current-price fw-bold">
                                        {{ \App\Helpers\PriceHelper::format($item->total_price_cents) }}
                                    </div>
                                    <div class="unit-price text-muted small">
                                        {{ \App\Helpers\PriceHelper::format($item->unit_price_cents) }} each
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Actions -->
                        @if($order->status === 'delivered')
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    @if(!$item->hasReview())
                                    <button class="btn btn-outline-primary btn-sm" onclick="showItemReviewModal({{ $item->id }})">
                                        <i class="fas fa-star me-1"></i>Write Review
                                    </button>
                                    @else
                                    <a href="{{ route('products.show', $item->product) }}#reviews" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Review
                                    </a>
                                    @endif
                                    <button class="btn btn-outline-success btn-sm" onclick="addToCart({{ $item->product->id }}, {{ $item->variant_id ?? 'null' }})">
                                        <i class="fas fa-cart-plus me-1"></i>Buy Again
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary & Details -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="order-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal ({{ $order->items->sum('quantity') }} items)</span>
                            <span>{{ \App\Helpers\PriceHelper::format($order->subtotal_cents) }}</span>
                        </div>
                        @if($order->discount_cents > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                <i class="fas fa-tag me-1"></i>Discount
                                @if($order->coupon_code)
                                    ({{ $order->coupon_code }})
                                @endif
                            </span>
                            <span>-{{ \App\Helpers\PriceHelper::format($order->discount_cents) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>
                                @if($order->shipping_cents > 0)
                                    {{ \App\Helpers\PriceHelper::format($order->shipping_cents) }}
                                @else
                                    <span class="text-success">Free</span>
                                @endif
                            </span>
                        </div>
                        @if($order->tax_cents > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span>{{ \App\Helpers\PriceHelper::format($order->tax_cents) }}</span>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span class="text-primary">{{ \App\Helpers\PriceHelper::format($order->total_cents) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="payment-info">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-credit-card text-primary me-3"></i>
                            <div>
                                <div class="fw-medium">{{ $order->paymentMethod->name ?? 'Payment Method' }}</div>
                                <small class="text-muted">
                                    Status:
                                    <span class="badge bg-{{ $order->payment_status_color }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </small>
                            </div>
                        </div>

                        @if($order->payments->isNotEmpty())
                            @foreach($order->payments as $payment)
                            <div class="payment-item p-3 bg-light rounded mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Transaction ID</small>
                                        <div class="fw-medium">{{ $payment->transaction_id ?? 'Pending' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">{{ \App\Helpers\PriceHelper::format($payment->amount_cents) }}</div>
                                        <small class="text-muted">{{ $payment->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Shipping Information</h6>
                </div>
                <div class="card-body">
                    <div class="shipping-info">
                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-truck text-primary me-3 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="fw-medium mb-1">{{ $order->shippingMethod->name ?? 'Standard Shipping' }}</div>
                                @if($order->tracking_number)
                                <div class="mb-2">
                                    <small class="text-muted">Tracking Number</small>
                                    <div class="fw-medium">{{ $order->tracking_number }}</div>
                                </div>
                                @endif
                                @if($order->shipped_at)
                                <small class="text-muted">
                                    Shipped on {{ $order->shipped_at->format('M d, Y') }}
                                </small>
                                @endif
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="delivery-address p-3 bg-light rounded">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt text-secondary me-3 mt-1"></i>
                                <div>
                                    <div class="fw-medium">{{ $order->shipping_name }}</div>
                                    <div class="text-muted">{{ $order->shipping_phone }}</div>
                                    <div class="mt-1">
                                        {{ $order->shipping_address }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                        {{ $order->shipping_country }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->status === 'delivered' && !$order->hasReviews())
                        <button class="btn btn-primary" onclick="showOrderReviewModal({{ $order->id }})">
                            <i class="fas fa-star me-2"></i>Write Reviews for All Items
                        </button>
                        @endif

                        @if($order->status === 'delivered')
                        <button class="btn btn-outline-success" onclick="reorderAll({{ $order->id }})">
                            <i class="fas fa-redo me-2"></i>Reorder All Items
                        </button>
                        @endif

                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>

                        @if(in_array($order->status, ['delivered']))
                        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline-info" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>Download Invoice
                        </a>
                        @endif

                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Notes -->
    @if($order->notes)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Order Notes</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="reviewContent">
                    <!-- Review form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.order-timeline {
    position: relative;
}

.timeline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    margin: 0;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 35px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.timeline-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    position: relative;
    z-index: 2;
}

.timeline-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 10px;
    border: 3px solid #e9ecef;
}

.timeline-item.completed .timeline-icon {
    background: #198754;
    color: white;
    border-color: #198754;
}

.timeline-item.current .timeline-icon {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
    100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-content p {
    font-size: 0.8rem;
    margin-bottom: 5px;
}

.order-summary .d-flex {
    padding: 0.5rem 0;
}

.payment-item {
    transition: all 0.3s ease;
}

.payment-item:hover {
    background-color: #f8f9fa !important;
}

.delivery-address {
    transition: all 0.3s ease;
}

.delivery-address:hover {
    background-color: #f8f9fa !important;
}

@media (max-width: 768px) {
    .timeline {
        flex-direction: column;
        align-items: stretch;
    }

    .timeline::before {
        display: none;
    }

    .timeline-item {
        flex-direction: row;
        text-align: left;
        margin-bottom: 20px;
        align-items: flex-start;
    }

    .timeline-icon {
        margin-right: 15px;
        margin-bottom: 0;
        flex-shrink: 0;
    }

    .timeline-content {
        flex-grow: 1;
    }
}

@media print {
    .btn, .modal, .navbar, .breadcrumb {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }

    .text-primary {
        color: #000 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh order status every 30 seconds for shipped orders
    @if($order->status === 'shipped')
    setInterval(function() {
        checkOrderStatus();
    }, 30000);
    @endif
});

// Check order status update
function checkOrderStatus() {
    fetch(`/orders/{{ $order->id }}/status`)
        .then(response => response.json())
        .then(data => {
            if (data.status !== '{{ $order->status }}') {
                location.reload();
            }
        })
        .catch(error => console.log('Status check failed'));
}

// Cancel order
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Order cancelled successfully', 'success');
                location.reload();
            } else {
                showAlert(data.message || 'Error cancelling order', 'error');
            }
        })
        .catch(error => {
            showAlert('Error cancelling order', 'error');
        });
    }
}

// Reorder all items
function reorderAll(orderId) {
    if (confirm('Add all items from this order to your cart?')) {
        fetch(`/orders/${orderId}/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('All items added to cart successfully!', 'success');
                updateCartCount();
            } else {
                showAlert('Some items are no longer available', 'warning');
            }
        })
        .catch(error => {
            showAlert('Error processing reorder', 'error');
        });
    }
}

// Add single item to cart
function addToCart(productId, variantId = null) {
    const data = {
        product_id: productId,
        quantity: 1
    };

    if (variantId) {
        data.variant_id = variantId;
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
            showAlert('Item added to cart!', 'success');
            updateCartCount();
        } else {
            showAlert(data.message || 'Error adding item to cart', 'error');
        }
    })
    .catch(error => {
        showAlert('Error adding item to cart', 'error');
    });
}

// Show review modal for single item
function showItemReviewModal(itemId) {
    fetch(`/orders/items/${itemId}/review-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviewContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        })
        .catch(error => {
            showAlert('Error loading review form', 'error');
        });
}

// Show review modal for all items
function showOrderReviewModal(orderId) {
    fetch(`/orders/${orderId}/review-all-form`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviewContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        })
        .catch(error => {
            showAlert('Error loading review form', 'error');
        });
}

// Alert helper
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Update cart count
function updateCartCount() {
    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.count;
            }
        });
}
</script>
@endpush
@endsection
