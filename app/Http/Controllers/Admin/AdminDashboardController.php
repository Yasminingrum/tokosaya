<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Payment;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {;
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin,staff');
    }

    /**
     * Main admin dashboard
     */
    public function index()
    {
        $data = Cache::remember('admin_dashboard_data', 300, function () {
            // Key metrics for today and this month
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            // Orders statistics
            $orderStats = [
                'today' => [
                    'count' => Order::whereDate('created_at', $today)->count(),
                    'revenue' => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_cents') / 100
                ],
                'this_month' => [
                    'count' => Order::where('created_at', '>=', $thisMonth)->count(),
                    'revenue' => Order::where('created_at', '>=', $thisMonth)->where('payment_status', 'paid')->sum('total_cents') / 100
                ],
                'last_month' => [
                    'count' => Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count(),
                    'revenue' => Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->where('payment_status', 'paid')->sum('total_cents') / 100
                ]
            ];

            // Calculate growth percentages
            $orderStats['growth'] = [
                'orders' => $orderStats['last_month']['count'] > 0 ?
                    round((($orderStats['this_month']['count'] - $orderStats['last_month']['count']) / $orderStats['last_month']['count']) * 100, 1) : 0,
                'revenue' => $orderStats['last_month']['revenue'] > 0 ?
                    round((($orderStats['this_month']['revenue'] - $orderStats['last_month']['revenue']) / $orderStats['last_month']['revenue']) * 100, 1) : 0
            ];

            // General statistics
            $generalStats = [
                'total_products' => Product::count(),
                'active_products' => Product::where('status', 'active')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                'low_stock' => Product::whereRaw('stock_quantity <= min_stock_level')->where('stock_quantity', '>', 0)->count(),
                'total_customers' => User::whereHas('role', function($q) { $q->where('name', 'customer'); })->count(),
                'new_customers_today' => User::whereHas('role', function($q) { $q->where('name', 'customer'); })->whereDate('created_at', $today)->count(),
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'processing_orders' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
                'pending_payments' => Payment::where('status', 'pending')->count()
            ];

            return [
                'order_stats' => $orderStats,
                'general_stats' => $generalStats
            ];
        });

        // Recent orders (not cached for real-time data)
        $recentOrders = Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('stock_quantity', '>', 0)
            ->where('status', 'active')
            ->with(['category'])
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Top selling products this month
        $topProducts = $this->getTopSellingProducts();

        return view('admin.dashboard.index', array_merge($data, [
            'recent_orders' => $recentOrders,
            'low_stock_products' => $lowStockProducts,
            'top_products' => $topProducts
        ]));
    }

    /**
     * Get analytics data for charts
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        $data = Cache::remember("analytics_data_{$period}", 1800, function () use ($startDate, $period) {
            // Daily sales data
            $salesData = Order::where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_cents) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'orders' => $item->orders,
                        'revenue' => $item->revenue / 100
                    ];
                });

            // Fill missing dates with zero values
            $dateRange = collect();
            for ($i = $period - 1; $i >= 0; $i--) {
                $dateRange->push(Carbon::now()->subDays($i)->format('Y-m-d'));
            }

            $completeSalesData = $dateRange->map(function ($date) use ($salesData) {
                $existing = $salesData->firstWhere('date', $date);
                return [
                    'date' => $date,
                    'formatted_date' => Carbon::parse($date)->format('M d'),
                    'orders' => $existing ? $existing['orders'] : 0,
                    'revenue' => $existing ? $existing['revenue'] : 0
                ];
            });

            // Order status distribution
            $orderStatusData = Order::where('created_at', '>=', $startDate)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => $item->count];
                });

            // Top categories by sales
            $topCategories = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.created_at', '>=', $startDate)
                ->where('orders.payment_status', 'paid')
                ->selectRaw('categories.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price_cents) as total_revenue')
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'total_sold' => $item->total_sold,
                        'total_revenue' => $item->total_revenue / 100
                    ];
                });

            return [
                'sales_data' => $completeSalesData,
                'order_status_data' => $orderStatusData,
                'top_categories' => $topCategories
            ];
        });

        return response()->json($data);
    }

    /**
     * Get reports data
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
        }

        $data = Cache::remember("reports_data_{$period}", 1800, function () use ($startDate, $period) {
            // Sales summary
            $salesSummary = Order::where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->selectRaw('
                    COUNT(*) as total_orders,
                    SUM(total_cents) as total_revenue,
                    AVG(total_cents) as avg_order_value,
                    SUM(tax_cents) as total_tax,
                    SUM(shipping_cents) as total_shipping,
                    SUM(discount_cents) as total_discounts
                ')
                ->first();

            $salesSummary->total_revenue = $salesSummary->total_revenue / 100;
            $salesSummary->avg_order_value = $salesSummary->avg_order_value / 100;
            $salesSummary->total_tax = $salesSummary->total_tax / 100;
            $salesSummary->total_shipping = $salesSummary->total_shipping / 100;
            $salesSummary->total_discounts = $salesSummary->total_discounts / 100;

            // Customer metrics
            $customerMetrics = [
                'new_customers' => User::whereHas('role', function($q) { $q->where('name', 'customer'); })
                    ->where('created_at', '>=', $startDate)
                    ->count(),
                'returning_customers' => Order::where('created_at', '>=', $startDate)
                    ->whereIn('user_id', function($query) use ($startDate) {
                        $query->select('user_id')
                            ->from('orders')
                            ->where('created_at', '<', $startDate)
                            ->groupBy('user_id');
                    })
                    ->distinct('user_id')
                    ->count()
            ];

            // Product performance
            $productPerformance = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.created_at', '>=', $startDate)
                ->where('orders.payment_status', 'paid')
                ->selectRaw('
                    products.name,
                    SUM(order_items.quantity) as units_sold,
                    SUM(order_items.total_price_cents) as revenue,
                    AVG(order_items.unit_price_cents) as avg_price
                ')
                ->groupBy('products.id', 'products.name')
                ->orderBy('revenue', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'units_sold' => $item->units_sold,
                        'revenue' => $item->revenue / 100,
                        'avg_price' => $item->avg_price / 100
                    ];
                });

            return [
                'sales_summary' => $salesSummary,
                'customer_metrics' => $customerMetrics,
                'product_performance' => $productPerformance,
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d')
            ];
        });

        return response()->json($data);
    }

    /**
     * Get sales chart data
     */
    public function salesChart(Request $request)
    {
        $period = $request->get('period', '7'); // days
        $startDate = Carbon::now()->subDays($period);

        $data = Cache::remember("sales_chart_{$period}", 900, function () use ($startDate, $period) {
            $salesData = Order::where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_cents) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Create date range
            $dateRange = collect();
            for ($i = $period - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $existing = $salesData->firstWhere('date', $date->format('Y-m-d'));

                $dateRange->push([
                    'date' => $date->format('Y-m-d'),
                    'formatted_date' => $date->format('M d'),
                    'day_name' => $date->format('D'),
                    'orders' => $existing ? $existing->orders : 0,
                    'revenue' => $existing ? $existing->revenue / 100 : 0
                ]);
            }

            return $dateRange;
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Get overview data
     */
    public function overview()
    {
        $data = Cache::remember('admin_overview', 600, function () {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $thisWeek = Carbon::now()->startOfWeek();
            $lastWeek = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

            // Today vs Yesterday comparison
            $todayStats = [
                'orders' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_cents') / 100,
                'customers' => User::whereDate('created_at', $today)->count()
            ];

            $yesterdayStats = [
                'orders' => Order::whereDate('created_at', $yesterday)->count(),
                'revenue' => Order::whereDate('created_at', $yesterday)->where('payment_status', 'paid')->sum('total_cents') / 100,
                'customers' => User::whereDate('created_at', $yesterday)->count()
            ];

            // This week vs Last week comparison
            $thisWeekStats = [
                'orders' => Order::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => Order::where('created_at', '>=', $thisWeek)->where('payment_status', 'paid')->sum('total_cents') / 100
            ];

            $lastWeekStats = [
                'orders' => Order::whereBetween('created_at', [$lastWeek, $lastWeekEnd])->count(),
                'revenue' => Order::whereBetween('created_at', [$lastWeek, $lastWeekEnd])->where('payment_status', 'paid')->sum('total_cents') / 100
            ];

            // Calculate percentage changes
            $dailyChanges = [
                'orders' => $yesterdayStats['orders'] > 0 ? round((($todayStats['orders'] - $yesterdayStats['orders']) / $yesterdayStats['orders']) * 100, 1) : 0,
                'revenue' => $yesterdayStats['revenue'] > 0 ? round((($todayStats['revenue'] - $yesterdayStats['revenue']) / $yesterdayStats['revenue']) * 100, 1) : 0,
                'customers' => $yesterdayStats['customers'] > 0 ? round((($todayStats['customers'] - $yesterdayStats['customers']) / $yesterdayStats['customers']) * 100, 1) : 0
            ];

            $weeklyChanges = [
                'orders' => $lastWeekStats['orders'] > 0 ? round((($thisWeekStats['orders'] - $lastWeekStats['orders']) / $lastWeekStats['orders']) * 100, 1) : 0,
                'revenue' => $lastWeekStats['revenue'] > 0 ? round((($thisWeekStats['revenue'] - $lastWeekStats['revenue']) / $lastWeekStats['revenue']) * 100, 1) : 0
            ];

            // Inventory alerts
            $inventoryAlerts = [
                'out_of_stock' => Product::where('stock_quantity', 0)->where('status', 'active')->count(),
                'low_stock' => Product::whereRaw('stock_quantity <= min_stock_level')->where('stock_quantity', '>', 0)->where('status', 'active')->count(),
                'critical_stock' => Product::where('stock_quantity', '<=', 2)->where('status', 'active')->count()
            ];

            // Recent activities
            $recentActivities = DB::table('activity_logs')
                ->join('users', 'activity_logs.user_id', '=', 'users.id')
                ->select('activity_logs.*', 'users.first_name', 'users.last_name')
                ->orderBy('activity_logs.created_at', 'desc')
                ->limit(10)
                ->get();

            return [
                'today_stats' => $todayStats,
                'yesterday_stats' => $yesterdayStats,
                'this_week_stats' => $thisWeekStats,
                'last_week_stats' => $lastWeekStats,
                'daily_changes' => $dailyChanges,
                'weekly_changes' => $weeklyChanges,
                'inventory_alerts' => $inventoryAlerts,
                'recent_activities' => $recentActivities
            ];
        });

        return response()->json($data);
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts($limit = 5)
    {
        return Cache::remember('top_selling_products', 1800, function () use ($limit) {
            $startDate = Carbon::now()->startOfMonth();

            return DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.created_at', '>=', $startDate)
                ->where('orders.payment_status', 'paid')
                ->selectRaw('
                    products.id,
                    products.name,
                    products.price_cents,
                    SUM(order_items.quantity) as total_sold,
                    SUM(order_items.total_price_cents) as total_revenue
                ')
                ->groupBy('products.id', 'products.name', 'products.price_cents')
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price_cents / 100,
                        'total_sold' => $item->total_sold,
                        'total_revenue' => $item->total_revenue / 100
                    ];
                });
        });
    }

    /**
     * Export dashboard data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', '30');

        try {
            switch ($type) {
                case 'sales':
                    return $this->exportSalesData($period);
                case 'products':
                    return $this->exportProductsData($period);
                case 'customers':
                    return $this->exportCustomersData($period);
                default:
                    return back()->withErrors(['error' => 'Invalid export type']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Export failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Export sales data
     */
    private function exportSalesData($period)
    {
        $startDate = Carbon::now()->subDays($period);

        $salesData = Order::where('created_at', '>=', $startDate)
            ->with(['user', 'items.product'])
            ->get()
            ->map(function ($order) {
                return [
                    'Order Number' => $order->order_number,
                    'Date' => $order->created_at->format('Y-m-d H:i:s'),
                    'Customer' => $order->user->first_name . ' ' . $order->user->last_name,
                    'Email' => $order->user->email,
                    'Status' => ucfirst($order->status),
                    'Payment Status' => ucfirst($order->payment_status),
                    'Items Count' => $order->items->count(),
                    'Subtotal' => $order->subtotal_cents / 100,
                    'Tax' => $order->tax_cents / 100,
                    'Shipping' => $order->shipping_cents / 100,
                    'Discount' => $order->discount_cents / 100,
                    'Total' => $order->total_cents / 100
                ];
            });

        $filename = 'sales_data_' . $period . '_days_' . now()->format('Y-m-d') . '.csv';

        return $this->generateCSV($salesData, $filename);
    }

    /**
     * Export products data
     */
    private function exportProductsData($period)
    {
        $startDate = Carbon::now()->subDays($period);

        $productsData = Product::where('created_at', '>=', $startDate)
            ->withCount(['orders as total_orders' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->get()
            ->map(function ($product) {
                return [
                    'Product Name' => $product->name,
                    'SKU' => $product->sku ?? '',
                    'Category' => optional($product->category)->name ?? '',
                    'Price' => $product->price_cents / 100,
                    'Stock Quantity' => $product->stock_quantity,
                    'Status' => ucfirst($product->status),
                    'Created At' => $product->created_at->format('Y-m-d H:i:s'),
                    'Total Orders' => $product->total_orders ?? 0
                ];
            });

        $filename = 'products_data_' . $period . '_days_' . now()->format('Y-m-d') . '.csv';

        return $this->generateCSV($productsData, $filename);
    }

    /**
     * Export customers data
     */
    private function exportCustomersData($period)
    {
        $startDate = Carbon::now()->subDays($period);

        $customersData = User::whereHas('role', function($q) { $q->where('name', 'customer'); })
            ->where('created_at', '>=', $startDate)
            ->get()
            ->map(function ($user) {
                return [
                    'First Name' => $user->first_name,
                    'Last Name' => $user->last_name,
                    'Email' => $user->email,
                    'Phone' => $user->phone ?? '',
                    'Registered At' => $user->created_at->format('Y-m-d H:i:s'),
                    'Total Orders' => $user->orders()->count(),
                    'Total Spent' => number_format($user->orders()->where('payment_status', 'paid')->sum('total_cents') / 100, 2)
                ];
            });

        $filename = 'customers_data_' . $period . '_days_' . now()->format('Y-m-d') . '.csv';

        return $this->generateCSV($customersData, $filename);
    }

    /**
     * Generate CSV file
     */
    private function generateCSV($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if ($data->isNotEmpty()) {
                // Write headers
                fputcsv($file, array_keys($data->first()));

                // Write data
                foreach ($data as $row) {
                    fputcsv($file, array_values($row));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
