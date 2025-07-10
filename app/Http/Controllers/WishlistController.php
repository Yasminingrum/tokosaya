<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ShoppingCart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('user.status'); // Comment out jika middleware belum ada
    }

    /**
     * Display user's wishlist
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Get wishlist with product details
            $wishlistItems = Wishlist::where('user_id', $user->id)
                ->with([
                    'product' => function($query) {
                        $query->select('id', 'name', 'slug', 'price_cents', 'compare_price_cents', 'stock_quantity', 'status')
                              ->with([
                                  'images' => function($q) {
                                      $q->where('is_primary', true);
                                  },
                                  'category:id,name,slug'
                              ]);
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

        } catch (\Exception $e) {
            Log::error('Wishlist index error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return view('wishlist.index', [
                'wishlistItems' => collect(),
                'suggestedProducts' => collect(),
                'totalItems' => 0,
                'totalValue' => 0
            ])->with('error', 'Failed to load wishlist');
        }
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

        try {
            $user = Auth::user();
            $productId = $request->product_id;

            // Check if product is active
            $product = Product::findOrFail($productId);
            if ($product->status !== 'active') {
                throw new \Exception('Product is not available');
            }

            // Check if already exists
            $existingItem = Wishlist::where('user_id', $user->id)
                                  ->where('product_id', $productId)
                                  ->first();

            if ($existingItem) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Product already in wishlist',
                        'is_new' => false,
                        'wishlist_count' => $this->getWishlistCount($user->id)
                    ]);
                }

                return back()->with('info', 'Product is already in your wishlist');
            }

            // Add to wishlist
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'created_at' => now()
            ]);

            // Clear cache
            Cache::forget("user_wishlist_{$user->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to wishlist',
                    'is_new' => true,
                    'wishlist_count' => $this->getWishlistCount($user->id)
                ]);
            }

            return back()->with('success', 'Product added to wishlist successfully!');

        } catch (\Exception $e) {
            Log::error('Add to wishlist error', [
                'error' => $e->getMessage(),
                'product_id' => $request->product_id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add product to wishlist'
                ], 500);
            }

            return back()->with('error', 'Failed to add product to wishlist');
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
        try {
            $user = Auth::user();

            $deleted = Wishlist::where('user_id', $user->id)
                              ->where('product_id', $product->id)
                              ->delete();

            // Clear cache
            if ($deleted) {
                Cache::forget("user_wishlist_{$user->id}");
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
            Log::error('Remove from wishlist error', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove product from wishlist'
                ], 500);
            }

            return back()->with('error', 'Failed to remove product from wishlist');
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
        try {
            $user = Auth::user();
            $deletedCount = Wishlist::where('user_id', $user->id)->delete();

            // Clear cache
            Cache::forget("user_wishlist_{$user->id}");

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
            Log::error('Clear wishlist error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear wishlist'
                ], 500);
            }

            return back()->with('error', 'Failed to clear wishlist');
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
        try {
            $user = Auth::user();

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
                $inWishlist = false;
                $message = 'Product removed from wishlist';
            } else {
                // Add to wishlist
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'created_at' => now()
                ]);
                $inWishlist = true;
                $message = 'Product added to wishlist';
            }

            // Clear cache
            Cache::forget("user_wishlist_{$user->id}");

            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'message' => $message,
                'wishlist_count' => $this->getWishlistCount($user->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Toggle wishlist error', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'user_id' => Auth::id()
            ]);

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
        try {
            $user = Auth::user();
            $quantity = $request->get('quantity', 1);

            DB::beginTransaction();

            // 1. Check if product is in wishlist
            $wishlistItem = Wishlist::where('user_id', $user->id)
                                  ->where('product_id', $product->id)
                                  ->first();

            if (!$wishlistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in wishlist'
                ], 404);
            }

            // 2. Check product availability
            if ($product->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available'
                ], 400);
            }

            // 3. Check stock
            if ($product->stock_quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ], 400);
            }

            // 4. Add to cart
            $this->addToCartDirect($user, $product, $quantity);

            // 5. Remove from wishlist
            $wishlistItem->delete();

            // 6. Clear caches
            Cache::forget("user_wishlist_{$user->id}");

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product moved to cart successfully',
                    'wishlist_count' => $this->getWishlistCount($user->id)
                ]);
            }

            return back()->with('success', 'Product moved to cart successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Move to cart error', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to move product to cart'
                ], 500);
            }

            return back()->with('error', 'Failed to move product to cart');
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
        try {
            $user = Auth::user();

            $inWishlist = Wishlist::where('user_id', $user->id)
                                 ->where('product_id', $product->id)
                                 ->exists();

            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'wishlist_count' => $this->getWishlistCount($user->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking wishlist status'
            ], 500);
        }
    }

    /**
     * Get wishlist count for user (AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        try {
            $user = Auth::user();
            $count = $this->getWishlistCount($user->id);

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Share wishlist (public URL)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function share()
    {
        try {
            $user = Auth::user();

            // Generate share token
            $shareToken = hash('sha256', $user->id . $user->email . now()->timestamp);

            // Cache the user ID with token for 24 hours
            Cache::put("wishlist_share_{$shareToken}", $user->id, 86400);

            $shareUrl = route('wishlist.public', ['token' => $shareToken]);

            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'expires_in' => '24 hours'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate share link'
            ], 500);
        }
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
                          ->with([
                              'images' => function($q) {
                                  $q->where('is_primary', true);
                              },
                              'category:id,name,slug'
                          ]);
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

    // =============================================================================
    // HELPER METHODS
    // =============================================================================

    /**
     * Get wishlist count for user with caching
     *
     * @param int $userId
     * @return int
     */
    private function getWishlistCount($userId)
    {
        return Cache::remember("user_wishlist_{$userId}", 3600, function () use ($userId) {
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
        try {
            if ($categoryIds->isEmpty()) {
                // If no categories, get featured products
                return Product::where('status', 'active')
                             ->where('featured', true)
                             ->with([
                                 'images' => function($query) {
                                     $query->where('is_primary', true);
                                 },
                                 'category:id,name'
                             ])
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
                ->with([
                    'images' => function($query) {
                        $query->where('is_primary', true);
                    },
                    'category:id,name'
                ])
                ->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

        } catch (\Exception $e) {
            Log::error('Get suggested products error', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Add product directly to cart (fallback method)
     *
     * @param \App\Models\User $user
     * @param \App\Models\Product $product
     * @param int $quantity
     * @return void
     */
    private function addToCartDirect($user, $product, $quantity)
    {
        try {
            // Method 1: Try using CartController directly
            $cartController = app('App\Http\Controllers\CartController');
            $cartRequest = new Request([
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
            $cartRequest->headers->set('Accept', 'application/json');

            $response = $cartController->add($cartRequest);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Cart add failed');
            }

        } catch (\Exception $e) {
            // Method 2: Direct database insert as fallback
            $this->addToCartDirectDatabase($user, $product, $quantity);
        }
    }

    /**
     * Add to cart via direct database insert (fallback)
     *
     * @param \App\Models\User $user
     * @param \App\Models\Product $product
     * @param int $quantity
     * @return void
     */
    private function addToCartDirectDatabase($user, $product, $quantity)
    {
        // Try to find existing cart models
        $cartModel = null;
        $cartItemModel = null;

        // Check for different possible cart model names
        $possibleCartModels = [
            '\App\Models\ShoppingCart',
            '\App\Models\Cart',
            '\App\Models\UserCart'
        ];

        $possibleCartItemModels = [
            '\App\Models\CartItem',
            '\App\Models\ShoppingCartItem',
            '\App\Models\UserCartItem'
        ];

        foreach ($possibleCartModels as $model) {
            if (class_exists($model)) {
                $cartModel = $model;
                break;
            }
        }

        foreach ($possibleCartItemModels as $model) {
            if (class_exists($model)) {
                $cartItemModel = $model;
                break;
            }
        }

        if (!$cartModel || !$cartItemModel) {
            throw new \Exception('Cart models not found. Please implement cart functionality.');
        }

        // Get or create cart
        $cart = $cartModel::firstOrCreate([
            'user_id' => $user->id
        ], [
            'status' => 'active',
            'expires_at' => now()->addDays(7)
        ]);

        // Check if item already exists
        $existingItem = $cartItemModel::where('cart_id', $cart->id)
                                     ->where('product_id', $product->id)
                                     ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $quantity;
            $existingItem->update([
                'quantity' => $newQuantity,
                'total_price_cents' => $existingItem->unit_price_cents * $newQuantity
            ]);
        } else {
            // Create new item
            $cartItemModel::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_cents' => $product->price_cents,
                'total_price_cents' => $product->price_cents * $quantity
            ]);
        }

        // Update cart totals
        $this->updateCartTotals($cart);
    }

    /**
     * Update cart totals
     *
     * @param mixed $cart
     * @return void
     */
    private function updateCartTotals($cart)
    {
        try {
            $items = $cart->items ?? collect();
            $subtotal = $items->sum('total_price_cents');

            $cart->update([
                'subtotal_cents' => $subtotal,
                'total_cents' => $subtotal,
                'item_count' => $items->sum('quantity')
            ]);
        } catch (\Exception $e) {
            // Ignore cart total update errors
            Log::warning('Failed to update cart totals', ['error' => $e->getMessage()]);
        }
    }
}
