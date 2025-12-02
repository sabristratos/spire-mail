<?php

namespace SpireMail\Tags\Formatters;

class NumberFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'number';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $number = is_numeric($value) ? (float) $value : 0;
        $decimals = $argument !== null ? (int) $argument : 0;

        return number_format($number, $decimals);
    }
}
