<?php
// File: app/Services/PaymentService.php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment based on method type
     */
    public function processPayment(Payment $payment, PaymentMethod $method, array $data = [])
    {
        switch ($method->code) {
            case 'bank_transfer':
                return $this->processBankTransfer($payment, $data);
            case 'credit_card':
                return $this->processCreditCard($payment, $data);
            case 'ewallet':
                return $this->processEWallet($payment, $data);
            case 'cod':
                return $this->processCashOnDelivery($payment, $data);
            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported payment method'
                ];
        }
    }

    /**
     * Process bank transfer payment
     */
    protected function processBankTransfer(Payment $payment, array $data)
    {
        // For bank transfer, we just mark as pending until manual verification
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Please transfer the payment to our bank account. Your order will be processed after payment verification.',
            'payment_instructions' => $this->getBankTransferInstructions($payment)
        ];
    }

    /**
     * Process credit card payment
     */
    protected function processCreditCard(Payment $payment, array $data)
    {
        try {
            // Integration with payment gateway (example: Midtrans, Xendit, etc.)
            $response = $this->callPaymentGateway('credit_card', [
                'amount' => $payment->amount_cents,
                'order_id' => $payment->order->order_number,
                'payment_id' => $payment->id,
                'customer' => [
                    'name' => $payment->order->shipping_name,
                    'email' => $payment->order->user->email,
                    'phone' => $payment->order->shipping_phone
                ]
            ]);

            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => 'processing',
                    'transaction_id' => $response['transaction_id'],
                    'redirect_url' => $response['redirect_url'],
                    'gateway_response' => $response,
                    'message' => 'Redirecting to payment gateway...'
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'gateway_response' => $response,
                'message' => $response['message'] ?? 'Payment processing failed'
            ];

        } catch (\Exception $e) {
            Log::error('Credit card payment failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment processing failed. Please try again.'
            ];
        }
    }

    /**
     * Process e-wallet payment
     */
    protected function processEWallet(Payment $payment, array $data)
    {
        try {
            // Integration with e-wallet providers
            $response = $this->callPaymentGateway('ewallet', [
                'amount' => $payment->amount_cents,
                'order_id' => $payment->order->order_number,
                'payment_id' => $payment->id,
                'wallet_type' => $data['wallet_type'] ?? 'default'
            ]);

            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => $response['status'],
                    'transaction_id' => $response['transaction_id'],
                    'qr_code' => $response['qr_code'] ?? null,
                    'deep_link' => $response['deep_link'] ?? null,
                    'gateway_response' => $response,
                    'message' => 'Please complete payment in your e-wallet app'
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'gateway_response' => $response,
                'message' => $response['message'] ?? 'E-wallet payment failed'
            ];

        } catch (\Exception $e) {
            Log::error('E-wallet payment failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'E-wallet payment failed. Please try again.'
            ];
        }
    }

    /**
     * Process cash on delivery
     */
    protected function processCashOnDelivery(Payment $payment, array $data)
    {
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Your order will be processed. Please prepare exact amount for cash payment upon delivery.'
        ];
    }

    /**
     * Verify payment callback
     */
    public function verifyCallback($gateway, array $data)
    {
        // Implement signature verification based on gateway
        switch ($gateway) {
            case 'midtrans':
                return $this->verifyMidtransCallback($data);
            case 'xendit':
                return $this->verifyXenditCallback($data);
            default:
                return false;
        }
    }

    /**
     * Process payment callback
     */
    public function processCallback($gateway, array $data)
    {
        try {
            // Parse callback data based on gateway
            $callbackData = $this->parseCallbackData($gateway, $data);

            return [
                'success' => true,
                'payment_id' => $callbackData['payment_id'],
                'status' => $callbackData['status'],
                'transaction_id' => $callbackData['transaction_id'],
                'gateway_response' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Payment callback processing failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed'
            ];
        }
    }

    /**
     * Verify payment status with gateway
     */
    public function verifyPayment(Payment $payment)
    {
        if (!$payment->transaction_id) {
            return ['updated' => false];
        }

        try {
            $response = $this->callPaymentGateway('verify', [
                'transaction_id' => $payment->transaction_id,
                'payment_id' => $payment->id
            ]);

            if ($response['success'] && $response['status'] !== $payment->status) {
                $payment->update([
                    'status' => $response['status'],
                    'gateway_response' => $response,
                    'paid_at' => $response['status'] === 'success' ? now() : $payment->paid_at
                ]);

                return ['updated' => true];
            }

            return ['updated' => false];

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return ['updated' => false];
        }
    }

    /**
     * Get bank transfer instructions
     */
    protected function getBankTransferInstructions(Payment $payment)
    {
        return [
            'banks' => [
                [
                    'name' => 'Bank BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'TokoSaya Indonesia'
                ],
                [
                    'name' => 'Bank Mandiri',
                    'account_number' => '0987654321',
                    'account_name' => 'TokoSaya Indonesia'
                ]
            ],
            'amount' => $payment->amount_cents,
            'reference' => $payment->reference_number,
            'instructions' => [
                'Transfer exact amount to one of the bank accounts above',
                'Use reference number: ' . $payment->reference_number,
                'Upload payment proof after transfer',
                'Payment will be verified within 1-2 business hours'
            ]
        ];
    }

    /**
     * Call payment gateway API
     */
    protected function callPaymentGateway($action, array $data)
    {
        // This is a placeholder - implement actual gateway integration
        $gatewayUrl = config('payment.gateway_url');
        $apiKey = config('payment.api_key');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($gatewayUrl . '/' . $action, $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Gateway API call failed: ' . $response->body());
    }

    /**
     * Verify Midtrans callback signature
     */
    protected function verifyMidtransCallback(array $data)
    {
        $serverKey = config('payment.midtrans.server_key');
        $signature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);

        return hash_equals($signature, $data['signature_key']);
    }

    /**
     * Verify Xendit callback signature
     */
    protected function verifyXenditCallback(array $data)
    {
        $callbackToken = config('payment.xendit.callback_token');
        $signature = hash_hmac('sha256', json_encode($data), $callbackToken);

        return hash_equals($signature, $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '');
    }

    /**
     * Parse callback data based on gateway
     */
    protected function parseCallbackData($gateway, array $data)
    {
        switch ($gateway) {
            case 'midtrans':
                return [
                    'payment_id' => $data['custom_field1'] ?? null,
                    'transaction_id' => $data['transaction_id'],
                    'status' => $this->mapMidtransStatus($data['transaction_status'])
                ];
            case 'xendit':
                return [
                    'payment_id' => $data['external_id'] ?? null,
                    'transaction_id' => $data['id'],
                    'status' => $this->mapXenditStatus($data['status'])
                ];
            default:
                throw new \Exception('Unsupported gateway: ' . $gateway);
        }
    }

    /**
     * Map Midtrans status to internal status
     */
    protected function mapMidtransStatus($status)
    {
        $statusMap = [
            'capture' => 'success',
            'settlement' => 'success',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired'
        ];

        return $statusMap[$status] ?? 'failed';
    }

    /**
     * Map Xendit status to internal status
     */
    protected function mapXenditStatus($status)
    {
        $statusMap = [
            'PAID' => 'success',
            'PENDING' => 'pending',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed'
        ];

        return $statusMap[$status] ?? 'failed';
    }
}
