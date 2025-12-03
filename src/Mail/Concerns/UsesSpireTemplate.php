<?php

namespace SpireMail\Mail\Concerns;

use Illuminate\Mail\Mailables\Content;
use RuntimeException;
use SpireMail\Models\MailTemplate;
use SpireMail\Services\SpireMailManager;
use SpireMail\Tags\TagProcessor;

trait UsesSpireTemplate
{
    protected ?MailTemplate $spireTemplate = null;

    /** @var array<string, mixed> */
    protected array $spireData = [];

    public function useTemplate(MailTemplate|string $template): static
    {
        $this->spireTemplate = MailTemplate::findBySlugOrFail($template);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function withSpireData(array $data): static
    {
        $this->spireData = array_merge($this->spireData, $data);

        return $this;
    }

    /**
     * @throws RuntimeException
     * @throws \SpireMail\Exceptions\MissingRequiredTagsException
     */
    protected function getSpireContent(): Content
    {
        if (! $this->spireTemplate) {
            throw new RuntimeException('No Spire template set. Call useTemplate() first.');
        }

        app(TagProcessor::class)->validateRequiredTags($this->spireTemplate, $this->spireData);

        $html = app(SpireMailManager::class)
            ->render($this->spireTemplate, $this->spireData);

        return new Content(htmlString: $html);
    }

    protected function getSpireSubject(): string
    {
        if (! $this->spireTemplate) {
            throw new RuntimeException('No Spire template set. Call useTemplate() first.');
        }

        return app(SpireMailManager::class)
            ->processTags($this->spireTemplate->subject, $this->spireData);
    }

    public function getSpireTemplate(): ?MailTemplate
    {
        return $this->spireTemplate;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSpireData(): array
    {
        return $this->spireData;
    }
}
