@extends('layouts.admin')

@section('title', 'Analytics & Reports')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-bar me-2"></i>Analytics & Business Intelligence</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-calendar me-1"></i> Date Range
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="setDateRange('today')">Today</a></li>
                <li><a class="dropdown-item" href="#" onclick="setDateRange('yesterday')">Yesterday</a></li>
                <li><a class="dropdown-item" href="#" onclick="setDateRange('week')">This Week</a></li>
                <li><a class="dropdown-item" href="#" onclick="setDateRange('month')">This Month</a></li>
                <li><a class="dropdown-item" href="#" onclick="setDateRange('quarter')">This Quarter</a></li>
                <li><a class="dropdown-item" href="#" onclick="setDateRange('year')">This Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="showCustomDateRange()">Custom Range</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Date Range Display -->
<div class="alert alert-info mb-4" id="dateRangeDisplay">
    <i class="fas fa-calendar me-2"></i>
    <strong>Period:</strong> <span id="currentDateRange">{{ $dateRange['label'] }}</span>
    <span class="ms-3">
        <strong>From:</strong> {{ $dateRange['start']->format('M d, Y') }}
        <strong>To:</strong> {{ $dateRange['end']->format('M d, Y') }}
    </span>
</div>

<!-- Key Performance Indicators -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">
                            {{ formatCurrency($kpis['total_revenue']) }}
                        </div>
                        <div class="text-xs">
                            <span class="text-{{ $kpis['revenue_growth'] >= 0 ? 'success' : 'danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($kpis['revenue_growth']), 1) }}%
                            </span>
                            <span class="text-muted">vs previous period</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">
                            {{ number_format($kpis['total_orders']) }}
                        </div>
                        <div class="text-xs">
                            <span class="text-{{ $kpis['orders_growth'] >= 0 ? 'success' : 'danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['orders_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($kpis['orders_growth']), 1) }}%
                            </span>
                            <span class="text-muted">vs previous period</span>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Order Value</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="averageOrderValue">
                            {{ formatCurrency($kpis['average_order_value']) }}
                        </div>
                        <div class="text-xs">
                            <span class="text-{{ $kpis['aov_growth'] >= 0 ? 'success' : 'danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['aov_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($kpis['aov_growth']), 1) }}%
                            </span>
                            <span class="text-muted">vs previous period</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Conversion Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="conversionRate">
                            {{ number_format($kpis['conversion_rate'], 2) }}%
                        </div>
                        <div class="text-xs">
                            <span class="text-{{ $kpis['conversion_growth'] >= 0 ? 'success' : 'danger' }}">
                                <i class="fas fa-arrow-{{ $kpis['conversion_growth'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($kpis['conversion_growth']), 1) }}%
                            </span>
                            <span class="text-muted">vs previous period</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row mb-4">
    <!-- Revenue Trend Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Trend</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="changeChartType('revenue', 'line')">Line Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeChartType('revenue', 'bar')">Bar Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeChartType('revenue', 'area')">Area Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Categories</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @foreach($topCategories as $index => $category)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ $categoryColors[$index] ?? '#6c757d' }}"></i>
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row mb-4">
    <!-- Order Status Distribution -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Status Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-4">
                    @foreach($orderStatusData as $status => $data)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ ucfirst($status) }}</span>
                            <span class="font-weight-bold">{{ $data['count'] }} ({{ $data['percentage'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Acquisition -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Acquisition & Retention</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="customerChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row mb-4">
    <!-- Top Products -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Sales</th>
                                <th>Revenue</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->primary_image)
                                            <img src="{{ asset('storage/' . $product->primary_image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="rounded me-2" width="30" height="30" style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="font-weight-bold">{{ Str::limit($product->name, 25) }}</div>
                                            <div class="text-muted small">{{ $product->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-weight-bold">{{ $product->sales_count }}</span>
                                    <div class="text-muted small">units</div>
                                </td>
                                <td>
                                    <span class="font-weight-bold">{{ formatCurrency($product->sales_revenue) }}</span>
                                </td>
                                <td>
                                    <span class="text-{{ $product->trend >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-arrow-{{ $product->trend >= 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($product->trend), 1) }}%
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

    <!-- Recent Orders -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent High-Value Orders</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">#{{ $order->order_number }}</div>
                                        <div class="text-muted small">{{ $order->created_at->format('M d, H:i') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">{{ $order->user->first_name }} {{ $order->user->last_name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-primary">{{ formatCurrency($order->total_cents) }}</span>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Analytics Row -->
<div class="row mb-4">
    <!-- Customer Insights -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Customer Insights</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>New Customers</span>
                        <span class="font-weight-bold text-success">{{ $customerInsights['new_customers'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Returning Customers</span>
                        <span class="font-weight-bold text-info">{{ $customerInsights['returning_customers'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Customer Lifetime Value</span>
                        <span class="font-weight-bold text-primary">{{ formatCurrency($customerInsights['avg_lifetime_value']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Repeat Purchase Rate</span>
                        <span class="font-weight-bold text-warning">{{ number_format($customerInsights['repeat_rate'], 1) }}%</span>
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-primary mb-3">Customer Segments</h6>
                    @foreach($customerInsights['segments'] as $segment => $data)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ ucfirst($segment) }}</span>
                            <span class="font-weight-bold">{{ $data['count'] }} ({{ $data['percentage'] }}%)</span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Financial Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Gross Revenue</span>
                        <span class="font-weight-bold text-success">{{ formatCurrency($financialSummary['gross_revenue']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Refunds</span>
                        <span class="font-weight-bold text-danger">{{ formatCurrency($financialSummary['refunds']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Net Revenue</span>
                        <span class="font-weight-bold text-primary">{{ formatCurrency($financialSummary['net_revenue']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Processing Fees</span>
                        <span class="font-weight-bold text-warning">{{ formatCurrency($financialSummary['processing_fees']) }}</span>
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-primary mb-3">Payment Methods</h6>
                    @foreach($financialSummary['payment_methods'] as $method)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $method['name'] }}</span>
                            <span class="font-weight-bold">{{ $method['percentage'] }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $method['percentage'] }}%"></div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <div class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        All amounts exclude taxes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Insights -->
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Insights</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Products</span>
                        <span class="font-weight-bold">{{ number_format($inventoryInsights['total_products']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Low Stock Items</span>
                        <span class="font-weight-bold text-warning">{{ $inventoryInsights['low_stock_count'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Out of Stock</span>
                        <span class="font-weight-bold text-danger">{{ $inventoryInsights['out_of_stock_count'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Inventory Value</span>
                        <span class="font-weight-bold text-success">{{ formatCurrency($inventoryInsights['total_value']) }}</span>
                    </div>
                </div>

                <hr>

                <div class="mt-4">
                    <h6 class="text-primary mb-3">Top Categories by Stock</h6>
                    @foreach($inventoryInsights['top_categories'] as $category)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $category['name'] }}</span>
                            <span class="font-weight-bold">{{ number_format($category['product_count']) }} items</span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $category['percentage'] }}%"></div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('admin.products.index', ['status' => 'low_stock']) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-exclamation-triangle me-1"></i>View Low Stock Items
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Date Range Modal -->
<div class="modal fade" id="customDateRangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customDateRangeForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="compareWithPrevious" name="compare_previous">
                        <label class="form-check-label" for="compareWithPrevious">
                            Compare with previous period
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Date Range</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts
    initializeRevenueChart();
    initializeCategoryChart();
    initializeOrderStatusChart();
    initializeCustomerChart();

    // Custom date range form
    document.getElementById('customDateRangeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        applyCustomDateRange();
    });

    // Auto-refresh data every 5 minutes
    setInterval(refreshAnalyticsData, 300000);
});

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenueChartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.labels,
            datasets: [{
                label: 'Revenue',
                data: revenueData.data,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }, {
                label: 'Previous Period',
                data: revenueData.previous_data,
                borderColor: '#858796',
                backgroundColor: 'rgba(133, 135, 150, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: {
                            day: 'MMM dd'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
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

function initializeCategoryChart() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryChartData);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.data,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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
                    callbacks: {
                        label: function(context) {
                            const percentage = ((context.parsed / categoryData.total) * 100).toFixed(1);
                            return context.label + ': ' + formatCurrency(context.parsed) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

function initializeOrderStatusChart() {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusData = @json($orderStatusChartData);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: orderStatusData.labels,
            datasets: [{
                data: orderStatusData.data,
                backgroundColor: [
                    '#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b', '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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
                    callbacks: {
                        label: function(context) {
                            const percentage = ((context.parsed / orderStatusData.total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' orders (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function initializeCustomerChart() {
    const ctx = document.getElementById('customerChart').getContext('2d');
    const customerData = @json($customerChartData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: customerData.labels,
            datasets: [{
                label: 'New Customers',
                data: customerData.new_customers,
                backgroundColor: '#1cc88a',
                borderColor: '#1cc88a',
                borderWidth: 1
            }, {
                label: 'Returning Customers',
                data: customerData.returning_customers,
                backgroundColor: '#36b9cc',
                borderColor: '#36b9cc',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
}

function setDateRange(range) {
    showLoading('Updating analytics data...');

    fetch(`{{ route('admin.analytics.update-range') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ range: range })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            location.reload();
        } else {
            showAlert('Failed to update date range.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while updating date range.', 'danger');
        console.error('Error:', error);
    });
}

function showCustomDateRange() {
    const modal = new bootstrap.Modal(document.getElementById('customDateRangeModal'));
    modal.show();
}

function applyCustomDateRange() {
    const form = document.getElementById('customDateRangeForm');
    const formData = new FormData(form);

    showLoading('Applying custom date range...');

    fetch(`{{ route('admin.analytics.custom-range') }}`, {
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
            bootstrap.Modal.getInstance(document.getElementById('customDateRangeModal')).hide();
            location.reload();
        } else {
            showAlert(data.message || 'Failed to apply custom date range.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while applying date range.', 'danger');
        console.error('Error:', error);
    });
}

function changeChartType(chartName, type) {
    showLoading('Changing chart type...');

    fetch(`{{ route('admin.analytics.change-chart-type') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            chart: chartName,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Reinitialize the specific chart with new type
            if (chartName === 'revenue') {
                initializeRevenueChart();
            }
            showAlert('Chart type updated successfully.', 'success');
        } else {
            showAlert('Failed to change chart type.', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('An error occurred while changing chart type.', 'danger');
        console.error('Error:', error);
    });
}

function exportReport(format) {
    showLoading('Generating report...');

    const params = new URLSearchParams();
    params.append('format', format);
    params.append('date_range', document.getElementById('currentDateRange').textContent);

    const exportUrl = `{{ route('admin.analytics.export') }}?${params.toString()}`;

    // Create temporary link and click it
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `analytics-report-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => {
        hideLoading();
        showAlert(`Analytics report exported successfully as ${format.toUpperCase()}.`, 'success');
    }, 2000);
}

function refreshAnalyticsData() {
    fetch('{{ route("admin.analytics.refresh") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update KPIs
            document.getElementById('totalRevenue').textContent = formatCurrency(data.kpis.total_revenue);
            document.getElementById('totalOrders').textContent = data.kpis.total_orders.toLocaleString();
            document.getElementById('averageOrderValue').textContent = formatCurrency(data.kpis.average_order_value);
            document.getElementById('conversionRate').textContent = data.kpis.conversion_rate.toFixed(2) + '%';

            // Update charts if needed
            // This would require more complex chart update logic
        }
    })
    .catch(error => {
        console.error('Error refreshing analytics data:', error);
    });
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

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

// Real-time updates with WebSocket (if available)
if (typeof window.Echo !== 'undefined') {
    window.Echo.channel('analytics')
        .listen('AnalyticsUpdated', (e) => {
            refreshAnalyticsData();
        });
}
</script>

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

.chart-area {
    position: relative;
    height: 400px;
}

.chart-pie {
    position: relative;
    height: 300px;
}

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table thead th {
    border-bottom: 2px solid #e3e6f0;
    font-weight: 600;
    color: #5a5c69;
    background-color: #f8f9fc;
}

.progress {
    background-color: #e3e6f0;
}

.progress-bar {
    background-color: #4e73df;
}

.badge.bg-success {
    background-color: #1cc88a !important;
}

.badge.bg-warning {
    background-color: #f6c23e !important;
    color: #333;
}

.badge.bg-info {
    background-color: #36b9cc !important;
}

.badge.bg-danger {
    background-color: #e74a3b !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-warning {
    color: #f6c23e !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-danger {
    color: #e74a3b !important;
}

.text-primary {
    color: #4e73df !important;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.btn-outline-warning:hover {
    background-color: #f6c23e;
    border-color: #f6c23e;
    color: #333;
}

.dropdown-menu {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}

.dropdown-item:hover {
    background-color: #f8f9fc;
}

.modal-content {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.form-control:focus,
.form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
}

@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-group {
        width: 100%;
    }

    .card-body {
        padding: 1rem;
    }

    .chart-area,
    .chart-pie {
        height: 250px;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .h5 {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .col-xl-3 {
        margin-bottom: 1rem;
    }

    .card-body .d-flex {
        flex-direction: column;
        align-items: flex-start;
    }

    .chart-area,
    .chart-pie {
        height: 200px;
    }
}

/* Custom loading animation */
#loadingOverlay {
    backdrop-filter: blur(2px);
}

#loadingOverlay .bg-white {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Chart tooltip improvements */
.chartjs-tooltip {
    background: rgba(0, 0, 0, 0.8);
    border-radius: 4px;
    color: white;
    font-size: 12px;
    padding: 8px;
}

/* Enhanced scrollbar */
.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.card-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endpush
