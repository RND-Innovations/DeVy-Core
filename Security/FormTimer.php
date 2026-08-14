<?php

namespace DeVy\Core\Security;

use DeVy\Core\Contracts\Session\SessionInterface;

class FormTimer
{
    private string $sessionKey = '_form_time';

    public function __construct(
        private SessionInterface $session
    ) {}

    public function start(): void
    {
        $this->session->put(
            $this->sessionKey,
            time()
        );
    }

    public function validate(
        int $minimumSeconds = 3
    ): bool {

        $started = (int) $this->session->get(
            $this->sessionKey,
            0
        );

        if ($started <= 0) {
            return false;
        }

        return (time() - $started)
            >= $minimumSeconds;
    }

    public function reset(): void
    {
        $this->session->forget(
            $this->sessionKey
        );
    }
}