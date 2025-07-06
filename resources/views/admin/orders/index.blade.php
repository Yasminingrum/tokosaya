@extends('layouts.admin')

@section('title', 'Order Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Order Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>Export Orders
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportOrders('excel')">
                    <i class="fas fa-file-excel me-2"></i>Export to Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportOrders('csv')">
                    <i class="fas fa-file-csv me-2"></i>Export to CSV
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportOrders('pdf')">
                    <i class="fas fa-file-pdf me-2"></i>Export to PDF
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="printOrderLabels()">
                    <i class="fas fa-print me-2"></i>Print Shipping Labels
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="row mb-4">
        @foreach([
            ['total_orders', 'Total Orders', 'fas fa-shopping-cart', 'primary'],
            ['pending_orders', 'Pending', 'fas fa-clock', 'warning'],
            ['processing_orders', 'Processing', 'fas fa-cog', 'info'],
            ['shipped_orders', 'Shipped', 'fas fa-shipping-fast', 'primary'],
            ['delivered_orders', 'Delivered', 'fas fa-check-circle', 'success'],
            ['total_revenue', 'Revenue', 'fas fa-dollar-sign', 'secondary']
        ] as $stat)
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card bg-{{ $stat[3] }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">{{ $stat[1] }}</h6>
                            <h3 class="mb-0" @if($stat[0] === 'total_revenue') style="font-size: 1.2rem;" @endif>
                                {{ $stat[0] === 'total_revenue' ? format_currency($stats[$stat[0]]) : number_format($stats[$stat[0]]) }}
                            </h3>
                        </div>
                        <div class="align-self-center">
                            <i class="{{ $stat[2] }} fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Search & Filter Orders</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" action="{{ route('admin.orders.index') }}" method="GET">
                <div class="row g-3">
                    <!-- Search Field -->
                    <div class="col-md-3">
                        <label class="form-label">Search Orders</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Order number, customer name, email">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Status Filter -->
                    <div class="col-md-2">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="">All Payments</option>
                            @foreach(['pending', 'paid', 'failed', 'refunded', 'partial'] as $status)
                            <option value="{{ $status }}" {{ request('payment_status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-md-2">
                        <label class="form-label">Date Range</label>
                        <select name="date_range" class="form-select" onchange="toggleCustomDateRange()">
                            <option value="">All Time</option>
                            @foreach([
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'last_7_days' => 'Last 7 Days',
                                'last_30_days' => 'Last 30 Days',
                                'this_month' => 'This Month',
                                'last_month' => 'Last Month',
                                'custom' => 'Custom Range'
                            ] as $value => $label)
                            <option value="{{ $value }}" {{ request('date_range') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-3">
                        <label class="form-label">Sort By</label>
                        <select name="sort_by" class="form-select">
                            @foreach([
                                'created_at_desc' => 'Newest First',
                                'created_at_asc' => 'Oldest First',
                                'total_desc' => 'Highest Value',
                                'total_asc' => 'Lowest Value',
                                'customer_name' => 'Customer Name'
                            ] as $value => $label)
                            <option value="{{ $value }}" {{ request('sort_by') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Custom Date Range (Hidden by default) -->
                <div class="row g-3 mt-2" id="customDateRange" style="display: {{ request('date_range') == 'custom' ? 'flex' : 'none' }};">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <!-- Amount Range -->
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Amount Range</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="min_amount"
                                       value="{{ request('min_amount') }}" placeholder="Min">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="max_amount"
                                       value="{{ request('max_amount') }}" placeholder="Max">
                            </div>
                        </div>
                    </div>

                    <!-- Items Per Page -->
                    <div class="col-md-2">
                        <label class="form-label">Items per page</label>
                        <select name="per_page" class="form-select">
                            @foreach([10, 25, 50, 100] as $perPage)
                            <option value="{{ $perPage }}" {{ request('per_page') == $perPage ? 'selected' : '' }}>
                                {{ $perPage }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-7 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Orders List
                <span class="badge bg-secondary">{{ $orders->total() }} total</span>
            </h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" id="bulkActionsBtn" disabled>
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Status Updates</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('confirmed')">
                            <i class="fas fa-check text-success me-2"></i>Mark as Confirmed
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('processing')">
                            <i class="fas fa-cog text-info me-2"></i>Mark as Processing
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('shipped')">
                            <i class="fas fa-shipping-fast text-primary me-2"></i>Mark as Shipped
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('delivered')">
                            <i class="fas fa-check-circle text-success me-2"></i>Mark as Delivered
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Actions</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkGenerateInvoices()">
                            <i class="fas fa-file-invoice text-info me-2"></i>Generate Invoices
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkPrintLabels()">
                            <i class="fas fa-print text-secondary me-2"></i>Print Shipping Labels
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkExport()">
                            <i class="fas fa-download text-primary me-2"></i>Export Selected
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()"></th>
                                <th width="120">Order</th>
                                <th>Customer</th>
                                <th width="100">Status</th>
                                <th width="100">Payment</th>
                                <th width="120">Total</th>
                                <th width="100">Items</th>
                                <th width="120">Date</th>
                                <th width="140">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><input type="checkbox" class="order-checkbox" value="{{ $order->id }}" onchange="updateBulkActions()"></td>
                                <td>
                                    <div>
                                        <strong class="text-primary">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                                #{{ $order->order_number }}
                                            </a>
                                        </strong>
                                        @if($order->tracking_number)
                                            <br><small class="text-muted">Track: {{ $order->tracking_number }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ $order->user->full_name }}</div>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                        @if($order->shipping_phone)
                                            <br><small class="text-muted"><i class="fas fa-phone fa-xs me-1"></i>{{ $order->shipping_phone }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="status-badges">
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->status === 'pending' && $order->created_at->diffInHours() > 24)
                                            <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Old</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->payment_status_color }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                    @if($order->payment_method)
                                        <br><small class="text-muted">{{ $order->payment_method->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-end">
                                        <strong>{{ format_currency($order->total_cents) }}</strong>
                                        @if($order->coupon_code)
                                            <br><small class="text-success"><i class="fas fa-tag"></i> {{ $order->coupon_code }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $order->items->sum('quantity') }} items</span>
                                    @if($order->items->count() > 1)
                                        <br><small class="text-muted">{{ $order->items->count() }} products</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        <br><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown">
                                                <span class="visually-hidden">More actions</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($order->status === 'pending')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">
                                                        <i class="fas fa-check text-success me-2"></i>Confirm Order
                                                    </a></li>
                                                @endif
                                                @if(in_array($order->status, ['confirmed', 'processing']))
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'shipped')">
                                                        <i class="fas fa-shipping-fast text-primary me-2"></i>Mark Shipped
                                                    </a></li>
                                                @endif
                                                @if($order->status === 'shipped')
                                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                                                        <i class="fas fa-check-circle text-success me-2"></i>Mark Delivered
                                                    </a></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.orders.invoice', $order) }}" target="_blank">
                                                    <i class="fas fa-file-invoice me-2"></i>View Invoice
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.orders.print-label', $order) }}" target="_blank">
                                                    <i class="fas fa-shipping-fast me-2"></i>Print Label
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="sendCustomerNotification({{ $order->id }})">
                                                    <i class="fas fa-envelope me-2"></i>Notify Customer
                                                </a></li>
                                                @if($order->status !== 'cancelled')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder({{ $order->id }})">
                                                        <i class="fas fa-times text-danger me-2"></i>Cancel Order
                                                    </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Orders Found</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'payment_status', 'date_range']))
                            No orders match your current filters. Try adjusting your search criteria.
                        @else
                            No orders have been placed yet. Orders will appear here once customers start purchasing.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'date_range']))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if($orders->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }}
                        of {{ $orders->total() }} results
                    </div>
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Order Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="orderIdInput" name="order_id">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" id="newStatusSelect" name="status" required>
                            <option value="">Select Status</option>
                            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="trackingNumberField" style="display: none;">
                        <label class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" placeholder="Enter tracking number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes for this status update"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="notify_customer" id="notifyCustomer" checked>
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

<!-- Customer Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Customer Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="notificationForm">
                    <input type="hidden" id="notificationOrderId" name="order_id">
                    <div class="mb-3">
                        <label class="form-label">Notification Type</label>
                        <select class="form-select" name="notification_type" required onchange="updateNotificationTemplate()">
                            <option value="">Select Type</option>
                            @foreach([
                                'order_update' => 'Order Update',
                                'shipping_update' => 'Shipping Update',
                                'delivery_reminder' => 'Delivery Reminder',
                                'feedback_request' => 'Feedback Request',
                                'custom' => 'Custom Message'
                            ] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required placeholder="Email subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="6" required placeholder="Your message to the customer"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="include_order_details" id="includeOrderDetails" checked>
                            <label class="form-check-label" for="includeOrderDetails">
                                Include order details in email
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendNotification()">
                    Send Notification
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.table th {
    font-weight: 600;
    border-bottom: 2px solid #e5e5e5;
    background-color: #f8f9fa !important;
}

.status-badges .badge {
    font-size: 0.75rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.order-checkbox:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e5e5e5;
}

.dropdown-header {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.75rem;
    text-transform: uppercase;
}

.table-responsive {
    max-height: 70vh;
}

.fw-medium {
    font-weight: 500;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group-sm .btn {
        padding: 0.125rem 0.25rem;
    }

    .badge {
        font-size: 0.65rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let selectedOrders = [];

// Toggle select all functionality
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');

    orderCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateBulkActions();
}

// Update bulk actions button state
function updateBulkActions() {
    const orderCheckboxes = document.querySelectorAll('.order-checkbox:checked');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    selectedOrders = Array.from(orderCheckboxes).map(cb => cb.value);

    bulkActionsBtn.disabled = selectedOrders.length === 0;
    bulkActionsBtn.textContent = selectedOrders.length > 0
        ? `Bulk Actions (${selectedOrders.length})`
        : 'Bulk Actions';

    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.order-checkbox');
    selectAllCheckbox.indeterminate = selectedOrders.length > 0 && selectedOrders.length < allCheckboxes.length;
    selectAllCheckbox.checked = selectedOrders.length === allCheckboxes.length && allCheckboxes.length > 0;
}

// Custom date range toggle
function toggleCustomDateRange() {
    const dateRange = document.querySelector('select[name="date_range"]').value;
    const customDateRange = document.getElementById('customDateRange');

    if (dateRange === 'custom') {
        customDateRange.style.display = 'flex';
    } else {
        customDateRange.style.display = 'none';
    }
}

// Order status update
function updateOrderStatus(orderId, status) {
    document.getElementById('orderIdInput').value = orderId;
    document.getElementById('newStatusSelect').value = status;

    // Show/hide tracking number field
    const trackingField = document.getElementById('trackingNumberField');
    if (status === 'shipped') {
        trackingField.style.display = 'block';
    } else {
        trackingField.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

function submitStatusUpdate() {
    const form = document.getElementById('statusUpdateForm');
    const formData = new FormData(form);

    fetch('{{ route("admin.orders.update-status") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            location.reload(); // Refresh to show updated status
        } else {
            alert(data.message || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update order status');
    });
}

// Bulk actions
function bulkUpdateStatus(status) {
    if (selectedOrders.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    if (confirm(`Are you sure you want to mark ${selectedOrders.length} orders as ${status}?`)) {
        const formData = new FormData();
        formData.append('status', status);
        formData.append('order_ids', JSON.stringify(selectedOrders));
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("admin.orders.bulk-update-status") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update orders');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update orders');
        });
    }
}

function bulkGenerateInvoices() {
    if (selectedOrders.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    const url = `{{ route('admin.orders.bulk-invoices') }}?order_ids=${selectedOrders.join(',')}`;
    window.open(url, '_blank');
}

function bulkPrintLabels() {
    if (selectedOrders.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    const url = `{{ route('admin.orders.bulk-labels') }}?order_ids=${selectedOrders.join(',')}`;
    window.open(url, '_blank');
}

function bulkExport() {
    if (selectedOrders.length === 0) {
        alert('Please select at least one order.');
        return;
    }

    const url = `{{ route('admin.orders.bulk-export') }}?order_ids=${selectedOrders.join(',')}&format=excel`;
    window.open(url, '_blank');
}

// Export functions
function exportOrders(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);

    const url = `{{ route('admin.orders.export') }}?${params.toString()}`;
    window.open(url, '_blank');
}

function printOrderLabels() {
    const params = new URLSearchParams(window.location.search);
    const url = `{{ route('admin.orders.print-labels') }}?${params.toString()}`;
    window.open(url, '_blank');
}

// Cancel order
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        fetch(`{{ route('admin.orders.cancel', '') }}/${orderId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to cancel order');
        });
    }
}

// Customer notification
function sendCustomerNotification(orderId) {
    document.getElementById('notificationOrderId').value = orderId;
    new bootstrap.Modal(document.getElementById('notificationModal')).show();
}

function updateNotificationTemplate() {
    const type = document.querySelector('select[name="notification_type"]').value;
    const subjectInput = document.querySelector('input[name="subject"]');
    const messageTextarea = document.querySelector('textarea[name="message"]');

    const templates = {
        'order_update': {
            subject: 'Order Update - #{order_number}',
            message: 'Hello {customer_name},\n\nWe wanted to update you on your recent order #{order_number}.\n\n[Your message here]\n\nThank you for your business!'
        },
        'shipping_update': {
            subject: 'Your Order Has Been Shipped - #{order_number}',
            message: 'Hello {customer_name},\n\nGreat news! Your order #{order_number} has been shipped and is on its way to you.\n\nTracking information will be available shortly.\n\nThank you for choosing us!'
        },
        'delivery_reminder': {
            subject: 'Delivery Reminder - #{order_number}',
            message: 'Hello {customer_name},\n\nThis is a friendly reminder that your order #{order_number} is scheduled for delivery.\n\nPlease ensure someone is available to receive the package.\n\nThank you!'
        },
        'feedback_request': {
            subject: 'How was your experience? - #{order_number}',
            message: 'Hello {customer_name},\n\nWe hope you\'re happy with your recent purchase (#{order_number})!\n\nWe\'d love to hear about your experience. Your feedback helps us improve our service.\n\nThank you for your business!'
        }
    };

    if (templates[type]) {
        subjectInput.value = templates[type].subject;
        messageTextarea.value = templates[type].message;
    } else {
        subjectInput.value = '';
        messageTextarea.value = '';
    }
}

function sendNotification() {
    const form = document.getElementById('notificationForm');
    const formData = new FormData(form);

    fetch('{{ route("admin.orders.send-notification") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('notificationModal')).hide();
            alert('Notification sent successfully!');
        } else {
            alert(data.message || 'Failed to send notification');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send notification');
    });
}

// Auto-refresh orders every 30 seconds for real-time updates
setInterval(function() {
    // Only refresh if no modals are open
    if (!document.querySelector('.modal.show')) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('auto_refresh', '1');

        fetch(`${window.location.pathname}?${urlParams.toString()}`)
            .then(response => response.text())
            .then(html => {
                // Update only the order statistics
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newStats = doc.querySelector('.row.mb-4');
                if (newStats) {
                    document.querySelector('.row.mb-4').innerHTML = newStats.innerHTML;
                }
            })
            .catch(error => {
                console.log('Auto-refresh failed:', error);
            });
    }
}, 30000); // 30 seconds

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select:not([name="date_range"])');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            form.submit();
        });
    });

    // Initialize bulk actions state
    updateBulkActions();

    // Real-time search with debounce
    const searchInput = form.querySelector('input[name="search"]');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            form.submit();
        }, 500);
    });

    // Initialize custom date range visibility
    toggleCustomDateRange();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+A or Cmd+A to select all orders
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.getElementById('selectAllCheckbox').checked = true;
        toggleSelectAll();
    }

    // Escape to deselect all
    if (e.key === 'Escape') {
        document.getElementById('selectAllCheckbox').checked = false;
        toggleSelectAll();
    }
});

// Enhanced order row highlighting on hover
document.addEventListener('DOMContentLoaded', function() {
    const orderRows = document.querySelectorAll('tbody tr');

    orderRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });

        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>
@endpush
