<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;
use DeVy\Core\Error\ErrorRenderer;

use DeVy\Core\Contracts\Session\SessionInterface;
use DeVy\Core\Security\Csrf;
use DeVy\Core\Infrastructure\Session\PhpSessionStore;

use DeVy\Core\Schema\SchemaForm;

use DeVy\Core\Services\{
    ConfigService,
    SettingsService,
    PermissionService,
    PathService,
    HookManager,
    TemplateService,
    CryptoService
};

class InfrastructureServiceProvider
{
    public static function register(Container $c): void
    {

        /*
        |--------------------------------------------------------------------------
        | Error Renderer
        |--------------------------------------------------------------------------
        */

        $c->singleton(ErrorRenderer::class, fn($c) =>
            new ErrorRenderer(
                $c->get(PathService::class),
                $c->get(TemplateService::class),
                $c->get(SessionInterface::class),
                $c
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        $c->singleton(Csrf::class, fn($c) =>
            new Csrf(
                $c->get(SessionInterface::class)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Schema Form
        |--------------------------------------------------------------------------
        */

        $c->singleton(SchemaForm::class, fn($c) =>
            new SchemaForm(
                $c->get(HookManager::class),
                $c->get(CryptoService::class)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Config Service
        |--------------------------------------------------------------------------
        */

        $c->singleton(ConfigService::class, fn($c) =>
            new ConfigService(
                $c->get(PathService::class),
                $c->get(HookManager::class)
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Settings Service
        |--------------------------------------------------------------------------
        */

        $c->singleton(SettingsService::class, fn($c) =>
            new SettingsService(
                $c->get(PathService::class),
                $c->get(HookManager::class)
            )
        );



        /*
        |--------------------------------------------------------------------------
        | Permission Service
        |--------------------------------------------------------------------------
        */

        $c->singleton(PermissionService::class, fn($c) =>
            new PermissionService(
                $c->get(PathService::class),
                $c->get(HookManager::class)
            )
        );



        /*
        |--------------------------------------------------------------------------
        | Session Store
        |--------------------------------------------------------------------------
        |
        | Centralized session abstraction.
        | Never access $_SESSION directly in modules/services.
        |
        */

        $c->singleton(SessionInterface::class, function () {

            $session = new PhpSessionStore();

            $session->start();

            return $session;
        });

    }
}