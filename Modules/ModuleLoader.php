<?php

namespace DeVy\Core\Modules;

use Throwable;
use RuntimeException;

use DeVy\Core\Application;

final class ModuleLoader
{
    private array $bootQueue = [];

    private array $loadedModules = [];

    private array $report = [];

    private array $errors = [];

    public function __construct(
        private Application $app,
        private ModuleLocator $locator,
        private PackageAutoloader $autoloader
    ) {}

    /**
     * ---------------------------------------------------------
     * Load Modules
     * ---------------------------------------------------------
     */
    public function load(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Discover Modules
        |--------------------------------------------------------------------------
        */

        $this->locator->discover();

        /*
        |--------------------------------------------------------------------------
        | Register Runtime Autoloader
        |--------------------------------------------------------------------------
        */

        $this->autoloader->register();

        /*
        |--------------------------------------------------------------------------
        | Build Report
        |--------------------------------------------------------------------------
        */

        foreach ($this->locator->all() as $module => $data) {

            $meta = $data['definition']['meta'] ?? [];

            $this->report[$module] = [

                /*
                |--------------------------------------------------------------------------
                | Identity
                |--------------------------------------------------------------------------
                */

                'module'      => $module,
                'name'        => $meta['name'] ?? $module,
                'description' => $meta['description'] ?? '',
                'icon'        => $meta['icon'] ?? 'package',

                /*
                |--------------------------------------------------------------------------
                | Package
                |--------------------------------------------------------------------------
                */

                'version'     => $meta['version'] ?? '1.0.0',
                'author'      => $meta['author'] ?? '',
                'website'     => $meta['website'] ?? '',
                'license'     => $meta['license'] ?? '',

                /*
                |--------------------------------------------------------------------------
                | Runtime
                |--------------------------------------------------------------------------
                */

                'namespace'   => $meta['namespace'] ?? '',
                'status'      => 'pending',
                'source'      => $data['source'],
                'path'        => $data['path'],

                /*
                |--------------------------------------------------------------------------
                | Compatibility
                |--------------------------------------------------------------------------
                */

                'requires'    => $meta['requires'] ?? [],

                /*
                |--------------------------------------------------------------------------
                | Optional
                |--------------------------------------------------------------------------
                */

                'override'    => $meta['override'] ?? false,

            ];

        }

        $this->registerResolved();

        $this->boot();
    }

    /**
     * ---------------------------------------------------------
     * Helpers
     * ---------------------------------------------------------
     */

    public function has(string $module): bool
    {
        return $this->locator->has($module);
    }

    public function getModulePath(string $module): ?string
    {
        return $this->locator->path($module);
    }

    public function report(): array
    {
        return array_values($this->report);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * ---------------------------------------------------------
     * Register Modules
     * ---------------------------------------------------------
     */

    private function registerResolved(): void
    {
        $remaining = $this->locator->all();

        while (!empty($remaining)) {

            $progress = false;

            $context = new ModuleContext($this->app);

            foreach ($remaining as $module => $data) {

                try {

                    $definition = $data['definition'];

                    if (!is_array($definition)) {
                        unset($remaining[$module]);
                        continue;
                    }

                    $meta = $definition['meta'] ?? [];

                    $this->validateNamespace(
                        $module,
                        $meta
                    );

                    if (
                        !$this->dependenciesSatisfied($meta)
                    ) {
                        continue;
                    }

                    $this->validate(
                        $module,
                        $meta
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Register
                    |--------------------------------------------------------------------------
                    */

                    if (
                        isset($definition['register']) &&
                        is_callable($definition['register'])
                    ) {
                        $definition['register']($context);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Queue Boot
                    |--------------------------------------------------------------------------
                    */

                    if (
                        isset($definition['boot']) &&
                        is_callable($definition['boot'])
                    ) {
                        $this->bootQueue[] = [
                            $module,
                            $definition['boot']
                        ];
                    }

                    $this->loadedModules[$module] = true;

                    $this->report[$module]['status'] = 'registered';

                    unset($remaining[$module]);

                    $progress = true;

                } catch (Throwable $e) {

                    $this->fail($module, $e);

                    $this->report[$module]['status'] = 'failed';

                    unset($remaining[$module]);
                }
            }

            if (!$progress) {

                throw new RuntimeException(
                    'Unresolved or circular module dependencies: '
                    . implode(', ', array_keys($remaining))
                );

            }
        }
    }

    /**
     * ---------------------------------------------------------
     * Validation
     * ---------------------------------------------------------
     */

    private function validateNamespace(
        string $module,
        array $meta
    ): void {

        $namespace = $meta['namespace'] ?? null;

        if (!$namespace) {
            throw new RuntimeException(
                "Module [$module] missing namespace."
            );
        }

        if (!str_starts_with(
            $namespace,
            'DeVy\\Modules\\'
        )) {

            throw new RuntimeException(
                "Module [$module] namespace must begin with DeVy\\Modules\\"
            );

        }
    }

    private function dependenciesSatisfied(
        array $meta
    ): bool {

        foreach (
            $meta['requires']['modules'] ?? []
            as $dependency
        ) {

            if (!isset(
                $this->loadedModules[$dependency]
            )) {
                return false;
            }

        }

        return true;
    }

    private function validate(
        string $module,
        array $meta
    ): void {

        if (isset(
            $meta['requires']['framework']
        )) {

            if (
                !$this->satisfies(
                    $this->app->version(),
                    $meta['requires']['framework']
                )
            ) {

                throw new RuntimeException(
                    "Module [$module] requires framework {$meta['requires']['framework']}"
                );

            }
        }

        if (isset(
            $meta['requires']['php']
        )) {

            if (
                !version_compare(
                    PHP_VERSION,
                    ltrim($meta['requires']['php'], '^'),
                    '>='
                )
            ) {

                throw new RuntimeException(
                    "Module [$module] requires PHP {$meta['requires']['php']}"
                );

            }
        }
    }

    private function satisfies(
        string $version,
        string $constraint
    ): bool {

        return str_starts_with(
            $version,
            ltrim($constraint, '^')
        );
    }

    private function fail(
        string $module,
        Throwable $e
    ): void {

        $this->errors[] = [

            'module'  => $module,
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),

        ];

    }

    /**
     * ---------------------------------------------------------
     * Boot Modules
     * ---------------------------------------------------------
     */

    private function boot(): void
    {
        $context = new ModuleContext(
            $this->app
        );

        foreach ($this->bootQueue as [$module, $boot]) {

            try {

                $boot($context);

                $this->report[$module]['status'] = 'booted';

            } catch (Throwable $e) {

                $this->fail(
                    $module,
                    $e
                );

                $this->report[$module]['status'] = 'failed';

            }
        }
    }

    public function module(string $name): ?array
    {
        foreach ($this->report() as $module) {

            if ($module['module'] === $name) {
                return $module;
            }

        }

        return null;
    }
    
}