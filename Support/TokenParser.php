<?php

namespace DeVy\Core\Support;

class TokenParser
{
    public function parse(string $text, array $data): string
    {
        return preg_replace_callback(
            '/%([a-zA-Z0-9_.]+)%/',
            function ($matches) use ($data) {

                $parts = explode('.', $matches[1]);

                $value = $data;

                foreach ($parts as $part) {

                    if (!is_array($value) || !array_key_exists($part, $value)) {
                        return $matches[0];
                    }

                    $value = $value[$part];
                }

                return is_scalar($value)
                    ? (string) $value
                    : $matches[0];

            },
            $text
        );
    }
}