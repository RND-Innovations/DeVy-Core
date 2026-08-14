<?php

namespace DeVy\Core\Services;

class HookManager
{
    private array $listeners = [];

    // ✅ Debug storage
    private array $debug = [
        'hooks' => []
    ];

    /**
     * Ensure debug structure exists
     */
    private function ensureHook(string $hook): void
    {
        if (!isset($this->debug['hooks'][$hook])) {
            $this->debug['hooks'][$hook] = [
                'registered' => 0,
                'called' => false,
                'last_called_at' => null,
                'last_type' => null,
                'call_count' => 0,                
                'listeners' => []
            ];
        }
    }

    /**
     * FILTER (modifies payload)
     */
    public function add(string $hook, callable $callback, int $priority = 10): void
    {
        $this->listeners[$hook][] = [
            'callback' => $callback,
            'type' => 'filter',
            'priority' => $priority
        ];

        // ✅ DEBUG
        $this->ensureHook($hook);
        $this->debug['hooks'][$hook]['registered']++;
        $this->debug['hooks'][$hook]['listeners'][] = [
            'type' => 'filter',
            'priority' => $priority
        ];
    }

    /**
     * ACTION (no payload modification required)
     */
    public function on(string $hook, callable $callback, int $priority = 10): void
    {
        $this->listeners[$hook][] = [
            'callback' => $callback,
            'type' => 'action',
            'priority' => $priority
        ];

        // ✅ DEBUG
        $this->ensureHook($hook);
        $this->debug['hooks'][$hook]['registered']++;
        $this->debug['hooks'][$hook]['listeners'][] = [
            'type' => 'action',
            'priority' => $priority
        ];
    }

    /**
     * DISPATCH (filters + actions)
     */
    public function dispatch(string $hook, mixed $payload = null): mixed
    {
        // ✅ DEBUG: mark called even if no listeners
        $this->ensureHook($hook);
        
        $this->debug['hooks'][$hook]['called'] = true;
        $this->debug['hooks'][$hook]['last_called_at'] = date('c');
        $this->debug['hooks'][$hook]['last_type'] = 'dispatch';
        $this->debug['hooks'][$hook]['call_count']++;

        if (empty($this->listeners[$hook])) {
            return $payload;
        }

        // ✅ Normalize payload
        if ($payload === null) {
            $payload = [];
        }

        // ✅ SORT FIRST
        $listeners = $this->listeners[$hook];
        usort($listeners, fn($a, $b) => $a['priority'] <=> $b['priority']);

        foreach ($listeners as $listener) {

            if ($listener['type'] === 'filter') {

                $result = $listener['callback']($payload);

                if ($result !== null) {
                    $payload = $result;
                }
            }

            if ($listener['type'] === 'action') {
                $listener['callback']($payload);
            }
        }

        return $payload;
    }

    /**
     * RENDER (for HTML injection hooks only)
     */
    public function render(string $hook, array $context = []): string
    {
        // ✅ DEBUG: mark called
        $this->ensureHook($hook);

        $this->debug['hooks'][$hook]['called'] = true;
        $this->debug['hooks'][$hook]['last_called_at'] = date('c');
        $this->debug['hooks'][$hook]['last_type'] = 'render';
        $this->debug['hooks'][$hook]['call_count']++;

        if (empty($this->listeners[$hook])) {
            return '';
        }

        // ✅ SORT FIRST
        $listeners = $this->listeners[$hook];
        usort($listeners, fn($a, $b) => $a['priority'] <=> $b['priority']);

        $output = '';

        foreach ($listeners as $listener) {

            $result = $listener['callback']($context);

            if (is_string($result)) {
                $output .= $result;
            }
        }

        return $output;
    }


    /**
     * RENDER FIRST
     * Returns the first non-empty render result.
     */
    public function renderFirst(string $hook, array $context = []): string
    {
        $this->ensureHook($hook);

        $this->debug['hooks'][$hook]['called'] = true;
        $this->debug['hooks'][$hook]['last_called_at'] = date('c');
        $this->debug['hooks'][$hook]['last_type'] = 'render-first';
        $this->debug['hooks'][$hook]['call_count']++;

        if (empty($this->listeners[$hook])) {
            return '';
        }

        $listeners = $this->listeners[$hook];

        // Highest priority first
        usort($listeners, fn ($a, $b) => $b['priority'] <=> $a['priority']);

        foreach ($listeners as $listener) {

            $result = $listener['callback']($context);

            if (is_string($result) && trim($result) !== '') {
                return $result;
            }
        }

        return '';
    }

    /**
     * OPTIONAL: Helper for safe array hooks
     */
    public function collect(string $hook, array $default = []): array
    {
        $result = $this->dispatch($hook, $default);

        return is_array($result) ? $result : $default;
    }

    /**
     * ✅ EXPORT DEBUG DATA (for JSON logging)
     */
    public function exportDebug(): array
    {
        return [
            'generated_at' => date('c'),
            'hooks' => $this->debug['hooks']
        ];
    }

    /**
     * ✅ OPTIONAL: Reset debug (useful per request if needed)
     */
    public function resetDebug(): void
    {
        $this->debug = [
            'hooks' => []
        ];
    }
}