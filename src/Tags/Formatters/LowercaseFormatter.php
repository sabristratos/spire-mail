<?php

namespace SpireMail\Tags\Formatters;

class LowercaseFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'lowercase';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null) {
            return '';
        }

        return mb_strtolower((string) $value);
    }
}
