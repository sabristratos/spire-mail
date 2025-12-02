<?php

namespace SpireMail\Contracts;

use SpireMail\Models\MailTemplate;

interface TemplateRendererInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(MailTemplate $template, array $data = []): string;

    /**
     * Render HTML from raw content and settings (for live preview).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     */
    public function renderFromContent(array $blocks, array $settings, array $data = []): string;

    /**
     * Build MJML from raw content and settings (for live preview).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     */
    public function buildMjmlFromContent(array $blocks, array $settings, array $data = []): string;
}
