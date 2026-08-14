<?php

declare(strict_types=1);

namespace DeVy\Core\Persistence;

use RuntimeException;

use DeVy\Core\Contracts\Store\StoreInterface;

class JsonStore implements StoreInterface
{
    protected string $path;

    protected array $data = [];

    public function __construct(string $path)
    {
        $this->path = $path;

        $this->reload();
    }

    /**
     * ----------------------------------------
     * Reload
     * ----------------------------------------
     */
    public function reload(): void
    {
        if (!is_file($this->path)) {
            $this->data = [];
            return;
        }

        $json = file_get_contents($this->path);

        $decoded = json_decode($json ?: '', true);

        if (!is_array($decoded)) {
            throw new RuntimeException(
                "Invalid JSON file: {$this->path}"
            );
        }

        $this->data = $decoded;
    }

    /**
     * ----------------------------------------
     * Save
     * ----------------------------------------
     */
    public function save(?array $data = null): bool
    {
        if ($data !== null) {
            $this->data = $data;
        }

        return file_put_contents(
            $this->path,
            json_encode(
                $this->data,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            ),
            LOCK_EX
        ) !== false;
    }

    /**
     * ----------------------------------------
     * All
     * ----------------------------------------
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * ----------------------------------------
     * Replace All
     * ----------------------------------------
     */
    public function replace(array $data): void
    {
        $this->data = $data;
    }

    /**
     * ----------------------------------------
     * Get
     * ----------------------------------------
     */
    public function get(
        string $key = '',
        mixed $default = null
    ): mixed {

        if ($key === '') {
            return $this->data;
        }

        $value = $this->data;

        foreach (explode('.', $key) as $segment) {

            if (
                !is_array($value) ||
                !array_key_exists($segment, $value)
            ) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * ----------------------------------------
     * Has
     * ----------------------------------------
     */
    public function has(string $key): bool
    {
        $value = $this->data;

        foreach (explode('.', $key) as $segment) {

            if (
                !is_array($value) ||
                !array_key_exists($segment, $value)
            ) {
                return false;
            }

            $value = $value[$segment];
        }

        return true;
    }

    /**
     * ----------------------------------------
     * Set
     * ----------------------------------------
     */
    public function set(
        string $key,
        mixed $value
    ): void {

        $segments = explode('.', $key);

        $current = &$this->data;

        foreach ($segments as $segment) {

            if (
                !isset($current[$segment]) ||
                !is_array($current[$segment])
            ) {
                $current[$segment] = [];
            }

            $current = &$current[$segment];
        }

        $current = $value;
    }

    /**
     * ----------------------------------------
     * Remove
     * ----------------------------------------
     */
    public function remove(string $key): void
    {
        $segments = explode('.', $key);

        $last = array_pop($segments);

        $current = &$this->data;

        foreach ($segments as $segment) {

            if (
                !isset($current[$segment]) ||
                !is_array($current[$segment])
            ) {
                return;
            }

            $current = &$current[$segment];
        }

        unset($current[$last]);
    }

    /**
     * ----------------------------------------
     * Path
     * ----------------------------------------
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * ----------------------------------------
     * Merge Defaults
     * ----------------------------------------
     */
    public function mergeDefaults(array $schema): void
    {
        foreach ($schema as $section) {

            foreach ($section['fields'] ?? [] as $field) {

                if (!isset($field['path'])) {
                    continue;
                }

                if (!$this->has($field['path'])) {

                    $this->set(
                        $field['path'],
                        $field['default'] ?? null
                    );
                }
            }
        }
    }

}