@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users me-2"></i>User Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportUsers('excel')">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportUsers('csv')">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-1"></i> Add New User
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalUsers">{{ number_format($statistics['total_users']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeUsers">{{ number_format($statistics['active_users']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">New This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="newUsers">{{ number_format($statistics['new_users_month']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Online Now</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="onlineUsers">{{ number_format($statistics['online_users']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form id="filtersForm" class="row g-3">
            <div class="col-md-3">
                <label for="searchInput" class="form-label">Search Users</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Name, email, phone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label for="roleFilter" class="form-label">Role</label>
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="verificationFilter" class="form-label">Email Status</label>
                <select class="form-select" id="verificationFilter">
                    <option value="">All</option>
                    <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="dateFilter" class="form-label">Registration</label>
                <select class="form-select" id="dateFilter">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('date') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-secondary d-block" onclick="clearFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0 font-weight-bold text-primary">
                    Users List
                    <span class="badge bg-secondary ms-2" id="userCount">{{ $users->total() }} users</span>
                </h6>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <input type="checkbox" class="btn-check" id="selectAll" autocomplete="off">
                    <label class="btn btn-outline-primary btn-sm" for="selectAll">Select All</label>

                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                            <i class="fas fa-check-circle text-success me-2"></i>Activate Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                            <i class="fas fa-ban text-warning me-2"></i>Deactivate Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('unlock')">
                            <i class="fas fa-unlock text-info me-2"></i>Unlock Selected
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('export')">
                            <i class="fas fa-download text-primary me-2"></i>Export Selected
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('delete')">
                            <i class="fas fa-trash text-danger me-2"></i>Delete Selected
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAllTable" class="form-check-input">
                        </th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registration</th>
                        <th>Last Login</th>
                        <th>Orders</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr data-user-id="{{ $user->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->first_name }}" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-muted small">{{ $user->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'staff' ? 'warning' : 'primary') }}">
                                {{ $user->role->display_name }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($user->is_active)
                                    <span class="badge bg-success mb-1">Active</span>
                                @else
                                    <span class="badge bg-secondary mb-1">Inactive</span>
                                @endif

                                @if($user->email_verified_at)
                                    <span class="badge bg-info">Email Verified</span>
                                @else
                                    <span class="badge bg-warning">Email Pending</span>
                                @endif

                                @if($user->locked_until && $user->locked_until > now())
                                    <span class="badge bg-danger mt-1">Locked</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <strong>{{ $user->created_at->format('d M Y') }}</strong><br>
                                <span class="text-muted">{{ $user->created_at->format('H:i') }}</span><br>
                                <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <div class="small">
                                    <strong>{{ $user->last_login_at->format('d M Y') }}</strong><br>
                                    <span class="text-muted">{{ $user->last_login_at->format('H:i') }}</span><br>
                                    <span class="text-muted">{{ $user->last_login_at->diffForHumans() }}</span>
                                </div>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-center">
                                <strong class="text-primary">{{ $user->orders_count ?? 0 }}</strong><br>
                                <small class="text-muted">
                                    {{ formatCurrency($user->orders_sum ?? 0) }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" title="More Actions">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                            <i class="fas fa-eye me-2"></i>View Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="editUser({{ $user->id }})">
                                            <i class="fas fa-edit me-2"></i>Edit User
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($user->is_active)
                                        <li>
                                            <a class="dropdown-item text-warning" href="#" onclick="toggleUserStatus({{ $user->id }}, 'deactivate')">
                                                <i class="fas fa-ban me-2"></i>Deactivate
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item text-success" href="#" onclick="toggleUserStatus({{ $user->id }}, 'activate')">
                                                <i class="fas fa-check-circle me-2"></i>Activate
                                            </a>
                                        </li>
                                    @endif

                                    @if($user->locked_until && $user->locked_until > now())
                                        <li>
                                            <a class="dropdown-item text-info" href="#" onclick="unlockUser({{ $user->id }})">
                                                <i class="fas fa-unlock me-2"></i>Unlock Account
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item text-warning" href="#" onclick="lockUser({{ $user->id }})">
                                                <i class="fas fa-lock me-2"></i>Lock Account
                                            </a>
                                        </li>
                                    @endif

                                    @if(!$user->email_verified_at)
                                        <li>
                                            <a class="dropdown-item text-info" href="#" onclick="resendVerification({{ $user->id }})">
                                                <i class="fas fa-envelope me-2"></i>Resend Verification
                                            </a>
                                        </li>
                                    @endif

                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-primary" href="#" onclick="loginAsUser({{ $user->id }})">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login as User
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})">
                                            <i class="fas fa-trash me-2"></i>Delete User
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5>No Users Found</h5>
                                <p>No users match your search criteria.</p>
                                <button type="button" class="btn btn-primary" onclick="clearFilters()">
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col">
                <p class="text-muted mb-0">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </p>
            </div>
            <div class="col-auto">
                {{ $users->links('vendor.pagination.default') }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select class="form-select" id="role" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="passwordConfirm" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="passwordConfirm" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sendWelcome" name="send_welcome" checked>
                        <label class="form-check-label" for="sendWelcome">
                            Send welcome email to user
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create User
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
    // Search functionality with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterUsers();
        }, 500);
    });

    // Filter change handlers
    document.querySelectorAll('#filtersForm select').forEach(select => {
        select.addEventListener('change', filterUsers);
    });

    // Select all functionality
    document.getElementById('selectAllTable').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox handlers
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Create user form
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createUser();
    });

    // Auto-refresh every 30 seconds
    setInterval(refreshStats, 30000);
});

