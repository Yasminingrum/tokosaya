@extends('layouts.app')

@section('title', 'Notifications - TokoSaya')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
            <li class="breadcrumb-item active">Notifications</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        Notifications
                        @if($unreadCount > 0)
                            <span class="badge bg-danger">{{ $unreadCount }}</span>
                        @endif
                    </h1>
                    <p class="text-muted mb-0">Stay updated with your orders, promotions, and account activities</p>
                </div>
                <div class="d-flex gap-2">
                    @if($unreadCount > 0)
                    <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-2"></i>Mark All Read
                    </button>
                    @endif
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="fas fa-cog me-2"></i>Settings
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?filter=all">All Notifications</a></li>
                            <li><a class="dropdown-item" href="?filter=unread">Unread Only</a></li>
                            <li><a class="dropdown-item" href="?filter=orders">Orders</a></li>
                            <li><a class="dropdown-item" href="?filter=promotions">Promotions</a></li>
                            <li><a class="dropdown-item" href="?filter=system">System</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-bell text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $stats['total'] }}</h5>
                    <p class="text-muted small mb-0">Total Notifications</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-envelope text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $stats['unread'] }}</h5>
                    <p class="text-muted small mb-0">Unread</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $stats['orders'] }}</h5>
                    <p class="text-muted small mb-0">Order Updates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-tags text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title mb-1">{{ $stats['promotions'] }}</h5>
                    <p class="text-muted small mb-0">Promotions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($unreadCount > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-3"></i>
                <div class="flex-grow-1">
                    <strong>You have {{ $unreadCount }} unread notifications</strong>
                    <p class="mb-0 small">Stay up to date with your latest activities and updates.</p>
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                    Mark All Read
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0">Your Notifications</h6>
                </div>
                <div class="col-auto">
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="view-mode" id="list-view" checked>
                        <label class="btn btn-outline-secondary" for="list-view">
                            <i class="fas fa-list"></i>
                        </label>
                        <input type="radio" class="btn-check" name="view-mode" id="card-view">
                        <label class="btn btn-outline-secondary" for="card-view">
                            <i class="fas fa-th"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div id="notifications-container">
                    @foreach($notifications as $notification)
                    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }} border-bottom"
                         data-notification-id="{{ $notification->id }}"
                         data-type="{{ $notification->type }}">
                        <div class="p-4">
                            <div class="row align-items-center">
                                <!-- Notification Icon -->
                                <div class="col-auto">
                                    <div class="notification-icon {{ $notification->type_class }}">
                                        <i class="fas fa-{{ $notification->type_icon }}"></i>
                                    </div>
                                </div>

                                <!-- Notification Content -->
                                <div class="col">
                                    <div class="notification-content">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="notification-title mb-0">{{ $notification->title }}</h6>
                                            <div class="notification-meta">
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                                <small class="text-muted ms-2">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <p class="notification-message mb-2">{{ $notification->message }}</p>

                                        @if($notification->action_url)
                                        <div class="notification-actions">
                                            <a href="{{ $notification->action_url }}"
                                               class="btn btn-primary btn-sm"
                                               onclick="markAsRead({{ $notification->id }})">
                                                {{ $notification->action_text ?? 'View Details' }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Notification Actions -->
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(!$notification->is_read)
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="markAsRead({{ $notification->id }})">
                                                    <i class="fas fa-check me-2"></i>Mark as Read
                                                </a>
                                            </li>
                                            @else
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="markAsUnread({{ $notification->id }})">
                                                    <i class="fas fa-envelope me-2"></i>Mark as Unread
                                                </a>
                                            </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification({{ $notification->id }})">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Data -->
                            @if($notification->data && is_array($notification->data))
                            <div class="notification-data mt-3 p-3 bg-light rounded">
                                @if($notification->type === 'order_update' && isset($notification->data['order_number']))
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-primary me-2"></i>
                                    <strong>Order #{{ $notification->data['order_number'] }}</strong>
                                    @if(isset($notification->data['status']))
                                        <span class="badge bg-{{ $notification->data['status_color'] ?? 'secondary' }} ms-2">
                                            {{ ucfirst($notification->data['status']) }}
                                        </span>
                                    @endif
                                </div>
                                @elseif($notification->type === 'promotion' && isset($notification->data['discount']))
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-percent text-success me-2"></i>
                                    <strong>{{ $notification->data['discount'] }}% OFF</strong>
                                    @if(isset($notification->data['code']))
                                        <span class="badge bg-success ms-2">{{ $notification->data['code'] }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-4 border-top bg-light">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">No notifications found</h5>
                    <p class="text-muted mb-4">
                        @if(request('filter') && request('filter') !== 'all')
                            No {{ request('filter') }} notifications found. Try changing your filter.
                        @else
                            You're all caught up! New notifications will appear here.
                        @endif
                    </p>
                    @if(request('filter') && request('filter') !== 'all')
                    <a href="{{ route('profile.notifications') }}" class="btn btn-outline-primary">
                        <i class="fas fa-filter me-2"></i>View All Notifications
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Load More Button (for AJAX loading) -->
    @if($notifications->hasMorePages())
    <div class="text-center mt-4">
        <button class="btn btn-outline-primary" id="loadMoreBtn" onclick="loadMoreNotifications()">
            <i class="fas fa-sync-alt me-2"></i>Load More Notifications
        </button>
    </div>
    @endif
</div>

<!-- Notification Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="notificationSettingsForm">
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="mb-3">Email Notifications</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailOrders" checked>
                            <label class="form-check-label" for="emailOrders">
                                Order updates and shipping notifications
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailPromotions">
                            <label class="form-check-label" for="emailPromotions">
                                Promotional offers and discounts
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailNewsletter">
                            <label class="form-check-label" for="emailNewsletter">
                                Newsletter and product updates
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Push Notifications</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pushOrders" checked>
                            <label class="form-check-label" for="pushOrders">
                                Order status changes
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pushMessages">
                            <label class="form-check-label" for="pushMessages">
                                Messages and support updates
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Notification Frequency</h6>
                        <select class="form-select" id="notificationFrequency">
                            <option value="instant">Instant (Recommended)</option>
                            <option value="daily">Daily Summary</option>
                            <option value="weekly">Weekly Summary</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <h6 class="mb-3">Auto-delete Notifications</h6>
                        <select class="form-select" id="autoDelete">
                            <option value="never">Never delete</option>
                            <option value="30">After 30 days</option>
                            <option value="60">After 60 days</option>
                            <option value="90">After 90 days</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
    position: relative;
}

.notification-item.unread {
    background-color: #f8f9ff;
    border-left: 4px solid #0d6efd;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
}

.notification-icon.order {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
}

.notification-icon.promotion {
    background: linear-gradient(135deg, #198754, #157347);
}

.notification-icon.system {
    background: linear-gradient(135deg, #6c757d, #5c636a);
}

.notification-icon.message {
    background: linear-gradient(135deg, #ffc107, #ffca2c);
}

.notification-icon.warning {
    background: linear-gradient(135deg, #fd7e14, #e8650e);
}

.notification-title {
    font-weight: 600;
    color: #212529;
}

.notification-message {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 0;
}

.notification-meta {
    display: flex;
    align-items: center;
}

.notification-actions .btn {
    font-size: 0.875rem;
}

.notification-data {
    font-size: 0.9rem;
}

.empty-state-icon i {
    opacity: 0.3;
}

/* Card view styles */
.notifications-card-view .notification-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    border-left-width: 1px !important;
}

.notifications-card-view .notification-item.unread {
    border-color: #0d6efd;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
    .notification-item .row {
        --bs-gutter-x: 0.5rem;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .notification-title {
        font-size: 0.9rem;
    }

    .notification-message {
        font-size: 0.85rem;
    }

    .notification-meta {
        flex-direction: column;
        align-items: flex-start;
    }

    .notification-meta .badge {
        margin-bottom: 0.25rem;
    }
}

/* Animation for new notifications */
@keyframes slideInFromTop {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item.new-notification {
    animation: slideInFromTop 0.5s ease-out;
}

/* Loading states */
.notification-item.loading {
    opacity: 0.6;
    pointer-events: none;
}

.notification-item.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #0d6efd;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Custom scrollbar for notification container */
#notifications-container {
    max-height: 70vh;
    overflow-y: auto;
}

#notifications-container::-webkit-scrollbar {
    width: 6px;
}

#notifications-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#notifications-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#notifications-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification functionality
    initializeNotifications();

    // Check for new notifications every 30 seconds
    setInterval(checkForNewNotifications, 30000);

    // Handle view mode switching
    document.querySelectorAll('input[name="view-mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleViewMode(this.id === 'card-view');
        });
    });
});

