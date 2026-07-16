<?php

namespace App\Enums;

enum AdminRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Support = 'support';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super admin',
            self::Admin => 'Admin',
            self::Support => 'Support',
        };
    }

    public function canAccessPanel(): bool
    {
        return true;
    }

    public function canManageAdmins(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function canWrite(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
    }

    public function canManageSettings(): bool
    {
        return $this === self::SuperAdmin;
    }
}
