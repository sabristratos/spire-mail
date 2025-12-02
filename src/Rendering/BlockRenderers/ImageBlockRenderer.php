<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\HtmlSanitizer;

class ImageBlockRenderer extends BaseBlockRenderer
{
    public function __construct(protected HtmlSanitizer $sanitizer) {}

    public function getType(): string
    {
        return 'image';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'src' => '',
            'alt' => '',
            'href' => '',
            'width' => '100%',
            'align' => 'center',
            'fluidOnMobile' => true,
            'borderRadius' => 0,
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

        if (empty($props['src'])) {
            return '';
        }

        $padding = $this->formatPadding($props['padding'] ?? []);

        $src = $this->sanitizer->sanitizeUrl($props['src']);
        $href = ! empty($props['href']) ? $this->sanitizer->sanitizeUrl($props['href']) : null;

        $width = $props['width'] ?? '100%';
        $mjmlWidth = $this->convertWidthForMjml($width);

        $attrs = $this->buildAttributes([
            'src' => $src,
            'alt' => $props['alt'] ?? '',
            'href' => $href,
            'width' => $mjmlWidth,
            'align' => $props['align'] ?? 'center',
            'fluid-on-mobile' => ($props['fluidOnMobile'] ?? true) ? 'true' : null,
            'border-radius' => isset($props['borderRadius']) && $props['borderRadius'] > 0
                ? $props['borderRadius'].'px'
                : null,
            'padding' => $padding,
        ]);

        return "<mj-image{$attrs} />";
    }

    /**
     * Convert width value for MJML.
     * MJML expects pixel values, not CSS percentages.
     * For 100%, we omit the width to let MJML use the container width.
     */
    protected function convertWidthForMjml(string $width): ?string
    {
        if ($width === '100%') {
            return null;
        }

        if (str_ends_with($width, '%')) {
            return null;
        }

        if (is_numeric($width)) {
            return $width.'px';
        }

        if (preg_match('/^\d+px$/', $width)) {
            return $width;
        }

        return null;
    }
}
