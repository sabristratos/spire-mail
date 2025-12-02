<?php

namespace SpireMail\Tags\Formatters;

interface FormatterInterface
{
    /**
     * Get the formatter name/identifier.
     */
    public function getName(): string;

    /**
     * Format the given value.
     */
    public function format(mixed $value, ?string $argument = null): string;
}
