<?php

declare(strict_types=1);

namespace DeVy\Core\Contracts\Database;

interface DatabaseConnectionInterface
{
    /**
     * Execute a SQL statement.
     */
    public function execute(
        string $sql,
        array $params = []
    ): mixed;

    /**
     * Fetch a single row.
     */
    public function fetch(
        string $sql,
        array $params = []
    ): ?array;

    /**
     * Fetch multiple rows.
     */
    public function fetchAll(
        string $sql,
        array $params = []
    ): array;

    /**
     * Execute a statement and return the affected row count.
     */
    public function affectedRows(): int;

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): bool;

    /**
     * Commit the current transaction.
     */
    public function commit(): bool;

    /**
     * Roll back the current transaction.
     */
    public function rollBack(): bool;
}