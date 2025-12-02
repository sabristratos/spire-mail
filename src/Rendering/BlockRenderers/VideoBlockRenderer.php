<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\HtmlSanitizer;

class VideoBlockRenderer extends BaseBlockRenderer
{
    public function __construct(protected HtmlSanitizer $sanitizer) {}

    public function getType(): string
    {
        return 'video';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'videoUrl' => '',
            'thumbnailUrl' => '',
            'alt' => 'Video',
            'width' => '100%',
            'align' => 'center',
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

        $videoUrl = $props['videoUrl'] ?? '';
        $thumbnailUrl = $props['thumbnailUrl'] ?? '';

        if (empty($videoUrl) && empty($thumbnailUrl)) {
            return '';
        }

        $thumbnail = $thumbnailUrl ?: $this->generateThumbnail($videoUrl);

        if (empty($thumbnail)) {
            return '';
        }

        $padding = $this->formatPadding($props['padding'] ?? []);

        $attrs = $this->buildAttributes([
            'src' => $this->sanitizer->sanitizeUrl($thumbnail),
            'alt' => $props['alt'] ?? 'Video',
            'href' => ! empty($videoUrl) ? $this->sanitizer->sanitizeUrl($videoUrl) : null,
            'width' => $props['width'] ?? '100%',
            'align' => $props['align'] ?? 'center',
            'border-radius' => isset($props['borderRadius']) && $props['borderRadius'] > 0
                ? $props['borderRadius'].'px'
                : null,
            'padding' => $padding,
        ]);

        return "<mj-image{$attrs} />";
    }

    protected function generateThumbnail(string $videoUrl): ?string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([^&\n?#]+)/', $videoUrl, $matches)) {
            return "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)) {
            return "https://vumbnail.com/{$matches[1]}.jpg";
        }

        return null;
    }
}
