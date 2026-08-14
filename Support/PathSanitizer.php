<?php

namespace DeVy\Core\Support;

class PathSanitizer
{
    public function clean(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#\.\.+#', '', $path);
        $path = preg_replace('#/{2,}#', '/', $path);
        $path = trim($path, '/');

        $segments = explode('/', $path);
        $clean = [];

        foreach ($segments as $segment) {
            $segment = strtolower($segment);
            $segment = preg_replace('/[^a-z0-9\-]/', '-', $segment);
            $segment = preg_replace('/-+/', '-', $segment);
            $segment = trim($segment, '-');

            if ($segment !== '') {
                $clean[] = $segment;
            }
        }

        return implode('/', $clean);
    }

    public function isValid(string $path): bool
    {
        return !empty($path) && strlen($path) < 255;
    }
}