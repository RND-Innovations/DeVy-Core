<?php

namespace DeVy\Core\Http;

class Response
{
    protected string $content;

    protected int $status;

    protected array $headers = [];

    protected array $cookies = [];

    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function cookie(
        string $name,
        string $value,
        array $options = []
    ): static {

        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
            'options' => $options,
        ];

        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->cookies as $cookie) {

            setcookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['options']
            );
        }

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }

    public static function redirect(
        string $url,
        int $status = 302
    ): static {
        return new static('', $status, [
            'Location' => $url
        ]);
    }

    public static function json(
        mixed $data,
        int $status = 200
    ): static {
        return new static(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            $status,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }
}