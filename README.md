# Spire Mail

A visual drag-and-drop email template editor for Laravel. Build beautiful, responsive email templates with an intuitive editor and send them using Laravel's mail system.

## Features

- Visual drag-and-drop email editor
- 10+ content blocks (text, heading, image, button, divider, spacer, HTML, video, social icons, multi-column rows)
- Real-time preview
- MJML-powered responsive rendering
- Merge tag support for dynamic content
- Template management with bulk actions
- Test email sending
- Asset upload and management
- Fully customizable and extensible

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Node.js 18+ (for frontend assets)

## Installation

### 1. Install via Composer

```bash
composer require spire/mail
```

### 2. Run the Install Command

```bash
php artisan spire-mail:install
```

This will:
- Run database migrations
- Register a default authorization gate (allows all authenticated users)

**Options:**
- `--publish-config` - Publish the configuration file for customization
- `--no-migrate` - Skip running migrations
- `--force` - Overwrite existing configuration

### 3. Install Frontend Dependencies (Optional)

The package uses Vue 3 and Inertia.js for the editor interface. Install the required npm package:

```bash
npm install @sabrenski/spire-mail
```

### 4. Register Inertia Pages

In your `vite.config.js`, ensure the Spire Mail pages are resolved:

```javascript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            'spire-mail': '@sabrenski/spire-mail',
        },
    },
})
```

In your `resources/js/app.js`, register the Spire Mail pages:

```javascript
import { createApp, h } from 'vue'
import { createInertiaApp, resolvePageComponent } from '@inertiajs/vue3'

createInertiaApp({
    resolve: (name) => {
        // Handle Spire Mail pages
        if (name.startsWith('spire-mail::')) {
            const pageName = name.replace('spire-mail::', '')
            return resolvePageComponent(
                `./vendor/spire-mail/Pages/${pageName}.vue`,
                import.meta.glob('./vendor/spire-mail/Pages/**/*.vue')
            )
        }

        // Handle your application pages
        return resolvePageComponent(
            `./Pages/${pageName}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        )
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
```

### 5. Build Assets

```bash
npm run build
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=spire-mail-config
```

### Configuration Options

```php
// config/spire-mail.php

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

    // Default template settings
    'templates' => [
        'content_width' => 600,
        'font_family' => 'Arial, sans-serif',
        'background_color' => '#f5f5f5',
        'content_background_color' => '#ffffff',
    ],

    // Asset storage configuration
    'storage' => [
        'disk' => env('SPIRE_MAIL_DISK', 'public'),
        'path' => 'mail-assets',
    ],

    // Custom block renderers
    'blocks' => [
        // 'custom-block' => \App\Mail\Blocks\CustomBlockRenderer::class,
    ],

    // Global merge tags
    'merge_tags' => [
        'app_name' => fn () => config('app.name'),
        'app_url' => fn () => config('app.url'),
        'current_year' => fn () => date('Y'),
    ],

    // Logging configuration
    'logging' => [
        'enabled' => env('SPIRE_MAIL_LOGGING', true),
        'channel' => env('SPIRE_MAIL_LOG_CHANNEL', 'spire-mail'),
        'level' => env('SPIRE_MAIL_LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
];
```

## Authorization

By default, Spire Mail uses Laravel Gates for authorization. Define the gate in your `AuthServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('manage-mail-templates', function ($user) {
        return $user->hasRole('admin'); // Your authorization logic
    });
}
```

To disable authorization entirely, set `authorization.enabled` to `false` in the config.

## Usage

### Accessing the Editor

Once installed, access the email template editor at:

```
https://your-app.com/admin/mail
```

### Sending Emails

#### Option 1: Using SpireTemplateMailable

The simplest way to send emails using Spire templates:

```php
use SpireMail\Mail\SpireTemplateMailable;
use Illuminate\Support\Facades\Mail;

// Send using template slug
Mail::to('user@example.com')->send(
    new SpireTemplateMailable('welcome-email', [
        'user_name' => 'John Doe',
        'activation_link' => 'https://example.com/activate/abc123',
    ])
);
```

#### Option 2: Using the Trait in Custom Mailables

For more control, use the `UsesSpireTemplate` trait in your own Mailable:

```php
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use SpireMail\Mail\Concerns\UsesSpireTemplate;

class WelcomeEmail extends Mailable
{
    use UsesSpireTemplate;

