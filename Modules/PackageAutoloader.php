<?php

namespace DeVy\Core\Modules;

final class PackageAutoloader
{
    private bool $registered = false;

    public function __construct(
        private ModuleLocator $locator
    ) {}

    /**
     * ---------------------------------------------------------
     * Register Runtime Module Autoloader
     * ---------------------------------------------------------
     */
    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->registered = true;

        spl_autoload_register(
            function (string $class): void {

                $prefix = 'DeVy\\Modules\\';

                if (!str_starts_with($class, $prefix)) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Remove Namespace Prefix
                |--------------------------------------------------------------------------
                */

                $relative = substr(
                    $class,
                    strlen($prefix)
                );

                /*
                |--------------------------------------------------------------------------
                | Split Module + Remaining Namespace
                |--------------------------------------------------------------------------
                */

                $parts = explode('\\', $relative, 2);

                if (count($parts) !== 2) {
                    return;
                }

                [$module, $rest] = $parts;

                /*
                |--------------------------------------------------------------------------
                | Locate Module
                |--------------------------------------------------------------------------
                */

                $basePath = $this->locator->path($module);

                if ($basePath === null) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Build File Path
                |--------------------------------------------------------------------------
                */

                $file = $basePath
                    . '/src/'
                    . str_replace('\\', '/', $rest)
                    . '.php';

                /*
                |--------------------------------------------------------------------------
                | Load Class
                |--------------------------------------------------------------------------
                */

                if (is_file($file)) {
                    require_once $file;
                }

            },
            true,
            true
        );
    }
}