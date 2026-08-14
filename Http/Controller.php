<?php

namespace DeVy\Core\Http;

use Throwable;

use DeVy\Core\Container;
use DeVy\Core\Services\TemplateService;
use DeVy\Core\Cache\HtmlCache;

abstract class Controller
{
    /**
     * ----------------------------------------
     * Container
     * ----------------------------------------
     */
    protected Container $container;

    /**
     * ----------------------------------------
     * Core Services
     * ----------------------------------------
     */
    protected Request $request;

    protected TemplateService $templates;

    protected Router $router;

    protected HtmlCache $pageCache;

    /**
     * ----------------------------------------
     * Constructor
     * ----------------------------------------
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;

        $this->request = $container->get(
            Request::class
        );

        $this->templates = $container->get(
            TemplateService::class
        );

        $this->router = $container->get(
            Router::class
        );

        $this->pageCache = $container->get(
            HtmlCache::class
        );

        if (method_exists($this, 'boot')) {
            $this->boot();
        }
    }

    /**
     * ----------------------------------------
     * Resolve Service
     * ----------------------------------------
     */
    protected function service(
        string $id
    ): mixed {
        return $this->container->get($id);
    }

    /**
     * ----------------------------------------
     * Check Service Exists
     * ----------------------------------------
     */
    protected function hasService(
        string $id
    ): bool {
        return $this->container->has($id);
    }

    /**
     * ----------------------------------------
     * Get Container
     * ----------------------------------------
     */
    protected function container(): Container
    {
        return $this->container;
    }

    /**
     * ----------------------------------------
     * Render Template To String
     * ----------------------------------------
     */
    protected function renderView(
        string $template,
        array $data = []
    ): string {

        return $this->templates->render(
            $template,
            $data
        );
    }

    /**
     * ----------------------------------------
     * Render View Response
     * ----------------------------------------
     */
    protected function view(
        string $template,
        array $data = [],
        int $status = 200,
        array $headers = []
    ): Response {

        return $this->response(
            $this->renderView(
                $template,
                $data
            ),
            $status,
            $headers
        );
    }

    /**
     * ----------------------------------------
     * Raw Response
     * ----------------------------------------
     */
    protected function response(
        string $content,
        int $status = 200,
        array $headers = []
    ): Response {

        return new Response(
            $content,
            $status,
            $headers
        );
    }

    /**
     * ----------------------------------------
     * Redirect Route
     * ----------------------------------------
     */
    protected function redirectRoute(
        string $route,
        array $params = [],
        int $status = 302
    ): Response {

        return Response::redirect(
            $this->router->route(
                $route,
                $params
            ),
            $status
        );
    }

    /**
     * ----------------------------------------
     * Redirect URL
     * ----------------------------------------
     */
    protected function redirectUrl(
        string $url,
        int $status = 302
    ): Response {

        return Response::redirect(
            $url,
            $status
        );
    }

    /**
     * ----------------------------------------
     * Redirect Back
     * ----------------------------------------
     */
    protected function redirectBack(
        int $status = 302,
        ?string $fallback = '/'
    ): Response {

        return Response::redirect(
            $this->request->referer() ?? $fallback,
            $status
        );
    }

    /**
     * ----------------------------------------
     * JSON Response
     * ----------------------------------------
     */
    protected function json(
        array $data,
        int $status = 200
    ): Response {

        return Response::json(
            $data,
            $status
        );
    }

    /**
     * ----------------------------------------
     * Abort Response
     * ----------------------------------------
     */
    protected function abort(
        int $status = 404,
        string $template = '',
        array $data = []
    ): Response {

        $map = [
            403 => '@theme/errors/403.twig',
            404 => '@theme/errors/404.twig',
            500 => '@theme/errors/500.twig',
        ];

        if ($template === '') {
            $template = $map[$status]
                ?? '@theme/errors/error.twig';
        }

        try {

            $html = $this->renderView(
                $template,
                $data
            );

        } catch (Throwable) {

            $html = sprintf(
                '<h1>%d Error</h1>',
                $status
            );
        }

        return $this->response(
            $html,
            $status
        );
    }

    /**
     * ----------------------------------------
     * Current Request
     * ----------------------------------------
     */
    protected function request(): Request
    {
        return $this->request;
    }

    /**
     * ----------------------------------------
     * Current Router
     * ----------------------------------------
     */
    protected function router(): Router
    {
        return $this->router;
    }

    /**
     * ----------------------------------------
     * Template Service
     * ----------------------------------------
     */
    protected function templates(): TemplateService
    {
        return $this->templates;
    }

    /**
     * ----------------------------------------
     * HTML Page Cache
     * ----------------------------------------
     */
    protected function pageCache(): HtmlCache
    {
        return $this->pageCache;
    }

}