<?php

namespace SpireMail\Facades;

use Illuminate\Support\Facades\Facade;
use SpireMail\Models\MailTemplate;
use SpireMail\Services\SpireMailManager;

/**
 * @method static string render(MailTemplate|string $template, array $data = [])
 * @method static string processMergeTags(string $content, array $data = [])
 * @method static MailTemplate|null findTemplate(string $slug)
 * @method static MailTemplate findTemplateOrFail(string $slug)
 *
 * @see SpireMailManager
 */
class SpireMail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SpireMailManager::class;
    }
}
