<?php

declare(strict_types=1);

namespace DeVy\Core\Support;

class DesignRegistry
{
    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    */

    protected static array $logoTypes = [
        'text'  => 'Text Logo',
        'image' => 'Image Logo',
        'both'  => 'Image & Text',
    ];


    /*
    |--------------------------------------------------------------------------
    | Direction
    |--------------------------------------------------------------------------
    */

    protected static array $directions = [
        'ltr' => 'Left to Right',
        'rtl' => 'Right to Left',
    ];


    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    protected static array $layoutWidths = [
        'boxed' => 'Boxed',
        'full'  => 'Full Width',
    ];


    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    protected static array $headerBehaviors = [
        'normal' => 'Normal',
        'sticky' => 'Sticky',
        'fixed'  => 'Fixed',
    ];

    protected static array $headerHeights = [
        '64px' => 'Compact — 64px',
        '72px' => 'Standard — 72px',
        '80px' => 'Comfortable — 80px',
        '96px' => 'Large — 96px',
    ];


    /*
    |--------------------------------------------------------------------------
    | Typography
    |--------------------------------------------------------------------------
    */

    protected static array $fontSizes = [
        '14px' => 'Small — 14px',
        '16px' => 'Standard — 16px',
        '18px' => 'Large — 18px',
        '20px' => 'Extra Large — 20px',
    ];

    protected static array $lineHeights = [
        '1.4'  => 'Tight — 1.4',
        '1.5'  => 'Compact — 1.5',
        '1.6'  => 'Standard — 1.6',
        '1.75' => 'Relaxed — 1.75',
        '1.9'  => 'Loose — 1.9',
    ];


    /*
    |--------------------------------------------------------------------------
    | Spacing
    |--------------------------------------------------------------------------
    */

    protected static array $sectionSpacings = [
        '3rem' => 'Compact — 3rem',
        '4rem' => 'Moderate — 4rem',
        '5rem' => 'Standard — 5rem',
        '6rem' => 'Large — 6rem',
    ];

    protected static array $contentGaps = [
        '1rem'   => 'Compact — 1rem',
        '1.5rem' => 'Moderate — 1.5rem',
        '2rem'   => 'Standard — 2rem',
        '2.5rem' => 'Large — 2.5rem',
        '3rem'   => 'Wide — 3rem',
    ];

    protected static array $gutters = [
        '1rem'    => 'Compact — 1rem',
        '1.25rem' => 'Standard — 1.25rem',
        '1.5rem'  => 'Comfortable — 1.5rem',
        '2rem'    => 'Wide — 2rem',
    ];


    /*
    |--------------------------------------------------------------------------
    | Public Options
    |--------------------------------------------------------------------------
    */

    public static function logoTypes(): array
    {
        return static::$logoTypes;
    }

    public static function directions(): array
    {
        return static::$directions;
    }

    public static function layoutWidths(): array
    {
        return static::$layoutWidths;
    }

    public static function headerBehaviors(): array
    {
        return static::$headerBehaviors;
    }

    public static function headerHeights(): array
    {
        return static::$headerHeights;
    }

    public static function fontSizes(): array
    {
        return static::$fontSizes;
    }

    public static function lineHeights(): array
    {
        return static::$lineHeights;
    }

    public static function sectionSpacings(): array
    {
        return static::$sectionSpacings;
    }

    public static function contentGaps(): array
    {
        return static::$contentGaps;
    }

    public static function gutters(): array
    {
        return static::$gutters;
    }
}