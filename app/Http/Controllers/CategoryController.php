<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Collections\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['index', 'show']);
    }

    /**
     * Display categories listing (Public)
     */
    public function index()
    {
        $categories = Cache::remember('categories_tree', 3600, function () {
            return Category::active()
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->active()->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        return view('categories.index', compact('categories'));
    }

    /**
     * Show category with products
     */
    public function show(Category $category, Request $request)
    {
        // Load category with children for navigation
        $category->load(['children' => function($query) {
            $query->active()->orderBy('sort_order');
        }, 'parent']);

        // Get category products with filters
        $query = Product::active()
            ->where('category_id', $category->id)
            ->with(['brand', 'images', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // Apply filters
        if ($request->filled('brand')) {
            $query->whereHas('brand', function($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price_cents', '>=', $request->min_price * 100);
        }

        if ($request->filled('max_price')) {
            $query->where('price_cents', '<=', $request->max_price * 100);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price_cents', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price_cents', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating_average', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('sale_count', 'desc');
                    break;
                default:
                    $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        // Get category statistics
        $allCategoryProducts = Product::active()->where('category_id', $category->id)->get();
        $productCollection = new ProductCollection($allCategoryProducts);

        $stats = [
            'total_products' => $productCollection->count(),
            'price_range' => [
                'min' => $productCollection->isEmpty() ? 0 : $productCollection->min('price_cents') / 100,
                'max' => $productCollection->isEmpty() ? 0 : $productCollection->max('price_cents') / 100,
            ],
            'brands' => $productCollection->groupBy('brand.name')->keys()->filter(),
            'avg_rating' => $productCollection->avg('rating_average') ?: 0
        ];

        return view('categories.show', compact('category', 'products', 'stats'));
    }

    /**
     * Admin: Display categories listing
     */
    public function adminIndex(Request $request)
    {
        $query = Category::withTrashed()
            ->withCount('products')
            ->orderBy('parent_id')
            ->orderBy('sort_order');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at')->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->whereNull('deleted_at')->where('is_active', false);
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        $categories = $query->paginate(20);

        // Category statistics
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::active()->count(),
            'inactive_categories' => Category::where('is_active', false)->count(),
            'deleted_categories' => Category::onlyTrashed()->count(),
            'total_products' => Product::count()
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show create category form
     */
    public function create()
    {
        $parentCategories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store new category
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            $data['slug'] = $this->ensureUniqueSlug($data['slug']);

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadCategoryImage($request->file('image'));
            }

            // Set sort order
            if (!isset($data['sort_order'])) {
                $data['sort_order'] = $this->getNextSortOrder($data['parent_id'] ?? null);
            }

            $category = Category::create($data);

            // Update materialized path
            $this->updateCategoryPath($category);

            DB::commit();

            // Clear cache
            Cache::tags(['categories'])->flush();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit category form
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::active()
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Generate slug if changed
            if ($data['name'] !== $category->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug (excluding current category)
            if (isset($data['slug']) && $data['slug'] !== $category->slug) {
                $data['slug'] = $this->ensureUniqueSlug($data['slug'], $category->id);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $data['image'] = $this->uploadCategoryImage($request->file('image'));
            }

            $category->update($data);

            // Update materialized path if parent changed
            if (isset($data['parent_id']) && $data['parent_id'] !== $category->parent_id) {
                $this->updateCategoryPath($category);

                // Update all descendant paths
                $this->updateDescendantPaths($category);
            }

            DB::commit();

            // Clear cache
            Cache::tags(['categories'])->flush();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that contains products. Please move or delete products first.'
                ], 400);
            }

            // Check if category has children
            if ($category->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has subcategories. Please delete subcategories first.'
                ], 400);
            }

            // Delete category image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            // Clear cache
            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder categories (Admin AJAX)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                Category::where('id', $categoryData['id'])
                    ->update(['sort_order' => $categoryData['sort_order']]);
            }

            DB::commit();

            // Clear cache
            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Categories reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle category status (Admin AJAX)
     */
    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            // Clear cache
            Cache::tags(['categories'])->flush();

            return response()->json([
                'success' => true,
                'is_active' => $category->is_active,
                'message' => 'Category status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category tree for select dropdown (AJAX)
     */
    public function getTree()
    {
        $categories = Category::active()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'parent_id', 'level']);

        $tree = $this->buildCategoryTree($categories);

        return response()->json($tree);
    }

    /**
     * Ensure unique slug
     */
    private function ensureUniqueSlug($slug, $excludeId = null)
    {
        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Category::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Upload category image
     */
    private function uploadCategoryImage($file)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('categories', $filename, 'public');
    }

    /**
     * Get next sort order for category
     */
    private function getNextSortOrder($parentId = null)
    {
        return Category::where('parent_id', $parentId)->max('sort_order') + 1;
    }

    /**
     * Update category materialized path
     */
    private function updateCategoryPath(Category $category)
    {
        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);
            $category->update([
                'path' => ($parent->path ?? '/') . $parent->id . '/',
                'level' => $parent->level + 1
            ]);
        } else {
            $category->update([
                'path' => '/',
                'level' => 0
            ]);
        }
    }

    /**
     * Update descendant paths when parent changes
     */
    private function updateDescendantPaths(Category $category)
    {
        $descendants = Category::where('path', 'LIKE', '%/' . $category->id . '/%')->get();

        foreach ($descendants as $descendant) {
            $this->updateCategoryPath($descendant);
        }
    }

    /**
     * Build category tree for display
     */
    private function buildCategoryTree($categories, $parentId = null, $level = 0)
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->display_name = str_repeat('-- ', $level) . $category->name;
                $tree[] = $category;

                // Add children
                $children = $this->buildCategoryTree($categories, $category->id, $level + 1);
                $tree = array_merge($tree, $children);
            }
        }

        return $tree;
    }
}
