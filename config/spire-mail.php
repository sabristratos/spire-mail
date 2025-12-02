<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for all Spire Mail admin routes.
    |
    */
    'route_prefix' => env('SPIRE_MAIL_PREFIX', 'admin/mail'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware applied to all Spire Mail routes.
    |
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Configure how authorization is handled for mail template operations.
    | Set 'enabled' to false to disable authorization checks entirely.
    | When enabled, the 'gate' name will be checked using Gate::allows().
    | Your application must define this gate in a service provider.
    |
    */
    'authorization' => [
        'enabled' => true,
        'gate' => 'manage-mail-templates',
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Defaults
    |--------------------------------------------------------------------------
    |
    | Default settings for new email templates.
    |
    */
    'templates' => [
        'content_width' => 600,
        'font_family' => 'Arial, sans-serif',
        'background_color' => '#f5f5f5',
        'content_background_color' => '#ffffff',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Configuration for storing uploaded assets (images, etc.)
    |
    */
    'storage' => [
        'disk' => env('SPIRE_MAIL_DISK', 'public'),
        'path' => 'mail-assets',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Block Renderers
    |--------------------------------------------------------------------------
    |
    | Register custom block renderers here.
    |
    */
    'blocks' => [
        // 'custom-block' => \App\Mail\Blocks\CustomBlockRenderer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Merge Tags
    |--------------------------------------------------------------------------
    |
    | Merge tags available in all templates.
    |
    */
    'merge_tags' => [
        'app_name' => fn () => config('app.name'),
        'app_url' => fn () => config('app.url'),
        'current_year' => fn () => date('Y'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Configure validation behavior for email sending.
    |
    */
    'validation' => [
        'required_tags' => env('SPIRE_MAIL_VALIDATE_TAGS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure logging for Spire Mail operations.
    |
    */
    'logging' => [
        'enabled' => env('SPIRE_MAIL_LOGGING', true),
        'channel' => env('SPIRE_MAIL_LOG_CHANNEL', 'spire-mail'),
        'level' => env('SPIRE_MAIL_LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
];
