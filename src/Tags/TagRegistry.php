<?php

namespace SpireMail\Tags;

class TagRegistry
{
    /** @var array<string, array{value: mixed, label: string, description: string, example: string|null}> */
    protected array $globalTags = [];

    protected bool $configLoaded = false;

    /**
     * Register a single global tag.
     *
     * @param  array{value?: mixed, label?: string, description?: string, example?: string|null}  $definition
     */
    public function registerTag(string $key, array $definition): self
    {
        $this->globalTags[$key] = [
            'value' => $definition['value'] ?? null,
            'label' => $definition['label'] ?? $this->humanize($key),
            'description' => $definition['description'] ?? '',
            'example' => $definition['example'] ?? null,
        ];

        return $this;
    }

    /**
     * Register multiple global tags at once.
     *
     * @param  array<string, mixed|array{value?: mixed, label?: string, description?: string, example?: string|null}>  $tags
     */
    public function registerTags(array $tags): self
    {
        foreach ($tags as $key => $definition) {
            if (is_array($definition) && (isset($definition['value']) || isset($definition['label']))) {
                $this->registerTag($key, $definition);
            } else {
                $this->registerTag($key, ['value' => $definition]);
            }
        }

        return $this;
    }

    /**
     * Get all registered global tags.
     *
     * @return array<string, array{value: mixed, label: string, description: string, example: string|null}>
     */
    public function getGlobalTags(): array
    {
        $this->loadConfigTags();

        return $this->globalTags;
    }

    /**
     * Get global tags formatted for the editor UI.
     *
     * @return array<int, array{key: string, label: string, description: string, example: string|null, global: bool}>
     */
    public function getGlobalTagsForEditor(): array
    {
        $tags = [];

        foreach ($this->getGlobalTags() as $key => $definition) {
            $tags[] = [
                'key' => $key,
                'label' => $definition['label'],
                'description' => $definition['description'],
                'example' => $definition['example'] ?? $this->resolveValue($definition['value']),
                'global' => true,
            ];
        }

        return $tags;
    }

    /**
     * Resolve all global tag values into a data array.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function resolveGlobalTagValues(array $data = []): array
    {
        foreach ($this->getGlobalTags() as $key => $definition) {
            if (! array_key_exists($key, $data)) {
                $data[$key] = $this->resolveValue($definition['value']);
            }
        }

        return $data;
    }

    /**
     * Check if a tag key exists in global tags.
     */
    public function hasGlobalTag(string $key): bool
    {
        $this->loadConfigTags();

        return isset($this->globalTags[$key]);
    }

    /**
     * Get a specific global tag definition.
     *
     * @return array{value: mixed, label: string, description: string, example: string|null}|null
     */
    public function getGlobalTag(string $key): ?array
    {
        $this->loadConfigTags();

        return $this->globalTags[$key] ?? null;
    }

    /**
     * Load global tags from config if not already loaded.
     */
    protected function loadConfigTags(): void
    {
        if ($this->configLoaded) {
            return;
        }

        $configTags = config('spire-mail.merge_tags', []);

        foreach ($configTags as $key => $value) {
            if (! isset($this->globalTags[$key])) {
                $this->registerTag($key, ['value' => $value]);
            }
        }

        $this->configLoaded = true;
    }

    /**
     * Resolve a tag value (handles callables).
     */
    protected function resolveValue(mixed $value): mixed
    {
        if (is_callable($value) && ! is_string($value)) {
            return $value();
        }

        return $value;
    }

    /**
     * Convert a key to a human-readable label.
     */
    protected function humanize(string $key): string
    {
        $key = str_replace(['.', '_', '-'], ' ', $key);

        return ucwords($key);
    }
}
