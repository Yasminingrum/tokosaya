<?php

// File: app/Http/Middleware/CartMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartMiddleware
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Initialize cart for the current session/user
        $this->cartService->initializeCart();

        // Share cart data with all views
        view()->share('globalCart', $this->cartService->getCart());
        view()->share('globalCartCount', $this->cartService->getItemCount());
        view()->share('globalCartTotal', $this->cartService->getTotal());

        return $next($request);
    }
}

// File: app/Http/Middleware/CartNotEmptyMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartNotEmptyMiddleware
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cart = $this->cartService->getCart();
        $cartItems = $this->cartService->getItems();

        if (!$cart || $cartItems->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items before proceeding to checkout.');
        }

        return $next($request);
    }
}
