<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('admin')->only(['adminIndex', 'approve', 'reject', 'destroy']);
        $this->middleware('user.status')->except(['show']);
    }

    /**
     * Display reviews for a product (public)
     *
     * @param \App\Models\Product $product
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Product $product, Request $request)
    {
        $query = ProductReview::where('product_id', $product->id)
                             ->where('is_approved', true)
                             ->with(['user:id,first_name,last_name,avatar', 'orderItem'])
                             ->withCount('helpfulUsers');

        // Filter by rating if specified
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort options
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest_rating':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest_rating':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'most_helpful':
                $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate(10);

        // Get rating statistics
        $ratingStats = ProductReview::where('product_id', $product->id)
                                  ->where('is_approved', true)
                                  ->selectRaw('rating, COUNT(*) as count')
                                  ->groupBy('rating')
                                  ->orderBy('rating', 'desc')
                                  ->pluck('count', 'rating')
                                  ->toArray();

        // Fill missing ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingStats[$i])) {
                $ratingStats[$i] = 0;
            }
        }
        ksort($ratingStats);

        $totalReviews = array_sum($ratingStats);
        $averageRating = $totalReviews > 0 ? array_sum(array_map(function($rating, $count) {
            return $rating * $count;
        }, array_keys($ratingStats), $ratingStats)) / $totalReviews : 0;

        if ($request->expectsJson()) {
            return response()->json([
                'reviews' => $reviews,
                'rating_stats' => $ratingStats,
                'total_reviews' => $totalReviews,
                'average_rating' => round($averageRating, 2)
            ]);
        }

        return view('products.reviews', compact(
            'product', 'reviews', 'ratingStats', 'totalReviews', 'averageRating', 'sortBy'
        ));
    }

    /**
     * Store a new review
     *
     * @param \App\Http\Requests\Review\StoreReviewRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreReviewRequest $request, Product $product)
    {
        $user = Auth::user();
        $data = $request->validated();

        try {
            // Check if user has purchased this product
            $orderItem = null;
            if ($request->filled('order_item_id')) {
                $orderItem = OrderItem::where('id', $request->order_item_id)
                                    ->where('product_id', $product->id)
                                    ->whereHas('order', function($query) use ($user) {
                                        $query->where('user_id', $user->id)
                                              ->where('payment_status', 'paid')
                                              ->where('status', 'delivered');
                                    })
                                    ->first();
            }

            // Check if user already reviewed this product for this order item
            $existingReview = ProductReview::where('user_id', $user->id)
                                         ->where('product_id', $product->id)
                                         ->where('order_item_id', $orderItem?->id)
                                         ->first();

            if ($existingReview) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already reviewed this product'
                    ], 400);
                }

                return back()->withErrors(['error' => 'You have already reviewed this product']);
            }

            DB::beginTransaction();

            // Handle image uploads
            $imageUrls = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('reviews', $filename, 'public');
                    $imageUrls[] = Storage::url($path);
                }
            }

            // Create review
            $review = ProductReview::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'order_item_id' => $orderItem?->id,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'review' => $data['review'] ?? null,
                'images' => !empty($imageUrls) ? json_encode($imageUrls) : null,
                'is_verified' => $orderItem !== null,
                'is_approved' => $this->shouldAutoApprove($user, $orderItem)
            ]);

            // Update product rating if auto-approved
            if ($review->is_approved) {
                $this->updateProductRating($product);
            }

            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy($user)
                ->withProperties([
                    'review_id' => $review->id,
                    'rating' => $review->rating,
                    'is_verified' => $review->is_verified
                ])
                ->log('product_review_created');

            DB::commit();

            $message = $review->is_approved
                ? 'Review submitted successfully and is now live!'
                : 'Review submitted successfully and is pending approval.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'review' => $review->load('user:id,first_name,last_name,avatar'),
                    'is_approved' => $review->is_approved
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit review'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to submit review']);
        }
    }

    /**
     * Update an existing review
     *
     * @param \App\Http\Requests\Review\UpdateReviewRequest $request
     * @param \App\Models\ProductReview $review
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(UpdateReviewRequest $request, ProductReview $review)
    {
        $user = Auth::user();

        // Check ownership
        if ($review->user_id !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this review'
                ], 403);
            }

            return back()->withErrors(['error' => 'Unauthorized to update this review']);
        }

        // Check if review can be edited (within 30 days)
        if ($review->created_at->diffInDays() > 30) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review can only be edited within 30 days of creation'
                ], 400);
            }

            return back()->withErrors(['error' => 'Review can only be edited within 30 days of creation']);
        }

        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Handle new image uploads
            $existingImages = $review->images ? json_decode($review->images, true) : [];
            $newImageUrls = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('reviews', $filename, 'public');
                    $newImageUrls[] = Storage::url($path);
                }
            }

            // Combine existing and new images
            $allImages = array_merge($existingImages, $newImageUrls);

            // Handle image deletions
            if ($request->filled('delete_images')) {
                $imagesToDelete = $request->delete_images;
                foreach ($imagesToDelete as $imageUrl) {
                    if (in_array($imageUrl, $existingImages)) {
                        // Delete from storage
                        $path = str_replace('/storage/', '', $imageUrl);
                        Storage::disk('public')->delete($path);

                        // Remove from array
                        $allImages = array_filter($allImages, function($img) use ($imageUrl) {
                            return $img !== $imageUrl;
                        });
                    }
                }
            }

            $wasApproved = $review->is_approved;

            // Update review
            $review->update([
                'rating' => $data['rating'],
                'title' => $data['title'] ?? $review->title,
                'review' => $data['review'] ?? $review->review,
                'images' => !empty($allImages) ? json_encode(array_values($allImages)) : null,
                'is_approved' => $this->shouldAutoApprove($user, $review->orderItem, true)
            ]);

            // Update product rating if approval status changed
            if ($wasApproved !== $review->is_approved || $review->is_approved) {
                $this->updateProductRating($review->product);
            }

            // Log activity
            activity()
                ->performedOn($review->product)
                ->causedBy($user)
                ->withProperties([
                    'review_id' => $review->id,
                    'old_rating' => $review->getOriginal('rating'),
                    'new_rating' => $review->rating
                ])
                ->log('product_review_updated');

            DB::commit();

            $message = $review->is_approved
                ? 'Review updated successfully!'
                : 'Review updated and is pending approval.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'review' => $review->fresh()->load('user:id,first_name,last_name,avatar')
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update review'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update review']);
        }
    }

    /**
     * Mark review as helpful
     *
     * @param \App\Models\ProductReview $review
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function helpful(ProductReview $review, Request $request)
    {
        $user = Auth::user();

        // Check if user already marked this review as helpful
        $existingHelpful = DB::table('review_helpful')
                            ->where('review_id', $review->id)
                            ->where('user_id', $user->id)
                            ->exists();

        if ($existingHelpful) {
            return response()->json([
                'success' => false,
                'message' => 'You have already marked this review as helpful'
            ], 400);
        }

        // Can't mark own review as helpful
        if ($review->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot mark your own review as helpful'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Add helpful record
            DB::table('review_helpful')->insert([
                'review_id' => $review->id,
                'user_id' => $user->id,
                'created_at' => now()
            ]);

            // Update helpful count
            $review->increment('helpful_count');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for marking this review as helpful',
                'helpful_count' => $review->helpful_count
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark review as helpful'
            ], 500);
        }
    }

    /**
     * Remove helpful mark from review
     *
     * @param \App\Models\ProductReview $review
     * @return \Illuminate\Http\JsonResponse
     */
    public function unhelpful(ProductReview $review)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            $deleted = DB::table('review_helpful')
                        ->where('review_id', $review->id)
                        ->where('user_id', $user->id)
                        ->delete();

            if ($deleted) {
                $review->decrement('helpful_count');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Helpful mark removed',
                'helpful_count' => $review->helpful_count
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove helpful mark'
            ], 500);
        }
    }

    /**
     * Admin: Display all reviews for moderation
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function adminIndex(Request $request)
    {
        $query = ProductReview::with(['user:id,first_name,last_name,email', 'product:id,name,slug'])
                             ->withCount('helpfulUsers');

        // Filter by approval status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('is_approved', false);
                    break;
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'verified':
                    $query->where('is_verified', true);
                    break;
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by product name or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'LIKE', "%{$search}%")
                             ->orWhere('last_name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
                });
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest_rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest_rating':
                $query->orderBy('rating', 'asc');
                break;
            case 'most_helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
            'verified' => ProductReview::where('is_verified', true)->count()
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Admin: Show single review
     *
     * @param \App\Models\ProductReview $review
     * @return \Illuminate\View\View
     */
    public function adminShow(ProductReview $review)
    {
        $review->load([
            'user:id,first_name,last_name,email,created_at',
            'product:id,name,slug,price_cents',
            'orderItem.order:id,order_number,created_at,status',
            'approver:id,first_name,last_name'
        ]);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Admin: Approve review
     *
     * @param \App\Models\ProductReview $review
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function approve(ProductReview $review, Request $request)
    {
        if ($review->is_approved) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review is already approved'
                ], 400);
            }

            return back()->withErrors(['error' => 'Review is already approved']);
        }

        try {
            DB::beginTransaction();

            $review->update([
                'is_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Update product rating
            $this->updateProductRating($review->product);

            // Log activity
            activity()
                ->performedOn($review)
                ->causedBy(Auth::user())
                ->withProperties(['review_id' => $review->id])
                ->log('review_approved');

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review approved successfully'
                ]);
            }

            return back()->with('success', 'Review approved successfully');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve review'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to approve review']);
        }
    }

    /**
     * Admin: Reject/unapprove review
     *
     * @param \App\Models\ProductReview $review
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reject(ProductReview $review, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $wasApproved = $review->is_approved;

            $review->update([
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => $request->reason
            ]);

            // Update product rating if it was previously approved
            if ($wasApproved) {
                $this->updateProductRating($review->product);
            }

            // Log activity
            activity()
                ->performedOn($review)
                ->causedBy(Auth::user())
                ->withProperties([
                    'review_id' => $review->id,
                    'reason' => $request->reason
                ])
                ->log('review_rejected');

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review rejected successfully'
                ]);
            }

            return back()->with('success', 'Review rejected successfully');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject review'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to reject review']);
        }
    }

    /**
     * Admin: Delete review
     *
     * @param \App\Models\ProductReview $review
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(ProductReview $review, Request $request)
    {
        try {
            DB::beginTransaction();

            $wasApproved = $review->is_approved;
            $product = $review->product;

            // Delete review images
            if ($review->images) {
                $images = json_decode($review->images, true);
                foreach ($images as $imageUrl) {
                    $path = str_replace('/storage/', '', $imageUrl);
                    Storage::disk('public')->delete($path);
                }
            }

            // Delete helpful records
            DB::table('review_helpful')->where('review_id', $review->id)->delete();

            // Delete review
            $review->delete();

            // Update product rating if review was approved
            if ($wasApproved) {
                $this->updateProductRating($product);
            }

            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->withProperties(['deleted_review_id' => $review->id])
                ->log('review_deleted');

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review deleted successfully'
                ]);
            }

            return back()->with('success', 'Review deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete review'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete review']);
        }
    }

    /**
     * Bulk operations on reviews
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'reviews' => 'required|array|min:1',
            'reviews.*' => 'exists:product_reviews,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $action = $request->action;
        $reviewIds = $request->reviews;
        $reason = $request->reason;

        try {
            DB::beginTransaction();

            $reviews = ProductReview::whereIn('id', $reviewIds)->get();
            $affectedProducts = collect();
            $processedCount = 0;

            foreach ($reviews as $review) {
                $wasApproved = $review->is_approved;

                switch ($action) {
                    case 'approve':
                        if (!$review->is_approved) {
                            $review->update([
                                'is_approved' => true,
                                'approved_by' => Auth::id(),
                                'approved_at' => now()
                            ]);
                            $affectedProducts->push($review->product);
                            $processedCount++;
                        }
                        break;

                    case 'reject':
                        if ($review->is_approved) {
                            $review->update([
                                'is_approved' => false,
                                'approved_by' => null,
                                'approved_at' => null,
                                'rejection_reason' => $reason
                            ]);
                            $affectedProducts->push($review->product);
                            $processedCount++;
                        }
                        break;

                    case 'delete':
                        // Delete images
                        if ($review->images) {
                            $images = json_decode($review->images, true);
                            foreach ($images as $imageUrl) {
                                $path = str_replace('/storage/', '', $imageUrl);
                                Storage::disk('public')->delete($path);
                            }
                        }

                        // Delete helpful records
                        DB::table('review_helpful')->where('review_id', $review->id)->delete();

                        if ($wasApproved) {
                            $affectedProducts->push($review->product);
                        }

                        $review->delete();
                        $processedCount++;
                        break;
                }
            }

            // Update product ratings for affected products
            foreach ($affectedProducts->unique('id') as $product) {
                $this->updateProductRating($product);
            }

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'action' => $action,
                    'count' => $processedCount,
                    'review_ids' => $reviewIds
                ])
                ->log('reviews_bulk_action');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully {$action}d {$processedCount} reviews"
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed'
            ], 500);
        }
    }

    /**
     * Get user's reviews
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function userReviews(Request $request)
    {
        $user = Auth::user();

        $reviews = ProductReview::where('user_id', $user->id)
                               ->with(['product:id,name,slug', 'product.images' => function($query) {
                                   $query->where('is_primary', true);
                               }])
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);

        return view('profile.reviews', compact('reviews'));
    }

    /**
     * Check if review should be auto-approved
     *
     * @param \App\Models\User $user
     * @param \App\Models\OrderItem|null $orderItem
     * @param bool $isUpdate
     * @return bool
     */
    private function shouldAutoApprove($user, $orderItem = null, $isUpdate = false)
    {
        // Auto-approve if verified purchase
        if ($orderItem) {
            return true;
        }

        // Auto-approve for trusted users (users with previous approved reviews)
        $approvedReviewsCount = ProductReview::where('user_id', $user->id)
                                           ->where('is_approved', true)
                                           ->count();

        if ($approvedReviewsCount >= 3) {
            return true;
        }

        // For updates, maintain current approval status if user is trusted
        if ($isUpdate && $approvedReviewsCount >= 1) {
            return true;
        }

        return false;
    }

    /**
     * Update product rating based on approved reviews
     *
     * @param \App\Models\Product $product
     * @return void
     */
    private function updateProductRating($product)
    {
        $reviews = ProductReview::where('product_id', $product->id)
                               ->where('is_approved', true)
                               ->get();

        $averageRating = $reviews->avg('rating') ?: 0;
        $reviewCount = $reviews->count();

        $product->update([
            'rating_average' => round($averageRating, 2),
            'rating_count' => $reviewCount
        ]);

        // Clear product cache
        \Illuminate\Support\Facades\Cache::forget("product_{$product->id}");
    }
}
