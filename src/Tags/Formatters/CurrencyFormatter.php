<?php

namespace SpireMail\Tags\Formatters;

use NumberFormatter;

class CurrencyFormatter implements FormatterInterface
{
    public function getName(): string
    {
        return 'currency';
    }

    public function format(mixed $value, ?string $argument = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $amount = is_numeric($value) ? (float) $value : 0;
        $currency = $argument ?? 'USD';
        $locale = config('app.locale', 'en_US');

        if (class_exists(NumberFormatter::class)) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

            return $formatter->formatCurrency($amount, $currency);
        }

        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'CA$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
        ];

        $symbol = $symbols[$currency] ?? $currency.' ';

        return $symbol.number_format($amount, 2);
    }
}