// Initialize notification features
function initializeNotifications() {
    // Add click handlers for notification items
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown') && !e.target.closest('.btn')) {
                const notificationId = this.dataset.notificationId;
                markAsRead(notificationId);
            }
        });
    });
}

// Mark single notification as read
function markAsRead(notificationId) {
    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationItem && !notificationItem.classList.contains('loading')) {
        notificationItem.classList.add('loading');

        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationItem.classList.remove('unread', 'loading');
                updateNotificationCounts();
                showToast('Notification marked as read', 'success');
            } else {
                throw new Error(data.message || 'Failed to mark as read');
            }
        })
        .catch(error => {
            notificationItem.classList.remove('loading');
            showToast('Error marking notification as read', 'error');
        });
    }
}

// Mark single notification as unread
function markAsUnread(notificationId) {
    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.classList.add('loading');

        fetch(`/notifications/${notificationId}/mark-unread`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationItem.classList.add('unread');
                notificationItem.classList.remove('loading');
                updateNotificationCounts();
                showToast('Notification marked as unread', 'success');
            } else {
                throw new Error(data.message || 'Failed to mark as unread');
            }
        })
        .catch(error => {
            notificationItem.classList.remove('loading');
            showToast('Error marking notification as unread', 'error');
        });
    }
}

