<?php

namespace DeVy\Core\Support;

class PermissionRegistry
{
    protected static array $permissions = [

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        'dashboard.view' => [
            'key' => 'dashboard.view',
            'name' => 'View Dashboard',
            'module' => 'core',
            'description' => 'Access the dashboard',
            'active' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */

        'pages.view' => [
            'key' => 'pages.view',
            'name' => 'View Pages',
            'module' => 'pages',
            'description' => 'View pages',
            'active' => true,
        ],

        'pages.create' => [
            'key' => 'pages.create',
            'name' => 'Create Pages',
            'module' => 'pages',
            'description' => 'Create pages',
            'active' => true,
        ],

        'pages.edit' => [
            'key' => 'pages.edit',
            'name' => 'Edit Pages',
            'module' => 'pages',
            'description' => 'Edit pages',
            'active' => true,
        ],

        'pages.delete' => [
            'key' => 'pages.delete',
            'name' => 'Delete Pages',
            'module' => 'pages',
            'description' => 'Delete pages',
            'active' => true,
        ],

        'pages.publish' => [
            'key' => 'pages.publish',
            'name' => 'Publish Pages',
            'module' => 'pages',
            'description' => 'Publish pages',
            'active' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Media
        |--------------------------------------------------------------------------
        */

        'media.view' => [
            'key' => 'media.view',
            'name' => 'View Media',
            'module' => 'media',
            'description' => 'View media',
            'active' => true,
        ],

        'media.upload' => [
            'key' => 'media.upload',
            'name' => 'Upload Media',
            'module' => 'media',
            'description' => 'Upload files',
            'active' => true,
        ],

        'media.edit' => [
            'key' => 'media.edit',
            'name' => 'Edit Media',
            'module' => 'media',
            'description' => 'Edit media',
            'active' => true,
        ],

        'media.delete' => [
            'key' => 'media.delete',
            'name' => 'Delete Media',
            'module' => 'media',
            'description' => 'Delete media',
            'active' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        'users.view' => [
            'key' => 'users.view',
            'name' => 'View Users',
            'module' => 'users',
            'description' => 'View users',
            'active' => true,
        ],

        'users.create' => [
            'key' => 'users.create',
            'name' => 'Create Users',
            'module' => 'users',
            'description' => 'Create users',
            'active' => true,
        ],

        'users.edit' => [
            'key' => 'users.edit',
            'name' => 'Edit Users',
            'module' => 'users',
            'description' => 'Edit users',
            'active' => true,
        ],

        'users.delete' => [
            'key' => 'users.delete',
            'name' => 'Delete Users',
            'module' => 'users',
            'description' => 'Delete users',
            'active' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        'settings.view' => [
            'key' => 'settings.view',
            'name' => 'View Settings',
            'module' => 'settings',
            'description' => 'View settings',
            'active' => true,
        ],

        'settings.edit' => [
            'key' => 'settings.edit',
            'name' => 'Edit Settings',
            'module' => 'settings',
            'description' => 'Modify settings',
            'active' => true,
        ],

    ];

    public static function all(): array
    {
        return static::$permissions;
    }

    public static function get(string $permission): ?array
    {
        return static::$permissions[$permission] ?? null;
    }

    public static function exists(string $permission): bool
    {
        return isset(static::$permissions[$permission]);
    }

    public static function name(string $permission): ?string
    {
        return static::get($permission)['name'] ?? null;
    }

    public static function description(string $permission): ?string
    {
        return static::get($permission)['description'] ?? null;
    }

    public static function module(string $permission): ?string
    {
        return static::get($permission)['module'] ?? null;
    }

    public static function active(): array
    {
        return array_filter(
            static::$permissions,
            fn ($permission) => $permission['active'] === true
        );
    }

    public static function byModule(
        string $module
    ): array {
        return array_filter(
            static::$permissions,
            fn ($permission)
                => $permission['module'] === $module
        );
    }

    public static function modules(): array
    {
        return array_unique(
            array_column(
                static::$permissions,
                'module'
            )
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$permissions as $key => $permission) {
            $options[$key] = $permission['name'];
        }

        return $options;
    }
}