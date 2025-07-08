<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Models\User;
use App\Models\Order;
use App\Models\CustomerAddress;
use App\Models\ProductReview;
use App\Models\Notification;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;

class ProfileController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        //$this->middleware('auth');
        // Comment out if user.status middleware doesn't exist
        // $this->middleware('user.status');
    }

    /**
     * Display user profile dashboard
     *
     * @return \Illuminate\View\View
     */
public function index()
    {
        $user = Auth::user();

        // Get user statistics
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'completed_orders' => Order::where('user_id', $user->id)
                                      ->where('status', 'delivered')
                                      ->where('payment_status', 'paid')
                                      ->count(),
            'total_spent' => Order::where('user_id', $user->id)
                                 ->where('payment_status', 'paid')
                                 ->sum('total_cents'),
            'reviews_count' => ProductReview::where('user_id', $user->id)->count(),
            'wishlist_count' => Wishlist::where('user_id', $user->id)->count(),
            'addresses_count' => CustomerAddress::where('user_id', $user->id)->count(),
            'unread_notifications' => Notification::where('user_id', $user->id)
                                                 ->where('is_read', false)
                                                 ->count()
        ];

        // ✅ CREATE ALL REQUIRED VARIABLES
        $recentOrdersCount = $stats['total_orders'];
        $wishlistCount = $stats['wishlist_count'];
        $unreadNotifications = $stats['unread_notifications'];

        // Calculate additional stats
        $averageRating = ProductReview::where('user_id', $user->id)->avg('rating') ?? 0;
        $averageRating = round($averageRating, 1);
        $totalReviews = $stats['reviews_count'];
        $loyaltyPoints = $this->calculateLoyaltyPoints($stats['total_spent'], $stats['completed_orders']);

        // Recent orders with status colors
        $recentOrders = Order::where('user_id', $user->id)
                            ->with(['items.product:id,name,slug'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function($order) {
                                $order->status_color = $this->getStatusColor($order->status);
                                return $order;
                            });

        // Recent reviews
        $recentReviews = ProductReview::where('user_id', $user->id)
                                     ->with(['product:id,name,slug'])
                                     ->orderBy('created_at', 'desc')
                                     ->limit(3)
                                     ->get();

        // Recent notifications
        $recentNotifications = Notification::where('user_id', $user->id)
                                          ->orderBy('created_at', 'desc')
                                          ->limit(5)
                                          ->get();

        // ✅ CREATE RECENT ACTIVITY
        $recentActivity = collect([]);

        // Add recent orders to activity
        foreach($recentOrders->take(3) as $order) {
            $recentActivity->push((object)[
                'type_color' => $this->getStatusColor($order->status),
                'icon' => 'shopping-bag',
                'title' => 'Order ' . ucfirst($order->status),
                'description' => "Order #{$order->order_number} - " . $this->formatCurrency($order->total_cents),
                'created_at' => $order->created_at,
                'action_url' => route('orders.show', $order),
                'action_text' => 'View Order'
            ]);
        }

        // Add recent reviews to activity
        foreach($recentReviews as $review) {
            $recentActivity->push((object)[
                'type_color' => 'warning',
                'icon' => 'star',
                'title' => 'Review Posted',
                'description' => "You rated {$review->product->name} {$review->rating} stars",
                'created_at' => $review->created_at,
                'action_url' => route('products.show', $review->product),
                'action_text' => 'View Product'
            ]);
        }

        // Add profile update activity
        if ($user->updated_at->diffInDays() <= 30) {
            $recentActivity->push((object)[
                'type_color' => 'info',
                'icon' => 'user-edit',
                'title' => 'Profile Updated',
                'description' => 'You updated your profile information',
                'created_at' => $user->updated_at,
                'action_url' => route('profile.edit'),
                'action_text' => 'Edit Profile'
            ]);
        }

        // Add welcome activity for new users
        if ($user->created_at->diffInDays() <= 7) {
            $recentActivity->push((object)[
                'type_color' => 'success',
                'icon' => 'user-plus',
                'title' => 'Welcome to TokoSaya!',
                'description' => 'Your account has been created successfully',
                'created_at' => $user->created_at,
                'action_url' => route('products.index'),
                'action_text' => 'Start Shopping'
            ]);
        }

        // Sort activity by date and limit
        $recentActivity = $recentActivity->sortByDesc('created_at')->take(5)->values();

        // ✅ CREATE RECOMMENDED PRODUCTS
        $recommendedProducts = collect([]);

        try {
            // Try to get recommendations based on user's order history
            $userCategories = Order::where('user_id', $user->id)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.payment_status', 'paid')
                ->pluck('products.category_id')
                ->unique()
                ->take(3);

            if ($userCategories->isNotEmpty()) {
                $recommendedProducts = Product::whereIn('category_id', $userCategories)
                    ->where('status', 'active')
                    ->where('stock_quantity', '>', 0)
                    ->with(['category:id,name', 'images'])
                    ->inRandomOrder()
                    ->limit(8)
                    ->get();
            } else {
                // Fallback: get featured products
                $recommendedProducts = Product::where('status', 'active')
                    ->where('stock_quantity', '>', 0)
                    ->where('featured', true)
                    ->with(['category:id,name', 'images'])
                    ->orderBy('sale_count', 'desc')
                    ->limit(8)
                    ->get();
            }

            // Add primary image to each product
            $recommendedProducts = $recommendedProducts->map(function($product) {
                $product->primary_image = $product->images->where('is_primary', true)->first()?->image_url
                    ?? $product->images->first()?->image_url
                    ?? asset('images/no-image.jpg');
                return $product;
            });

        } catch (\Exception $e) {
            // If anything fails, just use empty collection
            $recommendedProducts = collect([]);
        }

        // Calculate customer tier
        $customerTier = $this->calculateCustomerTier($stats['total_spent'], $stats['completed_orders']);
        $currentTier = $customerTier;

        return view('profile.index', compact(
            'user', 'stats', 'recentOrders', 'recentReviews',
            'recentNotifications', 'customerTier', 'recentOrdersCount',
            'wishlistCount', 'unreadNotifications', 'averageRating',
            'totalReviews', 'loyaltyPoints', 'currentTier', 'recentActivity',
            'recommendedProducts'
        ));
    }

    /**
     * Show profile edit form
     *
     * @return \Illuminate\View\View
     */


    /**
     * Get status color for order badges
     *
     * @param string $status
     * @return string
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary'
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Format currency for display
     *
     * @param int $cents
     * @return string
     */
    private function formatCurrency($cents)
    {
        return 'Rp ' . number_format($cents / 100, 0, ',', '.');
    }

