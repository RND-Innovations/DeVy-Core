<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;
use DeVy\Core\Application;

use DeVy\Core\Http\Router;
use DeVy\Core\Http\Request;
use DeVy\Core\Http\Response;

use DeVy\Core\Contracts\Session\SessionInterface;

use DeVy\Core\Services\{
    PathService,
    ConfigService
};

class RoutingServiceProvider
{
    public static function register(Container $c, Application $app): void
    {
        /*
        |--------------------------------------------------------------------------
        | Router
        |--------------------------------------------------------------------------
        */
        $c->singleton(Router::class, fn($c) =>
            new Router(
                $c->get(PathService::class),
                $c->get(ConfigService::class),
                $c,
                $app,
                $c->get(Request::class)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Request (ONE per lifecycle)
        |--------------------------------------------------------------------------
        */
        $c->singleton(Request::class, function ($c) {

            $session = $c->get(SessionInterface::class);

            return Request::capture($session);
        });

        /*
        |--------------------------------------------------------------------------
        | Response (factory / NOT singleton)
        |--------------------------------------------------------------------------
        */
        $c->bind(Response::class, function () {
            return new Response();
        });
    }
}