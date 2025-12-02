<?php

namespace SpireMail\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SpireMail\Models\MailTemplate;
use SpireMail\Services\SpireMailManager;
use SpireMail\Tags\TagProcessor;

class SpireTemplateMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected MailTemplate $template;

    /** @var array<string, mixed> */
    protected array $mergeData;

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws \SpireMail\Exceptions\MissingRequiredTagsException
     */
    public function __construct(
        MailTemplate|string $template,
        array $data = []
    ) {
        $this->template = MailTemplate::findBySlugOrFail($template);
        $this->mergeData = $data;

        app(TagProcessor::class)->validateRequiredTags($this->template, $this->mergeData);
    }

    public function envelope(): Envelope
    {
        $subject = app(SpireMailManager::class)
            ->processMergeTags($this->template->subject, $this->mergeData);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $html = $this->template->render($this->mergeData);

        return new Content(htmlString: $html);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function with(array $data): static
    {
        $this->mergeData = array_merge($this->mergeData, $data);

        return $this;
    }

    public function getTemplate(): MailTemplate
    {
        return $this->template;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMergeData(): array
    {
        return $this->mergeData;
    }
}
