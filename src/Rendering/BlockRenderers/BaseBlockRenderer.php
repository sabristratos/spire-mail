<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Contracts\BlockRendererInterface;

abstract class BaseBlockRenderer implements BlockRendererInterface
{
    /**
     * Format padding array to CSS string.
     *
     * @param  array<string, int>|string  $padding
     */
    protected function formatPadding(array|string $padding): string
    {
        if (is_string($padding)) {
            return $padding;
        }

        $top = $padding['top'] ?? 0;
        $right = $padding['right'] ?? 0;
        $bottom = $padding['bottom'] ?? 0;
        $left = $padding['left'] ?? 0;

        return "{$top}px {$right}px {$bottom}px {$left}px";
    }

    /**
     * Build HTML attributes string from an associative array.
     *
     * @param  array<string, mixed>  $attrs
     */
    protected function buildAttributes(array $attrs): string
    {
        $filtered = array_filter($attrs, fn ($v) => $v !== null && $v !== '');
        $pairs = array_map(
            fn ($k, $v) => sprintf(' %s="%s"', $k, htmlspecialchars((string) $v)),
            array_keys($filtered),
            array_values($filtered)
        );

        return implode('', $pairs);
    }

    /**
     * Merge provided props with defaults.
     *
     * @param  array<string, mixed>  $props
     * @return array<string, mixed>
     */
    protected function mergeWithDefaults(array $props): array
    {
        return array_merge($this->getDefaultProps(), $props);
    }
}
