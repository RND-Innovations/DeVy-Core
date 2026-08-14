<?php

namespace DeVy\Core;

use Dotenv\Dotenv;

class Bootstrap
{
    public static function init(string $basePath): void
    {
        self::loadEnv($basePath);
        self::startSession();
    }

    private static function loadEnv(string $basePath): void
    {
        if (!file_exists($basePath . '/.env')) {
            return;
        }

        $dotenv = \Dotenv\Dotenv::createImmutable($basePath);
        $dotenv->safeLoad(); // no exception if missing vars
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }
}