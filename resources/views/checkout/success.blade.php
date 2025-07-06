@extends('layouts.app')

@section('title', 'Order Successful - TokoSaya')
@section('meta_description', 'Your order has been placed successfully. Thank you for shopping with TokoSaya!')

@section('content')
<div class="success-container py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Header -->
                <div class="success-header text-center mb-5">
                    <div class="success-icon mb-4">
                        <div class="success-checkmark">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <h1 class="display-5 fw-bold text-success mb-3">Order Successful!</h1>
                    <p class="lead text-muted">
                        Thank you for your purchase. Your order has been placed successfully and is being processed.
                    </p>
                </div>

                <!-- Order Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt text-primary me-2"></i>
                                    Order Details
                                </h5>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-success px-3 py-2">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="order-info">
                                    <h6 class="fw-bold mb-3">Order Information</h6>
                                    <div class="info-item mb-2">
                                        <span class="text-muted">Order Number:</span>
                                        <strong class="ms-2">#{{ $order->order_number }}</strong>
                                        <button class="btn btn-sm btn-outline-secondary ms-2"
                                                onclick="copyToClipboard('{{ $order->order_number }}')"
                                                title="Copy order number">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="info-item mb-2">
                                        <span class="text-muted">Order Date:</span>
                                        <strong class="ms-2">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</strong>
                                    </div>
                                    <div class="info-item mb-2">
                                        <span class="text-muted">Payment Method:</span>
                                        <strong class="ms-2">{{ $order->paymentMethod->name }}</strong>
                                    </div>
                                    <div class="info-item">
                                        <span class="text-muted">Total Amount:</span>
                                        <strong class="ms-2 text-primary">{{ format_currency($order->total_cents) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="shipping-info">
                                    <h6 class="fw-bold mb-3">Shipping Information</h6>
                                    <div class="shipping-address">
                                        <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                                        <p class="mb-1">{{ $order->shipping_phone }}</p>
                                        <p class="mb-1">{{ $order->shipping_address }}</p>
                                        <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="order-items">
                            <h6 class="fw-bold mb-3">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->product->primary_image }}"
                                                         alt="{{ $item->product_name }}"
                                                         class="me-3 rounded"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1">{{ $item->product_name }}</h6>
                                                        @if($item->variant_name)
                                                            <small class="text-muted">{{ $item->variant_name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ format_currency($item->unit_price_cents) }}</td>
                                            <td><strong>{{ format_currency($item->total_price_cents) }}</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td><strong>{{ format_currency($order->subtotal_cents) }}</strong></td>
                                        </tr>
                                        @if($order->shipping_cents > 0)
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                            <td><strong>{{ format_currency($order->shipping_cents) }}</strong></td>
                                        </tr>
                                        @endif
                                        @if($order->tax_cents > 0)
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                            <td><strong>{{ format_currency($order->tax_cents) }}</strong></td>
                                        </tr>
                                        @endif
                                        @if($order->discount_cents > 0)
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Discount:</strong></td>
                                            <td><strong class="text-success">-{{ format_currency($order->discount_cents) }}</strong></td>
                                        </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong class="text-primary">{{ format_currency($order->total_cents) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions (if needed) -->
                @if($order->payment_status === 'pending' && $order->paymentMethod->code === 'bank_transfer')
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="mb-0 text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Payment Instructions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Complete Your Payment</h6>
                            <p class="mb-3">Please transfer the exact amount to complete your order:</p>
                            <div class="payment-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Bank:</strong> Bank Central Asia (BCA)<br>
                                        <strong>Account Number:</strong> 1234567890<br>
                                        <strong>Account Name:</strong> PT TokoSaya Indonesia
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Amount:</strong> {{ format_currency($order->total_cents) }}<br>
                                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                                        <strong>Payment Due:</strong> {{ $order->created_at->addHours(24)->format('F j, Y \a\t g:i A') }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Please include your order number as the transfer reference.
                                    Payment confirmation will be processed within 1-2 hours during business hours.
                                </small>
                            </div>
                        </div>

                        <!-- Upload Payment Proof -->
                        <div class="upload-proof mt-3">
                            <h6>Upload Payment Proof (Optional)</h6>
                            <form action="{{ route('orders.upload-proof', $order) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                                    <div class="form-text">Upload a clear photo of your transfer receipt</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>
                                    Upload Proof
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Next Steps -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks text-primary me-2"></i>
                            What's Next?
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="next-steps">
                            <div class="step-item d-flex mb-3">
                                <div class="step-icon me-3">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="mb-1">Order Confirmed</h6>
                                    <p class="text-muted mb-0">Your order has been received and is being processed.</p>
                                </div>
                            </div>

                            @if($order->payment_status === 'pending')
                            <div class="step-item d-flex mb-3">
                                <div class="step-icon me-3">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="mb-1">Payment Pending</h6>
                                    <p class="text-muted mb-0">Complete your payment to proceed with order processing.</p>
                                </div>
                            </div>
                            @else
                            <div class="step-item d-flex mb-3">
                                <div class="step-icon me-3">
                                    <i class="fas fa-credit-card text-success"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="mb-1">Payment Confirmed</h6>
                                    <p class="text-muted mb-0">Your payment has been processed successfully.</p>
                                </div>
                            </div>
                            @endif

                            <div class="step-item d-flex mb-3">
                                <div class="step-icon me-3">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="mb-1">Order Processing</h6>
                                    <p class="text-muted mb-0">Your items will be prepared and packaged for shipping.</p>
                                </div>
                            </div>

                            <div class="step-item d-flex">
                                <div class="step-icon me-3">
                                    <i class="fas fa-shipping-fast text-muted"></i>
                                </div>
                                <div class="step-content">
                                    <h6 class="mb-1">Shipping & Delivery</h6>
                                    <p class="text-muted mb-0">Track your package and receive it at your doorstep.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons text-center">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>
                                View Order Details
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('orders.track', $order->order_number) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Track Order
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Customer Support -->
                <div class="customer-support mt-5 text-center">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-3">Need Help with Your Order?</h6>
                            <p class="text-muted mb-3">
                                Our customer support team is here to help you with any questions or concerns.
                            </p>
                            <div class="support-buttons">
                                <a href="https://wa.me/6281234567890?text=Hi, I need help with order {{ $order->order_number }}"
                                   class="btn btn-success me-2" target="_blank">
                                    <i class="fab fa-whatsapp me-1"></i>
                                    WhatsApp
                                </a>
                                <a href="mailto:support@tokosaya.com?subject=Order {{ $order->order_number }} - Support Request"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Newsletter Signup -->
                <div class="newsletter-signup mt-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="mb-3">Stay Updated!</h6>
                            <p class="mb-3">
                                Subscribe to our newsletter for exclusive deals and new product announcements.
                            </p>
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                                @csrf
                                <div class="input-group">
                                    <input type="email"
                                           class="form-control"
                                           name="email"
                                           placeholder="Enter your email"
                                           value="{{ auth()->user()->email ?? '' }}"
                                           required>
                                    <button type="submit" class="btn btn-light">
                                        Subscribe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Celebration Modal -->
<div class="modal fade" id="celebrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-5">
                <div class="celebration-icon mb-4">
                    <i class="fas fa-trophy text-warning" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-3">🎉 Congratulations!</h4>
                <p class="mb-4">
                    You've successfully placed your order! As a token of our appreciation,
                    here's a special discount for your next purchase.
                </p>
                <div class="coupon-code mb-4">
                    <div class="alert alert-success">
                        <h6 class="mb-2">Use coupon code:</h6>
                        <h4 class="mb-0">
                            <strong>WELCOME10</strong>
                            <button class="btn btn-sm btn-outline-success ms-2"
                                    onclick="copyToClipboard('WELCOME10')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </h4>
                        <small>Get 10% off your next order</small>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Awesome, Thanks!
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.success-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 50vh;
    position: relative;
}

.success-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.container {
    position: relative;
    z-index: 1;
}

.success-checkmark {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    animation: bounceIn 0.8s ease-out;
}

.success-checkmark i {
    font-size: 3rem;
    color: white;
    animation: checkmark 0.6s ease-out 0.3s both;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.step-item {
    padding: 1rem;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.step-item:hover {
    background-color: #f8fafc;
    transform: translateX(5px);
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.step-icon i {
    font-size: 1.2rem;
}

.action-buttons .btn {
    border-radius: 12px;
    padding: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.newsletter-form .form-control {
    border: none;
    border-radius: 12px 0 0 12px;
    padding: 0.75rem 1rem;
}

.newsletter-form .btn {
    border-radius: 0 12px 12px 0;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.celebration-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.coupon-code .alert {
    border: 2px dashed #10b981;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #374151;
    background: #f9fafb;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.payment-details {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1rem 0;
}

@media (max-width: 768px) {
    .success-checkmark {
        width: 80px;
        height: 80px;
    }

    .success-checkmark i {
        font-size: 2rem;
    }

    .display-5 {
        font-size: 2rem;
    }

    .step-item {
        margin-bottom: 1rem;
    }

    .action-buttons .btn {
        margin-bottom: 0.75rem;
    }

    .support-buttons .btn {
        margin-bottom: 0.5rem;
    }
}

/* Confetti Animation */
.confetti {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
}

.confetti-piece {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #f59e0b;
    animation: confetti-fall 3s linear infinite;
}

.confetti-piece:nth-child(odd) {
    background: #ef4444;
    animation-delay: -0.5s;
}

.confetti-piece:nth-child(3n) {
    background: #10b981;
    animation-delay: -1s;
}

.confetti-piece:nth-child(4n) {
    background: #3b82f6;
    animation-delay: -1.5s;
}

@keyframes confetti-fall {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show celebration modal after page load
    setTimeout(() => {
        const celebrationModal = new bootstrap.Modal(document.getElementById('celebrationModal'));
        celebrationModal.show();
    }, 1500);

    // Create confetti animation
    createConfetti();

    // Auto-copy order number functionality
    const orderNumber = '{{ $order->order_number }}';

    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Subscribing...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Successfully subscribed to newsletter!', 'success');
                    this.reset();
                } else {
                    showToast(data.message || 'Subscription failed', 'error');
                }
            })
            .catch(error => {
                showToast('Subscription failed', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Payment proof upload
    const uploadForm = document.querySelector('form[action*="upload-proof"]');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            if (!fileInput.files.length) {
                e.preventDefault();
                showToast('Please select a file to upload', 'warning');
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                e.preventDefault();
                showToast('File size must be less than 5MB', 'error');
                return;
            }

            if (!file.type.startsWith('image/')) {
                e.preventDefault();
                showToast('Please upload an image file', 'error');
                return;
            }
        });
    }

    // Track order button with analytics
    const trackButton = document.querySelector('a[href*="track"]');
    if (trackButton) {
        trackButton.addEventListener('click', function() {
            // Track event for analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'track_order', {
                    'order_id': orderNumber,
                    'event_category': 'order',
                    'event_label': 'track_from_success_page'
                });
            }
        });
    }

    // Auto-refresh order status every 30 seconds if payment is pending
    @if($order->payment_status === 'pending')
    setInterval(function() {
        fetch(`/api/orders/{{ $order->id }}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.payment_status === 'paid') {
                    location.reload();
                }
            })
            .catch(error => console.log('Status check failed:', error));
    }, 30000);
    @endif
});

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        showToast('Copied to clipboard!', 'success');
    } catch (err) {
        showToast('Could not copy to clipboard', 'error');
    }

    document.body.removeChild(textArea);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0 show`;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; min-width: 300px;';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

function createConfetti() {
    const confettiContainer = document.createElement('div');
    confettiContainer.className = 'confetti';
    document.body.appendChild(confettiContainer);

    for (let i = 0; i < 100; i++) {
        const confettiPiece = document.createElement('div');
        confettiPiece.className = 'confetti-piece';
        confettiPiece.style.left = Math.random() * 100 + '%';
        confettiPiece.style.animationDelay = Math.random() * 3 + 's';
        confettiPiece.style.animationDuration = (Math.random() * 2 + 2) + 's';
        confettiContainer.appendChild(confettiPiece);
    }

    // Remove confetti after animation
    setTimeout(() => {
        confettiContainer.remove();
    }, 5000);
}

// Share functionality
function shareOrder() {
    if (navigator.share) {
        navigator.share({
            title: 'TokoSaya Order Confirmation',
            text: `I just placed an order on TokoSaya! Order #{{ $order->order_number }}`,
            url: window.location.href
        }).catch(console.error);
    } else {
        copyToClipboard(window.location.href);
        showToast('Order URL copied to clipboard!', 'success');
    }
}

// Print order functionality
function printOrder() {
    window.print();
}
</script>
@endpush
