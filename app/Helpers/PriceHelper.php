// File: app/Helpers/PriceHelper.php

<?php

if (!function_exists('format_currency')) {
    /**
     * Format currency amount
     */
    function format_currency($amountCents, $showSymbol = true, $showDecimals = null)
    {
        $config = config('tokosaya.currency');

        // Convert cents to main currency unit
        $amount = $amountCents / 100;

        // Determine decimal places
        $decimals = $showDecimals !== null ? $showDecimals : $config['decimal_places'];

        // Format number
        $formatted = number_format(
            $amount,
            $decimals,
            $config['decimal_separator'],
            $config['thousands_separator']
        );

        // Add currency symbol
        if ($showSymbol) {
            $symbol = $config['symbol'];
            $formatted = $config['symbol_position'] === 'before'
                ? $symbol . ' ' . $formatted
                : $formatted . ' ' . $symbol;
        }

        return $formatted;
    }
}

if (!function_exists('parse_currency')) {
    /**
     * Parse currency string to cents
     */
    function parse_currency($currencyString)
    {
        $config = config('tokosaya.currency');

        // Remove currency symbol and spaces
        $cleaned = str_replace([
            $config['symbol'],
            ' ',
            $config['thousands_separator']
        ], ['', '', ''], $currencyString);

        // Replace decimal separator with dot
        $cleaned = str_replace($config['decimal_separator'], '.', $cleaned);

        // Convert to float and then to cents
        $amount = (float) $cleaned;
        return (int) round($amount * 100);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     */
    function currency_symbol()
    {
        return config('tokosaya.currency.symbol');
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get currency code
     */
    function currency_code()
    {
        return config('tokosaya.currency.code');
    }
}
