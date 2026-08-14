<?php

namespace DeVy\Core\Contracts\Auth;

interface UserProviderInterface
{
    /**
     * Find user by ID
     */
    public function findById(
        int|string $id
    ): ?UserInterface;

    /**
     * Find user by login identity
     * (username, email, phone, etc.)
     */
    public function findByIdentity(
        string $identity
    ): ?UserInterface;

    /**
     * Create new user
     */
    public function create(
        array $data
    ): UserInterface;

    /**
     * Update existing user
     */
    public function update(
        int|string $id,
        array $data
    ): bool;

    /**
     * Delete user
     */
    public function delete(
        int|string $id
    ): bool;

    /**
     * Get all users
     */
    public function all(): array;
}