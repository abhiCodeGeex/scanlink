<?php

namespace App\Enums;

enum ClientUserRole: int
{
    case SubUser = 1;
    case Primary = 5;

    public function label(): string
    {
        return match ($this) {
            self::Primary => 'Primary account',
            self::SubUser => 'Sub-user',
        };
    }
}
