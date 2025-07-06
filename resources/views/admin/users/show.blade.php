@extends('layouts.admin')

@section('title', 'User Profile - ' . $user->first_name . ' ' . $user->last_name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h2 mb-0">
                <i class="fas fa-user me-2"></i>User Profile
            </h1>
            <p class="text-muted mb-0">{{ $user->first_name }} {{ $user->last_name }}</p>
        </div>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportUserData()">
                <i class="fas fa-download me-1"></i> Export Data
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="sendMessage()">
                <i class="fas fa-envelope me-1"></i> Send Message
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="editUser()">
                <i class="fas fa-edit me-1"></i> Edit User
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-1"></i> Actions
            </button>
            <ul class="dropdown-menu">
                @if($user->is_active)
                    <li><a class="dropdown-item text-warning" href="#" onclick="toggleUserStatus('deactivate')">
                        <i class="fas fa-ban me-2"></i>Deactivate Account
                    </a></li>
                @else
                    <li><a class="dropdown-item text-success" href="#" onclick="toggleUserStatus('activate')">
                        <i class="fas fa-check-circle me-2"></i>Activate Account
                    </a></li>
                @endif

                @if($user->locked_until && $user->locked_until > now())
                    <li><a class="dropdown-item text-info" href="#" onclick="unlockUser()">
                        <i class="fas fa-unlock me-2"></i>Unlock Account
                    </a></li>
                @else
                    <li><a class="dropdown-item text-warning" href="#" onclick="lockUser()">
                        <i class="fas fa-lock me-2"></i>Lock Account
                    </a></li>
                @endif

                @if(!$user->email_verified_at)
                    <li><a class="dropdown-item text-info" href="#" onclick="resendVerification()">
                        <i class="fas fa-envelope me-2"></i>Resend Verification
                    </a></li>
                @endif

                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-primary" href="#" onclick="loginAsUser()">
                    <i class="fas fa-sign-in-alt me-2"></i>Login as User
                </a></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser()">
                    <i class="fas fa-trash me-2"></i>Delete User
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- User Overview -->
<div class="row mb-4">
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->first_name }}"
                             class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3"
                             style="width: 120px; height: 120px; font-size: 2rem;">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>

                @if($user->phone)
                    <p class="text-muted mb-3">{{ $user->phone }}</p>
                @endif

                <div class="mb-3">
                    <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'staff' ? 'warning' : 'primary') }} me-2">
                        {{ $user->role->display_name }}
                    </span>

                    @if($user->is_active)
                        <span class="badge bg-success me-2">Active</span>
                    @else
                        <span class="badge bg-secondary me-2">Inactive</span>
                    @endif

                    @if($user->email_verified_at)
                        <span class="badge bg-info">Verified</span>
                    @else
                        <span class="badge bg-warning">Unverified</span>
                    @endif

                    @if($user->locked_until && $user->locked_until > now())
                        <span class="badge bg-danger d-block mt-2">Locked until {{ $user->locked_until->format('d M Y H:i') }}</span>
                    @endif
                </div>

                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-primary mb-0">{{ $user->orders_count ?? 0 }}</h5>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-success mb-0">{{ formatCurrency($user->total_spent ?? 0) }}</h5>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h5 class="text-info mb-0">{{ $user->reviews_count ?? 0 }}</h5>
                        <small class="text-muted">Reviews</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">User ID:</td>
                        <td class="fw-bold">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Username:</td>
                        <td>{{ $user->username }}</td>
                    </tr>
                    @if($user->date_of_birth)
                    <tr>
                        <td class="text-muted">Date of Birth:</td>
                        <td>{{ $user->date_of_birth->format('d M Y') }} ({{ $user->date_of_birth->age }} years)</td>
                    </tr>
                    @endif
                    @if($user->gender)
                    <tr>
                        <td class="text-muted">Gender:</td>
                        <td>{{ $user->gender === 'M' ? 'Male' : ($user->gender === 'F' ? 'Female' : 'Other') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Registration:</td>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Last Login:</td>
                        <td>
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('d M Y H:i') }}
                                <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                    </tr>
                    @if($user->login_attempts > 0)
                    <tr>
                        <td class="text-muted">Failed Logins:</td>
                        <td>
                            <span class="badge bg-warning">{{ $user->login_attempts }} attempts</span>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <!-- Activity Overview -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_orders'] }}</div>
                                <div class="text-xs text-muted">
                                    {{ $statistics['completed_orders'] }} completed, {{ $statistics['pending_orders'] }} pending
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ formatCurrency($statistics['total_revenue']) }}</div>
                                <div class="text-xs text-muted">
                                    Avg: {{ formatCurrency($statistics['avg_order_value']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                    <i class="fas fa-shopping-cart me-2"></i>Orders ({{ $user->orders_count ?? 0 }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab">
                    <i class="fas fa-map-marker-alt me-2"></i>Addresses ({{ $user->addresses_count ?? 0 }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                    <i class="fas fa-star me-2"></i>Reviews ({{ $user->reviews_count ?? 0 }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                    <i class="fas fa-history me-2"></i>Activity Log
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="userTabsContent">
            <!-- Orders Tab -->
            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->orders()->latest()->take(10)->get() as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->created_at->format('d M Y') }}</strong><br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->items_count ?? $order->items->count() }} items</span>
                                        </td>
                                        <td>
                                            <strong>{{ formatCurrency($order->total_cents) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{
                                                $order->status === 'delivered' ? 'success' :
                                                ($order->status === 'cancelled' ? 'danger' :
                                                ($order->status === 'shipped' ? 'info' : 'warning'))
                                            }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                <h6>No Orders Found</h6>
                                                <p class="mb-0">This user hasn't placed any orders yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($user->orders_count > 10)
                        <div class="card-footer text-center">
                            <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Orders ({{ $user->orders_count }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Addresses Tab -->
            <div class="tab-pane fade" id="addresses" role="tabpanel">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            @forelse($user->addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="card border {{ $address->is_default ? 'border-primary' : '' }}">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            {{ $address->label }}
                                            @if($address->is_default)
                                                <span class="badge bg-primary ms-2">Default</span>
                                            @endif
                                        </h6>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editAddress({{ $address->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="mb-2">
                                            <strong>{{ $address->recipient_name }}</strong><br>
                                            <span class="text-muted">{{ $address->phone }}</span>
                                        </div>
                                        <address class="mb-0">
                                            {{ $address->address_line1 }}<br>
                                            @if($address->address_line2)
                                                {{ $address->address_line2 }}<br>
                                            @endif
                                            {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                            {{ $address->country }}
                                        </address>
                                        @if($address->latitude && $address->longitude)
                                            <div class="mt-2">
                                                <a href="https://maps.google.com/?q={{ $address->latitude }},{{ $address->longitude }}"
                                                   target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-map-marker-alt me-1"></i>View on Map
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                    <h6>No Addresses Found</h6>
                                    <p class="text-muted mb-0">This user hasn't added any addresses yet.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->reviews()->with('product')->latest()->take(10)->get() as $review)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($review->product->primary_image)
                                                    <img src="{{ asset('storage/' . $review->product->primary_image) }}"
                                                         alt="{{ $review->product->name }}"
                                                         class="rounded me-3" width="40" height="40" style="object-fit: cover;">
                                                @endif
                                                <div>
                                                    <a href="{{ route('admin.products.show', $review->product) }}"
                                                       class="text-decoration-none">
                                                        {{ Str::limit($review->product->name, 30) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="stars me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="badge bg-secondary">{{ $review->rating }}/5</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->title)
                                                <div class="fw-bold mb-1">{{ Str::limit($review->title, 40) }}</div>
                                            @endif
                                            @if($review->review)
                                                <div class="text-muted small">{{ Str::limit($review->review, 80) }}</div>
                                            @endif
                                            @if($review->images)
                                                <div class="mt-1">
                                                    <span class="badge bg-info">{{ count($review->images) }} images</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>{{ $review->created_at->format('d M Y') }}</strong><br>
                                                <span class="text-muted">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->is_approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                            @if($review->is_verified)
                                                <span class="badge bg-info mt-1">Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.reviews.show', $review) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$review->is_approved)
                                                    <button class="btn btn-sm btn-outline-success"
                                                            onclick="approveReview({{ $review->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-star fa-3x mb-3"></i>
                                                <h6>No Reviews Found</h6>
                                                <p class="mb-0">This user hasn't written any reviews yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($user->reviews_count > 10)
                        <div class="card-footer text-center">
                            <a href="{{ route('admin.reviews.index', ['user' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Reviews ({{ $user->reviews_count }})
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Log Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Activity</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->activityLogs()->latest()->take(20)->get() as $log)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{
                                                str_contains($log->action, 'login') ? 'success' :
                                                (str_contains($log->action, 'order') ? 'primary' :
                                                (str_contains($log->action, 'profile') ? 'info' : 'secondary'))
                                            }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $log->description }}
                                            @if($log->model && $log->model_id)
                                                <small class="text-muted d-block">
                                                    {{ $log->model }} #{{ $log->model_id }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->ip_address)
                                                <code>{{ $log->ip_address }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>{{ $log->created_at->format('d M Y H:i') }}</strong><br>
                                                <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-history fa-3x mb-3"></i>
                                                <h6>No Activity Found</h6>
                                                <p class="mb-0">No activity logs available for this user.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message to {{ $user->first_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sendMessageForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="messageSubject" class="form-label">Subject *</label>
                        <input type="text" class="form-control" id="messageSubject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message *</label>
                        <textarea class="form-control" id="messageContent" name="message" rows="5" required></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sendCopy" name="send_copy">
                        <label class="form-check-label" for="sendCopy">
                            Send a copy to my email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Send message form
    document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
});

function editUser() {
    window.location.href = `{{ route('admin.users.edit', $user) }}`;
}

function toggleUserStatus(action) {
    const actionText = action === 'activate' ? 'activate' : 'deactivate';
    const confirmText = `Are you sure you want to ${actionText} this user account?`;

    if (confirm(confirmText)) {
        showLoading(`${actionText.charAt(0).toUpperCase() + actionText.slice(1)}ing user...`);

        fetch(`{{ route('admin.users.toggle-status', $user) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ action: action })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while updating user status.', 'danger');
            console.error('Error:', error);
        });
    }
}

function unlockUser() {
    if (confirm('Are you sure you want to unlock this user account?')) {
        showLoading('Unlocking user...');

        fetch(`{{ route('admin.users.unlock', $user) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while unlocking user.', 'danger');
            console.error('Error:', error);
        });
    }
}

function lockUser() {
    const reason = prompt('Please enter the reason for locking this account:');
    if (reason && reason.trim() !== '') {
        showLoading('Locking user...');

        fetch(`{{ route('admin.users.lock', $user) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while locking user.', 'danger');
            console.error('Error:', error);
        });
    }
}

function resendVerification() {
    if (confirm('Are you sure you want to resend the email verification to this user?')) {
        showLoading('Sending verification email...');

        fetch(`{{ route('admin.users.resend-verification', $user) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while sending verification email.', 'danger');
            console.error('Error:', error);
        });
    }
}

function loginAsUser() {
    if (confirm('Are you sure you want to login as this user? This will log you out from the admin panel.')) {
        showLoading('Logging in as user...');

        fetch(`{{ route('admin.users.login-as', $user) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                window.location.href = data.redirect_url || '{{ route("home") }}';
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while logging in as user.', 'danger');
            console.error('Error:', error);
        });
    }
}

function deleteUser() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const additionalConfirm = confirm('This will permanently delete all user data including orders and reviews. Are you absolutely sure?');
        if (additionalConfirm) {
            showLoading('Deleting user...');

            fetch(`{{ route('admin.users.destroy', $user) }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert(data.message, 'success');
                    window.location.href = '{{ route("admin.users.index") }}';
                } else {
                    showAlert(data.message || 'An error occurred', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('An error occurred while deleting user.', 'danger');
                console.error('Error:', error);
            });
        }
    }
}

function sendMessage() {
    const modal = document.getElementById('sendMessageModal');
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

function submitMessage() {
    const form = document.getElementById('sendMessageForm');
    const formData = new FormData(form);

    showLoading('Sending message...');

    fetch(`{{ route('admin.users.send-message', $user) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('sendMessageModal')).hide();
            form.reset();
        } else {
            showAlert(data.message || 'An error occurred', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while sending message.', 'danger');
        console.error('Error:', error);
    });
}

function approveReview(reviewId) {
    if (confirm('Are you sure you want to approve this review?')) {
        showLoading('Approving review...');

        fetch(`{{ route('admin.reviews.approve', '') }}/${reviewId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert(data.message, 'success');
                location.reload();
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('An error occurred while approving review.', 'danger');
            console.error('Error:', error);
        });
    }
}

function editAddress(addressId) {
    // Redirect to address edit page or open edit modal
    // This would need to be implemented based on your address management structure
    showAlert('Address editing functionality would be implemented here.', 'info');
}

function exportUserData() {
    showLoading('Preparing user data export...');

    fetch(`{{ route('admin.users.export-data', $user) }}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        hideLoading();

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `user-data-{{ $user->id }}-${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        showAlert('User data exported successfully.', 'success');
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while exporting user data.', 'danger');
        console.error('Error:', error);
    });
}

// Utility functions
function showLoading(message = 'Loading...') {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        overlay.style.zIndex = '9999';
        overlay.innerHTML = `
            <div class="bg-white rounded p-4 text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div id="loadingMessage">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loadingMessage').textContent = message;
        overlay.style.display = 'flex';
    }
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '10000';
    alertDiv.style.minWidth = '300px';

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
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.stars .fa-star {
    font-size: 0.8rem;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
}

.tab-pane {
    padding: 1rem;
}

@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
    }
}
</style>
@endpush
