<?php

namespace SpireMail\Rendering;

class MergeTagProcessor
{
    protected string $pattern = '/\{\{([a-zA-Z0-9_.]+)\}\}/';

    /**
     * @param  array<string, mixed>  $data
     */
    public function process(string $content, array $data = []): string
    {
        $data = $this->mergeGlobalTags($data);

        return preg_replace_callback(
            $this->pattern,
            fn (array $matches) => $this->resolve($matches[1], $data),
            $content
        ) ?? $content;
    }

    /**
     * @return array<int, string>
     */
    public function extractVariables(string $content): array
    {
        preg_match_all($this->pattern, $content, $matches);

        return array_unique($matches[1]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolve(string $path, array $data): string
    {
        $keys = explode('.', $path);
        $value = $data;

        foreach ($keys as $key) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
            } elseif (is_object($value) && property_exists($value, $key)) {
                $value = $value->{$key};
            } else {
                return '{{'.$path.'}}';
            }
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
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mergeGlobalTags(array $data): array
    {
        $globalTags = config('spire-mail.merge_tags', []);

        foreach ($globalTags as $key => $value) {
            if (! array_key_exists($key, $data)) {
                $data[$key] = is_callable($value) ? $value() : $value;
            }
        }

        return $data;
    }
}
