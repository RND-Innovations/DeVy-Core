<?php

declare(strict_types=1);

namespace DeVy\Core\Cache;

use DeVy\Core\Http\Request;
use DeVy\Core\Services\PathService;
use DeVy\Core\Services\ConfigService;

final class HtmlCache
{
    public function __construct(
        private PathService $path,
        private Request $request,
        private ConfigService $config
    ) {}


    /**
     * ----------------------------------------
     * Is HTML caching enabled?
     * ----------------------------------------
     */
    private function enabled(): bool
    {
        return (bool) $this->config->get(
            'site.cache',
            true
        );
    }

    /**
     * ----------------------------------------
     * Store Current Page
     * ----------------------------------------
     */
    public function putCurrent(
        string $html
    ): void {

        if (!$this->enabled()) {
            return;
        }

        $this->put(
            $this->request->uri(),
            $html
        );
    }

    /**
     * ----------------------------------------
     * Store HTML
     * ----------------------------------------
     */
    public function put(
        string $uri,
        string $html
    ): void {

        if (!$this->enabled()) {
            return;
        }

        $file = $this->path($uri);

        $this->path->ensureDir(
            dirname($file)
        );

        file_put_contents(
            $file,
            $html,
            LOCK_EX
        );
    }

    /**
     * ----------------------------------------
     * Current Page Cached?
     * ----------------------------------------
     */
    public function currentExists(): bool
    {

        if (!$this->enabled()) {
            return false;
        }

        return $this->exists(
            $this->request->uri()
        );
    }

    /**
     * ----------------------------------------
     * Cache Exists
     * ----------------------------------------
     */
    public function exists(
        string $uri
    ): bool {

        if (!$this->enabled()) {
            return false;
        }

        return is_file(
            $this->path($uri)
        );
    }

    /**
     * ----------------------------------------
     * Current Cached HTML
     * ----------------------------------------
     */
    public function current(): string
    {

        if (!$this->enabled()) {
            return false;
        }

        return $this->get(
            $this->request->uri()
        );
    }

    /**
     * ----------------------------------------
     * Get Cached HTML
     * ----------------------------------------
     */
    public function get(
        string $uri
    ): string {

        if (!$this->enabled()) {
            return false;
        }

        $file = $this->path($uri);

        return is_file($file)
            ? (string) file_get_contents($file)
            : '';
    }

    /**
     * ----------------------------------------
     * Delete Current Cache
     * ----------------------------------------
     */
    public function deleteCurrent(): void
    {
        $this->delete(
            $this->request->uri()
        );
    }

    /**
     * ----------------------------------------
     * Delete Cache
     * ----------------------------------------
     */
    public function delete(
        string $uri
    ): void {

        $file = $this->path($uri);

        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * ----------------------------------------
     * Current Cache Path
     * ----------------------------------------
     */
    public function currentPath(): string
    {
        return $this->path(
            $this->request->uri()
        );
    }

    /**
     * ----------------------------------------
     * Resolve Cache File
     * ----------------------------------------
     */
    public function path(
        string $uri
    ): string {

        $uri = trim($uri);

        if ($uri === '' || $uri === '/') {

            return $this->path->publicCache(
                'pages/home.html'
            );
        }

        return $this->path->publicCache(
            'pages/' . trim($uri, '/') . '.html'
        );
    }
}