@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Order #{{ $order->order_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-file-invoice me-2"></i>View Invoice
            </a>
            <a href="{{ route('admin.orders.print-label', $order) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-shipping-fast me-2"></i>Print Label
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>

    <!-- Order Status Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="status-indicator me-3">
                            <span class="badge bg-{{ $order->status_color }} fs-6">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Order Status</h6>
                            <small class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric">
                        <span class="badge bg-{{ $order->payment_status_color }} fs-6">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                        <div class="small text-muted mt-1">Payment Status</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric">
                        <div class="h4 mb-0">{{ format_currency($order->total_cents) }}</div>
                        <small class="text-muted">Total Amount</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric">
                        <div class="h4 mb-0">{{ $order->items->sum('quantity') }}</div>
                        <small class="text-muted">Total Items</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-end gap-2">
                        @if($order->status === 'pending')
                            <button type="button" class="btn btn-sm btn-success" onclick="updateStatus('confirmed')">
                                <i class="fas fa-check me-1"></i>Confirm
                            </button>
                        @elseif($order->status === 'confirmed')
                            <button type="button" class="btn btn-sm btn-info" onclick="updateStatus('processing')">
                                <i class="fas fa-cog me-1"></i>Process
                            </button>
                        @elseif($order->status === 'processing')
                            <button type="button" class="btn btn-sm btn-primary" onclick="updateStatus('shipped')">
                                <i class="fas fa-shipping-fast me-1"></i>Ship
                            </button>
                        @elseif($order->status === 'shipped')
                            <button type="button" class="btn btn-sm btn-success" onclick="updateStatus('delivered')">
                                <i class="fas fa-check-circle me-1"></i>Delivered
                            </button>
                        @endif

                        @if(!in_array($order->status, ['cancelled', 'delivered']))
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelOrder()">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Order Items</h5>
                    <span class="badge bg-secondary">{{ $order->items->count() }} products</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th width="100">SKU</th>
                                    <th width="80">Qty</th>
                                    <th width="120">Unit Price</th>
                                    <th width="120">Total</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->primary_image)
                                                    <img src="{{ $item->product->primary_image }}"
                                                         alt="{{ $item->product_name }}"
                                                         class="img-thumbnail me-3"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if($item->product)
                                                            <a href="{{ route('admin.products.show', $item->product) }}"
                                                               class="text-decoration-none">
                                                                {{ $item->product_name }}
                                                            </a>
                                                        @else
                                                            {{ $item->product_name }}
                                                            <small class="text-muted">(Product deleted)</small>
                                                        @endif
                                                    </h6>
                                                    @if($item->variant_name)
                                                        <small class="text-muted">{{ $item->variant_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $item->product_sku }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ format_currency($item->unit_price_cents) }}
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ format_currency($item->total_price_cents) }}</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($item->product)
                                                    <a href="{{ route('admin.products.show', $item->product) }}"
                                                       class="btn btn-outline-primary btn-sm" title="View Product">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if($order->status === 'delivered' && !$item->review)
                                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            title="Request Review">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Order Placed</h6>
                                <p class="timeline-desc">Order was created by customer</p>
                                <small class="timeline-time">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>

                        @if($order->confirmed_at)
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Confirmed</h6>
                                    <p class="timeline-desc">Order confirmed and payment verified</p>
                                    <small class="timeline-time">{{ $order->confirmed_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-light"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Confirmation</h6>
                                    <p class="timeline-desc">Waiting for confirmation</p>
                                </div>
                            </div>
                        @endif

                        @if($order->shipped_at)
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Shipped</h6>
                                    <p class="timeline-desc">
                                        Package handed to carrier
                                        @if($order->tracking_number)
                                            <br>Tracking: <strong>{{ $order->tracking_number }}</strong>
                                        @endif
                                    </p>
                                    <small class="timeline-time">{{ $order->shipped_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-light"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Shipping</h6>
                                    <p class="timeline-desc">Waiting for shipment</p>
                                </div>
                            </div>
                        @endif

                        @if($order->delivered_at)
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Delivered</h6>
                                    <p class="timeline-desc">Package delivered to customer</p>
                                    <small class="timeline-time">{{ $order->delivered_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-light"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Delivery</h6>
                                    <p class="timeline-desc">Awaiting delivery</p>
                                </div>
                            </div>
                        @endif

                        @if($order->cancelled_at)
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Cancelled</h6>
                                    <p class="timeline-desc">Order was cancelled</p>
                                    <small class="timeline-time">{{ $order->cancelled_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Internal Notes</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addNote()">
                        <i class="fas fa-plus me-1"></i>Add Note
                    </button>
                </div>
                <div class="card-body">
                    @if($order->internal_notes)
                        <div class="alert alert-info">
                            <strong>Order Notes:</strong><br>
                            {{ $order->internal_notes }}
                        </div>
                    @endif

                    <!-- Dynamic notes would be loaded here -->
                    <div id="notesContainer">
                        @forelse($order->notes ?? [] as $note)
                            <div class="note-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $note->user->name }}</strong>
                                    <small class="text-muted">{{ $note->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                <p class="mb-0 mt-2">{{ $note->content }}</p>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3">
                                No internal notes yet. Add notes to track order progress and communicate with team.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="customer-info">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar me-3">
                                @if($order->user->avatar)
                                    <img src="{{ $order->user->avatar }}" class="rounded-circle"
                                         width="50" height="50" alt="{{ $order->user->full_name }}">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $order->user->full_name }}</h6>
                                <small class="text-muted">Customer since {{ $order->user->created_at->format('M Y') }}</small>
                            </div>
                        </div>

                        <div class="contact-info">
                            <div class="info-item mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
                            </div>
                            @if($order->shipping_phone)
                                <div class="info-item mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <a href="tel:{{ $order->shipping_phone }}">{{ $order->shipping_phone }}</a>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="customer-stats">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat">
                                        <div class="h5 mb-0">{{ $order->user->orders->count() }}</div>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat">
                                        <div class="h5 mb-0">{{ format_currency($order->user->orders->sum('total_cents')) }}</div>
                                        <small class="text-muted">Total Spent</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user me-2"></i>View Customer Profile
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="sendCustomerEmail()">
                                <i class="fas fa-envelope me-2"></i>Send Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <div class="shipping-info">
                        <h6 class="mb-2">Shipping Address</h6>
                        <address>
                            <strong>{{ $order->shipping_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                            {{ $order->shipping_country }}
                        </address>

                        @if($order->shipping_method)
                            <h6 class="mb-2">Shipping Method</h6>
                            <p class="mb-0">{{ $order->shipping_method->name }}</p>
                            <small class="text-muted">
                                {{ format_currency($order->shipping_cents) }}
                                @if($order->shipping_method->estimated_min_days && $order->shipping_method->estimated_max_days)
                                    • {{ $order->shipping_method->estimated_min_days }}-{{ $order->shipping_method->estimated_max_days }} days
                                @endif
                            </small>
                        @endif

                        @if($order->tracking_number)
                            <h6 class="mb-2 mt-3">Tracking Information</h6>
                            <div class="tracking-info">
                                <code>{{ $order->tracking_number }}</code>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2"
                                        onclick="trackPackage('{{ $order->tracking_number }}')">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="payment-info">
                        @if($order->payment_method)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Payment Method:</span>
                                <strong>{{ $order->payment_method->name }}</strong>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Status:</span>
                            <span class="badge bg-{{ $order->payment_status_color }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>

                        @if($order->payments->count() > 0)
                            <hr>
                            <h6 class="mb-2">Payment History</h6>
                            @foreach($order->payments as $payment)
                                <div class="payment-item mb-2 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ format_currency($payment->amount_cents) }}</span>
                                        <span class="badge bg-{{ $payment->status_color }} badge-sm">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        {{ $payment->created_at->format('M d, Y h:i A') }}
                                        @if($payment->transaction_id)
                                            • ID: {{ $payment->transaction_id }}
                                        @endif
                                    </small>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="order-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>{{ format_currency($order->subtotal_cents) }}</span>
                        </div>

                        @if($order->discount_cents > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>
                                    Discount
                                    @if($order->coupon_code)
                                        ({{ $order->coupon_code }})
                                    @endif
                                    :
                                </span>
                                <span>-{{ format_currency($order->discount_cents) }}</span>
                            </div>
                        @endif

                        @if($order->tax_cents > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>{{ format_currency($order->tax_cents) }}</span>
                            </div>
                        @endif

                        @if($order->shipping_cents > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>{{ format_currency($order->shipping_cents) }}</span>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="h5 mb-0">{{ format_currency($order->total_cents) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="sendCustomerEmail()">
                            <i class="fas fa-envelope me-2"></i>Email Customer
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="duplicateOrder()">
                            <i class="fas fa-copy me-2"></i>Duplicate Order
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="printOrder()">
                            <i class="fas fa-print me-2"></i>Print Order
                        </button>

                        @if($order->status !== 'cancelled')
                            <hr>
                            <button type="button" class="btn btn-outline-danger" onclick="cancelOrder()">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        @endif

                        @if($order->payment_status === 'paid' && $order->status === 'delivered')
                            <button type="button" class="btn btn-outline-warning" onclick="initiateRefund()">
                                <i class="fas fa-undo me-2"></i>Process Refund
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="newStatus" name="status">
                    <div class="mb-3" id="trackingNumberField" style="display: none;">
                        <label class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number"
                               placeholder="Enter tracking number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3"
                                  placeholder="Add any notes for this status update"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="notify_customer"
                                   id="notifyCustomer" checked>
                            <label class="form-check-label" for="notifyCustomer">
                                Send notification to customer
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Internal Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addNoteForm">
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" rows="4" required
                                  placeholder="Add your note here..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitNote()">Add Note</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelOrderForm">
                    <div class="mb-3">
                        <label class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" name="reason" rows="3" required
                                  placeholder="Please specify the reason for cancelling this order..."></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="notify_customer"
                                   id="notifyCustomerCancel" checked>
                            <label class="form-check-label" for="notifyCustomerCancel">
                                Send notification to customer
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitCancelOrder()">Confirm Cancellation</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function updateStatus(status) {
        $('#newStatus').val(status);

        // Show tracking number field only for shipped status
        if (status === 'shipped') {
            $('#trackingNumberField').show();
        } else {
            $('#trackingNumberField').hide();
        }

        $('#statusUpdateModal').modal('show');
    }

    function submitStatusUpdate() {
        const form = $('#statusUpdateForm');
        const url = "{{ route('admin.orders.update-status', $order) }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert('An error occurred while updating the status.');
            }
        });
    }

    function addNote() {
        $('#addNoteModal').modal('show');
    }

    function submitNote() {
        const form = $('#addNoteForm');
        const url = "{{ route('admin.orders.add-note', $order) }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert('An error occurred while adding the note.');
            }
        });
    }

    function cancelOrder() {
        $('#cancelOrderModal').modal('show');
    }

    function submitCancelOrder() {
        const form = $('#cancelOrderForm');
        const url = "{{ route('admin.orders.cancel', $order) }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert('An error occurred while cancelling the order.');
            }
        });
    }

    function sendCustomerEmail() {
        // Implementation for sending email to customer
        alert('Email functionality would be implemented here');
    }

    function duplicateOrder() {
        // Implementation for duplicating order
        alert('Order duplication functionality would be implemented here');
    }

    function printOrder() {
        window.print();
    }

    function initiateRefund() {
        // Implementation for initiating refund
        alert('Refund functionality would be implemented here');
    }

    function trackPackage(trackingNumber) {
        // Implementation for tracking package
        alert('Tracking functionality would be implemented for: ' + trackingNumber);
    }
</script>
@endsection
