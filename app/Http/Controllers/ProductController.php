<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
 /**
 * Display products listing (Public) - WORKING VERSION
 */
    public function index(Request $request)
    {
        try {
            // Query sederhana tanpa kompleksitas berlebihan
            $query = Product::where('status', 'active')
                ->select([
                    'id', 'name', 'slug', 'price_cents', 'compare_price_cents',
                    'stock_quantity', 'category_id', 'brand_id', 'created_at',
                    'rating_average', 'rating_count', 'featured'
                ]);

            // Apply basic filters
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }

            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->get('brand_id'));
            }

            // Sorting
            switch ($request->get('sort', 'newest')) {
                case 'price_low':
                    $query->orderBy('price_cents', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price_cents', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('rating_average', 'desc')->orderBy('sale_count', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            // Get products dengan relationship
            $products = $query->with([
                    'category:id,name,slug',
                    'brand:id,name,slug'
                ])
                ->paginate(12);

            // Get data untuk filter
            $categories = Category::where('is_active', true)
                                ->select('id', 'name', 'slug')
                                ->orderBy('name')
                                ->get();

            $brands = Brand::where('is_active', true)
                        ->select('id', 'name', 'slug')
                        ->orderBy('name')
                        ->get();

            return view('products.index', compact('products', 'categories', 'brands'));

        } catch (\Exception $e) {
            // Log error tapi tetap return view
            \Illuminate\Support\Facades\Log::error('Product index error: ' . $e->getMessage());

            // Return dengan data kosong
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1,
                ['path' => $request->url()]
            );
            $categories = collect();
            $brands = collect();

            return view('products.index', compact('products', 'categories', 'brands'));
        }
    }

    /**
     * Track product view for analytics
     */
    public function trackView(Request $request)
    {
        $productId = $request->input('product_id');

        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $product->incrementViewCount();
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle ketika tidak ada produk atau terjadi error
     */
    private function handleEmptyProducts($request)
    {
        // Buat dummy pagination object
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            collect([]), // empty collection
            0, // total
            12, // per page
            1, // current page
            ['path' => $request->url()]
        );

        $categories = collect();
        $brands = collect();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show single product (Public)
     */
    public function show(Product $product)
    {
        // Increment view count
        $product->increment('view_count');

        // Load relationships
        $product->load([
            'category',
            'brand',
            'images',
            'variants',
            'attributeValues.attribute',
            'reviews' => function($query) {
                $query->where('is_approved', true)->latest()->limit(10);
            },
            'reviews.user'
        ]);

        // Get related products
        $allProducts = new ProductCollection(Product::where('status', 'active')->get());
        $relatedProducts = $allProducts->getRecommendations($product, 6);

        // Recently viewed products
        $this->addToRecentlyViewed($product);

        // Calculate average rating
        $avgRating = $product->reviews->avg('rating') ?? 0;
        $totalReviews = $product->reviews->count();

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'avgRating',
            'totalReviews'
        ));
    }

