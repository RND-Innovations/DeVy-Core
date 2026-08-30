<?php

namespace DeVy\Core\Modules;

use DeVy\Core\Application;
use DeVy\Core\Services\PathService;
use RuntimeException;
use Throwable;

final class ModuleLocator
{
    private array $modules = [];

    public function __construct(
        private Application $app,
        private PathService $paths
    ) {}

    /**
     * ---------------------------------------------------------
     * Discover Modules
     * ---------------------------------------------------------
     */
    public function discover(): void
    {
        $this->modules = [];

        $this->scanLocation(
            basePath: $this->paths->coreModules(),
            source: 'core'
        );

        $this->scanLocation(
            basePath: $this->paths->modules(),
            source: 'site'
        );
    }

    /**
     * ---------------------------------------------------------
     * All Modules
     * ---------------------------------------------------------
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * ---------------------------------------------------------
     * Has Module
     * ---------------------------------------------------------
     */
    public function has(string $module): bool
    {
        return isset($this->modules[$module]);
    }

    /**
     * ---------------------------------------------------------
     * Get Module
     * ---------------------------------------------------------
     */
    public function get(string $module): ?array
    {
        return $this->modules[$module] ?? null;
    }

    /**
     * ---------------------------------------------------------
     * Module Path
     * ---------------------------------------------------------
     */
    public function path(string $module): ?string
    {
        return $this->modules[$module]['path'] ?? null;
    }

    /**
     * ---------------------------------------------------------
     * Module Definition
     * ---------------------------------------------------------
     */
    public function definition(string $module): ?array
    {
        return $this->modules[$module]['definition'] ?? null;
    }

    /**
     * ---------------------------------------------------------
     * Module Source
     * ---------------------------------------------------------
     */
    public function source(string $module): ?string
    {
        return $this->modules[$module]['source'] ?? null;
    }

    /**
     * ---------------------------------------------------------
     * Scan One Location
     * ---------------------------------------------------------
     */
    private function scanLocation(
        string $basePath,
        string $source
    ): void {

        if (!is_dir($basePath)) {
            return;
        }

        foreach (scandir($basePath) as $module) {

            if ($module === '.' || $module === '..') {
                continue;
            }

            $modulePath = $basePath . '/' . $module;

            if (!is_dir($modulePath)) {
                continue;
            }

            $bootFile = $modulePath . '/module.php';

            if (!is_file($bootFile)) {
                continue;
            }

            try {

                $definition = require $bootFile;

                if (!is_array($definition)) {
                    throw new RuntimeException(
                        "Module [$module] did not return an array."
                    );
                }

            } catch (Throwable $e) {

                throw new RuntimeException(
                    ucfirst($source)
                    . " module [$module] failed: "
                    . $e->getMessage(),
                    0,
                    $e
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Conflict Detection
            |--------------------------------------------------------------------------
            */

            if (isset($this->modules[$module])) {

                if ($source !== 'site') {
                    throw new RuntimeException(
                        "Duplicate module [$module]."
                    );
                }

                $meta = $definition['meta'] ?? [];

                if (empty($meta['override'])) {
                    throw new RuntimeException(
                        "Module conflict: [$module] exists in Core and Site.\n"
                        . "Add 'override' => true in the site module."
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Register / Override
            |--------------------------------------------------------------------------
            */

            $this->modules[$module] = [

                'name'       => $module,

                'definition' => $definition,

                'path'       => $modulePath,

                'source'     => $source,

            ];
        }
    }
}