function filterUsers() {
    const formData = new FormData(document.getElementById('filtersForm'));
    const params = new URLSearchParams();

    // Add all non-empty form values to params
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }

    // Add search value separately
    const searchValue = document.getElementById('searchInput').value.trim();
    if (searchValue !== '') {
        params.append('search', searchValue);
    }

    // Update URL and reload
    const url = new URL(window.location);
    url.search = params.toString();
    window.location.href = url.toString();
}

function clearFilters() {
    // Clear all form inputs
    document.getElementById('filtersForm').reset();
    document.getElementById('searchInput').value = '';

    // Remove all URL parameters
    const url = new URL(window.location);
    url.search = '';
    window.location.href = url.toString();
}

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkActionBtn = document.querySelector('.btn-group [data-bs-toggle="dropdown"]');

    if (selectedCheckboxes.length > 0) {
        bulkActionBtn.disabled = false;
        bulkActionBtn.textContent = `Bulk Actions (${selectedCheckboxes.length})`;
    } else {
        bulkActionBtn.disabled = true;
        bulkActionBtn.textContent = 'Bulk Actions';
    }
}

function bulkAction(action) {
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedUsers.length === 0) {
        showAlert('Please select at least one user.', 'warning');
        return;
    }

    let confirmMessage = '';
    let actionText = '';

    switch(action) {
        case 'activate':
            confirmMessage = `Are you sure you want to activate ${selectedUsers.length} user(s)?`;
            actionText = 'Activating users...';
            break;
        case 'deactivate':
            confirmMessage = `Are you sure you want to deactivate ${selectedUsers.length} user(s)?`;
            actionText = 'Deactivating users...';
            break;
        case 'unlock':
            confirmMessage = `Are you sure you want to unlock ${selectedUsers.length} user(s)?`;
            actionText = 'Unlocking users...';
            break;
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${selectedUsers.length} user(s)? This action cannot be undone.`;
            actionText = 'Deleting users...';
            break;
        case 'export':
            exportSelectedUsers(selectedUsers);
            return;
    }

    if (confirm(confirmMessage)) {
        performBulkAction(action, selectedUsers, actionText);
    }
}

function performBulkAction(action, userIds, actionText) {
    showLoading(actionText);

    fetch(`{{ route('admin.users.bulk-action') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            action: action,
            user_ids: userIds
        })
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
        showAlert('An error occurred while processing the request.', 'danger');
        console.error('Error:', error);
    });
}

