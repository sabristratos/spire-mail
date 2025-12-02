<?php

namespace SpireMail\Tags\Formatters;

class FormatterRegistry
{
    /** @var array<string, FormatterInterface> */
    protected array $formatters = [];

    protected bool $defaultsRegistered = false;

    /**
     * Register a formatter.
     */
    public function register(FormatterInterface $formatter): self
    {
        $this->formatters[$formatter->getName()] = $formatter;

        return $this;
    }

    /**
     * Check if a formatter exists.
     */
    public function has(string $name): bool
    {
        $this->registerDefaults();

        return isset($this->formatters[$name]);
    }

    /**
     * Get a formatter by name.
     */
    public function get(string $name): ?FormatterInterface
    {
        $this->registerDefaults();

        return $this->formatters[$name] ?? null;
    }

    /**
     * Apply a formatter to a value.
     */
    public function apply(string $name, mixed $value, ?string $argument = null): string
    {
        $formatter = $this->get($name);

        if ($formatter === null) {
            return is_scalar($value) ? (string) $value : '';
        }

        return $formatter->format($value, $argument);
    }

    /**
     * Get all registered formatter names.
     *
     * @return array<int, string>
     */
    public function getNames(): array
    {
        $this->registerDefaults();

        return array_keys($this->formatters);
    }

    /**
     * Register default formatters.
     */
    protected function registerDefaults(): void
    {
        if ($this->defaultsRegistered) {
            return;
        }

        $defaults = [
            new DefaultFormatter,
            new DateFormatter,
            new CurrencyFormatter,
            new UppercaseFormatter,
            new LowercaseFormatter,
            new CapitalizeFormatter,
            new TruncateFormatter,
            new CountFormatter,
            new NumberFormatter,
        ];

        foreach ($defaults as $formatter) {
            if (! isset($this->formatters[$formatter->getName()])) {
                $this->register($formatter);
            }
        }

        $this->defaultsRegistered = true;
    }
}
