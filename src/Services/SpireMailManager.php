<?php

namespace SpireMail\Services;

use SpireMail\Contracts\TemplateRendererInterface;
use SpireMail\Models\MailTemplate;
use SpireMail\Rendering\MergeTagProcessor;

class SpireMailManager
{
    public function __construct(
        protected TemplateRendererInterface $renderer,
        protected MergeTagProcessor $mergeTagProcessor
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(MailTemplate|string $template, array $data = []): string
    {
        $template = MailTemplate::findBySlugOrFail($template);

        return $this->renderer->render($template, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function processMergeTags(string $content, array $data = []): string
    {
        return $this->mergeTagProcessor->process($content, $data);
    }

    public function findTemplate(string $slug): ?MailTemplate
    {
        return MailTemplate::where('slug', $slug)->first();
    }

    public function findTemplateOrFail(string $slug): MailTemplate
    {
        return MailTemplate::findBySlugOrFail($slug);
    }
}
