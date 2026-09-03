<?php

declare(strict_types=1);

namespace DeVy\Core\Support;

use InvalidArgumentException;

final class PermissionRegistry
{
    /**
     * ---------------------------------------------------------
     * Default Core Roles
     * ---------------------------------------------------------
     *
     * Modules may add additional roles at runtime.
     */
    protected static array $roles = [

        'superadmin' => 'Super Administrator'

    ];

    /**
     * ---------------------------------------------------------
     * Runtime Permissions
     * ---------------------------------------------------------
     *
     * Core starts with its own permissions only.
     *
     * Modules add their permissions at runtime.
     */
    protected static array $permissions = [];

    /**
     * ---------------------------------------------------------
     * Roles
     * ---------------------------------------------------------
     */

    public static function addRole(
        string $key,
        string $name
    ): void {
        $key = trim($key);
        $name = trim($name);

        if ($key === '') {
            throw new InvalidArgumentException(
                'Role key cannot be empty.'
            );
        }

        if ($name === '') {
            throw new InvalidArgumentException(
                'Role name cannot be empty.'
            );
        }

        static::$roles[$key] = $name;
    }

    public static function addRoles(
        array $roles
    ): void {
        foreach ($roles as $key => $name) {

            static::addRole(
                (string) $key,
                (string) $name
            );
        }
    }

    public static function hasRole(
        string $role
    ): bool {
        return array_key_exists(
            $role,
            static::$roles
        );
    }

    public static function roleName(
        string $role
    ): ?string {
        return static::$roles[$role] ?? null;
    }

    public static function roles(): array
    {
        return static::$roles;
    }

    /**
     * ---------------------------------------------------------
     * Permissions
     * ---------------------------------------------------------
     */

    public static function addPermission(
        string $key,
        string $name,
        string $module,
        string $description = '',
        array $roles = [],
        bool $active = true
    ): void {
        $key = trim($key);
        $name = trim($name);
        $module = trim($module);
        $description = trim($description);

        if ($key === '') {
            throw new InvalidArgumentException(
                'Permission key cannot be empty.'
            );
        }

        if ($name === '') {
            throw new InvalidArgumentException(
                "Permission '{$key}' must have a name."
            );
        }

        if ($module === '') {
            throw new InvalidArgumentException(
                "Permission '{$key}' must have a module."
            );
        }

        /*
         * Make sure every explicitly assigned role exists.
         */
        foreach ($roles as $role) {

            $role = trim((string) $role);

            if ($role === '') {
                continue;
            }

            if (!static::hasRole($role)) {
                throw new InvalidArgumentException(
                    "Role '{$role}' is not registered."
                );
            }
        }

        static::$permissions[$key] = [
            'key' => $key,
            'name' => $name,
            'module' => $module,
            'description' => $description,
            'active' => $active,
            'roles' => array_values(
                array_unique($roles)
            ),
        ];
    }

    /**
     * ---------------------------------------------------------
     * Bulk Permission Registration
     * ---------------------------------------------------------
     */

    public static function addPermissions(
        array $permissions
    ): void {
        foreach ($permissions as $permission) {

            static::addPermission(
                key: $permission['key'],
                name: $permission['name'],
                module: $permission['module'],
                description: $permission['description'] ?? '',
                roles: $permission['roles'] ?? [],
                active: $permission['active'] ?? true
            );
        }
    }

    /**
     * ---------------------------------------------------------
     * Permission Lookup
     * ---------------------------------------------------------
     */

    public static function all(): array
    {
        return static::$permissions;
    }

    public static function get(
        string $permission
    ): ?array {
        return static::$permissions[$permission] ?? null;
    }

    public static function exists(
        string $permission
    ): bool {
        return isset(
            static::$permissions[$permission]
        );
    }

    public static function name(
        string $permission
    ): ?string {
        return static::get($permission)['name'] ?? null;
    }

    public static function description(
        string $permission
    ): ?string {
        return static::get($permission)['description'] ?? null;
    }

    public static function module(
        string $permission
    ): ?string {
        return static::get($permission)['module'] ?? null;
    }

    public static function active(): array
    {
        return array_filter(
            static::$permissions,
            fn (array $permission): bool =>
                $permission['active'] === true
        );
    }

    public static function byModule(
        string $module
    ): array {
        return array_filter(
            static::$permissions,
            fn (array $permission): bool =>
                $permission['module'] === $module
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

        foreach (
            static::$permissions
            as $key => $permission
        ) {
            $options[$key] = $permission['name'];
        }

        return $options;
    }

    /**
     * ---------------------------------------------------------
     * Permission Check
     * ---------------------------------------------------------
     */

    public static function has(
        string $role,
        string $permission
    ): bool {

        /*
         * Superadmin has unrestricted access.
         */
        if ($role === 'superadmin') {
            return true;
        }

        $definition = static::get($permission);

        if (!$definition) {
            return false;
        }

        /*
         * Disabled permissions cannot be used.
         */
        if (($definition['active'] ?? false) !== true) {
            return false;
        }

        /*
         * No roles means every authenticated admin user
         * may use this permission.
         */
        if (empty($definition['roles'])) {
            return true;
        }

        return in_array(
            $role,
            $definition['roles'] ?? [],
            true
        );
    }

    /**
     * ---------------------------------------------------------
     * Permissions For Role
     * ---------------------------------------------------------
     */

    public static function forRole(
        string $role
    ): array {

        if ($role === 'superadmin') {
            return ['*'];
        }

        $permissions = [];

        foreach (
            static::$permissions
            as $key => $definition
        ) {

            if (
                ($definition['active'] ?? false) !== true
            ) {
                continue;
            }

            if (
                in_array(
                    $role,
                    $definition['roles'] ?? [],
                    true
                )
            ) {
                $permissions[] = $key;
            }
        }

        return $permissions;
    }
}