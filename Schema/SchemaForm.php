<?php

namespace DeVy\Core\Schema;

use DeVy\Core\Services\HookManager;
use DeVy\Core\Services\CryptoService;
use DeVy\Core\Contracts\Store\StoreInterface;

class SchemaForm
{
    public function __construct(
        protected HookManager $hooks,
        protected CryptoService $crypto
    ) {}

    /**
     * Build schema ready for Twig.
     */
    public function build(
        StoreInterface $store,
        string $hook
    ): array {

        $schema = $this->hooks->dispatch(
            $hook,
            []
        );

        uasort($schema, fn($a, $b) =>
            ($a['order'] ?? 100)
            <=>
            ($b['order'] ?? 100)
        );

        foreach ($schema as &$section) {

            uasort(
                $section['fields'],
                fn($a, $b) =>
                    ($a['order'] ?? 100)
                    <=>
                    ($b['order'] ?? 100)
            );

            foreach ($section['fields'] as &$field) {

                if (($field['stores_value'] ?? true) === false) {
                    continue;
                }

                $value = $store->get(
                    $field['path'],
                    $field['default'] ?? null
                );

                if (
                    ($field['encrypted'] ?? false)
                    && is_string($value)
                    && $value !== ''
                ) {

                    $value = $this->crypto->decrypt($value);

                    if (
                        ($field['write_only'] ?? false)
                    ) {
                        $value = '';
                    }
                }

                /*
                 * textarea_line
                 *
                 * Stored as an array but displayed
                 * in the form as one item per line.
                 */
                if (
                    ($field['type'] ?? '') === 'textarea_line'
                    && is_array($value)
                ) {
                    $value = implode("\n", $value);
                }

                if (
                    isset($field['options_source'])
                ) {
                    $field['options'] = $this->resolveOptionsSource(
                        $field['options_source']
                    );
                }

                $field['value'] = $value;
            }
        }

        unset($section, $field);

        return $schema;
    }

    /**
     * Save submitted form.
     */
    public function save(
        StoreInterface $store,
        string $hook,
        array $input
    ): void {

        $schema = $this->hooks->dispatch(
            $hook,
            []
        );

        foreach ($schema as $sectionKey => $section) {

            if (!isset($input[$sectionKey])) {
                continue;
            }

            foreach ($section['fields'] as $fieldKey => $field) {

                if (!array_key_exists(
                    $fieldKey,
                    $input[$sectionKey]
                )) {
                    continue;
                }

                if (($field['stores_value'] ?? true) === false) {
                    continue;
                }

                $value = $input[$sectionKey][$fieldKey];

                switch ($field['type'] ?? '') {

                    case 'checkbox':

                        $value = (bool)$value;

                        break;

                    case 'number':

                        $value = (int)$value;

                        break;

                    case 'textarea_line':

                        $value = preg_split(
                            '/\r\n|\r|\n/',
                            trim($value)
                        );

                        $value = array_values(
                            array_filter(
                                array_map('trim', $value),
                                fn($v) => $v !== ''
                            )
                        );

                        break;
                }

                if (
                    ($field['encrypted'] ?? false)
                    && is_string($value)
                ) {

                    if ($value === '') {
                        continue;
                    }

                    $value = $this->crypto->encrypt($value);
                }

                $store->set(
                    $field['path'],
                    $value
                );
            }
        }

        $store->save();
    }

    /**
     * ----------------------------------------
     * Resolve Options Source
     * ----------------------------------------
     */
    protected function resolveOptionsSource(
        array $source
    ): array {

        return match ($source['type'] ?? null) {

            'class' => (

                $class = $source['target'] ?? null

            ) && class_exists($class)

                ? $class::{
                    $source['method'] ?? 'options'
                }()

                : [],

            'hook' => $this->hooks->dispatch(
                $source['target'] ?? '',
                []
            ),

            default => [],
        };
    }
}