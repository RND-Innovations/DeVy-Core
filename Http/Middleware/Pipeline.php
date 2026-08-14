<?php

namespace DeVy\Core\Http\Middleware;

use DeVy\Core\Http\Request;

class Pipeline
{
    protected array $middlewares = [];

    public function through(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(\Closure $destination, Request $request)
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function (Request $request) use ($middleware, $next) {
                    return $middleware->handle($request, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}