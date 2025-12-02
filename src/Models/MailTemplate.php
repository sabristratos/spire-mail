<?php

namespace SpireMail\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use SpireMail\Services\SpireMailManager;

class MailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'description',
        'content',
        'settings',
        'tags',
        'preview_text',
        'is_active',
        'last_sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'settings' => 'array',
            'tags' => 'array',
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MailTemplate $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }

            if (empty($template->content)) {
                $template->content = [
                    'version' => '1.0',
                    'blocks' => [],
                ];
            }

            if (empty($template->settings)) {
                $template->settings = [
                    'fontFamily' => config('spire-mail.templates.font_family'),
                    'backgroundColor' => config('spire-mail.templates.background_color'),
                    'contentBackgroundColor' => config('spire-mail.templates.content_background_color'),
                    'contentWidth' => config('spire-mail.templates.content_width'),
                ];
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data = []): string
    {
        return app(SpireMailManager::class)->render($this, $data);
    }

    /**
     * @param  Builder<MailTemplate>  $query
     * @return Builder<MailTemplate>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getBlocks(): array
    {
        return $this->content['blocks'] ?? [];
    }

    public function getVersion(): string
    {
        return $this->content['version'] ?? '1.0';
    }

    /**
     * Get template-specific tags.
     *
     * @return array<int, array{key: string, label: string, description: string, type: string, required: bool, default: mixed, example: string|null}>
     */
    public function getTags(): array
    {
        return $this->tags ?? [];
    }

    /**
     * Set template-specific tags.
     *
     * @param  array<int, array{key: string, label?: string, description?: string, type?: string, required?: bool, default?: mixed, example?: string|null}>  $tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = array_map(function (array $tag) {
            return [
                'key' => $tag['key'],
                'label' => $tag['label'] ?? $this->humanizeKey($tag['key']),
                'description' => $tag['description'] ?? '',
                'type' => $tag['type'] ?? 'string',
                'required' => $tag['required'] ?? false,
                'default' => $tag['default'] ?? null,
                'example' => $tag['example'] ?? null,
            ];
        }, $tags);

        return $this;
    }

    /**
     * Add a single tag to the template.
     *
     * @param  array{label?: string, description?: string, type?: string, required?: bool, default?: mixed, example?: string|null}  $definition
     */
    public function addTag(string $key, array $definition = []): self
    {
        $tags = $this->getTags();
        $tags[] = [
            'key' => $key,
            'label' => $definition['label'] ?? $this->humanizeKey($key),
            'description' => $definition['description'] ?? '',
            'type' => $definition['type'] ?? 'string',
            'required' => $definition['required'] ?? false,
            'default' => $definition['default'] ?? null,
            'example' => $definition['example'] ?? null,
        ];

        $this->tags = $tags;

        return $this;
    }

    /**
     * Remove a tag from the template.
     */
    public function removeTag(string $key): self
    {
        $tags = array_filter($this->getTags(), fn (array $tag) => $tag['key'] !== $key);
        $this->tags = array_values($tags);

        return $this;
    }

    /**
     * Check if a tag exists in the template.
     */
    public function hasTag(string $key): bool
    {
        foreach ($this->getTags() as $tag) {
            if ($tag['key'] === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get required tag keys.
     *
     * @return array<int, string>
     */
    public function getRequiredTagKeys(): array
    {
        return array_map(
            fn (array $tag) => $tag['key'],
            array_filter($this->getTags(), fn (array $tag) => $tag['required'] ?? false)
        );
    }

    /**
     * Convert a key to a human-readable label.
     */
    protected function humanizeKey(string $key): string
    {
        $key = str_replace(['.', '_', '-'], ' ', $key);

        return ucwords($key);
    }

    /**
     * Find a template by slug or return the instance if already a MailTemplate.
     */
    public static function findBySlugOrFail(string|self $template): self
    {
        if ($template instanceof self) {
            return $template;
        }

        return static::where('slug', $template)->firstOrFail();
    }
}
