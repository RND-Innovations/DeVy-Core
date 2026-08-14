<?php

namespace DeVy\Core\Rendering;

use DeVy\Core\Services\HookManager;
use DeVy\Core\Services\PermissionService;

class NavigationService
{
    public function __construct(
        private HookManager $hooks,
        private PermissionService $permissions
    ) {}

    public function build(): array
    {
        $nav = $this->hooks->collect('navigation.build', [
            'admin' => [],
            'site' => []
        ]);

        foreach ($nav as &$items) {

            $items = array_values(array_filter($items, function ($item) {

                if (!isset($item['permission'])) {
                    return true;
                }

                return $this->permissions->has(
                    $this->getUserRole(),
                    $item['permission']
                );
            }));

            usort($items, fn($a, $b) =>
                ($a['order'] ?? 0) <=> ($b['order'] ?? 0)
            );
        }

        return $nav;
    }

    private function getUserRole(): string
    {
        return app(HookManager::class)->dispatch('auth.role', 'guest');
    }
}