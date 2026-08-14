<?php

namespace DeVy\Core\Support;

class UserRoleRegistry
{
    protected static array $roles = [

        'super_admin' => [
            'key' => 'super_admin',
            'name' => 'Super Administrator',
            'description' => 'Full system access',
            'level' => 100,
            'color' => 'red',
            'icon' => 'shield-check',
            'system' => true,
            'active' => true,
        ],

        'admin' => [
            'key' => 'admin',
            'name' => 'Administrator',
            'description' => 'Administrative access',
            'level' => 90,
            'color' => 'orange',
            'icon' => 'shield',
            'system' => true,
            'active' => true,
        ],

        'manager' => [
            'key' => 'manager',
            'name' => 'Manager',
            'description' => 'Manage content and users',
            'level' => 70,
            'color' => 'purple',
            'icon' => 'briefcase',
            'system' => true,
            'active' => true,
        ],

        'editor' => [
            'key' => 'editor',
            'name' => 'Editor',
            'description' => 'Manage and publish content',
            'level' => 50,
            'color' => 'blue',
            'icon' => 'pencil-square',
            'system' => true,
            'active' => true,
        ],

        'author' => [
            'key' => 'author',
            'name' => 'Author',
            'description' => 'Create and edit own content',
            'level' => 40,
            'color' => 'green',
            'icon' => 'document-text',
            'system' => true,
            'active' => true,
        ],

        'contributor' => [
            'key' => 'contributor',
            'name' => 'Contributor',
            'description' => 'Submit content for review',
            'level' => 30,
            'color' => 'cyan',
            'icon' => 'plus-circle',
            'system' => true,
            'active' => true,
        ],

        'member' => [
            'key' => 'member',
            'name' => 'Member',
            'description' => 'Registered user',
            'level' => 20,
            'color' => 'gray',
            'icon' => 'user',
            'system' => true,
            'active' => true,
        ],

        'guest' => [
            'key' => 'guest',
            'name' => 'Guest',
            'description' => 'Public visitor',
            'level' => 0,
            'color' => 'slate',
            'icon' => 'eye',
            'system' => true,
            'active' => true,
        ],

    ];

    public static function all(): array
    {
        return static::$roles;
    }

    public static function get(string $role): ?array
    {
        return static::$roles[$role] ?? null;
    }

    public static function exists(string $role): bool
    {
        return isset(static::$roles[$role]);
    }

    public static function name(string $role): ?string
    {
        return static::get($role)['name'] ?? null;
    }

    public static function description(string $role): ?string
    {
        return static::get($role)['description'] ?? null;
    }

    public static function level(string $role): int
    {
        return static::get($role)['level'] ?? 0;
    }

    public static function color(string $role): ?string
    {
        return static::get($role)['color'] ?? null;
    }

    public static function icon(string $role): ?string
    {
        return static::get($role)['icon'] ?? null;
    }

    public static function isSystem(string $role): bool
    {
        return static::get($role)['system'] ?? false;
    }

    public static function isActive(string $role): bool
    {
        return static::get($role)['active'] ?? false;
    }

    public static function active(): array
    {
        return array_filter(
            static::$roles,
            fn ($role) => $role['active'] === true
        );
    }

    public static function hasLevel(
        string $role,
        int $requiredLevel
    ): bool {
        return static::level($role) >= $requiredLevel;
    }

    public static function higherThan(
        string $roleA,
        string $roleB
    ): bool {
        return static::level($roleA)
            > static::level($roleB);
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$roles as $key => $role) {
            $options[$key] = $role['name'];
        }

        return $options;
    }

    public static function default(): string
    {
        return 'member';
    }
}