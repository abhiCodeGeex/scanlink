<?php

namespace App\Filament\Portal\Concerns;

use App\Enums\UserType;
use App\Models\User;
use App\Support\LoginEntrance;

/**
 * Pages that legacy shows only on the full "My Account" submenu — Code Balance,
 * Purchase codes, Multiple Code Renewal. Kohana builds that submenu from
 * users.is_sub_user (Controller_Template_Master::before), never from users.role.
 */
trait RestrictsToPrimaryClientUser
{
    public static function canAccess(): bool
    {
        $user = auth()->user();

        // By door, not user_type: an admin who is also a client member is user_type = admin.
        if (! $user instanceof User || ! LoginEntrance::isPortalSession($user)) {
            return false;
        }

        return $user->clientMemberships()
            ->active()
            ->notSubUser()
            ->exists();
    }
}
