<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Brand;
use App\Models\Coupon;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Display homepage with optimized queries
     */
    public function index()
    {
        $data = Cache::remember('homepage_data', 3600, function () {
            // Get active banners with optimized query
            $banners = Banner::active()
                ->where('position', 'hero')
                ->validDateRange()
                ->orderBy('sort_order')
                ->select(['id', 'title', 'subtitle', 'image', 'mobile_image', 'link_url', 'link_text'])
                ->get();

            // Get featured categories with hierarchy support
            $featuredCategories = Category::with(['children' => function($query) {
                    $query->where('is_active', true)
                          ->orderBy('sort_order')
                          ->select(['id', 'name', 'slug', 'image', 'parent_id']);
                }])
                ->where('is_active', true)
                ->where('level', 0) // Top level categories
                ->orderBy('sort_order')
                ->limit(8)
                ->select(['id', 'name', 'slug', 'image', 'icon'])
                ->get();

            // Get all active products for collection processing with optimized selects
            $allProducts = Product::where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->with(['images' => function($query) {
                    $query->select(['id', 'product_id', 'image_url', 'alt_text', 'sort_order', 'is_primary'])
                          ->orderBy('is_primary', 'desc')
                          ->orderBy('sort_order');
                }, 'category:id,name,slug', 'brand:id,name,slug'])
                ->select(['id', 'name', 'slug', 'price_cents', 'compare_price_cents', 'stock_quantity',
                         'rating_average', 'sale_count', 'category_id', 'brand_id'])
                ->get();

            $productCollection = new ProductCollection($allProducts);

            // Get homepage product sections using ProductCollection
            $homepageProducts = $productCollection->forHomepage();

            // Flash sale products with active coupons
            $flashSaleProducts = Product::where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->whereNotNull('compare_price_cents')
                ->whereColumn('compare_price_cents', '>', 'price_cents') // Produk dengan diskon
                ->with(['images'])
                ->orderBy('sale_count', 'desc')
                ->limit(8)
                ->get();

            // Latest products with optimized query
            $latestProducts = Product::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->with(['images' => function($query) {
                    $query->select(['id', 'product_id', 'image_url', 'alt_text'])
                          ->limit(1);
                }])
                ->select(['id', 'name', 'slug', 'price_cents', 'compare_price_cents'])
                ->limit(8)
                ->get();

            // Featured brands with product counts
            $brands = Brand::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', 'active');
                }])
                ->orderBy('product_count', 'desc')
                ->select(['id', 'name', 'slug', 'logo'])
                ->limit(10)
                ->get();

            // Active coupons for promotion display
            $featuredCoupons = Coupon::where('is_active', true)
                ->where('is_public', true)
                ->validDateRange()
                ->orderBy('value_cents', 'desc')
                ->select(['id', 'code', 'name', 'type', 'value_cents', 'minimum_order_cents', 'expires_at'])
                ->limit(3)
                ->get();

            return [
                'banners' => $banners,
                'featured_categories' => $featuredCategories,
                'featured_products' => $homepageProducts['featured'],
                'new_arrivals' => $homepageProducts['new_arrivals'],
                'best_sellers' => $homepageProducts['best_sellers'],
                'on_sale' => $homepageProducts['on_sale'],
                'flashSaleProducts' => $flashSaleProducts,
                'latestProducts' => $latestProducts,
                'brands' => $brands,
                'featuredCoupons' => $featuredCoupons,
            ];
        });

        return view('home.index', $data);
    }

    /**
     * About page with SEO optimization
     */
    public function about()
    {
        $page = Cache::remember('page_about', 86400, function() {
            return Page::where('slug', 'about')
                ->select(['title', 'content', 'meta_title', 'meta_description', 'featured_image'])
                ->first();
        });

        if (!$page) {
            $page = (object) [
                'title' => 'Tentang TokoSaya',
                'content' => 'Halaman tentang kami sedang dalam pengembangan.',
                'meta_title' => 'Tentang TokoSaya',
                'meta_description' => 'Pelajari lebih lanjut tentang TokoSaya',
                'featured_image' => null,
            ];
        }

        return view('pages.about', compact('page'));
    }

    /**
     * Contact page with optimized query
     */
    public function contact()
    {
        $page = Cache::remember('page_contact', 86400, function() {
            return Page::where('slug', 'contact')
                ->select(['title', 'content', 'meta_title', 'meta_description'])
                ->first();
        });

        if (!$page) {
            $page = (object) [
                'title' => 'Hubungi Kami',
                'content' => 'Silakan gunakan form di bawah ini untuk menghubungi kami.',
                'meta_title' => 'Hubungi Kami - TokoSaya',
                'meta_description' => 'Formulir kontak TokoSaya',
            ];
        }

        return view('pages.contact', compact('page'));
    }

    /**
     * Handle contact form submission with improved validation
     */
    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:180',
            'phone' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:1000',
            'g-recaptcha-response' => 'required|captcha'
        ], [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA verification.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function() use ($request) {
                // Save to database
                DB::table('contact_submissions')->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Log activity if activity package is available
                if (function_exists('activity')) {
                    activity('contact_form')
                        ->withProperties([
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'subject' => $request->subject,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ])
                        ->log('Contact form submitted');
                }
            });

            return back()->with('success', 'Pesan Anda telah dikirim. Kami akan merespon dalam 24 jam.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengirim pesan. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Global search with better redirection
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q'));

        if (empty($query) || strlen($query) < 2) {
            return redirect()->route('home')->with('warning', 'Masukkan minimal 2 karakter untuk pencarian.');
        }

        // Redirect to product search with filters
        return redirect()->route('products.search', ['q' => $query]);
    }

    /**
     * Search suggestions (AJAX)
     */
    public function searchSuggestions(Request $request)
    {
        $query = trim($request->get('q'));

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $cacheKey = 'search_suggestions_' . md5($query);

        $suggestions = Cache::remember($cacheKey, 600, function () use ($query) {
            // Product suggestions
            $products = Product::where('status', 'active')
                ->where('name', 'LIKE', "%{$query}%")
                ->orderBy('sale_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'slug', 'price_cents']);

            // Category suggestions
            $categories = Category::where('is_active', true)
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'slug']);

            // Brand suggestions
            $brands = Brand::where('is_active', true)
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'slug']);

            return [
                'products' => $products->map(function ($product) {
                    return [
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->name,
                        'url' => route('products.show', $product),
                        'price' => 'Rp ' . number_format($product->price_cents / 100, 0, ',', '.')
                    ];
                }),
                'categories' => $categories->map(function ($category) {
                    return [
                        'type' => 'category',
                        'id' => $category->id,
                        'name' => $category->name,
                        'url' => route('products.category', $category)
                    ];
                }),
                'brands' => $brands->map(function ($brand) {
                    return [
                        'type' => 'brand',
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'url' => route('products.brand', $brand)
                    ];
                })
            ];
        });

        // Flatten and limit total suggestions
        $allSuggestions = collect()
            ->merge($suggestions['products'])
            ->merge($suggestions['categories'])
            ->merge($suggestions['brands'])
            ->take(10);

        return response()->json(['suggestions' => $allSuggestions]);
    }

    /**
     * Privacy policy page with caching
     */
    public function privacy()
    {
        $page = Cache::remember('page_privacy', 86400, function() {
            return Page::where('slug', 'privacy-policy')
                ->select(['title', 'content', 'meta_title', 'meta_description'])
                ->first();
        });

        if (!$page) {
            $page = (object) [
                'title' => 'Kebijakan Privasi',
                'content' => 'Halaman kebijakan privasi sedang dalam pengembangan.',
                'meta_title' => 'Kebijakan Privasi - TokoSaya',
                'meta_description' => 'Kebijakan privasi TokoSaya'
            ];
        }

        return view('pages.privacy', compact('page'));
    }

    /**
     * Terms of service page with caching
     */
    public function terms()
    {
        $page = Cache::remember('page_terms', 86400, function() {
            return Page::where('slug', 'terms-of-service')
                ->select(['title', 'content', 'meta_title', 'meta_description'])
                ->first();
        });

        if (!$page) {
            $page = (object) [
                'title' => 'Syarat dan Ketentuan',
                'content' => 'Halaman syarat dan ketentuan sedang dalam pengembangan.',
                'meta_title' => 'Syarat dan Ketentuan - TokoSaya',
                'meta_description' => 'Syarat dan ketentuan penggunaan TokoSaya'
            ];
        }

        return view('pages.terms', compact('page'));
    }

    /**
     * FAQ page with categorized questions
     */
    public function faq()
    {
        $faqs = Cache::remember('faqs_list', 86400, function() {
            return DB::table('faqs')
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->select(['id', 'question', 'answer', 'category'])
                ->get()
                ->groupBy('category');
        });

        if ($faqs->isEmpty()) {
            $faqs = [
                'Umum' => [
                    (object) [
                        'question' => 'Bagaimana cara melakukan pemesanan?',
                        'answer' => 'Anda dapat melakukan pemesanan dengan memilih produk, menambahkan ke keranjang, dan mengikuti proses checkout.'
                    ],
                    (object) [
                        'question' => 'Apa saja metode pembayaran yang tersedia?',
                        'answer' => 'Kami menerima pembayaran melalui transfer bank, kartu kredit, e-wallet, dan cash on delivery (COD).'
                    ]
                ],
                'Pengiriman' => [
                    (object) [
                        'question' => 'Berapa lama proses pengiriman?',
                        'answer' => 'Proses pengiriman biasanya memakan waktu 1-3 hari kerja untuk area Jabodetabek dan 3-7 hari kerja untuk luar Jabodetabek.'
                    ]
                ]
            ];
        }

        return view('pages.faq', compact('faqs'));
    }

    /**
     * Sitemap page with optimized queries
     */
    public function sitemap()
    {
        $data = Cache::remember('sitemap_data', 86400, function () {
            return [
                'categories' => Category::where('is_active', true)
                    ->select(['id', 'name', 'slug', 'parent_id', 'updated_at'])
                    ->get(),
                'brands' => Brand::where('is_active', true)
                    ->select(['id', 'name', 'slug', 'updated_at'])
                    ->get(),
                'pages' => Page::where('status', 'published')
                    ->select(['id', 'title', 'slug', 'updated_at'])
                    ->get()
            ];
        });

        return view('pages.sitemap', $data);
    }

    /**
     * Get trending products (AJAX) with better algorithm
     */
    public function trending()
    {
        $products = Cache::remember('trending_products', 1800, function () {
            return Product::where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->with(['images' => function($query) {
                    $query->select(['id', 'product_id', 'image_url'])
                          ->limit(1);
                }])
                ->select(['id', 'name', 'slug', 'price_cents', 'view_count', 'sale_count'])
                ->orderByRaw('(view_count * 0.6) + (sale_count * 0.4) DESC')
                ->limit(8)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price_cents,
                    'formatted_price' => $this->formatPrice($product->price_cents),
                    'image' => $product->images->first()?->image_url,
                    'url' => route('products.show', $product)
                ];
            })
        ]);
    }

    /**
     * Store visitor analytics with bot detection
     */
    public function trackVisitor(Request $request)
    {
        // Skip tracking for bots and crawlers
        $userAgent = $request->userAgent();
        if (preg_match('/bot|crawl|slurp|spider|mediapartners/i', $userAgent)) {
            return response()->json(['success' => true]);
        }

        try {
            $sessionKey = 'visitor_tracked_' . session()->getId();

            if (!session($sessionKey)) {
                DB::table('visitor_tracking')->insert([
                    'url' => $request->url(),
                    'referrer' => $request->header('referer'),
                    'user_agent' => $userAgent,
                    'ip_address' => $request->ip(),
                    'device_type' => $this->getDeviceType($userAgent),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                session([$sessionKey => true]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get quick stats for dashboard widgets with better metrics
     */
    public function quickStats()
    {
        $stats = Cache::remember('homepage_quick_stats', 1800, function () {
            return [
                'total_products' => Product::where('status', 'active')->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'total_brands' => Brand::where('is_active', true)->count(),
                'featured_products' => Product::where('status', 'active')->where('featured', true)->count(),
                'active_coupons' => Coupon::active()->validDateRange()->count(),
                'today_orders' => DB::table('orders')
                    ->whereDate('created_at', today())
                    ->where('payment_status', 'paid')
                    ->count(),
                'today_revenue' => DB::table('orders')
                    ->whereDate('created_at', today())
                    ->where('payment_status', 'paid')
                    ->sum('total_cents')
            ];
        });

        return response()->json($stats);
    }

    /**
     * Handle 404 errors gracefully with better suggestions
     */
    public function notFound()
    {
        $suggestedProducts = Cache::remember('suggested_products_404', 3600, function () {
            return Product::where('status', 'active')
                ->where('featured', true)
                ->with(['images' => function($query) {
                    $query->select(['id', 'product_id', 'image_url'])
                          ->limit(1);
                }])
                ->select(['id', 'name', 'slug', 'price_cents'])
                ->inRandomOrder()
                ->limit(4)
                ->get();
        });

        return response()->view('errors.404', compact('suggestedProducts'), 404);
    }

    /**
     * Health check endpoint with more comprehensive checks
     */
    public function health()
    {
        try {
            // Check database connection
            DB::connection()->getPdo();

            // Check cache
            $cacheWorking = Cache::put('health_check', time(), 60)
                    && Cache::get('health_check') !== null;

            // Check queue connection if applicable
            $queueWorking = true;
            if (config('queue.default') !== 'sync') {
                try {
                    dispatch(function() {})->onQueue('healthcheck');
                    $queueWorking = true;
                } catch (\Exception $e) {
                    $queueWorking = false;
                }
            }

            // Check storage
            $storageWorking = false;
            try {
                $storageWorking = Storage::disk()->put('healthcheck.txt', 'test')
                               && Storage::disk()->get('healthcheck.txt') === 'test';
                Storage::disk()->delete('healthcheck.txt');
            } catch (\Exception $e) {
                $storageWorking = false;
            }

            $status = [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'services' => [
                    'database' => 'up',
                    'cache' => $cacheWorking ? 'up' : 'down',
                    'queue' => $queueWorking ? 'up' : 'down',
                    'storage' => $storageWorking ? 'up' : 'down'
                ],
                'metrics' => [
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                    'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg()[0] : null,
                ]
            ];

            return response()->json($status);

        } catch (\Exception $e) {
            $status = [
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ];

            return response()->json($status, 503);
        }
    }

    /**
     * Get device type from user agent
     */
    protected function getDeviceType($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|Opera Mini/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Format price helper function
     */
    protected function formatPrice($priceCents)
    {
        return 'Rp ' . number_format($priceCents / 100, 0, ',', '.');
    }
}