/**
     * Search suggestions for autocomplete (AJAX)
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        try {
            $products = Product::where('status', 'active')
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name', 'slug', 'price_cents')
                ->limit(5)
                ->get();

            $suggestions = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => 'Rp ' . number_format($product->price_cents / 100, 0, ',', '.'),
                    'image' => asset('images/placeholder-product.jpg')
                ];
            });

            return response()->json(['suggestions' => $suggestions]);
        } catch (\Exception $e) {
            return response()->json(['suggestions' => []]);
        }
    }

    /**
     * Products by category
     */
    public function category(Category $category, Request $request)
    {
        $products = Product::where('status', 'active')
            ->where('category_id', $category->id)
            ->with(['brand', 'images'])
            ->get();

        $collection = new ProductCollection($products);

        // Apply additional filters
        $filtered = $collection->advancedFilter($request->all());

        // Pagination
        $paginated = $collection->paginateCollection(
            $request->get('per_page', 12),
            $request->get('page', 1)
        );

        return view('products.category', compact('category', 'paginated'));
    }

    /**
     * Products by brand
     */
    public function brand(Brand $brand, Request $request)
    {
        $products = Product::where('status', 'active')
            ->where('brand_id', $brand->id)
            ->with(['category', 'images'])
            ->get();

        $collection = new ProductCollection($products);

        // Apply filters
        $filtered = $collection->advancedFilter($request->all());

        // Pagination
        $paginated = $collection->paginateCollection(
            $request->get('per_page', 12),
            $request->get('page', 1)
        );

        return view('products.brand', compact('brand', 'paginated'));
    }

    /**
     * Display brands listing page
     */
    public function brandIndex(Request $request)
    {
        try {
            $query = Brand::where('is_active', true);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%");
            }

            // Category filter
            if ($request->filled('category')) {
                $query->whereHas('products', function($q) use ($request) {
                    $q->where('category_id', $request->get('category'))
                    ->where('status', 'active');
                });
            }

            // Min products filter
            if ($request->filled('min_products')) {
                $query->where('product_count', '>=', $request->get('min_products'));
            }

            // Sorting
            switch ($request->get('sort', 'name_asc')) {
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'products_asc':
                    $query->orderBy('product_count', 'asc');
                    break;
                case 'products_desc':
                    $query->orderBy('product_count', 'desc');
                    break;
                default: // name_asc
                    $query->orderBy('name', 'asc');
            }

            // Get brands with products count
            $brands = $query->withCount(['products' => function($q) {
                                $q->where('status', 'active');
                            }])
                            ->paginate(12);

            // Get categories for filter
            $categories = Category::where('is_active', true)
                                ->withCount(['products' => function($q) {
                                    $q->where('status', 'active');
                                }])
                                ->having('products_count', '>', 0)
                                ->orderBy('name')
                                ->get();

            // Calculate statistics
            $statistics = [
                'total_brands' => Brand::where('is_active', true)->count(),
                'total_products' => Product::where('status', 'active')->count(),
                'happy_customers' => User::whereHas('role', function($q) {
                    $q->where('name', 'customer');
                })->count(),
            ];

            // Get featured categories for bottom section
            $featuredCategories = Category::where('is_active', true)
                                        ->withCount(['products' => function($q) {
                                            $q->where('status', 'active');
                                        }])
                                        ->orderBy('products_count', 'desc')
                                        ->limit(6)
                                        ->get();

            // Sort options for dropdown
            $sortOptions = [
                'name_asc' => 'Nama A-Z',
                'name_desc' => 'Nama Z-A',
                'products_desc' => 'Produk Terbanyak',
                'products_asc' => 'Produk Tersedikit',
            ];

            return view('brands.index', compact(
                'brands',
                'categories',
                'statistics',
                'featuredCategories',
                'sortOptions'
            ));

        } catch (\Exception $e) {
            Log::error('Brand index error: ' . $e->getMessage());

            // Return empty data if error occurs
            $brands = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1,
                ['path' => $request->url()]
            );

            $categories = collect();
            $statistics = [
                'total_brands' => 0,
                'total_products' => 0,
                'happy_customers' => 0,
            ];
            $featuredCategories = collect();
            $sortOptions = [
                'name_asc' => 'Nama A-Z',
                'name_desc' => 'Nama Z-A',
                'products_desc' => 'Produk Terbanyak',
                'products_asc' => 'Produk Tersedikit',
            ];

            return view('brands.index', compact(
                'brands',
                'categories',
                'statistics',
                'featuredCategories',
                'sortOptions'
            ))->with('error', 'Terjadi kesalahan saat memuat data brand.');
        }
    }

    /**
     * Admin products listing
     */
    public function adminIndex(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Brand filter
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->whereRaw('stock_quantity <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $allProducts = new ProductCollection(Product::all());
        $stats = $allProducts->quickStats();

        // Get filter options
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.index', compact(
            'products',
            'stats',
            'categories',
            'brands'
        ));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = \App\Models\ProductAttribute::all();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'slug' => 'required|string|max:220|unique:products',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:50|unique:products',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price_cents' => 'required|integer|min:0',
            'compare_price_cents' => 'nullable|integer|min:0',
            'cost_price_cents' => 'nullable|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,active,inactive,discontinued',
            'featured' => 'boolean',
            'digital' => 'boolean',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['created_by'] = Auth::id();

            $product = Product::create($data);

            DB::commit();

            // Clear cache
            Cache::tags(['products'])->flush();

            return redirect()
                ->route('admin.products.show', $product)
                ->with('success', 'Product berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat product: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit product form
     */
    public function edit(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'variants', 'attributeValues.attribute']);

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = \App\Models\ProductAttribute::all();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'slug' => 'required|string|max:220|unique:products,slug,' . $product->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price_cents' => 'required|integer|min:0',
            'compare_price_cents' => 'nullable|integer|min:0',
            'cost_price_cents' => 'nullable|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight_grams' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,active,inactive,discontinued',
            'featured' => 'boolean',
            'digital' => 'boolean',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:320'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $product->update($request->all());

            DB::commit();

            // Clear cache
            Cache::tags(['products'])->flush();

            return redirect()
                ->route('admin.products.show', $product)
                ->with('success', 'Product berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal memperbarui product: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            // Clear cache
            Cache::tags(['products'])->flush();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus product: ' . $e->getMessage()]);
        }
    }

    /**
     * Update product stock (AJAX)
     */
    public function updateStock(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldStock = $product->stock_quantity;
            $product->update(['stock_quantity' => $request->stock_quantity]);

            // Log stock change
            activity('stock_update')
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->withProperties([
                    'old_stock' => $oldStock,
                    'new_stock' => $request->stock_quantity,
                    'reason' => $request->reason
                ])
                ->log('Stock updated');

            return response()->json([
                'success' => true,
                'message' => 'Stock berhasil diperbarui',
                'new_stock' => $product->fresh()->stock_quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle product status (AJAX)
     */
    public function toggleStatus(Product $product)
    {
        try {
            $newStatus = $product->status === 'active' ? 'inactive' : 'active';
            $product->update(['status' => $newStatus]);

            // Clear cache
            Cache::tags(['products'])->flush();

            // Log status change
            activity('status_change')
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->withProperties(['new_status' => $newStatus])
                ->log('Product status changed');

            return response()->json([
                'success' => true,
                'message' => "Product berhasil di{$newStatus}kan",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add to recently viewed (private method)
     */
    private function addToRecentlyViewed(Product $product)
    {
        $key = 'recently_viewed_' . (Auth::id() ?: session()->getId());
        $recentlyViewed = collect(session()->get($key, []));

        // Remove if already exists
        $recentlyViewed = $recentlyViewed->reject(function ($id) use ($product) {
            return $id == $product->id;
        });

        // Add to beginning
        $recentlyViewed->prepend($product->id);

        // Limit to 10 items
        $recentlyViewed = $recentlyViewed->take(10);

        session()->put($key, $recentlyViewed->toArray());
    }

    /**
     * Get featured products for homepage
     */
    public function featured()
    {
        $products = Product::where('status', 'active')
            ->where('featured', true)
            ->where('stock_quantity', '>', 0)
            ->with(['images', 'category'])
            ->orderBy('sale_count', 'desc')
            ->limit(8)
            ->get();

        return view('components.featured-products', compact('products'));
    }
}
