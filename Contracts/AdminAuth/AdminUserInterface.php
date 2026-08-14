<?php

namespace DeVy\Core\Contracts\AdminAuth;

use DeVy\Core\Contracts\Auth\UserInterface;

interface AdminUserInterface extends UserInterface
{
    public function getUsername(): string;

    public function getRole(): string;

    public function hasTwoFactorEnabled(): bool;

    public function getTwoFactorSecret(): ?string;

    public function getRecoveryCodes(): array;
}