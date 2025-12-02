<?php

namespace SpireMail\Tags\Formatters;

class UppercaseFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'uppercase';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null) {
            return '';
        }

        return mb_strtoupper((string) $value);
    }
}
