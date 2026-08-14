<?php

namespace DeVy\Core\Assets;

use DeVy\Core\Services\PathService;

class AssetPublisher
{
    public function __construct(
        private PathService $paths
    ) {}

    public function publish(string $relative): string
    {
        if (!$relative) return '';

        // Normalize input
        $relative = '/' . ltrim($relative, '/');

        // -----------------------------------
        // 🔥 CASE 1: THEME BUILT ASSETS (SKIP PIPELINE)
        // -----------------------------------
        if ($this->isPublicThemeAsset($relative)) {

            $publicPath = $this->paths->public() . $relative;

            if (!file_exists($publicPath)) {
                $this->log('missing_theme_asset', $relative);
                return '';
            }

            // Optional cache bust
            $version = filemtime($publicPath);

            return $relative . '?v=' . $version;
        }

        // -----------------------------------
        // 🔥 CASE 2: NORMAL PIPELINE (MODULES, ETC)
        // -----------------------------------

        $relative = ltrim($relative, '/');

        // ⚠️ DO NOT lowercase full path blindly (breaks Linux paths)
        $urlPath = $relative;

        $source = $this->resolveSource($relative);

        if (!$source) {
            $this->log('missing_source', $relative);
            return '';
        }

        $target = $this->paths->public() . '/assets/' . $urlPath;

        // Ensure directory exists
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Smart update check
        $shouldCopy =
            !file_exists($target) ||
            filemtime($source) > filemtime($target) ||
            filesize($source) !== filesize($target);

        if ($shouldCopy) {
            if (!@copy($source, $target)) {
                $this->log('copy_failed', $relative);
                return '';
            }
        }

        $version = filemtime($source);

        return '/assets/' . $urlPath . '?v=' . $version;
    }

    // -----------------------------------
    // 🔥 DETECT THEME PUBLIC FILES
    // -----------------------------------
    private function isPublicThemeAsset(string $path): bool
    {
        // Must start with /themes/
        if (!str_starts_with($path, '/themes/')) {
            return false;
        }

        // Must look like hashed build file
        // Example: YTuner.c340b50a.css
        return preg_match('/\.[a-f0-9]{6,}\.(css|js)$/i', $path);
    }

    // -----------------------------------
    // 🔍 SOURCE RESOLUTION
    // -----------------------------------
    private function resolveSource(string $relative): ?string
    {
        $relative = ltrim($relative, '/');

        $bases = [
            'site' => $this->paths->site(),
            'base' => $this->paths->base(),
        ];

        $checked = [];

        foreach ($bases as $label => $base) {

            $exact = $base . '/' . $relative;
            $checked[] = $exact;

            if (file_exists($exact)) {
                return $exact;
            }

            $resolved = $this->resolveCaseInsensitive($base, $relative);

            if ($resolved) {
                $this->log('case_mismatch', $relative, [
                    'resolved' => $resolved,
                    'base' => $label
                ]);
                return $resolved;
            }
        }

        $this->log('missing_source', $relative, [
            'checked_paths' => $checked
        ]);

        return null;
    }

    private function resolveCaseInsensitive(string $base, string $relative): ?string
    {
        $parts = explode('/', $relative);
        $current = rtrim($base, '/');

        foreach ($parts as $part) {

            if (!is_dir($current)) {
                return null;
            }

            $found = null;

            foreach (scandir($current) as $file) {
                if (strcasecmp($file, $part) === 0) {
                    $found = $file;
                    break;
                }
            }

            if (!$found) {
                return null;
            }

            $current .= '/' . $found;
        }

        return $current;
    }

    // -----------------------------------
    // 🧠 LOGGER
    // -----------------------------------
    private function log(string $type, string $relative, array $context = []): void
    {
        try {
            $dir = $this->paths->ensureWritable(
                $this->paths->logs()
            );

            $file = $dir . '/assets-debug.json';

            $data = [
                'generated_at' => date('c'),
                'assets' => []
            ];

            if (file_exists($file)) {
                $json = json_decode(file_get_contents($file), true);
                if (is_array($json)) {
                    $data = $json;
                }
            }

            if (!isset($data['assets'][$type])) {
                $data['assets'][$type] = [];
            }

            if (!isset($data['assets'][$type][$relative])) {
                $data['assets'][$type][$relative] = [
                    'count' => 0,
                    'last_seen' => null,
                    'context' => []
                ];
            }

            $entry = &$data['assets'][$type][$relative];

            $entry['count']++;
            $entry['last_seen'] = date('c');

            foreach ($context as $key => $value) {
                if (!isset($entry['context'][$key])) {
                    $entry['context'][$key] = [];
                }

                if (is_array($value)) {
                    $entry['context'][$key] = array_values(array_unique(array_merge(
                        $entry['context'][$key],
                        $value
                    )));
                } else {
                    $entry['context'][$key][] = $value;
                    $entry['context'][$key] = array_unique($entry['context'][$key]);
                }
            }

            file_put_contents(
                $file,
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                LOCK_EX
            );

        } catch (\Throwable $e) {
            // never break UI
        }
    }
}