<?php

namespace DeVy\Core\Assets;

class AssetResolver
{
    public function __construct(
        private AssetRegistry $registry,
        private AssetPublisher $publisher
    ) {}

    public function css(): array
    {
        return $this->resolve('css');
    }

    public function js(): array
    {
        return $this->resolve('js');
    }

    private function resolve(string $type): array
    {
        $files = [];

        foreach ($this->registry->getUsed() as $assetKey) {

            $assets = $this->registry->get($assetKey);

            foreach ($assets[$type] ?? [] as $file) {

                $url = $this->publisher->publish($file);

                if ($url) {
                    $files[] = $url;
                }
            }
        }

        return array_values(
            array_unique($files)
        );
    }
}