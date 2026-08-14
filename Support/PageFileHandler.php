<?php

namespace DeVy\Core\Support;

class PageFileHandler
{
    public function ensureDirectory(string $dir): bool
    {
        return is_dir($dir) || mkdir($dir, 0777, true);
    }

    public function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) return false;

        foreach (glob($dir . '/*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return rmdir($dir);
    }

    public function read(string $file): string
    {
        return file_exists($file) ? file_get_contents($file) ?: '' : '';
    }

    public function write(string $file, string $content): bool
    {
        return file_put_contents($file, $content, LOCK_EX) !== false;
    }
}