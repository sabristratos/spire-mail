<?php

namespace SpireMail\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SpireMail\Http\Controllers\Concerns\LogsToChannel;
use SpireMail\Models\MailTemplate;
use SpireMail\Services\SpireMailManager;

class TagController extends Controller
{
    use LogsToChannel;

    public function __construct(
        protected SpireMailManager $manager
    ) {}

    /**
     * Get all global tags.
     */
    public function index(): JsonResponse
    {
        $tags = $this->manager->getGlobalTagsForEditor();

        return response()->json([
            'tags' => $tags,
        ]);
    }

    /**
     * Get tags for a specific template.
     */
    public function show(MailTemplate $template): JsonResponse
    {
        return response()->json([
            'global' => $this->manager->getGlobalTagsForEditor(),
            'template' => $template->getTags(),
        ]);
    }

    /**
     * Update tags for a specific template.
     */
    public function update(Request $request, MailTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'tags' => ['required', 'array'],
            'tags.*.key' => ['required', 'string', 'max:100'],
            'tags.*.label' => ['nullable', 'string', 'max:100'],
            'tags.*.description' => ['nullable', 'string', 'max:500'],
            'tags.*.type' => ['nullable', 'string', 'in:string,number,date,url,boolean'],
            'tags.*.required' => ['nullable', 'boolean'],
            'tags.*.default' => ['nullable'],
            'tags.*.example' => ['nullable', 'string', 'max:255'],
        ]);

        $template->setTags($validated['tags']);
        $template->save();

        $this->logInfo('Template tags updated', [
            'template_id' => $template->id,
            'template_slug' => $template->slug,
            'tags_count' => count($validated['tags']),
        ]);

        return response()->json([
            'message' => __('spire-mail::messages.tags_updated'),
            'tags' => $template->getTags(),
        ]);
    }

    /**
     * Get available formatters.
     */
    public function formatters(): JsonResponse
    {
        $formatters = [
            [
                'name' => 'default',
                'description' => __('spire-mail::messages.formatter_default'),
                'syntax' => '{{tag|default:value}}',
                'example' => '{{user.name|default:Guest}}',
            ],
            [
                'name' => 'date',
                'description' => __('spire-mail::messages.formatter_date'),
                'syntax' => '{{tag|date:format}}',
                'example' => '{{order.date|date:d/m/Y}}',
            ],
            [
                'name' => 'currency',
                'description' => __('spire-mail::messages.formatter_currency'),
                'syntax' => '{{tag|currency:code}}',
                'example' => '{{order.total|currency:EUR}}',
            ],
            [
                'name' => 'uppercase',
                'description' => __('spire-mail::messages.formatter_uppercase'),
                'syntax' => '{{tag|uppercase}}',
                'example' => '{{user.name|uppercase}}',
            ],
            [
                'name' => 'lowercase',
                'description' => __('spire-mail::messages.formatter_lowercase'),
                'syntax' => '{{tag|lowercase}}',
                'example' => '{{user.email|lowercase}}',
            ],
            [
                'name' => 'capitalize',
                'description' => __('spire-mail::messages.formatter_capitalize'),
                'syntax' => '{{tag|capitalize}}',
                'example' => '{{user.name|capitalize}}',
            ],
            [
                'name' => 'truncate',
                'description' => __('spire-mail::messages.formatter_truncate'),
                'syntax' => '{{tag|truncate:length}}',
                'example' => '{{description|truncate:50}}',
            ],
            [
                'name' => 'count',
                'description' => __('spire-mail::messages.formatter_count'),
                'syntax' => '{{tag|count}}',
                'example' => '{{items|count}}',
            ],
            [
                'name' => 'number',
                'description' => __('spire-mail::messages.formatter_number'),
                'syntax' => '{{tag|number:decimals}}',
                'example' => '{{price|number:2}}',
            ],
        ];

        return response()->json([
            'formatters' => $formatters,
        ]);
    }
}
