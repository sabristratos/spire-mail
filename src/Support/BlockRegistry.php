<?php

namespace SpireMail\Support;

use InvalidArgumentException;
use SpireMail\Contracts\BlockRendererInterface;
use SpireMail\Rendering\BlockRenderers\ButtonBlockRenderer;
use SpireMail\Rendering\BlockRenderers\DividerBlockRenderer;
use SpireMail\Rendering\BlockRenderers\HeadingBlockRenderer;
use SpireMail\Rendering\BlockRenderers\HtmlBlockRenderer;
use SpireMail\Rendering\BlockRenderers\ImageBlockRenderer;
use SpireMail\Rendering\BlockRenderers\RowBlockRenderer;
use SpireMail\Rendering\BlockRenderers\SocialIconsBlockRenderer;
use SpireMail\Rendering\BlockRenderers\SpacerBlockRenderer;
use SpireMail\Rendering\BlockRenderers\TextBlockRenderer;
use SpireMail\Rendering\BlockRenderers\VideoBlockRenderer;

class BlockRegistry
{
    /** @var array<string, class-string<BlockRendererInterface>> */
    protected array $renderers = [];

    /** @var array<string, BlockRendererInterface> */
    protected array $instances = [];

    public function __construct()
    {
        $this->registerDefaultRenderers();
        $this->registerCustomRenderers();
    }

    protected function registerDefaultRenderers(): void
    {
        $this->register('text', TextBlockRenderer::class);
        $this->register('heading', HeadingBlockRenderer::class);
        $this->register('image', ImageBlockRenderer::class);
        $this->register('button', ButtonBlockRenderer::class);
        $this->register('divider', DividerBlockRenderer::class);
        $this->register('spacer', SpacerBlockRenderer::class);
        $this->register('html', HtmlBlockRenderer::class);
        $this->register('video', VideoBlockRenderer::class);
        $this->register('social', SocialIconsBlockRenderer::class);
        $this->register('row', RowBlockRenderer::class);
    }

    protected function registerCustomRenderers(): void
    {
        $customBlocks = config('spire-mail.blocks', []);

        foreach ($customBlocks as $type => $rendererClass) {
            $this->register($type, $rendererClass);
        }
    }

    /**
     * @param  class-string<BlockRendererInterface>  $rendererClass
     */
    public function register(string $type, string $rendererClass): void
    {
        $this->renderers[$type] = $rendererClass;
    }

    public function getRenderer(string $type): BlockRendererInterface
    {
        if (! isset($this->renderers[$type])) {
            throw new InvalidArgumentException("No renderer registered for block type: {$type}");
        }

        if (! isset($this->instances[$type])) {
            $this->instances[$type] = app($this->renderers[$type]);
        }

        return $this->instances[$type];
    }

    public function hasRenderer(string $type): bool
    {
        return isset($this->renderers[$type]);
    }

    /**
     * @return array<string, array{type: string, label: string, icon: string, defaultProps: array<string, mixed>}>
     */
    public function getAvailableBlocks(): array
    {
        $blocks = [];

        foreach ($this->renderers as $type => $rendererClass) {
            $renderer = $this->getRenderer($type);
            $blocks[$type] = [
                'type' => $type,
                'label' => ucfirst($type),
                'icon' => $this->getBlockIcon($type),
                'defaultProps' => $renderer->getDefaultProps(),
            ];
        }

        return $blocks;
    }

    protected function getBlockIcon(string $type): string
    {
        return match ($type) {
            'text' => 'text-align-left',
            'heading' => 'heading',
            'image' => 'image-01',
            'button' => 'cursor-click-01',
            'divider' => 'horizontal-resize',
            'spacer' => 'distribute-vertical-center',
            'html' => 'source-code',
            'video' => 'video-01',
            'social' => 'share-01',
            'row' => 'layout-grid',
            default => 'square-01',
        };
    }
}
