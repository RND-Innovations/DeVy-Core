<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;
use Twig\TwigFunction;

use DeVy\Core\Assets\AssetRegistry;
use DeVy\Core\Assets\AssetResolver;

class TwigAssets
{
    public function __construct(
        private AssetRegistry $assets,
        private AssetResolver $resolver
    ) {}

    public function register(Environment $twig): void
    {
        // COMPONENT
        $twig->addFunction(new TwigFunction('component', function ($name, $data = [], $template = null) use ($twig) {

            $this->assets->use($name);

            $template = $template ?? "@Components/{$name}.twig";

            return $twig->render($template, $data);

        }, ['is_safe' => ['html']]));

        // CSS
        $twig->addFunction(new TwigFunction('assets_css', function () {

            $html = '';
            foreach ($this->resolver->css() as $file) {
                $html .= "<link rel='stylesheet' href='{$file}'>";
            }
            return $html;

        }, ['is_safe' => ['html']]));

        // JS
        $twig->addFunction(new TwigFunction('assets_js', function () {

            $html = '';
            foreach ($this->resolver->js() as $file) {
                $html .= "<script src='{$file}'></script>";
            }
            return $html;

        }, ['is_safe' => ['html']]));
    }
}