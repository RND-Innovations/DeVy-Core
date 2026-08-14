<?php

namespace DeVy\Core\Contracts\Auth;

interface PasswordHasherInterface
{
    /**
     * Hash password
     */
    public function hash(
        string $password
    ): string;

    /**
     * Verify password
     */
    public function verify(
        string $password,
        string $hash
    ): bool;

    /**
     * Needs rehash
     */
    public function needsRehash(
        string $hash
    ): bool;
}