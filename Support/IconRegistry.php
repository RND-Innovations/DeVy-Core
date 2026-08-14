<?php

declare(strict_types=1);

namespace DeVy\Core\Support;

class IconRegistry
{
    protected static ?array $icons = null;

    protected static ?array $categories = null;

    protected static ?array $grouped = null;

    /**
     * Return all icons.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        self::load();

        return self::$icons;
    }

    /**
     * Return a single icon SVG.
     */
    public static function get(string $name): ?string
    {
        self::load();

        return self::$icons[$name] ?? null;
    }

    /**
     * Return available categories.
     *
     * Example:
     * [
     *     'system' => 'System',
     *     'actions' => 'Actions',
     * ]
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        self::load();

        return self::$categories;
    }

    /**
     * Return icons grouped by category.
     *
     * Example:
     * [
     *     'system' => [
     *         'settings' => '<svg>...</svg>',
     *     ],
     *     'actions' => [
     *         'refresh' => '<svg>...</svg>',
     *     ],
     * ]
     *
     * @return array<string, array<string, string>>
     */
    public static function iconsByCategory(): array
    {
        self::load();

        return self::$grouped;
    }

    /**
     * Load icons once.
     */
    protected static function load(): void
    {
        if (self::$icons !== null) {
            return;
        }

        self::$icons = [];
        self::$categories = [];
        self::$grouped = [];

        $files = glob(__DIR__ . '/Icons/*.php') ?: [];

        sort($files, SORT_NATURAL);

        foreach ($files as $file) {

            $filename = pathinfo($file, PATHINFO_FILENAME);

            // 01_System -> System
            $label = preg_replace('/^\d+_/', '', $filename) ?: $filename;

            $key = strtolower($label);

            self::$categories[$key] = $label;

            $icons = require $file;

            if (!is_array($icons)) {
                continue;
            }

            foreach ($icons as $icon => $svg) {

                $svg = trim((string) $svg);

                // Ignore placeholders
                if ($svg === '') {
                    continue;
                }

                // Prevent duplicate icon names
                if (isset(self::$icons[$icon])) {
                    continue;
                }

                self::$icons[$icon] = $svg;
                self::$grouped[$key][$icon] = $svg;
            }
        }
    }
}