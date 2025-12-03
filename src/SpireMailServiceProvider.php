<?php

namespace SpireMail;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use SpireMail\Console\Commands\InstallCommand;
use SpireMail\Contracts\TemplateRendererInterface;
use SpireMail\Http\Middleware\AuthorizeMailManagement;
use SpireMail\Rendering\TemplateRenderer;
use SpireMail\Services\SpireMailManager;
use SpireMail\Support\HtmlSanitizer;
use SpireMail\Tags\Conditionals\ConditionalProcessor;
use SpireMail\Tags\Formatters\FormatterRegistry;
use SpireMail\Tags\TagParser;
use SpireMail\Tags\TagProcessor;
use SpireMail\Tags\TagRegistry;

class SpireMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/spire-mail.php', 'spire-mail');

        $this->app->singleton(HtmlSanitizer::class);
        $this->app->singleton(TagRegistry::class);
        $this->app->singleton(TagParser::class);
        $this->app->singleton(FormatterRegistry::class);
        $this->app->singleton(ConditionalProcessor::class);
        $this->app->singleton(TagProcessor::class);
        $this->app->singleton(SpireMailManager::class);
        $this->app->bind(TemplateRendererInterface::class, TemplateRenderer::class);

        $this->app->alias(SpireMailManager::class, 'spire-mail');
    }

    public function boot(): void
    {
        $this->registerLogging();
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerPublishables();
        $this->registerCommands();
        $this->registerDefaultGate();
        $this->registerInertiaSharedData();
    }

    protected function registerInertiaSharedData(): void
    {
        if (class_exists(Inertia::class)) {
            Inertia::share('spireMailPrefix', fn () => '/'.ltrim(config('spire-mail.route_prefix', 'admin/mail'), '/'));
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    protected function registerDefaultGate(): void
    {
        $this->app->booted(function () {
            $gateName = config('spire-mail.authorization.gate', 'manage-mail-templates');

            if (! Gate::has($gateName)) {
                Gate::define($gateName, fn ($user) => true);
            }
        });
    }

    protected function registerLogging(): void
    {
        if (config('spire-mail.logging.enabled', true)) {
            $channel = config('spire-mail.logging.channel', 'spire-mail');

            $this->app->make('config')->set("logging.channels.{$channel}", [
                'driver' => 'daily',
                'path' => storage_path('logs/spire-mail.log'),
                'level' => config('spire-mail.logging.level', 'debug'),
                'days' => config('spire-mail.logging.days', 14),
            ]);
        }
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function routeConfiguration(): array
    {
        $middleware = config('spire-mail.middleware', ['web', 'auth']);

        if (config('spire-mail.authorization.enabled', true)) {
            $middleware[] = AuthorizeMailManagement::class;
        }

        return [
            'prefix' => config('spire-mail.route_prefix', 'admin/mail'),
            'middleware' => $middleware,
            'as' => 'spire-mail.',
        ];
    }

    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'spire-mail');
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'spire-mail');
    }

    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/spire-mail.php' => config_path('spire-mail.php'),
            ], 'spire-mail-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'spire-mail-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/spire-mail'),
            ], 'spire-mail-views');

            $this->publishes([
                __DIR__.'/../resources/js/Pages/Templates' => resource_path('js/Pages/SpireMail/Templates'),
            ], 'spire-mail-pages');

            $this->publishes([
                __DIR__.'/../resources/js/Components' => resource_path('js/Components/SpireMail'),
            ], 'spire-mail-components');

            $this->publishes([
                __DIR__.'/../lang' => lang_path('vendor/spire-mail'),
            ], 'spire-mail-lang');
        }
    }
}
