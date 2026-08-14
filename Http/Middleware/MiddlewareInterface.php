<?php

namespace DeVy\Core\Http\Middleware;

use DeVy\Core\Http\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, \Closure $next);
}