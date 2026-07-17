<?php

namespace App\Filament\Portal\Concerns;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Models\User;

trait RestrictsToPrimaryClientUser
{
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->user_type !== UserType::Portal) {
            return false;
        }

        return $user->clientMemberships()
            ->active()
            ->where('role', ClientUserRole::Primary)
            ->exists();
    }
}
