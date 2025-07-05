<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display products listing (Public)
     */
    public function index(Request $request)
    {
        $cacheKey = 'products_listing_' . md5(serialize($request->all()));

        $data = Cache::remember($cacheKey, 1800, function () use ($request) {
            // Get all active products
            $products = Product::where('status', 'active')
                ->with(['category', 'brand', 'images'])
                ->get();

            $collection = new ProductCollection($products);

            // Apply filters using ProductCollection
            $filtered = $collection->advancedFilter($request->all());

            // Pagination
            $paginated = $collection->paginateCollection(
                $request->get('per_page', 12),
                $request->get('page', 1)
            );

            return [
                'products' => $paginated,
                'categories' => Category::where('is_active', true)->get(),
                'brands' => Brand::where('is_active', true)->get(),
                'price_range' => [
                    'min' => $collection->min('price_cents') / 100,
                    'max' => $collection->max('price_cents') / 100
                ],
                'filters' => $request->all(),
                'stats' => $collection->quickStats()
            ];
        });

        return view('products.index', $data);
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
     * Search products
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q'));

        if (empty($query)) {
            return redirect()->route('products.index');
        }

        $cacheKey = 'product_search_' . md5($query);

        $results = Cache::remember($cacheKey, 900, function () use ($query) {
            // Search in products
            $products = Product::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('short_description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%");
                })
                ->with(['category', 'brand', 'images'])
                ->get();

            $collection = new ProductCollection($products);
            return $collection->smartSearch($query);
        });

        return view('products.search', [
            'query' => $query,
            'results' => $results['results'],
            'total' => $results['total_found'],
            'suggestions' => $results['suggestions']
        ]);
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
            $data['created_by'] = auth()->id();

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
                ->causedBy(auth()->user())
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
                ->causedBy(auth()->user())
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
        $key = 'recently_viewed_' . (auth()->id() ?: session()->getId());
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