function toggleUserStatus(userId, action) {
    const actionText = action === 'activate' ? 'Activating' : 'Deactivating';
    const confirmText = action === 'activate' ? 'activate' : 'deactivate';

    if (confirm(`Are you sure you want to ${confirmText} this user?`)) {
        showLoading(`${actionText} user...`);

        fetch(`{{ route('admin.users.toggle-status', '') }}/${userId}`, {
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

function unlockUser(userId) {
    if (confirm('Are you sure you want to unlock this user account?')) {
        showLoading('Unlocking user...');

        fetch(`{{ route('admin.users.unlock', '') }}/${userId}`, {
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

function lockUser(userId) {
    const reason = prompt('Please enter the reason for locking this account:');
    if (reason && reason.trim() !== '') {
        showLoading('Locking user...');

        fetch(`{{ route('admin.users.lock', '') }}/${userId}`, {
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

function resendVerification(userId) {
    if (confirm('Are you sure you want to resend the email verification to this user?')) {
        showLoading('Sending verification email...');

        fetch(`{{ route('admin.users.resend-verification', '') }}/${userId}`, {
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

function loginAsUser(userId) {
    if (confirm('Are you sure you want to login as this user? This will log you out from the admin panel.')) {
        showLoading('Logging in as user...');

        fetch(`{{ route('admin.users.login-as', '') }}/${userId}`, {
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

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const additionalConfirm = confirm('This will permanently delete all user data including orders and reviews. Are you absolutely sure?');
        if (additionalConfirm) {
            showLoading('Deleting user...');

            fetch(`{{ route('admin.users.destroy', '') }}/${userId}`, {
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
                    location.reload();
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

function editUser(userId) {
    // Redirect to edit page or open edit modal
    window.location.href = `{{ route('admin.users.edit', '') }}/${userId}`;
}

function createUser() {
    const form = document.getElementById('createUserForm');
    const formData = new FormData(form);

    // Validate passwords match
    const password = formData.get('password');
    const passwordConfirm = formData.get('password_confirmation');

    if (password !== passwordConfirm) {
        showAlert('Passwords do not match.', 'danger');
        return;
    }

    showLoading('Creating user...');

    fetch('{{ route("admin.users.store") }}', {
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
            bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
            location.reload();
        } else {
            if (data.errors) {
                // Display validation errors
                let errorMessage = 'Please fix the following errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${data.errors[field][0]}\n`;
                });
                showAlert(errorMessage, 'danger');
            } else {
                showAlert(data.message || 'An error occurred', 'danger');
            }
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while creating user.', 'danger');
        console.error('Error:', error);
    });
}

function exportUsers(format) {
    showLoading('Preparing export...');

    const params = new URLSearchParams(window.location.search);
    params.append('export', format);

    const exportUrl = `{{ route('admin.users.export') }}?${params.toString()}`;

    // Create temporary link and click it
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `users-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        hideLoading();
        showAlert(`Users exported successfully as ${format.toUpperCase()}.`, 'success');
    }, 1000);
}

function exportSelectedUsers(userIds) {
    showLoading('Preparing export...');

    fetch('{{ route("admin.users.export-selected") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_ids: userIds,
            format: 'excel'
        })
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
        link.download = `selected-users-${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        showAlert('Selected users exported successfully.', 'success');
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while exporting users.', 'danger');
        console.error('Error:', error);
    });
}

function refreshStats() {
    fetch('{{ route("admin.users.stats") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalUsers').textContent = data.stats.total_users.toLocaleString();
            document.getElementById('activeUsers').textContent = data.stats.active_users.toLocaleString();
            document.getElementById('newUsers').textContent = data.stats.new_users_month.toLocaleString();
            document.getElementById('onlineUsers').textContent = data.stats.online_users.toLocaleString();
        }
    })
    .catch(error => {
        console.error('Error refreshing stats:', error);
    });
}

// Utility functions
function showLoading(message = 'Loading...') {
    // Create or update loading overlay
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
    // Create alert element
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

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+N for new user
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        document.querySelector('[data-bs-target="#createUserModal"]').click();
    }

    // Ctrl+F for search
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('searchInput');
        if (searchInput === document.activeElement) {
            searchInput.value = '';
            filterUsers();
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.avatar-sm img {
    object-fit: cover;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

.badge {
    font-size: 0.7em;
}

.btn-group .dropdown-toggle-split {
    padding-left: 0.375rem;
    padding-right: 0.375rem;
}

@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-group {
        width: 100%;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .avatar-sm {
        width: 32px !important;
        height: 32px !important;
    }
}
</style>
@endpush
