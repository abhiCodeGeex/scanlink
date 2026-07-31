<?php

namespace App\Support;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Sends Filament database (bell) notifications alongside the transactional emails,
 * targeting only the users an email is actually addressed to. Every path is
 * defensively wrapped: a notification failure must never break the email or the
 * surrounding action/flow.
 */
class SystemNotifier
{
    public static function toUser(
        ?User $user,
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
    ): void {
        if (! $user || ! Schema::hasTable('notifications')) {
            return;
        }

        try {
            // Filament's DatabaseNotification is queued (QUEUE_CONNECTION=redis); send it
            // synchronously so the bell notification is written even with no queue worker,
            // and so it works inside console commands (expiry cron) too.
            $user->notifyNow(
                Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon($icon)
                    ->iconColor($color)
                    ->toDatabase()
            );
        } catch (\Throwable $exception) {
            Log::warning('System notification failed', [
                'user_id' => $user->getKey(),
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Notify a client member (ClientUser) via its linked auth User.
     */
    public static function toMember(
        ?ClientUser $member,
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
    ): void {
        self::toUser($member?->authUser, $title, $body, $icon, $color);
    }

    /**
     * Notify a client's primary member (the account owner's auth User).
     */
    public static function toClientPrimary(
        ?Client $client,
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
    ): void {
        if (! $client) {
            return;
        }

        try {
            $client->loadMissing('primaryUser.authUser');
        } catch (\Throwable) {
            // ignore
        }

        self::toMember($client->primaryUser, $title, $body, $icon, $color);
    }

    /**
     * Notify every admin user (the email path targets the config admin inbox;
     * the "related users" for a bell notification are the admin accounts).
     */
    public static function toAdmins(
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
    ): void {
        try {
            User::query()
                ->where('user_type', UserType::Admin)
                ->get()
                ->each(fn (User $user) => self::toUser($user, $title, $body, $icon, $color));
        } catch (\Throwable $exception) {
            Log::warning('Admin system notification failed', ['error' => $exception->getMessage()]);
        }
    }
}
