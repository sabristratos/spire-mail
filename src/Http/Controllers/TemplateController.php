<?php

namespace SpireMail\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use SpireMail\Http\Controllers\Concerns\LogsToChannel;
use SpireMail\Http\Requests\StoreTemplateRequest;
use SpireMail\Http\Requests\UpdateTemplateRequest;
use SpireMail\Http\Resources\TemplateResource;
use SpireMail\Models\MailTemplate;
use SpireMail\Support\BlockRegistry;

class TemplateController extends Controller
{
    use LogsToChannel;

    public function index(): Response
    {
        $templates = MailTemplate::query()
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return Inertia::render('spire-mail::Templates/Index', [
            'templates' => TemplateResource::collection($templates),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('spire-mail::Templates/Create');
    }

    public function store(StoreTemplateRequest $request): RedirectResponse
    {
        try {
            $template = DB::transaction(function () use ($request) {
                return MailTemplate::create($request->validated());
            });

            Log::channel($this->logChannel())->info('Template created', [
                'id' => $template->id,
                'name' => $template->name,
            ]);

            return redirect()
                ->route('spire-mail.templates.edit', $template)
                ->with('toast', [
                    'color' => 'success',
                    'description' => __('spire-mail::messages.template_created'),
                ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Template creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return back()
                ->withInput()
                ->with('toast', [
                    'color' => 'danger',
                    'description' => __('spire-mail::messages.template_create_failed'),
                ]);
        }
    }

    public function edit(MailTemplate $template, BlockRegistry $blockRegistry): Response
    {
        return Inertia::render('spire-mail::Templates/Edit', [
            'template' => new TemplateResource($template),
            'availableBlocks' => $blockRegistry->getAvailableBlocks(),
        ]);
    }

    public function update(UpdateTemplateRequest $request, MailTemplate $template): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $template) {
                $template->update($request->validated());
            });

            Log::channel($this->logChannel())->info('Template updated', [
                'id' => $template->id,
                'name' => $template->name,
            ]);

            return back()->with('toast', [
                'color' => 'success',
                'description' => __('spire-mail::messages.template_saved'),
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Template update failed', [
                'id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('toast', [
                    'color' => 'danger',
                    'description' => __('spire-mail::messages.template_save_failed'),
                ]);
        }
    }

    public function destroy(MailTemplate $template): RedirectResponse
    {
        try {
            $templateId = $template->id;
            $templateName = $template->name;

            DB::transaction(function () use ($template) {
                $template->delete();
            });

            Log::channel($this->logChannel())->info('Template deleted', [
                'id' => $templateId,
                'name' => $templateName,
            ]);

            return redirect()
                ->route('spire-mail.templates.index')
                ->with('toast', [
                    'color' => 'success',
                    'description' => __('spire-mail::messages.template_deleted'),
                ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Template deletion failed', [
                'id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('toast', [
                'color' => 'danger',
                'description' => __('spire-mail::messages.template_delete_failed'),
            ]);
        }
    }

    public function duplicate(MailTemplate $template): RedirectResponse
    {
        try {
            $copy = DB::transaction(function () use ($template) {
                $copy = $template->replicate();
                $copy->name = $template->name.' (Copy)';
                $copy->slug = null;
                $copy->save();

                return $copy;
            });

            Log::channel($this->logChannel())->info('Template duplicated', [
                'source_id' => $template->id,
                'new_id' => $copy->id,
                'name' => $copy->name,
            ]);

            return redirect()
                ->route('spire-mail.templates.edit', $copy)
                ->with('toast', [
                    'color' => 'success',
                    'description' => __('spire-mail::messages.template_duplicated'),
                ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Template duplication failed', [
                'source_id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('toast', [
                'color' => 'danger',
                'description' => __('spire-mail::messages.template_duplicate_failed'),
            ]);
        }
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:mail_templates,id'],
        ]);

        try {
            $count = DB::transaction(function () use ($request) {
                return MailTemplate::whereIn('id', $request->ids)->delete();
            });

            Log::channel($this->logChannel())->info('Templates bulk deleted', [
                'count' => $count,
                'ids' => $request->ids,
            ]);

            return back()->with('toast', [
                'color' => 'success',
                'description' => __('spire-mail::messages.templates_bulk_deleted', ['count' => $count]),
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Bulk delete failed', [
                'ids' => $request->ids,
                'error' => $e->getMessage(),
            ]);

            return back()->with('toast', [
                'color' => 'danger',
                'description' => __('spire-mail::messages.templates_bulk_delete_failed'),
            ]);
        }
    }

    public function toggleStatus(MailTemplate $template): RedirectResponse
    {
        try {
            DB::transaction(function () use ($template) {
                $template->update(['is_active' => ! $template->is_active]);
            });

            Log::channel($this->logChannel())->info('Template status toggled', [
                'id' => $template->id,
                'is_active' => $template->is_active,
            ]);

            return back()->with('toast', [
                'color' => 'success',
                'description' => __('spire-mail::messages.template_status_updated'),
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Status toggle failed', [
                'id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('toast', [
                'color' => 'danger',
                'description' => __('spire-mail::messages.template_status_failed'),
            ]);
        }
    }
}
