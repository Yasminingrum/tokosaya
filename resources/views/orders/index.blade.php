@extends('layouts.app')

@section('title', 'Order History - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Order History</h1>
                    <p class="text-muted mb-0">Track and manage your orders</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $orderStats['total_orders'] }}</h5>
                    <p class="text-muted small mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $orderStats['pending_orders'] }}</h5>
                    <p class="text-muted small mb-0">Pending Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $orderStats['completed_orders'] }}</h5>
                    <p class="text-muted small mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-rupiah-sign text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ \App\Helpers\PriceHelper::format($orderStats['total_spent']) }}</h5>
                    <p class="text-muted small mb-0">Total Spent</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0">Your Orders ({{ $orders->total() }} orders)</h6>
                </div>
                <div class="col-auto">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Search orders..." id="searchOrders" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
                @foreach($orders as $order)
                <div class="border-bottom p-4 order-item" data-order-id="{{ $order->id }}">
                    <div class="row align-items-center">
                        <!-- Order Info -->
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="order-icon me-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                                    Order #{{ $order->order_number }}
                                                </a>
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                Placed on {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                        <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                                    </div>

                                    <!-- Order Items Preview -->
                                    <div class="order-items-preview mb-2">
                                        <div class="d-flex align-items-center">
                                            @foreach($order->items->take(3) as $item)
                                                <div class="item-image me-2">
                                                    <img src="{{ $item->product->primary_image_url }}"
                                                         alt="{{ $item->product_name }}"
                                                         class="rounded"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                            @if($order->items->count() > 3)
                                                <span class="text-muted small">+{{ $order->items->count() - 3 }} more items</span>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                {{ $order->items->sum('quantity') }} items •
                                                Total: <strong>{{ \App\Helpers\PriceHelper::format($order->total_cents) }}</strong>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Order Progress -->
                                    @if($order->status !== 'cancelled')
                                    <div class="order-progress mb-2">
                                        <div class="progress" style="height: 4px;">
                                            @php
                                                $progress = match($order->status) {
                                                    'pending' => 20,
                                                    'confirmed' => 40,
                                                    'processing' => 60,
                                                    'shipped' => 80,
                                                    'delivered' => 100,
                                                    default => 0
                                                };
                                            @endphp
                                            <div class="progress-bar bg-{{ $order->status_color }}"
                                                 role="progressbar"
                                                 style="width: {{ $progress }}%">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">Order Progress</small>
                                            <small class="text-muted">{{ $progress }}%</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="col-md-4 text-md-end">
                            <div class="order-actions">
                                @if($order->status === 'delivered')
                                    @if(!$order->isReviewed())
                                    <button class="btn btn-outline-primary btn-sm mb-2 w-100"
                                            onclick="showReviewModal({{ $order->id }})">
                                        <i class="fas fa-star me-1"></i>Write Review
                                    </button>
                                    @endif
                                    <button class="btn btn-primary btn-sm mb-2 w-100"
                                            onclick="reorderItems({{ $order->id }})">
                                        <i class="fas fa-redo me-1"></i>Reorder
                                    </button>
                                @endif

                                @if($order->status === 'shipped')
                                    <a href="{{ route('orders.track', $order) }}"
                                       class="btn btn-info btn-sm mb-2 w-100">
                                        <i class="fas fa-truck me-1"></i>Track Order
                                    </a>
                                @endif

                                @if(in_array($order->status, ['pending', 'confirmed']) && $order->canBeCancelled())
                                    <button class="btn btn-outline-danger btn-sm mb-2 w-100"
                                            onclick="cancelOrder({{ $order->id }})">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </button>
                                @endif

                                <a href="{{ route('orders.show', $order) }}"
                                   class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>

                                @if($order->status === 'delivered')
                                <a href="{{ route('orders.invoice', $order) }}"
                                   class="btn btn-outline-info btn-sm mt-2 w-100"
                                   target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Download Invoice
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="p-4 border-top bg-light">
                    {{ $orders->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">No orders found</h5>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('orders.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_from"
                                       value="{{ request('date_from') }}" placeholder="From">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" name="date_to"
                                       value="{{ request('date_to') }}" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" name="amount_min"
                                       value="{{ request('amount_min') }}" placeholder="Min amount">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="amount_max"
                                       value="{{ request('amount_max') }}" placeholder="Max amount">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchOrders');
    const searchBtn = document.getElementById('searchBtn');

    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.trim();
        const url = new URL(window.location);

        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }

        window.location.href = url.toString();
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });
});

// Reorder functionality
function reorderItems(orderId) {
    if (confirm('Are you sure you want to reorder all items from this order?')) {
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
                showAlert('Items added to cart successfully!', 'success');
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

// Show review modal
function showReviewModal(orderId) {
    fetch(`/orders/${orderId}/review-form`)
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
    // Create and show toast notification
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Update cart count (assuming you have this function)
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
