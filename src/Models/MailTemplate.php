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
