<?php

namespace DeVy\Core\Http;

class HttpException extends \Exception
{
    protected int $status;

    protected array $headers = [];

    public function __construct(
        int $status,
        string $message = '',
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            0,
            $previous
        );

        $this->status = $status;

        $this->headers = $headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}