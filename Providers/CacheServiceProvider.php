<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Cache\HtmlCache;
use DeVy\Core\Container;
use DeVy\Core\Http\Request;
use DeVy\Core\Services\PathService;
use DeVy\Core\Services\ConfigService;

class CacheServiceProvider
{
    public static function register(
        Container $c
    ): void {

        $c->singleton(
            HtmlCache::class,
            fn($c) => new HtmlCache(
                $c->get(PathService::class),
                $c->get(Request::class),
                $c->get(ConfigService::class)
            )
        );
    }
}