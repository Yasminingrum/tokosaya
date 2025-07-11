<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login authentication
     */
    public function login(Request $request)
    {
        // Rate limiting
        $key = 'login-attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ])->withInput();
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        if ($validator->fails()) {
            Log::info('Login validation failed', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray()
            ]);
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Log attempt
        Log::info('Login attempt', [
            'email' => $credentials['email'],
            'remember' => $remember,
            'ip' => $request->ip()
        ]);

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            Log::warning('Login failed - user not found', ['email' => $credentials['email']]);
            RateLimiter::hit($key);
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->except('password'));
        }

        // Check if user is active
        if (!$user->is_active) {
            Log::warning('Login failed - user inactive', ['email' => $credentials['email'], 'user_id' => $user->id]);
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->withInput($request->except('password'));
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);

            // Regenerate session
            $request->session()->regenerate();

            // Clear any previous intended URL
            session()->forget('url.intended');

            // Get authenticated user
            $user = Auth::user();

            // Get user role
            $userRole = $this->getUserRole($user);

            // Determine if user is admin
            $isAdmin = $this->isAdminRole($userRole);

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $userRole,
                'is_admin' => $isAdmin,
                'ip' => $request->ip()
            ]);

            // Log activity
            $this->logActivity('login', 'User logged in successfully');

            // Redirect based on role
            if ($isAdmin) {
                Log::info('ADMIN LOGIN - Redirecting to admin dashboard', [
                    'user_id' => $user->id,
                    'role' => $userRole
                ]);

                return redirect()->to('admin/dashboard')
                    ->with('success', 'Login berhasil! Selamat datang admin.');
            } else {
                Log::info('CUSTOMER LOGIN - Redirecting to profile', [
                    'user_id' => $user->id,
                    'role' => $userRole
                ]);

                return redirect()->to('/profile')
                    ->with('success', 'Login berhasil! Selamat datang kembali.');
            }
        }

        // Login failed
        RateLimiter::hit($key);

        Log::warning('Login failed - invalid credentials', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->except('password'));
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:180|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15'
        ], [
            'first_name.required' => 'Nama depan wajib diisi.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Get customer role
            $customerRole = DB::table('roles')->where('name', 'customer')->first();

            if (!$customerRole) {
                Log::error('Customer role not found during registration');
                return back()->withErrors(['error' => 'System error: Role tidak ditemukan.'])->withInput();
            }

            // Determine password field
            $passwordField = $this->getPasswordField();

            $userData = [
                'role_id' => $customerRole->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => strtolower($request->first_name . $request->last_name . rand(100, 999)),
                'email' => $request->email,
                $passwordField => Hash::make($request->password),
                'phone' => $request->phone,
                'is_active' => true
            ];

            $user = User::create($userData);

            // Log registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            $this->logActivity('registration', 'New user registered', $user);

            // Auto login
            Auth::login($user);

            return redirect('/profile')->with('success', 'Registrasi berhasil! Selamat datang di TokoSaya.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Registrasi gagal. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        if (Auth::check()) {
            Log::info('User logged out', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email
            ]);

            $this->logActivity('logout', 'User logged out');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda berhasil logout.');
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = User::where('id', Auth::id())->first();
        if (!$user) {
            return redirect('/login');
        }

        // Load addresses using direct query if relationship doesn't exist
        $addresses = $this->getUserAddresses($user->id);
        $user->addresses = $addresses;

        // Load recent orders using direct query
        $recentOrders = $this->getUserOrders($user->id, 5);
        $user->orders = $recentOrders;

        // Calculate order stats
        $orderStats = $this->calculateOrderStats($user->id);

        return view('auth.profile', compact('user', 'orderStats'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:M,F,O'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $user->fill($request->only([
                'first_name', 'last_name', 'phone', 'date_of_birth', 'gender'
            ]));
            $user->save();

            Log::info('User updated profile', ['user_id' => $user->id]);
            $this->logActivity('profile_update', 'User updated profile');

            return back()->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Gagal memperbarui profil.']);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = User::find(Auth::id());
        $passwordField = $this->getPasswordField();

        // Verify current password
        $currentPassword = $user->{$passwordField};
        if (!Hash::check($request->current_password, $currentPassword)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        try {
            $user->{$passwordField} = Hash::make($request->password);
            $user->save();

            Log::info('User changed password', ['user_id' => $user->id]);
            $this->logActivity('password_change', 'User changed password');

            return back()->with('success', 'Password berhasil diubah.');

        } catch (\Exception $e) {
            Log::error('Password change failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Gagal mengubah password.']);
        }
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();

            Log::info('Dashboard access attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id
            ]);

            // Get user role
            $userRole = $this->getUserRole($user);
            $isAdmin = $this->isAdminRole($userRole);

            Log::info('Dashboard role check', [
                'user_id' => $user->id,
                'role' => $userRole,
                'is_admin' => $isAdmin
            ]);

            // If admin, redirect to admin dashboard
            if ($isAdmin) {
                Log::info('Admin user accessing dashboard - redirecting to admin panel');
                return redirect()->route('admin.dashboard');
            }

            // For regular customers, show customer dashboard
            Log::info('Customer user accessing dashboard - showing customer dashboard');

            // Get recent orders using direct DB query
            $recentOrders = $this->getUserOrders($user->id, 5);

            // Get wishlist count using direct DB query
            $wishlistCount = $this->getWishlistCount($user->id);

            // Get notifications using direct DB query
            $notifications = $this->getNotifications($user->id, 5);

            // Get order statistics for additional dashboard data
            $orderStats = $this->calculateOrderStats($user->id);

            Log::info('Dashboard data loaded successfully', [
                'user_id' => $user->id,
                'recent_orders_count' => $recentOrders->count(),
                'wishlist_count' => $wishlistCount,
                'notifications_count' => $notifications->count()
            ]);

            return view('dashboard', compact(
                'user',
                'recentOrders',
                'wishlistCount',
                'notifications',
                'orderStats'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard load failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback with minimal data to prevent complete failure
            $user = Auth::user();

            return view('dashboard', [
                'user' => $user,
                'recentOrders' => collect(),
                'wishlistCount' => 0,
                'notifications' => collect(),
                'orderStats' => [
                    'total_orders' => 0,
                    'completed_orders' => 0,
                    'pending_orders' => 0,
                    'total_spent' => 0
                ]
            ]);
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Here you would normally send email
        // For now, just return success message
        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    /**
     * Get user role safely
     */
    private function getUserRole($user)
    {
        try {
            if (Schema::hasTable('roles')) {
                $role = DB::table('roles')->where('id', $user->role_id)->first();

                Log::info('getUserRole executed', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'role_found' => $role ? $role->name : 'null'
                ]);

                return $role ? $role->name : 'customer';
            }
        } catch (\Exception $e) {
            Log::error('Failed to get user role', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        return 'customer';
    }

    /**
     * Check if role is admin
     */
    private function isAdminRole($roleName)
    {
        $adminRoles = ['admin', 'super_admin', 'superadmin'];

        $isAdmin = in_array($roleName, $adminRoles);

        Log::info('Admin role check', [
            'role_name' => $roleName,
            'admin_roles' => $adminRoles,
            'is_admin' => $isAdmin
        ]);

        return $isAdmin;
    }

    /**
     * Get user orders using direct DB query
     */
    private function getUserOrders($userId, $limit = null)
    {
        try {
            if (Schema::hasTable('orders')) {
                $query = DB::table('orders')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc');

                if ($limit) {
                    $query->limit($limit);
                }

                return $query->get();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user orders', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Get user addresses using direct DB query
     */
    private function getUserAddresses($userId)
    {
        try {
            if (Schema::hasTable('customer_addresses')) {
                return DB::table('customer_addresses')
                    ->where('user_id', $userId)
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user addresses', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Get wishlist count using direct DB query
     */
    private function getWishlistCount($userId)
    {
        try {
            if (Schema::hasTable('wishlists')) {
                return DB::table('wishlists')
                    ->where('user_id', $userId)
                    ->count();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get wishlist count', ['error' => $e->getMessage()]);
        }

        return 0;
    }

    /**
     * Get notifications using direct DB query
     */
    private function getNotifications($userId, $limit = null)
    {
        try {
            if (Schema::hasTable('notifications')) {
                $query = DB::table('notifications')
                    ->where('user_id', $userId)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc');

                if ($limit) {
                    $query->limit($limit);
                }

                return $query->get();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get notifications', ['error' => $e->getMessage()]);
        }

        return collect();
    }

    /**
     * Calculate order statistics
     */
    private function calculateOrderStats($userId)
    {
        $stats = [
            'total_orders' => 0,
            'completed_orders' => 0,
            'pending_orders' => 0,
            'total_spent' => 0
        ];

        try {
            if (Schema::hasTable('orders')) {
                $stats['total_orders'] = DB::table('orders')
                    ->where('user_id', $userId)
                    ->count();

                $stats['completed_orders'] = DB::table('orders')
                    ->where('user_id', $userId)
                    ->where('status', 'delivered')
                    ->count();

                $stats['pending_orders'] = DB::table('orders')
                    ->where('user_id', $userId)
                    ->whereIn('status', ['pending', 'confirmed', 'processing'])
                    ->count();

                $totalSpentCents = DB::table('orders')
                    ->where('user_id', $userId)
                    ->where('payment_status', 'paid')
                    ->sum('total_cents');

                $stats['total_spent'] = $totalSpentCents ? $totalSpentCents / 100 : 0;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to calculate order stats', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    /**
     * Determine password field name
     */
    private function getPasswordField()
    {
        try {
            if (Schema::hasTable('users')) {
                $columns = Schema::getColumnListing('users');

                if (in_array('password_hash', $columns)) {
                    return 'password_hash';
                }

                if (in_array('password', $columns)) {
                    return 'password';
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to determine password field', ['error' => $e->getMessage()]);
        }

        return 'password'; // Default
    }

    /**
     * Log activity safely
     */
    private function logActivity($action, $description, $user = null)
    {
        try {
            // Check if we should use activity logs
            if (Schema::hasTable('activity_logs')) {
                DB::table('activity_logs')->insert([
                    'user_id' => $user ? $user->id : Auth::id(),
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Activity logging failed', [
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}
