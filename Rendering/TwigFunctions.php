<?php

namespace DeVy\Core\Rendering;

use Twig\Environment;
use Twig\TwigFunction;

use DeVy\Core\Application;
use DeVy\Core\Http\Router;
use DeVy\Core\Services\SettingsService;
use DeVy\Core\Security\Csrf;
use DeVy\Core\Security\Honeypot;
use DeVy\Core\Security\FormTimer;
use DeVy\Core\Services\HookManager;
use DeVy\Core\Services\PermissionService;
use DeVy\Core\Support\FontRegistry;

class TwigFunctions
{
    public function __construct(
        private HookManager $hooks,
        private PermissionService $permissions,
        private NavigationService $navigation,
        private SettingsService $settings
    ) {}

    public function register(Environment $twig): void
    {
        // ---------------- HOOKS ----------------
        $twig->addFunction(new TwigFunction('hook', function ($name, $default = []) {
            return $this->hooks->dispatch($name, $default);
        }));

        $twig->addFunction(new TwigFunction('hook_render', function ($name, $context = []) {
            return $this->hooks->render($name, $context);
        }, ['is_safe' => ['html']]));

        $twig->addFunction(
            new TwigFunction(
                'hook_render_first',
                function ($name, $context = []) {
                    return $this->hooks->renderFirst($name, $context);
                },
                ['is_safe' => ['html']]
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'font_stack',
                function (string $type, string $font): string {

                    return match ($type) {

                        'body' => FontRegistry::bodyStack($font),

                        'heading' => FontRegistry::headingStack($font),

                        'mono' => FontRegistry::monoStack($font),

                        default => FontRegistry::get('system')
                            ?? 'system-ui, sans-serif',
                    };
                },
                [
                    'is_safe' => ['html'],
                ]
            )
        );


        // ---------------- CSRF ----------------
        $twig->addFunction(new TwigFunction('csrf_token', function () {
            return app(Csrf::class)->token();
        }));

        $twig->addFunction(new TwigFunction('csrf_field', function () {
            $token = app(Csrf::class)->token();

            return '<input type="hidden" name="_token" value="'
                . htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
                . '">';
        }, ['is_safe' => ['html']]));


        // ---------------- Honeypot ----------------
        $twig->addFunction(new TwigFunction('honeypot_field', function () {
            $name = app(Honeypot::class)->name();
            $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            return '<input name="'.$name.'" value="" style="height:0;padding:0;border:0;opacity:0;position:absolute;pointer-events:none;">';
        }, ['is_safe' => ['html']]));


        // ---------------- FORM TIMER ----------------
        $twig->addFunction(new TwigFunction('form_timer_field', function () {

            app(FormTimer::class)->start();

            return '<input type="hidden" name="_form_timer" value="1">';

        }, ['is_safe' => ['html']]));


        // ---------------- ROUTE ----------------
        $twig->addFunction(new TwigFunction('route', function ($name, $params = []) {
            return Application::getInstance()
                ->make(Router::class)
                ->route($name, $params);
        }));

        // ---------------- NAVIGATION ----------------
        $twig->addFunction(new TwigFunction('navigation', function () {
            return $this->navigation->build();
        }));

        // ---------------- HELPERS ----------------
        $twig->addFunction(new TwigFunction('php_ini', fn($k) => ini_get($k)));
        $twig->addFunction(new TwigFunction('icon', 'icon', ['is_safe' => ['html']]));


        $twig->addFunction(
            new TwigFunction(
                'money',
                fn (
                    string|int|float|null $amount,
                    string $currency = 'USD'
                ) => money(
                    $amount ?? 0,
                    $currency
                )
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'currency_name',
                fn ($code)
                    => currency_name($code)
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'currency_symbol',
                fn ($code)
                    => currency_symbol($code)
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'money_short',
                fn (
                    string|int|float|null $amount,
                    string $currency = 'USD',
                    int $decimals = 1
                ) => money_short($amount ?? 0, $currency, $decimals)
            )
        );
        
        $twig->addFunction(
            new TwigFunction(
                'number',
                fn (
                    int|float|null $number,
                    int $decimals = 0
                ) => number_format(
                    (float) ($number ?? 0),
                    $decimals
                )
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'number_short',
                fn(
                    int|float|string|null $number,
                    int $decimals = 1
                ) => number_short(
                    $number ?? 0,
                    $decimals
                )
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'percent',
                fn (
                    int|float|null $number,
                    int $decimals = 1
                ) => number_format(
                    (float) ($number ?? 0),
                    $decimals
                ) . '%'
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'yesno',
                fn ($value) => $value ? 'Yes' : 'No'
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'json',
                fn ($value) => json_encode(
                    $value,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            )
        );



        // Date Time Magic Functions

        $twig->addFunction(
            new TwigFunction(
                'site_date',
                function (
                    int|string|null $timestamp,
                    ?string $format = null
                ): string {

                    if ($timestamp === null || $timestamp === '') {
                        return '';
                    }

                    $format ??= $this->settings->get(
                        'site.date_format',
                        'Y-m-d'
                    );

                    $timezone = $this->settings->get(
                        'site.timezone',
                        'UTC'
                    );

                    try {

                        return (new \DateTimeImmutable(
                            '@' . (int) $timestamp
                        ))
                            ->setTimezone(
                                new \DateTimeZone($timezone)
                            )
                            ->format($format);

                    } catch (\Throwable) {

                        return '';
                    }
                }
            )
        );        
        
        $twig->addFunction(
            new TwigFunction(
                'site_time',
                function (
                    int|string|null $timestamp,
                    ?string $format = null
                ): string {

                    if ($timestamp === null || $timestamp === '') {
                        return '';
                    }

                    $format ??= $this->settings->get(
                        'site.time_format',
                        'H:i'
                    );

                    $timezone = $this->settings->get(
                        'site.timezone',
                        'UTC'
                    );

                    try {

                        return (new \DateTimeImmutable(
                            '@' . (int) $timestamp
                        ))
                            ->setTimezone(
                                new \DateTimeZone($timezone)
                            )
                            ->format($format);

                    } catch (\Throwable) {

                        return '';
                    }
                }
            )
        );

        $twig->addFunction(
            new TwigFunction(
                'site_datetime',
                function (
                    int|string|null $timestamp,
                    ?string $dateFormat = null,
                    ?string $timeFormat = null
                ): string {

                    if ($timestamp === null || $timestamp === '') {
                        return '';
                    }

                    $dateFormat ??= $this->settings->get(
                        'site.date_format',
                        'Y-m-d'
                    );

                    $timeFormat ??= $this->settings->get(
                        'site.time_format',
                        'H:i'
                    );

                    $timezone = $this->settings->get(
                        'site.timezone',
                        'UTC'
                    );

                    try {

                        return (new \DateTimeImmutable(
                            '@' . (int) $timestamp
                        ))
                            ->setTimezone(
                                new \DateTimeZone($timezone)
                            )
                            ->format(
                                $dateFormat . ' ' . $timeFormat
                            );

                    } catch (\Throwable) {

                        return '';
                    }
                }
            )
        );


    }
}