<?php

namespace SpireMail\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use SpireMail\Models\MailTemplate;

/**
 * @mixin MailTemplate
 */
class TemplateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'subject' => $this->subject,
            'description' => $this->description,
            'content' => $this->content,
            'settings' => $this->settings,
            'preview_text' => $this->preview_text,
            'is_active' => $this->is_active,
            'last_sent_at' => $this->last_sent_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'block_count' => count($this->getBlocks()),
        ];
    }
}
