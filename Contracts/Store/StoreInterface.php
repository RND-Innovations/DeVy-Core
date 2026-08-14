<?php

declare(strict_types=1);

namespace DeVy\Core\Contracts\Store;

interface StoreInterface
{
    /**
     * Get a value.
     */
    public function get(
        string $key = '',
        mixed $default = null
    ): mixed;

    /**
     * Set a value.
     */
    public function set(
        string $key,
        mixed $value
    ): void;

    /**
     * Check if a key exists.
     */
    public function has(
        string $key
    ): bool;

    /**
     * Return all values.
     */
    public function all(): array;

    /**
     * Persist changes.
     */
    public function save(): bool;
}