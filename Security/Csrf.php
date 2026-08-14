<?php

namespace DeVy\Core\Security;

use DeVy\Core\Contracts\Session\SessionInterface;

class Csrf
{
    private string $sessionKey = '_csrf_token';

    public function __construct(
        private SessionInterface $session
    ) {}

    public function token(): string
    {
        if (!$this->session->has($this->sessionKey)) {
            $this->session->put(
                $this->sessionKey,
                bin2hex(random_bytes(32))
            );
        }

        return $this->session->get($this->sessionKey);
    }

    public function regenerate(): string
    {
        $token = bin2hex(random_bytes(32));

        $this->session->put(
            $this->sessionKey,
            $token
        );

        return $token;
    }

    public function validate(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        return hash_equals(
            $this->token(),
            $token
        );
    }
}