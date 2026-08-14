<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;
use DeVy\Core\Application;

class ServiceProvider
{
    public static function register(Container $c, string $basePath, Application $app): void
    {
        CoreServiceProvider::register($c, $basePath);
        InfrastructureServiceProvider::register($c);
        RoutingServiceProvider::register($c, $app);
        AssetServiceProvider::register($c);
        RenderingServiceProvider::register($c);
        CacheServiceProvider::register($c);
    }
}