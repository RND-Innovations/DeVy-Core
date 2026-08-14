<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;
use DeVy\Core\Services\{
    SettingsService,
    ConfigService,
    ToastService
};

use DeVy\Core\Assets\AssetRegistry;

class TwigGlobals
{
    public function __construct(
        private SettingsService $settings,
        private ConfigService $config,
        private ToastService $toast,
        private AssetRegistry $assets
    ) {}

    public function register(Environment $twig): void
    {
        $twig->addGlobal('settings', $this->settings);

        $twig->addGlobal('toasts', $this->toast->get());

        $twig->addGlobal('asset_registry', $this->assets);

        // Runtime application information
        $twig->addGlobal(
            'framework_name',
            $this->config->get('app.name')
        );

        $twig->addGlobal(
            'framework_version',
            $this->config->get('app.version')
        );

        $twig->addGlobal(
            'app_env',
            $this->config->get('app.env')
        );

        $twig->addGlobal(
            'app_url',
            $this->config->get('app.url')
        );

        $twig->addGlobal(
            'admin_url',
            $this->config->get('app.url_admin')
        );
    }
}