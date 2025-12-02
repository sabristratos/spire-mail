<?php

namespace SpireMail\Rendering\BlockRenderers;

class HtmlBlockRenderer extends BaseBlockRenderer
{
    public function getType(): string
    {
        return 'html';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'content' => '',
            'padding' => [
                'top' => 10,
                'right' => 20,
                'bottom' => 10,
                'left' => 20,
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

        $content = $props['content'] ?? '';

        if (empty($content)) {
            return '';
        }

        $padding = $this->formatPadding($props['padding'] ?? []);

        return "<mj-section padding=\"{$padding}\"><mj-column><mj-raw>{$content}</mj-raw></mj-column></mj-section>";
    }
}
