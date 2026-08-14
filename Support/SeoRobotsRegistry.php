<?php

namespace DeVy\Core\Support;

class SeoRobotsRegistry
{
    protected static array $directives = [

        'index,follow' => [
            'key' => 'index,follow',
            'name' => 'Index, Follow',
            'description' => 'Allow indexing and link crawling',
            'index' => true,
            'follow' => true,
            'recommended' => true,
        ],

        'index,nofollow' => [
            'key' => 'index,nofollow',
            'name' => 'Index, No Follow',
            'description' => 'Allow indexing but do not follow links',
            'index' => true,
            'follow' => false,
            'recommended' => false,
        ],

        'noindex,follow' => [
            'key' => 'noindex,follow',
            'name' => 'No Index, Follow',
            'description' => 'Do not index page but follow links',
            'index' => false,
            'follow' => true,
            'recommended' => false,
        ],

        'noindex,nofollow' => [
            'key' => 'noindex,nofollow',
            'name' => 'No Index, No Follow',
            'description' => 'Do not index page and do not follow links',
            'index' => false,
            'follow' => false,
            'recommended' => false,
        ],

    ];

    public static function all(): array
    {
        return static::$directives;
    }

    public static function get(string $key): ?array
    {
        return static::$directives[$key] ?? null;
    }

    public static function exists(string $key): bool
    {
        return isset(static::$directives[$key]);
    }

    public static function name(string $key): ?string
    {
        return static::get($key)['name'] ?? null;
    }

    public static function description(string $key): ?string
    {
        return static::get($key)['description'] ?? null;
    }

    public static function shouldIndex(string $key): bool
    {
        return static::get($key)['index'] ?? false;
    }

    public static function shouldFollow(string $key): bool
    {
        return static::get($key)['follow'] ?? false;
    }

    public static function recommended(): string
    {
        return 'index,follow';
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$directives as $key => $directive) {
            $options[$key] = $directive['name'];
        }

        return $options;
    }
}