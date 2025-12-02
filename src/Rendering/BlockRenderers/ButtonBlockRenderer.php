<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\HtmlSanitizer;
use SpireMail\Support\ThemeColors;

class ButtonBlockRenderer extends BaseBlockRenderer
{
    public function __construct(protected HtmlSanitizer $sanitizer) {}

    public function getType(): string
    {
        return 'button';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'text' => 'Click Here',
            'href' => '#',
            'variant' => 'primary',
            'size' => 'md',
            'align' => 'center',
            'borderRadius' => 6,
            'fullWidth' => false,
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

        $innerPadding = $this->getInnerPadding($props['size'] ?? 'md');
        $outerPadding = $this->formatPadding($props['padding'] ?? []);

        $backgroundColor = $this->getBackgroundColor($props);
        $textColor = $this->getTextColor($props);

        $href = $this->sanitizer->sanitizeUrl($props['href'] ?? '#');

        $variant = $props['variant'] ?? 'primary';

        $attrs = $this->buildAttributes([
            'href' => $href,
            'background-color' => $backgroundColor,
            'color' => $textColor,
            'border' => $variant === 'outline' ? "2px solid {$textColor}" : null,
            'border-radius' => ($props['borderRadius'] ?? 6).'px',
            'font-weight' => '600',
            'font-size' => $this->getFontSize($props['size'] ?? 'md'),
            'inner-padding' => $innerPadding,
            'padding' => $outerPadding,
            'align' => $props['align'] ?? 'center',
            'width' => ($props['fullWidth'] ?? false) ? '100%' : null,
        ]);

        $text = htmlspecialchars($props['text'] ?? 'Click Here');

        return "<mj-button{$attrs}>{$text}</mj-button>";
    }

    /**
     * @param  array<string, mixed>  $props
     */
    protected function getBackgroundColor(array $props): string
    {
        return ThemeColors::getButtonBackgroundColor($props);
    }

    /**
     * @param  array<string, mixed>  $props
     */
    protected function getTextColor(array $props): string
    {
        return ThemeColors::getButtonTextColor($props);
    }

    protected function getInnerPadding(string $size): string
    {
        return match ($size) {
            'sm' => '8px 16px',
            'lg' => '16px 32px',
            default => '12px 24px',
        };
    }

    protected function getFontSize(string $size): string
    {
        return match ($size) {
            'sm' => '14px',
            'lg' => '18px',
            default => '16px',
        };
    }
}
