<?php

namespace DeVy\Core\Security;

use DeVy\Core\Contracts\Session\SessionInterface;

class Honeypot
{
    private string $sessionKey = '_honeypot_name';

    public function __construct(
        private SessionInterface $session
    ) {}

    public function name(): string
    {
        if (!$this->session->has($this->sessionKey)) {
            $this->regenerate();
        }

        return $this->session->get($this->sessionKey);
    }

    public function regenerate(): string
    {
        $field = 'hp_' . bin2hex(random_bytes(8));

        $this->session->put(
            $this->sessionKey,
            $field
        );

        return $field;
    }

    public function validate(array $input): bool
    {
        $field = $this->name();

        return empty(
            trim((string)($input[$field] ?? ''))
        );
    }
}