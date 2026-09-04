<?php

namespace DeVy\Core;

use DeVy\Core\Services\PathService;

class EnvironmentValidator
{
    public function __construct(
        private PathService $paths
    ) {}

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
            $this->paths->ensureWritable($path);
        }
    }
}