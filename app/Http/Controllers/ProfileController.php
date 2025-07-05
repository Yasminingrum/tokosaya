<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Models\User;
use App\Models\Order;
use App\Models\CustomerAddress;
use App\Models\ProductReview;
use App\Models\Notification;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller
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

        // Recent orders
        $recentOrders = Order::where('user_id', $user->id)
                            ->with(['items.product:id,name,slug'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

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

        // Calculate customer tier
        $customerTier = $this->calculateCustomerTier($stats['total_spent'], $stats['completed_orders']);

        return view('profile.index', compact(
            'user', 'stats', 'recentOrders', 'recentReviews',
            'recentNotifications', 'customerTier'
        ));
    }

    /**
     * Show profile edit form
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     *
     * @param \App\Http\Requests\Profile\UpdateProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        try {
            DB::beginTransaction();

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

            // Update user
            $user->update($data);

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties(['updated_fields' => array_keys($data)])
                ->log('profile_updated');

            DB::commit();

            return redirect()->route('profile.index')
                           ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update profile']);
        }
    }

    /**
     * Change user password
     *
     * @param \App\Http\Requests\Profile\ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password_hash)) {
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
            $user->update([
                'password_hash' => Hash::make($data['new_password']),
                'remember_token' => null // Force re-login on other devices
            ]);

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('password_changed');

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
                     ->with(['items.product:id,name,slug', 'items.product.images' => function($q) {
                         $q->where('is_primary', true);
                     }]);

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

        return view('profile.addresses', compact('addresses'));
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

            // Log activity
            activity()
                ->performedOn($address)
                ->causedBy($user)
                ->withProperties(['label' => $address->label])
                ->log('address_created');

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
     * @param \App\Models\CustomerAddress $address
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateAddress(Request $request, CustomerAddress $address)
    {
        // Check ownership
        if ($address->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return back()->withErrors(['error' => 'Unauthorized']);
        }

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

            $address->update($data);

            // Log activity
            activity()
                ->performedOn($address)
                ->causedBy(Auth::user())
                ->withProperties(['label' => $address->label])
                ->log('address_updated');

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
     * @param \App\Models\CustomerAddress $address
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function deleteAddress(CustomerAddress $address, Request $request)
    {
        // Check ownership
        if ($address->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return back()->withErrors(['error' => 'Unauthorized']);
        }

        try {
            DB::beginTransaction();

            $wasDefault = $address->is_default;
            $userId = $address->user_id;
            $label = $address->label;

            $address->delete();

            // If deleted address was default, set another as default
            if ($wasDefault) {
                $newDefault = CustomerAddress::where('user_id', $userId)->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['deleted_label' => $label])
                ->log('address_deleted');

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
     * @param \App\Models\CustomerAddress $address
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function setDefaultAddress(CustomerAddress $address, Request $request)
    {
        // Check ownership
        if ($address->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return back()->withErrors(['error' => 'Unauthorized']);
        }

        try {
            DB::beginTransaction();

            // Unset current default
            CustomerAddress::where('user_id', Auth::id())
                          ->update(['is_default' => false]);

            // Set new default
            $address->update(['is_default' => true]);

            // Log activity
            activity()
                ->performedOn($address)
                ->causedBy(Auth::user())
                ->withProperties(['label' => $address->label])
                ->log('address_set_default');

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
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();

        $query = ProductReview::where('user_id', $user->id)
                             ->with(['product:id,name,slug', 'product.images' => function($q) {
                                 $q->where('is_primary', true);
                             }]);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by approval status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'pending':
                    $query->where('is_approved', false);
                    break;
                case 'verified':
                    $query->where('is_verified', true);
                    break;
            }
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get review statistics
        $reviewStats = [
            'total' => ProductReview::where('user_id', $user->id)->count(),
            'approved' => ProductReview::where('user_id', $user->id)->where('is_approved', true)->count(),
            'pending' => ProductReview::where('user_id', $user->id)->where('is_approved', false)->count(),
            'verified' => ProductReview::where('user_id', $user->id)->where('is_verified', true)->count(),
            'average_rating' => ProductReview::where('user_id', $user->id)->avg('rating') ?: 0
        ];

        return view('profile.reviews', compact('reviews', 'reviewStats'));
    }

    /**
     * Display user's notifications
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();

        $query = Notification::where('user_id', $user->id);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(15);

        // Mark notifications as read when viewed
        Notification::where('user_id', $user->id)
                   ->where('is_read', false)
                   ->update(['is_read' => true, 'read_at' => now()]);

        // Get notification statistics
        $notificationStats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'today' => Notification::where('user_id', $user->id)
                                  ->whereDate('created_at', today())
                                  ->count()
        ];

        return view('profile.notifications', compact('notifications', 'notificationStats'));
    }

    /**
     * Display user's wishlist
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function wishlist()
    {
        return redirect()->route('wishlist.index');
    }

    /**
     * Display user's download history (for digital products)
     *
     * @return \Illuminate\View\View
     */
    public function downloads()
    {
        $user = Auth::user();

        // Get digital products from completed orders
        $downloads = DB::table('order_items')
                      ->join('orders', 'order_items.order_id', '=', 'orders.id')
                      ->join('products', 'order_items.product_id', '=', 'products.id')
                      ->where('orders.user_id', $user->id)
                      ->where('orders.payment_status', 'paid')
                      ->where('products.digital', true)
                      ->select(
                          'order_items.*',
                          'orders.order_number',
                          'orders.created_at as order_date',
                          'products.name as product_name',
                          'products.slug as product_slug'
                      )
                      ->orderBy('orders.created_at', 'desc')
                      ->paginate(10);

        return view('profile.downloads', compact('downloads'));
    }

    /**
     * Display loyalty/rewards information
     *
     * @return \Illuminate\View\View
     */
    public function loyalty()
    {
        $user = Auth::user();

        // Calculate loyalty points and tier
        $totalSpent = Order::where('user_id', $user->id)
                          ->where('payment_status', 'paid')
                          ->sum('total_cents');

        $completedOrders = Order::where('user_id', $user->id)
                               ->where('status', 'delivered')
                               ->where('payment_status', 'paid')
                               ->count();

        $loyaltyPoints = $this->calculateLoyaltyPoints($totalSpent, $completedOrders);
        $customerTier = $this->calculateCustomerTier($totalSpent, $completedOrders);

        // Get tier benefits and next tier requirements
        $tierInfo = $this->getTierInformation($customerTier, $totalSpent, $completedOrders);

        // Recent point activities (mock data - implement based on your loyalty system)
        $pointActivities = [
            ['date' => now()->subDays(1), 'description' => 'Order #1234 completed', 'points' => 125],
            ['date' => now()->subDays(5), 'description' => 'Product review bonus', 'points' => 50],
            ['date' => now()->subDays(10), 'description' => 'Order #1230 completed', 'points' => 200],
        ];

        return view('profile.loyalty', compact(
            'loyaltyPoints', 'customerTier', 'tierInfo', 'pointActivities', 'totalSpent', 'completedOrders'
        ));
    }

    /**
     * Export user data (GDPR compliance)
     *
     * @return \Illuminate\Http\Response
     */
    public function exportData()
    {
        $user = Auth::user();

        // Collect user data
        $userData = [
            'personal_information' => $user->toArray(),
            'addresses' => CustomerAddress::where('user_id', $user->id)->get()->toArray(),
            'orders' => Order::where('user_id', $user->id)
                           ->with('items')
                           ->get()
                           ->toArray(),
            'reviews' => ProductReview::where('user_id', $user->id)->get()->toArray(),
            'wishlist' => Wishlist::where('user_id', $user->id)
                                 ->with('product:id,name')
                                 ->get()
                                 ->toArray(),
            'notifications' => Notification::where('user_id', $user->id)->get()->toArray()
        ];

        // Remove sensitive data
        unset($userData['personal_information']['password_hash']);
        unset($userData['personal_information']['remember_token']);

        // Log data export
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('data_exported');

        $filename = 'user_data_' . $user->id . '_' . date('Y-m-d') . '.json';

        return response()
            ->json($userData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ], JSON_PRETTY_PRINT);
    }

    /**
     * Delete user account (GDPR compliance)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is incorrect'
                ], 400);
            }

            return back()->withErrors(['password' => 'Password is incorrect']);
        }

        // Check for pending orders
        $pendingOrders = Order::where('user_id', $user->id)
                             ->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])
                             ->count();

        if ($pendingOrders > 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account with pending orders'
                ], 400);
            }

            return back()->withErrors(['error' => 'Cannot delete account with pending orders']);
        }

        try {
            DB::beginTransaction();

            // Log account deletion
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('account_deleted');

            // Anonymize user data instead of hard delete
            $user->update([
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

            // Keep orders and reviews for business records but anonymize
            ProductReview::where('user_id', $user->id)->update(['user_id' => null]);

            DB::commit();

            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account deleted successfully'
                ]);
            }

            return redirect()->route('home')
                           ->with('success', 'Account deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete account'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to delete account']);
        }
    }

    /**
     * Calculate customer tier based on spending and orders
     *
     * @param int $totalSpentCents
     * @param int $completedOrders
     * @return string
     */
    private function calculateCustomerTier($totalSpentCents, $completedOrders)
    {
        $totalSpent = $totalSpentCents / 100; // Convert to rupiah

        if ($totalSpent >= 10000000 || $completedOrders >= 50) { // 10 million or 50+ orders
            return 'VIP';
        } elseif ($totalSpent >= 5000000 || $completedOrders >= 20) { // 5 million or 20+ orders
            return 'Gold';
        } elseif ($totalSpent >= 1000000 || $completedOrders >= 5) { // 1 million or 5+ orders
            return 'Silver';
        } else {
            return 'Bronze';
        }
    }

    /**
     * Calculate loyalty points
     *
     * @param int $totalSpentCents
     * @param int $completedOrders
     * @return int
     */
    private function calculateLoyaltyPoints($totalSpentCents, $completedOrders)
    {
        // 1 point per 1000 rupiah spent + 50 points per completed order
        $spendingPoints = intval($totalSpentCents / 100000); // 1 point per 1000 rupiah
        $orderPoints = $completedOrders * 50;

        return $spendingPoints + $orderPoints;
    }

    /**
     * Get tier information and benefits
     *
     * @param string $currentTier
     * @param int $totalSpentCents
     * @param int $completedOrders
     * @return array
     */
    private function getTierInformation($currentTier, $totalSpentCents, $completedOrders)
    {
        $totalSpent = $totalSpentCents / 100;

        $tiers = [
            'Bronze' => [
                'benefits' => ['Free shipping on orders over Rp 100,000', 'Birthday discount'],
                'next_tier' => 'Silver',
                'spending_required' => 1000000,
                'orders_required' => 5
            ],
            'Silver' => [
                'benefits' => ['Free shipping on orders over Rp 50,000', 'Early access to sales', '5% birthday discount'],
                'next_tier' => 'Gold',
                'spending_required' => 5000000,
                'orders_required' => 20
            ],
            'Gold' => [
                'benefits' => ['Free shipping on all orders', 'Priority customer support', '10% birthday discount', 'Exclusive products access'],
                'next_tier' => 'VIP',
                'spending_required' => 10000000,
                'orders_required' => 50
            ],
            'VIP' => [
                'benefits' => ['All Gold benefits', 'Personal shopping assistant', '15% birthday discount', 'VIP events invitation'],
                'next_tier' => null,
                'spending_required' => null,
                'orders_required' => null
            ]
        ];

        $tierInfo = $tiers[$currentTier];

        if ($tierInfo['next_tier']) {
            $tierInfo['progress'] = [
                'spending_progress' => min(100, ($totalSpent / $tierInfo['spending_required']) * 100),
                'orders_progress' => min(100, ($completedOrders / $tierInfo['orders_required']) * 100),
                'spending_needed' => max(0, $tierInfo['spending_required'] - $totalSpent),
                'orders_needed' => max(0, $tierInfo['orders_required'] - $completedOrders)
            ];
        }

        return $tierInfo;
    }
}
