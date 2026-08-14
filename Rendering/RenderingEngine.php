<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;

class RenderingEngine
{
    public function __construct(
        private Environment $twig,
        private HookDebugService $debug
    ) {}

    public function render(string $template, array $data = []): string
    {
        $data['_page'] = $data['_page'] ?? ($data['page_id'] ?? null);

        $output = $this->twig->render($template, $data);

        $this->debug->dump();

        return $output;
    }

    public function engine(): Environment
    {
        return $this->twig;
    }
}