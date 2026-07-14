<?php

namespace App\Filament\Portal\Concerns;

use App\Models\Client;
use App\Models\ClientUser;
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
            ->where('status', true)
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

    public function canAddCode(): bool
    {
        $member = $this->currentClientUser();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_addcode;
    }

    public function canEditCode(): bool
    {
        $member = $this->currentClientUser();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_edit;
    }

    public function canDeleteCode(): bool
    {
        $member = $this->currentClientUser();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_delete;
    }
}
