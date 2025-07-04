<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        if ($this->model && $this->model_id) {
            return $this->morphTo('subject', 'model', 'model_id');
        }
        return null;
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $model, $modelId = null)
    {
        $query = $query->where('model', $model);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Helper methods
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->full_name : 'System';
    }

    public function getActionIconAttribute()
    {
        return match($this->action) {
            'created' => 'plus-circle',
            'updated' => 'edit',
            'deleted' => 'trash-2',
            'login' => 'log-in',
            'logout' => 'log-out',
            'view' => 'eye',
            'download' => 'download',
            'upload' => 'upload',
            'sent' => 'send',
            'approved' => 'check-circle',
            'rejected' => 'x-circle',
            default => 'activity',
        };
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'login' => 'info',
            'logout' => 'secondary',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'primary',
        };
    }

    public function hasDataChanges()
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    public function getChangedAttributesAttribute()
    {
        if (!$this->hasDataChanges()) {
            return [];
        }

        $changed = [];
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];

        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changed;
    }

    public function getFormattedIpAddressAttribute()
    {
        if (!$this->ip_address) {
            return null;
        }

        // Convert binary IP to string if needed
        if (strlen($this->ip_address) === 16) {
            return inet_ntop($this->ip_address);
        }

        return $this->ip_address;
    }

    public function getBrowserAttribute()
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        // Simple browser detection
        $userAgent = $this->user_agent;

        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($userAgent, 'Opera')) {
            return 'Opera';
        }

        return 'Unknown';
    }

    // Static methods
    public static function log($action, $description = null, $model = null, $modelId = null, $oldValues = [], $newValues = [])
    {
        return static::create([
            'user_id' => optional(\Illuminate\Support\Facades\Auth::user())->id,
            'action' => $action,
            'description' => $description,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logModelAction($model, $action, $description = null, $oldValues = [], $newValues = [])
    {
        return static::log(
            $action,
            $description,
            get_class($model),
            $model->getKey(),
            $oldValues,
            $newValues
        );
    }

    public static function cleanup($days = 90)
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $log->created_at = now();
        });
    }
}
