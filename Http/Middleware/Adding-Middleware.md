🧠 The Big Picture

Adding middleware is always 3 steps:

1. Create class
2. Register alias
3. Attach to route (or global)

That’s it.

✅ Sample Codes

1. Create The Middleware Class

Example: Let’s say you want a middleware that logs every request.

<?php
// LogMiddleware.php
namespace RND\Core\Http\Middleware;

class LogMiddleware implements MiddlewareInterface
{
    public function handle(array $server, \Closure $next)
    {
        error_log('Request: ' . $server['REQUEST_URI']);

        return $next($server);
    }
}
?>

2. Register Alias in Application

Inside Application:

protected array $middlewareAliases = [
    'auth' => AuthMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'log' => \RND\Core\Http\Middleware\LogMiddleware::class,
];

That connects the string 'log' to the class.


3. Use It In A Route

Inside your Router::registerRoutes():

[
    'method' => 'GET',
    'uri' => '/',
    'action' => [PublicController::class, 'index'],
    'middleware' => ['log']
],


++++++++++++++++++++++++++++++++++++++++++++++++

Adding Multiple Middleware

Just stack them:

'middleware' => ['log', 'auth', 'permission:site.publish']

Execution order will be:

log
→ auth
→ permission
→ controller

Because your pipeline reverses internally.

++++++++++++++++++++++++++++++++++++++++++++++++

Global Middleware

If you want middleware to run on EVERY route:

In Application:

protected array $globalMiddleware = [
    'log'
];

Now all routes get logging automatically.

Router already merges:

array_merge(
    $this->app->getGlobalMiddleware(),
    $route['middleware']
);

So globals run first.


