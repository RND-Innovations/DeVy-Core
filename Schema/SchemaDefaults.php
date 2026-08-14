<?php

declare(strict_types=1);

namespace DeVy\Core\Schema;

use DeVy\Core\Contracts\Store\StoreInterface;

class SchemaDefaults
{
    /**
     * ----------------------------------------
     * Apply Defaults
     * ----------------------------------------
     */
    public function apply(
        StoreInterface $store,
        array $schema
    ): void {

        foreach ($schema as $section) {

            foreach ($section['fields'] ?? [] as $field) {

                if (!isset($field['path'])) {
                    continue;
                }

                if (!$store->has($field['path'])) {

                    $store->set(
                        $field['path'],
                        $field['default'] ?? null
                    );

                }

            }

        }

    }
}