<?php

declare(strict_types=1);

namespace DeVy\Core\Contracts\Database;

interface DatabaseManagerInterface
{
    /**
     * Determine whether database infrastructure is available.
     */
    public function available(): bool;

    /**
     * Get the active database connection.
     *
     * @throws \RuntimeException
     */
    public function connection(): DatabaseConnectionInterface;
}