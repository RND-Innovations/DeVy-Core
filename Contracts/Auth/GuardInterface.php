<?php

namespace DeVy\Core\Contracts\Auth;

interface GuardInterface
{
    /**
     * Attempt login
     */
    public function attempt(
        string $identity,
        string $password
    ): bool;

    /**
     * Is authenticated
     */
    public function check(): bool;

    /**
     * Current user
     */
    public function user(): ?UserInterface;

    /**
     * Current user ID
     */
    public function id(): int|string|null;

    /**
     * Logout user
     */
    public function logout(): void;
}