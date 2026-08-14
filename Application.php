<?php

namespace DeVy\Core;

use RuntimeException;

use DeVy\Core\Services\{
    SettingsService,
    ConfigService,
    PathService
};

use DeVy\Core\Modules\ModuleManager;
use DeVy\Core\Providers\ServiceProvider;
use DeVy\Core\Rendering\RenderingEngine;

use DeVy\Core\Http\Middleware\MiddlewareRegistry;

class Application
{
    protected static ?Application $instance = null;

    private string $basePath;

    private Container $container;

    private bool $booted = false;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');

        $this->container = new Container();

        static::$instance = $this;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        /**
         * --------------------------------------------------
         * Register All Service Providers
         * --------------------------------------------------
         */
        ServiceProvider::register(
            $this->container,
            $this->basePath,
            $this
        );

        /**
         * --------------------------------------------------
         * Middleware Registry
         * --------------------------------------------------
         */
        $this->container->singleton(
            MiddlewareRegistry::class,
            fn () => new MiddlewareRegistry()
        );

        /**
         * --------------------------------------------------
         * Module loader
         * --------------------------------------------------
         */
        ModuleManager::boot(
            $this,
            $this->container
        );

        /**
         * --------------------------------------------------
         * Rendering engine
         * --------------------------------------------------
         */
        $this->container->get(RenderingEngine::class);

        /**
         * --------------------------------------------------
         * Settings
         * --------------------------------------------------
         */
        $settings = $this->container->get(SettingsService::class);

        date_default_timezone_set(
            $settings->get('site.timezone', 'UTC')
        );

        $this->booted = true;
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function make(string $abstract): mixed
    {
        return $this->container->get($abstract);
    }

    public static function get(string $abstract): mixed
    {
        return self::getInstance()->make($abstract);
    }

    public static function getInstance(): Application
    {
        if (!static::$instance) {
            throw new RuntimeException('Application not initialized.');
        }

        return static::$instance;
    }

    /**
     * --------------------------------------------------
     * Base path
     * --------------------------------------------------
     */
    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * --------------------------------------------------
     * Version
     * --------------------------------------------------
     */
    public function version(): string
    {
        $config = $this->container->get(ConfigService::class);

        return (string) $config->get('app.version', '1.0.0');
    }

    /**
     * --------------------------------------------------
     * Boot state
     * --------------------------------------------------
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }
}