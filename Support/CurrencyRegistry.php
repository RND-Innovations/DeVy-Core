<?php

namespace DeVy\Core\Support;

class CurrencyRegistry
{
    protected static array $currencies = [

        'USD' => [
            'code' => 'USD',
            'numeric_code' => '840',
            'name' => 'US Dollar',
            'plural_name' => 'US Dollars',
            'symbol' => '$',
            'native_symbol' => '$',
            'country' => 'United States',
            'region' => 'North America',
            'locale' => 'en_US',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
            'exchange_rate' => null,
            'active' => true,
            'rtl' => false,
            'flag' => 'us',
            'emoji' => '🇺🇸',
        ],

        'EUR' => [
            'code' => 'EUR',
            'numeric_code' => '978',
            'name' => 'Euro',
            'plural_name' => 'Euros',
            'symbol' => '€',
            'native_symbol' => '€',
            'country' => 'European Union',
            'region' => 'Europe',
            'locale' => 'en_EU',
            'decimals' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'before',
            'exchange_rate' => null,
            'active' => true,
            'rtl' => false,
            'flag' => 'eu',
            'emoji' => '🇪🇺',
        ],

        'GBP' => [
            'code' => 'GBP',
            'numeric_code' => '826',
            'name' => 'British Pound',
            'plural_name' => 'British Pounds',
            'symbol' => '£',
            'native_symbol' => '£',
            'country' => 'United Kingdom',
            'region' => 'Europe',
            'locale' => 'en_GB',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
            'exchange_rate' => null,
            'active' => true,
            'rtl' => false,
            'flag' => 'gb',
            'emoji' => '🇬🇧',
        ],

        'VND' => [
            'code' => 'VND',
            'numeric_code' => '704',
            'name' => 'Vietnamese Dong',
            'plural_name' => 'Vietnamese Dong',
            'symbol' => '₫',
            'native_symbol' => '₫',
            'country' => 'Vietnam',
            'region' => 'Asia',
            'locale' => 'vi_VN',
            'decimals' => 0,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'after',
            'exchange_rate' => null,
            'active' => true,
            'rtl' => false,
            'flag' => 'vn',
            'emoji' => '🇻🇳',
        ],

        'LKR' => [
            'code' => 'LKR',
            'numeric_code' => '144',
            'name' => 'Sri Lankan Rupee',
            'plural_name' => 'Sri Lankan Rupees',
            'symbol' => 'Rs',
            'native_symbol' => 'රු',
            'country' => 'Sri Lanka',
            'region' => 'Asia',
            'locale' => 'si_LK',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
            'exchange_rate' => null,
            'active' => true,
            'rtl' => false,
            'flag' => 'lk',
            'emoji' => '🇱🇰',
        ],

    ];

    /**
     * Get all currencies
     */
    public static function all(): array
    {
        return static::$currencies;
    }

    /**
     * Get currency by code
     */
    public static function get(string $code): ?array
    {
        $code = strtoupper($code);

        return static::$currencies[$code] ?? null;
    }

    /**
     * Get currency name
     */
    public static function name(string $code): ?string
    {
        return static::get($code)['name'] ?? null;
    }

    /**
     * Get currency symbol
     */
    public static function symbol(string $code): ?string
    {
        return static::get($code)['symbol'] ?? null;
    }

    /**
     * Select options
     */
    public static function options(): array
    {
        $options = [];

        foreach (static::$currencies as $code => $currency) {
            $options[$code] = sprintf(
                '%s (%s)',
                $currency['name'],
                $code
            );
        }

        return $options;
    }

    public static function locale(string $code): ?string
    {
        return static::get($code)['locale'] ?? null;
    }

    public static function country(string $code): ?string
    {
        return static::get($code)['country'] ?? null;
    }

    public static function decimals(string $code): int
    {
        return static::get($code)['decimals'] ?? 2;
    }

    public static function flag(string $code): ?string
    {
        return static::get($code)['flag'] ?? null;
    }

    public static function emoji(string $code): ?string
    {
        return static::get($code)['emoji'] ?? null;
    }


    public static function format(float $amount, string $currency): string
    {
        $currency = static::get($currency);

        if (!$currency) {
            return (string) $amount;
        }

        $value = number_format(
            $amount,
            $currency['decimals'],
            $currency['decimal_separator'],
            $currency['thousands_separator']
        );

        return $currency['symbol_position'] === 'before'
            ? $currency['symbol'] . $value
            : $value . ' ' . $currency['symbol'];
    }


}