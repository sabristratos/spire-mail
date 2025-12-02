<?php

namespace SpireMail\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SpireMail\Http\Controllers\Concerns\LogsToChannel;
use SpireMail\Mail\SpireTemplateMailable;
use SpireMail\Models\MailTemplate;
use SpireMail\Rendering\TemplateRenderer;

class PreviewController extends Controller
{
    use LogsToChannel;

    public function __construct(
        protected TemplateRenderer $renderer
    ) {}

    public function show(Request $request, MailTemplate $template): JsonResponse
    {
        try {
            $previewData = $request->input('data', []);
            $content = $request->input('content');
            $settings = $request->input('settings');

            if ($content !== null) {
                $mjml = $this->renderer->buildMjmlFromContent($content, $settings ?? [], $previewData);
                $html = $this->renderer->renderFromContent($content, $settings ?? [], $previewData);
            } else {
                $mjml = $this->renderer->buildMjml($template, $previewData);
                $html = $template->render($previewData);
            }

            Log::channel($this->logChannel())->debug('Template preview rendered', [
                'template_id' => $template->id,
                'live_preview' => $content !== null,
            ]);

            return response()->json([
                'html' => $html,
                'mjml' => $mjml,
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Template preview failed', [
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('spire-mail::messages.preview_failed'),
            ], 500);
        }
    }

    public function sendTest(Request $request, MailTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'data' => ['nullable', 'array'],
        ]);

        $email = $validated['email'];
        $data = $validated['data'] ?? [];

        try {
            Mail::to($email)->send(new SpireTemplateMailable($template, $data));

            Log::channel($this->logChannel())->info('Test email sent', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'recipient' => $email,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('spire-mail::messages.test_email_sent', ['email' => $email]),
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Test email failed', [
                'template_id' => $template->id,
                'recipient' => $email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('spire-mail::messages.test_email_failed'),
            ], 500);
        }
    }
}
