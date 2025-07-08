@extends('layouts.app')

@section('title', 'My Profile - TokoSaya')
@section('meta_description', 'Manage your TokoSaya account, view order history, addresses, and account settings')

@section('content')
<div class="profile-container py-5">
    <div class="container">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar">
                    <!-- User Info Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3" x-data="{ showUpload: false }">
                                <div class="avatar-container position-relative"
                                     @mouseenter="showUpload = true"
                                     @mouseleave="showUpload = false">
                                    <img src="{{ auth()->user()->avatar ?: asset('images/default-avatar.jpg') }}"
                                         alt="{{ auth()->user()->first_name }}"
                                         class="avatar-image rounded-circle">
                                    <div class="avatar-overlay"
                                         x-show="showUpload"
                                         x-transition
                                         @click="document.getElementById('avatarUpload').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <form id="avatarUploadForm" action="{{ route('profile.upload-avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                                    @csrf
                                    <input type="file" id="avatarUpload" name="avatar" accept="image/*" @change="uploadAvatar">
                                </form>
                            </div>
                            <h5 class="mb-1">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h5>
                            <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                            <div class="user-stats">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h6 class="stat-number">{{ 'Rp ' . number_format(($stats['total_spent'] ?? 0) / 100, 0, ',', '.') }}</h6>
                                            <small class="text-muted">Spent</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h6 class="stat-number">{{ 'Rp ' . number_format(($stats['total_spent'] ?? 0) / 100, 0, ',', '.') }}</h6>
                                            <small class="text-muted">Spent</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h6 class="stat-number">{{ $stats['wishlist_count'] ?? 0 }}</h6>
                                            <small class="text-muted">Points</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    <div class="card shadow-sm">
                        <div class="profile-nav">
                            <a href="{{ route('profile.index') }}"
                               class="nav-item {{ request()->routeIs('profile.index') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                <i class="fas fa-user-edit"></i>
                                <span>Edit Profile</span>
                            </a>
                            <a href="{{ route('orders.index') }}"
                               class="nav-item {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Order History</span>
                                @if(($stats['total_orders'] ?? 0) > 0)
                                    <span class="badge bg-warning">{{ $stats['total_orders'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('profile.addresses.index') }}"
                               class="nav-item {{ request()->routeIs('profile.addresses.index') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Addresses</span>
                            </a>
                            <a href="{{ route('wishlist.index') }}"
                               class="nav-item {{ request()->routeIs('wishlist.index') ? 'active' : '' }}">
                                <i class="fas fa-heart"></i>
                                <span>Wishlist</span>
                                @if(($stats['wishlist_count'] ?? 0) > 0)
                                    <span class="badge bg-danger">{{ $stats['wishlist_count'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('profile.reviews') }}"
                               class="nav-item {{ request()->routeIs('profile.reviews') ? 'active' : '' }}">
                                <i class="fas fa-star"></i>
                                <span>My Reviews</span>
                            </a>
                            <a href="{{ route('profile.notifications') }}"
                               class="nav-item {{ request()->routeIs('profile.notifications') ? 'active' : '' }}">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                                @if($unreadNotifications > 0)
                                    <span class="badge bg-primary">{{ $unreadNotifications }}</span>
                                @endif
                            </a>
                            <a href="{{ route('profile.security') }}"
                               class="nav-item {{ request()->routeIs('profile.security') ? 'active' : '' }}">
                                <i class="fas fa-shield-alt"></i>
                                <span>Security</span>
                            </a>
                            <div class="nav-divider"></div>
                            <a href="#" onclick="confirmLogout()" class="nav-item text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Welcome Header -->
                <div class="welcome-header mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-1">Welcome back, {{ auth()->user()->first_name }}!</h2>
                            <p class="text-muted mb-0">Here's what's happening with your account</p>
                        </div>
                        <div class="col-auto">
                            <div class="date-time text-end">
                                <small class="text-muted" id="currentDateTime"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-content">
                                <h5 class="stat-value">{{ $recentOrdersCount }}</h5>
                                <p class="stat-label">Recent Orders</p>
                                <small class="stat-change text-success">
                                    <i class="fas fa-arrow-up"></i> +12% this month
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-content">
                                <h5 class="stat-value">{{ $wishlistCount }}</h5>
                                <p class="stat-label">Saved Items</p>
                                <small class="stat-change text-muted">
                                    In your wishlist
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <h5 class="stat-value">{{ $averageRating }}</h5>
                                <p class="stat-label">Avg. Rating Given</p>
                                <small class="stat-change text-muted">
                                    {{ $totalReviews }} reviews
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="stat-content">
                                <h5 class="stat-value">{{ $loyaltyPoints ?? 0 }}</h5>
                                <p class="stat-label">Loyalty Points</p>
                                <small class="stat-change text-success">
                                    <i class="fas fa-plus"></i> Earn more!
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    Recent Orders
                                </h6>
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All
                                </a>
                            </div>
                            <div class="card-body">
                                @if($recentOrders->count() > 0)
                                    <div class="orders-list">
                                        @foreach($recentOrders as $order)
                                        <div class="order-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="order-info">
                                                        <h6 class="order-number mb-1">#{{ $order->order_number }}</h6>
                                                        <small class="text-muted">{{ $order->created_at->format('M j, Y') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="order-status">
                                                        <span class="badge bg-{{ $order->status_color }}">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="order-total">
                                                        <strong>{{ format_currency($order->total_cents) }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <div class="order-actions">
                                                        <a href="{{ route('orders.show', $order) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a>
                                                        @if($order->status === 'delivered' && !$order->hasReview())
                                                            <a href="{{ route('orders.review', $order) }}"
                                                               class="btn btn-sm btn-outline-warning ms-1">
                                                                Review
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state text-center py-4">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <h6>No orders yet</h6>
                                        <p class="text-muted mb-3">Start shopping to see your orders here</p>
                                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                                            Start Shopping
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Account Actions -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-cog text-primary me-2"></i>
                                    Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="quick-actions">
                                    <a href="{{ route('profile.edit') }}" class="action-item">
                                        <div class="action-icon bg-primary">
                                            <i class="fas fa-user-edit"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6>Update Profile</h6>
                                            <small class="text-muted">Edit your personal information</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>

                                    <a href="{{ route('profile.addresses.index') }}" class="action-item">
                                        <div class="action-icon bg-success">
                                            <i class="fas fa-plus"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6>Add Address</h6>
                                            <small class="text-muted">Manage delivery addresses</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>

                                    <a href="{{ route('profile.security') }}" class="action-item">
                                        <div class="action-icon bg-warning">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6>Change Password</h6>
                                            <small class="text-muted">Update security settings</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>

                                    <a href="{{ route('support.tickets') }}" class="action-item">
                                        <div class="action-icon bg-info">
                                            <i class="fas fa-headset"></i>
                                        </div>
                                        <div class="action-content">
                                            <h6>Get Support</h6>
                                            <small class="text-muted">Contact customer service</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Loyalty Program -->
                        <div class="card shadow-sm mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-crown text-warning me-2"></i>
                                    Loyalty Program
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="loyalty-info">
                                    <div class="loyalty-tier mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="tier-name">{{ $currentTier ?? 'Bronze' }} Member</span>
                                            <span class="tier-points">{{ $loyaltyPoints ?? 0 }} pts</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning"
                                                 style="width: {{ ($loyaltyPoints ?? 0) / 1000 * 100 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">
                                            {{ 1000 - ($loyaltyPoints ?? 0) }} points to next tier
                                        </small>
                                    </div>

                                    <div class="loyalty-benefits">
                                        <h6 class="mb-2">Your Benefits:</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <small>Free shipping on orders over IDR 100K</small>
                                            </li>
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <small>Early access to sales</small>
                                            </li>
                                            <li class="mb-1">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <small>Birthday month discount</small>
                                            </li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('loyalty.program') }}" class="btn btn-outline-warning btn-sm w-100">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-history text-primary me-2"></i>
                                    Recent Activity
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    @foreach($recentActivity as $activity)
                                    <div class="activity-item">
                                        <div class="activity-icon bg-{{ $activity->type_color }}">
                                            <i class="fas fa-{{ $activity->icon }}"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-header">
                                                <h6 class="activity-title">{{ $activity->title }}</h6>
                                                <small class="activity-time">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="activity-description">{{ $activity->description }}</p>
                                            @if($activity->action_url)
                                                <a href="{{ $activity->action_url }}" class="btn btn-sm btn-outline-primary">
                                                    {{ $activity->action_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    @if($recentActivity->isEmpty())
                                    <div class="empty-activity text-center py-4">
                                        <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No recent activity</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                @if($recommendedProducts->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-magic text-primary me-2"></i>
                                    Recommended for You
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($recommendedProducts as $product)
                                    <div class="col-lg-3 col-md-4 col-6 mb-3">
                                        <div class="product-recommendation">
                                            <div class="product-image">
                                                <a href="{{ route('products.show', $product) }}">
                                                    <img src="{{ $product->primary_image }}"
                                                         alt="{{ $product->name }}"
                                                         class="img-fluid rounded">
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger wishlist-btn"
                                                        data-product="{{ $product->id }}">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            </div>
                                            <div class="product-info mt-2">
                                                <h6 class="product-name">
                                                    <a href="{{ route('products.show', $product) }}"
                                                       class="text-decoration-none">
                                                        {{ Str::limit($product->name, 50) }}
                                                    </a>
                                                </h6>
                                                <div class="product-price">
                                                    <span class="current-price fw-bold">
                                                        {{ format_currency($product->price_cents) }}
                                                    </span>
                                                    @if($product->compare_price_cents)
                                                        <span class="original-price text-muted">
                                                            {{ format_currency($product->compare_price_cents) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="product-rating">
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $product->rating_average ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <small class="text-muted">({{ $product->rating_count }})</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                        View More Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout from your account?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

.profile-sidebar .card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.avatar-container {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
    cursor: pointer;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.user-stats .stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.profile-nav {
    padding: 0;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    color: #64748b;
    text-decoration: none;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
    position: relative;
}

.nav-item:hover {
    background: #f8fafc;
    color: #2563eb;
    transform: translateX(4px);
}

.nav-item.active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    border-left: 4px solid #1d4ed8;
}

.nav-item i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 0.9rem;
}

.nav-item .badge {
    margin-left: auto;
    font-size: 0.75rem;
}

.nav-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 0.5rem 0;
}

.welcome-header h2 {
    font-weight: 700;
    color: #1e293b;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #1e293b;
}

.stat-label {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    font-weight: 500;
}

.order-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.order-item:hover {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    margin: 0 -1rem;
}

.order-item:last-child {
    border-bottom: none;
}

.order-number {
    font-weight: 600;
    color: #2563eb;
}

.action-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    color: inherit;
    text-decoration: none;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.action-item:hover {
    color: #2563eb;
    transform: translateX(4px);
}

.action-item:last-child {
    border-bottom: none;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.action-content {
    flex-grow: 1;
}

.action-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.loyalty-tier .progress {
    height: 8px;
    border-radius: 4px;
    background: #f1f5f9;
}

.loyalty-tier .progress-bar {
    border-radius: 4px;
}

.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    margin-bottom: 1.5rem;
    position: relative;
}

.activity-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 50px;
    bottom: -24px;
    width: 2px;
    background: #e2e8f0;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.activity-content {
    flex-grow: 1;
}

.activity-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0;
    margin-right: 1rem;
}

.activity-time {
    color: #64748b;
    white-space: nowrap;
}

.activity-description {
    color: #64748b;
    margin-bottom: 0.5rem;
}

.product-recommendation {
    position: relative;
    transition: all 0.3s ease;
}

.product-recommendation:hover {
    transform: translateY(-4px);
}

.product-image {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
}

.product-image img {
    aspect-ratio: 1;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-recommendation:hover .product-image img {
    transform: scale(1.05);
}

.wishlist-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.product-recommendation:hover .wishlist-btn {
    opacity: 1;
}

.product-name a {
    color: #1e293b;
    font-weight: 500;
}

.product-name a:hover {
    color: #2563eb;
}

.current-price {
    color: #2563eb;
    font-size: 1.1rem;
}

.original-price {
    text-decoration: line-through;
    font-size: 0.9rem;
    margin-left: 0.5rem;
}

.stars {
    display: inline-flex;
    gap: 2px;
    margin-right: 0.5rem;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

@media (max-width: 768px) {
    .profile-sidebar {
        margin-bottom: 2rem;
    }

    .stat-card {
        margin-bottom: 1rem;
    }

    .activity-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .activity-time {
        margin-top: 0.25rem;
    }

    .order-item .row > div {
        margin-bottom: 0.5rem;
    }

    .order-actions {
        text-align: left !important;
    }
}

/* Loading Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current date time
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Wishlist functionality
    initWishlistButtons();

    // Avatar upload functionality
    initAvatarUpload();
});

function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    document.getElementById('currentDateTime').textContent =
        now.toLocaleDateString('en-US', options);
}

function confirmLogout() {
    const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
    modal.show();
}

function initWishlistButtons() {
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.dataset.product;
            const icon = this.querySelector('i');
            const isWishlisted = icon.classList.contains('fas');

            // Optimistic UI update
            if (isWishlisted) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.classList.remove('btn-danger');
                this.classList.add('btn-outline-danger');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                this.classList.remove('btn-outline-danger');
                this.classList.add('btn-danger');
            }

            // Send request to server
            fetch(`/api/wishlist/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert optimistic update
                    if (isWishlisted) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    }
                    showToast('Failed to update wishlist', 'error');
                } else {
                    showToast(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Wishlist error:', error);
                // Revert optimistic update
                if (isWishlisted) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.classList.remove('btn-outline-danger');
                    this.classList.add('btn-danger');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.classList.remove('btn-danger');
                    this.classList.add('btn-outline-danger');
                }
                showToast('Network error', 'error');
            });
        });
    });
}

function initAvatarUpload() {
    const avatarUpload = document.getElementById('avatarUpload');
    if (avatarUpload) {
        avatarUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Validate file
            if (!file.type.startsWith('image/')) {
                showToast('Please select an image file', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB
                showToast('Image size must be less than 5MB', 'error');
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.avatar-image').src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload image
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('{{ route("profile.upload-avatar") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Avatar updated successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to update avatar', 'error');
                }
            })
            .catch(error => {
                console.error('Avatar upload error:', error);
                showToast('Failed to upload avatar', 'error');
            });
        });
    }
}

function showToast(message, type = 'info') {
    // Create toast element
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

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Alpine.js data for avatar upload
document.addEventListener('alpine:init', () => {
    Alpine.data('avatarUpload', () => ({
        uploadAvatar() {
            // This function is called from the Alpine.js directive
            // The actual upload logic is handled in initAvatarUpload()
        }
    }));
});
</script>
@endpush
