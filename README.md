# Spire Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)
[![License](https://img.shields.io/packagist/l/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)

A visual drag-and-drop email template editor for Laravel. Build beautiful, responsive email templates with an intuitive editor and send them using Laravel's mail system.

## Features

- Drag-and-drop email editor with real-time preview
- 10+ content blocks (text, heading, image, button, divider, spacer, HTML, video, social icons, multi-column rows)
- MJML-powered responsive rendering
- **Advanced tag system** with formatters, conditionals, and inline fallbacks
- Global and template-specific tags with editor UI
- 9 built-in formatters (date, currency, uppercase, lowercase, capitalize, truncate, count, number, default)
- Conditional rendering with `{{#if}}`, `{{else}}`, `{{#unless}}`
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

## Tag System

Spire Mail features a powerful tag system for dynamic content with formatters, conditionals, and inline fallbacks.

### Basic Tags

Use double-brace syntax with dot notation for nested data:

```
Hello {{user.name}},

Welcome to {{app_name}}!

Your order #{{order.id}} was placed on {{order.date}}.
```

### Inline Fallbacks

Provide default values for missing tags:

```
Hello {{user.nickname|default:Valued Customer}},

Your discount: {{discount_code|default:No discount applied}}
```

### Formatters

Apply formatters using pipe syntax:

| Formatter | Syntax | Example | Output |
|-----------|--------|---------|--------|
| `date` | `{{tag\|date:format}}` | `{{order.date\|date:d/m/Y}}` | `25/12/2024` |
| `currency` | `{{tag\|currency:code}}` | `{{total\|currency:EUR}}` | `€99.99` |
| `uppercase` | `{{tag\|uppercase}}` | `{{name\|uppercase}}` | `JOHN DOE` |
| `lowercase` | `{{tag\|lowercase}}` | `{{email\|lowercase}}` | `john@example.com` |
| `capitalize` | `{{tag\|capitalize}}` | `{{name\|capitalize}}` | `John Doe` |
| `truncate` | `{{tag\|truncate:length}}` | `{{desc\|truncate:50}}` | `First 50 chars...` |
| `count` | `{{tag\|count}}` | `{{items\|count}}` | `5` |
| `number` | `{{tag\|number:decimals}}` | `{{price\|number:2}}` | `99.00` |

### Conditionals

Show or hide content based on data:

```html
{{#if user.premium}}
<p>Thank you for being a premium member!</p>
{{/if}}

{{#if order.discount}}
<p>You saved {{order.discount|currency:USD}}!</p>
{{else}}
<p>No discount applied to this order.</p>
{{/if}}

{{#unless user.verified}}
<p style="color: orange;">Please verify your email address.</p>
{{/unless}}
```

### Global Tags

Global tags are available in all templates. Define them in `config/spire-mail.php`:

```php
'merge_tags' => [
    'app_name' => fn () => config('app.name'),
    'app_url' => fn () => config('app.url'),
    'current_year' => fn () => date('Y'),
    'support_email' => 'support@example.com',
],
```

### Registering Tags Programmatically

Register global tags in a service provider:

```php
use SpireMail\Facades\SpireMail;

public function boot(): void
{
    SpireMail::registerTags([
        'company_name' => [
            'value' => 'Acme Inc',
            'label' => 'Company Name',
            'description' => 'The company name',
        ],
        'support_email' => [
            'value' => fn () => config('mail.from.address'),
            'label' => 'Support Email',
            'description' => 'Support contact email',
            'example' => 'support@example.com',
        ],
    ]);

    // Or register a single tag
    SpireMail::registerTag('current_date', [
        'value' => fn () => now()->format('F j, Y'),
        'label' => 'Current Date',
    ]);
}
```

### Template-Specific Tags

Define tags per template in the editor UI (Tags tab) or programmatically:

```php
use SpireMail\Models\MailTemplate;

$template = MailTemplate::where('slug', 'order-confirmation')->first();

$template->setTags([
    [
        'key' => 'user.name',
        'label' => 'User Name',
        'description' => 'The customer\'s full name',
        'type' => 'string',
        'required' => true,
        'example' => 'John Doe',
    ],
    [
        'key' => 'order.total',
        'label' => 'Order Total',
        'description' => 'Total order amount',
        'type' => 'number',
        'required' => true,
        'example' => '99.99',
    ],
]);

$template->save();
```

### Using Tags When Sending

Pass data when sending emails:

```php
use SpireMail\Mail\SpireTemplateMailable;
use Illuminate\Support\Facades\Mail;

Mail::to($user->email)->send(
    new SpireTemplateMailable('order-confirmation', [
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'premium' => $user->is_premium,
        ],
        'order' => [
            'id' => $order->id,
            'date' => $order->created_at,
            'total' => $order->total,
            'discount' => $order->discount_amount,
            'items' => $order->items->toArray(),
        ],
    ])
);
```

### Template Example

```html
<h1>Hello {{user.name|default:Valued Customer}}!</h1>

<p>Thank you for your order #{{order.id}}.</p>

<p>Order placed on: {{order.date|date:F j, Y}}</p>
<p>Total: {{order.total|currency:USD}}</p>
<p>Items: {{order.items|count}}</p>

{{#if order.discount}}
<p style="color: green;">You saved {{order.discount|currency:USD}}!</p>
{{/if}}

{{#unless user.verified}}
<p style="color: orange;">Please verify your email address.</p>
{{/unless}}

<p>Best regards,<br>{{app_name}}</p>
<p>&copy; {{current_year}} All rights reserved.</p>
```

### Tag API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/admin/mail/tags` | GET | List all global tags |
| `/admin/mail/tags/formatters` | GET | List available formatters |
| `/admin/mail/templates/{id}/tags` | GET | Get template tags |
| `/admin/mail/templates/{id}/tags` | PUT | Update template tags |

### Tag Validation

Spire Mail automatically validates that all required tags are provided when sending emails. If a template defines tags as required and the data is missing, an exception is thrown.

```php
use SpireMail\Exceptions\MissingRequiredTagsException;
use SpireMail\Mail\SpireTemplateMailable;

// This will throw MissingRequiredTagsException if required tags are missing
try {
    Mail::to($user->email)->send(
        new SpireTemplateMailable('order-confirmation', [
            'user' => ['name' => $user->name],
            // Missing 'order' data - will throw if order.id is required
        ])
    );
} catch (MissingRequiredTagsException $e) {
    // Handle missing tags
    Log::error('Missing tags', [
        'template' => $e->templateSlug,
        'missing' => $e->missingTags,
    ]);
}
```

#### Manual Validation

Validate tags before queueing emails to catch errors early:

```php
use SpireMail\Facades\SpireMail;

// Validate before queueing
SpireMail::validateTags('order-confirmation', $data);
Mail::to($user)->queue(new SpireTemplateMailable('order-confirmation', $data));
```

#### Disable Validation

To disable tag validation globally:

```env
SPIRE_MAIL_VALIDATE_TAGS=false
```

Or in config:

```php
// config/spire-mail.php
'validation' => [
    'required_tags' => false,
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
