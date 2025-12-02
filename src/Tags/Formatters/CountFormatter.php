<?php

namespace SpireMail\Tags\Formatters;

use Countable;

class CountFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'count';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null) {
            return '0';
        }

        if (is_array($value) || $value instanceof Countable) {
            return (string) count($value);
        }

        if (is_string($value)) {
            return (string) mb_strlen($value);
        }

        return '1';
    }
}
