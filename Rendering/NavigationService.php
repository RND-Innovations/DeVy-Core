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

        $role = $this->getUserRole();

        foreach ($nav as &$items) {

            $items = $this->filterItems(
                $items,
                $role
            );

            usort(
                $items,
                fn($a, $b) =>
                    ($a['order'] ?? 0)
                    <=>
                    ($b['order'] ?? 0)
            );
        }

        return $nav;
    }

    private function filterItems(
        array $items,
        string $role
    ): array {

        $filtered = [];

        foreach ($items as $item) {

            /*
             * Check this item's permission.
             */
            if (
                isset($item['permission']) &&
                !$this->permissions->has(
                    $role,
                    $item['permission']
                )
            ) {
                continue;
            }

            /*
             * Recursively filter children.
             */
            if (isset($item['children'])) {

                $item['children'] = $this->filterItems(
                    $item['children'],
                    $role
                );

                /*
                 * If this item has children but all of them
                 * were removed, decide whether the parent
                 * should also disappear.
                 */
                if (
                    empty($item['children']) &&
                    !isset($item['url'])
                ) {
                    continue;
                }
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    private function getUserRole(): string
    {
        return $this->hooks->dispatch(
            'auth.role',
            'guest'
        );
    }
}