<?php

namespace App\Filament\Portal\Concerns;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait InteractsWithClientMembership
{
    public function currentClientUser(): ?ClientUser
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return once(fn (): ?ClientUser => $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first());
    }

    public function currentClient(): ?Client
    {
        return $this->currentClientUser()?->client;
    }

    /**
     * Profile dropdown options with legacy-safe labels (never blank).
     *
     * @param  callable(\Illuminate\Database\Eloquent\Builder): void|null  $constrain
     * @return Collection<int|string, string>
     */
    public function clientProfileOptions(?callable $constrain = null): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::selectOptionsForClient((int) $client->id, $constrain);
    }

    public function requireClientUser(): ClientUser
    {
        $member = $this->currentClientUser();

        if (! $member) {
            abort(403, 'No active client membership.');
        }

        return $member;
    }

    public function requireClient(): Client
    {
        $client = $this->currentClient();

        if (! $client) {
            abort(403, 'No active client account.');
        }

        return $client;
    }

    public function isPrimaryUser(): bool
    {
        return (bool) $this->currentClientUser()?->isPrimary();
    }

    public function canAddCode(): bool
    {
        return static::memberCanAddCode($this->currentClientUser());
    }

    public function canEditCode(): bool
    {
        return static::memberCanEditCode($this->currentClientUser());
    }

    public function canDeleteCode(): bool
    {
        return static::memberCanDeleteCode($this->currentClientUser());
    }

    public function canAccessAnalytics(): bool
    {
        return static::memberCanAccessAnalytics($this->currentClientUser());
    }

    public function canAccessFormSubmissions(): bool
    {
        return static::memberCanAccessFormSubmissions($this->currentClientUser());
    }

    public function canDownload(): bool
    {
        return static::memberCanDownload($this->currentClientUser());
    }

    public function canOrderLabel(): bool
    {
        return static::memberCanOrderLabel($this->currentClientUser());
    }

    public function canAccessFormBuilder(): bool
    {
        return static::memberCanAccessFormBuilder($this->currentClientUser());
    }

    public function canAccessVisitorLog(): bool
    {
        return static::memberCanAccessVisitorLog($this->currentClientUser());
    }

    public static function portalMembership(): ?ClientUser
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        // Live client logins may be typed portal or voc while still having client_users rows.
        if (! in_array($user->user_type, [UserType::Portal, UserType::Voc], true)) {
            return null;
        }

        return $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first();
    }

    public static function memberCanAddCode(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_addcode;
    }

    public static function memberCanEditCode(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_edit;
    }

    public static function memberCanDeleteCode(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_delete;
    }

    public static function memberCanAccessAnalytics(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_analytics;
    }

    public static function memberCanAccessFormSubmissions(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_form_submission;
    }

    public static function memberCanAccessFormBuilder(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary()
            || (bool) $member->access_form_submission
            || (bool) $member->access_edit;
    }

    public static function memberCanDownload(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_download;
    }

    public static function memberCanOrderLabel(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_label;
    }

    public static function memberCanAccessVisitorLog(?ClientUser $member): bool
    {
        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_log;
    }
}
