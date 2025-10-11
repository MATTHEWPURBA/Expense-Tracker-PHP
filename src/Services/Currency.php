<?php
/**
 * Currency Service
 * 
 * Handles currency management and formatting
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

class Currency
{
    /**
     * Supported currencies with their details
     */
    private static $currencies = [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before', // before or after
            'countries' => ['United States', 'Ecuador', 'El Salvador']
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Germany', 'France', 'Spain', 'Italy', 'Netherlands']
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'code' => 'GBP',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['United Kingdom']
        ],
        'JPY' => [
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'code' => 'JPY',
            'decimal_places' => 0,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Japan']
        ],
        'CNY' => [
            'name' => 'Chinese Yuan',
            'symbol' => '¥',
            'code' => 'CNY',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['China']
        ],
        'INR' => [
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'code' => 'INR',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['India']
        ],
        'CAD' => [
            'name' => 'Canadian Dollar',
            'symbol' => 'CA$',
            'code' => 'CAD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Canada']
        ],
        'AUD' => [
            'name' => 'Australian Dollar',
            'symbol' => 'A$',
            'code' => 'AUD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Australia']
        ],
        'CHF' => [
            'name' => 'Swiss Franc',
            'symbol' => 'CHF',
            'code' => 'CHF',
            'decimal_places' => 2,
            'thousands_separator' => "'",
            'decimal_separator' => '.',
            'symbol_position' => 'after',
            'countries' => ['Switzerland']
        ],
        'BRL' => [
            'name' => 'Brazilian Real',
            'symbol' => 'R$',
            'code' => 'BRL',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'before',
            'countries' => ['Brazil']
        ],
        'MXN' => [
            'name' => 'Mexican Peso',
            'symbol' => 'MX$',
            'code' => 'MXN',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Mexico']
        ],
        'KRW' => [
            'name' => 'South Korean Won',
            'symbol' => '₩',
            'code' => 'KRW',
            'decimal_places' => 0,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['South Korea']
        ],
        'RUB' => [
            'name' => 'Russian Ruble',
            'symbol' => '₽',
            'code' => 'RUB',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Russia']
        ],
        'SGD' => [
            'name' => 'Singapore Dollar',
            'symbol' => 'S$',
            'code' => 'SGD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Singapore']
        ],
        'HKD' => [
            'name' => 'Hong Kong Dollar',
            'symbol' => 'HK$',
            'code' => 'HKD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Hong Kong']
        ],
        'SEK' => [
            'name' => 'Swedish Krona',
            'symbol' => 'kr',
            'code' => 'SEK',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Sweden']
        ],
        'NOK' => [
            'name' => 'Norwegian Krone',
            'symbol' => 'kr',
            'code' => 'NOK',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Norway']
        ],
        'NZD' => [
            'name' => 'New Zealand Dollar',
            'symbol' => 'NZ$',
            'code' => 'NZD',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['New Zealand']
        ],
        'ZAR' => [
            'name' => 'South African Rand',
            'symbol' => 'R',
            'code' => 'ZAR',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'symbol_position' => 'before',
            'countries' => ['South Africa']
        ],
        'TRY' => [
            'name' => 'Turkish Lira',
            'symbol' => '₺',
            'code' => 'TRY',
            'decimal_places' => 2,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Turkey']
        ],
        'AED' => [
            'name' => 'UAE Dirham',
            'symbol' => 'د.إ',
            'code' => 'AED',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['United Arab Emirates']
        ],
        'SAR' => [
            'name' => 'Saudi Riyal',
            'symbol' => '﷼',
            'code' => 'SAR',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Saudi Arabia']
        ],
        'THB' => [
            'name' => 'Thai Baht',
            'symbol' => '฿',
            'code' => 'THB',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Thailand']
        ],
        'MYR' => [
            'name' => 'Malaysian Ringgit',
            'symbol' => 'RM',
            'code' => 'MYR',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Malaysia']
        ],
        'IDR' => [
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'code' => 'IDR',
            'decimal_places' => 0,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'before',
            'countries' => ['Indonesia']
        ],
        'PHP' => [
            'name' => 'Philippine Peso',
            'symbol' => '₱',
            'code' => 'PHP',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Philippines']
        ],
        'VND' => [
            'name' => 'Vietnamese Dong',
            'symbol' => '₫',
            'code' => 'VND',
            'decimal_places' => 0,
            'thousands_separator' => '.',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Vietnam']
        ],
        'PLN' => [
            'name' => 'Polish Zloty',
            'symbol' => 'zł',
            'code' => 'PLN',
            'decimal_places' => 2,
            'thousands_separator' => ' ',
            'decimal_separator' => ',',
            'symbol_position' => 'after',
            'countries' => ['Poland']
        ],
        'EGP' => [
            'name' => 'Egyptian Pound',
            'symbol' => 'E£',
            'code' => 'EGP',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Egypt']
        ],
        'NGN' => [
            'name' => 'Nigerian Naira',
            'symbol' => '₦',
            'code' => 'NGN',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'symbol_position' => 'before',
            'countries' => ['Nigeria']
        ]
    ];

    /**
     * Get all supported currencies
     */
    public static function getAll(): array
    {
        return self::$currencies;
    }

    /**
     * Get currency details by code
     */
    public static function get(string $code): ?array
    {
        $code = strtoupper($code);
        return self::$currencies[$code] ?? null;
    }

    /**
     * Format amount according to currency
     */
    public static function format(float $amount, string $currencyCode = 'USD'): string
    {
        $currency = self::get($currencyCode);
        
        if (!$currency) {
            // Fallback to USD format
            return '$' . number_format($amount, 2);
        }

        // Format the number
        $formatted = number_format(
            $amount,
            $currency['decimal_places'],
            $currency['decimal_separator'],
            $currency['thousands_separator']
        );

        // Add currency symbol
        if ($currency['symbol_position'] === 'before') {
            return $currency['symbol'] . $formatted;
        } else {
            return $formatted . ' ' . $currency['symbol'];
        }
    }

    /**
     * Get currency symbol
     */
    public static function getSymbol(string $currencyCode = 'USD'): string
    {
        $currency = self::get($currencyCode);
        return $currency['symbol'] ?? '$';
    }

    /**
     * Check if currency code is valid
     */
    public static function isValid(string $code): bool
    {
        return isset(self::$currencies[strtoupper($code)]);
    }

    /**
     * Get currency code by country (simple lookup)
     */
    public static function getByCountry(string $country): ?string
    {
        foreach (self::$currencies as $code => $details) {
            if (in_array($country, $details['countries'])) {
                return $code;
            }
        }
        return null;
    }

    /**
     * Detect user's currency based on various factors
     */
    public static function detectUserCurrency(): string
    {
        // Try to detect from browser locale
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            
            $localeMap = [
                'en' => 'USD',
                'de' => 'EUR',
                'fr' => 'EUR',
                'es' => 'EUR',
                'it' => 'EUR',
                'ja' => 'JPY',
                'zh' => 'CNY',
                'hi' => 'INR',
                'pt' => 'BRL',
                'ru' => 'RUB',
                'ko' => 'KRW',
                'ar' => 'SAR',
                'th' => 'THB',
                'vi' => 'VND',
                'id' => 'IDR',
                'ms' => 'MYR',
                'tr' => 'TRY',
                'pl' => 'PLN',
                'sv' => 'SEK',
                'no' => 'NOK'
            ];
            
            if (isset($localeMap[$locale])) {
                return $localeMap[$locale];
            }
        }
        
        // Default to USD
        return 'USD';
    }
}

