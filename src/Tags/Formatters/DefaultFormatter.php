<?php

namespace SpireMail\Tags\Formatters;

class DefaultFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'default';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null || $value === '') {
            return $argument ?? '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value) ?: '';
        }

        return '';
    }
}
