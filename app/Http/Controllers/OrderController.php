<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display customer orders
     */
    public function index(Request $request)
    {
        $query = auth()->user()->orders()->with(['items.product', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get order statistics
        $stats = [
            'total_orders' => auth()->user()->orders()->count(),
            'completed_orders' => auth()->user()->orders()->where('status', 'delivered')->count(),
            'pending_orders' => auth()->user()->orders()->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
            'total_spent' => auth()->user()->orders()->where('payment_status', 'paid')->sum('total_cents') / 100
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Show single order
     */
    public function show(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id() && !auth()->user()->role->name === 'admin') {
            abort(403);
        }

        $order->load([
            'items.product.images',
            'items.variant',
            'payments',
            'user'
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Create order from cart (Checkout process)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:50',
            'shipping_state' => 'required|string|max:50',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_country' => 'required|string|size:2',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:500',
            'use_billing_address' => 'boolean',
            'billing_name' => 'required_if:use_billing_address,true|string|max:100',
            'billing_phone' => 'required_if:use_billing_address,true|string|max:15',
            'billing_address' => 'required_if:use_billing_address,true|string',
            'billing_city' => 'required_if:use_billing_address,true|string|max:50',
            'billing_state' => 'required_if:use_billing_address,true|string|max:50',
            'billing_postal_code' => 'required_if:use_billing_address,true|string|max:10',
            'billing_country' => 'required_if:use_billing_address,true|string|size:2'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Get user's cart
            $cart = ShoppingCart::where('user_id', auth()->id())->first();

            if (!$cart || $cart->item_count == 0) {
                throw new \Exception('Keranjang kosong');
            }

            $cartItems = $cart->items()->with(['product', 'variant'])->get();

            // Validate stock availability
            foreach ($cartItems as $item) {
                $availableStock = $item->variant ?
                    $item->variant->stock_quantity :
                    $item->product->stock_quantity;

                if ($availableStock < $item->quantity) {
                    throw new \Exception("Stok {$item->product->name} tidak mencukupi");
                }
            }

            // Calculate totals
            $subtotal = $cartItems->sum('total_price_cents');
            $shipping = $this->calculateShippingCost($subtotal, $request);
            $tax = $this->calculateTax($subtotal);

            // Apply coupon discount
            $discount = 0;
            $couponCode = null;
            if (session('applied_coupon')) {
                $discount = session('applied_coupon')['discount_amount'];
                $couponCode = session('applied_coupon')['code'];
            }

            $total = $subtotal + $shipping + $tax - $discount;

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal_cents' => $subtotal,
                'tax_cents' => $tax,
                'shipping_cents' => $shipping,
                'discount_cents' => $discount,
                'total_cents' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => $request->shipping_country,
                'billing_name' => $request->use_billing_address ? $request->billing_name : null,
                'billing_phone' => $request->use_billing_address ? $request->billing_phone : null,
                'billing_address' => $request->use_billing_address ? $request->billing_address : null,
                'billing_city' => $request->use_billing_address ? $request->billing_city : null,
                'billing_state' => $request->use_billing_address ? $request->billing_state : null,
                'billing_postal_code' => $request->use_billing_address ? $request->billing_postal_code : null,
                'billing_country' => $request->use_billing_address ? $request->billing_country : null,
                'notes' => $request->notes,
                'coupon_code' => $couponCode,
                'payment_method_id' => $request->payment_method_id
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->variant ? $cartItem->variant->sku : $cartItem->product->sku,
                    'variant_name' => $cartItem->variant ? $cartItem->variant->variant_name . ': ' . $cartItem->variant->variant_value : null,
                    'quantity' => $cartItem->quantity,
                    'unit_price_cents' => $cartItem->unit_price_cents,
                    'total_price_cents' => $cartItem->total_price_cents,
                    'cost_price_cents' => $cartItem->product->cost_price_cents
                ]);

                // Reserve stock
                if ($cartItem->variant) {
                    $cartItem->variant->increment('reserved_quantity', $cartItem->quantity);
                } else {
                    $cartItem->product->increment('reserved_quantity', $cartItem->quantity);
                }
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $request->payment_method_id,
                'amount_cents' => $total,
                'status' => 'pending'
            ]);

            // Clear cart
            $cart->items()->delete();
            $cart->update(['item_count' => 0, 'total_cents' => 0]);

            // Clear applied coupon
            session()->forget('applied_coupon');

            // Log activity
            activity('order_created')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['order_number' => $orderNumber, 'total' => $total])
                ->log('Order created');

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'Pesanan tidak dapat dibatalkan']);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $item->variant->decrement('reserved_quantity', $item->quantity);
                } else {
                    $item->product->decrement('reserved_quantity', $item->quantity);
                }
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            // Update payment status
            $order->payments()->update(['status' => 'cancelled']);

            // Log activity
            activity('order_cancelled')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->log('Order cancelled by customer');

            DB::commit();

            return back()->with('success', 'Pesanan berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal membatalkan pesanan']);
        }
    }

    /**
     * Reorder (Add order items back to cart)
     */
    public function reorder(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $cart = ShoppingCart::firstOrCreate(
                ['user_id' => auth()->id()],
                ['item_count' => 0, 'total_cents' => 0]
            );

            $addedItems = 0;
            $unavailableItems = [];

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);

                if (!$product || $product->status !== 'active') {
                    $unavailableItems[] = $item->product_name;
                    continue;
                }

                $variant = $item->variant_id ? $product->variants()->find($item->variant_id) : null;

                // Check stock
                $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
                if ($availableStock < $item->quantity) {
                    $unavailableItems[] = $item->product_name . ' (stok tidak mencukupi)';
                    continue;
                }

                // Check if item already in cart
                $existingItem = $cart->items()
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variant?->id)
                    ->first();

                $unitPrice = $variant ?
                    ($product->price_cents + $variant->price_adjustment_cents) :
                    $product->price_cents;

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $item->quantity;
                    if ($newQuantity <= $availableStock) {
                        $existingItem->update([
                            'quantity' => $newQuantity,
                            'total_price_cents' => $unitPrice * $newQuantity
                        ]);
                        $addedItems++;
                    } else {
                        $unavailableItems[] = $item->product_name . ' (total quantity melebihi stok)';
                    }
                } else {
                    $cart->items()->create([
                        'product_id' => $product->id,
                        'variant_id' => $variant?->id,
                        'quantity' => $item->quantity,
                        'unit_price_cents' => $unitPrice,
                        'total_price_cents' => $unitPrice * $item->quantity
                    ]);
                    $addedItems++;
                }
            }

            // Update cart totals
            $totals = $cart->items()->selectRaw('SUM(quantity) as total_items, SUM(total_price_cents) as total_price')->first();
            $cart->update([
                'item_count' => $totals->total_items ?? 0,
                'total_cents' => $totals->total_price ?? 0
            ]);

            $message = "Berhasil menambahkan {$addedItems} item ke keranjang";
            if (!empty($unavailableItems)) {
                $message .= ". Item yang tidak dapat ditambahkan: " . implode(', ', $unavailableItems);
            }

            return redirect()->route('cart.index')->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan item ke keranjang']);
        }
    }

    /**
     * Track order
     */
    public function track(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payments']);

        // Order timeline
        $timeline = [
            [
                'status' => 'pending',
                'title' => 'Pesanan Dibuat',
                'description' => 'Pesanan Anda telah dibuat dan menunggu konfirmasi',
                'date' => $order->created_at,
                'completed' => true
            ],
            [
                'status' => 'confirmed',
                'title' => 'Pesanan Dikonfirmasi',
                'description' => 'Pesanan Anda telah dikonfirmasi dan siap diproses',
                'date' => $order->confirmed_at,
                'completed' => in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'])
            ],
            [
                'status' => 'processing',
                'title' => 'Sedang Diproses',
                'description' => 'Pesanan Anda sedang diproses dan dikemas',
                'date' => null,
                'completed' => in_array($order->status, ['processing', 'shipped', 'delivered'])
            ],
            [
                'status' => 'shipped',
                'title' => 'Dikirim',
                'description' => 'Pesanan Anda telah dikirim',
                'date' => $order->shipped_at,
                'completed' => in_array($order->status, ['shipped', 'delivered'])
            ],
            [
                'status' => 'delivered',
                'title' => 'Terkirim',
                'description' => 'Pesanan Anda telah sampai di tujuan',
                'date' => $order->delivered_at,
                'completed' => $order->status === 'delivered'
            ]
        ];

        return view('orders.track', compact('order', 'timeline'));
    }

    /**
     * Admin: Display all orders
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items', 'payments']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('shipping_name', 'LIKE', "%{$search}%")
                  ->orWhere('shipping_phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'LIKE', "%{$search}%")
                               ->orWhere('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_cents') / 100,
            'pending_payments' => Order::where('payment_status', 'pending')->count()
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Admin: Show single order
     */
    public function adminShow(Order $order)
    {
        $order->load([
            'user',
            'items.product.images',
            'items.variant',
            'payments.paymentMethod'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();

        try {
            $updateData = ['status' => $newStatus];

            // Update timestamps based on status
            switch ($newStatus) {
                case 'confirmed':
                    $updateData['confirmed_at'] = now();
                    break;
                case 'shipped':
                    $updateData['shipped_at'] = now();
                    if ($request->tracking_number) {
                        $updateData['tracking_number'] = $request->tracking_number;
                    }
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    // Auto-confirm payment for COD orders
                    if ($order->payment_status === 'pending') {
                        $updateData['payment_status'] = 'paid';
                        $order->payments()->update(['status' => 'success', 'paid_at' => now()]);
                    }
                    break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    // Restore stock
                    foreach ($order->items as $item) {
                        if ($item->variant_id) {
                            $item->variant->decrement('reserved_quantity', $item->quantity);
                        } else {
                            $item->product->decrement('reserved_quantity', $item->quantity);
                        }
                    }
                    // Cancel payments
                    $order->payments()->update(['status' => 'cancelled']);
                    break;
            }

            // Add internal notes
            if ($request->notes) {
                $updateData['internal_notes'] = ($order->internal_notes ? $order->internal_notes . "\n\n" : '') .
                    "[" . now()->format('Y-m-d H:i:s') . "] " . $request->notes;
            }

            $order->update($updateData);

            // If order is confirmed, reduce stock and reserved quantity
            if ($newStatus === 'confirmed' && $oldStatus === 'pending') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $variant = $item->variant;
                        $variant->decrement('stock_quantity', $item->quantity);
                        $variant->decrement('reserved_quantity', $item->quantity);
                    } else {
                        $product = $item->product;
                        $product->decrement('stock_quantity', $item->quantity);
                        $product->decrement('reserved_quantity', $item->quantity);

                        // Update product metrics
                        $product->increment('sale_count', $item->quantity);
                        $product->increment('revenue_cents', $item->total_price_cents);
                        $product->update(['last_sold_at' => now()]);
                    }
                }
            }

            // Log activity
            activity('order_status_updated')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'notes' => $request->notes
                ])
                ->log('Order status updated');

            // Send notification to customer
            $order->user->notifications()->create([
                'type' => 'order_status_update',
                'title' => 'Status Pesanan Diperbarui',
                'message' => "Status pesanan #{$order->order_number} telah diperbarui menjadi: " . ucfirst($newStatus),
                'data' => json_encode([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $newStatus
                ]),
                'action_url' => route('orders.show', $order)
            ]);

            DB::commit();

            return back()->with('success', 'Status pesanan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal memperbarui status pesanan: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin: Add note to order
     */
    public function addNote(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $timestamp = now()->format('Y-m-d H:i:s');
            $user = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            $newNote = "[{$timestamp}] {$user}: {$request->note}";

            $order->update([
                'internal_notes' => ($order->internal_notes ? $order->internal_notes . "\n\n" : '') . $newNote
            ]);

            // Log activity
            activity('order_note_added')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties(['note' => $request->note])
                ->log('Note added to order');

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil ditambahkan',
                'note' => $newNote
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Print invoice
     */
    public function printInvoice(Order $order)
    {
        $order->load([
            'user',
            'items.product',
            'items.variant',
            'payments.paymentMethod'
        ]);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Admin: Bulk update orders
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*' => 'exists:orders,id',
            'action' => 'required|in:confirm,cancel,mark_shipped,mark_delivered',
            'tracking_number' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $orders = Order::whereIn('id', $request->orders)->get();
            $updated = 0;

            foreach ($orders as $order) {
                $canUpdate = false;
                $newStatus = $order->status;

                switch ($request->action) {
                    case 'confirm':
                        if ($order->status === 'pending') {
                            $newStatus = 'confirmed';
                            $canUpdate = true;
                        }
                        break;
                    case 'cancel':
                        if (in_array($order->status, ['pending', 'confirmed'])) {
                            $newStatus = 'cancelled';
                            $canUpdate = true;
                        }
                        break;
                    case 'mark_shipped':
                        if (in_array($order->status, ['confirmed', 'processing'])) {
                            $newStatus = 'shipped';
                            $canUpdate = true;
                        }
                        break;
                    case 'mark_delivered':
                        if ($order->status === 'shipped') {
                            $newStatus = 'delivered';
                            $canUpdate = true;
                        }
                        break;
                }

                if ($canUpdate) {
                    $updateData = ['status' => $newStatus];

                    // Update timestamps
                    switch ($newStatus) {
                        case 'confirmed':
                            $updateData['confirmed_at'] = now();
                            break;
                        case 'shipped':
                            $updateData['shipped_at'] = now();
                            if ($request->tracking_number) {
                                $updateData['tracking_number'] = $request->tracking_number;
                            }
                            break;
                        case 'delivered':
                            $updateData['delivered_at'] = now();
                            break;
                        case 'cancelled':
                            $updateData['cancelled_at'] = now();
                            break;
                    }

                    $order->update($updateData);
                    $updated++;

                    // Handle stock for confirmed orders
                    if ($newStatus === 'confirmed' && $order->status === 'pending') {
                        foreach ($order->items as $item) {
                            if ($item->variant_id) {
                                $item->variant->decrement('stock_quantity', $item->quantity);
                                $item->variant->decrement('reserved_quantity', $item->quantity);
                            } else {
                                $item->product->decrement('stock_quantity', $item->quantity);
                                $item->product->decrement('reserved_quantity', $item->quantity);
                            }
                        }
                    }

                    // Handle stock for cancelled orders
                    if ($newStatus === 'cancelled') {
                        foreach ($order->items as $item) {
                            if ($item->variant_id) {
                                $item->variant->decrement('reserved_quantity', $item->quantity);
                            } else {
                                $item->product->decrement('reserved_quantity', $item->quantity);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui {$updated} pesanan"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $prefix = 'TS'; // TokoSaya
        $date = now()->format('ymd');

        // Get today's order count
        $todayCount = Order::whereDate('created_at', now())->count() + 1;
        $number = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        return $prefix . $date . $number;
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShippingCost($subtotal, $request)
    {
        // Simple implementation - you can enhance this with shipping zones and methods

        // Free shipping above 100k
        if ($subtotal >= 10000000) { // 100k in cents
            return 0;
        }

        // Different rates based on location
        $city = strtolower($request->shipping_city);

        if (in_array($city, ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'])) {
            return 1500000; // 15k for Jabodetabek
        } elseif (in_array($city, ['bandung', 'semarang', 'surabaya', 'yogyakarta'])) {
            return 2000000; // 20k for major cities
        } else {
            return 2500000; // 25k for other cities
        }
    }

    /**
     * Calculate tax
     */
    private function calculateTax($subtotal)
    {
        // Simple 10% tax
        return (int) ($subtotal * 0.1);
    }
}
