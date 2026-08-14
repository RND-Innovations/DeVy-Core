<?php

namespace DeVy\Core\Modules;

use DeVy\Core\Application;
use DeVy\Core\Container;
use DeVy\Core\Services\PathService;

final class ModuleManager
{
    public static function boot(
        Application $app,
        Container $container
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Register Services
        |--------------------------------------------------------------------------
        */

        $container->singleton(
            ModuleLocator::class,
            fn ($c) => new ModuleLocator(
                $app,
                $c->get(PathService::class)
            )
        );

        $container->singleton(
            PackageAutoloader::class,
            fn ($c) => new PackageAutoloader(
                $c->get(ModuleLocator::class)
            )
        );

        $container->singleton(
            ModuleLoader::class,
            fn ($c) => new ModuleLoader(
                $app,
                $c->get(ModuleLocator::class),
                $c->get(PackageAutoloader::class)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Load Modules
        |--------------------------------------------------------------------------
        */

        $container
            ->get(ModuleLoader::class)
            ->load();
    }
}