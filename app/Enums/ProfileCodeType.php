<?php

namespace App\Enums;

enum ProfileCodeType: string
{
    case QrCode = '0';
    case DataMatrix = '1';

    public function label(): string
    {
        return match ($this) {
            self::QrCode => 'QR Code',
            self::DataMatrix => 'Data Matrix',
        };
    }
}
