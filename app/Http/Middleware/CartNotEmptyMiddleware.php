<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CartNotEmptyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong.');
        }

        return $next($request);
    }
}
