<?php

declare(strict_types=1);

namespace DeVy\Core\Services;

use RuntimeException;

final class CryptoService
{
    private string $key;

    public function __construct()
    {
        $key = env('APP_KEY');

        if (!$key) {
            throw new RuntimeException(
                'APP_KEY is not configured.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize key to 32 bytes
        |--------------------------------------------------------------------------
        */

        $this->key = hash(
            'sha256',
            $key,
            true
        );
    }

    /**
     * --------------------------------------------------------
     * Encrypt
     * --------------------------------------------------------
     */
    public function encrypt(
        string $plain
    ): string {

        if ($plain === '') {
            return '';
        }

        $iv = random_bytes(16);

        $cipher = openssl_encrypt(
            $plain,
            'AES-256-CBC',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($cipher === false) {
            throw new RuntimeException(
                'Unable to encrypt value.'
            );
        }

        return base64_encode(
            $iv . $cipher
        );
    }

    /**
     * --------------------------------------------------------
     * Decrypt
     * --------------------------------------------------------
     */
    public function decrypt(
        string $encrypted
    ): string {

        if ($encrypted === '') {
            return '';
        }

        $payload = base64_decode(
            $encrypted,
            true
        );

        if ($payload === false || strlen($payload) < 17) {
            return '';
        }

        $iv = substr(
            $payload,
            0,
            16
        );

        $cipher = substr(
            $payload,
            16
        );

        $plain = openssl_decrypt(
            $cipher,
            'AES-256-CBC',
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plain === false) {
            return '';
        }

        return $plain;
    }

    /**
     * --------------------------------------------------------
     * Check
     * --------------------------------------------------------
     */
    public function isEncrypted(
        string $value
    ): bool {

        if ($value === '') {
            return false;
        }

        $decoded = base64_decode(
            $value,
            true
        );

        return $decoded !== false
            && strlen($decoded) > 16;
    }
}