<?php

namespace DeVy\Core\Plugins;

use DeVy\Core\Support\TokenParser;

class PluginAPI
{
    private array $filters = [];
    private array $actions = [];
    private array $renders = [];
    private array $settings = [];
    private array $settingsValues = [];

    protected PluginContext $context;
    protected TokenParser $tokens;


    public function __construct(
        TokenParser $tokens,
        PluginContext $context
    ) {
        $this->tokens = $tokens;
        $this->context = $context;
    }

    public function text(
        string $key,
        mixed $default = null
    ): string {

        return $this->tokens->parse(
            (string) $this->get($key, $default),
            $this->context->all()
        );
    }

    /**
     * -----------------------------------
     * Settings
     * -----------------------------------
     */

    public function settings(array $config): void
    {
        $this->settings = $config;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setValues(array $values): void
    {
        $this->settingsValues = $values;
    }

    public function values(): array
    {
        return $this->settingsValues;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settingsValues[$key] ?? $default;
    }

    /**
     * -----------------------------------
     * FILTERS
     * -----------------------------------
     */

    public function add(
        string $hook,
        callable $callback,
        int $priority = 10
    ): void {

        $this->filters[] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority
        ];
    }

    /**
     * -----------------------------------
     * ACTIONS
     * -----------------------------------
     */

    public function on(
        string $hook,
        callable $callback,
        int $priority = 10
    ): void {

        $this->actions[] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority
        ];
    }

    /**
     * -----------------------------------
     * RENDER HOOKS
     * -----------------------------------
     */

    public function render(
        string $hook,
        callable $callback,
        int $priority = 10
    ): void {

        $this->renders[] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority
        ];
    }

    /**
     * -----------------------------------
     * INTERNAL
     * -----------------------------------
     */

    public function filters(): array
    {
        return $this->filters;
    }

    public function actions(): array
    {
        return $this->actions;
    }

    public function renders(): array
    {
        return $this->renders;
    }
}