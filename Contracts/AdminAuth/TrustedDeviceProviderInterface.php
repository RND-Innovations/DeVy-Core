<?php

namespace DeVy\Core\Contracts\AdminAuth;

interface TrustedDeviceProviderInterface
{
    public function create(array $data): TrustedDeviceInterface;

    public function save(TrustedDeviceInterface $device): void;

    public function find(string $id): ?TrustedDeviceInterface;

    public function findByHash(string $hash): ?TrustedDeviceInterface;

    public function all(): array;

    public function delete(string $id): void;

    public function deleteByUsername(string $username): void;

    public function purgeExpired(): void;
}