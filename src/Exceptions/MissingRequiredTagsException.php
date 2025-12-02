<?php

namespace SpireMail\Exceptions;

use RuntimeException;

class MissingRequiredTagsException extends RuntimeException
{
    /**
     * @param  array<int, string>  $missingTags
     */
    public function __construct(
        public readonly string $templateSlug,
        public readonly array $missingTags
    ) {
        $tagList = implode(', ', $missingTags);

        parent::__construct(
            "Missing required tags for template '{$templateSlug}': {$tagList}"
        );
    }

    /**
     * Get the template slug.
     */
    public function getTemplateSlug(): string
    {
        return $this->templateSlug;
    }

    /**
     * Get the list of missing tags.
     *
     * @return array<int, string>
     */
    public function getMissingTags(): array
    {
        return $this->missingTags;
    }
}
