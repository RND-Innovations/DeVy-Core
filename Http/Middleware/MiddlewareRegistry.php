<?php

namespace DeVy\Core\Http\Middleware;

use RuntimeException;

class MiddlewareRegistry
{
    protected array $aliases = [];

    protected array $global = [];

    public function register(string $alias, string $class): void
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Middleware class {$class} not found.");
        }

        if (!is_subclass_of($class, MiddlewareInterface::class)) {
            throw new RuntimeException(
                "{$class} must implement MiddlewareInterface"
            );
        }

        $this->aliases[$alias] = $class;
    }

    public function resolve(string $alias): ?string
    {
        return $this->aliases[$alias] ?? null;
    }

    public function pushGlobal(string $alias): void
    {
        if (!in_array($alias, $this->global, true)) {
            $this->global[] = $alias;
        }
    }

    public function prependGlobal(string $alias): void
    {
        if (!in_array($alias, $this->global, true)) {
            array_unshift($this->global, $alias);
        }
    }

    public function global(): array
    {
        return $this->global;
    }
}