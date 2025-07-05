<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Page;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Display homepage
     */
    public function index()
    {
        $data = Cache::remember('homepage_data', 3600, function () {
            // Get active banners
            $banners = Banner::where('is_active', true)
                ->where('position', 'hero')
                ->where(function($query) {
                    $query->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                })
                ->orderBy('sort_order')
                ->get();

            // Get featured categories
            $featuredCategories = Category::where('is_active', true)
                ->where('level', 0) // Top level categories
                ->orderBy('sort_order')
                ->limit(8)
                ->get();

            // Get all active products for collection processing
            $allProducts = Product::where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->with(['images', 'category', 'brand'])
                ->get();

            $productCollection = new ProductCollection($allProducts);

            // Get homepage product sections using ProductCollection
            $homepageProducts = $productCollection->forHomepage();

            return [
                'banners' => $banners,
                'featured_categories' => $featuredCategories,
                'featured_products' => $homepageProducts['featured'],
                'new_arrivals' => $homepageProducts['new_arrivals'],
                'best_sellers' => $homepageProducts['best_sellers'],
                'on_sale' => $homepageProducts['on_sale']
            ];
        });

        return view('home.index', $data);
    }

    /**
     * About page
     */
    public function about()
    {
        $page = Page::where('slug', 'about')->first();

        if (!$page) {
            $page = (object) [
                'title' => 'Tentang TokoSaya',
                'content' => 'Halaman tentang kami sedang dalam pengembangan.',
                'meta_title' => 'Tentang TokoSaya',
                'meta_description' => 'Pelajari lebih lanjut tentang TokoSaya'
            ];
        }

        return view('pages.about', compact('page'));
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $page = Page::where('slug', 'contact')->first();

        return view('pages.contact', compact('page'));
    }

    /**
     * Handle contact form submission
     */
    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:180',
            'phone' => 'nullable|string|max:15',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Here you can save to database, send email, etc.
            // For now, we'll just log the activity

            activity('contact_form')
                ->withProperties([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('Contact form submitted');

            return back()->with('success', 'Pesan Anda telah dikirim. Kami akan merespon dalam 24 jam.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengirim pesan. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Global search
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q'));

        if (empty($query)) {
            return redirect()->route('home');
        }

        // Redirect to product search
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
            $brands = \App\Models\Brand::where('is_active', true)
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'slug']);

            return [
                'products' => $products->map(function($product) {
                    return [
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->name,
                        'url' => route('products.show', $product),
                        'price' => 'Rp ' . number_format($product->price_cents / 100, 0, ',', '.')
                    ];
                }),
                'categories' => $categories->map(function($category) {
                    return [
                        'type' => 'category',
                        'id' => $category->id,
                        'name' => $category->name,
                        'url' => route('products.category', $category)
                    ];
                }),
                'brands' => $brands->map(function($brand) {
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
     * Privacy policy page
     */
    public function privacy()
    {
        $page = Page::where('slug', 'privacy-policy')->first();

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
     * Terms of service page
     */
    public function terms()
    {
        $page = Page::where('slug', 'terms-of-service')->first();

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
     * FAQ page
     */
    public function faq()
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara melakukan pemesanan?',
                'answer' => 'Anda dapat melakukan pemesanan dengan memilih produk, menambahkan ke keranjang, dan mengikuti proses checkout.'
            ],
            [
                'question' => 'Apa saja metode pembayaran yang tersedia?',
                'answer' => 'Kami menerima pembayaran melalui transfer bank, kartu kredit, e-wallet, dan cash on delivery (COD).'
            ],
            [
                'question' => 'Berapa lama proses pengiriman?',
                'answer' => 'Proses pengiriman biasanya memakan waktu 1-3 hari kerja untuk area Jabodetabek dan 3-7 hari kerja untuk luar Jabodetabek.'
            ],
            [
                'question' => 'Apakah bisa melakukan pengembalian barang?',
                'answer' => 'Ya, kami menerima pengembalian barang dalam 7 hari setelah barang diterima dengan syarat dan ketentuan yang berlaku.'
            ],
            [
                'question' => 'Bagaimana cara melacak pesanan?',
                'answer' => 'Anda dapat melacak pesanan melalui halaman "Pesanan Saya" di akun Anda atau menggunakan nomor resi yang diberikan.'
            ]
        ];

        return view('pages.faq', compact('faqs'));
    }

    /**
     * Sitemap page
     */
    public function sitemap()
    {
        $data = Cache::remember('sitemap_data', 86400, function () {
            return [
                'categories' => Category::where('is_active', true)->get(),
                'brands' => \App\Models\Brand::where('is_active', true)->get(),
                'pages' => Page::where('status', 'published')->get()
            ];
        });

        return view('pages.sitemap', $data);
    }

    /**
     * Newsletter subscription
     */
    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:180|unique:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar atau tidak valid'
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            \App\Models\NewsletterSubscriber::create([
                'email' => $request->email,
                'is_active' => true,
                'subscribed_at' => now()
            ]);

            // Log activity
            activity('newsletter_subscription')
                ->withProperties([
                    'email' => $request->email,
                    'ip_address' => $request->ip()
                ])
                ->log('Newsletter subscription');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih! Anda telah berlangganan newsletter kami.'
                ]);
            }

            return back()->with('success', 'Terima kasih! Anda telah berlangganan newsletter kami.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendaftarkan email. Silakan coba lagi.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal mendaftarkan email. Silakan coba lagi.']);
        }
    }

    /**
     * Get trending products (AJAX)
     */
    public function trending()
    {
        $products = Cache::remember('trending_products', 1800, function () {
            $allProducts = Product::where('status', 'active')
                ->where('stock_quantity', '>', 0)
                ->with(['images', 'category'])
                ->get();

            $collection = new ProductCollection($allProducts);
            return $collection->trending(8);
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price_cents,
                    'formatted_price' => 'Rp ' . number_format($product->price_cents / 100, 0, ',', '.'),
                    'image' => $product->images->first()?->image_url,
                    'category' => $product->category?->name,
                    'url' => route('products.show', $product)
                ];
            })
        ]);
    }

    /**
     * Store visitor analytics
     */
    public function trackVisitor(Request $request)
    {
        try {
            // Simple visitor tracking
            $sessionKey = 'visitor_tracked_' . session()->getId();

            if (!session($sessionKey)) {
                activity('page_visit')
                    ->withProperties([
                        'url' => $request->url(),
                        'referrer' => $request->header('referer'),
                        'user_agent' => $request->userAgent(),
                        'ip_address' => $request->ip()
                    ])
                    ->log('Page visited');

                session([$sessionKey => true]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get quick stats for dashboard widgets
     */
    public function quickStats()
    {
        $stats = Cache::remember('homepage_quick_stats', 1800, function () {
            return [
    /**
     * Get quick stats for dashboard widgets
     */
    public function quickStats()
    {
        $stats = Cache::remember('homepage_quick_stats', 1800, function () {
            return [
                'total_products' => Product::where('status', 'active')->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'total_brands' => \App\Models\Brand::where('is_active', true)->count(),
                'featured_products' => Product::where('status', 'active')->where('featured', true)->count()
            ];
        });

        return response()->json($stats);
    }

    /**
     * Handle 404 errors gracefully
     */
    public function notFound()
    {
        // Get some suggested products
        $suggestedProducts = Cache::remember('suggested_products_404', 3600, function () {
            return Product::where('status', 'active')
                ->where('featured', true)
                ->with(['images', 'category'])
                ->inRandomOrder()
                ->limit(4)
                ->get();
        });

        return response()->view('errors.404', compact('suggestedProducts'), 404);
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        try {
            // Check database connection
            \DB::connection()->getPdo();

            // Check cache
            Cache::put('health_check', time(), 60);
            $cacheWorking = Cache::get('health_check') !== null;

            $status = [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'services' => [
                    'database' => 'up',
                    'cache' => $cacheWorking ? 'up' : 'down'
                ]
            ];

            return response()->json($status);

        } catch (\Exception $e) {
            $status = [
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ];

            return response()->json($status, 503);
        }
    }
}
