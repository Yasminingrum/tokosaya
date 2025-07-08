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

        // Debug: Log attempt
        Log::info('Login attempt', [
            'email' => $credentials['email'],
            'remember' => $remember,
            'ip' => $request->ip()
        ]);

        // Check if user exists first
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

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip()
            ]);

            // Create activity log if function exists
            $this->logActivity('login', 'User logged in successfully');

            $request->session()->regenerate();

            // Redirect based on role - Safe role checking
            $user = Auth::user();
            $userRole = $this->getUserRole($user);

            if (in_array($userRole, ['admin', 'super_admin'])) {
                return redirect()->intended('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang admin.');
            }

            return redirect()->intended('/dashboard')->with('success', 'Login berhasil! Selamat datang kembali.');
        }

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
            // Get customer role using direct query
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

            return redirect('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang di TokoSaya.');

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
     * Show dashboard - SAFE VERSION WITHOUT MODEL DEPENDENCIES
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();

            // Get recent orders using direct DB query
            $recentOrders = $this->getUserOrders($user->id, 5);

            // Get wishlist count using direct DB query
            $wishlistCount = $this->getWishlistCount($user->id);

            // Get notifications using direct DB query
            $notifications = $this->getNotifications($user->id, 5);

            return view('dashboard', compact('user', 'recentOrders', 'wishlistCount', 'notifications'));

        } catch (\Exception $e) {
            Log::error('Dashboard load failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback with minimal data
            return view('dashboard', [
                'user' => Auth::user(),
                'recentOrders' => collect(),
                'wishlistCount' => 0,
                'notifications' => collect()
            ]);
        }
    }

    /**
     * Helper: Get user role safely
     */
    private function getUserRole($user)
    {
        try {
            if (Schema::hasTable('roles')) {
                $role = DB::table('roles')->where('id', $user->role_id)->first();
                return $role ? $role->name : 'customer';
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user role', ['error' => $e->getMessage()]);
        }

        return 'customer';
    }

    /**
     * Helper: Get user orders using direct DB query
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
     * Helper: Get user addresses using direct DB query
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
     * Helper: Get wishlist count using direct DB query
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
     * Helper: Get notifications using direct DB query
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
     * Helper: Calculate order statistics
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
     * Helper: Determine password field name
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
     * Helper: Log activity safely
     */
    private function logActivity($action, $description, $user = null)
    {
        try {
            if (function_exists('activity')) {
                $activityLog = activity($action);

                if ($user) {
                    $activityLog->causedBy($user);
                } else {
                    $activityLog->causedBy(Auth::user());
                }

                $activityLog->log($description);
            } else {
                // Alternative: Direct database insert if activity helper doesn't exist
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
            }
        } catch (\Exception $e) {
            Log::warning('Activity logging failed', [
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}
