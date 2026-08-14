<?php

namespace DeVy\Core\Contracts\Session;

interface SessionInterface
{
    public function start(): void;

    public function has(string $key): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value): void;

    public function forget(string $key): void;

    public function flush(): void;

    public function regenerate(bool $destroy = false): void;

    public function all(): array;
}