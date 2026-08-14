<?php

namespace DeVy\Core\Support;

class ContentStatusRegistry
{
    protected static array $statuses = [

        'draft' => [
            'key' => 'draft',
            'name' => 'Draft',
            'description' => 'Work in progress',
            'color' => 'gray',
            'icon' => 'file',
            'public' => false,
            'editable' => true,
            'active' => true,
        ],

        'pending' => [
            'key' => 'pending',
            'name' => 'Pending Review',
            'description' => 'Awaiting approval',
            'color' => 'yellow',
            'icon' => 'clock',
            'public' => false,
            'editable' => true,
            'active' => true,
        ],

        'scheduled' => [
            'key' => 'scheduled',
            'name' => 'Scheduled',
            'description' => 'Will publish automatically',
            'color' => 'blue',
            'icon' => 'calendar',
            'public' => false,
            'editable' => true,
            'active' => true,
        ],

        'published' => [
            'key' => 'published',
            'name' => 'Published',
            'description' => 'Visible to the public',
            'color' => 'green',
            'icon' => 'check-circle',
            'public' => true,
            'editable' => true,
            'active' => true,
        ],

        'private' => [
            'key' => 'private',
            'name' => 'Private',
            'description' => 'Visible only to authorized users',
            'color' => 'purple',
            'icon' => 'lock',
            'public' => false,
            'editable' => true,
            'active' => true,
        ],

        'archived' => [
            'key' => 'archived',
            'name' => 'Archived',
            'description' => 'Stored but not active',
            'color' => 'slate',
            'icon' => 'archive-box',
            'public' => false,
            'editable' => true,
            'active' => true,
        ],

        'trashed' => [
            'key' => 'trashed',
            'name' => 'Trash',
            'description' => 'Soft deleted',
            'color' => 'red',
            'icon' => 'trash',
            'public' => false,
            'editable' => false,
            'active' => true,
        ],

    ];

    /**
     * Get all statuses
     */
    public static function all(): array
    {
        return static::$statuses;
    }

    /**
     * Get status
     */
    public static function get(string $status): ?array
    {
        return static::$statuses[$status] ?? null;
    }

    /**
     * Exists
     */
    public static function exists(string $status): bool
    {
        return isset(static::$statuses[$status]);
    }

    /**
     * Name
     */
    public static function name(string $status): ?string
    {
        return static::get($status)['name'] ?? null;
    }

    /**
     * Description
     */
    public static function description(string $status): ?string
    {
        return static::get($status)['description'] ?? null;
    }

    /**
     * Color
     */
    public static function color(string $status): ?string
    {
        return static::get($status)['color'] ?? null;
    }

    /**
     * Icon
     */
    public static function icon(string $status): ?string
    {
        return static::get($status)['icon'] ?? null;
    }

    /**
     * Is public
     */
    public static function isPublic(string $status): bool
    {
        return static::get($status)['public'] ?? false;
    }

    /**
     * Is editable
     */
    public static function isEditable(string $status): bool
    {
        return static::get($status)['editable'] ?? false;
    }

    /**
     * Active statuses
     */
    public static function active(): array
    {
        return array_filter(
            static::$statuses,
            fn ($status) => $status['active'] === true
        );
    }

    /**
     * Public statuses
     */
    public static function public(): array
    {
        return array_filter(
            static::$statuses,
            fn ($status) => $status['public'] === true
        );
    }

    /**
     * Select options
     */
    public static function options(): array
    {
        $options = [];

        foreach (static::$statuses as $key => $status) {
            $options[$key] = $status['name'];
        }

        return $options;
    }

    /**
     * Common workflow statuses
     */
    public static function workflow(): array
    {
        return [
            'draft',
            'pending',
            'scheduled',
            'published',
        ];
    }
}