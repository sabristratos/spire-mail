<?php

namespace SpireMail\Tags\Conditionals;

use SpireMail\Tags\TagParser;

class ConditionalProcessor
{
    public function __construct(
        protected TagParser $parser
    ) {}

    /**
     * Process all conditional blocks in the content.
     *
     * @param  array<string, mixed>  $data
     */
    public function process(string $content, array $data): string
    {
        $pattern = $this->parser->getConditionalPattern();

        return preg_replace_callback(
            $pattern,
            fn (array $matches) => $this->evaluateConditional($matches, $data),
            $content
        ) ?? $content;
    }

    /**
     * Evaluate a single conditional block.
     *
     * @param  array<int, string|null>  $matches
     * @param  array<string, mixed>  $data
     */
    protected function evaluateConditional(array $matches, array $data): string
    {
        $type = $matches[1];
        $condition = $matches[2];
        $truthyContent = $matches[3];
        $falsyContent = $matches[4] ?? '';

        $value = $this->resolveValue($condition, $data);
        $isTruthy = $this->isTruthy($value);

        if ($type === 'unless') {
            $isTruthy = ! $isTruthy;
        }

        return $isTruthy ? $truthyContent : $falsyContent;
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
     * Determine if a value is truthy.
     */
    protected function isTruthy(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $value !== '' && $value !== '0' && strtolower($value) !== 'false';
        }

        if (is_numeric($value)) {
            return $value != 0;
        }

        if (is_array($value)) {
            return count($value) > 0;
        }

        if (is_object($value)) {
            if (method_exists($value, 'isEmpty')) {
                return ! $value->isEmpty();
            }
            if (method_exists($value, 'count')) {
                return $value->count() > 0;
            }

            return true;
        }

        return (bool) $value;
    }
}
