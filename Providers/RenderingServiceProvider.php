<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;

use DeVy\Core\Rendering\{
    RenderingEngine,
    ViewFinder,
    TwigBootstrap,
    TwigGlobals,
    TwigFunctions,
    TwigAssets,
    NavigationService,
    HookDebugService,
    RouteDebugService
};

use Twig\Environment;

use DeVy\Core\Services\{
    PathService,
    SettingsService,
    ConfigService,
    ToastService,
    HookManager,
    PermissionService
};

use DeVy\Core\Assets\{
    AssetRegistry,
    AssetResolver
};

class RenderingServiceProvider
{
    public static function register(Container $c): void
    {
        /**
         * --------------------------------------------------
         * View Finder
         * --------------------------------------------------
         */
        $c->singleton(ViewFinder::class, fn($c) =>
            new ViewFinder(
                $c->get(PathService::class),
                $c->get(SettingsService::class)
            )
        );

        /**
         * --------------------------------------------------
         * Twig Globals
         * --------------------------------------------------
         */
        $c->singleton(TwigGlobals::class, fn($c) =>
            new TwigGlobals(
                $c->get(SettingsService::class),
                $c->get(ConfigService::class),
                $c->get(ToastService::class),
                $c->get(AssetRegistry::class)
            )
        );

        /**
         * --------------------------------------------------
         * Navigation Service
         * --------------------------------------------------
         */
        $c->singleton(NavigationService::class, fn($c) =>
            new NavigationService(
                $c->get(HookManager::class),
                $c->get(PermissionService::class)
            )
        );

        /**
         * --------------------------------------------------
         * Twig Functions
         * --------------------------------------------------
         */
        $c->singleton(TwigFunctions::class, fn($c) =>
            new TwigFunctions(
                $c->get(HookManager::class),
                $c->get(PermissionService::class),
                $c->get(NavigationService::class),
                $c->get(SettingsService::class)
            )
        );

        /**
         * --------------------------------------------------
         * Twig Assets
         * --------------------------------------------------
         */
        $c->singleton(TwigAssets::class, fn($c) =>
            new TwigAssets(
                $c->get(AssetRegistry::class),
                $c->get(AssetResolver::class)
            )
        );

        /**
         * --------------------------------------------------
         * Hook Debug Service (FIXED - was broken before)
         * --------------------------------------------------
         */
        $c->singleton(HookDebugService::class, fn($c) =>
            new HookDebugService(
                $c->get(PathService::class),
                $c->get(ConfigService::class),
                $c->get(HookManager::class)
            )
        );

        /**
         * --------------------------------------------------
         * Twig Bootstrap (CREATES Environment)
         * --------------------------------------------------
         */
        $c->singleton(TwigBootstrap::class, fn($c) =>
            new TwigBootstrap(
                $c->get(ViewFinder::class),
                $c->get(TwigGlobals::class),
                $c->get(TwigFunctions::class),
                $c->get(TwigAssets::class)
            )
        );

        /**
         * --------------------------------------------------
         * Twig Environment (CRITICAL FIX)
         * --------------------------------------------------
         */
        $c->singleton(Environment::class, fn($c) =>
            $c->get(TwigBootstrap::class)->boot()
        );

        /**
         * --------------------------------------------------
         * Rendering Engine
         * --------------------------------------------------
         */
        $c->singleton(RenderingEngine::class, fn($c) =>
            new RenderingEngine(
                $c->get(Environment::class),
                $c->get(HookDebugService::class),
                $c->get(RouteDebugService::class)
            )
        );
    }
}