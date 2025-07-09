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
     * Display homepage with optimized queries - FIXED VERSION
     */
    public function index()
    {

        // Get active banners - Simple query
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
            ->select(['id', 'title', 'subtitle', 'image', 'mobile_image', 'link_url', 'link_text'])
            ->get();

        // Get featured categories - TANPA Closure
        $featuredCategories = Category::where('is_active', true)
            ->where('level', 0) // Top level categories
            ->orderBy('sort_order')
            ->limit(8)
            ->select(['id', 'name', 'slug', 'image', 'icon'])
            ->get();

        // Load children separately jika diperlukan
        foreach($featuredCategories as $category) {
            $category->load(['children' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('sort_order')
                      ->select(['id', 'name', 'slug', 'image', 'parent_id']);
            }]);
        }

        // Get featured products - Simple query
        $featuredProducts = Product::where('status', 'active')
            ->where('featured', true)
            ->where('stock_quantity', '>', 0)
            ->with(['category:id,name,slug', 'brand:id,name,slug'])
            ->select(['id', 'name', 'slug', 'price_cents', 'compare_price_cents', 'stock_quantity',
                     'rating_average', 'rating_count', 'sale_count', 'category_id', 'brand_id'])
            ->limit(8)
            ->get();

        // Load images separately
        foreach($featuredProducts as $product) {
            $product->load(['images' => function($query) {
                $query->select(['id', 'product_id', 'image_url', 'alt_text', 'sort_order', 'is_primary'])
                      ->orderBy('is_primary', 'desc')
                      ->orderBy('sort_order')
                      ->limit(1);
            }]);
        }

        // Get categories with product count
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        // Stats toko - Simple count queries
        $stats = [
            'total_products' => Product::where('status', 'active')->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'in_stock' => Product::where('status', 'active')->where('stock_quantity', '>', 0)->count(),
        ];

        return view('home', compact('featuredProducts', 'categories', 'stats', 'banners', 'featuredCategories'));
    }

    public function indexWithCache()
    {

        // Cache stats (data simple)
        $stats = Cache::remember('homepage_stats', 3600, function () {
            return [
                'total_products' => Product::where('status', 'active')->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'in_stock' => Product::where('status', 'active')->where('stock_quantity', '>', 0)->count(),
            ];
        });

        // Cache banners (data simple)
        $banners = Cache::remember('homepage_banners', 3600, function () {
            return Banner::where('is_active', true)
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
                ->select(['id', 'title', 'subtitle', 'image', 'mobile_image', 'link_url', 'link_text'])
                ->get();
        });

        // Ambil data dengan relationship TANPA cache
        $featuredProducts = Product::where('status', 'active')
            ->where('featured', true)
            ->where('stock_quantity', '>', 0)
            ->with(['category:id,name,slug', 'brand:id,name,slug', 'images'])
            ->limit(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('status', 'active');
            }])
            ->limit(6)
            ->get();

        return view('home', compact('featuredProducts', 'categories', 'stats', 'banners'));
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

        return view('about', compact('page'));
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

        return view('contact', compact('page'));
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
        ], [
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
        return redirect()->route('products.index', ['search' => $query]);
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
        $faqs = collect([
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
        ]);

        return view('pages.faq', compact('faqs'));
    }

    /**
     * Handle 404 errors gracefully with better suggestions
     */
    public function notFound()
    {
        $suggestedProducts = Product::where('status', 'active')
            ->where('featured', true)
            ->with(['images'])
            ->select(['id', 'name', 'slug', 'price_cents'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return response()->view('errors.404', compact('suggestedProducts'), 404);
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        try {
            // Check database connection
            DB::connection()->getPdo();

            $status = [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'services' => [
                    'database' => 'up',
                ],
            ];

            return response()->json($status);

        } catch (\Exception $e) {
            $status = [
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage(),
            ];

            return response()->json($status, 503);
        }
    }

    /**
     * Handle newsletter subscription
     */
    public function newsletterSubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:180',
            'name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Check if email already exists
            $exists = DB::table('newsletter_subscribers')
                ->where('email', $request->email)
                ->exists();

            if ($exists) {
                return back()->with('warning', 'Email sudah terdaftar untuk newsletter.');
            }

            DB::table('newsletter_subscribers')->insert([
                'email' => $request->email,
                'name' => $request->name,
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribe_token' => str()->random(32),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Terima kasih! Anda telah berlangganan newsletter kami.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mendaftarkan newsletter. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Handle newsletter unsubscribe
     */
    public function newsletterUnsubscribe($token)
    {
        try {
            $subscriber = DB::table('newsletter_subscribers')
                ->where('unsubscribe_token', $token)
                ->first();

            if (!$subscriber) {
                return redirect()->route('home')->with('error', 'Token tidak valid.');
            }

            DB::table('newsletter_subscribers')
                ->where('unsubscribe_token', $token)
                ->update([
                    'is_active' => false,
                    'unsubscribed_at' => now(),
                    'updated_at' => now(),
                ]);

            return redirect()->route('home')->with('success', 'Anda telah berhasil berhenti berlangganan newsletter.');

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Gagal membatalkan langganan. Silakan coba lagi.');
        }
    }
}
