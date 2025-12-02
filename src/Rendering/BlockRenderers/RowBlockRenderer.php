<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\BlockRegistry;

class RowBlockRenderer extends BaseBlockRenderer
{
    public function __construct(
        protected BlockRegistry $blockRegistry
    ) {}

    public function getType(): string
    {
        return 'row';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'preset' => '2-equal',
            'columns' => [
                ['id' => '', 'width' => '50%', 'blocks' => [], 'style' => []],
                ['id' => '', 'width' => '50%', 'blocks' => [], 'style' => []],
            ],
            'gap' => 10,
            'stackOnMobile' => true,
            'style' => [
                'backgroundColor' => null,
                'backgroundImage' => null,
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center center',
                'padding' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                ],
                'borderRadius' => 0,
                'textAlign' => 'left',
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

        $columns = $props['columns'] ?? [];
        $style = $props['style'] ?? [];
        $stackOnMobile = $props['stackOnMobile'] ?? true;
        $gap = $props['gap'] ?? 10;

        $columnsHtml = '';
        $totalColumns = count($columns);
        foreach ($columns as $index => $column) {
            $columnsHtml .= $this->renderColumn($column, $data, $gap, $index, $totalColumns);
        }

        $sectionAttrs = $this->buildSectionAttributes($props, $style, $stackOnMobile);

        return "<mj-section{$sectionAttrs}>{$columnsHtml}</mj-section>";
    }

    /**
     * Build section attributes from props and style.
     *
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $style
     */
    protected function buildSectionAttributes(array $props, array $style, bool $stackOnMobile): string
    {
        $padding = $this->formatPadding($style['padding'] ?? []);

        $attrs = [
            'padding' => $padding,
            'background-color' => $style['backgroundColor'] ?? null,
            'text-align' => $style['textAlign'] ?? null,
            'css-class' => $stackOnMobile ? null : 'no-stack',
        ];

        if (! empty($style['backgroundImage'])) {
            $attrs['background-url'] = $style['backgroundImage'];
            $attrs['background-size'] = $style['backgroundSize'] ?? 'cover';
            $attrs['background-position'] = $style['backgroundPosition'] ?? 'center center';
        }

        if (! empty($style['borderRadius'])) {
            $attrs['border-radius'] = $style['borderRadius'].'px';
        }

        return $this->buildAttributes($attrs);
    }

    /**
     * Render a single column with its content.
     *
     * @param  array<string, mixed>  $column
     * @param  array<string, mixed>  $data
     */
    protected function renderColumn(array $column, array $data, int $gap = 10, int $index = 0, int $totalColumns = 1): string
    {
        $width = $column['width'] ?? '50%';
        $style = $column['style'] ?? [];
        $blocks = $column['blocks'] ?? [];

        $columnAttrs = $this->buildColumnAttributes($width, $style, $gap, $index, $totalColumns);
        $contentHtml = $this->renderColumnBlocks($blocks, $data);

        return "<mj-column{$columnAttrs}>{$contentHtml}</mj-column>";
    }

    /**
     * Build column attributes from width and style.
     *
     * @param  array<string, mixed>  $style
     */
    protected function buildColumnAttributes(string $width, array $style, int $gap = 10, int $index = 0, int $totalColumns = 1): string
    {
        $backgroundColor = $style['backgroundColor'] ?? null;

        if (! empty($style['overlayColor']) && ! empty($style['backgroundImage'])) {
            $backgroundColor = $this->applyOverlayToColor(
                $style['backgroundColor'] ?? '#ffffff',
                $style['overlayColor'],
                ($style['overlayOpacity'] ?? 50) / 100
            );
        }

        $attrs = [
            'width' => $width,
            'background-color' => $backgroundColor,
            'vertical-align' => $this->mapVerticalAlign($style['verticalAlign'] ?? 'top'),
        ];

        if (! empty($style['backgroundImage'])) {
            $attrs['background-url'] = $style['backgroundImage'];
            $attrs['background-size'] = $style['backgroundSize'] ?? 'cover';
            $attrs['background-position'] = $style['backgroundPosition'] ?? 'center center';
            $attrs['background-repeat'] = 'no-repeat';
        }

        $columnPadding = $this->calculateColumnPadding($style['padding'] ?? [], $gap, $index, $totalColumns);
        if ($columnPadding !== null) {
            $attrs['padding'] = $columnPadding;
        }

        if (! empty($style['borderRadius'])) {
            $attrs['border-radius'] = $style['borderRadius'].'px';
        }

        return $this->buildAttributes($attrs);
    }

    /**
     * Calculate column padding including gap spacing.
     *
     * @param  array<string, mixed>  $stylePadding
     */
    protected function calculateColumnPadding(array $stylePadding, int $gap, int $index, int $totalColumns): ?string
    {
        $halfGap = $gap / 2;

        $top = $stylePadding['top'] ?? 0;
        $right = $stylePadding['right'] ?? 0;
        $bottom = $stylePadding['bottom'] ?? 0;
        $left = $stylePadding['left'] ?? 0;

        if ($totalColumns > 1 && $gap > 0) {
            $isFirst = $index === 0;
            $isLast = $index === $totalColumns - 1;

            if ($isFirst) {
                $right += $halfGap;
            } elseif ($isLast) {
                $left += $halfGap;
            } else {
                $left += $halfGap;
                $right += $halfGap;
            }
        }

        if ($top === 0 && $right === 0 && $bottom === 0 && $left === 0) {
            return null;
        }

        return "{$top}px {$right}px {$bottom}px {$left}px";
    }

    /**
     * Apply an overlay color to a base color.
     * Used when MJML doesn't support true overlays.
     */
    protected function applyOverlayToColor(string $baseColor, string $overlayColor, float $opacity): string
    {
        $baseRgb = $this->hexToRgb($baseColor);
        $overlayRgb = $this->hexToRgb($overlayColor);

        if ($baseRgb === null || $overlayRgb === null) {
            return $baseColor;
        }

        $r = (int) round($overlayRgb['r'] * $opacity + $baseRgb['r'] * (1 - $opacity));
        $g = (int) round($overlayRgb['g'] * $opacity + $baseRgb['g'] * (1 - $opacity));
        $b = (int) round($overlayRgb['b'] * $opacity + $baseRgb['b'] * (1 - $opacity));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Convert hex color to RGB array.
     *
     * @return array{r: int, g: int, b: int}|null
     */
    protected function hexToRgb(string $hex): ?array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6) {
            return null;
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Map vertical align value to MJML value.
     */
    protected function mapVerticalAlign(string $align): string
    {
        return match ($align) {
            'top' => 'top',
            'middle' => 'middle',
            'bottom' => 'bottom',
            default => 'top',
        };
    }

    /**
     * Render content blocks within a column.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $data
     */
    protected function renderColumnBlocks(array $blocks, array $data): string
    {
        $html = '';

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';

            if ($type === 'row') {
                continue;
            }

            if (! $this->blockRegistry->hasRenderer($type)) {
                continue;
            }

            $renderer = $this->blockRegistry->getRenderer($type);
            $html .= $renderer->render($block['props'] ?? [], $data);
        }

        return $html;
    }
}
