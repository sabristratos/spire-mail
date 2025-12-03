# Spire Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)
[![License](https://img.shields.io/packagist/l/spire/mail.svg?style=flat-square)](https://packagist.org/packages/spire/mail)

A visual drag-and-drop email template editor for Laravel. Build beautiful, responsive email templates with an intuitive editor and send them using Laravel's mail system.

## Features

- Drag-and-drop email editor with real-time preview
- 10+ content blocks (text, heading, image, button, divider, spacer, HTML, video, social icons, multi-column rows)
- MJML-powered responsive rendering
- Advanced tag system with formatters, conditionals, and inline fallbacks
- Global and template-specific tags with editor UI
- 9 built-in formatters (date, currency, uppercase, lowercase, capitalize, truncate, count, number, default)
- Conditional rendering with `{{#if}}`, `{{else}}`, `{{#unless}}`
- Template management with bulk actions
- Test email sending
- Asset upload and management
- Configurable route prefix and authorization
- Vue 3 components for custom integrations

## Requirements

### Backend
- PHP 8.2+
- Laravel 11 or 12

### Frontend
- Node.js 18+
- Vue 3
- Inertia.js

## Quick Start

```bash
# Install the Composer package
composer require spire/mail

# Run the install command
php artisan spire-mail:install

# Install the npm package
npm install @sabrenski/spire-mail

# Build your assets
npm run build
```

Access the editor at `/admin/mail` (or your configured route prefix).

## Installation

### Backend (Composer)

```bash
composer require spire/mail
```

### Frontend (NPM)

The Vue editor components are distributed as a separate npm package:

```bash
npm install @sabrenski/spire-mail
```

**Peer Dependencies:** Your project must have these packages installed:

| Package | Version | Description |
|---------|---------|-------------|
| `vue` | ^3.0.0 | Vue.js framework |
| `@sabrenski/spire-ui-vue` | ^0.2.0 | Spire UI component library |
| `@hugeicons/core-free-icons` | ^2.0.0 | Icon library |
| `@inertiajs/vue3` | ^2.0.0 | Inertia.js Vue adapter |

### Install Command

Run the install command to set up the package:

```bash
php artisan spire-mail:install
```

**Options:**

| Option | Description |
|--------|-------------|
| `--publish-config` | Publish configuration file for customization |
| `--no-migrate` | Skip running migrations |
| `--force` | Overwrite existing files |

## Configuration

### Route Prefix

By default, routes are registered at `/admin/mail`. Customize this with an environment variable:

```env
SPIRE_MAIL_PREFIX=email-admin
```

Routes will then be available at `/email-admin`.

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `SPIRE_MAIL_PREFIX` | `admin/mail` | Route prefix for the admin interface |
| `SPIRE_MAIL_DISK` | `public` | Storage disk for uploaded assets |
| `SPIRE_MAIL_VALIDATE_TAGS` | `true` | Enable/disable required tag validation |
| `SPIRE_MAIL_LOGGING` | `true` | Enable/disable logging |
| `SPIRE_MAIL_LOG_CHANNEL` | `spire-mail` | Log channel name |
| `SPIRE_MAIL_LOG_LEVEL` | `debug` | Log level |

### Configuration File

Publish the configuration file for full customization:

```bash
php artisan spire-mail:install --publish-config
```

Or manually:

```bash
php artisan vendor:publish --tag=spire-mail-config
```

**Key Configuration Options:**

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

    // Template defaults
    'templates' => [
        'content_width' => 600,
        'font_family' => 'Arial, sans-serif',
        'background_color' => '#f5f5f5',
        'content_background_color' => '#ffffff',
    ],

    // Asset storage
    'storage' => [
        'disk' => env('SPIRE_MAIL_DISK', 'public'),
        'path' => 'mail-assets',
    ],

    // Global merge tags
    'merge_tags' => [
        'app_name' => fn () => config('app.name'),
        'app_url' => fn () => config('app.url'),
        'current_year' => fn () => date('Y'),
    ],
];
```

### Authorization

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

### Tag Validation

Spire Mail automatically validates that all required tags are provided when sending emails:

```php
use SpireMail\Exceptions\MissingRequiredTagsException;
use SpireMail\Mail\SpireTemplateMailable;

try {
    Mail::to($user->email)->send(
        new SpireTemplateMailable('order-confirmation', [
            'user' => ['name' => $user->name],
            // Missing 'order' data - will throw if order.id is required
        ])
    );
} catch (MissingRequiredTagsException $e) {
    Log::error('Missing tags', [
        'template' => $e->templateSlug,
        'missing' => $e->missingTags,
    ]);
}
```

**Validate before queueing:**

```php
use SpireMail\Facades\SpireMail;

SpireMail::validateTags('order-confirmation', $data);
Mail::to($user)->queue(new SpireTemplateMailable('order-confirmation', $data));
```

**Disable validation:**

```env
SPIRE_MAIL_VALIDATE_TAGS=false
```

## Vue Editor Components

The npm package exports Vue components for custom integrations.

### Available Exports

```typescript
// Main editor components
import {
    EmailEditor,
    EditorSidebar,
    EditorCanvas,
    EditorProperties,
    PreviewModal,
} from '@sabrenski/spire-mail'

// Canvas components
import { CanvasBlock, CanvasDropZone } from '@sabrenski/spire-mail'

// Block components
import { TextBlock, ImageBlock, ButtonBlock } from '@sabrenski/spire-mail'

// Property panels
import { TextProperties, ImageProperties, ButtonProperties } from '@sabrenski/spire-mail'

// Page components (for custom routing)
import { TemplatesIndex, TemplatesEdit, TemplatesCreate } from '@sabrenski/spire-mail'

// Composables and stores
import { useEditorStore } from '@sabrenski/spire-mail'

// Types
import type {
    EmailBlock,
    EmailSettings,
    TemplateData,
    BlockDefinition,
    GlobalTag,
    TemplateTag,
} from '@sabrenski/spire-mail'

// Layout
import { DefaultLayout } from '@sabrenski/spire-mail'
```

### Custom Layouts

The Index and Create pages use Inertia's persistent layout pattern. After publishing the pages, you can customize the layout to use your own admin layout.

> **Note:** The Edit page (email editor) maintains its own full-screen layout and does not support custom layouts.

**Step 1: Publish the pages**

```bash
php artisan vendor:publish --tag=spire-mail-pages
```

**Step 2: Edit the layout in published files**

Update the Index and Create pages to use your layout:

```vue
// resources/js/Pages/SpireMail/Templates/Index.vue
<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

// ... rest of the component
</script>
```

```vue
// resources/js/Pages/SpireMail/Templates/Create.vue
<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

// ... rest of the component
</script>
```

**Your layout component** should render the default slot:

```vue
// resources/js/Layouts/AdminLayout.vue
<script setup lang="ts">
import { Sidebar, Navbar } from '@/Components'
</script>

<template>
    <div class="flex min-h-screen">
        <Sidebar />
        <div class="flex-1">
            <Navbar />
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
```

### Importing CSS

Include the package CSS in your application:

```typescript
// In your main.ts or app.ts
import '@sabrenski/spire-mail/style.css'
```

### Using EmailEditor

The main editor component for building email templates:

```vue
<script setup lang="ts">
import { EmailEditor, PreviewModal } from '@sabrenski/spire-mail'
import type { TemplateData, BlockDefinition, GlobalTag } from '@sabrenski/spire-mail'

interface Props {
    template: { data: TemplateData } | null
    availableBlocks: Record<string, BlockDefinition>
    globalTags: GlobalTag[]
}

const props = defineProps<Props>()

function handleSave(content, settings, tags) {
    // Save template via Inertia or API
}

function handlePreview(content, settings) {
    // Open preview modal
}
</script>

<template>
    <EmailEditor
        :template="template?.data"
        :available-blocks="availableBlocks"
        :global-tags="globalTags"
        show-back-link
        back-link-href="/admin/mail"
        @save="handleSave"
        @preview="handlePreview"
    />
</template>
```

### Using PreviewModal

Preview and send test emails:

```vue
<script setup lang="ts">
import { ref } from 'vue'
import { PreviewModal } from '@sabrenski/spire-mail'

const showPreview = ref(false)
const templateId = ref(1)
const content = ref([])
const settings = ref({})
</script>

<template>
    <PreviewModal
        v-model="showPreview"
        :template-id="templateId"
        :content="content"
        :settings="settings"
        @test-sent="handleTestSent"
    />
</template>
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

## API Endpoints

All endpoints use the configured route prefix (default: `/admin/mail`).

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/` | GET | Template list page |
| `/templates` | POST | Create template |
| `/templates/{id}` | GET | Edit template page |
| `/templates/{id}` | PUT | Update template |
| `/templates/{id}` | DELETE | Delete template |
| `/templates/{id}/toggle-status` | PATCH | Toggle active status |
| `/templates/{id}/duplicate` | POST | Duplicate template |
| `/templates/{id}/preview` | POST | Generate preview HTML |
| `/templates/{id}/send-test` | POST | Send test email |
| `/templates/{id}/tags` | GET | Get template tags |
| `/templates/{id}/tags` | PUT | Update template tags |
| `/tags` | GET | List global tags |
| `/tags/formatters` | GET | List available formatters |
| `/assets/upload` | POST | Upload asset |
| `/assets/{filename}` | DELETE | Delete asset |

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

## Publishing Assets

```bash
php artisan vendor:publish --tag=spire-mail-config      # Configuration
php artisan vendor:publish --tag=spire-mail-migrations  # Migrations
php artisan vendor:publish --tag=spire-mail-views       # Blade views
php artisan vendor:publish --tag=spire-mail-lang        # Language files
php artisan vendor:publish --tag=spire-mail-pages       # Vue pages
php artisan vendor:publish --tag=spire-mail-components  # Vue components
```

## Troubleshooting

### Blank page at /admin/mail

Ensure you've run the install command and built your assets:

```bash
php artisan spire-mail:install
npm run build
```

### 403 Forbidden

Define the authorization gate or disable authorization:

```php
// Option 1: Define the gate
Gate::define('manage-mail-templates', fn($user) => $user->isAdmin());

// Option 2: Disable authorization
// config/spire-mail.php
'authorization' => ['enabled' => false],
```

### Missing peer dependencies

Install all required npm packages:

```bash
npm install vue @sabrenski/spire-ui-vue @hugeicons/core-free-icons @inertiajs/vue3
```

### Asset upload fails

Ensure your storage is properly configured:

1. Run `php artisan storage:link`
2. Check that `storage/app/public` is writable
3. Verify `SPIRE_MAIL_DISK` is correctly configured

## License

MIT License. See [LICENSE](LICENSE) for more information.
