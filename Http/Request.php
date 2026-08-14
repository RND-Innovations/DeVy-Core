<?php

namespace DeVy\Core\Http;

use DeVy\Core\Contracts\Session\SessionInterface;

class Request
{
    protected array $get;

    protected array $post;

    protected array $server;

    protected array $cookies;

    protected array $files;

    protected SessionInterface $session;

    protected array $attributes = [];

    public function __construct(
        array $get,
        array $post,
        array $server,
        array $cookies,
        array $files,
        SessionInterface $session
    ) {
        $this->get = $get;

        $this->post = $post;

        $this->server = $server;

        $this->cookies = $cookies;

        $this->files = $files;

        $this->session = $session;
    }

    /**
     * ----------------------------------------
     * Capture Request
     * ----------------------------------------
     */
    public static function capture(
        SessionInterface $session
    ): static {

        return new static(
            $_GET,
            $_POST,
            $_SERVER,
            $_COOKIE,
            $_FILES,
            $session
        );
    }

    /**
     * ----------------------------------------
     * HTTP Method
     * ----------------------------------------
     */
    public function method(): string
    {
        return strtoupper(
            $this->server['REQUEST_METHOD'] ?? 'GET'
        );
    }

    public function secure(): bool
    {
        return (
            ($this->server['HTTPS'] ?? '') === 'on'
            || ($this->server['HTTPS'] ?? '') === '1'
            || strtolower(
                $this->header(
                    'X-Forwarded-Proto',
                    ''
                )
            ) === 'https'
        );
    }

    /**
     * ----------------------------------------
     * Request URI
     * ----------------------------------------
     */
    public function uri(): string
    {
        $uri = parse_url(
            $this->server['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        return rtrim($uri, '/') ?: '/';
    }

    /**
     * ----------------------------------------
     * Full URL
     * ----------------------------------------
     */
    public function fullUrl(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * ----------------------------------------
     * All Input
     * ----------------------------------------
     */
    public function all(): array
    {
        return array_merge(
            $this->get,
            $this->post
        );
    }

    /**
     * ----------------------------------------
     * Input
     * ----------------------------------------
     */
    public function input(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        $data = $this->all();

        if ($key === null) {
            return $data;
        }

        return $this->getValue(
            $data,
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * Query Parameters
     * ----------------------------------------
     */
    public function query(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        if ($key === null) {
            return $this->get;
        }

        return $this->getValue(
            $this->get,
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * POST Parameters
     * ----------------------------------------
     */
    public function post(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        if ($key === null) {
            return $this->post;
        }

        return $this->getValue(
            $this->post,
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * Cookies
     * ----------------------------------------
     */
    public function cookie(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        if ($key === null) {
            return $this->cookies;
        }

        return $this->getValue(
            $this->cookies,
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * Files
     * ----------------------------------------
     */
    public function file(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        if ($key === null) {
            return $this->files;
        }

        return $this->getValue(
            $this->files,
            $key,
            $default
        );
    }

    /**
     * ----------------------------------------
     * Json
     * ----------------------------------------
     */
    public function json(): array
    {
        $raw = file_get_contents('php://input');

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * ----------------------------------------
     * Headers
     * ----------------------------------------
     */
    public function header(
        string $key,
        mixed $default = null
    ): mixed {

        $key = 'HTTP_' . strtoupper(
            str_replace('-', '_', $key)
        );

        return $this->server[$key] ?? $default;
    }

    /**
     * ----------------------------------------
     * Client IP
     * ----------------------------------------
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    /**
     * ----------------------------------------
     * User Agent
     * ----------------------------------------
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT']
            ?? '';
    }

    /**
     * ----------------------------------------
     * Referrer
     * ----------------------------------------
     */
    public function referer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * ----------------------------------------
     * Session
     * ----------------------------------------
     */
    public function session(): SessionInterface
    {
        return $this->session;
    }

    /**
     * ----------------------------------------
     * Check Method
     * ----------------------------------------
     */
    public function isMethod(
        string $method
    ): bool {

        return $this->method()
            === strtoupper($method);
    }

    /**
     * ----------------------------------------
     * AJAX Request
     * ----------------------------------------
     */
    public function ajax(): bool
    {
        return strtolower(
            $this->header(
                'X-Requested-With',
                ''
            )
        ) === 'xmlhttprequest';
    }

    /**
     * ----------------------------------------
     * Has Input
     * ----------------------------------------
     */
    public function has(string $key): bool
    {
        return $this->input($key) !== null;
    }

    /**
     * ----------------------------------------
     * Filled
     * ----------------------------------------
     */
    public function filled(string $key): bool
    {
        $value = $this->input($key);

        return !empty($value);
    }

    /**
     * ----------------------------------------
     * Only Keys
     * ----------------------------------------
     */
    public function only(array $keys): array
    {
        $results = [];

        foreach ($keys as $key) {

            $results[$key] = $this->input($key);
        }

        return $results;
    }

    /**
     * ----------------------------------------
     * Except Keys
     * ----------------------------------------
     */
    public function except(array $keys): array
    {
        $data = $this->all();

        foreach ($keys as $key) {

            unset($data[$key]);
        }

        return $data;
    }

    /**
     * ----------------------------------------
     * Dot Notation Getter
     * ----------------------------------------
     */
    protected function getValue(
        array $data,
        string $key,
        mixed $default = null
    ): mixed {

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        foreach (explode('.', $key) as $segment) {

            if (
                !is_array($data) ||
                !array_key_exists($segment, $data)
            ) {
                return $default;
            }

            $data = $data[$segment];
        }

        return $data;
    }


    public function setAttribute(
        string $key,
        mixed $value
    ): void {
        $this->attributes[$key] = $value;
    }

    public function attribute(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        if ($key === null) {
            return $this->attributes;
        }

        return $this->attributes[$key]
            ?? $default;
    }
    
}