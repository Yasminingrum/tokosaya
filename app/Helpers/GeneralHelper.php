<?php

/**
 * Additional Global Helper Functions for TokoSaya
 * This file extends the existing GeneralHelper.php
 * Add these functions to your existing GeneralHelper.php file
 */

use App\Helpers\PriceHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!function_exists('format_currency')) {
    /**
     * Format price from cents to Rupiah currency
     *
     * @param int $cents
     * @param bool $showSymbol
     * @return string
     */
    function format_currency($cents, $showSymbol = true)
    {
        return PriceHelper::format($cents, $showSymbol);
    }
}

if (!function_exists('format_price')) {
    /**
     * Alias for format_currency
     *
     * @param int $cents
     * @param bool $showSymbol
     * @return string
     */
    function format_price($cents, $showSymbol = true)
    {
        return format_currency($cents, $showSymbol);
    }
}

if (!function_exists('price_to_cents')) {
    /**
     * Convert rupiah to cents
     *
     * @param float $rupiah
     * @return int
     */
    function price_to_cents($rupiah)
    {
        return PriceHelper::toCents($rupiah);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return PriceHelper::symbol();
    }
}

if (!function_exists('format_number')) {
    /**
     * Format number with Indonesian format
     *
     * @param int|float $number
     * @param int $decimals
     * @return string
     */
    function format_number($number, $decimals = 0)
    {
        return number_format($number, $decimals, ',', '.');
    }
}

if (!function_exists('percentage')) {
    /**
     * Calculate percentage
     *
     * @param float $value
     * @param float $total
     * @param int $decimals
     * @return float
     */
    function percentage($value, $total, $decimals = 1)
    {
        if ($total == 0) {
            return 0;
        }
        return round(($value / $total) * 100, $decimals);
    }
}

if (!function_exists('short_number')) {
    /**
     * Format large numbers to short format (1K, 1M, etc)
     *
     * @param int $number
     * @return string
     */
    function short_number($number)
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }

        return (string) $number;
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate Bootstrap badge HTML for status
     *
     * @param string $status
     * @param array $colors
     * @return string
     */
    function status_badge($status, $colors = [])
    {
        $defaultColors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'draft' => 'secondary',
            'published' => 'success',
            'discontinued' => 'dark'
        ];

        $allColors = array_merge($defaultColors, $colors);
        $color = $allColors[$status] ?? 'secondary';
        $label = ucfirst(str_replace('_', ' ', $status));

        return "<span class=\"badge bg-{$color}\">{$label}</span>";
    }
}

if (!function_exists('stock_badge')) {
    /**
     * Generate stock status badge
     *
     * @param int $stock
     * @param int $minLevel
     * @return string
     */
    function stock_badge($stock, $minLevel = 0)
    {
        if ($stock == 0) {
            return '<span class="badge bg-danger">Out of Stock</span>';
        } elseif ($stock <= $minLevel) {
            return '<span class="badge bg-warning">Low Stock</span>';
        } else {
            return '<span class="badge bg-success">In Stock</span>';
        }
    }
}

if (!function_exists('time_ago')) {
    /**
     * Get human readable time ago
     *
     * @param string|Carbon $datetime
     * @return string
     */
    function time_ago($datetime)
    {
        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }

        return $datetime->diffForHumans();
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to specified length
     *
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('avatar_url')) {
    /**
     * Generate avatar URL (placeholder or user avatar)
     *
     * @param string|null $avatar
     * @param string $name
     * @param int $size
     * @return string
     */
    function avatar_url($avatar = null, $name = 'User', $size = 40)
    {
        if ($avatar && file_exists(public_path('storage/' . $avatar))) {
            return asset('storage/' . $avatar);
        }

        // Generate initials avatar with UI Avatars
        $initials = strtoupper(substr($name, 0, 1));
        if (strpos($name, ' ') !== false) {
            $parts = explode(' ', $name);
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return "https://ui-avatars.com/api/?name={$initials}&size={$size}&background=007bff&color=fff";
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Get product image URL with fallback
     *
     * @param string|null $image
     * @param string $size
     * @return string
     */
    function product_image_url($image = null, $size = 'medium')
    {
        if ($image && file_exists(public_path('storage/' . $image))) {
            return asset('storage/' . $image);
        }

        // Return placeholder image
        $dimensions = [
            'small' => '150x150',
            'medium' => '300x300',
            'large' => '600x600'
        ];

        $dim = $dimensions[$size] ?? '300x300';
        return "https://via.placeholder.com/{$dim}/f8f9fa/6c757d?text=No+Image";
    }
}

if (!function_exists('file_size_format')) {
    /**
     * Format file size in human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function file_size_format($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

if (!function_exists('rating_stars')) {
    /**
     * Generate star rating HTML
     *
     * @param float $rating
     * @param int $maxStars
     * @return string
     */
    function rating_stars($rating, $maxStars = 5)
    {
        $html = '';
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;

        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $fullStars) {
                $html .= '<i class="fas fa-star text-warning"></i>';
            } elseif ($i == $fullStars + 1 && $halfStar) {
                $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
            } else {
                $html .= '<i class="far fa-star text-muted"></i>';
            }
        }

        return $html;
    }
}

if (!function_exists('setting')) {
    /**
     * Get application setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        try {
            if (Schema::hasTable('settings')) {
                $setting = DB::table('settings')
                    ->where('key_name', $key)
                    ->first();

                if ($setting) {
                    return $setting->value;
                }
            }
        } catch (\Exception $e) {
            // Ignore errors and return default
        }

        return $default;
    }
}

if (!function_exists('flash_message')) {
    /**
     * Set flash message
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    function flash_message($type, $message)
    {
        session()->flash('flash_message', [
            'type' => $type,
            'message' => $message
        ]);
    }
}

if (!function_exists('success_message')) {
    /**
     * Set success flash message
     *
     * @param string $message
     * @return void
     */
    function success_message($message)
    {
        flash_message('success', $message);
    }
}

if (!function_exists('error_message')) {
    /**
     * Set error flash message
     *
     * @param string $message
     * @return void
     */
    function error_message($message)
    {
        flash_message('error', $message);
    }
}

if (!function_exists('warning_message')) {
    /**
     * Set warning flash message
     *
     * @param string $message
     * @return void
     */
    function warning_message($message)
    {
        flash_message('warning', $message);
    }
}

if (!function_exists('info_message')) {
    /**
     * Set info flash message
     *
     * @param string $message
     * @return void
     */
    function info_message($message)
    {
        flash_message('info', $message);
    }
}
