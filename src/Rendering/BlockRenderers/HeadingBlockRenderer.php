<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\ThemeColors;

class HeadingBlockRenderer extends BaseBlockRenderer
{
    public function getType(): string
    {
        return 'heading';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'content' => '',
            'level' => 'h1',
            'align' => 'left',
            'color' => ThemeColors::TEXT_DEFAULT,
            'fontSize' => 28,
            'lineHeight' => 1.2,
            'fontWeight' => 'bold',
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
        $fontWeight = $this->getFontWeight($props['fontWeight'] ?? 'bold');
        $padding = $this->formatPadding($props['padding'] ?? []);

        $attrs = $this->buildAttributes([
            'align' => $props['align'] ?? 'left',
            'color' => $props['color'] ?? ThemeColors::TEXT_DEFAULT,
            'font-size' => $fontSize,
            'font-weight' => $fontWeight,
            'line-height' => $props['lineHeight'] ?? 1.2,
            'padding' => $padding,
        ]);

        $content = htmlspecialchars($props['content'] ?? '', ENT_QUOTES, 'UTF-8');
        $level = $props['level'] ?? 'h1';

        return "<mj-text{$attrs}><{$level}>{$content}</{$level}></mj-text>";
    }

    /**
     * @param  array<string, mixed>  $props
     */
    protected function getFontSize(array $props): string
    {
        if (isset($props['fontSize'])) {
            return $props['fontSize'].'px';
        }

        return match ($props['level'] ?? 'h1') {
            'h1' => '28px',
            'h2' => '24px',
            'h3' => '20px',
            default => '28px',
        };
    }

    protected function getFontWeight(string $fontWeight): string
    {
        return match ($fontWeight) {
            'normal' => 'normal',
            'medium' => '500',
            'semibold' => '600',
            default => '700',
        };
    }
}
