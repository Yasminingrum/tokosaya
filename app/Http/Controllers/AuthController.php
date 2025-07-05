<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

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
            ]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);

            // Log activity
            activity('login')
                ->causedBy(auth()->user())
                ->log('User logged in successfully');

            $request->session()->regenerate();

            // Redirect based on role
            $user = auth()->user();
            if ($user->role->name === 'admin' || $user->role->name === 'super_admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($key);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Get customer role
            $customerRole = \App\Models\Role::where('name', 'customer')->first();

            $user = User::create([
                'role_id' => $customerRole->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => strtolower($request->first_name . $request->last_name . rand(100, 999)),
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'phone' => $request->phone,
                'is_active' => true
            ]);

            // Log activity
            activity('registration')
                ->causedBy($user)
                ->log('New user registered');

            // Auto login
            Auth::login($user);

            return redirect('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang di TokoSaya.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registrasi gagal. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        if (auth()->check()) {
            activity('logout')
                ->causedBy(auth()->user())
                ->log('User logged out');
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
        $user = auth()->user()->load([
            'addresses',
            'orders' => function($query) {
                $query->latest()->limit(5);
            }
        ]);

        $orderStats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('status', 'delivered')->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total_cents') / 100
        ];

        return view('auth.profile', compact('user', 'orderStats'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

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
            $user->update($request->only([
                'first_name', 'last_name', 'phone', 'date_of_birth', 'gender'
            ]));

            // Log activity
            activity('profile_update')
                ->causedBy($user)
                ->log('User updated profile');

            return back()->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
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

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        try {
            $user->update([
                'password_hash' => Hash::make($request->password)
            ]);

            // Log activity
            activity('password_change')
                ->causedBy($user)
                ->log('User changed password');

            return back()->with('success', 'Password berhasil diubah.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah password.']);
        }
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Get recent orders
        $recentOrders = $user->orders()
            ->with(['items.product'])
            ->latest()
            ->limit(5)
            ->get();

        // Get wishlist count
        $wishlistCount = $user->wishlists()->count();

        // Get recent notifications
        $notifications = $user->notifications()
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('user', 'recentOrders', 'wishlistCount', 'notifications'));
    }
}
