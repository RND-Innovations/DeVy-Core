<?php

namespace DeVy\Core\Assets;

class AssetRegistry
{
    private array $definitions = [];
    private array $used = [];

    private function normalize(string $key): string
    {
        return strtolower(trim($key));
    }

    /**
     * Register an asset bundle.
     */
    public function register(
        string $key,
        array $assets
    ): void {

        $key = $this->normalize($key);

        if (!isset($this->definitions[$key])) {
            $this->definitions[$key] = [
                'css' => [],
                'js'  => [],
            ];
        }

        foreach (['css', 'js'] as $type) {

            if (empty($assets[$type])) {
                continue;
            }

            $this->definitions[$key][$type] = array_values(
                array_unique(
                    array_merge(
                        $this->definitions[$key][$type],
                        $assets[$type]
                    )
                )
            );
        }
    }

    /**
     * Mark an asset bundle for use.
     */
    public function use(string $key): void
    {
        $this->used[
            $this->normalize($key)
        ] = true;
    }

    /**
     * Check whether an asset bundle is registered.
     */
    public function has(string $key): bool
    {
        return isset(
            $this->definitions[
                $this->normalize($key)
            ]
        );
    }

    /**
     * Get all used asset bundle keys.
     */
    public function getUsed(): array
    {
        return array_keys($this->used);
    }

    /**
     * Get an asset bundle.
     */
    public function get(string $key): array
    {
        return $this->definitions[
            $this->normalize($key)
        ] ?? [
            'css' => [],
            'js'  => [],
        ];
    }
}