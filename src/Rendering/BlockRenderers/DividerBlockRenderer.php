<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\ThemeColors;

class DividerBlockRenderer extends BaseBlockRenderer
{
    public function getType(): string
    {
        return 'divider';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'style' => 'solid',
            'color' => ThemeColors::DIVIDER,
            'width' => '100%',
            'align' => 'center',
            'thickness' => 1,
            'padding' => [
                'top' => 20,
                'right' => 0,
                'bottom' => 20,
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

        // For spacer only, use mj-spacer
        if ($props['style'] === 'none') {
            $height = ($props['padding']['top'] ?? 20) + ($props['padding']['bottom'] ?? 20);

            return "<mj-spacer height=\"{$height}px\" />";
        }

        $padding = $this->formatPadding($props['padding'] ?? []);

        $attrs = $this->buildAttributes([
            'border-style' => $props['style'],
            'border-color' => $props['color'],
            'border-width' => ($props['thickness'] ?? 1).'px',
            'width' => $props['width'],
            'align' => $props['align'],
            'padding' => $padding,
        ]);

        return "<mj-divider{$attrs} />";
    }
}
