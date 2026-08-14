<?php

namespace DeVy\Core\Plugins;

class PluginContext
{
    protected array $data = [];

    public function set(array $data): void
    {
        $this->data = $data;
    }

    public function merge(array $data): void
    {
        $this->data = array_replace_recursive(
            $this->data,
            $data
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function clear(): void
    {
        $this->data = [];
    }
}