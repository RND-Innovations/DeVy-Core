<?php

declare(strict_types=1);

namespace DeVy\Core\Services;

use DeVy\Core\Support\PermissionRegistry;

final class PermissionService
{
    /**
     * ---------------------------------------------------------
     * Register Role
     * ---------------------------------------------------------
     */
    public function addRole(
        string $key,
        string $name
    ): void {
        PermissionRegistry::addRole(
            $key,
            $name
        );
    }

    /**
     * ---------------------------------------------------------
     * Register Roles
     * ---------------------------------------------------------
     */
    public function addRoles(
        array $roles
    ): void {
        PermissionRegistry::addRoles(
            $roles
        );
    }

    /**
     * ---------------------------------------------------------
     * Register Permission
     * ---------------------------------------------------------
     */
    public function addPermission(
        string $key,
        string $name,
        string $module,
        string $description = '',
        array $roles = [],
        bool $active = true
    ): void {
        PermissionRegistry::addPermission(
            key: $key,
            name: $name,
            module: $module,
            description: $description,
            roles: $roles,
            active: $active
        );
    }

    /**
     * ---------------------------------------------------------
     * Register Permissions
     * ---------------------------------------------------------
     */
    public function addPermissions(
        array $permissions
    ): void {
        PermissionRegistry::addPermissions(
            $permissions
        );
    }

    /**
     * ---------------------------------------------------------
     * Check Permission
     * ---------------------------------------------------------
     */
    public function has(
        string $role,
        string $permission
    ): bool {
        return PermissionRegistry::has(
            $role,
            $permission
        );
    }

    /**
     * ---------------------------------------------------------
     * Permissions For Role
     * ---------------------------------------------------------
     */
    public function rolePermissions(
        string $role
    ): array {
        return PermissionRegistry::forRole(
            $role
        );
    }

    /**
     * ---------------------------------------------------------
     * Available Roles
     * ---------------------------------------------------------
     */
    public function roles(): array
    {
        return PermissionRegistry::roles();
    }

    /**
     * ---------------------------------------------------------
     * Role Exists
     * ---------------------------------------------------------
     */
    public function hasRole(
        string $role
    ): bool {
        return PermissionRegistry::hasRole(
            $role
        );
    }

    /**
     * ---------------------------------------------------------
     * Role Name
     * ---------------------------------------------------------
     */
    public function roleName(
        string $role
    ): ?string {
        return PermissionRegistry::roleName(
            $role
        );
    }

    /**
     * ---------------------------------------------------------
     * All Permissions
     * ---------------------------------------------------------
     */
    public function all(): array
    {
        return PermissionRegistry::all();
    }

    /**
     * ---------------------------------------------------------
     * Active Permissions
     * ---------------------------------------------------------
     */
    public function active(): array
    {
        return PermissionRegistry::active();
    }

    /**
     * ---------------------------------------------------------
     * Permission Exists
     * ---------------------------------------------------------
     */
    public function hasPermission(
        string $permission
    ): bool {
        return PermissionRegistry::exists(
            $permission
        );
    }

    /**
     * ---------------------------------------------------------
     * Permission Definition
     * ---------------------------------------------------------
     */
    public function get(
        string $permission
    ): ?array {
        return PermissionRegistry::get(
            $permission
        );
    }

    /**
     * ---------------------------------------------------------
     * Permissions By Module
     * ---------------------------------------------------------
     */
    public function byModule(
        string $module
    ): array {
        return PermissionRegistry::byModule(
            $module
        );
    }

    /**
     * ---------------------------------------------------------
     * Registered Modules
     * ---------------------------------------------------------
     */
    public function modules(): array
    {
        return PermissionRegistry::modules();
    }

    /**
     * ---------------------------------------------------------
     * Permission Options
     * ---------------------------------------------------------
     */
    public function options(): array
    {
        return PermissionRegistry::options();
    }
}