    public function __construct(
        protected User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSpireSubject(),
        );
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

#### Option 3: Using the Facade

Render templates programmatically:

```php
use SpireMail\Facades\SpireMail;

// Render a template to HTML
$html = SpireMail::render('newsletter-template', [
    'headline' => 'Weekly Update',
    'content' => 'Here is your weekly digest...',
]);

// Find a template
$template = SpireMail::findTemplate('welcome-email');

// Process merge tags in any string
$subject = SpireMail::processMergeTags(
    'Welcome to {{app_name}}!',
    ['app_name' => 'My App']
);
```

### Merge Tags

Use merge tags in your templates for dynamic content:

```
Hello {{user_name}},

Welcome to {{app_name}}!

Best regards,
The Team
```

#### Global Merge Tags

Define global merge tags available in all templates:

```php
// config/spire-mail.php
'merge_tags' => [
    'app_name' => fn () => config('app.name'),
    'app_url' => fn () => config('app.url'),
    'current_year' => fn () => date('Y'),
    'support_email' => fn () => 'support@example.com',
],
```

#### Template-Specific Merge Tags

Pass merge tags when sending:

```php
new SpireTemplateMailable('order-confirmation', [
    'order_number' => $order->number,
    'order_total' => number_format($order->total, 2),
    'items' => $order->items->pluck('name')->implode(', '),
]);
```

## Available Blocks

| Block | Description |
|-------|-------------|
| **Text** | Rich text content with formatting |
| **Heading** | H1, H2, H3 headings |
| **Image** | Responsive images with optional links |
| **Button** | Call-to-action buttons |
| **Divider** | Horizontal lines and spacers |
| **Spacer** | Vertical spacing |
| **HTML** | Raw HTML content |
| **Video** | Video embeds with thumbnail fallback |
| **Social Icons** | Social media icon links |
| **Row** | Multi-column layouts (1-3 columns) |

## Working with Templates Programmatically

### Creating Templates

```php
use SpireMail\Models\MailTemplate;

$template = MailTemplate::create([
    'name' => 'Welcome Email',
    'subject' => 'Welcome to {{app_name}}!',
    'description' => 'Sent to new users after registration',
    'content' => [
        'version' => '1.0',
        'blocks' => [
            // Block structure...
        ],
    ],
    'settings' => [
        'fontFamily' => 'Arial, sans-serif',
        'backgroundColor' => '#f5f5f5',
        'contentBackgroundColor' => '#ffffff',
        'contentWidth' => 600,
    ],
    'is_active' => true,
]);
```

### Querying Templates

```php
use SpireMail\Models\MailTemplate;

// Get all active templates
$templates = MailTemplate::active()->get();

// Find by slug
$template = MailTemplate::where('slug', 'welcome-email')->first();

// Or use the helper
$template = MailTemplate::findBySlugOrFail('welcome-email');
```

### Rendering Templates

```php
// Render to HTML
$html = $template->render([
    'user_name' => 'John',
]);
```

## Custom Block Renderers

Create custom block types by implementing a renderer:

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
// config/spire-mail.php
'blocks' => [
    'custom-block' => \App\Mail\Blocks\CustomBlockRenderer::class,
],
```

## Publishing Assets

```bash
# Publish everything
php artisan vendor:publish --provider="SpireMail\SpireMailServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=spire-mail-config
php artisan vendor:publish --tag=spire-mail-migrations
php artisan vendor:publish --tag=spire-mail-views
php artisan vendor:publish --tag=spire-mail-assets
php artisan vendor:publish --tag=spire-mail-lang
```

## Routes

The package registers the following routes:

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | /admin/mail | spire-mail.templates.index | List templates |
| GET | /admin/mail/templates/create | spire-mail.templates.create | Create form |
| POST | /admin/mail/templates | spire-mail.templates.store | Store template |
| GET | /admin/mail/templates/{id} | spire-mail.templates.edit | Edit template |
| PUT | /admin/mail/templates/{id} | spire-mail.templates.update | Update template |
| DELETE | /admin/mail/templates/{id} | spire-mail.templates.destroy | Delete template |
| DELETE | /admin/mail/templates/bulk | spire-mail.templates.bulk-destroy | Bulk delete |
| PATCH | /admin/mail/templates/{id}/toggle-status | spire-mail.templates.toggle-status | Toggle status |
| POST | /admin/mail/templates/{id}/duplicate | spire-mail.templates.duplicate | Duplicate |
| POST | /admin/mail/templates/{id}/preview | spire-mail.templates.preview | Preview |
| POST | /admin/mail/templates/{id}/send-test | spire-mail.templates.send-test | Send test |

## Testing

```bash
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
