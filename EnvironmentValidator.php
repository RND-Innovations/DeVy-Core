<?php

namespace DeVy\Core;

use DeVy\Core\{
    Services\PathService
};

class EnvironmentValidator
{
    private PathService $paths;

    public function __construct(PathService $paths)
    {
        $this->paths = $paths;
    }

    public function validate(): void
    {
        $requiredWritable = [
            $this->paths->cache(),
            $this->paths->content(),
            $this->paths->logs(),
            $this->paths->modules(),
            $this->paths->plugins(),
            $this->paths->system(),
            $this->paths->themes(),
        ];

        foreach ($requiredWritable as $path) {

            if (!is_dir($path)) {
                throw new \RuntimeException("Missing directory: {$path}");
            }

            if (!is_writable($path)) {
                throw new \RuntimeException("Directory not writable: {$path}");
            }
        }
    }
}