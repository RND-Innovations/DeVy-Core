<?php

namespace DeVy\Core\Error;

use Throwable;

use DeVy\Core\Container;

use DeVy\Core\Http\{
    Response,
    HttpException
};

use DeVy\Core\Services\{
    PathService,
    TemplateService
};

use DeVy\Core\Contracts\Session\SessionInterface;

use DeVy\Core\Http\Middleware\CsrfMiddleware;

class ErrorRenderer
{
    private PathService $paths;

    private TemplateService $templates;

    private SessionInterface $session;

    private Container $container;

    private bool $debug;

    public function __construct(
        PathService $paths,
        TemplateService $templates,
        SessionInterface $session,
        Container $container
    ) {
        $this->paths = $paths;
        $this->templates = $templates;
        $this->session = $session;
        $this->container = $container;

        $this->debug = (bool) env('APP_DEBUG', false);
    }

    /**
     * ----------------------------------------
     * Render Exception
     * ----------------------------------------
     */
    public function render(Throwable $e): Response
    {
        $status = $this->resolveStatus($e);

        $this->log($e, $status);

        /*
        |--------------------------------------------------------------------------
        | Debug Mode
        |--------------------------------------------------------------------------
        */

        if ($this->debug) {
            return $this->renderDebug($e, $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Production Mode
        |--------------------------------------------------------------------------
        */

        return $this->renderProduction($e, $status);
    }

    /**
     * ----------------------------------------
     * Resolve HTTP Status
     * ----------------------------------------
     */
    private function resolveStatus(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            return $e->getStatus();
        }

        return 500;
    }

    /**
     * ----------------------------------------
     * Production Rendering
     * ----------------------------------------
     */
    private function renderProduction(
        Throwable $e,
        int $status
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Try Twig Rendering First
        |--------------------------------------------------------------------------
        */

        try {

            $html = $this->templates->render(
                "@Error/{$status}.twig",
                [
                    'status' => $status,
                    'message' => $e->getMessage()
                ]
            );

            return new Response(
                $html,
                $status
            );

        } catch (Throwable $inner) {

            /*
            |--------------------------------------------------------------------------
            | Hard Fallback Rendering
            |--------------------------------------------------------------------------
            */

            return $this->renderFallback($status);
        }
    }

    /**
     * ----------------------------------------
     * Debug Rendering
     * ----------------------------------------
     */
    private function renderDebug(
        Throwable $e,
        int $status
    ): Response {

        ob_start();
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title>Debug Error</title>

            <style>
                body {
                    background: #0f172a;
                    color: #e2e8f0;
                    font-family: monospace;
                    padding: 40px;
                }

                .box {
                    background: #1e293b;
                    padding: 20px;
                    border-radius: 8px;
                }

                h1 {
                    color: #f87171;
                }

                .trace {
                    margin-top: 20px;
                    white-space: pre-wrap;
                    font-size: 14px;
                }
            </style>
        </head>

        <body>

            <div class="box">

                <h1>
                    <?= $status ?>
                    -
                    <?= htmlspecialchars($e->getMessage()) ?>
                </h1>

                <p>
                    <strong>File:</strong>
                    <?= $e->getFile() ?>
                </p>

                <p>
                    <strong>Line:</strong>
                    <?= $e->getLine() ?>
                </p>

                <div class="trace">
                    <?= htmlspecialchars($e->getTraceAsString()) ?>
                </div>

            </div>

        </body>
        </html>

        <?php

        return new Response(
            ob_get_clean(),
            $status
        );
    }

    /**
     * ----------------------------------------
     * Hard Fallback Renderer
     * ----------------------------------------
     */
    private function renderFallback(
        int $status
    ): Response {

        ob_start();

        $template = __DIR__ . "/templates/{$status}.php";

        if (file_exists($template)) {

            include $template;

        } else {

            echo "<h1>{$status} Error</h1>";
        }

        return new Response(
            ob_get_clean(),
            $status
        );
    }

    /**
     * ----------------------------------------
     * Error Logging
     * ----------------------------------------
     */
    private function log(
        Throwable $e,
        int $status
    ): void {

        $logDir = $this->paths->logs();

        $logPath = $logDir . '/error.log';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        if (!is_dir($logDir) || !is_writable($logDir)) {
            return;
        }

        @file_put_contents(
            $logPath,
            '[' . date('Y-m-d H:i:s') . "] [{$status}] " .
            $e->getMessage() .
            ' in ' . $e->getFile() .
            ':' . $e->getLine() .
            PHP_EOL,
            FILE_APPEND
        );
    }
}