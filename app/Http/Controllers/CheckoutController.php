<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShoppingCart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        try {
            // Get cart
            $cart = ShoppingCart::where('user_id', Auth::id())->first();

            if (!$cart || $cart->item_count == 0) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty. Please add items before checkout.');
            }

            $cartItems = $cart->items()->with(['product.images', 'variant'])->get();
            $summary = $this->calculateOrderSummary($cartItems);

            return view('checkout.index', compact('cart', 'cartItems', 'summary'));

        } catch (\Exception $e) {
            Log::error('Checkout index error: ' . $e->getMessage());

            return redirect()->route('cart.index')
                ->with('error', 'Failed to load checkout page');
        }
    }

    /**
     * Process the order - SIMPLIFIED
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:50',
            'shipping_state' => 'required|string|max:50',
            'shipping_postal_code' => 'required|string|max:10',
            'payment_method' => 'required|string|in:bank_transfer,cash_on_delivery',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Get cart
            $cart = ShoppingCart::where('user_id', $user->id)->first();

            if (!$cart || $cart->item_count == 0) {
                throw new \Exception('Cart is empty');
            }

            $cartItems = $cart->items()->with(['product', 'variant'])->get();

            // Validate stock
            foreach ($cartItems as $item) {
                $availableStock = $item->variant ?
                    $item->variant->stock_quantity :
                    $item->product->stock_quantity;

                if ($item->quantity > $availableStock) {
                    throw new \Exception("Insufficient stock for {$item->product->name}");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum('total_price_cents');
            $shipping = 0; // Free shipping for now
            $total = $subtotal + $shipping;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal_cents' => $subtotal,
                'shipping_cents' => $shipping,
                'total_cents' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => 'ID',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price_cents' => $item->unit_price_cents,
                    'total_price_cents' => $item->total_price_cents,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku ?? ''
                ]);

                // Update stock
                if ($item->variant) {
                    $item->variant->decrement('stock_quantity', $item->quantity);
                } else {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Clear cart
            $cart->items()->delete();
            $cart->update(['item_count' => 0, 'total_cents' => 0]);

            DB::commit();

            return redirect()->route('checkout.success', ['order' => $order->order_number])
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order process error: ' . $e->getMessage());

            return back()->with('error', 'Failed to process order: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Show order success page
     */
    public function success(Request $request)
    {
        $orderNumber = $request->route('order');
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product'])
            ->first();

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        return view('checkout.success', compact('order'));
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    /**
     * Calculate order summary
     */
    private function calculateOrderSummary($cartItems)
    {
        $subtotal = $cartItems->sum('total_price_cents');
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'item_count' => $cartItems->sum('quantity'),
            'formatted' => [
                'subtotal' => 'Rp ' . number_format($subtotal / 100, 0, ',', '.'),
                'shipping' => 'Rp ' . number_format($shipping / 100, 0, ',', '.'),
                'total' => 'Rp ' . number_format($total / 100, 0, ',', '.')
            ]
        ];
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
