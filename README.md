# Spire Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)
[![License](https://img.shields.io/packagist/l/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)

A visual drag-and-drop email template editor for Laravel. Build beautiful, responsive email templates with an intuitive editor and send them using Laravel's mail system.

## Features

- Drag-and-drop email editor with real-time preview
- 10+ content blocks (text, heading, image, button, divider, spacer, HTML, video, social icons, multi-column rows)
- MJML-powered responsive rendering
- Merge tag support for dynamic content
- Template management with bulk actions
- Test email sending
- Asset upload and management
- Zero-configuration setup

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Quick Start

```bash
composer require spire/mail

php artisan spire-mail:install
```

That's it! Access the editor at `/admin/mail`.

## Installation Options

The install command accepts the following options:

| Option | Description |
|--------|-------------|
| `--publish-config` | Publish configuration file for customization |
| `--no-migrate` | Skip running migrations |
| `--force` | Overwrite existing configuration |

## Sending Emails

### Using SpireTemplateMailable

```php
use SpireMail\Mail\SpireTemplateMailable;
use Illuminate\Support\Facades\Mail;

Mail::to('user@example.com')->send(
    new SpireTemplateMailable('welcome-email', [
        'user_name' => 'John Doe',
        'activation_link' => 'https://example.com/activate/abc123',
    ])
);
```

### Using the Trait

```php
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use SpireMail\Mail\Concerns\UsesSpireTemplate;

class WelcomeEmail extends Mailable
{
    use UsesSpireTemplate;

    public function __construct(protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->getSpireSubject());
    }

    public function content(): Content
    {
        return $this->useTemplate('welcome-email')
            ->withSpireData([
                'user_name' => $this->user->name,
                'user_email' => $this->user->email,
            ])
            ->getSpireContent();
    }
}
```

### Using the Facade

```php
use SpireMail\Facades\SpireMail;

$html = SpireMail::render('newsletter-template', [
    'headline' => 'Weekly Update',
    'content' => 'Here is your weekly digest...',
]);
```

## Merge Tags

Use merge tags in your templates for dynamic content:

```
Hello {{user_name}},

Welcome to {{app_name}}!
```

### Global Merge Tags

Define global merge tags in `config/spire-mail.php`:

```php
'merge_tags' => [
    'app_name' => fn () => config('app.name'),
    'app_url' => fn () => config('app.url'),
    'current_year' => fn () => date('Y'),
],
```

## Available Blocks

| Block | Description |
|-------|-------------|
| Text | Rich text content with formatting |
| Heading | H1, H2, H3 headings |
| Image | Responsive images with optional links |
| Button | Call-to-action buttons |
| Divider | Horizontal lines |
| Spacer | Vertical spacing |
| HTML | Raw HTML content |
| Video | Video embeds with thumbnail fallback |
| Social Icons | Social media icon links |
| Row | Multi-column layouts (1-3 columns) |

## Configuration

Publish the configuration file:

```bash
php artisan spire-mail:install --publish-config
```

Or manually:

```bash
php artisan vendor:publish --tag=spire-mail-config
```

### Key Configuration Options

```php
return [
    // Route prefix for the admin interface
    'route_prefix' => env('SPIRE_MAIL_PREFIX', 'admin/mail'),

    // Middleware applied to all routes
    'middleware' => ['web', 'auth'],

    // Authorization settings
    'authorization' => [
        'enabled' => true,
        'gate' => 'manage-mail-templates',
    ],

    // Asset storage
    'storage' => [
        'disk' => env('SPIRE_MAIL_DISK', 'public'),
        'path' => 'mail-assets',
    ],
];
```

## Authorization

By default, Spire Mail allows all authenticated users to manage templates. To restrict access, define a gate in your `AppServiceProvider` or `AuthServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('manage-mail-templates', function ($user) {
        return $user->hasRole('admin');
    });
}
```

To disable authorization entirely:

```php
// config/spire-mail.php
'authorization' => [
    'enabled' => false,
],
```

## Working with Templates

### Query Templates

```php
use SpireMail\Models\MailTemplate;

// Get all active templates
$templates = MailTemplate::active()->get();

// Find by slug
$template = MailTemplate::where('slug', 'welcome-email')->first();
```

### Create Templates Programmatically

```php
$template = MailTemplate::create([
    'name' => 'Welcome Email',
    'subject' => 'Welcome to {{app_name}}!',
    'content' => ['version' => '1.0', 'blocks' => []],
    'is_active' => true,
]);
```

## Custom Block Renderers

Create custom block types:

```php
namespace App\Mail\Blocks;

use SpireMail\Rendering\BlockRenderers\BaseBlockRenderer;

class CustomBlockRenderer extends BaseBlockRenderer
{
    public function render(array $block, array $data = []): string
    {
        $props = $block['props'] ?? [];

        return sprintf(
            '<mj-section><mj-column><mj-text>%s</mj-text></mj-column></mj-section>',
            $this->processMergeTags($props['content'] ?? '', $data)
        );
    }
}
```

Register in config:

```php
'blocks' => [
    'custom-block' => \App\Mail\Blocks\CustomBlockRenderer::class,
],
```

## Publishing Assets

```bash
php artisan vendor:publish --tag=spire-mail-config
php artisan vendor:publish --tag=spire-mail-migrations
php artisan vendor:publish --tag=spire-mail-views
php artisan vendor:publish --tag=spire-mail-lang
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
