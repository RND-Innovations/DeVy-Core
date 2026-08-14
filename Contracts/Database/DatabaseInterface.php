<?php

declare(strict_types=1);

namespace DeVy\Core\Contracts\Database;

interface DatabaseInterface
{
    /**
     * Check whether the database is available.
     */
    public function isAvailable(): bool;

    /**
     * Get the underlying database connection.
     *
     * The concrete implementation decides what this returns.
     */
    public function connection(): mixed;
}