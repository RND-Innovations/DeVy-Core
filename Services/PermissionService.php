<?php

declare(strict_types=1);

namespace DeVy\Core\Services;

use DeVy\Core\Persistence\JsonStore;

class PermissionService
{
    protected array $roles = [];

    protected HookManager $hooks;

    protected JsonStore $store;

    public function __construct(
        PathService $paths,
        HookManager $hooks
    ) {
        $this->hooks = $hooks;

        // Resolve roles file via PathService
        $this->store = new JsonStore(
            $paths->roles()
        );

        $data = $this->store->all();

        // Ensure valid structure
        $roles = is_array($data) ? $data : [];

        // Allow plugins to modify roles
        $this->roles = $this->hooks->dispatch(
            'permissions.register',
            $roles
        );
    }

    public function rolePermissions(string $role): array
    {
        return $this->roles[$role] ?? [];
    }

    public function has(
        string $role,
        string $permission
    ): bool {
        $permissions = $this->rolePermissions($role);

        // Wildcard access
        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array(
            $permission,
            $permissions,
            true
        );
    }

    public function all(): array
    {
        return $this->roles;
    }

    public function save(): bool
    {
        $data = $this->hooks->dispatch(
            'permissions.saving',
            $this->roles
        );

        $result = $this->store->save($data);

        $this->hooks->dispatch(
            'permissions.saved',
            $data
        );

        return $result;
    }
}