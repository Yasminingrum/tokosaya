<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WishlistController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user.status');
    }

    /**
     * Display user's wishlist
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get wishlist with product details
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with([
                'product' => function($query) {
                    $query->select('id', 'name', 'slug', 'price_cents', 'compare_price_cents', 'stock_quantity', 'status')
                          ->with(['images' => function($q) {
                              $q->where('is_primary', true)->first();
                          }, 'category:id,name,slug']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter out inactive products
        $activeWishlistItems = $wishlistItems->filter(function($item) {
            return $item->product && $item->product->status === 'active';
        });

        // Remove inactive products from wishlist
        $inactiveItems = $wishlistItems->filter(function($item) {
            return !$item->product || $item->product->status !== 'active';
        });

        if ($inactiveItems->count() > 0) {
            Wishlist::whereIn('id', $inactiveItems->pluck('id'))->delete();
        }

        // Get suggested products based on wishlist
        $suggestedProducts = $this->getSuggestedProducts($activeWishlistItems->pluck('product.category.id')->unique());

        return view('wishlist.index', [
            'wishlistItems' => $activeWishlistItems,
            'suggestedProducts' => $suggestedProducts,
            'totalItems' => $activeWishlistItems->count(),
            'totalValue' => $activeWishlistItems->sum('product.price_cents')
        ]);
    }

    /**
     * Add product to wishlist
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        try {
            // Check if product is active
            $product = Product::findOrFail($productId);

            if ($product->status !== 'active') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product is not available for wishlist'
                    ], 400);
                }

                return back()->withErrors(['error' => 'Product is not available for wishlist']);
            }

            // Add to wishlist (will ignore if already exists due to unique constraint)
            $wishlistItem = Wishlist::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);

            $isNewItem = $wishlistItem->wasRecentlyCreated;

            if ($isNewItem) {
                // Clear wishlist cache for user
                Cache::forget("user_wishlist_{$user->id}");

                // Log activity
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties(['product_id' => $productId])
                    ->log('product_added_to_wishlist');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isNewItem ? 'Product added to wishlist' : 'Product already in wishlist',
                    'is_new' => $isNewItem,
                    'wishlist_count' => $this->getWishlistCount($user->id)
                ]);
            }

            $message = $isNewItem ? 'Product added to wishlist successfully!' : 'Product is already in your wishlist';
            return back()->with('success', $message);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to wishlist'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to add product to wishlist']);
        }
    }

    /**
     * Remove product from wishlist
     *
     * @param \App\Models\Product $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function remove(Product $product, Request $request)
    {
        $user = Auth::user();

        try {
            $deleted = Wishlist::where('user_id', $user->id)
                              ->where('product_id', $product->id)
                              ->delete();

            if ($deleted) {
                // Clear wishlist cache for user
                Cache::forget("user_wishlist_{$user->id}");

                // Log activity
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties(['product_id' => $product->id])
                    ->log('product_removed_from_wishlist');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $deleted ? 'Product removed from wishlist' : 'Product not found in wishlist',
                    'wishlist_count' => $this->getWishlistCount($user->id)
                ]);
            }

            $message = $deleted ? 'Product removed from wishlist successfully!' : 'Product not found in wishlist';
            return back()->with('success', $message);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove product from wishlist'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to remove product from wishlist']);
        }
    }

    /**
     * Clear entire wishlist
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function clear(Request $request)
    {
        $user = Auth::user();

        try {
            $deletedCount = Wishlist::where('user_id', $user->id)->delete();

            if ($deletedCount > 0) {
                // Clear wishlist cache for user
                Cache::forget("user_wishlist_{$user->id}");

                // Log activity
                activity()
                    ->causedBy($user)
                    ->withProperties(['deleted_count' => $deletedCount])
                    ->log('wishlist_cleared');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Cleared {$deletedCount} items from wishlist",
                    'deleted_count' => $deletedCount
                ]);
            }

            return redirect()->route('wishlist.index')
                           ->with('success', "Cleared {$deletedCount} items from wishlist successfully!");

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear wishlist'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to clear wishlist']);
        }
    }

    /**
     * Toggle product in wishlist (AJAX)
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Product $product)
    {
        $user = Auth::user();

        try {
            // Check if product is active
            if ($product->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available'
                ], 400);
            }

            $wishlistItem = Wishlist::where('user_id', $user->id)
                                  ->where('product_id', $product->id)
                                  ->first();

            if ($wishlistItem) {
                // Remove from wishlist
                $wishlistItem->delete();
                $action = 'removed';
                $message = 'Product removed from wishlist';

                // Log activity
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties(['product_id' => $product->id])
                    ->log('product_removed_from_wishlist');
            } else {
                // Add to wishlist
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);
                $action = 'added';
                $message = 'Product added to wishlist';

                // Log activity
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties(['product_id' => $product->id])
                    ->log('product_added_to_wishlist');
            }

            // Clear wishlist cache for user
            Cache::forget("user_wishlist_{$user->id}");

            return response()->json([
                'success' => true,
                'action' => $action,
                'message' => $message,
                'in_wishlist' => $action === 'added',
                'wishlist_count' => $this->getWishlistCount($user->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist'
            ], 500);
        }
    }

    /**
     * Move product from wishlist to cart
     *
     * @param \App\Models\Product $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function moveToCart(Product $product, Request $request)
    {
        $request->validate([
            'quantity' => 'integer|min:1|max:10'
        ]);

        $user = Auth::user();
        $quantity = $request->get('quantity', 1);

        try {
            // Check if product is in wishlist
            $wishlistItem = Wishlist::where('user_id', $user->id)
                                  ->where('product_id', $product->id)
                                  ->first();

            if (!$wishlistItem) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found in wishlist'
                    ], 404);
                }

                return back()->withErrors(['error' => 'Product not found in wishlist']);
            }

            // Check product availability
            if ($product->status !== 'active') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product is not available'
                    ], 400);
                }

                return back()->withErrors(['error' => 'Product is not available']);
            }

            // Check stock
            if ($product->track_stock && $product->stock_quantity < $quantity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available'
                    ], 400);
                }

                return back()->withErrors(['error' => 'Insufficient stock available']);
            }

            DB::beginTransaction();

            try {
                // Add to cart using CartService
                $cartService = app(\App\Services\CartService::class);
                $cartResult = $cartService->addItem($product, $quantity);

                // Remove from wishlist
                $wishlistItem->delete();

                // Clear caches
                Cache::forget("user_wishlist_{$user->id}");

                // Log activity
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties([
                        'product_id' => $product->id,
                        'quantity' => $quantity
                    ])
                    ->log('product_moved_from_wishlist_to_cart');

                DB::commit();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Product moved to cart successfully',
                        'cart_count' => $cartResult['cart_count'],
                        'wishlist_count' => $this->getWishlistCount($user->id)
                    ]);
                }

                return back()->with('success', 'Product moved to cart successfully!');

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to move product to cart'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to move product to cart']);
        }
    }

    /**
     * Check if product is in user's wishlist (AJAX)
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Product $product)
    {
        $user = Auth::user();

        $inWishlist = Wishlist::where('user_id', $user->id)
                             ->where('product_id', $product->id)
                             ->exists();

        return response()->json([
            'in_wishlist' => $inWishlist,
            'wishlist_count' => $this->getWishlistCount($user->id)
        ]);
    }

    /**
     * Get wishlist count for user (AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $user = Auth::user();
        $count = $this->getWishlistCount($user->id);

        return response()->json(['count' => $count]);
    }

    /**
     * Get recently viewed products that are not in wishlist
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions()
    {
        $user = Auth::user();

        // Get user's category preferences from wishlist
        $preferredCategories = Wishlist::where('user_id', $user->id)
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->pluck('products.category_id')
            ->unique();

        // Get suggested products
        $suggestions = Product::whereIn('category_id', $preferredCategories)
            ->where('status', 'active')
            ->where('featured', true)
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('product_id')
                      ->from('wishlists')
                      ->where('user_id', $user->id);
            })
            ->with(['images' => function($query) {
                $query->where('is_primary', true);
            }, 'category:id,name'])
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return response()->json([
            'suggestions' => $suggestions->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price_cents' => $product->price_cents,
                    'formatted_price' => $this->formatPrice($product->price_cents),
                    'image' => $product->images->first()?->image_url,
                    'category' => $product->category?->name,
                    'url' => route('products.show', $product->slug)
                ];
            })
        ]);
    }

    /**
     * Get user's wishlist count (cached)
     *
     * @param int $userId
     * @return int
     */
    private function getWishlistCount($userId)
    {
        return Cache::remember("user_wishlist_{$userId}_count", 3600, function() use ($userId) {
            return Wishlist::where('user_id', $userId)->count();
        });
    }

    /**
     * Get suggested products based on wishlist categories
     *
     * @param \Illuminate\Support\Collection $categoryIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getSuggestedProducts($categoryIds)
    {
        if ($categoryIds->isEmpty()) {
            // If no categories, get featured products
            return Product::where('status', 'active')
                         ->where('featured', true)
                         ->with(['images' => function($query) {
                             $query->where('is_primary', true);
                         }, 'category:id,name'])
                         ->limit(4)
                         ->get();
        }

        $user = Auth::user();

        return Product::whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('product_id')
                      ->from('wishlists')
                      ->where('user_id', $user->id);
            })
            ->with(['images' => function($query) {
                $query->where('is_primary', true);
            }, 'category:id,name'])
            ->orderBy('featured', 'desc')
            ->orderBy('sale_count', 'desc')
            ->limit(4)
            ->get();
    }

    /**
     * Share wishlist (public URL)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function share()
    {
        $user = Auth::user();

        // Generate share token (you might want to store this in database)
        $shareToken = hash('sha256', $user->id . $user->email . now()->timestamp);

        // Cache the user ID with token for 24 hours
        Cache::put("wishlist_share_{$shareToken}", $user->id, 86400);

        $shareUrl = route('wishlist.public', ['token' => $shareToken]);

        return response()->json([
            'success' => true,
            'share_url' => $shareUrl,
            'expires_in' => '24 hours'
        ]);
    }

    /**
     * View public shared wishlist
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function public($token)
    {
        $userId = Cache::get("wishlist_share_{$token}");

        if (!$userId) {
            abort(404, 'Wishlist not found or link has expired');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            abort(404, 'User not found');
        }

        $wishlistItems = Wishlist::where('user_id', $userId)
            ->with([
                'product' => function($query) {
                    $query->where('status', 'active')
                          ->with(['images' => function($q) {
                              $q->where('is_primary', true);
                          }, 'category:id,name,slug']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($item) {
                return $item->product !== null;
            });

        return view('wishlist.public', [
            'wishlistItems' => $wishlistItems,
            'ownerName' => $user->first_name . ' ' . $user->last_name,
            'totalItems' => $wishlistItems->count(),
            'isPublic' => true
        ]);
    }

    /**
     * Bulk operations on wishlist
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,move_to_cart',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id'
        ]);

        $user = Auth::user();
        $action = $request->action;
        $productIds = $request->products;

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'delete':
                    $deletedCount = Wishlist::where('user_id', $user->id)
                                          ->whereIn('product_id', $productIds)
                                          ->delete();

                    $message = "Removed {$deletedCount} items from wishlist";
                    break;

                case 'move_to_cart':
                    $cartService = app(\App\Services\CartService::class);
                    $movedCount = 0;

                    foreach ($productIds as $productId) {
                        $product = Product::find($productId);
                        if ($product && $product->status === 'active') {
                            try {
                                $cartService->addItem($product, 1);

                                Wishlist::where('user_id', $user->id)
                                       ->where('product_id', $productId)
                                       ->delete();

                                $movedCount++;
                            } catch (\Exception $e) {
                                // Skip this product if adding to cart fails
                                continue;
                            }
                        }
                    }

                    $message = "Moved {$movedCount} items to cart";
                    break;
            }

            // Clear cache
            Cache::forget("user_wishlist_{$user->id}");
            Cache::forget("user_wishlist_{$user->id}_count");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'wishlist_count' => $this->getWishlistCount($user->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed'
            ], 500);
        }
    }
}
