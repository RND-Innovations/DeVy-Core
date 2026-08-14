<?php

namespace DeVy\Core\Contracts\AdminAuth;

interface TrustedDeviceInterface
{
    public function getId(): string;

    public function getUsername(): string;

    public function getTokenHash(): string;

    public function getCreatedAt(): int;

    public function getExpiresAt(): int;

    public function getLastUsedAt(): int;

    public function getUserAgent(): ?string;

    public function getIpAddress(): ?string;

    public function setLastUsedAt(int $timestamp): void;

    public function toArray(): array;
}