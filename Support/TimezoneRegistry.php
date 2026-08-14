<?php

namespace DeVy\Core\Support;

class TimezoneRegistry
{
    protected static array $timezones = [

        'UTC' => [
            'id' => 'UTC',
            'name' => 'Coordinated Universal Time',
            'country' => 'Global',
            'region' => 'Global',
            'offset' => '+00:00',
            'dst' => false,
            'locale' => 'en_US',
            'flag' => 'un',
            'emoji' => '🌍',
        ],

        'America/New_York' => [
            'id' => 'America/New_York',
            'name' => 'Eastern Time',
            'country' => 'United States',
            'region' => 'North America',
            'offset' => '-05:00',
            'dst' => true,
            'locale' => 'en_US',
            'flag' => 'us',
            'emoji' => '🇺🇸',
        ],

        'America/Los_Angeles' => [
            'id' => 'America/Los_Angeles',
            'name' => 'Pacific Time',
            'country' => 'United States',
            'region' => 'North America',
            'offset' => '-08:00',
            'dst' => true,
            'locale' => 'en_US',
            'flag' => 'us',
            'emoji' => '🇺🇸',
        ],

        'Europe/London' => [
            'id' => 'Europe/London',
            'name' => 'United Kingdom',
            'country' => 'United Kingdom',
            'region' => 'Europe',
            'offset' => '+00:00',
            'dst' => true,
            'locale' => 'en_GB',
            'flag' => 'gb',
            'emoji' => '🇬🇧',
        ],

        'Europe/Paris' => [
            'id' => 'Europe/Paris',
            'name' => 'Central European Time',
            'country' => 'France',
            'region' => 'Europe',
            'offset' => '+01:00',
            'dst' => true,
            'locale' => 'fr_FR',
            'flag' => 'fr',
            'emoji' => '🇫🇷',
        ],

        'Asia/Ho_Chi_Minh' => [
            'id' => 'Asia/Ho_Chi_Minh',
            'name' => 'Vietnam Time',
            'country' => 'Vietnam',
            'region' => 'Asia',
            'offset' => '+07:00',
            'dst' => false,
            'locale' => 'vi_VN',
            'flag' => 'vn',
            'emoji' => '🇻🇳',
        ],

        'Asia/Colombo' => [
            'id' => 'Asia/Colombo',
            'name' => 'Sri Lanka Time',
            'country' => 'Sri Lanka',
            'region' => 'Asia',
            'offset' => '+05:30',
            'dst' => false,
            'locale' => 'si_LK',
            'flag' => 'lk',
            'emoji' => '🇱🇰',
        ],

        'Asia/Tokyo' => [
            'id' => 'Asia/Tokyo',
            'name' => 'Japan Standard Time',
            'country' => 'Japan',
            'region' => 'Asia',
            'offset' => '+09:00',
            'dst' => false,
            'locale' => 'ja_JP',
            'flag' => 'jp',
            'emoji' => '🇯🇵',
        ],

    ];

    /**
     * Get all timezones
     */
    public static function all(): array
    {
        return static::$timezones;
    }

    /**
     * Get timezone
     */
    public static function get(string $timezone): ?array
    {
        return static::$timezones[$timezone] ?? null;
    }

    /**
     * Get timezone name
     */
    public static function name(string $timezone): ?string
    {
        return static::get($timezone)['name'] ?? null;
    }

    /**
     * Get locale
     */
    public static function locale(string $timezone): ?string
    {
        return static::get($timezone)['locale'] ?? null;
    }

    /**
     * Get country
     */
    public static function country(string $timezone): ?string
    {
        return static::get($timezone)['country'] ?? null;
    }

    /**
     * Get offset
     */
    public static function offset(string $timezone): ?string
    {
        return static::get($timezone)['offset'] ?? null;
    }

    /**
     * Has DST
     */
    public static function dst(string $timezone): bool
    {
        return static::get($timezone)['dst'] ?? false;
    }

    /**
     * Get flag
     */
    public static function flag(string $timezone): ?string
    {
        return static::get($timezone)['flag'] ?? null;
    }

    /**
     * Get emoji
     */
    public static function emoji(string $timezone): ?string
    {
        return static::get($timezone)['emoji'] ?? null;
    }

    /**
     * Select options
     */
    public static function options(): array
    {
        $options = [];

        foreach (static::$timezones as $id => $timezone) {
            $options[$id] = sprintf(
                '%s (%s)',
                $timezone['name'],
                $id
            );
        }

        return $options;
    }

    /**
     * Format date in timezone
     */
    public static function format(
        string|\DateTimeInterface $date,
        string $timezone,
        string $format = 'Y-m-d H:i:s'
    ): string {

        if (!$date instanceof \DateTimeInterface) {
            $date = new \DateTime($date);
        }

        $date = (clone $date)
            ->setTimezone(new \DateTimeZone($timezone));

        return $date->format($format);
    }
}