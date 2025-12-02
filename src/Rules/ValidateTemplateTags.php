<?php

namespace SpireMail\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use SpireMail\Tags\TagProcessor;
use SpireMail\Tags\TagRegistry;

class ValidateTemplateTags implements ValidationRule
{
    public function __construct(
        protected TagProcessor $processor,
        protected TagRegistry $registry
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $content = request()->input('content', []);
        $tags = request()->input('tags', []);

        if (empty($content) || ! is_array($content)) {
            return;
        }

        $usedTags = $this->processor->extractTagsFromContent($content);

        if (empty($usedTags) && empty($tags)) {
            return;
        }

        $definedKeys = array_column($tags, 'key');
        $globalKeys = $this->getGlobalTagKeys();
        $allAvailableKeys = array_merge($definedKeys, $globalKeys);

        $requiredKeys = array_column(
            array_filter($tags, fn ($t) => ($t['required'] ?? false) === true),
            'key'
        );

        $undefined = array_diff($usedTags, $allAvailableKeys);
        if (! empty($undefined)) {
            $fail(__('spire-mail::validation.undefined_tags', [
                'tags' => implode(', ', $undefined),
            ]));
        }

        $unusedRequired = array_diff($requiredKeys, $usedTags);
        if (! empty($unusedRequired)) {
            $fail(__('spire-mail::validation.unused_required_tags', [
                'tags' => implode(', ', $unusedRequired),
            ]));
        }
    }

    /**
     * Get all global tag keys.
     *
     * @return array<int, string>
     */
    protected function getGlobalTagKeys(): array
    {
        return array_keys($this->registry->getGlobalTags());
    }
}
