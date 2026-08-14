<?php

namespace DeVy\Core\Modules;

use DeVy\Core\Application;
use DeVy\Core\Http\Router;
use DeVy\Core\Http\Request;
use DeVy\Core\Services\{
    HookManager,
    ConfigService,
    PathService,
    SettingsService,
    ToastService,
    QrCodeService,
    PermissionService,
    TemplateService
};
use DeVy\Core\Assets\AssetRegistry;
use DeVy\Core\Http\Middleware\MiddlewareRegistry;

class ModuleContext
{
    public function __construct(
        protected Application $app
    ) {}

    // -----------------------------
    // Core accessors
    // -----------------------------

    public function app(): Application
    {
        return $this->app;
    }

    public function container()
    {
        return $this->app->container();
    }

    public function get(string $class): mixed
    {
        return $this->app->get($class);
    }

    // -----------------------------
    // Common services shortcuts
    // -----------------------------

    public function router(): Router
    {
        return $this->get(Router::class);
    }

    public function request(): Request
    {
        return $this->get(Request::class);
    }

    public function hooks(): HookManager
    {
        return $this->get(HookManager::class);
    }

    public function config(): ConfigService
    {
        return $this->get(ConfigService::class);
    }

    public function settings(): SettingsService
    {
        return $this->get(SettingsService::class);
    }

    public function assets(): AssetRegistry
    {
        return $this->get(AssetRegistry::class);
    }

    public function templates(): TemplateService
    {
        return $this->get(TemplateService::class);
    }

    public function toast(): ToastService
    {
        return $this->get(ToastService::class);
    }

    public function path(): PathService
    {
        return $this->get(PathService::class);
    }

    public function permission(): PermissionService
    {
        return $this->get(PermissionService::class);
    }

    public function Qr(): QrCodeService
    {
        return $this->get(QrCodeService::class);
    }

    // -----------------------------
    // Middleware
    // -----------------------------

    public function middleware(): MiddlewareRegistry
    {
        return $this->get(MiddlewareRegistry::class);
    }

    public function registerMiddleware(
        string $alias,
        string $class
    ): void {
        $this->middleware()->register($alias, $class);
    }

    public function setGlobalMiddleware(
        string $alias
    ): void {
        $this->middleware()->pushGlobal($alias);
    }

    public function prependGlobalMiddleware(
        string $alias
    ): void {
        $this->middleware()->prependGlobal($alias);
    }

    // -----------------------------
    // Helpers
    // -----------------------------

    public function singleton(
        string $id,
        callable $factory
    ): void {

        $this->container()->singleton(
            $id,
            $factory
        );
    }

    public function controller(
        string $class
    ): void {

        $this->container()->singleton(
            $class,
            fn($c) => new $class($c)
        );
    }

    public function controllers(
        array $classes
    ): void {

        foreach ($classes as $class) {
            $this->controller($class);
        }
    }

    public function registerAssets(
        string $page,
        array $assets
    ): void {

        $this->registerConditionalAssets(
            fn($ctxData) =>
                ($ctxData['page'] ?? null) === $page,
            $page,
            $assets
        );
    }

    public function registerAreaAssets(
        string $area,
        string $component,
        array $assets
    ): void {

        $this->registerConditionalAssets(
            fn($ctxData) =>
                ($ctxData['area'] ?? null) === $area,
            $component,
            $assets
        );
    }

    public function adminRoutes(
        callable $callback
    ): void {

        $this->router()->group(
            [
                'prefix' => trim(
                    $this->config()->get(
                        'admin.slug'
                    ),
                    '/'
                ),
                'middleware' => [
                    'admin.guard'
                ]
            ],
            $callback
        );
    }

    public function publicRoutes(
        callable $callback,
        array $options = []
    ): void {

        $this->router()->group(
            $options,
            $callback
        );
    }

    public function hook(
        string $hook,
        callable $callback,
        int $priority = 10
    ): void {

        $this->hooks()->add(
            $hook,
            $callback,
            $priority
        );
    }

    public function addNavigation(
        string $group,
        array $item
    ): void {

        $this->hooks()->add(
            'navigation.build',
            function ($nav) use (
                $group,
                $item
            ) {

                $nav[$group][] = $item;

                return $nav;
            }
        );
    }

    public function addAdminNavigation(
        string $title,
        string $route,
        string $icon = 'folder',
        int $order = 100,
        ?string $permission = null
    ): void {

        $this->addNavigation(
            'admin',
            [
                'title' => $title,
                'icon' => $icon,
                'order' => $order,
                'url' => $this->router()->route($route),
                'permission' => $permission,
            ]
        );
    }

    // -----------------------------
    // Help Helpers secretly *_* shhh..
    // -----------------------------
    protected function registerConditionalAssets(
        callable $condition,
        string $component,
        array $assets
    ): void {

        $this->hook(
            'assets.register',
            function ($ctxData) use (
                $condition,
                $component,
                $assets
            ) {

                if (!$condition($ctxData)) {
                    return;
                }

                $registry = $this->assets();

                $registry->registerComponent(
                    $component,
                    $assets
                );

                $registry->use($component);
            }
        );
    }


    // -----------------------------
    // Easy register schema to hooks
    // -----------------------------

    public function registerSchema(
        string $hook,
        string $file,
        int $priority = 10
    ): void {

        $this->hook(
            $hook,
            function (array $schema) use ($file) {

                if (!is_file($file)) {
                    return $schema;
                }

                $moduleSchema = require $file;

                if (!is_array($moduleSchema)) {
                    return $schema;
                }

                return array_replace_recursive(
                    $schema,
                    $moduleSchema
                );

            },
            $priority
        );

    }

    public function settingsSchema(string $file): void
    {
        $this->registerSchema('settings.schema', $file);
    }

    public function configSchema(string $file): void
    {
        $this->registerSchema('config.schema', $file);
    }

}