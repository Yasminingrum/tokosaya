<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Format price from cents to Rupiah
     *
     * @param int $cents
     * @param bool $showCurrency
     * @return string
     */
    public static function format($cents, $showCurrency = true)
    {
        // Handle null or invalid input
        if (!is_numeric($cents)) {
            $cents = 0;
        }

        // Convert cents to rupiah
        $rupiah = $cents / 100;

        // Format with Indonesian number format
        $formatted = number_format($rupiah, 0, ',', '.');

        // Add currency symbol if requested
        if ($showCurrency) {
            return 'Rp ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Format price without currency symbol
     *
     * @param int $cents
     * @return string
     */
    public static function formatWithoutSymbol($cents)
    {
        return self::format($cents, false);
    }

    /**
     * Convert rupiah to cents
     *
     * @param float $rupiah
     * @return int
     */
    public static function toCents($rupiah)
    {
        return (int) round($rupiah * 100);
    }

    /**
     * Format price with decimal places
     *
     * @param int $cents
     * @param int $decimals
     * @param bool $showCurrency
     * @return string
     */
    public static function formatWithDecimals($cents, $decimals = 2, $showCurrency = true)
    {
        if (!is_numeric($cents)) {
            $cents = 0;
        }

        $rupiah = $cents / 100;
        $formatted = number_format($rupiah, $decimals, ',', '.');

        if ($showCurrency) {
            return 'Rp ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Parse currency string to cents
     *
     * @param string $currencyString
     * @return int
     */
    public static function parse($currencyString)
    {
        // Remove currency symbol and spaces
        $cleaned = str_replace(['Rp', ' ', '.'], ['', '', ''], $currencyString);

        // Replace comma with dot for decimal
        $cleaned = str_replace(',', '.', $cleaned);

        // Convert to float and then to cents
        $amount = (float) $cleaned;
        return (int) round($amount * 100);
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public static function symbol()
    {
        return 'Rp';
    }

    /**
     * Format price for input fields (no currency, with decimals)
     *
     * @param int $cents
     * @return string
     */
    public static function formatForInput($cents)
    {
        if (!is_numeric($cents)) {
            return '0';
        }

        return number_format($cents / 100, 0, ',', '.');
    }
}
