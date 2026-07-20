<?php

namespace App\Providers;

use App\Filament\Support\FormPlaceholderDefaults;
use Illuminate\Console\Events\CommandStarting;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FormPlaceholderDefaults::register();

        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $command = $event->command;

            if ($command === null || ! in_array($command, self::DESTRUCTIVE_COMMANDS, true)) {
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
