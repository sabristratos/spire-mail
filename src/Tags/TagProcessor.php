<?php

namespace SpireMail\Tags;

use SpireMail\Exceptions\MissingRequiredTagsException;
use SpireMail\Models\MailTemplate;
use SpireMail\Tags\Conditionals\ConditionalProcessor;
use SpireMail\Tags\Formatters\FormatterRegistry;

class TagProcessor
{
    public function __construct(
        protected TagRegistry $tagRegistry,
        protected TagParser $parser,
        protected FormatterRegistry $formatters,
        protected ConditionalProcessor $conditionalProcessor
    ) {}

    /**
     * Process all tags and conditionals in the content.
     *
     * @param  array<string, mixed>  $data
     */
    public function process(string $content, array $data = []): string
    {
        $data = $this->tagRegistry->resolveGlobalTagValues($data);

        $content = $this->conditionalProcessor->process($content, $data);

        return $this->replaceTags($content, $data);
    }

    /**
     * Replace all tag placeholders with their values.
     *
     * @param  array<string, mixed>  $data
     */
    protected function replaceTags(string $content, array $data): string
    {
        $tags = $this->parser->extractTags($content);

        foreach ($tags as $tag) {
            $value = $this->resolveValue($tag['key'], $data);
            $formattedValue = $this->applyPipes($value, $tag['pipes']);
            $content = str_replace($tag['full'], $formattedValue, $content);
        }

        return $content;
    }

    /**
     * Resolve a value from the data array using dot notation.
     *
     * @param  array<string, mixed>  $data
     */
    protected function resolveValue(string $path, array $data): mixed
    {
        $keys = explode('.', $path);
        $value = $data;

        foreach ($keys as $key) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } elseif (is_object($value) && property_exists($value, $key)) {
                $value = $value->{$key};
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Apply formatter pipes to a value.
     *
     * @param  array<int, array{name: string, argument: string|null}>  $pipes
     */
    protected function applyPipes(mixed $value, array $pipes): string
    {
        if (empty($pipes)) {
            return $this->valueToString($value);
        }

        $hasDefault = false;
        $defaultValue = null;

        foreach ($pipes as $pipe) {
            if ($pipe['name'] === 'default') {
                $hasDefault = true;
                $defaultValue = $pipe['argument'];

                continue;
            }
        }

        if ($hasDefault && ($value === null || $value === '')) {
            $value = $defaultValue;
        }

        foreach ($pipes as $pipe) {
            if ($pipe['name'] === 'default') {
                continue;
            }

            if ($this->formatters->has($pipe['name'])) {
                $value = $this->formatters->apply($pipe['name'], $value, $pipe['argument']);
            }
        }

        return $this->valueToString($value);
    }

    /**
     * Convert a value to string for output.
     */
    protected function valueToString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value) ?: '';
        }

        return '';
    }

    /**
     * Extract all variable keys used in the content.
     *
     * @return array<int, string>
     */
    public function extractVariables(string $content): array
    {
        return $this->parser->extractVariableKeys($content);
    }

    /**
     * Extract all tag keys used in an array structure (like template content).
     *
     * @param  array<string, mixed>  $content
     * @return array<int, string>
     */
    public function extractTagsFromContent(array $content): array
    {
        $tags = [];
        $this->extractTagsRecursively($content, $tags);

        return array_values(array_unique($tags));
    }

    /**
     * Recursively extract tag keys from mixed data.
     *
     * @param  array<int, string>  $tags
     */
    private function extractTagsRecursively(mixed $data, array &$tags): void
    {
        if (is_string($data)) {
            preg_match_all('/\{\{([^}|]+)/', $data, $matches);
            foreach ($matches[1] as $tag) {
                $cleanTag = trim($tag);
                if ($cleanTag !== '' && ! str_starts_with($cleanTag, '#') && ! str_starts_with($cleanTag, '/')) {
                    $tags[] = $cleanTag;
                }
            }
        } elseif (is_array($data)) {
            foreach ($data as $value) {
                $this->extractTagsRecursively($value, $tags);
            }
        }
    }

    /**
     * Get the tag registry instance.
     */
    public function getTagRegistry(): TagRegistry
    {
        return $this->tagRegistry;
    }

    /**
     * Get the formatter registry instance.
     */
    public function getFormatterRegistry(): FormatterRegistry
    {
        return $this->formatters;
    }

    /**
     * Validate that all required tags have been provided in the data.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws MissingRequiredTagsException
     */
    public function validateRequiredTags(MailTemplate $template, array $data): void
    {
        if (! config('spire-mail.validation.required_tags', true)) {
            return;
        }

        $requiredKeys = $template->getRequiredTagKeys();

        if (empty($requiredKeys)) {
            return;
        }

        $data = $this->tagRegistry->resolveGlobalTagValues($data);
        $missing = [];

        foreach ($requiredKeys as $key) {
            $value = $this->resolveValue($key, $data);

            if ($value === null || $value === '') {
                $missing[] = $key;
            }
        }

        if (! empty($missing)) {
            throw new MissingRequiredTagsException($template->slug, $missing);
        }
    }
}
