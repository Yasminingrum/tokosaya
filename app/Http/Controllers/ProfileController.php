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
