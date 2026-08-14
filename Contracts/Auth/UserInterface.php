<?php

namespace DeVy\Core\Contracts\Auth;

interface UserInterface
{
    /**
     * Unique user identifier
     */
    public function getId(): int|string|null;

    /**
     * Password hash
     */
    public function getPasswordHash(): string;

    /**
     * Full raw user data
     */
    public function toArray(): array;
}