<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $shippingService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        ShippingService $shippingService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->shippingService = $shippingService;

        $this->middleware('auth');
        $this->middleware('cart.not_empty')->except(['success', 'failed']);
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        // Get cart data
        $cart = $this->cartService->getCart();
        $cartItems = $this->cartService->getItems();
        $cartSummary = $this->cartService->getSummary();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        // Get user data
        $user = Auth()->user::load('customerAddresses');
        $defaultAddress = $user->customerAddresses->where('is_default', true)->first();

        // Get shipping methods
        $shippingMethods = ShippingMethod::where('is_active', true) // Changed active() to where condition
            ->orderBy('sort_order')
            ->get();

        // Get payment methods
        $paymentMethods = PaymentMethod::where('is_active', true) // Changed active() to where condition
            ->orderBy('sort_order')
            ->get();

        return view('checkout.index', compact(
            'cart',
            'cartItems',
            'cartSummary',
            'user',
            'defaultAddress',
            'shippingMethods',
            'paymentMethods'
        ));
    }

    /**
     * Handle shipping step
     */
    public function shipping(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:customer_addresses,id',
            'shipping_name' => 'required_without:address_id|string|max:100',
            'shipping_phone' => 'required_without:address_id|string|max:15',
            'shipping_address' => 'required_without:address_id|string',
            'shipping_city' => 'required_without:address_id|string|max:50',
            'shipping_state' => 'required_without:address_id|string|max:50',
            'shipping_postal_code' => 'required_without:address_id|string|max:10',
            'shipping_country' => 'required_without:address_id|string|size:2',
        ]);

        // Store shipping info in session
        $shippingData = $this->prepareShippingData($request);
        session(['checkout.shipping' => $shippingData]);

        // Calculate shipping rates
        $rates = $this->shippingService->calculateRates($shippingData);
        session(['checkout.shipping_rates' => $rates]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'shipping_rates' => $rates,
                'message' => 'Shipping information saved successfully'
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('checkout.payment')
        ]);
    }

    /**
     * Handle payment step
     */
    public function payment(Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'billing_same_as_shipping' => 'boolean',
            'billing_name' => 'required_if:billing_same_as_shipping,false|string|max:100',
            'billing_phone' => 'required_if:billing_same_as_shipping,false|string|max:15',
            'billing_address' => 'required_if:billing_same_as_shipping,false|string',
            'billing_city' => 'required_if:billing_same_as_shipping,false|string|max:50',
            'billing_state' => 'required_if:billing_same_as_shipping,false|string|max:50',
            'billing_postal_code' => 'required_if:billing_same_as_shipping,false|string|max:10',
            'billing_country' => 'required_if:billing_same_as_shipping,false|string|size:2',
        ]);

        // Store payment info in session
        $paymentData = $this->preparePaymentData($request);
        session(['checkout.payment' => $paymentData]);

        // Calculate final totals
        $summary = $this->calculateFinalSummary();
        session(['checkout.summary' => $summary]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'summary' => $summary,
                'message' => 'Payment information saved successfully'
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('checkout.review')
        ]);
    }

    /**
     * Show order review page
     */
    public function review()
    {
        // Validate session data
        if (!session()->has(['checkout.shipping', 'checkout.payment', 'checkout.summary'])) {
            return redirect()->route('checkout.index')
                ->with('error', 'Please complete all checkout steps.');
        }

        $cart = $this->cartService->getCart();
        $cartItems = $this->cartService->getItems();
        $shipping = session('checkout.shipping');
        $payment = session('checkout.payment');
        $summary = session('checkout.summary');

        // Load related data
        $shippingMethod = ShippingMethod::find($payment['shipping_method_id']);
        $paymentMethod = PaymentMethod::find($payment['payment_method_id']);

        return view('checkout.review', compact(
            'cart',
            'cartItems',
            'shipping',
            'payment',
            'summary',
            'shippingMethod',
            'paymentMethod'
        ));
    }

    /**
     * Process the order
     */
    public function process(CreateOrderRequest $request)
    {
        // Validate session data
        if (!session()->has(['checkout.shipping', 'checkout.payment', 'checkout.summary'])) {
            return redirect()->route('checkout.index')
                ->with('error', 'Please complete all checkout steps.');
        }

        DB::beginTransaction();

        try {
            // Create order
            $orderData = $this->prepareOrderData();
            $order = $this->orderService->create($orderData);

            // Clear cart
            $this->cartService->clearCart();

            // Clear checkout session
            session()->forget(['checkout.shipping', 'checkout.payment', 'checkout.summary']);

            DB::commit();

            // Log order creation
            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => Auth::id(),
                'total' => $order->total_cents
            ]);

            return redirect()->route('checkout.success', ['order' => $order->order_number])
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout.review')
                ->with('error', 'Failed to process your order. Please try again.');
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
            ->with(['items.product', 'payments'])
            ->first();

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Show order failed page
     */
    public function failed(Request $request)
    {
        $orderNumber = $request->get('order');
        $order = null;

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('checkout.failed', compact('order'));
    }

    /**
     * Calculate shipping rates (AJAX)
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_country' => 'required|string|size:2'
        ]);

        try {
            $shippingData = [
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'country' => $request->shipping_country
            ];

            $rates = $this->shippingService->calculateRates($shippingData);

            return response()->json([
                'success' => true,
                'rates' => $rates
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping rates: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Validate coupon code (AJAX)
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:30'
        ]);

        try {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', true) // Changed active() to where condition
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code'
                ], 400);
            }

            // Validate coupon
            $validation = $this->orderService->validateCoupon($coupon, $this->cartService->getCart());

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value_cents,
                    'discount_amount' => $validation['discount_amount']
                ],
                'message' => 'Coupon applied successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate coupon: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply coupon code (AJAX)
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:30'
        ]);

        try {
            $result = $this()->orderService::applyCoupon($request->coupon_code, $this->cartService->getCart());

            if ($result['success']) {
                session(['checkout.coupon' => $result['coupon']]);

                return response()->json([
                    'success' => true,
                    'coupon' => $result['coupon'],
                    'discount_amount' => $result['discount_amount'],
                    'new_total' => $result['new_total'],
                    'message' => 'Coupon applied successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply coupon: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove applied coupon (AJAX)
     */
    public function removeCoupon()
    {
        session()->forget('checkout.coupon');

        $summary = $this->calculateFinalSummary();

        return response()->json([
            'success' => true,
            'new_summary' => $summary,
            'message' => 'Coupon removed successfully'
        ]);
    }

    /**
     * Prepare shipping data from request
     */
    private function prepareShippingData(Request $request)
    {
        if ($request->filled('address_id')) {
            $address = Auth()->user::customerAddresses()->find($request->address_id);

            return [
                'address_id' => $address->id,
                'shipping_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address' => $address->address_line1 . ($address->address_line2 ? ', ' . $address->address_line2 : ''),
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_postal_code' => $address->postal_code,
                'shipping_country' => $address->country,
            ];
        }

        return [
            'shipping_name' => $request->shipping_name,
            'shipping_phone' => $request->shipping_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_country' => $request->shipping_country,
        ];
    }

    /**
     * Prepare payment data from request
     */
    private function preparePaymentData(Request $request)
    {
        $data = [
            'shipping_method_id' => $request->shipping_method_id,
            'payment_method_id' => $request->payment_method_id,
            'billing_same_as_shipping' => $request->boolean('billing_same_as_shipping', true),
        ];

        if (!$data['billing_same_as_shipping']) {
            $data = array_merge($data, [
                'billing_name' => $request->billing_name,
                'billing_phone' => $request->billing_phone,
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_postal_code' => $request->billing_postal_code,
                'billing_country' => $request->billing_country,
            ]);
        }

        return $data;
    }

    /**
     * Calculate final order summary
     */
    private function calculateFinalSummary()
    {
        $cartSummary = $this->cartService->getSummary();
        $payment = session('checkout.payment');
        $coupon = session('checkout.coupon');

        $subtotal = $cartSummary['subtotal_cents'];
        $shipping = 0;
        $tax = 0;
        $discount = 0;

        // Calculate shipping
        if ($payment && isset($payment['shipping_method_id'])) {
            $shipping = $this->shippingService->getShippingCost(
                $payment['shipping_method_id'],
                session('checkout.shipping')
            );
        }

        // Calculate tax (if applicable)
        $tax = $this->orderService->calculateTax($subtotal + $shipping, session('checkout.shipping'));

        // Apply coupon discount
        if ($coupon) {
            $discount = $this->orderService->calculateCouponDiscount($coupon, $subtotal);
        }

        $total = $subtotal + $shipping + $tax - $discount;

        return [
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'tax_cents' => $tax,
            'discount_cents' => $discount,
            'total_cents' => $total,
            'item_count' => $cartSummary['item_count']
        ];
    }

    /**
     * Prepare order data for creation
     */
    private function prepareOrderData()
    {
        $shipping = session('checkout.shipping');
        $payment = session('checkout.payment');
        $summary = session('checkout.summary');
        $cartItems = $this->cartService->getItems();
        $coupon = session('checkout.coupon');

        // Prepare billing data
        $billing = $payment['billing_same_as_shipping'] ? $shipping : $payment;

        return [
            'user_id' => Auth::id(),
            'items' => $cartItems,
            'shipping' => $shipping,
            'billing' => $billing,
            'payment_method_id' => $payment['payment_method_id'],
            'shipping_method_id' => $payment['shipping_method_id'],
            'summary' => $summary,
            'coupon' => $coupon,
            'notes' => request('notes'),
        ];
    }
}
