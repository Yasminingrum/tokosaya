<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'date_of_birth',
        'gender',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_hash', // if using custom column
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the password field for authentication
     * Override this if using custom password column
     */
    public function getAuthPassword()
    {
        // If using 'password_hash' column instead of 'password'
        if (isset($this->attributes['password_hash'])) {
            return $this->password_hash;
        }

        return $this->password;
    }

    /**
     * Relationship: User belongs to Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function canUseCart(): bool
    {
        // Jika user tidak memiliki role, anggap sebagai customer
        if (!$this->role) {
            return true;
        }

        // Hanya role 'customer' yang bisa menggunakan cart
        return $this->role->name === 'customer';
    }

    /**
     * Relationship: User has many Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relationship: User has many Wishlists
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Relationship: User has many Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get customer addresses
     */
    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Alias for addresses (backward compatibility)
     */
    public function addresses()
    {
        return $this->customerAddresses();
    }


    /**
     * Relationship: User has many Shopping Carts
     */
    public function carts()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    /**
     * Relationship: User has one active Shopping Cart (singular)
     */
    public function shoppingCart()
    {
        return $this->hasOne(ShoppingCart::class)->latest();
    }

    /**
     * Relationship: User has many Reviews
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Relationship: User has many Activity Logs
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Helper method: Check if user has specific role
     */
    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return $this->role && in_array($this->role->name, $roleName);
        }
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Helper method: Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    /**
     * Helper method: Check if user is customer
     */
    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    /**
     * Helper method: Get user's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Helper method: Get user's active cart
     */
    public function getActiveCart()
    {
        return $this->carts()
            ->where('expires_at', '>', now())
            ->orWhereNull('expires_at')
            ->first();
    }

    /**
     * Helper method: Get unread notifications count
     */
    public function getUnreadNotificationsCount()
    {
        try {
            return $this->notifications()->where('is_read', false)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Helper method: Get wishlist count
     */
    public function getWishlistCount()
    {
        try {
            return $this->wishlists()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Scope: Active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Users by role
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope: Customers only
     */
    public function scopeCustomers($query)
    {
        return $query->withRole('customer');
    }

    /**
     * Scope: Admins only
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        });
    }
}
