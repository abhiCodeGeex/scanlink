<?php

namespace App\Filament\Portal\Concerns;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait InteractsWithClientMembership
{
    public function currentClientUser(): ?ClientUser
    {
        $user = Auth::user();

        if (! $user instanceof User) {
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

    public static function portalMembership(): ?ClientUser
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        if (! in_array($user->user_type, [UserType::Portal, UserType::Voc], true)) {
            return null;
        }

        return $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first();
    }
}
