<?php

namespace DeVy\Core\Services;

use DeVy\Core\Rendering\RenderingEngine;

class TemplateService
{
    private RenderingEngine $engine;

    public function __construct(RenderingEngine $engine)
    {
        $this->engine = $engine;
    }

    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }

    public function engine(): RenderingEngine
    {
        return $this->engine;
    }

}