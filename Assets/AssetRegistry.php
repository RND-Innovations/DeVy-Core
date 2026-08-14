<?php

namespace DeVy\Core\Assets;

class AssetRegistry
{
    private array $definitions = [];
    private array $used = [];

    private function normalize(string $name): string
    {
        return strtolower($name); // 🔥 enforce consistency
    }

    public function registerComponent(string $name, array $assets): void
    {

        $name = $this->normalize($name);
        
        if (!isset($this->definitions[$name])) {
            $this->definitions[$name] = [
                'css' => [],
                'js'  => []
            ];
        }

        // Merge safely
        foreach (['css', 'js'] as $type) {
            if (!empty($assets[$type])) {
                $this->definitions[$name][$type] = array_values(array_unique(array_merge(
                    $this->definitions[$name][$type],
                    $assets[$type]
                )));
            }
        }
    }

    public function use(string $name): void
    {
        $name = $this->normalize($name);

        $this->used[$name] = true;
    }

    public function getUsed(): array
    {
        return array_keys($this->used);
    }

    public function get(string $name): array
    {
        $name = $this->normalize($name);

        return $this->definitions[$name] ?? [];
    }
}