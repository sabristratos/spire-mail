<?php

namespace SpireMail\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SpireMail\Http\Controllers\Concerns\LogsToChannel;

class AssetController extends Controller
{
    use LogsToChannel;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        try {
            $file = $validated['file'];
            $disk = config('spire-mail.storage.disk', 'public');
            $path = config('spire-mail.storage.path', 'mail-assets');

            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs($path, $filename, $disk);

            if (! $filePath) {
                throw new \RuntimeException('File storage returned false');
            }

            $url = Storage::disk($disk)->url($filePath);

            Log::channel($this->logChannel())->info('Asset uploaded', [
                'filename' => $filename,
                'path' => $filePath,
                'disk' => $disk,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $filePath,
                'filename' => $filename,
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Asset upload failed', [
                'error' => $e->getMessage(),
                'original_name' => $request->file('file')?->getClientOriginalName(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('spire-mail::messages.upload_failed'),
            ], 500);
        }
    }

    public function destroy(string $filename): JsonResponse
    {
        try {
            $disk = config('spire-mail.storage.disk', 'public');
            $path = config('spire-mail.storage.path', 'mail-assets');
            $filePath = $path.'/'.$filename;

            if (! Storage::disk($disk)->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => __('spire-mail::messages.asset_not_found'),
                ], 404);
            }

            Storage::disk($disk)->delete($filePath);

            Log::channel($this->logChannel())->info('Asset deleted', [
                'filename' => $filename,
                'path' => $filePath,
                'disk' => $disk,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('spire-mail::messages.asset_deleted'),
            ]);
        } catch (\Throwable $e) {
            Log::channel($this->logChannel())->error('Asset deletion failed', [
                'error' => $e->getMessage(),
                'filename' => $filename,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('spire-mail::messages.delete_failed'),
            ], 500);
        }
    }
}
