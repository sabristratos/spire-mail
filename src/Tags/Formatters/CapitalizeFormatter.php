<?php

namespace SpireMail\Tags\Formatters;

class CapitalizeFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'capitalize';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null) {
            return '';
        }

        return mb_convert_case((string) $value, MB_CASE_TITLE);
    }
}
