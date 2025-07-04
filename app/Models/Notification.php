<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function isRead()
    {
        return $this->is_read;
    }

    public function isUnread()
    {
        return !$this->is_read;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasAction()
    {
        return !empty($this->action_url);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getDaysAgoAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'order_confirmed' => 'check-circle',
            'order_shipped' => 'truck',
            'order_delivered' => 'package',
            'payment_received' => 'credit-card',
            'product_review' => 'star',
            'stock_alert' => 'alert-triangle',
            'promotion' => 'tag',
            'system' => 'info',
            default => 'bell',
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'order_confirmed' => 'success',
            'order_shipped' => 'info',
            'order_delivered' => 'success',
            'payment_received' => 'primary',
            'product_review' => 'warning',
            'stock_alert' => 'danger',
            'promotion' => 'purple',
            'system' => 'secondary',
            default => 'light',
        };
    }

    // Actions
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
    }

    // Static methods
    public static function createForUser($userId, $type, $title, $message, $data = [], $actionUrl = null, $expiresAt = null)
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'expires_at' => $expiresAt,
        ]);
    }

    public static function markAllAsReadForUser($userId)
    {
        static::where('user_id', $userId)
              ->where('is_read', false)
              ->update([
                  'is_read' => true,
                  'read_at' => now(),
              ]);
    }

    public static function getUnreadCountForUser($userId)
    {
        return static::where('user_id', $userId)
                    ->unread()
                    ->active()
                    ->count();
    }

    public static function cleanExpired()
    {
        static::expired()->delete();
    }
}
