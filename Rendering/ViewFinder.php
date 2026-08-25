<?php

namespace DeVy\Core\Rendering;

use DeVy\Core\Services\PathService;
use DeVy\Core\Services\SettingsService;
use Twig\Loader\FilesystemLoader;

class ViewFinder
{
    private PathService $paths;
    private SettingsService $settings;

    private array $namespaces = [];

    private ?string $theme = null;

    public function __construct(
        PathService $paths,
        SettingsService $settings
    ) {
        $this->paths = $paths;
        $this->settings = $settings;

        $this->theme = $this->settings->get('template.theme') ?? 'default';

        $this->boot(); 
    }

    private function boot(): void
    {
        $this->registerTheme();

        $this->registerModules(
            $this->paths->base() . '/Modules',
            'core'
        );

        $this->registerModules(
            $this->paths->modules(),
            'site'
        );

        $this->registerPlugins();
    }

    /**
     * THEME (highest priority)
     */
    private function registerTheme(): void
    {
        $themePath = $this->paths->themes($this->theme . "/views");

        if (is_dir($themePath)) {
            $this->namespaces['theme'][] = $themePath;
        }
    }

    /**
     * MODULES
     */
    private function registerModules(string $basePath, string $type): void
    {
        if (!is_dir($basePath)) return;

        foreach (scandir($basePath) as $module) {

            if ($module === '.' || $module === '..') continue;

            $viewPath = $basePath . '/' . $module . '/views';

            if (!is_dir($viewPath)) continue;

            $this->namespaces[$module][] = $viewPath;
        }
    }

    /**
     * PLUGINS
     */
    private function registerPlugins(): void
    {
        $path = $this->paths->plugins();

        if (!is_dir($path)) return;

        foreach (scandir($path) as $plugin) {

            if ($plugin === '.' || $plugin === '..') continue;

            $viewPath = $path . '/' . $plugin . '/views';

            if (is_dir($viewPath)) {
                $this->namespaces[$plugin][] = $viewPath;
            }
        }
    }

    /**
     * APPLY TO TWIG
     */
    public function register(FilesystemLoader $loader): void
    {
        // 1. Default (no namespace)
        if (!empty($this->namespaces['theme'][0])) {
            $loader->prependPath($this->namespaces['theme'][0]);
        }

        // 2. ALSO register @theme namespace
        if (!empty($this->namespaces['theme'])) {
            foreach ($this->namespaces['theme'] as $path) {
                $loader->addPath($path, 'theme');
            }
        }

        // 3. Other namespaces
        foreach ($this->namespaces as $namespace => $paths) {

            if ($namespace === 'theme') continue;

            foreach ($paths as $path) {
                $loader->addPath($path, $namespace);
            }
        }
    }

    public function dump(): array
    {
        return $this->namespaces;
    }
}