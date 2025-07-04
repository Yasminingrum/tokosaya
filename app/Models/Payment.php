<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount_cents',
        'fee_cents',
        'status',
        'transaction_id',
        'reference_number',
        'gateway_response',
        'payment_proof',
        'notes',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'fee_cents' => 'integer',
        'gateway_response' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['success', 'failed', 'cancelled', 'expired']);
    }

    // Amount helpers
    public function getAmountAttribute()
    {
        return $this->amount_cents / 100;
    }

    public function getFeeAttribute()
    {
        return $this->fee_cents / 100;
    }

    public function getTotalAmountCentsAttribute()
    {
        return $this->amount_cents + $this->fee_cents;
    }

    public function getTotalAmountAttribute()
    {
        return $this->total_amount_cents / 100;
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedFeeAttribute()
    {
        return 'Rp ' . number_format($this->fee, 0, ',', '.');
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isSuccess()
    {
        return $this->status === 'success';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || ($this->expires_at && $this->expires_at->isPast());
    }

    public function isCompleted()
    {
        return in_array($this->status, ['success', 'failed', 'cancelled', 'expired']);
    }

    public function isActive()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeCancelled()
    {
        return $this->isActive();
    }

    public function canBeRetried()
    {
        return in_array($this->status, ['failed', 'expired']);
    }

    // Other helpers
    public function hasProof()
    {
        return !empty($this->payment_proof);
    }

    public function getProofUrlAttribute()
    {
        if (!$this->payment_proof) {
            return null;
        }

        if (str_starts_with($this->payment_proof, ['http://', 'https://'])) {
            return $this->payment_proof;
        }

        return asset('storage/' . $this->payment_proof);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'success' => 'badge-success',
            'failed' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            'expired' => 'badge-dark',
            default => 'badge-light',
        };
    }

    public function getDaysFromCreatedAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getTimeUntilExpiryAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }

    public function isNearExpiry($hours = 2)
    {
        if (!$this->expires_at || $this->isExpired()) {
            return false;
        }

        return $this->expires_at->diffInHours(now()) <= $hours;
    }

    // Actions
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsSuccess($transactionId = null, $gatewayResponse = [])
    {
        $this->update([
            'status' => 'success',
            'transaction_id' => $transactionId ?: $this->transaction_id,
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
            'paid_at' => now(),
        ]);

        // Update order payment status
        $this->order->markAsPaid();
    }

    public function markAsFailed($reason = null, $gatewayResponse = [])
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
            'notes' => $this->notes . ($reason ? "\nFailed: " . $reason : ''),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . ($reason ? "\nCancelled: " . $reason : ''),
        ]);
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
    }

    public function uploadProof($filePath)
    {
        $this->update([
            'payment_proof' => $filePath,
            'status' => 'processing',
        ]);
    }

    // Static methods
    public static function generateReferenceNumber($prefix = 'PAY')
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;

        return $prefix . $date . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference_number)) {
                $payment->reference_number = static::generateReferenceNumber();
            }

            // Set default expiry for manual payments
            if (empty($payment->expires_at) && $payment->paymentMethod->isManual()) {
                $payment->expires_at = now()->addHours(24);
            }
        });

        static::updated(function ($payment) {
            // Auto-expire if past expiry date
            if ($payment->expires_at &&
                $payment->expires_at->isPast() &&
                $payment->isActive()) {
                $payment->expire();
            }
        });
    }
}
