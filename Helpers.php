<?php

use DeVy\Core\Application;
use DeVy\Core\Support\CurrencyRegistry;
use DeVy\Core\Support\IconRegistry;

function dd(...$vars)
{
    echo '<pre>';
    foreach ($vars as $v) {
        var_dump($v);
    }
    echo '</pre>';
    exit;
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('app')) {
    function app(?string $abstract = null)
    {
        $app = Application::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->make($abstract);
    }
}

if (!function_exists('icon')) {
    function icon($name, $class = 'w-5 h-5')
    {
        $svg = IconRegistry::get($name);

        if (!$svg) {
            return '';
        }

        // Target ONLY the root opening <svg> tag (limit: 1)
        $svg = preg_replace_callback('/<svg[^>]*>/i', function ($matches) use ($class) {
            $tag = $matches[0];

            // Strip width, height, and class from the opening <svg> tag ONLY
            $tag = preg_replace('/\s(width|height|class)="[^"]*"/i', '', $tag);

            // Add the new class attribute to <svg>
            return str_replace('<svg', '<svg class="' . $class . '"', $tag);
        }, $svg, 1);

        return $svg;
    }
}

if (!function_exists('currency')) {

    function currency(?string $code = null)
    {
        if ($code === null) {
            return CurrencyRegistry::all();
        }

        return CurrencyRegistry::get($code);
    }

}

if (!function_exists('currency_name')) {

    function currency_name(string $code): ?string
    {
        return CurrencyRegistry::name($code);
    }

}

if (!function_exists('currency_symbol')) {

    function currency_symbol(string $code): ?string
    {
        return CurrencyRegistry::symbol($code);
    }

}

if (!function_exists('money')) {

    function money(
        float|int|string $amount,
        string $currency = 'USD'
    ): string {

        return CurrencyRegistry::format(
            (float) $amount,
            $currency
        );
    }

}

if (!function_exists('money_short')) {
    function money_short(
        string|int|float|null $amount,
        string $currency = 'USD',
        int $decimals = 1
    ): string {

        $amount = (float)($amount ?? 0);

        $symbol = currency_symbol($currency);

        $negative = $amount < 0;

        $amount = abs($amount);

        $units = [
            1_000_000_000_000 => 'T',
            1_000_000_000     => 'B',
            1_000_000         => 'M',
            1_000             => 'K',
        ];

        foreach ($units as $value => $suffix) {

            if ($amount >= $value) {

                return sprintf(
                    '%s%s%s%s',
                    $negative ? '-' : '',
                    $symbol,
                    rtrim(
                        rtrim(
                            number_format($amount / $value, $decimals),
                            '0'
                        ),
                        '.'
                    ),
                    $suffix
                );

            }

        }

        return sprintf(
            '%s%s%s',
            $negative ? '-' : '',
            $symbol,
            number_format($amount)
        );
    }
}

if (!function_exists('number_short')) {

    function number_short(
        int|float|string|null $number,
        int $decimals = 1
    ): string {

        $number = (float) ($number ?? 0);

        $negative = $number < 0;

        $number = abs($number);

        $units = [
            1_000_000_000_000 => 'T',
            1_000_000_000     => 'B',
            1_000_000         => 'M',
            1_000             => 'K',
        ];

        foreach ($units as $value => $suffix) {

            if ($number >= $value) {

                return sprintf(
                    '%s%s%s',
                    $negative ? '-' : '',
                    rtrim(
                        rtrim(
                            number_format(
                                $number / $value,
                                $decimals
                            ),
                            '0'
                        ),
                        '.'
                    ),
                    $suffix
                );

            }

        }

        return sprintf(
            '%s%s',
            $negative ? '-' : '',
            number_format($number)
        );

    }

}