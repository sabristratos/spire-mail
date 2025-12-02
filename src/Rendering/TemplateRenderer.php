<?php

namespace SpireMail\Rendering;

use Spatie\Mjml\Mjml;
use SpireMail\Contracts\TemplateRendererInterface;
use SpireMail\Models\MailTemplate;
use SpireMail\Support\BlockRegistry;
use SpireMail\Tags\TagProcessor;

class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        protected BlockRegistry $blockRegistry,
        protected TagProcessor $tagProcessor
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(MailTemplate $template, array $data = []): string
    {
        $mjml = $this->buildMjml($template, $data);
        $html = $this->compileMjml($mjml);

        return $this->tagProcessor->process($html, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function buildMjml(MailTemplate $template, array $data = []): string
    {
        $content = $template->content;
        $settings = $template->settings ?? [];
        $blocks = $content['blocks'] ?? [];

        $blocksHtml = $this->renderBlocks($blocks, $data, $settings);

        return $this->wrapInMjmlDocument($blocksHtml, $settings, $template->preview_text);
    }

    /**
     * Render HTML from raw content and settings (for live preview).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     */
    public function renderFromContent(array $blocks, array $settings, array $data = []): string
    {
        $mjml = $this->buildMjmlFromContent($blocks, $settings, $data);
        $html = $this->compileMjml($mjml);

        return $this->tagProcessor->process($html, $data);
    }

    /**
     * Build MJML from raw content and settings (for live preview).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     */
    public function buildMjmlFromContent(array $blocks, array $settings, array $data = []): string
    {
        $blocksHtml = $this->renderBlocks($blocks, $data, $settings);

        return $this->wrapInMjmlDocument($blocksHtml, $settings);
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $settings
     */
    protected function renderBlocks(array $blocks, array $data, array $settings = []): string
    {
        $html = '';
        $pendingContentBlocks = [];
        $contentBackgroundColor = $settings['contentBackgroundColor'] ?? config('spire-mail.templates.content_background_color', '#ffffff');

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';

            if ($type === 'row') {
                if (! empty($pendingContentBlocks)) {
                    $html .= $this->wrapContentBlocksInSection($pendingContentBlocks, $data, $contentBackgroundColor);
                    $pendingContentBlocks = [];
                }

                if ($this->blockRegistry->hasRenderer($type)) {
                    $renderer = $this->blockRegistry->getRenderer($type);
                    $html .= $renderer->render($block['props'] ?? [], $data);
                }
            } else {
                $pendingContentBlocks[] = $block;
            }
        }

        if (! empty($pendingContentBlocks)) {
            $html .= $this->wrapContentBlocksInSection($pendingContentBlocks, $data, $contentBackgroundColor);
        }

        return $html;
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $data
     */
    protected function wrapContentBlocksInSection(array $blocks, array $data, string $backgroundColor): string
    {
        $contentHtml = '';

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';

            if (! $this->blockRegistry->hasRenderer($type)) {
                continue;
            }

            $renderer = $this->blockRegistry->getRenderer($type);
            $contentHtml .= $renderer->render($block['props'] ?? [], $data);
        }

        if (empty($contentHtml)) {
            return '';
        }

        return "<mj-section background-color=\"{$backgroundColor}\" padding=\"0\"><mj-column>{$contentHtml}</mj-column></mj-section>";
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    protected function wrapInMjmlDocument(string $blocksHtml, array $settings, ?string $previewText = null): string
    {
        $fontFamily = $settings['fontFamily'] ?? config('spire-mail.templates.font_family', 'Arial, sans-serif');
        $backgroundColor = $settings['backgroundColor'] ?? config('spire-mail.templates.background_color', '#f5f5f5');
        $contentWidth = $settings['contentWidth'] ?? config('spire-mail.templates.content_width', 600);

        $previewTextMjml = $previewText ? "<mj-preview>{$previewText}</mj-preview>" : '';

        return <<<MJML
<mjml>
  <mj-head>
    <mj-attributes>
      <mj-all font-family="{$fontFamily}" />
      <mj-text font-size="16px" color="#333333" line-height="1.5" />
      <mj-section padding="0" />
    </mj-attributes>
    <mj-style>
      /* Email CSS Reset - neutralize browser defaults */
      * { box-sizing: border-box; }
      h1, h2, h3, h4, h5, h6, p, ul, ol, dl, blockquote, figure, pre {
        margin: 0;
        margin-block-start: 0;
        margin-block-end: 0;
        margin-inline-start: 0;
        margin-inline-end: 0;
        padding: 0;
      }
      h1, h2, h3, h4, h5, h6 {
        font-size: inherit;
        font-weight: inherit;
      }
      ul, ol, dl { padding-inline-start: 0; }
      li, dd { margin: 0; padding: 0; }

      @media only screen and (max-width: 480px) {
        .no-stack table.mj-full-width-mobile {
          width: auto !important;
        }
        .no-stack td.mj-full-width-mobile {
          width: auto !important;
        }
      }
    </mj-style>
    {$previewTextMjml}
  </mj-head>
  <mj-body background-color="{$backgroundColor}" width="{$contentWidth}px">
    {$blocksHtml}
  </mj-body>
</mjml>
MJML;
    }

    protected function compileMjml(string $mjml): string
    {
        $binPath = realpath(__DIR__.'/../../bin');

        return Mjml::new()
            ->workingDirectory($binPath)
            ->toHtml($mjml);
    }
}
