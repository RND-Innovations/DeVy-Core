<?php

namespace DeVy\Core\Providers;

use DeVy\Core\Container;
use DeVy\Core\SiteResolver;
use DeVy\Core\EnvironmentValidator;

use DeVy\Core\Services\{
    PathService,
    HookManager,
    ToastService,
    CryptoService,
    QrCodeService
};

use DeVy\Core\Support\{
    PathSanitizer,
    PageFileHandler
};

class CoreServiceProvider
{
    public static function register(
        Container $c,
        string $basePath
    ): void {

        $c->singleton(
            SiteResolver::class,
            fn() => new SiteResolver($basePath)
        );

        $c->singleton(
            PathService::class,
            function ($c) use ($basePath) {

                $resolver = $c->get(
                    SiteResolver::class
                );

                return new PathService(
                    $basePath,
                    $resolver->getSitePath(),
                    $resolver->getPublicPath()
                );
            }
        );

        $c->singleton(
            EnvironmentValidator::class,
            fn($c) => new EnvironmentValidator(
                $c->get(PathService::class)
            )
        );

        $c->singleton(
            PathSanitizer::class,
            fn() => new PathSanitizer()
        );

        $c->singleton(
            PageFileHandler::class,
            fn() => new PageFileHandler()
        );

        $c->singleton(
            HookManager::class,
            fn() => new HookManager()
        );

        $c->singleton(
            ToastService::class,
            fn() => new ToastService()
        );

        $c->singleton(
            QrCodeService::class,
            fn() => new QrCodeService()
        );

        $c->singleton(
            QrCodeService::class,
            fn() => new QrCodeService()
        );

        $c->singleton(
            CryptoService::class,
            fn() => new CryptoService()
        );

    }
}