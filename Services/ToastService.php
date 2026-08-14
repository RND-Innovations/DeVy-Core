<?php

namespace DeVy\Core\Services;

class ToastService
{
    public function add(string $message, string $type = 'info'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['toasts'][] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public function get(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $toasts = $_SESSION['toasts'] ?? [];

        unset($_SESSION['toasts']);

        return $toasts;
    }
}