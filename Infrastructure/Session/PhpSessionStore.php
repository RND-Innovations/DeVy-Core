<?php

namespace DeVy\Core\Infrastructure\Session;

use DeVy\Core\Contracts\Session\SessionInterface;

class PhpSessionStore implements SessionInterface
{
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $_SESSION = [];
    }

    public function regenerate(bool $destroy = false): void
    {
        session_regenerate_id($destroy);
    }

    public function all(): array
    {
        return $_SESSION;
    }
}