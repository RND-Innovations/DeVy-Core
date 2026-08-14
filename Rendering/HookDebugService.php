<?php

namespace DeVy\Core\Rendering;

use DeVy\Core\Services\PathService;
use DeVy\Core\Services\ConfigService;
use DeVy\Core\Services\HookManager;

class HookDebugService
{
    public function __construct(
        private PathService $paths,
        private ConfigService $config,
        private HookManager $hooks
    ) {}

    public function dump(): void
    {
        try {

            if (!$this->config->get('app.debug', false)) {
                return;
            }

            $dir = $this->paths->ensureWritable(
                $this->paths->logs()
            );

            $file = $dir . '/hooks-debug.json';

            $current = $this->hooks->exportDebug()['hooks'];

            $existing = [];

            if (file_exists($file)) {
                $json = json_decode(file_get_contents($file), true);
                $existing = $json['hooks'] ?? [];
            }

            foreach ($current as $hook => $data) {

                if (!isset($existing[$hook])) {
                    $existing[$hook] = $data;
                    continue;
                }

                $existing[$hook]['call_count'] =
                    ($existing[$hook]['call_count'] ?? 0)
                    + ($data['call_count'] ?? 0);

                if ($data['called']) {
                    $existing[$hook]['last_called_at'] = $data['last_called_at'];
                    $existing[$hook]['last_type'] = $data['last_type'];
                }

                $existing[$hook]['registered'] = max(
                    $existing[$hook]['registered'] ?? 0,
                    $data['registered'] ?? 0
                );

                $existing[$hook]['listeners'] = $data['listeners'];
            }

            file_put_contents($file, json_encode([
                'generated_at' => date('c'),
                'hooks' => $existing
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        } catch (\Throwable $e) {
            // silent fail
        }
    }
}