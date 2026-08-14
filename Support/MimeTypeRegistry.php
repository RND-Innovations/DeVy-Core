<?php

namespace DeVy\Core\Support;

class MimeTypeRegistry
{
    protected static array $types = [

        'image/jpeg' => [
            'mime' => 'image/jpeg',
            'name' => 'JPEG Image',
            'extensions' => ['jpg', 'jpeg'],
            'category' => 'image',
        ],

        'image/png' => [
            'mime' => 'image/png',
            'name' => 'PNG Image',
            'extensions' => ['png'],
            'category' => 'image',
        ],

        'image/gif' => [
            'mime' => 'image/gif',
            'name' => 'GIF Image',
            'extensions' => ['gif'],
            'category' => 'image',
        ],

        'image/webp' => [
            'mime' => 'image/webp',
            'name' => 'WebP Image',
            'extensions' => ['webp'],
            'category' => 'image',
        ],

        'image/svg+xml' => [
            'mime' => 'image/svg+xml',
            'name' => 'SVG Image',
            'extensions' => ['svg'],
            'category' => 'image',
        ],

        'application/pdf' => [
            'mime' => 'application/pdf',
            'name' => 'PDF Document',
            'extensions' => ['pdf'],
            'category' => 'document',
        ],

        'text/plain' => [
            'mime' => 'text/plain',
            'name' => 'Text File',
            'extensions' => ['txt'],
            'category' => 'document',
        ],

        'text/csv' => [
            'mime' => 'text/csv',
            'name' => 'CSV File',
            'extensions' => ['csv'],
            'category' => 'document',
        ],

        'application/json' => [
            'mime' => 'application/json',
            'name' => 'JSON File',
            'extensions' => ['json'],
            'category' => 'document',
        ],

        'application/xml' => [
            'mime' => 'application/xml',
            'name' => 'XML File',
            'extensions' => ['xml'],
            'category' => 'document',
        ],

        'text/html' => [
            'mime' => 'text/html',
            'name' => 'HTML Document',
            'extensions' => ['html', 'htm'],
            'category' => 'document',
        ],

        'application/zip' => [
            'mime' => 'application/zip',
            'name' => 'ZIP Archive',
            'extensions' => ['zip'],
            'category' => 'archive',
        ],

        'application/x-rar-compressed' => [
            'mime' => 'application/x-rar-compressed',
            'name' => 'RAR Archive',
            'extensions' => ['rar'],
            'category' => 'archive',
        ],

        'application/x-7z-compressed' => [
            'mime' => 'application/x-7z-compressed',
            'name' => '7-Zip Archive',
            'extensions' => ['7z'],
            'category' => 'archive',
        ],

        'audio/mpeg' => [
            'mime' => 'audio/mpeg',
            'name' => 'MP3 Audio',
            'extensions' => ['mp3'],
            'category' => 'audio',
        ],

        'audio/wav' => [
            'mime' => 'audio/wav',
            'name' => 'WAV Audio',
            'extensions' => ['wav'],
            'category' => 'audio',
        ],

        'audio/ogg' => [
            'mime' => 'audio/ogg',
            'name' => 'OGG Audio',
            'extensions' => ['ogg'],
            'category' => 'audio',
        ],

        'video/mp4' => [
            'mime' => 'video/mp4',
            'name' => 'MP4 Video',
            'extensions' => ['mp4'],
            'category' => 'video',
        ],

        'video/webm' => [
            'mime' => 'video/webm',
            'name' => 'WebM Video',
            'extensions' => ['webm'],
            'category' => 'video',
        ],

        'video/quicktime' => [
            'mime' => 'video/quicktime',
            'name' => 'QuickTime Video',
            'extensions' => ['mov'],
            'category' => 'video',
        ],

        'application/msword' => [
            'mime' => 'application/msword',
            'name' => 'Word Document',
            'extensions' => ['doc'],
            'category' => 'office',
        ],

        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'name' => 'Word Document',
            'extensions' => ['docx'],
            'category' => 'office',
        ],

        'application/vnd.ms-excel' => [
            'mime' => 'application/vnd.ms-excel',
            'name' => 'Excel Spreadsheet',
            'extensions' => ['xls'],
            'category' => 'office',
        ],

        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'name' => 'Excel Spreadsheet',
            'extensions' => ['xlsx'],
            'category' => 'office',
        ],

        'application/vnd.ms-powerpoint' => [
            'mime' => 'application/vnd.ms-powerpoint',
            'name' => 'PowerPoint Presentation',
            'extensions' => ['ppt'],
            'category' => 'office',
        ],

        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'name' => 'PowerPoint Presentation',
            'extensions' => ['pptx'],
            'category' => 'office',
        ],

    ];

    public static function all(): array
    {
        return static::$types;
    }

    public static function get(string $mime): ?array
    {
        return static::$types[$mime] ?? null;
    }

    public static function exists(string $mime): bool
    {
        return isset(static::$types[$mime]);
    }

    public static function name(string $mime): ?string
    {
        return static::get($mime)['name'] ?? null;
    }

    public static function category(string $mime): ?string
    {
        return static::get($mime)['category'] ?? null;
    }

    public static function extensions(string $mime): array
    {
        return static::get($mime)['extensions'] ?? [];
    }

    public static function categories(): array
    {
        return array_unique(
            array_column(static::$types, 'category')
        );
    }

    public static function byCategory(string $category): array
    {
        return array_filter(
            static::$types,
            fn ($type) => $type['category'] === $category
        );
    }

    public static function fromExtension(string $extension): ?string
    {
        $extension = strtolower(ltrim($extension, '.'));

        foreach (static::$types as $mime => $data) {
            if (in_array($extension, $data['extensions'], true)) {
                return $mime;
            }
        }

        return null;
    }

    public static function isImage(string $mime): bool
    {
        return static::category($mime) === 'image';
    }

    public static function isVideo(string $mime): bool
    {
        return static::category($mime) === 'video';
    }

    public static function isAudio(string $mime): bool
    {
        return static::category($mime) === 'audio';
    }

    public static function isDocument(string $mime): bool
    {
        return static::category($mime) === 'document';
    }

    public static function options(): array
    {
        $options = [];

        foreach (static::$types as $mime => $type) {
            $options[$mime] = $type['name'];
        }

        return $options;
    }
}