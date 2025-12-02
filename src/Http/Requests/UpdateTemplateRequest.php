<?php

namespace SpireMail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use SpireMail\Rules\ValidBlockStructure;

class UpdateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! config('spire-mail.authorization.enabled', true)) {
            return true;
        }

        $gate = config('spire-mail.authorization.gate', 'manage-mail-templates');

        return Gate::allows($gate);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $templateId = $this->route('template')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('mail_templates', 'slug')->ignore($templateId)],
            'subject' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['sometimes', 'array'],
            'content.version' => ['nullable', 'string'],
            'content.blocks' => ['nullable', 'array', app(ValidBlockStructure::class)],
            'settings' => ['nullable', 'array'],
            'preview_text' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Please provide a template name.'),
            'subject.required' => __('Please provide an email subject.'),
            'slug.unique' => __('A template with this slug already exists.'),
        ];
    }
}
