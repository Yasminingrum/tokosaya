<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        try {
            // Get dashboard statistics
            $dashboardStats = $this->getDashboardStats();

            // Get recent orders
            $recentOrders = $this->getRecentOrdersData();

            // Get top products
            $topProducts = $this->getTopProductsData();

            // Get system alerts
            $systemAlerts = $this->getSystemAlerts();

            // Get recent activities
            $recentActivities = $this->getRecentActivities();

            // Get chart data
            $chartData = $this->getChartData();

            return view('admin.admin-dashboard', compact(
                'dashboardStats',
                'recentOrders',
                'topProducts',
                'systemAlerts',
                'recentActivities',
                'chartData'
            ));

        } catch (\Exception $e) {
            Log::error('Admin dashboard load failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return with default data to prevent error
            return view('admin.admin-dashboard', $this->getDefaultDashboardData());
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $stats = [
            'today_revenue' => 0,
            'today_orders' => 0,
            'new_customers' => 0,
            'pending_orders' => 0,
            'revenue_trend' => 0,
            'orders_trend' => 0
        ];

        try {
            if (Schema::hasTable('orders')) {
                $today = now()->startOfDay();
                $yesterday = now()->subDay()->startOfDay();

                // Today's revenue (in cents, convert to rupiah) - FIX: Cast to int
                $todayRevenue = (int) DB::table('orders')
                    ->where('created_at', '>=', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total_cents');
                $stats['today_revenue'] = $todayRevenue;  // Keep in cents for PriceHelper

                // Yesterday's revenue for trend - FIX: Cast to int
                $yesterdayRevenue = (int) DB::table('orders')
                    ->whereBetween('created_at', [$yesterday, $today])
                    ->where('payment_status', 'paid')
                    ->sum('total_cents');

                // Revenue trend calculation
                if ($yesterdayRevenue > 0) {
                    $revenueChange = $stats['today_revenue'] - $yesterdayRevenue;
                    $stats['revenue_trend'] = round(($revenueChange / $yesterdayRevenue) * 100, 1);
                }

                // Today's orders - FIX: Cast to int
                $stats['today_orders'] = (int) DB::table('orders')
                    ->where('created_at', '>=', $today)
                    ->count();

                // Yesterday's orders for trend - FIX: Cast to int
                $yesterdayOrders = (int) DB::table('orders')
                    ->whereBetween('created_at', [$yesterday, $today])
                    ->count();

                // Orders trend calculation
                if ($yesterdayOrders > 0) {
                    $ordersChange = $stats['today_orders'] - $yesterdayOrders;
                    $stats['orders_trend'] = round(($ordersChange / $yesterdayOrders) * 100, 1);
                }

                // Pending orders - FIX: Cast to int
                $stats['pending_orders'] = (int) DB::table('orders')
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();
            }

            if (Schema::hasTable('users')) {
                // New customers this month - FIX: Cast to int
                $stats['new_customers'] = (int) DB::table('users')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count();
            }

        } catch (\Exception $e) {
            Log::warning('Failed to get dashboard stats', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    /**
     * Get recent orders data
     */
    private function getRecentOrdersData($limit = 5)
    {
        try {
            if (Schema::hasTable('orders') && Schema::hasTable('users')) {
                $orders = DB::table('orders')
                    ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                    ->select(
                        'orders.id',
                        'orders.order_number',
                        'orders.total_cents',
                        'orders.status',
                        'orders.created_at',
                        'users.first_name',
                        'users.last_name'
                    )
                    ->orderBy('orders.created_at', 'desc')
                    ->limit($limit)
                    ->get();

                // Convert stdClass to array and add computed properties
                return $orders->map(function ($order) {
                    return [
                        'id' => (int) $order->id,
                        'order_number' => (string) $order->order_number,
                        'total_cents' => (int) $order->total_cents,
                        'status' => (string) $order->status,
                        'created_at' => $order->created_at,
                        'first_name' => (string) ($order->first_name ?? ''),
                        'last_name' => (string) ($order->last_name ?? ''),
                        'status_color' => $this->getStatusColor($order->status)
                    ];
                });
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get recent orders', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Get top products data
     */
    private function getTopProductsData($limit = 5)
    {
        try {
            if (Schema::hasTable('products')) {
                $products = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                    ->select(
                        'products.id',
                        'products.name',
                        'products.price_cents',
                        'products.stock_quantity',
                        'products.sale_count',
                        'products.revenue_cents',
                        'categories.name as category_name',
                        'brands.name as brand_name'
                    )
                    ->where('products.status', 'active')
                    ->orderBy('products.sale_count', 'desc')
                    ->limit($limit)
                    ->get();

                // Convert stdClass to array - FIX for stdClass error
                return $products->map(function ($product) {
                    return [
                        'id' => (int) $product->id,
                        'name' => (string) $product->name,
                        'price_cents' => (int) $product->price_cents,
                        'stock_quantity' => (int) $product->stock_quantity,
                        'sale_count' => (int) $product->sale_count,
                        'revenue_cents' => (int) $product->revenue_cents,
                        'category_name' => (string) ($product->category_name ?? 'No Category'),
                        'brand_name' => (string) ($product->brand_name ?? 'No Brand'),
                        'primary_image_url' => '/images/placeholder-product.jpg'
                    ];
                });
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get top products', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts()
    {
        $alerts = [
            'low_stock_count' => 0,
            'pending_reviews' => 0,
            'failed_payments' => 0
        ];

        try {
            if (Schema::hasTable('products')) {
                $alerts['low_stock_count'] = DB::table('products')
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('status', 'active')
                    ->count();
            }

            if (Schema::hasTable('product_reviews')) {
                $alerts['pending_reviews'] = DB::table('product_reviews')
                    ->where('is_approved', false)
                    ->count();
            }

            if (Schema::hasTable('payments')) {
                $alerts['failed_payments'] = DB::table('payments')
                    ->where('status', 'failed')
                    ->count();
            }

        } catch (\Exception $e) {
            Log::warning('Failed to get system alerts', ['error' => $e->getMessage()]);
        }

        return $alerts;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 5)
    {
        try {
            if (Schema::hasTable('activity_logs')) {
                $activities = DB::table('activity_logs')
                    ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                    ->select(
                        'activity_logs.*',
                        'users.first_name',
                        'users.last_name'
                    )
                    ->orderBy('activity_logs.created_at', 'desc')
                    ->limit($limit)
                    ->get();

                // Convert stdClass to array - FIX for stdClass error
                return $activities->map(function ($activity) {
                    return [
                        'id' => (int) $activity->id,
                        'action' => (string) $activity->action,
                        'description' => (string) ($activity->description ?? ''),
                        'created_at' => $activity->created_at,
                        'first_name' => (string) ($activity->first_name ?? 'System'),
                        'last_name' => (string) ($activity->last_name ?? ''),
                        'user_name' => trim(($activity->first_name ?? '') . ' ' . ($activity->last_name ?? '')) ?: 'System',
                        'icon' => $this->getActivityIcon($activity->action),
                        'color' => $this->getActivityColor($activity->action)
                    ];
                });
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get recent activities', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Get chart data
     */
    private function getChartData()
    {
        $chartData = [
            'revenue' => [
                'labels' => [],
                'values' => []
            ],
            'orderStatus' => [
                'labels' => [],
                'values' => []
            ]
        ];

        try {
            // Revenue chart data (last 7 days)
            if (Schema::hasTable('orders')) {
                $revenueData = DB::table('orders')
                    ->selectRaw('DATE(created_at) as date, SUM(total_cents) as revenue')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->where('payment_status', 'paid')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $chartData['revenue']['labels'] = $revenueData->pluck('date')->map(function ($date) {
                    return \Carbon\Carbon::parse($date)->format('M j');
                })->toArray();

                $chartData['revenue']['values'] = $revenueData->pluck('revenue')->map(function ($revenue) {
                    return $revenue / 100; // Convert cents to rupiah
                })->toArray();

                // Order status distribution
                $statusData = DB::table('orders')
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get();

                $chartData['orderStatus']['labels'] = $statusData->pluck('status')->map(function ($status) {
                    return ucfirst($status);
                })->toArray();

                $chartData['orderStatus']['values'] = $statusData->pluck('count')->toArray();
            }

        } catch (\Exception $e) {
            Log::warning('Failed to get chart data', ['error' => $e->getMessage()]);
        }

        return $chartData;
    }

    /**
     * Get default dashboard data (fallback)
     */
    private function getDefaultDashboardData()
    {
        return [
            'dashboardStats' => [
                'today_revenue' => 0,
                'today_orders' => 0,
                'new_customers' => 0,
                'pending_orders' => 0,
                'revenue_trend' => 0,
                'orders_trend' => 0
            ],
            'recentOrders' => collect(),
            'topProducts' => collect(),
            'systemAlerts' => [
                'low_stock_count' => 0,
                'pending_reviews' => 0,
                'failed_payments' => 0
            ],
            'recentActivities' => collect(),
            'chartData' => [
                'revenue' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'values' => [0, 0, 0, 0, 0, 0, 0]
                ],
                'orderStatus' => [
                    'labels' => ['Pending', 'Processing', 'Completed', 'Cancelled'],
                    'values' => [0, 0, 0, 0]
                ]
            ]
        ];
    }

    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary'
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Get activity icon
     */
    private function getActivityIcon($action)
    {
        $icons = [
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'create' => 'plus',
            'update' => 'edit',
            'delete' => 'trash',
            'order' => 'shopping-cart',
            'payment' => 'credit-card',
            'product' => 'box',
            'user' => 'user',
            'review' => 'star'
        ];

        return $icons[$action] ?? 'circle';
    }

    /**
     * Get activity color
     */
    private function getActivityColor($action)
    {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'order' => 'primary',
            'payment' => 'warning',
            'product' => 'info',
            'user' => 'primary',
            'review' => 'warning'
        ];

        return $colors[$action] ?? 'secondary';
    }

    // ========================================================================
    // API METHODS (untuk AJAX requests)
    // ========================================================================

    /**
     * API: Get dashboard stats
     */
    public function getStats()
    {
        return response()->json($this->getDashboardStats());
    }

    /**
     * API: Get sales chart data
     */
    public function getSalesChart(Request $request)
    {
        $period = $request->get('period', '7days');

        try {
            $days = match($period) {
                '30days' => 30,
                '90days' => 90,
                default => 7
            };

            if (Schema::hasTable('orders')) {
                $data = DB::table('orders')
                    ->selectRaw('DATE(created_at) as date, SUM(total_cents) as revenue')
                    ->where('created_at', '>=', now()->subDays($days))
                    ->where('payment_status', 'paid')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $chartData = [
                    'labels' => $data->pluck('date')->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('M j');
                    }),
                    'values' => $data->pluck('revenue')->map(function ($revenue) {
                        return $revenue / 100;
                    })
                ];

                return response()->json(['revenue' => $chartData]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to get sales chart data', ['error' => $e->getMessage()]);
        }

        return response()->json(['revenue' => ['labels' => [], 'values' => []]]);
    }

    /**
     * API: Get recent orders (for AJAX)
     */
    public function getRecentOrders()
    {
        return response()->json($this->getRecentOrdersData());
    }

    /**
     * API: Get top products (for AJAX)
     */
    public function getTopProducts()
    {
        return response()->json($this->getTopProductsData());
    }

    /**
     * API: Get low stock products
     */
    public function getLowStock()
    {
        try {
            if (Schema::hasTable('products')) {
                $products = DB::table('products')
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->where('status', 'active')
                    ->select('id', 'name', 'stock_quantity', 'min_stock_level')
                    ->orderBy('stock_quantity')
                    ->limit(10)
                    ->get();

                return response()->json($products);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get low stock products', ['error' => $e->getMessage()]);
        }

        return response()->json([]);
    }

    /**
     * API: Get live stats (for real-time updates)
     */
    public function getLiveStats()
    {
        return response()->json([
            'success' => true,
            'stats' => $this->getDashboardStats(),
            'recent' => [
                'orders' => $this->getRecentOrdersData(3)->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->first_name . ' ' . $order->last_name,
                        'total_cents' => $order->total_cents,
                        'status' => $order->status,
                        'status_color' => $order->status_color
                    ];
                }),
                'products' => $this->getTopProductsData(3)->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sales' => $product->sale_count,
                        'revenue' => $product->revenue_cents,
                        'stock' => $product->stock_quantity,
                        'image' => $product->primary_image_url
                    ];
                })
            ],
            'alerts' => $this->getSystemAlerts()
        ]);
    }

    /**
     * API: Get quick stats for widgets
     */
    public function getQuickStats()
    {
        $stats = $this->getDashboardStats();

        return response()->json([
            'today_revenue_formatted' => 'Rp ' . number_format($stats['today_revenue'], 0, ',', '.'),
            'today_orders' => $stats['today_orders'],
            'pending_orders' => $stats['pending_orders'],
            'new_customers' => $stats['new_customers']
        ]);
    }

    /**
     * Quick search in admin panel
     */
    public function quickSearch(Request $request)
    {
        $query = $request->get('q');
        $results = [];

        if (strlen($query) >= 2) {
            try {
                // Search products
                if (Schema::hasTable('products')) {
                    $products = DB::table('products')
                        ->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->limit(5)
                        ->get(['id', 'name', 'sku']);

                    foreach ($products as $product) {
                        $results[] = [
                            'type' => 'product',
                            'title' => $product->name,
                            'subtitle' => 'SKU: ' . $product->sku,
                            'url' => route('admin.products.edit', $product->id)
                        ];
                    }
                }

                // Search orders
                if (Schema::hasTable('orders')) {
                    $orders = DB::table('orders')
                        ->where('order_number', 'like', "%{$query}%")
                        ->limit(5)
                        ->get(['id', 'order_number', 'status']);

                    foreach ($orders as $order) {
                        $results[] = [
                            'type' => 'order',
                            'title' => '#' . $order->order_number,
                            'subtitle' => 'Status: ' . ucfirst($order->status),
                            'url' => route('admin.orders.show', $order->id)
                        ];
                    }
                }

                // Search users
                if (Schema::hasTable('users')) {
                    $users = DB::table('users')
                        ->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->limit(5)
                        ->get(['id', 'first_name', 'last_name', 'email']);

                    foreach ($users as $user) {
                        $results[] = [
                            'type' => 'user',
                            'title' => $user->first_name . ' ' . $user->last_name,
                            'subtitle' => $user->email,
                            'url' => route('admin.users.show', $user->id)
                        ];
                    }
                }

            } catch (\Exception $e) {
                Log::error('Quick search failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json($results);
    }
}
