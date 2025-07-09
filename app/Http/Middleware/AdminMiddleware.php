<?php
// File: app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
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
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to access admin area');
        }

        $user = Auth::user();

        // Check if user has admin role - FIXED VERSION
        if (!$this->isAdminUser($user)) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient privileges'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'You do not have permission to access admin area');
        }

        // Check if account is active
        if (!$user->is_active) {
            Auth::logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account has been deactivated'
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact administrator.');
        }

        return $next($request);
    }

    /**
     * Check if user is admin using safe database query
     *
     * @param \App\Models\User $user
     * @return bool
     */
    private function isAdminUser($user)
    {
        try {
            // Direct database query - most reliable
            $role = DB::table('roles')->where('id', $user->role_id)->first();

            if ($role) {
                $adminRoles = ['admin', 'super_admin', 'superadmin'];
                return in_array($role->name, $adminRoles);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Admin role check failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
