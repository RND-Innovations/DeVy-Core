<?php

namespace DeVy\Core\Support;

class LocaleRegistry
{
    protected static array $locales = [

        'en_US' => [
            'code' => 'en_US',
            'language' => 'en',
            'country' => 'US',
            'name' => 'English (United States)',
            'native_name' => 'English (United States)',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'date_format' => 'm/d/Y',
            'time_format' => 'h:i A',
            'first_day_of_week' => 'sunday',
            'rtl' => false,
            'active' => true,
        ],

        'en_GB' => [
            'code' => 'en_GB',
            'language' => 'en',
            'country' => 'GB',
            'name' => 'English (United Kingdom)',
            'native_name' => 'English (United Kingdom)',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'en_AU' => [
            'code' => 'en_AU',
            'language' => 'en',
            'country' => 'AU',
            'name' => 'English (Australia)',
            'native_name' => 'English (Australia)',
            'currency' => 'AUD',
            'timezone' => 'Australia/Sydney',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'vi_VN' => [
            'code' => 'vi_VN',
            'language' => 'vi',
            'country' => 'VN',
            'name' => 'Vietnamese (Vietnam)',
            'native_name' => 'Tiếng Việt',
            'currency' => 'VND',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'si_LK' => [
            'code' => 'si_LK',
            'language' => 'si',
            'country' => 'LK',
            'name' => 'Sinhala (Sri Lanka)',
            'native_name' => 'සිංහල',
            'currency' => 'LKR',
            'timezone' => 'Asia/Colombo',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'ta_LK' => [
            'code' => 'ta_LK',
            'language' => 'ta',
            'country' => 'LK',
            'name' => 'Tamil (Sri Lanka)',
            'native_name' => 'தமிழ்',
            'currency' => 'LKR',
            'timezone' => 'Asia/Colombo',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'fr_FR' => [
            'code' => 'fr_FR',
            'language' => 'fr',
            'country' => 'FR',
            'name' => 'French (France)',
            'native_name' => 'Français',
            'currency' => 'EUR',
            'timezone' => 'Europe/Paris',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'de_DE' => [
            'code' => 'de_DE',
            'language' => 'de',
            'country' => 'DE',
            'name' => 'German (Germany)',
            'native_name' => 'Deutsch',
            'currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
            'date_format' => 'd.m.Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'es_ES' => [
            'code' => 'es_ES',
            'language' => 'es',
            'country' => 'ES',
            'name' => 'Spanish (Spain)',
            'native_name' => 'Español',
            'currency' => 'EUR',
            'timezone' => 'Europe/Madrid',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'pt_PT' => [
            'code' => 'pt_PT',
            'language' => 'pt',
            'country' => 'PT',
            'name' => 'Portuguese (Portugal)',
            'native_name' => 'Português',
            'currency' => 'EUR',
            'timezone' => 'Europe/Lisbon',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'ja_JP' => [
            'code' => 'ja_JP',
            'language' => 'ja',
            'country' => 'JP',
            'name' => 'Japanese (Japan)',
            'native_name' => '日本語',
            'currency' => 'JPY',
            'timezone' => 'Asia/Tokyo',
            'date_format' => 'Y/m/d',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'ko_KR' => [
            'code' => 'ko_KR',
            'language' => 'ko',
            'country' => 'KR',
            'name' => 'Korean (South Korea)',
            'native_name' => '한국어',
            'currency' => 'KRW',
            'timezone' => 'Asia/Seoul',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'zh_CN' => [
            'code' => 'zh_CN',
            'language' => 'zh',
            'country' => 'CN',
            'name' => 'Chinese (China)',
            'native_name' => '中文',
            'currency' => 'CNY',
            'timezone' => 'Asia/Shanghai',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'first_day_of_week' => 'monday',
            'rtl' => false,
            'active' => true,
        ],

        'ar_SA' => [
            'code' => 'ar_SA',
            'language' => 'ar',
            'country' => 'SA',
            'name' => 'Arabic (Saudi Arabia)',
            'native_name' => 'العربية',
            'currency' => 'SAR',
            'timezone' => 'Asia/Riyadh',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'first_day_of_week' => 'sunday',
            'rtl' => true,
            'active' => true,
        ],

    ];

    public static function all(): array
    {
        return static::$locales;
    }

    public static function get(string $locale): ?array
    {
        return static::$locales[$locale] ?? null;
    }

    public static function name(string $locale): ?string
    {
        return static::get($locale)['name'] ?? null;
    }

    public static function nativeName(string $locale): ?string
    {
        return static::get($locale)['native_name'] ?? null;
    }

    public static function language(string $locale): ?string
    {
        return static::get($locale)['language'] ?? null;
    }

    public static function country(string $locale): ?string
    {
        return static::get($locale)['country'] ?? null;
    }

    public static function currency(string $locale): ?string
    {
        return static::get($locale)['currency'] ?? null;
    }

    public static function timezone(string $locale): ?string
    {
        return static::get($locale)['timezone'] ?? null;
    }

    public static function dateFormat(string $locale): ?string
    {
        return static::get($locale)['date_format'] ?? null;
    }

    public static function timeFormat(string $locale): ?string
    {
        return static::get($locale)['time_format'] ?? null;
    }

    public static function firstDayOfWeek(string $locale): ?string
    {
        return static::get($locale)['first_day_of_week'] ?? null;
    }

    public static function isRtl(string $locale): bool
    {
        return static::get($locale)['rtl'] ?? false;
    }

    public static function active(): array
    {
        return array_filter(
            static::$locales,
            fn ($locale) => $locale['active'] === true
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$locales as $code => $locale) {
            $options[$code] = $locale['native_name'];
        }

        return $options;
    }
}