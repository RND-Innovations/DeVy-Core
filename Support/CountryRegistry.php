<?php

namespace DeVy\Core\Support;

class CountryRegistry
{
    protected static array $countries = [

        'VN' => [
            'code' => 'VN',
            'iso3' => 'VNM',
            'numeric_code' => '704',
            'name' => 'Vietnam',
            'official_name' => 'Socialist Republic of Vietnam',
            'capital' => 'Hanoi',
            'region' => 'Asia',
            'subregion' => 'South-Eastern Asia',
            'currency' => 'VND',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'locale' => 'vi_VN',
            'phone_code' => '+84',
            'flag' => 'vn',
            'emoji' => '🇻🇳',
            'rtl' => false,
            'active' => true,
        ],

        'LK' => [
            'code' => 'LK',
            'iso3' => 'LKA',
            'numeric_code' => '144',
            'name' => 'Sri Lanka',
            'official_name' => 'Democratic Socialist Republic of Sri Lanka',
            'capital' => 'Sri Jayawardenepura Kotte',
            'region' => 'Asia',
            'subregion' => 'Southern Asia',
            'currency' => 'LKR',
            'timezone' => 'Asia/Colombo',
            'locale' => 'si_LK',
            'phone_code' => '+94',
            'flag' => 'lk',
            'emoji' => '🇱🇰',
            'rtl' => false,
            'active' => true,
        ],

        'US' => [
            'code' => 'US',
            'iso3' => 'USA',
            'numeric_code' => '840',
            'name' => 'United States',
            'official_name' => 'United States of America',
            'capital' => 'Washington, D.C.',
            'region' => 'North America',
            'subregion' => 'Northern America',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'locale' => 'en_US',
            'phone_code' => '+1',
            'flag' => 'us',
            'emoji' => '🇺🇸',
            'rtl' => false,
            'active' => true,
        ],

        'GB' => [
            'code' => 'GB',
            'iso3' => 'GBR',
            'numeric_code' => '826',
            'name' => 'United Kingdom',
            'official_name' => 'United Kingdom of Great Britain and Northern Ireland',
            'capital' => 'London',
            'region' => 'Europe',
            'subregion' => 'Northern Europe',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'locale' => 'en_GB',
            'phone_code' => '+44',
            'flag' => 'gb',
            'emoji' => '🇬🇧',
            'rtl' => false,
            'active' => true,
        ],

        'CA' => [
            'code' => 'CA',
            'iso3' => 'CAN',
            'numeric_code' => '124',
            'name' => 'Canada',
            'official_name' => 'Canada',
            'capital' => 'Ottawa',
            'region' => 'North America',
            'subregion' => 'Northern America',
            'currency' => 'CAD',
            'timezone' => 'America/Toronto',
            'locale' => 'en_CA',
            'phone_code' => '+1',
            'flag' => 'ca',
            'emoji' => '🇨🇦',
            'rtl' => false,
            'active' => true,
        ],

        'AU' => [
            'code' => 'AU',
            'iso3' => 'AUS',
            'numeric_code' => '036',
            'name' => 'Australia',
            'official_name' => 'Commonwealth of Australia',
            'capital' => 'Canberra',
            'region' => 'Oceania',
            'subregion' => 'Australia and New Zealand',
            'currency' => 'AUD',
            'timezone' => 'Australia/Sydney',
            'locale' => 'en_AU',
            'phone_code' => '+61',
            'flag' => 'au',
            'emoji' => '🇦🇺',
            'rtl' => false,
            'active' => true,
        ],

        'IN' => [
            'code' => 'IN',
            'iso3' => 'IND',
            'numeric_code' => '356',
            'name' => 'India',
            'official_name' => 'Republic of India',
            'capital' => 'New Delhi',
            'region' => 'Asia',
            'subregion' => 'Southern Asia',
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'locale' => 'en_IN',
            'phone_code' => '+91',
            'flag' => 'in',
            'emoji' => '🇮🇳',
            'rtl' => false,
            'active' => true,
        ],

        'SG' => [
            'code' => 'SG',
            'iso3' => 'SGP',
            'numeric_code' => '702',
            'name' => 'Singapore',
            'official_name' => 'Republic of Singapore',
            'capital' => 'Singapore',
            'region' => 'Asia',
            'subregion' => 'South-Eastern Asia',
            'currency' => 'SGD',
            'timezone' => 'Asia/Singapore',
            'locale' => 'en_SG',
            'phone_code' => '+65',
            'flag' => 'sg',
            'emoji' => '🇸🇬',
            'rtl' => false,
            'active' => true,
        ],

        'JP' => [
            'code' => 'JP',
            'iso3' => 'JPN',
            'numeric_code' => '392',
            'name' => 'Japan',
            'official_name' => 'Japan',
            'capital' => 'Tokyo',
            'region' => 'Asia',
            'subregion' => 'Eastern Asia',
            'currency' => 'JPY',
            'timezone' => 'Asia/Tokyo',
            'locale' => 'ja_JP',
            'phone_code' => '+81',
            'flag' => 'jp',
            'emoji' => '🇯🇵',
            'rtl' => false,
            'active' => true,
        ],

        'DE' => [
            'code' => 'DE',
            'iso3' => 'DEU',
            'numeric_code' => '276',
            'name' => 'Germany',
            'official_name' => 'Federal Republic of Germany',
            'capital' => 'Berlin',
            'region' => 'Europe',
            'subregion' => 'Western Europe',
            'currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
            'locale' => 'de_DE',
            'phone_code' => '+49',
            'flag' => 'de',
            'emoji' => '🇩🇪',
            'rtl' => false,
            'active' => true,
        ],

    ];

    public static function all(): array
    {
        return static::$countries;
    }

    public static function get(string $code): ?array
    {
        $code = strtoupper($code);

        return static::$countries[$code] ?? null;
    }

    public static function name(string $code): ?string
    {
        return static::get($code)['name'] ?? null;
    }

    public static function officialName(string $code): ?string
    {
        return static::get($code)['official_name'] ?? null;
    }

    public static function capital(string $code): ?string
    {
        return static::get($code)['capital'] ?? null;
    }

    public static function region(string $code): ?string
    {
        return static::get($code)['region'] ?? null;
    }

    public static function currency(string $code): ?string
    {
        return static::get($code)['currency'] ?? null;
    }

    public static function timezone(string $code): ?string
    {
        return static::get($code)['timezone'] ?? null;
    }

    public static function locale(string $code): ?string
    {
        return static::get($code)['locale'] ?? null;
    }

    public static function phoneCode(string $code): ?string
    {
        return static::get($code)['phone_code'] ?? null;
    }

    public static function flag(string $code): ?string
    {
        return static::get($code)['flag'] ?? null;
    }

    public static function emoji(string $code): ?string
    {
        return static::get($code)['emoji'] ?? null;
    }

    public static function isRtl(string $code): bool
    {
        return static::get($code)['rtl'] ?? false;
    }

    public static function isActive(string $code): bool
    {
        return static::get($code)['active'] ?? false;
    }

    public static function active(): array
    {
        return array_filter(
            static::$countries,
            fn ($country) => $country['active'] === true
        );
    }

    public static function byRegion(string $region): array
    {
        return array_filter(
            static::$countries,
            fn ($country) => strcasecmp($country['region'], $region) === 0
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$countries as $code => $country) {
            $options[$code] = sprintf(
                '%s %s',
                $country['emoji'],
                $country['name']
            );
        }

        return $options;
    }
}