/**
 * Upload avatar image
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('avatars', $filename, 'public');

            // Update user avatar
            User::where('id', $user->id)->update(['avatar' => $path]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_url' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update avatar: ' . $e->getMessage()
            ], 500);
        }
    }


    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Basic validation
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender']);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $avatar = $request->file('avatar');
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $path = $avatar->storeAs('avatars', $filename, 'public');
                $data['avatar'] = $path;
            }

            // Update user data
            User::where('id', $user->id)->update($data);

            DB::commit();

            return redirect()->route('profile.index')
                           ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Change user password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password_hash)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        try {
            // Update password
            User::where('id', $user->id)->update([
                'password_hash' => Hash::make($request->new_password),
                'remember_token' => null // Force re-login on other devices
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            }

            return back()->with('success', 'Password changed successfully!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to change password'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to change password']);
        }
    }

    /**
     * Display user's order history
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)
                     ->with(['items.product:id,name,slug']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'LIKE', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get order statistics
        $orderStats = [
            'total' => Order::where('user_id', $user->id)->count(),
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'shipped' => Order::where('user_id', $user->id)->where('status', 'shipped')->count(),
            'delivered' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count()
        ];

        return view('profile.orders', compact('orders', 'orderStats'));
    }

    /**
     * Display user's addresses
     *
     * @return \Illuminate\View\View
     */
    public function addresses()
    {
        $user = Auth::user();
        $addresses = CustomerAddress::where('user_id', $user->id)
                                   ->orderBy('is_default', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('profile.addresses.index', compact('addresses'));
    }

    /**
     * Store new address
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:30',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'address_line1' => 'required|string|max:200',
            'address_line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|size:2',
            'is_default' => 'boolean'
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['country'] = $data['country'] ?? 'ID';

            // If this is set as default, unset other defaults
            if ($request->boolean('is_default')) {
                CustomerAddress::where('user_id', $user->id)
                              ->update(['is_default' => false]);
            }

            $address = CustomerAddress::create($data);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address added successfully',
                    'address' => $address
                ]);
            }

            return back()->with('success', 'Address added successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add address'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to add address']);
        }
    }

    /**
     * Update existing address
     *
     * @param \Illuminate\Http\Request $request
     * @param int $addressId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateAddress(Request $request, $addressId)
    {
        $address = CustomerAddress::where('id', $addressId)
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();

        $request->validate([
            'label' => 'required|string|max:30',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'address_line1' => 'required|string|max:200',
            'address_line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:50',
            'state' => 'required|string|max:50',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|size:2',
            'is_default' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['country'] = $data['country'] ?? 'ID';

            // If this is set as default, unset other defaults
            if ($request->boolean('is_default')) {
                CustomerAddress::where('user_id', Auth::id())
                              ->where('id', '!=', $address->id)
                              ->update(['is_default' => false]);
            }

            CustomerAddress::where('id', $address->id)->update($data);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address updated successfully',
                    'address' => $address->fresh()
                ]);
            }

            return back()->with('success', 'Address updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update address'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update address']);
        }
    }

    /**
     * Delete address
     *
     * @param int $addressId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function deleteAddress($addressId, Request $request)
    {
        $address = CustomerAddress::where('id', $addressId)
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();

        try {
            DB::beginTransaction();

            $wasDefault = $address->is_default;
            $userId = $address->user_id;

            $address->delete();

            // If deleted address was default, set another as default
            if ($wasDefault) {
                $newDefault = CustomerAddress::where('user_id', $userId)->first();
                if ($newDefault) {
                    CustomerAddress::where('id', $newDefault->id)->update(['is_default' => true]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address deleted successfully'
                ]);
            }

            return back()->with('success', 'Address deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete address'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete address']);
        }
    }

    /**
     * Set address as default
     *
     * @param int $addressId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function setDefaultAddress($addressId, Request $request)
    {
        $address = CustomerAddress::where('id', $addressId)
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();

        try {
            DB::beginTransaction();

            // Unset current default
            CustomerAddress::where('user_id', Auth::id())
                          ->update(['is_default' => false]);

            // Set new default
            CustomerAddress::where('id', $address->id)->update(['is_default' => true]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Default address updated successfully'
                ]);
            }

            return back()->with('success', 'Default address updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update default address'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update default address']);
        }
    }

    /**
     * Display user's reviews
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();

        $query = ProductReview::where('user_id', $user->id)
                             ->with(['product:id,name,slug']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('profile.reviews', compact('reviews'));
    }

    /**
     * Display user's notifications
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();

        $query = Notification::where('user_id', $user->id);

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('profile.notifications', compact('notifications'));
    }

    /**
     * Display loyalty/rewards information
     */
    public function loyalty()
    {
        $user = Auth::user();

        // Calculate basic metrics
        $totalSpent = Order::where('user_id', $user->id)
                          ->where('payment_status', 'paid')
                          ->sum('total_cents');

        $completedOrders = Order::where('user_id', $user->id)
                               ->where('status', 'delivered')
                               ->where('payment_status', 'paid')
                               ->count();

        $loyaltyPoints = $this->calculateLoyaltyPoints($totalSpent, $completedOrders);
        $customerTier = $this->calculateCustomerTier($totalSpent, $completedOrders);

        return view('profile.loyalty', compact(
            'loyaltyPoints', 'customerTier', 'totalSpent', 'completedOrders'
        ));
    }

    /**
     * Display downloads
     */
    public function downloads()
    {
        $user = Auth::user();
        $downloads = collect(); // Empty collection for now

        return view('profile.downloads', compact('downloads'));
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData()
        {
            $user = Auth::user();

            try {
                // Collect user data - FIXED VERSION
                $userData = [
                    'personal_information' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'date_of_birth' => $user->date_of_birth,
                        'gender' => $user->gender,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ],
                    'addresses' => CustomerAddress::where('user_id', $user->id)->get()->map(function($address) {
                        return [
                            'id' => $address->id,
                            'label' => $address->label,
                            'recipient_name' => $address->recipient_name,
                            'phone' => $address->phone,
                            'address_line1' => $address->address_line1,
                            'address_line2' => $address->address_line2,
                            'city' => $address->city,
                            'state' => $address->state,
                            'postal_code' => $address->postal_code,
                            'country' => $address->country,
                            'is_default' => $address->is_default,
                            'created_at' => $address->created_at
                        ];
                    })->toArray(),
                    'orders' => Order::where('user_id', $user->id)->get()->map(function($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $order->status,
                            'payment_status' => $order->payment_status,
                            'total_cents' => $order->total_cents,
                            'created_at' => $order->created_at
                        ];
                    })->toArray(),
                    'reviews' => ProductReview::where('user_id', $user->id)->get()->map(function($review) {
                        return [
                            'id' => $review->id,
                            'product_id' => $review->product_id,
                            'rating' => $review->rating,
                            'title' => $review->title,
                            'review' => $review->review,
                            'created_at' => $review->created_at
                        ];
                    })->toArray(),
                ];

                $filename = 'user_data_' . $user->id . '_' . date('Y-m-d') . '.json';

                return response()
                    ->json($userData, 200, [
                        'Content-Type' => 'application/json',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                    ], JSON_PRETTY_PRINT);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to export data: ' . $e->getMessage()
                ], 500);
            }
        }
    /**
     * Delete user account (GDPR compliance)
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:DELETE'
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['password' => 'Password is incorrect']);
        }

        try {
            DB::beginTransaction();

            // Anonymize user data instead of hard delete
            User::where('id', $user->id)->update([
                'first_name' => 'Deleted',
                'last_name' => 'User',
                'email' => 'deleted_' . $user->id . '@example.com',
                'phone' => null,
                'avatar' => null,
                'date_of_birth' => null,
                'password_hash' => Hash::make(Str::random(60)),
                'is_active' => false,
                'email_verified_at' => null,
                'remember_token' => null
            ]);

            // Delete related personal data
            CustomerAddress::where('user_id', $user->id)->delete();
            Wishlist::where('user_id', $user->id)->delete();
            Notification::where('user_id', $user->id)->delete();

            DB::commit();

            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                           ->with('success', 'Account deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete account']);
        }
    }

    /**
     * Calculate customer tier based on spending and orders
     */
    private function calculateCustomerTier($totalSpentCents, $completedOrders)
    {
        $totalSpent = $totalSpentCents / 100; // Convert to rupiah

        if ($totalSpent >= 10000000 || $completedOrders >= 50) {
            return 'VIP';
        } elseif ($totalSpent >= 5000000 || $completedOrders >= 20) {
            return 'Gold';
        } elseif ($totalSpent >= 1000000 || $completedOrders >= 5) {
            return 'Silver';
        } else {
            return 'Bronze';
        }
    }

    /**
     * Calculate loyalty points
     */
    private function calculateLoyaltyPoints($totalSpentCents, $completedOrders)
    {
        $spendingPoints = intval($totalSpentCents / 100000);
        $orderPoints = $completedOrders * 50;

        return $spendingPoints + $orderPoints;
    }
}
