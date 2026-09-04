<?php

namespace DeVy\Core\Services;

class PathService
{
    private string $basePath;
    private string $rootPath;
    private string $sitePath;
    private string $publicPath;

    public function __construct(string $basePath, string $sitePath, string $publicPath)
    {
        $this->basePath   = rtrim($basePath, '/');
        $this->rootPath   = $this->basePath;
        $this->sitePath   = rtrim($sitePath, '/');
        $this->publicPath = rtrim($publicPath, '/');
    }

    // -------------------------
    // CORE PATHS
    // -------------------------

    public function base(): string
    {
        return $this->basePath;
    }

    public function root(): string
    {
        return $this->rootPath;
    }

    public function site(): string
    {
        return $this->safe($this->sitePath);
    }

    public function public(): string
    {
        // already resolved via realpath → safe to return
        return $this->publicPath;
    }

    // -------------------------
    // SITE STRUCTURE
    // -------------------------

    public function cache(): string
    {
        return $this->safe($this->site() . '/cache');
    }

    public function publicCache(string $path = ''): string
    {
        return $this->safe(
            $this->public() . '/cache/' . trim($path, '/')
        );
    }

    public function content(string $path = ''): string
    {
        return $this->safe(
            $this->site() . '/content/' . trim($path, '/')
        );
    }

    public function logs(): string
    {
        return $this->safe($this->site() . '/logs');
    }

    public function coreModules(string $path = ''): string
    {
        // This is where core modules are stored (global for multi sites).
        return rtrim($this->base() . '/modules/' . ltrim($path, '/'), '/');
    }

    public function modules(string $path = ''): string
    {
        // This is where site modules are stored.
        return rtrim($this->site() . '/modules/' . ltrim($path, '/'), '/');
    }

    public function moduleContent(string $module, string $path = ''): string
    {
        // Module-related content is stored inside site content.
        $module = trim($module, '/');
        $path   = trim($path, '/');

        return $this->content(
            'modules/' . $module . ($path !== '' ? '/' . $path : '')
        );
    }

    public function plugins(string $path = ''): string
    {
        return $this->site() . '/plugins/' . trim($path, '/');
    }

    public function pluginContent(string $plugin, string $path = ''): string
    {
        // Plugin-related content is stored inside site content.
        $plugin = trim($plugin, '/');
        $path   = trim($path, '/');

        return $this->content(
            'plugins/' . $plugin . ($path !== '' ? '/' . $path : '')
        );
    }

    public function pluginSettings(string $plugin): string
    {
        return $this->pluginContent($plugin, 'settings.json');
    }


    public function system(string $path = ''): string
    {
        return $this->safe(
            $this->site() . '/system/' . trim($path, '/')
        );
    }

    public function themes(string $theme = null): string
    {
        $base = $this->site() . '/themes';

        return $theme
            ? $this->safe("{$base}/{$theme}")
            : $this->safe($base);
    }



    // Content helpers
    public function pages(string $path = ''): string
    {
        return $this->content('pages/' . trim($path, '/'));
    }

    public function media(string $path = ''): string
    {
        return $this->content('media/' . trim($path, '/'));
    }

    public function navigation(): string
    {
        return $this->content('navigation.json');
    }


    // Theme helpers
    public function themeAssets(string $theme): string
    {
        return $this->safe($this->themes($theme) . '/assets');
    }

    public function themeViews(string $theme): string
    {
        return $this->safe($this->themes($theme) . '/views');
    }

    public function themeSchemas(string $theme): string
    {
        return $this->safe($this->themes($theme) . '/schemas');
    }




    // -------------------------
    // UTILITIES
    // -------------------------

    public function ensureDir(string $path): string
    {
        $path = $this->safe($path);

        if (!is_dir($path)) {
            if (!mkdir($path, 0775, true) && !is_dir($path)) {
                throw new \RuntimeException("Failed to create directory: {$path}");
            }
        }

        return $path;
    }

    public function ensureWritable(string $path): string
    {
        $path = $this->ensureDir($path);

        if (!is_writable($path)) {
            throw new \RuntimeException("Path not writable: {$path}");
        }

        return $path;
    }

    // -------------------------
    // SECURITY
    // -------------------------

    private function safe(string $path): string
    {
        $normalized = str_replace(['\\', '//'], '/', $path);

        if (str_contains($normalized, '..')) {
            throw new \RuntimeException("Invalid path traversal attempt: {$path}");
        }

        $allowedRoots = [
            $this->rootPath,
            $this->publicPath
        ];

        foreach ($allowedRoots as $root) {
            if (str_starts_with($normalized, rtrim($root, '/'))) {
                return $normalized;
            }
        }

        throw new \RuntimeException("Path outside allowed roots: {$path}");
    }

}