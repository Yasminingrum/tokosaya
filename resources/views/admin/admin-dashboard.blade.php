@extends('layouts.admin')

@section('title', 'Admin Dashboard - TokoSaya')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->first_name }}! Here's what's happening today.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/admin/export/dashboard?format=pdf">
                        <i class="fas fa-file-pdf me-2"></i>PDF Report
                    </a></li>
                    <li><a class="dropdown-item" href="/admin/export/dashboard?format=excel">
                        <i class="fas fa-file-excel me-2"></i>Excel Report
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Helpers\PriceHelper::format($dashboardStats['today_revenue']) }}
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-{{ $dashboardStats['revenue_trend'] >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $dashboardStats['revenue_trend'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ abs($dashboardStats['revenue_trend']) }}%
                                </span>
                                vs yesterday
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupiah-sign fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Orders Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($dashboardStats['today_orders']) }}
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-{{ $dashboardStats['orders_trend'] >= 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $dashboardStats['orders_trend'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ abs($dashboardStats['orders_trend']) }}%
                                </span>
                                vs yesterday
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                New Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($dashboardStats['new_customers']) }}
                            </div>
                            <div class="text-xs mt-1">
                                <span class="text-success">
                                    <i class="fas fa-user-plus me-1"></i>
                                    This month
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($dashboardStats['pending_orders']) }}
                            </div>
                            <div class="text-xs mt-1">
                                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-warning">
                                    <i class="fas fa-clock me-1"></i>
                                    Needs attention
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <div class="dropdown-header">Period:</div>
                            <a class="dropdown-item" href="#" onclick="updateChart('7days')">Last 7 Days</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('30days')">Last 30 Days</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('90days')">Last 90 Days</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Completed
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Processing
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Cancelled
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ isset($order['id']) ? route('admin.orders.show', $order['id']) : '#' }}" class="text-decoration-none">
                                            #{{ $order['order_number'] ?? 'ORD-' . $order['id'] }}
                                        </a>
                                    </td>
                                    <td>{{ $order['first_name'] ?? 'Guest' }} {{ $order['last_name'] ?? '' }}</td>
                                    <td>{{ \App\Helpers\PriceHelper::format($order['total_cents'] ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order['status_color'] ?? 'secondary' }}">
                                            {{ ucfirst($order['status'] ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sales</th>
                                    <th>Revenue</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $product->primary_image_url }}"
                                                 class="rounded me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="text-decoration-none">
                                                    {{ Str::limit($product->name, 30) }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($product->sale_count) }}</td>
                                    <td>{{ \App\Helpers\PriceHelper::format($product->revenue_cents) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & Quick Actions -->
    <div class="row mb-4">
        <!-- System Alerts -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Alerts</h6>
                </div>
                <div class="card-body">
                    <div class="alerts-container">
                        @if($systemAlerts['low_stock_count'] > 0)
                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="fas fa-exclamation-triangle me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Low Stock Alert</strong>
                                <p class="mb-0">{{ $systemAlerts['low_stock_count'] }} products are running low on stock.</p>
                            </div>
                            <a href="{{ route('admin.products.index', ['filter' => 'low_stock']) }}" class="btn btn-warning btn-sm">
                                View Products
                            </a>
                        </div>
                        @endif

                        @if($systemAlerts['pending_reviews'] > 0)
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="fas fa-star me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Reviews Pending</strong>
                                <p class="mb-0">{{ $systemAlerts['pending_reviews'] }} product reviews are waiting for approval.</p>
                            </div>
                            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-info btn-sm">
                                Review Now
                            </a>
                        </div>
                        @endif

                        @if($systemAlerts['failed_payments'] > 0)
                        <div class="alert alert-danger d-flex align-items-center mb-3">
                            <i class="fas fa-credit-card me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Payment Issues</strong>
                                <p class="mb-0">{{ $systemAlerts['failed_payments'] }} orders have payment issues.</p>
                            </div>
                            <a href="{{ route('admin.orders.index', ['payment_status' => 'failed']) }}" class="btn btn-danger btn-sm">
                                Investigate
                            </a>
                        </div>
                        @endif

                        @if(empty($systemAlerts) || (
                            $systemAlerts['low_stock_count'] == 0 &&
                            $systemAlerts['pending_reviews'] == 0 &&
                            $systemAlerts['failed_payments'] == 0
                        ))
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-3"></i>
                            <div>
                                <strong>All Systems Running Smoothly</strong>
                                <p class="mb-0">No critical issues requiring immediate attention.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Add New Product
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>Process Pending Orders
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-info">
                            <i class="fas fa-users me-2"></i>Manage Customers
                        </a>
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-2"></i>View Reports
                        </a>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkActionsModal">
                            <i class="fas fa-cogs me-2"></i>Bulk Actions
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="activity-feed" style="max-height: 300px; overflow-y: auto;">
                        @foreach($recentActivities as $activity)
                        <div class="activity-item mb-3 pb-2 border-bottom">
                            <div class="d-flex">
                                <div class="activity-icon me-3">
                                    <i class="fas fa-{{ $activity->icon }} text-{{ $activity->color }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="activity-content">
                                        <strong>{{ $activity->user->first_name ?? 'System' }}</strong>
                                        {{ $activity->description }}
                                    </div>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <button class="list-group-item list-group-item-action" onclick="bulkUpdateOrderStatus()">
                        <i class="fas fa-boxes me-2"></i>Update Order Status (Bulk)
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="bulkProductActions()">
                        <i class="fas fa-cubes me-2"></i>Bulk Product Management
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="exportData()">
                        <i class="fas fa-download me-2"></i>Export Data
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="systemMaintenance()">
                        <i class="fas fa-tools me-2"></i>System Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-xs {
    font-size: .7rem;
}

.chart-area {
    position: relative;
    height: 10rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

.activity-feed {
    padding-right: 0.5rem;
}

.activity-feed::-webkit-scrollbar {
    width: 4px;
}

.activity-feed::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.activity-feed::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 2px;
}

.activity-feed::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.activity-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8f9fc;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03) !important;
}

.stats-card {
    cursor: pointer;
}

.stats-card:hover .card-body {
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
}

@media (max-width: 768px) {
    .col-xl-3 {
        margin-bottom: 1rem;
    }

    .chart-area {
        height: 8rem;
    }

    .chart-pie {
        height: 12rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

.dashboard-loading {
    opacity: 0.6;
    pointer-events: none;
}

.dashboard-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #4e73df;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 9999;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

.alert {
    animation: slideInFromLeft 0.5s ease-out;
}

@keyframes slideInFromLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeRealTimeUpdates();

    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

// Initialize charts
function initializeCharts() {
    initializeRevenueChart();
    initializeOrderStatusChart();
}

// Revenue Chart
function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    // Sample data - replace with actual data from controller
    const revenueData = @json($chartData['revenue']);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.labels,
            datasets: [{
                label: 'Revenue',
                data: revenueData.values,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#858796'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(234, 236, 244, 0.5)'
                    },
                    ticks: {
                        color: '#858796',
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// Order Status Chart
function initializeOrderStatusChart() {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');

    const orderStatusData = @json($chartData['orderStatus']);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: orderStatusData.labels,
            datasets: [{
                data: orderStatusData.values,
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#f6c23e',
                    '#e74a3b'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

// Update chart data
function updateChart(period) {
    showLoading();

    fetch(`/dashboard/chart-data?period=${period}`)
        .then(response => response.json())
        .then(data => {
            // Update revenue chart
            const revenueChart = Chart.getChart('revenueChart');
            revenueChart.data.labels = data.revenue.labels;
            revenueChart.data.datasets[0].data = data.revenue.values;
            revenueChart.update();

            hideLoading();
            showToast('Chart updated successfully', 'success');
        })
        .catch(error => {
            hideLoading();
            showToast('Error updating chart', 'error');
        });
}

// Refresh dashboard data
function refreshDashboard() {
    refreshDashboardData();
}

function refreshDashboardData() {
    showLoading();

    fetch('/dashboard/refresh')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.stats);
                updateRecentData(data.recent);
                updateAlerts(data.alerts);
                showToast('Dashboard refreshed', 'success');
            }
        })
        .catch(error => {
            showToast('Error refreshing dashboard', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

// Update dashboard statistics
function updateDashboardStats(stats) {
    // Update revenue
    const revenueElement = document.querySelector('.border-left-primary .h5');
    if (revenueElement) {
        revenueElement.textContent = formatCurrency(stats.today_revenue);
    }

    // Update orders
    const ordersElement = document.querySelector('.border-left-success .h5');
    if (ordersElement) {
        ordersElement.textContent = stats.today_orders.toLocaleString();
    }

    // Update customers
    const customersElement = document.querySelector('.border-left-info .h5');
    if (customersElement) {
        customersElement.textContent = stats.new_customers.toLocaleString();
    }

    // Update pending orders
    const pendingElement = document.querySelector('.border-left-warning .h5');
    if (pendingElement) {
        pendingElement.textContent = stats.pending_orders.toLocaleString();
    }
}

// Update recent data
function updateRecentData(recent) {
    // Update recent orders table
    const ordersTableBody = document.querySelector('.card:has([href*="orders"]) tbody');
    if (ordersTableBody && recent.orders) {
        ordersTableBody.innerHTML = recent.orders.map(order => `
            <tr>
                <td><a href="/admin/orders/${order.id}" class="text-decoration-none">#${order.order_number}</a></td>
                <td>${order.customer_name}</td>
                <td>${formatCurrency(order.total_cents)}</td>
                <td><span class="badge bg-${order.status_color}">${order.status}</span></td>
            </tr>
        `).join('');
    }

    // Update top products table
    const productsTableBody = document.querySelector('.card:has([href*="products"]) tbody');
    if (productsTableBody && recent.products) {
        productsTableBody.innerHTML = recent.products.map(product => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${product.image}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <a href="/admin/products/${product.id}" class="text-decoration-none">
                                ${product.name.length > 30 ? product.name.substring(0, 30) + '...' : product.name}
                            </a>
                        </div>
                    </div>
                </td>
                <td>${product.sales.toLocaleString()}</td>
                <td>${formatCurrency(product.revenue)}</td>
                <td><span class="badge bg-${product.stock > 10 ? 'success' : (product.stock > 0 ? 'warning' : 'danger')}">${product.stock}</span></td>
            </tr>
        `).join('');
    }
}

// Update system alerts
function updateAlerts(alerts) {
    const alertsContainer = document.querySelector('.alerts-container');
    if (alertsContainer) {
        let alertsHTML = '';

        if (alerts.low_stock_count > 0) {
            alertsHTML += `
                <div class="alert alert-warning d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Low Stock Alert</strong>
                        <p class="mb-0">${alerts.low_stock_count} products are running low on stock.</p>
                    </div>
                    <a href="/admin/products?filter=low_stock" class="btn btn-warning btn-sm">View Products</a>
                </div>
            `;
        }

        if (alerts.pending_reviews > 0) {
            alertsHTML += `
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="fas fa-star me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Reviews Pending</strong>
                        <p class="mb-0">${alerts.pending_reviews} product reviews are waiting for approval.</p>
                    </div>
                    <a href="/admin/reviews?status=pending" class="btn btn-info btn-sm">Review Now</a>
                </div>
            `;
        }

        if (alerts.failed_payments > 0) {
            alertsHTML += `
                <div class="alert alert-danger d-flex align-items-center mb-3">
                    <i class="fas fa-credit-card me-3"></i>
                    <div class="flex-grow-1">
                        <strong>Payment Issues</strong>
                        <p class="mb-0">${alerts.failed_payments} orders have payment issues.</p>
                    </div>
                    <a href="/admin/orders?payment_status=failed" class="btn btn-danger btn-sm">Investigate</a>
                </div>
            `;
        }

        if (alertsHTML === '') {
            alertsHTML = `
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-3"></i>
                    <div>
                        <strong>All Systems Running Smoothly</strong>
                        <p class="mb-0">No critical issues requiring immediate attention.</p>
                    </div>
                </div>
            `;
        }

        alertsContainer.innerHTML = alertsHTML;
    }
}

// Real-time updates using WebSocket
function initializeRealTimeUpdates() {
    if (typeof Echo !== 'undefined') {
        Echo.channel('admin-dashboard')
            .listen('NewOrderPlaced', (e) => {
                updateOrderCount();
                showToast('New order received!', 'info');
            })
            .listen('StockAlert', (e) => {
                showToast(`Low stock alert: ${e.product_name}`, 'warning');
                refreshDashboardData();
            });
    }
}

// Bulk actions
function bulkUpdateOrderStatus() {
    bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal')).hide();
    window.location.href = '/admin/orders?bulk_action=status_update';
}

function bulkProductActions() {
    bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal')).hide();
    window.location.href = '/admin/products?bulk_action=manage';
}

function exportData() {
    bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal')).hide();
    window.open('/admin/export/dashboard?format=excel', '_blank');
}

function systemMaintenance() {
    bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal')).hide();
    if (confirm('This will perform system maintenance tasks. Continue?')) {
        fetch('/admin/system/maintenance', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
        });
    }
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount / 100);
}

function showLoading() {
    document.querySelector('.container-fluid').classList.add('dashboard-loading');
}

function hideLoading() {
    document.querySelector('.container-fluid').classList.remove('dashboard-loading');
}

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

// Add click handlers for stat cards
document.querySelectorAll('.border-left-primary, .border-left-success, .border-left-info, .border-left-warning').forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', function() {
        const cardType = this.className.includes('primary') ? 'revenue' :
                        this.className.includes('success') ? 'orders' :
                        this.className.includes('info') ? 'customers' : 'pending';

        switch (cardType) {
            case 'revenue':
                window.location.href = '/admin/reports/revenue';
                break;
            case 'orders':
                window.location.href = '/admin/orders';
                break;
            case 'customers':
                window.location.href = '/admin/users';
                break;
            case 'pending':
                window.location.href = '/admin/orders?status=pending';
                break;
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R to refresh dashboard
    if ((e.ctrlKey || e.metaKey) && e.key === 'r' && !e.shiftKey) {
        e.preventDefault();
        refreshDashboard();
    }

    // Ctrl/Cmd + N to add new product
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '/admin/products/create';
    }
});
</script>
@endpush
@endsection
