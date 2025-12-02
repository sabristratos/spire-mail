<?php

namespace SpireMail\Tags\Formatters;

use Carbon\Carbon;
use DateTimeInterface;

class DateFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'date';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $format = $argument ?? 'Y-m-d';

        try {
            if ($value instanceof DateTimeInterface) {
                return $value->format($format);
            }

            if ($value instanceof Carbon) {
                return $value->format($format);
            }

            if (is_numeric($value)) {
                return date($format, (int) $value);
            }

            $date = Carbon::parse($value);

            return $date->format($format);
        } catch (\Exception $e) {
            return (string) $value;
        }
    }
}
