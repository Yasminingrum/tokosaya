<?php
// File: app/Http/Middleware/CheckUserStatus.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user account is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been deactivated'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact support.');
            }

            // Check if user is locked due to failed login attempts
            if ($user->locked_until && $user->locked_until > now()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $unlockTime = $user->locked_until->diffForHumans();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Your account is temporarily locked. Try again {$unlockTime}."
                    ], 423);
                }

                return redirect()->route('login')
                    ->with('error', "Your account is temporarily locked due to multiple failed login attempts. Try again {$unlockTime}.");
            }

            // Update last activity
            $user->update(['last_login_at' => now()]);
        }

        return $next($request);
    }
}
