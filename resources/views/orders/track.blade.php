@extends('layouts.app')

@section('title', 'Track Order #' . $order->order_number . ' - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Order #{{ $order->order_number }}</a></li>
            <li class="breadcrumb-item active">Track Order</li>
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
                                <div class="tracking-icon me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1">Track Order #{{ $order->order_number }}</h1>
                                    <p class="text-muted mb-0">
                                        Shipped on {{ $order->shipped_at ? $order->shipped_at->format('F d, Y') : 'Processing' }}
                                        @if($order->tracking_number)
                                            • Tracking: <strong>{{ $order->tracking_number }}</strong>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2 mb-2">
                                {{ ucfirst($order->status) }}
                            </span>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" onclick="refreshTracking()">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh
                                </button>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Order Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tracking Timeline -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-route me-2"></i>Shipping Progress
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tracking-timeline">
                        @php
                            $trackingSteps = [
                                [
                                    'status' => 'confirmed',
                                    'title' => 'Order Confirmed',
                                    'description' => 'Your order has been confirmed and is being prepared',
                                    'icon' => 'check-circle',
                                    'timestamp' => $order->confirmed_at,
                                    'completed' => true
                                ],
                                [
                                    'status' => 'processing',
                                    'title' => 'Package Prepared',
                                    'description' => 'Your items are being packaged for shipment',
                                    'icon' => 'box',
                                    'timestamp' => $order->confirmed_at ? $order->confirmed_at->addHours(2) : null,
                                    'completed' => in_array($order->status, ['processing', 'shipped', 'delivered'])
                                ],
                                [
                                    'status' => 'shipped',
                                    'title' => 'Package Shipped',
                                    'description' => 'Your package is on its way to you',
                                    'icon' => 'truck',
                                    'timestamp' => $order->shipped_at,
                                    'completed' => in_array($order->status, ['shipped', 'delivered'])
                                ],
                                [
                                    'status' => 'in_transit',
                                    'title' => 'In Transit',
                                    'description' => 'Package is traveling to your delivery address',
                                    'icon' => 'shipping-fast',
                                    'timestamp' => $order->shipped_at ? $order->shipped_at->addHours(6) : null,
                                    'completed' => in_array($order->status, ['shipped', 'delivered']) && $order->shipped_at
                                ],
                                [
                                    'status' => 'out_for_delivery',
                                    'title' => 'Out for Delivery',
                                    'description' => 'Package is out for delivery and will arrive today',
                                    'icon' => 'truck-loading',
                                    'timestamp' => $order->status === 'delivered' ? $order->delivered_at?->subHours(2) : null,
                                    'completed' => $order->status === 'delivered'
                                ],
                                [
                                    'status' => 'delivered',
                                    'title' => 'Delivered',
                                    'description' => 'Package has been successfully delivered',
                                    'icon' => 'home',
                                    'timestamp' => $order->delivered_at,
                                    'completed' => $order->status === 'delivered'
                                ]
                            ];
                        @endphp

                        <div class="timeline">
                            @foreach($trackingSteps as $index => $step)
                            <div class="timeline-step {{ $step['completed'] ? 'completed' : '' }} {{ $order->status === $step['status'] ? 'current' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-{{ $step['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="timeline-title mb-1">{{ $step['title'] }}</h6>
                                        @if($step['timestamp'])
                                        <small class="timeline-time text-muted">
                                            {{ $step['timestamp']->format('M d, Y • H:i') }}
                                        </small>
                                        @endif
                                    </div>
                                    <p class="timeline-description mb-0">{{ $step['description'] }}</p>

                                    @if($step['status'] === 'delivered' && $step['completed'])
                                    <div class="mt-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Delivery Confirmed
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Tracking Map (Placeholder) -->
            @if($order->status === 'shipped' || $order->status === 'delivered')
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>Live Tracking Map
                    </h6>
                </div>
                <div class="card-body">
                    <div id="trackingMap" style="height: 300px; background: #f8f9fa; border-radius: 8px;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-map text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted mb-2">Real-time Tracking Map</h6>
                                <p class="text-muted small mb-0">
                                    Track your package's current location and estimated delivery time
                                </p>
                                <button class="btn btn-primary btn-sm mt-3" onclick="initializeMap()">
                                    <i class="fas fa-map-marker-alt me-1"></i>Show Live Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Tracking Information Sidebar -->
        <div class="col-lg-4">
            <!-- Estimated Delivery -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Delivery Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="delivery-info">
                        @if($order->status !== 'delivered')
                        <div class="estimated-delivery mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <small class="text-muted">Estimated Delivery</small>
                            </div>
                            <div class="fw-bold text-success fs-5">
                                @php
                                    $estimatedDelivery = $order->shipped_at
                                        ? $order->shipped_at->addDays(3)
                                        : now()->addDays(5);
                                @endphp
                                {{ $estimatedDelivery->format('M d, Y') }}
                            </div>
                            <small class="text-muted">
                                {{ $estimatedDelivery->diffForHumans() }}
                            </small>
                        </div>
                        @else
                        <div class="delivered-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small class="text-muted">Delivered On</small>
                            </div>
                            <div class="fw-bold text-success fs-5">
                                {{ $order->delivered_at->format('M d, Y') }}
                            </div>
                            <small class="text-muted">
                                {{ $order->delivered_at->format('H:i') }} • {{ $order->delivered_at->diffForHumans() }}
                            </small>
                        </div>
                        @endif

                        <hr>

                        <!-- Shipping Method -->
                        <div class="shipping-method mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-shipping-fast text-primary me-2"></i>
                                <small class="text-muted">Shipping Method</small>
                            </div>
                            <div class="fw-medium">{{ $order->shippingMethod->name ?? 'Standard Shipping' }}</div>
                            @if($order->tracking_number)
                            <div class="mt-1">
                                <small class="text-muted">Tracking Number</small>
                                <div class="fw-medium">{{ $order->tracking_number }}</div>
                                <button class="btn btn-link btn-sm p-0 mt-1" onclick="copyTrackingNumber()">
                                    <i class="fas fa-copy me-1"></i>Copy
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Delivery Address
                    </h6>
                </div>
                <div class="card-body">
                    <div class="delivery-address">
                        <div class="fw-medium mb-1">{{ $order->shipping_name }}</div>
                        <div class="text-muted mb-2">{{ $order->shipping_phone }}</div>
                        <div class="address-text">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                            {{ $order->shipping_country }}
                        </div>
                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="showMapLocation()">
                            <i class="fas fa-map me-1"></i>View on Map
                        </button>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>Package Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="package-details">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Items</small>
                                <div class="fw-medium">{{ $order->items->sum('quantity') }} items</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Weight</small>
                                <div class="fw-medium">
                                    @php
                                        $totalWeight = $order->items->sum(function($item) {
                                            return ($item->product->weight_grams ?? 500) * $item->quantity;
                                        });
                                    @endphp
                                    {{ number_format($totalWeight / 1000, 1) }} kg
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Package Value</small>
                                <div class="fw-medium">{{ \App\Helpers\PriceHelper::format($order->subtotal_cents) }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Shipping Cost</small>
                                <div class="fw-medium">
                                    @if($order->shipping_cents > 0)
                                        {{ \App\Helpers\PriceHelper::format($order->shipping_cents) }}
                                    @else
                                        <span class="text-success">Free</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Package Items Preview -->
                        <div class="package-items">
                            <small class="text-muted">Items in this package:</small>
                            <div class="items-preview mt-2">
                                @foreach($order->items->take(3) as $item)
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ $item->product->primary_image_url }}"
                                         alt="{{ $item->product_name }}"
                                         class="rounded me-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium small">{{ Str::limit($item->product_name, 30) }}</div>
                                        <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                    </div>
                                </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                <small class="text-muted">+{{ $order->items->count() - 3 }} more items</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-headset me-2"></i>Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Having issues with your delivery? Our support team is here to help.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="tel:+6281234567890" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone me-2"></i>Call Support
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-envelope me-2"></i>Send Message
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="openLiveChat()">
                            <i class="fas fa-comments me-2"></i>Live Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Updates -->
    @if($order->status === 'shipped' || $order->status === 'delivered')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Tracking Updates
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tracking-updates">
                        @php
                            $trackingUpdates = [
                                [
                                    'timestamp' => $order->delivered_at ?? $order->shipped_at,
                                    'status' => $order->status === 'delivered' ? 'Package delivered successfully' : 'Package shipped from facility',
                                    'location' => $order->status === 'delivered' ? $order->shipping_city : 'Distribution Center',
                                    'details' => $order->status === 'delivered' ? 'Delivered to recipient' : 'Package in transit',
                                    'icon' => $order->status === 'delivered' ? 'check-circle' : 'truck',
                                    'type' => $order->status === 'delivered' ? 'success' : 'primary'
                                ],
                                [
                                    'timestamp' => $order->shipped_at ? $order->shipped_at->subHours(2) : null,
                                    'status' => 'Package departed from origin facility',
                                    'location' => 'Jakarta Distribution Center',
                                    'details' => 'Package loaded onto delivery vehicle',
                                    'icon' => 'truck-loading',
                                    'type' => 'info'
                                ],
                                [
                                    'timestamp' => $order->shipped_at ? $order->shipped_at->subHours(6) : null,
                                    'status' => 'Package processed at facility',
                                    'location' => 'Jakarta Sorting Center',
                                    'details' => 'Package sorted and prepared for shipping',
                                    'icon' => 'cogs',
                                    'type' => 'secondary'
                                ],
                                [
                                    'timestamp' => $order->confirmed_at,
                                    'status' => 'Package prepared for shipment',
                                    'location' => 'TokoSaya Warehouse',
                                    'details' => 'Items packaged and ready for pickup',
                                    'icon' => 'box',
                                    'type' => 'secondary'
                                ]
                            ];
                        @endphp

                        @foreach($trackingUpdates as $update)
                            @if($update['timestamp'])
                            <div class="tracking-update-item mb-3 pb-3 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="update-icon bg-{{ $update['type'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-{{ $update['icon'] }}"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="update-content">
                                            <h6 class="mb-1">{{ $update['status'] }}</h6>
                                            <p class="text-muted small mb-1">{{ $update['details'] }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $update['location'] }}
                                                </small>
                                                <small class="text-muted">
                                                    {{ $update['timestamp']->format('M d, Y • H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delivery Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="deliveryMap" style="height: 400px; background: #f8f9fa; border-radius: 8px;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Loading map...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.tracking-timeline {
    position: relative;
}

.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 30px;
    position: relative;
}

.timeline-step:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 24px;
    top: 48px;
    bottom: -30px;
    width: 2px;
    background: #e9ecef;
    z-index: 1;
}

.timeline-step.completed:not(:last-child)::after {
    background: #198754;
}

.timeline-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 20px;
    border: 3px solid #e9ecef;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.timeline-step.completed .timeline-icon {
    background: #198754;
    color: white;
    border-color: #198754;
}

.timeline-step.current .timeline-icon {
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

.timeline-content {
    flex-grow: 1;
    padding-top: 8px;
}

.timeline-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-time {
    font-size: 0.85rem;
    color: #6c757d;
}

.timeline-description {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 5px;
}

.update-icon {
    font-size: 1rem;
}

.tracking-update-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.delivery-info, .shipping-method, .package-details {
    transition: all 0.3s ease;
}

.items-preview img {
    border: 1px solid #dee2e6;
}

@media (max-width: 768px) {
    .timeline-step {
        margin-bottom: 25px;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        margin-right: 15px;
        font-size: 1rem;
    }

    .timeline-step:not(:last-child)::after {
        left: 20px;
        top: 40px;
        bottom: -25px;
    }

    .timeline-content {
        padding-top: 4px;
    }

    .timeline-title {
        font-size: 0.9rem;
    }
}

.package-items .items-preview {
    max-height: 200px;
    overflow-y: auto;
}

.package-items .items-preview::-webkit-scrollbar {
    width: 4px;
}

.package-items .items-preview::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.package-items .items-preview::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 2px;
}

.package-items .items-preview::-webkit-scrollbar-thumb:hover {
    background: #999;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh tracking every 2 minutes for shipped orders
    @if($order->status === 'shipped')
    setInterval(function() {
        refreshTracking();
    }, 120000);
    @endif
});