// Mark all notifications as read
function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => item.classList.add('loading'));

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                unreadItems.forEach(item => {
                    item.classList.remove('unread', 'loading');
                });
                updateNotificationCounts();
                showToast(`${data.count} notifications marked as read`, 'success');

                // Hide the "mark all read" button
                const markAllBtn = document.querySelector('[onclick="markAllAsRead()"]');
                if (markAllBtn) {
                    markAllBtn.style.display = 'none';
                }
            } else {
                throw new Error(data.message || 'Failed to mark all as read');
            }
        })
        .catch(error => {
            unreadItems.forEach(item => item.classList.remove('loading'));
            showToast('Error marking all notifications as read', 'error');
        });
    }
}

// Delete notification
function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.classList.add('loading');

            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationItem.style.transition = 'all 0.3s ease';
                    notificationItem.style.opacity = '0';
                    notificationItem.style.transform = 'translateX(-100%)';

                    setTimeout(() => {
                        notificationItem.remove();
                        updateNotificationCounts();
                    }, 300);

                    showToast('Notification deleted', 'success');
                } else {
                    throw new Error(data.message || 'Failed to delete notification');
                }
            })
            .catch(error => {
                notificationItem.classList.remove('loading');
                showToast('Error deleting notification', 'error');
            });
        }
    }
}

// Check for new notifications
function checkForNewNotifications() {
    fetch('/notifications/check-new', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.hasNew) {
            updateNotificationCounts();
            showToast(`You have ${data.count} new notifications`, 'info');

            // Optionally reload the page or add new notifications to DOM
            if (data.count > 0) {
                // Add a reload button or automatically refresh
                const reloadBtn = document.createElement('button');
                reloadBtn.className = 'btn btn-primary btn-sm';
                reloadBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Refresh';
                reloadBtn.onclick = () => window.location.reload();

                // You could insert this button somewhere or show a notification
            }
        }
    })
    .catch(error => {
        console.log('Error checking for new notifications:', error);
    });
}

// Update notification counts in UI
function updateNotificationCounts() {
    fetch('/notifications/counts')
        .then(response => response.json())
        .then(data => {
            // Update badge in header if exists
            const headerBadge = document.querySelector('.navbar .notification-badge');
            if (headerBadge) {
                headerBadge.textContent = data.unread;
                headerBadge.style.display = data.unread > 0 ? 'inline' : 'none';
            }

            // Update page title badge
            const titleBadge = document.querySelector('h1 .badge');
            if (titleBadge) {
                if (data.unread > 0) {
                    titleBadge.textContent = data.unread;
                    titleBadge.style.display = 'inline';
                } else {
                    titleBadge.style.display = 'none';
                }
            }

            // Update stats cards
            const statsCards = document.querySelectorAll('.card-title');
            if (statsCards.length >= 2) {
                statsCards[0].textContent = data.total; // Total
                statsCards[1].textContent = data.unread; // Unread
            }
        })
        .catch(error => {
            console.log('Error updating notification counts:', error);
        });
}

// Toggle view mode between list and card
function toggleViewMode(isCardView) {
    const container = document.getElementById('notifications-container');
    if (isCardView) {
        container.classList.add('notifications-card-view');
    } else {
        container.classList.remove('notifications-card-view');
    }
}

