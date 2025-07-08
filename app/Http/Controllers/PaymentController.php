<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use AuthorizesRequests;
    protected $paymentService;
    protected $orderService;

    public function __construct(PaymentService $paymentService, OrderService $orderService)
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    /**
     * Process payment for an order
     */
    public function process(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_proof' => 'nullable|image|max:2048'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            // Verify order belongs to authenticated user
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to order'
                ], 403);
            }

            // Check if order can be paid
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be paid at this time'
                ], 400);
            }

            if ($order->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has already been processed for this order'
                ], 400);
            }

            DB::beginTransaction();

            // Get payment method
            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

            // Create payment record
            $paymentData = [
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount_cents' => $order->total_cents,
                'fee_cents' => $this->calculatePaymentFee($paymentMethod, $order->total_cents),
                'reference_number' => $this->generateReferenceNumber(),
                'status' => 'pending'
            ];

            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                $paymentData['payment_proof'] = $this->uploadPaymentProof($request->file('payment_proof'));
            }

            $payment = Payment::create($paymentData);

            // Process payment based on method type
            $result = $this->paymentService->processPayment($payment, $paymentMethod, $request->all());

            if ($result['success']) {
                // Update payment status
                $payment->update([
                    'status' => $result['status'],
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'gateway_response' => $result['gateway_response'] ?? null,
                    'paid_at' => $result['paid_at'] ?? null
                ]);

                // Update order payment status
                $this->orderService->updatePaymentStatus($order, $result['status']);

                DB::commit();

                Log::info('Payment processed successfully', [
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'amount' => $payment->amount_cents,
                    'method' => $paymentMethod->code,
                    'status' => $result['status']
                ]);

                return response()->json([
                    'success' => true,
                    'payment_id' => $payment->id,
                    'status' => $result['status'],
                    'redirect_url' => $result['redirect_url'] ?? null,
                    'message' => $result['message'] ?? 'Payment processed successfully'
                ]);

            } else {
                // Update payment status to failed
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $result['gateway_response'] ?? null
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Payment processing failed'
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Payment processing failed', [
                'order_id' => $request->order_id,
                'payment_method_id' => $request->payment_method_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle payment gateway callbacks
     */
    public function callback(Request $request, $gateway)
    {
        try {
            Log::info('Payment callback received', [
                'gateway' => $gateway,
                'data' => $request->all()
            ]);

            // Verify callback authenticity
            if (!$this->paymentService->verifyCallback($gateway, $request->all())) {
                Log::warning('Invalid payment callback received', [
                    'gateway' => $gateway,
                    'data' => $request->all()
                ]);

                return response('Invalid callback', 400);
            }

            // Process callback
            $result = $this->paymentService->processCallback($gateway, $request->all());

            if ($result['success']) {
                $payment = Payment::find($result['payment_id']);

                if ($payment) {
                    DB::beginTransaction();

                    // Update payment
                    $payment->update([
                        'status' => $result['status'],
                        'transaction_id' => $result['transaction_id'] ?? $payment->transaction_id,
                        'gateway_response' => $result['gateway_response'] ?? $payment->gateway_response,
                        'paid_at' => $result['status'] === 'success' ? now() : $payment->paid_at
                    ]);

                    // Update order
                    $this->orderService->updatePaymentStatus($payment->order, $result['status']);

                    DB::commit();

                    Log::info('Payment callback processed successfully', [
                        'payment_id' => $payment->id,
                        'order_id' => $payment->order_id,
                        'status' => $result['status']
                    ]);
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Payment callback processing failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $payment = null;

        if ($paymentId) {
            $payment = Payment::with(['order', 'paymentMethod'])
                ->where('id', $paymentId)
                ->whereHas('order', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->first();
        }

        return view('payment.success', compact('payment'));
    }

    /**
     * Payment failed page
     */
    public function failed(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $payment = null;

        if ($paymentId) {
            $payment = Payment::with(['order', 'paymentMethod'])
                ->where('id', $paymentId)
                ->whereHas('order', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->first();
        }

        return view('payment.failed', compact('payment'));
    }

    /**
     * Verify payment status
     */
    public function verify(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id'
        ]);

        try {
            $payment = Payment::with(['order', 'paymentMethod'])
                ->where('id', $request->payment_id)
                ->whereHas('order', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            // Verify payment with gateway
            $result = $this->paymentService->verifyPayment($payment);

            if ($result['updated']) {
                // Payment status was updated
                $payment->refresh();

                // Update order if needed
                $this->orderService->updatePaymentStatus($payment->order, $payment->status);
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'amount' => $payment->amount_cents,
                    'paid_at' => $payment->paid_at,
                    'transaction_id' => $payment->transaction_id
                ],
                'order' => [
                    'id' => $payment->order->id,
                    'order_number' => $payment->order->order_number,
                    'status' => $payment->order->status,
                    'payment_status' => $payment->order->payment_status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'payment_id' => $request->payment_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 500);
        }
    }

    /**
     * Admin: List all payments
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['order', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->whereHas('paymentMethod', function($q) use ($request) {
                $q->where('code', $request->payment_method);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(20);

        // Payment statistics
        $stats = [
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'successful_payments' => Payment::where('status', 'success')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'success')->sum('amount_cents'),
            'today_amount' => Payment::where('status', 'success')
                ->whereDate('paid_at', today())
                ->sum('amount_cents')
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Admin: Show payment details
     */
    public function adminShow(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['order.user', 'order.items.product', 'paymentMethod']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Admin: Approve manual payment
     */
    public function approve(Payment $payment)
    {
        $this->authorize('update', $payment);

        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending payments can be approved'
                ], 400);
            }

            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'notes' => 'Manually approved by admin: ' . Auth::user()->name
            ]);

            // Update order payment status
            $this->orderService->updatePaymentStatus($payment->order, 'success');

            DB::commit();

            Log::info('Payment approved manually', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Payment approval failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve payment'
            ], 500);
        }
    }

    /**
     * Admin: Reject manual payment
     */
    public function reject(Payment $payment)
    {
        $this->authorize('update', $payment);

        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending payments can be rejected'
                ], 400);
            }

            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'status' => 'failed',
                'notes' => 'Manually rejected by admin: ' . Auth::user()->name
            ]);

            // Update order payment status
            $this->orderService->updatePaymentStatus($payment->order, 'failed');

            DB::commit();

            Log::info('Payment rejected manually', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'rejected_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Payment rejection failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment'
            ], 500);
        }
    }

    /**
     * Calculate payment fee based on method
     */
    private function calculatePaymentFee(PaymentMethod $method, $amount)
    {
        if ($method->fee_type === 'fixed') {
            return $method->fee_amount_cents;
        }

        if ($method->fee_type === 'percentage') {
            return (int) round($amount * ($method->fee_amount_cents / 10000));
        }

        return 0;
    }

    /**
     * Generate unique reference number
     */
    private function generateReferenceNumber()
    {
        do {
            $reference = 'PAY' . date('Ymd') . strtoupper(Str::random(6));
        } while (Payment::where('reference_number', $reference)->exists());

        return $reference;
    }

    /**
     * Upload payment proof file
     */
    private function uploadPaymentProof($file)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment-proofs', $filename, 'public');

        return $path;
    }
}
