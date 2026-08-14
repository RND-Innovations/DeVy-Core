<?php

declare(strict_types=1);

namespace DeVy\Core\Services;

use DeVy\Core\Schema\SchemaDefaults;
use DeVy\Core\Persistence\JsonStore;
use DeVy\Core\Contracts\Store\StoreInterface;

class ConfigService implements StoreInterface
{
    protected JsonStore $store;

    protected HookManager $hooks;

    public function __construct(
        PathService $paths,
        HookManager $hooks
    ) {
        $this->hooks = $hooks;

        $this->store = new JsonStore(
            $paths->system('config.json')
        );

        $schema = $hooks->dispatch(
            'config.schema',
            []
        );

        (new SchemaDefaults)->apply(
            $this->store,
            $hooks->dispatch(
                'config.schema',
                []
            )
        );

        $data = $this->appendRuntime(
            $this->store->all(),
            $paths
        );

        $this->store->replace($data);
    }

    /**
     * ----------------------------------------
     * Get
     * ----------------------------------------
     */
    public function get(
        string $key = '',
        mixed $default = null
    ): mixed {

        return $this->store->get(
            $key,
            $default
        );

    }

    /**
     * ----------------------------------------
     * Set
     * ----------------------------------------
     */
    public function set(
        string $key,
        mixed $value
    ): void {

        $this->store->set(
            $key,
            $value
        );

    }

    /**
     * ----------------------------------------
     * Has
     * ----------------------------------------
     */
    public function has(
        string $key
    ): bool {

        return $this->store->has($key);

    }

    /**
     * ----------------------------------------
     * All
     * ----------------------------------------
     */
    public function all(): array
    {
        return $this->store->all();
    }

    /**
     * ----------------------------------------
     * Save
     * ----------------------------------------
     */
    public function save(): bool
    {
        return $this->store->save();
    }


    /**
     * ----------------------------------------
     * Append Runtime Values
     * ----------------------------------------
     */
    protected function appendRuntime(
        array $config,
        PathService $paths
    ): array {

        $composer = [];

        $composerFile = $paths->base() . '/composer.json';

        if (is_file($composerFile)) {

            $composer = json_decode(
                file_get_contents($composerFile),
                true
            ) ?? [];

        }

        $https = $_SERVER['HTTPS'] ?? null;

        $forwarded =
            $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;

        $scheme =
            ($https === 'on' || $forwarded === 'https')
                ? 'https://'
                : 'http://';

        $host =
            $_SERVER['HTTP_HOST'] ?? 'localhost';

        $url = rtrim(
            $scheme . $host,
            '/'
        );

        $appCode = env(
            'APP_CODE',
            'DeVy'
        );

        $adminSlug =
            $config['admin']['slug'] ?? $appCode;

        $config['app'] = [

            'env' => env(
                'APP_ENV',
                'production'
            ),

            'debug' => env(
                'APP_DEBUG',
                false
            ),

            'code' => $appCode,

            'url' => $url,

            'url_admin' =>
                $url . '/' . $adminSlug,

            'version' =>
                $composer['version'] ?? '1.0.0',

            'name' =>
                $composer['description'] ?? 'DeVy',

        ];

        return $config;
    }

}