<?php

namespace DeVy\Core\Rendering;

use DeVy\Core\Http\Router;
use DeVy\Core\Services\PathService;
use DeVy\Core\Services\ConfigService;

class RouteDebugService
{
    public function __construct(
        private PathService $paths,
        private ConfigService $config,
        private Router $router
    ) {}

    public function dump(): void
    {
        try {

            if (!$this->config->get('app.debug', false)) {
                return;
            }

            $dir = $this->paths->ensureWritable(
                $this->paths->logs()
            );

            $file = $dir . '/routes-debug.json';

            $current = $this->router->exportDebug()['routes'];

            file_put_contents(
                $file,
                json_encode([
                    'generated_at' => date('c'),
                    'routes' => $current
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

        } catch (\Throwable $e) {
            // Debug logging must never break the application.
        }
    }
}