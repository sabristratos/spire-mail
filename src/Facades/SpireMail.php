<?php

namespace SpireMail\Facades;

use Illuminate\Support\Facades\Facade;
use SpireMail\Models\MailTemplate;
use SpireMail\Services\SpireMailManager;
use SpireMail\Tags\TagProcessor;
use SpireMail\Tags\TagRegistry;

/**
 * @method static string render(MailTemplate|string $template, array $data = [])
 * @method static string processTags(string $content, array $data = [])
 * @method static SpireMailManager registerTags(array $tags)
 * @method static SpireMailManager registerTag(string $key, array $definition)
 * @method static array getGlobalTagsForEditor()
 * @method static array getAllTagsForEditor(?MailTemplate $template = null)
 * @method static array extractVariables(string $content)
 * @method static MailTemplate|null findTemplate(string $slug)
 * @method static MailTemplate findTemplateOrFail(string $slug)
 * @method static TagProcessor getTagProcessor()
 * @method static TagRegistry getTagRegistry()
 * @method static void validateTags(MailTemplate|string $template, array $data)
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
