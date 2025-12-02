<?php

namespace SpireMail\Tags\Formatters;

class TruncateFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'truncate';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null) {
            return '';
        }

        $string = (string) $value;
        $length = $argument !== null ? (int) $argument : 100;
        $suffix = '...';

        if (mb_strlen($string) <= $length) {
            return $string;
        }

        return mb_substr($string, 0, $length).$suffix;
    }
}
