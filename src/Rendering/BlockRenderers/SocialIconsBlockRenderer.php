<?php

namespace SpireMail\Rendering\BlockRenderers;

use SpireMail\Support\HtmlSanitizer;

class SocialIconsBlockRenderer extends BaseBlockRenderer
{
    /**
     * Simple Icons CDN names for each platform.
     *
     * @var array<string, string>
     */
    protected const SIMPLE_ICON_NAMES = [
        'facebook' => 'facebook',
        'twitter' => 'x',
        'instagram' => 'instagram',
        'linkedin' => 'linkedin',
        'youtube' => 'youtube',
        'tiktok' => 'tiktok',
        'pinterest' => 'pinterest',
        'email' => 'gmail',
    ];

    /**
     * Default brand colors (matching Vue component).
     *
     * @var array<string, string>
     */
    protected const PLATFORM_COLORS = [
        'facebook' => '#1877F2',
        'twitter' => '#000000',
        'instagram' => '#E4405F',
        'linkedin' => '#0A66C2',
        'youtube' => '#FF0000',
        'tiktok' => '#000000',
        'pinterest' => '#BD081C',
        'email' => '#6B7280',
    ];

    protected const ICON_SIZES = [
        'sm' => '24px',
        'md' => '32px',
        'lg' => '40px',
    ];

    public function __construct(protected HtmlSanitizer $sanitizer) {}

    public function getType(): string
    {
        return 'social';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'links' => [
                ['platform' => 'facebook', 'url' => '', 'enabled' => false],
                ['platform' => 'twitter', 'url' => '', 'enabled' => false],
                ['platform' => 'instagram', 'url' => '', 'enabled' => false],
                ['platform' => 'linkedin', 'url' => '', 'enabled' => false],
                ['platform' => 'youtube', 'url' => '', 'enabled' => false],
                ['platform' => 'tiktok', 'url' => '', 'enabled' => false],
                ['platform' => 'pinterest', 'url' => '', 'enabled' => false],
                ['platform' => 'email', 'url' => '', 'enabled' => false],
            ],
            'iconSize' => 'md',
            'iconStyle' => 'filled',
            'iconColor' => '',
            'align' => 'center',
            'gap' => 12,
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

        $links = $props['links'] ?? [];
        $enabledLinks = array_filter($links, fn ($link) => ($link['enabled'] ?? false) && ! empty($link['url']));

        if (empty($enabledLinks)) {
            return '';
        }

        $padding = $this->formatPadding($props['padding'] ?? []);
        $iconSize = self::ICON_SIZES[$props['iconSize'] ?? 'md'] ?? '32px';
        $iconStyle = $props['iconStyle'] ?? 'filled';
        $gap = ($props['gap'] ?? 12).'px';
        $iconColor = $props['iconColor'] ?? '';

        $socialElements = [];
        foreach ($enabledLinks as $link) {
            $socialElements[] = $this->renderSocialElement($link, $iconSize, $iconStyle, $iconColor);
        }

        $attrs = $this->buildAttributes([
            'align' => $props['align'] ?? 'center',
            'icon-size' => $iconSize,
            'mode' => 'horizontal',
            'icon-padding' => "0 {$gap} 0 0",
            'padding' => $padding,
        ]);

        $elements = implode('', $socialElements);

        return "<mj-social{$attrs}>{$elements}</mj-social>";
    }

    /**
     * @param  array<string, mixed>  $link
     */
    protected function renderSocialElement(array $link, string $iconSize, string $iconStyle, string $iconColor): string
    {
        $platform = $link['platform'] ?? '';
        $url = $link['url'] ?? '';

        $color = $this->getIconColor($platform, $iconStyle, $iconColor);
        $iconUrl = $this->getSimpleIconUrl($platform, $color);
        $backgroundColor = $iconStyle === 'circle' ? $color : 'transparent';
        $iconFillColor = $iconStyle === 'circle' ? 'white' : null;

        if ($iconStyle === 'circle' && $iconFillColor) {
            $iconUrl = $this->getSimpleIconUrl($platform, $iconFillColor);
        }

        $attrs = $this->buildAttributes([
            'name' => 'custom',
            'src' => $iconUrl,
            'href' => $this->sanitizer->sanitizeUrl($url),
            'background-color' => $backgroundColor,
            'alt' => ucfirst($platform),
            'border-radius' => $iconStyle === 'circle' ? '50%' : null,
        ]);

        return "<mj-social-element{$attrs} />";
    }

    /**
     * Get the color for the icon based on style and settings.
     */
    protected function getIconColor(string $platform, string $iconStyle, string $customColor): string
    {
        if (! empty($customColor)) {
            return $customColor;
        }

        if ($iconStyle === 'filled' || $iconStyle === 'circle') {
            return self::PLATFORM_COLORS[$platform] ?? '#6B7280';
        }

        return '#6B7280';
    }

    /**
     * Get the Simple Icons CDN URL for a platform.
     */
    protected function getSimpleIconUrl(string $platform, string $color): string
    {
        $iconName = self::SIMPLE_ICON_NAMES[$platform] ?? $platform;
        $colorHex = ltrim($color, '#');

        return "https://cdn.simpleicons.org/{$iconName}/{$colorHex}";
    }
}
