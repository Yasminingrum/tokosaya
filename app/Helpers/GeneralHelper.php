<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


if (!function_exists('format_weight')) {
    /**
     * Format weight from grams
     */
    function format_weight($weightGrams)
    {
        if ($weightGrams < 1000) {
            return $weightGrams . ' gram';
        } else {
            $kg = $weightGrams / 1000;
            return number_format($kg, 1) . ' kg';
        }
    }
}

if (!function_exists('format_dimensions')) {
    /**
     * Format product dimensions
     */
    function format_dimensions($lengthMm, $widthMm, $heightMm)
    {
        if (!$lengthMm || !$widthMm || !$heightMm) {
            return '-';
        }

        $length = $lengthMm / 10; // Convert to cm
        $width = $widthMm / 10;
        $height = $heightMm / 10;

        return "{$length} x {$width} x {$height} cm";
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size in human readable format
     */
    function format_file_size($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('generate_sku')) {
    /**
     * Generate unique SKU
     */
    function generate_sku($prefix = 'TSY')
    {
        return strtoupper($prefix) . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
}

if (!function_exists('format_phone_number')) {
    /**
     * Format Indonesian phone number
     */
    function format_phone_number($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to +62 format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return '+' . $phone;
    }
}

if (!function_exists('mask_email')) {
    /**
     * Mask email address for privacy
     */
    function mask_email($email)
    {
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1];

        if (strlen($username) <= 2) {
            $maskedUsername = str_repeat('*', strlen($username));
        } else {
            $maskedUsername = substr($username, 0, 1) .
                            str_repeat('*', strlen($username) - 2) .
                            substr($username, -1);
        }

        return $maskedUsername . '@' . $domain;
    }
}

if (!function_exists('format_date_indonesia')) {
    /**
     * Format date in Indonesian format
     */
    function format_date_indonesia($date, $includeTime = false)
    {
        if (!$date) {
            return '-';
        }

        $carbonDate = \Carbon\Carbon::parse($date);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $day = $carbonDate->day;
        $month = $months[$carbonDate->month];
        $year = $carbonDate->year;

        $formatted = "{$day} {$month} {$year}";

        if ($includeTime) {
            $time = $carbonDate->format('H:i');
            $formatted .= " pukul {$time}";
        }

        return $formatted;
    }
}

if (!function_exists('time_ago_indonesia')) {
    /**
     * Get time ago in Indonesian
     */
    function time_ago_indonesia($date)
    {
        if (!$date) {
            return '-';
        }

        $carbonDate = \Carbon\Carbon::parse($date);
        $now = \Carbon\Carbon::now();

        $diffInSeconds = $now->diffInSeconds($carbonDate);
        $diffInMinutes = $now->diffInMinutes($carbonDate);
        $diffInHours = $now->diffInHours($carbonDate);
        $diffInDays = $now->diffInDays($carbonDate);
        $diffInWeeks = $now->diffInWeeks($carbonDate);
        $diffInMonths = $now->diffInMonths($carbonDate);
        $diffInYears = $now->diffInYears($carbonDate);

        if ($diffInSeconds < 60) {
            return 'Baru saja';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit yang lalu';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' jam yang lalu';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' hari yang lalu';
        } elseif ($diffInWeeks < 4) {
            return $diffInWeeks . ' minggu yang lalu';
        } elseif ($diffInMonths < 12) {
            return $diffInMonths . ' bulan yang lalu';
        } else {
            return $diffInYears . ' tahun yang lalu';
        }
    }
}

if (!function_exists('slug_generator')) {
    /**
     * Generate URL-friendly slug
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    function slug_generator($text, $separator = '-')
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Replace Indonesian characters
        $replacements = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n'
        ];

        $text = strtr($text, $replacements);

        // Remove special characters and replace with separator
        $text = preg_replace('/[^a-z0-9]+/', $separator, $text);

        // Remove leading/trailing separators
        $text = trim($text, $separator);

        return $text;
    }
}

if (!function_exists('format_stock_status')) {
    /**
     * Format stock status with color coding
     *
     * @param int $stock
     * @param int $minLevel
     * @return array
     */
    function format_stock_status($stock, $minLevel = 5)
    {
        if ($stock <= 0) {
            return [
                'status' => 'Habis',
                'class' => 'text-danger',
                'badge' => 'badge-danger'
            ];
        } elseif ($stock <= $minLevel) {
            return [
                'status' => 'Stok Menipis',
                'class' => 'text-warning',
                'badge' => 'badge-warning'
            ];
        } else {
            return [
                'status' => 'Tersedia',
                'class' => 'text-success',
                'badge' => 'badge-success'
            ];
        }
    }
}

if (!function_exists('generate_order_number')) {
    /**
     * Generate unique order number
     *
     * @param string $prefix
     * @return string
     */
    function generate_order_number($prefix = 'TSY')
    {
        $date = date('ymd');
        $random = strtoupper(Str::random(4));
        $timestamp = substr(time(), -4);

        return $prefix . $date . $random . $timestamp;
    }
}

if (!function_exists('calculate_shipping_weight')) {
    /**
     * Calculate total shipping weight
     *
     * @param array $items
     * @return int Weight in grams
     */
    function calculate_shipping_weight($items)
    {
        $totalWeight = 0;

        foreach ($items as $item) {
            $weight = $item['weight_grams'] ?? 0;
            $quantity = $item['quantity'] ?? 1;
            $totalWeight += $weight * $quantity;
        }

        // Minimum weight 100g for shipping calculation
        return max($totalWeight, 100);
    }
}

if (!function_exists('format_rating')) {
    /**
     * Format rating display
     *
     * @param float $rating
     * @param int $maxRating
     * @return array
     */
    function format_rating($rating, $maxRating = 5)
    {
        $rating = round($rating, 1);
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = $maxRating - $fullStars - ($halfStar ? 1 : 0);

        return [
            'rating' => $rating,
            'full_stars' => $fullStars,
            'half_star' => $halfStar,
            'empty_stars' => $emptyStars,
            'percentage' => ($rating / $maxRating) * 100
        ];
    }
}

if (!function_exists('validate_indonesian_phone')) {
    /**
     * Validate Indonesian phone number
     *
     * @param string $phone
     * @return bool
     */
    function validate_indonesian_phone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if starts with valid Indonesian prefixes
        $validPrefixes = ['08', '628', '62'];

        foreach ($validPrefixes as $prefix) {
            if (substr($phone, 0, strlen($prefix)) === $prefix) {
                // Check length (10-15 digits total)
                return strlen($phone) >= 10 && strlen($phone) <= 15;
            }
        }

        return false;
    }
}

if (!function_exists('get_image_url')) {
    /**
     * Get full image URL with fallback
     *
     * @param string|null $imagePath
     * @param string $fallback
     * @return string
     */
    function get_image_url($imagePath, $fallback = 'images/no-image.png')
    {
        if (!$imagePath) {
            return asset($fallback);
        }

        // If already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Check if file exists
        if (Storage::disk('public')->exists($imagePath)) {
            return asset('storage/' . $imagePath);
        }

        // Return fallback
        return asset($fallback);
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text with proper word boundary
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

        $truncated = substr($text, 0, $length);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . $suffix;
    }
}

if (!function_exists('format_order_status')) {
    /**
     * Format order status with styling
     *
     * @param string $status
     * @return array
     */
    function format_order_status($status)
    {
        $statuses = [
            'pending' => [
                'label' => 'Menunggu Pembayaran',
                'class' => 'badge-warning',
                'color' => '#ffc107'
            ],
            'confirmed' => [
                'label' => 'Dikonfirmasi',
                'class' => 'badge-info',
                'color' => '#17a2b8'
            ],
            'processing' => [
                'label' => 'Diproses',
                'class' => 'badge-primary',
                'color' => '#007bff'
            ],
            'shipped' => [
                'label' => 'Dikirim',
                'class' => 'badge-secondary',
                'color' => '#6c757d'
            ],
            'delivered' => [
                'label' => 'Diterima',
                'class' => 'badge-success',
                'color' => '#28a745'
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'badge-danger',
                'color' => '#dc3545'
            ],
            'refunded' => [
                'label' => 'Dikembalikan',
                'class' => 'badge-dark',
                'color' => '#343a40'
            ]
        ];

        return $statuses[$status] ?? [
            'label' => ucfirst($status),
            'class' => 'badge-secondary',
            'color' => '#6c757d'
        ];
    }
}

if (!function_exists('format_payment_status')) {
    /**
     * Format payment status with styling
     *
     * @param string $status
     * @return array
     */
    function format_payment_status($status)
    {
        $statuses = [
            'pending' => [
                'label' => 'Menunggu Pembayaran',
                'class' => 'badge-warning',
                'color' => '#ffc107'
            ],
            'paid' => [
                'label' => 'Dibayar',
                'class' => 'badge-success',
                'color' => '#28a745'
            ],
            'failed' => [
                'label' => 'Gagal',
                'class' => 'badge-danger',
                'color' => '#dc3545'
            ],
            'refunded' => [
                'label' => 'Dikembalikan',
                'class' => 'badge-info',
                'color' => '#17a2b8'
            ],
            'partial' => [
                'label' => 'Sebagian',
                'class' => 'badge-secondary',
                'color' => '#6c757d'
            ]
        ];

        return $statuses[$status] ?? [
            'label' => ucfirst($status),
            'class' => 'badge-secondary',
            'color' => '#6c757d'
        ];
    }
}
