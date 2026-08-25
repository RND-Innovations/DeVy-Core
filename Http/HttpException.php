<?php

namespace DeVy\Core\Http;

class HttpException extends \Exception
{
    protected int $status;

    protected array $headers = [];

    protected string $template = '';

    protected array $data = [];

    public function __construct(
        int $status,
        string $message = '',
        array $headers = [],
        ?\Throwable $previous = null,
        string $template = '',
        array $data = []
    ) {
        parent::__construct(
            $message,
            0,
            $previous
        );

        $this->status   = $status;
        $this->headers  = $headers;
        $this->template = $template;
        $this->data     = $data;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getData(): array
    {
        return $this->data;
    }
}