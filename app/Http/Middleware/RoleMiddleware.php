<?php
// File: app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        // ROBUST ROLE CHECK - Multiple methods
        $hasRole = false;

        try {
            // Method 1: Try hasRole method if exists and user is User instance
            if ($user instanceof User && method_exists($user, 'hasRole')) {
                $hasRole = $user->hasRole($role);
            }

            // Method 2: Fallback - direct database check
            if (!$hasRole) {
                $userRole = DB::table('roles')->where('id', $user->role_id)->first();
                if ($userRole) {
                    if (is_array($role)) {
                        $hasRole = in_array($userRole->name, $role);
                    } else {
                        $hasRole = $userRole->name === $role;
                    }
                }
            }

            // Method 3: Emergency fallback for admin roles
            if (!$hasRole && in_array($role, ['admin', 'super_admin'])) {
                $hasRole = in_array($user->role_id, [1, 2]); // 1=superadmin, 2=admin
            }

        } catch (\Exception $e) {
            Log::error('RoleMiddleware role check failed', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            $hasRole = false;
        }

        // Check if user has the required role
        if (!$hasRole) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient privileges'
                ], 403);
            }

            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
