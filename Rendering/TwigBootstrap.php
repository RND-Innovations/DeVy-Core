<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigBootstrap
{
    public function __construct(
        private ViewFinder $viewFinder,
        private TwigGlobals $globals,
        private TwigFunctions $functions,
        private TwigAssets $assets
    ) {}

    public function boot(): Environment
    {
        $loader = new FilesystemLoader();

        $this->viewFinder->register($loader);

        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html'
        ]);

        $this->globals->register($twig);
        $this->functions->register($twig);
        $this->assets->register($twig);

        return $twig;
    }
}