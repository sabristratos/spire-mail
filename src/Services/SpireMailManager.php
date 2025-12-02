<?php

namespace SpireMail\Services;

use SpireMail\Contracts\TemplateRendererInterface;
use SpireMail\Models\MailTemplate;
use SpireMail\Tags\TagProcessor;
use SpireMail\Tags\TagRegistry;

class SpireMailManager
{
    public function __construct(
        protected TemplateRendererInterface $renderer,
        protected TagProcessor $tagProcessor,
        protected TagRegistry $tagRegistry
    ) {}

    /**
     * Render a template with the given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function render(MailTemplate|string $template, array $data = []): string
    {
        $template = MailTemplate::findBySlugOrFail($template);

        return $this->renderer->render($template, $data);
    }

    /**
     * Process tags in content with the given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function processTags(string $content, array $data = []): string
    {
        return $this->tagProcessor->process($content, $data);
    }

    /**
     * Register global tags programmatically.
     *
     * @param  array<string, mixed|array{value?: mixed, label?: string, description?: string, example?: string|null}>  $tags
     */
    public function registerTags(array $tags): self
    {
        $this->tagRegistry->registerTags($tags);

        return $this;
    }

    /**
     * Register a single global tag.
     *
     * @param  array{value?: mixed, label?: string, description?: string, example?: string|null}  $definition
     */
    public function registerTag(string $key, array $definition): self
    {
        $this->tagRegistry->registerTag($key, $definition);

        return $this;
    }

    /**
     * Get all global tags for the editor.
     *
     * @return array<int, array{key: string, label: string, description: string, example: string|null, global: bool}>
     */
    public function getGlobalTagsForEditor(): array
    {
        return $this->tagRegistry->getGlobalTagsForEditor();
    }

    /**
     * Get all tags (global + template-specific) for the editor.
     *
     * @return array{global: array<int, array{key: string, label: string, description: string, example: string|null, global: bool}>, template: array<int, array{key: string, label: string, description: string, type: string, required: bool, default: mixed, example: string|null}>}
     */
    public function getAllTagsForEditor(?MailTemplate $template = null): array
    {
        return [
            'global' => $this->tagRegistry->getGlobalTagsForEditor(),
            'template' => $template ? $template->getTags() : [],
        ];
    }

    /**
     * Extract variables used in content.
     *
     * @return array<int, string>
     */
    public function extractVariables(string $content): array
    {
        return $this->tagProcessor->extractVariables($content);
    }

    /**
     * Find a template by slug.
     */
    public function findTemplate(string $slug): ?MailTemplate
    {
        return MailTemplate::where('slug', $slug)->first();
    }

    /**
     * Find a template by slug or fail.
     */
    public function findTemplateOrFail(string $slug): MailTemplate
    {
        return MailTemplate::findBySlugOrFail($slug);
    }

    /**
     * Get the tag processor instance.
     */
    public function getTagProcessor(): TagProcessor
    {
        return $this->tagProcessor;
    }

    /**
     * Get the tag registry instance.
     */
    public function getTagRegistry(): TagRegistry
    {
        return $this->tagRegistry;
    }

    /**
     * Validate that all required tags are provided for a template.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \SpireMail\Exceptions\MissingRequiredTagsException
     */
    public function validateTags(MailTemplate|string $template, array $data): void
    {
        $template = MailTemplate::findBySlugOrFail($template);
        $this->tagProcessor->validateRequiredTags($template, $data);
    }
}
