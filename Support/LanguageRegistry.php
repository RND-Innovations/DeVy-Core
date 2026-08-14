<?php

namespace DeVy\Core\Support;

class LanguageRegistry
{
    protected static array $languages = [

        'en' => [
            'code' => 'en',
            'iso3' => 'eng',
            'name' => 'English',
            'native_name' => 'English',
            'locale' => 'en_US',
            'rtl' => false,
            'flag' => 'gb',
            'emoji' => '🇬🇧',
            'active' => true,
        ],

        'vi' => [
            'code' => 'vi',
            'iso3' => 'vie',
            'name' => 'Vietnamese',
            'native_name' => 'Tiếng Việt',
            'locale' => 'vi_VN',
            'rtl' => false,
            'flag' => 'vn',
            'emoji' => '🇻🇳',
            'active' => true,
        ],

        'si' => [
            'code' => 'si',
            'iso3' => 'sin',
            'name' => 'Sinhala',
            'native_name' => 'සිංහල',
            'locale' => 'si_LK',
            'rtl' => false,
            'flag' => 'lk',
            'emoji' => '🇱🇰',
            'active' => true,
        ],

        'ta' => [
            'code' => 'ta',
            'iso3' => 'tam',
            'name' => 'Tamil',
            'native_name' => 'தமிழ்',
            'locale' => 'ta_LK',
            'rtl' => false,
            'flag' => 'lk',
            'emoji' => '🇱🇰',
            'active' => true,
        ],

        'fr' => [
            'code' => 'fr',
            'iso3' => 'fra',
            'name' => 'French',
            'native_name' => 'Français',
            'locale' => 'fr_FR',
            'rtl' => false,
            'flag' => 'fr',
            'emoji' => '🇫🇷',
            'active' => true,
        ],

        'de' => [
            'code' => 'de',
            'iso3' => 'deu',
            'name' => 'German',
            'native_name' => 'Deutsch',
            'locale' => 'de_DE',
            'rtl' => false,
            'flag' => 'de',
            'emoji' => '🇩🇪',
            'active' => true,
        ],

        'es' => [
            'code' => 'es',
            'iso3' => 'spa',
            'name' => 'Spanish',
            'native_name' => 'Español',
            'locale' => 'es_ES',
            'rtl' => false,
            'flag' => 'es',
            'emoji' => '🇪🇸',
            'active' => true,
        ],

        'it' => [
            'code' => 'it',
            'iso3' => 'ita',
            'name' => 'Italian',
            'native_name' => 'Italiano',
            'locale' => 'it_IT',
            'rtl' => false,
            'flag' => 'it',
            'emoji' => '🇮🇹',
            'active' => true,
        ],

        'pt' => [
            'code' => 'pt',
            'iso3' => 'por',
            'name' => 'Portuguese',
            'native_name' => 'Português',
            'locale' => 'pt_PT',
            'rtl' => false,
            'flag' => 'pt',
            'emoji' => '🇵🇹',
            'active' => true,
        ],

        'ru' => [
            'code' => 'ru',
            'iso3' => 'rus',
            'name' => 'Russian',
            'native_name' => 'Русский',
            'locale' => 'ru_RU',
            'rtl' => false,
            'flag' => 'ru',
            'emoji' => '🇷🇺',
            'active' => true,
        ],

        'zh' => [
            'code' => 'zh',
            'iso3' => 'zho',
            'name' => 'Chinese',
            'native_name' => '中文',
            'locale' => 'zh_CN',
            'rtl' => false,
            'flag' => 'cn',
            'emoji' => '🇨🇳',
            'active' => true,
        ],

        'ja' => [
            'code' => 'ja',
            'iso3' => 'jpn',
            'name' => 'Japanese',
            'native_name' => '日本語',
            'locale' => 'ja_JP',
            'rtl' => false,
            'flag' => 'jp',
            'emoji' => '🇯🇵',
            'active' => true,
        ],

        'ko' => [
            'code' => 'ko',
            'iso3' => 'kor',
            'name' => 'Korean',
            'native_name' => '한국어',
            'locale' => 'ko_KR',
            'rtl' => false,
            'flag' => 'kr',
            'emoji' => '🇰🇷',
            'active' => true,
        ],

        'ar' => [
            'code' => 'ar',
            'iso3' => 'ara',
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'locale' => 'ar_SA',
            'rtl' => true,
            'flag' => 'sa',
            'emoji' => '🇸🇦',
            'active' => true,
        ],

    ];

    public static function all(): array
    {
        return static::$languages;
    }

    public static function get(string $code): ?array
    {
        $code = strtolower($code);

        return static::$languages[$code] ?? null;
    }

    public static function name(string $code): ?string
    {
        return static::get($code)['name'] ?? null;
    }

    public static function nativeName(string $code): ?string
    {
        return static::get($code)['native_name'] ?? null;
    }

    public static function locale(string $code): ?string
    {
        return static::get($code)['locale'] ?? null;
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
            static::$languages,
            fn ($language) => $language['active'] === true
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$languages as $code => $language) {
            $options[$code] = sprintf(
                '%s %s',
                $language['emoji'],
                $language['native_name']
            );
        }

        return $options;
    }
}