// Load more notifications (AJAX pagination)
function loadMoreNotifications() {
    const btn = document.getElementById('loadMoreBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
    btn.disabled = true;

    const nextPageUrl = '{{ $notifications->nextPageUrl() }}';

    if (nextPageUrl) {
        fetch(nextPageUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                // Append new notifications to container
                const container = document.getElementById('notifications-container');
                container.insertAdjacentHTML('beforeend', data.html);

                // Initialize new notification items
                initializeNotifications();

                // Hide load more button if no more pages
                if (!data.hasMorePages) {
                    btn.style.display = 'none';
                }
            }
        })
        .catch(error => {
            showToast('Error loading more notifications', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}

// Handle notification settings form
document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const settings = {
        email_orders: document.getElementById('emailOrders').checked,
        email_promotions: document.getElementById('emailPromotions').checked,
        email_newsletter: document.getElementById('emailNewsletter').checked,
        push_orders: document.getElementById('pushOrders').checked,
        push_messages: document.getElementById('pushMessages').checked,
        frequency: document.getElementById('notificationFrequency').value,
        auto_delete: document.getElementById('autoDelete').value
    };

    fetch('/profile/notification-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
            showToast('Notification settings saved successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to save settings');
        }
    })
    .catch(error => {
        showToast('Error saving notification settings', 'error');
    });
});

// Toast notification helper
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${getToastIcon(type)} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });

    bsToast.show();

    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function getToastIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        case 'info': return 'info-circle';
        default: return 'bell';
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + A to mark all as read
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        const unreadCount = document.querySelectorAll('.notification-item.unread').length;
        if (unreadCount > 0) {
            markAllAsRead();
        }
    }

    // Delete key to delete selected notification (if any)
    if (e.key === 'Delete' && document.activeElement.closest('.notification-item')) {
        const notificationId = document.activeElement.closest('.notification-item').dataset.notificationId;
        if (notificationId) {
            deleteNotification(notificationId);
        }
    }
});

// Real-time notifications using WebSocket (if available)
function initializeRealTimeNotifications() {
    if (typeof Echo !== 'undefined') {
        Echo.private(`App.Models.User.{{ auth()->id() }}`)
            .notification((notification) => {
                // Add new notification to the top of the list
                addNewNotificationToDOM(notification);
                updateNotificationCounts();
                showToast('New notification received', 'info');
            });
    }
}

// Add new notification to DOM (for real-time updates)
function addNewNotificationToDOM(notification) {
    const container = document.getElementById('notifications-container');
    const newNotificationHTML = createNotificationHTML(notification);

    container.insertAdjacentHTML('afterbegin', newNotificationHTML);

    // Add animation class
    const newItem = container.firstElementChild;
    newItem.classList.add('new-notification');

    // Initialize click handlers for new item
    initializeNotifications();
}

// Create HTML for new notification
function createNotificationHTML(notification) {
    return `
        <div class="notification-item unread border-bottom new-notification"
             data-notification-id="${notification.id}"
             data-type="${notification.type}">
            <div class="p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="notification-icon ${notification.type}">
                            <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="notification-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="notification-title mb-0">${notification.title}</h6>
                                <div class="notification-meta">
                                    <span class="badge bg-primary">New</span>
                                    <small class="text-muted ms-2">Just now</small>
                                </div>
                            </div>
                            <p class="notification-message mb-2">${notification.message}</p>
                            ${notification.action_url ? `
                                <div class="notification-actions">
                                    <a href="${notification.action_url}"
                                       class="btn btn-primary btn-sm"
                                       onclick="markAsRead(${notification.id})">
                                        ${notification.action_text || 'View Details'}
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="markAsRead(${notification.id})">
                                        <i class="fas fa-check me-2"></i>Mark as Read
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteNotification(${notification.id})">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getNotificationIcon(type) {
    switch (type) {
        case 'order_update': return 'box';
        case 'promotion': return 'tags';
        case 'system': return 'cog';
        case 'message': return 'envelope';
        case 'warning': return 'exclamation-triangle';
        default: return 'bell';
    }
}

// Initialize real-time notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeRealTimeNotifications, 1000);
});

// Service Worker for push notifications (if supported)
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.ready.then(function(registration) {
        // Service worker is ready, can setup push notifications
        console.log('Service Worker ready for push notifications');
    });
}

// Handle visibility change to mark notifications as read when user focuses tab
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Tab became visible, check for updates
        setTimeout(checkForNewNotifications, 1000);
    }
});
</script>
@endpush
@endsection
