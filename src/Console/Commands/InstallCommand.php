<?php

namespace SpireMail\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'spire-mail:install')]
class InstallCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'spire-mail:install
        {--force : Overwrite existing configuration}
        {--no-migrate : Skip running migrations}
        {--publish-config : Publish configuration file}';

    /**
     * @var string
     */
    protected $description = 'Install and configure Spire Mail package';

    public function handle(): int
    {
        $this->displayHeader();

        if (! $this->checkPrerequisites()) {
            return self::FAILURE;
        }

        if ($this->option('publish-config')) {
            $this->publishConfig();
        }

        if (! $this->option('no-migrate')) {
            $this->runMigrations();
        }

        $this->registerDefaultGate();
        $this->displaySuccessMessage();

        return self::SUCCESS;
    }

    protected function displayHeader(): void
    {
        $this->newLine();
        $this->components->info('Installing Spire Mail...');
        $this->newLine();
    }

    protected function checkPrerequisites(): bool
    {
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->components->error('Database connection failed. Please check your configuration.');

            return false;
        }

        return true;
    }

    protected function publishConfig(): void
    {
        $configPath = config_path('spire-mail.php');

        if (File::exists($configPath) && ! $this->option('force')) {
            if (! $this->components->confirm('Config file already exists. Overwrite?', false)) {
                $this->components->warn('Skipping config publish.');

                return;
            }
        }

        $this->components->task('Publishing configuration', function () {
            Artisan::call('vendor:publish', [
                '--tag' => 'spire-mail-config',
                '--force' => $this->option('force'),
            ]);

            return true;
        });
    }

    protected function runMigrations(): void
    {
        if (Schema::hasTable('mail_templates')) {
            $this->components->info('Mail templates table already exists. Skipping migration.');

            return;
        }

        $this->components->task('Running migrations', function () {
            Artisan::call('migrate', [
                '--force' => app()->environment('production') ? false : true,
            ]);

            return true;
        });
    }

    protected function registerDefaultGate(): void
    {
        $gateName = config('spire-mail.authorization.gate', 'manage-mail-templates');

        if (Gate::has($gateName)) {
            $this->components->info("Gate '{$gateName}' is already defined.");

            return;
        }

        Gate::define($gateName, fn ($user) => true);

        $this->components->info("Registered default gate '{$gateName}' (allows all authenticated users).");
        $this->components->warn('Customize this gate in AuthServiceProvider for production.');
    }

    protected function displaySuccessMessage(): void
    {
        $this->newLine();
        $this->components->success('Spire Mail installed successfully!');
        $this->newLine();

        $routePrefix = config('spire-mail.route_prefix', 'admin/mail');

        $this->components->bulletList([
            "Access the editor at: <comment>/{$routePrefix}</comment>",
            'Routes use middleware: <comment>web, auth</comment>',
            'Authorization gate: <comment>'.config('spire-mail.authorization.gate', 'manage-mail-templates').'</comment>',
        ]);

        $this->newLine();
        $this->components->info('Next Steps:');
        $this->components->bulletList([
            'Customize the authorization gate in your AuthServiceProvider',
            'Configure storage disk in config/spire-mail.php if needed',
        ]);

        $this->newLine();
    }
}
