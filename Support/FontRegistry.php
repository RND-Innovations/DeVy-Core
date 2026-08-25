<?php

declare(strict_types=1);

namespace DeVy\Core\Support;

class FontRegistry
{
    /*
    |--------------------------------------------------------------------------
    | Body Fonts
    |--------------------------------------------------------------------------
    */

    protected static array $bodyFonts = [

        'system' => 'System UI',

        'sans' => 'Modern Sans',

        'humanist' => 'Humanist Sans',

        'geometric' => 'Geometric Sans',

        'serif' => 'Classic Serif',

        'editorial' => 'Editorial Serif',

        'slab' => 'Slab Serif',

    ];


    /*
    |--------------------------------------------------------------------------
    | Heading Fonts
    |--------------------------------------------------------------------------
    */

    protected static array $headingFonts = [

        'system' => 'System UI',

        'sans' => 'Modern Sans',

        'humanist' => 'Humanist Sans',

        'geometric' => 'Geometric Sans',

        'serif' => 'Classic Serif',

        'editorial' => 'Editorial Serif',

        'display' => 'Display Sans',

        'slab' => 'Slab Serif',

    ];


    /*
    |--------------------------------------------------------------------------
    | Monospace Fonts
    |--------------------------------------------------------------------------
    */

    protected static array $monoFonts = [

        'system' => 'System Monospace',

        'classic' => 'Classic Monospace',

        'technical' => 'Technical Monospace',

    ];


    /*
    |--------------------------------------------------------------------------
    | CSS Font Stacks
    |--------------------------------------------------------------------------
    */

    protected static array $stacks = [

        /*
        |----------------------------------------------------------------------
        | Sans / System
        |----------------------------------------------------------------------
        */

        'system' =>
            'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',

        'sans' =>
            '"Helvetica Neue", Helvetica, Arial, sans-serif',

        'humanist' =>
            '"Trebuchet MS", Arial, sans-serif',

        'geometric' =>
            'Avenir, "Century Gothic", Futura, sans-serif',

        'display' =>
            'Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif',


        /*
        |----------------------------------------------------------------------
        | Serif
        |----------------------------------------------------------------------
        */

        'serif' =>
            'Georgia, "Times New Roman", Times, serif',

        'editorial' =>
            'Baskerville, "Times New Roman", Times, serif',

        'slab' =>
            'Rockwell, "Courier New", Courier, serif',


        /*
        |----------------------------------------------------------------------
        | Monospace
        |----------------------------------------------------------------------
        */

        'mono-system' =>
            'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',

        'mono-classic' =>
            '"Courier New", Courier, monospace',

        'mono-technical' =>
            'Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
    ];


    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    */

    public static function bodyFonts(): array
    {
        return static::$bodyFonts;
    }


    public static function headingFonts(): array
    {
        return static::$headingFonts;
    }


    public static function monoFonts(): array
    {
        return static::$monoFonts;
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Font Stack
    |--------------------------------------------------------------------------
    */

    public static function bodyStack(string $font): string
    {
        return static::$stacks[$font] ?? static::$stacks['system'];
    }


    public static function headingStack(string $font): string
    {
        return static::$stacks[$font] ?? static::$stacks['system'];
    }


    public static function monoStack(string $font): string
    {
        $key = match ($font) {
            'system' => 'mono-system',
            'classic' => 'mono-classic',
            'technical' => 'mono-technical',
            default => 'mono-system',
        };

        return static::$stacks[$key];
    }


    /*
    |--------------------------------------------------------------------------
    | All Stacks
    |--------------------------------------------------------------------------
    */

    public static function stacks(): array
    {
        return static::$stacks;
    }


    public static function get(string $font): ?string
    {
        return static::$stacks[$font] ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | Options With CSS Values
    |--------------------------------------------------------------------------
    */

    public static function bodyOptions(): array
    {
        return [
            'system'     => static::$stacks['system'],
            'sans'       => static::$stacks['sans'],
            'humanist'   => static::$stacks['humanist'],
            'geometric'  => static::$stacks['geometric'],
            'serif'      => static::$stacks['serif'],
            'editorial'  => static::$stacks['editorial'],
            'slab'       => static::$stacks['slab'],
        ];
    }


    public static function headingOptions(): array
    {
        return [
            'system'     => static::$stacks['system'],
            'sans'       => static::$stacks['sans'],
            'humanist'   => static::$stacks['humanist'],
            'geometric'  => static::$stacks['geometric'],
            'serif'      => static::$stacks['serif'],
            'editorial'  => static::$stacks['editorial'],
            'display'    => static::$stacks['display'],
            'slab'       => static::$stacks['slab'],
        ];
    }


    public static function monoOptions(): array
    {
        return [
            'system'    => static::$stacks['mono-system'],
            'classic'   => static::$stacks['mono-classic'],
            'technical' => static::$stacks['mono-technical'],
        ];
    }
}