<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;

class RenderingEngine
{
    public function __construct(
        private Environment $twig,
        private HookDebugService $hookDebug,
        private RouteDebugService $routeDebug
    ) {}

    public function render(string $template, array $data = []): string
    {
        $output = $this->twig->render($template, $data);

        $this->hookDebug->dump();
        $this->routeDebug->dump();

        return $output;
    }

    public function engine(): Environment
    {
        return $this->twig;
    }
}