// Refresh tracking information
function refreshTracking() {
    const refreshBtn = document.querySelector('[onclick="refreshTracking()"]');
    const originalIcon = refreshBtn.querySelector('i');

    // Show loading state
    originalIcon.className = 'fas fa-spinner fa-spin me-1';
    refreshBtn.disabled = true;

    fetch(`/orders/{{ $order->id }}/tracking-status`)
        .then(response => response.json())
        .then(data => {
            if (data.status_changed) {
                showAlert('Order status updated!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('Tracking information is up to date', 'info');
            }
        })
        .catch(error => {
            showAlert('Failed to refresh tracking', 'error');
        })
        .finally(() => {
            // Restore button state
            originalIcon.className = 'fas fa-sync-alt me-1';
            refreshBtn.disabled = false;
        });
}

// Copy tracking number
function copyTrackingNumber() {
    const trackingNumber = '{{ $order->tracking_number }}';
    navigator.clipboard.writeText(trackingNumber).then(function() {
        showAlert('Tracking number copied!', 'success');
    }, function() {
        showAlert('Failed to copy tracking number', 'error');
    });
}

// Show delivery location on map
function showMapLocation() {
    const modal = new bootstrap.Modal(document.getElementById('mapModal'));
    modal.show();

    // Initialize map when modal is shown
    setTimeout(() => {
        initializeDeliveryMap();
    }, 500);
}

// Initialize map for package location
function initializeMap() {
    const mapContainer = document.getElementById('trackingMap');
    mapContainer.innerHTML = `
        <div class="d-flex align-items-center justify-content-center h-100">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Loading live tracking...</p>
            </div>
        </div>
    `;

    // Simulate map loading
    setTimeout(() => {
        mapContainer.innerHTML = `
            <div class="position-relative h-100 bg-light rounded d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt text-primary mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-primary mb-2">Package Location</h6>
                    <p class="text-muted small mb-0">
                        📍 In transit to {{ $order->shipping_city }}<br>
                        Estimated arrival: {{ ($order->shipped_at ? $order->shipped_at->addDays(3) : now()->addDays(5))->format('M d, Y') }}
                    </p>
                </div>
                <div class="position-absolute top-0 end-0 m-3">
                    <button class="btn btn-sm btn-light" onclick="refreshTracking()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        `;
    }, 2000);
}

// Initialize delivery map in modal
function initializeDeliveryMap() {
    const mapContainer = document.getElementById('deliveryMap');

    // Simulate map loading
    setTimeout(() => {
        mapContainer.innerHTML = `
            <div class="position-relative h-100 bg-light rounded d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <i class="fas fa-map-marker-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-dark mb-2">Delivery Address</h6>
                    <p class="text-muted small mb-0">
                        📍 {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}
                    </p>
                </div>
            </div>
        `;
    }, 1000);
}

// Open live chat
function openLiveChat() {
    showAlert('Live chat will be available soon!', 'info');
    // Here you would integrate with your chat system
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

// Add smooth scrolling for any anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush
@endsection
