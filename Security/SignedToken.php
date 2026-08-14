<?php

namespace DeVy\Core\Security;

class SignedToken
{
    public function __construct(
        protected string $secret
    ) {
        $this->secret = env('APP_CODE', 'Signed_Token@DeVy');
    }

    public function create(
        array $data,
        int $ttl = 3600
    ): string {

        $payload = [
            'data' => $data,
            'expires' => time() + $ttl
        ];

        $json = json_encode(
            $payload,
            JSON_UNESCAPED_SLASHES
        );

        $encoded = rtrim(
            strtr(
                base64_encode($json),
                '+/',
                '-_'
            ),
            '='
        );

        $signature = hash_hmac(
            'sha256',
            $encoded,
            $this->secret
        );

        return $encoded . '.' . $signature;
    }

    public function validate(
        string $token
    ): bool {

        return $this->decode($token) !== null;
    }

    public function decode(
        string $token
    ): ?array {

        $parts = explode('.', $token, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$encoded, $signature] = $parts;

        $expected = hash_hmac(
            'sha256',
            $encoded,
            $this->secret
        );

        if (
            !hash_equals(
                $expected,
                $signature
            )
        ) {
            return null;
        }

        $json = base64_decode(
            strtr(
                $encoded,
                '-_',
                '+/'
            )
        );

        $payload = json_decode(
            $json,
            true
        );

        if (!$payload) {
            return null;
        }

        if (
            empty($payload['expires'])
            || $payload['expires'] < time()
        ) {
            return null;
        }

        return $payload['data'] ?? [];
    }
}