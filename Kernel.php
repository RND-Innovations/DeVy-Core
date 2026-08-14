<?php

namespace DeVy\Core;

use Throwable;

use DeVy\Core\Http\Router;
use DeVy\Core\Http\Request;
use DeVy\Core\Http\Response;
use DeVy\Core\Http\HttpException;

use DeVy\Core\Http\Middleware\Pipeline;
use DeVy\Core\Http\Middleware\MiddlewareRegistry;

use DeVy\Core\Contracts\Session\SessionInterface;

use DeVy\Core\Error\ErrorRenderer;

class Kernel
{
    private Application $app;

    private string $basePath;

    public function __construct(
        Application $app,
        string $basePath
    ) {
        $this->app = $app;

        $this->basePath = rtrim(
            $basePath,
            '/'
        );
    }

    public function handle(): Response
    {
        try {

            $this->app->boot();

            $this->validateEnvironment();

            $container = $this->app->container();

            $router = $container->get(Router::class);

            $registry = $container->get(
                MiddlewareRegistry::class
            );

            $resolved = [];

            foreach ($registry->global() as $alias) {

                $class = $registry->resolve($alias);

                if (!$class) {
                    continue;
                }

                $resolved[] = $container->get($class);
            }

            $pipeline = new Pipeline();

            $response = $pipeline
                ->through($resolved)
                ->then(
                    fn () => $router->dispatch(),
                    $container->get(Request::class)
                );

            return $this->normalizeResponse($response);

        } catch (Throwable $e) {

            return $this->handleException($e);
        }
    }

    private function validateEnvironment(): void
    {
        $validator = $this->app
            ->container()
            ->get(EnvironmentValidator::class);

        $validator->validate();
    }

    private function normalizeResponse(
        mixed $response
    ): Response {

        if ($response instanceof Response) {
            return $response;
        }

        if (is_array($response)) {
            return Response::json($response);
        }

        if (is_string($response)) {
            return new Response($response);
        }

        if ($response === null) {
            return new Response('', 204);
        }

        return new Response(
            (string) $response
        );
    }

    private function handleException(
        Throwable $e
    ): Response {

        try {

            $container = $this->app->container();

            if (
                $e instanceof HttpException &&
                $e->getStatus() === 419
            ) {

                $session = $container
                    ->get(SessionInterface::class);

                $session->regenerate();

                $request = $container
                    ->get(Request::class);

                return Response::redirect(
                    $request->fullUrl()
                );
            }

            $renderer = $container
                ->get(ErrorRenderer::class);

            return $renderer->render($e);

        } catch (Throwable $inner) {

            return new Response(
                '<h1>Critical Error: </h1><p>'.$inner.'</p>',
                500
            );
        }
    }
}