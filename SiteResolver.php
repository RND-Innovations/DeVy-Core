<?php

namespace DeVy\Core;

class SiteResolver
{
    private string $basePath;
    private string $sitesPath;
    private string $domain;
    private array $config;

    public function __construct(string $basePath)
    {
        $this->basePath  = rtrim($basePath, '/');
        $this->sitesPath = $this->basePath . '/Sites';
        $this->domain    = $this->detectDomain();

        $this->config = $this->resolve();
    }

    private function detectDomain(): string
    {
        return strtolower(trim($_SERVER['HTTP_HOST'] ?? 'localhost'));
    }

    private function resolve(): array
    {
        $mapFile = $this->sitesPath . '/domains.php';

        if (!file_exists($mapFile)) {
            throw new \RuntimeException("Missing domains map: {$mapFile}");
        }

        $map = require $mapFile;

        if (!isset($map[$this->domain])) {
            throw new \RuntimeException("Site not configured for domain: {$this->domain}");
        }

        $entry = $map[$this->domain];

        // Backward compatibility
        if (is_string($entry)) {
            return [
                'site'   => $entry,
                'public' => 'public_html'
            ];
        }

        if (!isset($entry['site'])) {
            throw new \RuntimeException("Invalid domain config: missing 'site'");
        }

        return $entry;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getSitePath(): string
    {
        return $this->sitesPath . '/' . $this->config['site'];
    }

    public function getPublicPath(): string
    {
        $public = $this->config['public'] ?? 'public_html';

        // Resolve safely (handles ../ etc)
        $full = realpath($this->basePath . '/' . $public);

        if ($full === false) {
            throw new \RuntimeException("Invalid public path: {$public}");
        }

        return rtrim($full, '/');
    }
}