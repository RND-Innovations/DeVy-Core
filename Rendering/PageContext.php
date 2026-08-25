<?php

declare(strict_types=1);

namespace DeVy\Core\Rendering;

class PageContext
{
    public const PAGE_ID      = 'page_id';
    public const PAGE_CLASS   = 'page_class';
    public const PAGE_META    = 'page_meta';
    public const PAGE_FIELDS  = 'page_fields';
    public const PAGE_CONTENT = 'page_content';
    public const PAGE_BODY    = 'page_body';

    protected array $data = [
        self::PAGE_ID      => '',
        self::PAGE_CLASS   => '',
        self::PAGE_META    => [],
        self::PAGE_FIELDS  => [],
        self::PAGE_CONTENT => '',
        self::PAGE_BODY    => '',
    ];

    public function set(
        string $key,
        mixed $value
    ): static {
        $this->data[$key] = $value;

        return $this;
    }

    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->data[$key] ?? $default;
    }

    public function has(
        string $key
    ): bool {
        return array_key_exists(
            $key,
            $this->data
        );
    }

    public function remove(
        string $key
    ): static {
        unset($this->data[$key]);

        return $this;
    }

    public function merge(
        array $data
    ): static {
        $this->data = array_replace(
            $this->data,
            $data
        );

        return $this;
    }

    public function id(
        ?string $value = null
    ): string|static {
        if ($value === null) {
            return $this->data[self::PAGE_ID];
        }

        $this->data[self::PAGE_ID] = $value;

        return $this;
    }

    public function class(
        ?string $value = null
    ): string|static {
        if ($value === null) {
            return $this->data[self::PAGE_CLASS];
        }

        $this->data[self::PAGE_CLASS] = $value;

        return $this;
    }

    public function meta(
        ?array $value = null
    ): array|static {
        if ($value === null) {
            return $this->data[self::PAGE_META];
        }

        $this->data[self::PAGE_META] = $value;

        return $this;
    }

    public function addMeta(
        array $data
    ): static {
        $this->data[self::PAGE_META] = array_replace(
            $this->data[self::PAGE_META],
            $data
        );

        return $this;
    }

    public function fields(
        ?array $value = null
    ): array|static {
        if ($value === null) {
            return $this->data[self::PAGE_FIELDS];
        }

        $this->data[self::PAGE_FIELDS] = $value;

        return $this;
    }

    public function addFields(
        array $data
    ): static {
        $this->data[self::PAGE_FIELDS] = array_replace_recursive(
            $this->data[self::PAGE_FIELDS],
            $data
        );

        return $this;
    }

    public function content(
        ?string $value = null
    ): string|static {
        if ($value === null) {
            return $this->data[self::PAGE_CONTENT];
        }

        $this->data[self::PAGE_CONTENT] = $value;

        return $this;
    }

    public function body(
        ?string $value = null
    ): string|static {
        if ($value === null) {
            return $this->data[self::PAGE_BODY];
        }

        $this->data[self::PAGE_BODY] = $value;

        return $this;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'page_context' => $this->all(),
        ];
    }
}