<?php

namespace DeVy\Core\Support;

class FileExtensionRegistry
{
    /**
     * Get all extensions
     */
    public static function all(): array
    {
        $extensions = [];

        foreach (MimeTypeRegistry::all() as $mime => $data) {
            foreach ($data['extensions'] as $extension) {
                $extensions[$extension] = [
                    'extension' => $extension,
                    'mime' => $mime,
                    'name' => $data['name'],
                    'category' => $data['category'],
                ];
            }
        }

        ksort($extensions);

        return $extensions;
    }

    /**
     * Get extension details
     */
    public static function get(string $extension): ?array
    {
        $extension = strtolower(
            ltrim($extension, '.')
        );

        return static::all()[$extension] ?? null;
    }

    /**
     * Check if extension exists
     */
    public static function exists(string $extension): bool
    {
        return static::get($extension) !== null;
    }

    /**
     * Get mime type
     */
    public static function mime(string $extension): ?string
    {
        return static::get($extension)['mime'] ?? null;
    }

    /**
     * Get category
     */
    public static function category(string $extension): ?string
    {
        return static::get($extension)['category'] ?? null;
    }

    /**
     * Get display name
     */
    public static function name(string $extension): ?string
    {
        return static::get($extension)['name'] ?? null;
    }

    /**
     * Image extensions
     */
    public static function images(): array
    {
        return static::byCategory('image');
    }

    /**
     * Video extensions
     */
    public static function videos(): array
    {
        return static::byCategory('video');
    }

    /**
     * Audio extensions
     */
    public static function audio(): array
    {
        return static::byCategory('audio');
    }

    /**
     * Document extensions
     */
    public static function documents(): array
    {
        return static::byCategory('document');
    }

    /**
     * Office extensions
     */
    public static function office(): array
    {
        return static::byCategory('office');
    }

    /**
     * Archive extensions
     */
    public static function archives(): array
    {
        return static::byCategory('archive');
    }

    /**
     * Get extensions by category
     */
    public static function byCategory(string $category): array
    {
        return array_filter(
            static::all(),
            fn ($item) => $item['category'] === $category
        );
    }

    /**
     * Get extension list only
     */
    public static function extensionList(): array
    {
        return array_keys(
            static::all()
        );
    }

    /**
     * Get extension list for category
     */
    public static function extensionListByCategory(
        string $category
    ): array {
        return array_keys(
            static::byCategory($category)
        );
    }

    /**
     * Select options
     */
    public static function options(): array
    {
        $options = [];

        foreach (static::all() as $extension => $data) {
            $options[$extension] = strtoupper($extension);
        }

        return $options;
    }
}