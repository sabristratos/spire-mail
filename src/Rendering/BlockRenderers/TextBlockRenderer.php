<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\HtmlSanitizer;
use SpireMail\Support\ThemeColors;

class TextBlockRenderer extends BaseBlockRenderer
{
    public function __construct(protected HtmlSanitizer $sanitizer) {}

    public function getType(): string
    {
        return 'text';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'content' => '',
            'variant' => 'paragraph',
            'align' => 'left',
            'color' => ThemeColors::TEXT_DEFAULT,
            'fontSize' => 16,
            'lineHeight' => 1.5,
            'fontWeight' => 'normal',
            'padding' => [
                'top' => 10,
                'right' => 0,
                'bottom' => 10,
                'left' => 0,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $data
     */
    public function render(array $props, array $data = []): string
    {
        $props = array_merge($this->getDefaultProps(), $props);

        $fontSize = $this->getFontSize($props);
        $fontWeight = $this->getFontWeight($props);
        $padding = $this->formatPadding($props['padding'] ?? []);

        $attrs = $this->buildAttributes([
            'align' => $props['align'] ?? 'left',
            'color' => $props['color'] ?? ThemeColors::TEXT_DEFAULT,
            'font-size' => $fontSize,
            'font-weight' => $fontWeight,
            'line-height' => $props['lineHeight'] ?? 1.5,
            'padding' => $padding,
        ]);

        $content = $this->sanitizer->sanitize($props['content'] ?? '');

        return "<mj-text{$attrs}>{$content}</mj-text>";
    }

    /**
     * @param  array<string, mixed>  $props
     */
    protected function getFontSize(array $props): string
    {
        if (isset($props['fontSize'])) {
            return $props['fontSize'].'px';
        }

        return match ($props['variant'] ?? 'paragraph') {
            'paragraph' => '16px',
            'caption' => '14px',
            default => '16px',
        };
    }

    /**
     * @param  array<string, mixed>  $props
     */
    protected function getFontWeight(array $props): string
    {
        return match ($props['fontWeight'] ?? 'normal') {
            'medium' => '500',
            'semibold' => '600',
            'bold' => '700',
            default => 'normal',
        };
    }
}
