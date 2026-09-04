<?php

namespace DeVy\Core\Http;

use DeVy\Core\Container;
use DeVy\Core\Application;

use DeVy\Core\Services\{
    HookManager,
    ConfigService,
    PathService
};

use DeVy\Core\Http\Middleware\Pipeline;
use DeVy\Core\Http\Middleware\MiddlewareRegistry;

class Router
{
    private ConfigService $config;
    private Container $container;
    private Application $app;
    private PathService $paths;
    private Request $request;

    private array $routes = [];
    private array $namedRoutes = [];
    private array $groupStack = [];

    public function __construct(
        PathService $paths,
        ConfigService $config,
        Container $container,
        Application $app,
        Request $request
    ) {
        $this->paths = $paths;
        $this->config = $config;
        $this->container = $container;
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * ----------------------------------------
     * Dynamic route matcher
     * ----------------------------------------
     */
    private function matchDynamicRoute(
        string $routeUri,
        string $requestUri,
        &$params = []
    ): bool {
        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_]+)(:([^}]+))?\}#',
            function ($matches) {
                $name = $matches[1];
                $regex = $matches[3] ?? '[^/]+';
                return '(?P<' . $name . '>' . $regex . ')';
            },
            $routeUri
        );

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestUri, $matches)) {
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = rawurldecode($value);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * ----------------------------------------
     * Group routes
     * ----------------------------------------
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;

        $callback($this);

        array_pop($this->groupStack);
    }

    /**
     * ----------------------------------------
     * Add route
     * ----------------------------------------
     */
    public function add(array $route): void
    {
        if (!isset($route['method'], $route['uri'], $route['action'])) {
            throw new \InvalidArgumentException('Invalid route definition.');
        }

        $route['middleware'] = (array) ($route['middleware'] ?? []);

        /*
        |--------------------------------------------------------------------------
        | Permission shortcut
        |--------------------------------------------------------------------------
        |
        | 'permission' => 'admin.pages.edit'
        |
        | automatically becomes:
        |
        | 'admin.permission:admin.pages.edit'
        |
        */

        if (!empty($route['permission'])) {

            $permissionMiddleware =
                'admin.permission:' . $route['permission'];

            // Avoid adding it twice if explicitly declared as middleware.
            if (!in_array(
                $permissionMiddleware,
                $route['middleware'],
                true
            )) {
                $route['middleware'][] = $permissionMiddleware;
            }
        }

        $prefix = '';

        foreach ($this->groupStack as $group) {

            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }

            if (isset($group['middleware'])) {
                $route['middleware'] = array_merge(
                    (array) $group['middleware'],
                    $route['middleware']
                );
            }
        }

        $route['uri'] = '/' . trim(
            $prefix . '/' . ltrim($route['uri'], '/'),
            '/'
        );

        $this->routes[] = $route;

        if (isset($route['name'])) {
            if (isset($this->namedRoutes[$route['name']])) {
                throw new \RuntimeException(
                    "Duplicate route name: {$route['name']}"
                );
            }

            $this->namedRoutes[$route['name']] = $route['uri'];
        }
    }

    /**
     * ----------------------------------------
     * Dispatch
     * ----------------------------------------
     */
    public function dispatch(): mixed
    {
        $method = $this->request->method();

        $uri = $this->request->uri();

        foreach ($this->routes as $route) {

            $params = [];

            if (
                strtoupper($route['method']) === $method &&
                (
                    $route['uri'] === $uri ||
                    $this->matchDynamicRoute($route['uri'], $uri, $params)
                )
            ) {
                return $this->runRoute($route, $params);
            }
        }

        $handled = $this->app
            ->get(HookManager::class)
            ->dispatch('router.fallback', $uri);

        if ($handled === true) {
            return null;
        }

        throw new HttpException(404, 'Page Not Found');
    }

    /**
     * ----------------------------------------
     * Named route generator
     * ----------------------------------------
     */
    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route {$name} not found.");
        }

        $uri = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {

            preg_match(
                '#\{' . preg_quote($key, '#') . '(:[^}]+)?\}#',
                $uri,
                $m
            );

            if (!empty($m[1])) {

                // custom regex → allow slashes
                $value = implode(
                    '/',
                    array_map(
                        'rawurlencode',
                        explode('/', trim($value, '/'))
                    )
                );

            } else {

                // normal parameter
                $value = rawurlencode($value);

            }

            $uri = preg_replace(
                '#\{' . preg_quote($key, '#') . '(?::[^}]+)?\}#',
                $value,
                $uri
            );
        }

        $uri = preg_replace('#\{[a-zA-Z_]+(:[^}]+)?\}#', '', $uri);

        return preg_replace('#/+#', '/', $uri);
    }

    /**
     * ----------------------------------------
     * Run route + middleware
     * ----------------------------------------
     */
    private function runRoute(array $route, array $params): mixed
    {
        /** @var MiddlewareRegistry $registry */
        $registry = $this->container->get(MiddlewareRegistry::class);

        $middlewareStack = $route['middleware'];

        $resolved = [];

        foreach ($middlewareStack as $entry) {

            [$name, $param] = array_pad(
                explode(':', $entry, 2),
                2,
                null
            );

            $class = $registry->resolve($name);

            if (!$class) {
                continue;
            }


            $middleware = $this->container->get($class);

            if ($param && method_exists($middleware, 'setParameter')) {
                $middleware->setParameter($param);
            }

            $resolved[] = $middleware;
        }

        [$controllerClass, $method] = $route['action'];


        $controller = $this->container->get($controllerClass);

        $pipeline = new Pipeline();

        return $pipeline->through($resolved)->then(
            fn () => $controller->$method(
                $params,
                $this->request
            ),
            $this->request
        );
    }

    /**
     * ----------------------------------------
     * Debug
     * ----------------------------------------
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function exportDebug(): array
    {
        $routes = [];

        foreach ($this->routes as $route) {
            $routes[] = [
                'method' => strtoupper($route['method']),
                'uri' => $route['uri'],
                'name' => $route['name'] ?? null,
                'action' => $route['action'],
                'middleware' => $route['middleware'],
            ];
        }

        return [
            'generated_at' => date('c'),
            'routes' => $routes
        ];
    }
}