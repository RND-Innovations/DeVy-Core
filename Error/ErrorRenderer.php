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

use DeVy\Core\Rendering\PageContext;
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

        return $this->renderError($e, $status);
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
    private function renderError(
        Throwable $e,
        int $status
    ): Response {

        try {

            $template = "@theme/pages/error-page.twig";

            $data = array_merge(
                [
                    'code'        => $status,
                    'message'     => $status . " Error",
                    'description' => $e->getMessage(),
                ],
                $e instanceof HttpException
                    ? $e->getData()
                    : []
            );

            if ($this->debug) {
                $error_trace = 
                '<div style="border-radius: 1em; background: black; color:green; margin: 0 auto; padding: 2em; white-space: pre-wrap; font-size: 1.1em;"><p style="color:orange;"><strong style="color:red;">File: </strong>'.$e->getFile().'</p>
                <p style="color:orange;"><strong style="color:red;">Line: </strong>'.$e->getLine().'</p>
                <div>'.htmlspecialchars($e->getTraceAsString()).'</div></div>';
            }


            $context = $this->container->get(
                PageContext::class
            );
            $context
                ->id('error-' . $status)
                ->class('error-page')
                ->meta([
                    'title'       => $status . " Error",
                    'description' => $e->getMessage(),
                ])
                ->fields($data ?? [])
                ->content($error_trace ?? '')
                ->body('');


            $html = $this->templates->render(
                $template,
                $context->toArray()
            );                

            
            return new Response(
                $html,
                $status,
                $e instanceof HttpException
                    ? $e->getHeaders()
                    : []
            );

        } catch (Throwable) {

            return $this->renderFallback($status);
        }
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