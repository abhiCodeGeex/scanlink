<?php

namespace App\Enums;

enum UserType: string
{
    case Admin = 'admin';
    case Portal = 'portal';
    case Voc = 'voc';
    case Registrant = 'registrant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Portal => 'Portal',
            self::Voc => 'VOC',
            self::Registrant => 'Registrant',
        };
    }

    public function canAccessAdminPanel(): bool
    {
        return $this === self::Admin;
    }
}
