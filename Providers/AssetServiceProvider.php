<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;

use DeVy\Core\Services\{
    PathService
};

use DeVy\Core\Assets\{
    AssetRegistry,
    AssetResolver,
    AssetPublisher
};

class AssetServiceProvider
{
    public static function register(Container $c): void
    {
        $c->singleton(AssetRegistry::class, fn() => new AssetRegistry());

        $c->singleton(AssetPublisher::class, fn($c) =>
            new AssetPublisher(
                $c->get(PathService::class)
            )
        );

        $c->singleton(AssetResolver::class, fn($c) =>
            new AssetResolver(
                $c->get(AssetRegistry::class),
                $c->get(AssetPublisher::class)
            )
        );
    }
}