<?php

namespace SpireMail\Rendering\BlockRenderers;

class SpacerBlockRenderer extends BaseBlockRenderer
{
    public function getType(): string
    {
        return 'spacer';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array
    {
        return [
            'height' => 40,
            'backgroundColor' => 'transparent',
        ];
    }

    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $data
     */
    public function render(array $props, array $data = []): string
    {
        $props = array_merge($this->getDefaultProps(), $props);

        $height = (int) ($props['height'] ?? 40);
        $backgroundColor = $props['backgroundColor'] ?? 'transparent';

        $attrs = $this->buildAttributes([
            'height' => $height.'px',
            'container-background-color' => $backgroundColor !== 'transparent' ? $backgroundColor : null,
        ]);

        return "<mj-spacer{$attrs} />";
    }
}
