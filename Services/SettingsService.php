<?php

declare(strict_types=1);

namespace DeVy\Core\Services;

use DeVy\Core\Schema\SchemaDefaults;
use DeVy\Core\Persistence\JsonStore;
use DeVy\Core\Contracts\Store\StoreInterface;

class SettingsService implements StoreInterface
{
    protected JsonStore $store;

    protected HookManager $hooks;

    public function __construct(
        PathService $paths,
        HookManager $hooks
    ) {
        $this->hooks = $hooks;

        $this->store = new JsonStore(
            $paths->content('settings.json')
        );

        $schema = $hooks->dispatch(
            'settings.schema',
            []
        );

        (new SchemaDefaults)->apply(
            $this->store,
            $hooks->dispatch(
                'settings.schema',
                []
            )
        );
    }

    /**
     * ----------------------------------------
     * Get
     * ----------------------------------------
     */
    public function get(
        string $key = '',
        mixed $default = null
    ): mixed {
        return $this->store->get(
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * Set
     * ----------------------------------------
     */
    public function set(
        string $key,
        mixed $value
    ): void {
        $this->store->set(
            $key,
            $value
        );
    }

    /**
     * ----------------------------------------
     * Has
     * ----------------------------------------
     */
    public function has(
        string $key
    ): bool {
        return $this->store->has($key);
    }

    /**
     * ----------------------------------------
     * All
     * ----------------------------------------
     */
    public function all(): array
    {
        return $this->store->all();
    }

    /**
     * ----------------------------------------
     * Save
     * ----------------------------------------
     */
    public function save(): bool
    {
        return $this->store->save();
    }


}