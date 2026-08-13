<?php

namespace App\Providers;

use App\Filament\Support\FormPlaceholderDefaults;
use App\Support\RestrictOutboundMailToYopmail;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Artisan commands that destroy or empty the database.
     *
     * @var list<string>
     */
    private const DESTRUCTIVE_COMMANDS = [
        'migrate:fresh',
        'migrate:refresh',
        'db:wipe',
        'scanlink:keep-one-dummy',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            \App\Http\Responses\FilamentLogoutResponse::class,
        );

        // Docker Desktop / shared volumes: Blade touch() on root-owned compiled
        // views under storage/framework/views causes php-fpm 500s. Prefer a
        // container-local path (also set via VIEW_COMPILED_PATH in compose).
        $compiled = env('VIEW_COMPILED_PATH');
        if (! filled($compiled) && is_file('/.dockerenv')) {
            $compiled = '/tmp/laravel-views';
        }
        if (filled($compiled)) {
            if (! is_dir($compiled)) {
                @mkdir($compiled, 0777, true);
            }
            $this->callAfterResolving('config', function ($config) use ($compiled): void {
                $config->set('view.compiled', $compiled);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FormPlaceholderDefaults::register();

        Event::listen(MessageSending::class, RestrictOutboundMailToYopmail::class);

        // Docker/php-fpm: BladeCompiler touch() after compile fails with
        // "Utime failed: Operation not permitted" when compiled views were
        // created as root via `docker exec`. Suppress that warning only.
        $previous = set_error_handler(static function (
            int $severity,
            string $message,
            string $file = '',
            int $line = 0,
        ) use (&$previous): bool {
            if ($severity === E_WARNING && str_contains($message, 'touch(): Utime failed')) {
                return true;
            }

            if (is_callable($previous)) {
                return (bool) $previous($severity, $message, $file, $line);
            }

            return false;
        });

        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $command = $event->command;

            if ($command === null || ! in_array($command, self::DESTRUCTIVE_COMMANDS, true)) {
                return;
            }

            // Feature tests are required to use isolated sqlite :memory:. Allow their
            // RefreshDatabase migrate:fresh while keeping Docker MySQL hard-locked.
            if (
                app()->environment('testing')
                && (config('database.default') === 'sqlite' || env('DB_CONNECTION') === 'sqlite')
                && (
                    config('database.connections.sqlite.database') === ':memory:'
                    || env('DB_DATABASE') === ':memory:'
                )
            ) {
                return;
            }

            if (filter_var(env('ALLOW_DB_WIPE', false), FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            /** @var OutputInterface $output */
            $output = $event->output;
            $output->writeln('<error>Blocked: '.$command.' would wipe database data.</error>');
            $output->writeln('<comment>Set ALLOW_DB_WIPE=1 only after explicit user approval, then retry.</comment>');
            $output->writeln('<comment>Tests should use sqlite (phpunit.xml), not Docker MySQL.</comment>');

            exit(1);
        });
    }
}
