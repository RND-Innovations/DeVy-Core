<?php

namespace DeVy\Core\Contracts\UserAuth;

use DeVy\Core\Contracts\Auth\UserInterface;

interface PublicUserInterface extends UserInterface
{
    public function getEmail(): string;

    public function getName(): string;

    public function isVerified(): bool;

    public function getMeta(
        string $key,
        mixed $default = null
    ): mixed;

    public function getCreatedAt(): int;

}