<?php

namespace DeVy\Core;

class SiteResolver
{
    private string $basePath;
    private string $domain;
    private string $sitePath;
    private string $publicPath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->domain = $this->detectDomain();

        $this->sitePath = $this->resolveSitePath();
        $this->publicPath = $this->resolvePublicPath();
    }

    /**
     * ----------------------------------------
     * Detect Domain
     * ----------------------------------------
     */
    private function detectDomain(): string
    {
        return strtolower(
            trim(
                $_SERVER['HTTP_HOST'] ?? 'localhost'
            )
        );
    }

    /**
     * ----------------------------------------
     * Resolve Site Path
     * ----------------------------------------
     *
     * The site folder comes from domains.php.
     */
    private function resolveSitePath(): string
    {
        $mapFile = $this->basePath . '/domains.php';

        if (!file_exists($mapFile)) {
            throw new \RuntimeException(
                "Missing domains map: {$mapFile}"
            );
        }

        $map = require $mapFile;

        if (!isset($map[$this->domain])) {
            throw new \RuntimeException(
                "Site not configured for domain: {$this->domain}"
            );
        }

        $site = $map[$this->domain];

        if (!is_string($site) || trim($site) === '') {
            throw new \RuntimeException(
                "Invalid site configuration for domain: {$this->domain}"
            );
        }

        $full = realpath(
            $this->basePath . '/' . $site
        );

        if ($full === false) {
            throw new \RuntimeException(
                "Invalid site path: {$site}"
            );
        }

        return rtrim($full, '/');
    }

    /**
     * ----------------------------------------
     * Resolve Public Path
     * ----------------------------------------
     *
     * PUBLIC_PATH is supplied by public/index.php.
     */
    private function resolvePublicPath(): string
    {
        if (!defined('PUBLIC_PATH')) {
            throw new \RuntimeException(
                'PUBLIC_PATH is not defined.'
            );
        }

        $full = realpath(PUBLIC_PATH);

        if ($full === false) {
            throw new \RuntimeException(
                'Invalid public path: ' . PUBLIC_PATH
            );
        }

        return rtrim($full, '/');
    }

    /**
     * ----------------------------------------
     * Get Domain
     * ----------------------------------------
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * ----------------------------------------
     * Get Site Path
     * ----------------------------------------
     */
    public function getSitePath(): string
    {
        return $this->sitePath;
    }

    /**
     * ----------------------------------------
     * Get Public Path
     * ----------------------------------------
     */
    public function getPublicPath(): string
    {
        return $this->publicPath;
    }
}