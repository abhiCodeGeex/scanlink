<?php

namespace App\Enums;

enum CodeOrderStatus: string
{
    case New = '0';
    case Renew = '1';
    case InvoiceSend = '2';
    case Paid = '3';
    case Cancelled = '4';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Renew => 'Renew',
            self::InvoiceSend => 'Invoice Send',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Status options shown in the list filter (legacy: All, New, Renew, Invoice Send, Paid).
     *
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            self::New->value => self::New->label(),
            self::Renew->value => self::Renew->label(),
            self::InvoiceSend->value => self::InvoiceSend->label(),
            self::Paid->value => self::Paid->label(),
        ];
    }

    /**
     * Status options selectable when changing an order (legacy view dropdown: New, Invoice Send, Paid).
     *
     * @return array<string, string>
     */
    public static function changeOptions(): array
    {
        return [
            self::New->value => self::New->label(),
            self::InvoiceSend->value => self::InvoiceSend->label(),
            self::Paid->value => self::Paid->label(),
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Renew => 'warning',
            self::InvoiceSend => 'primary',
            self::Paid => 'success',
            self::Cancelled => 'danger',
        };
    }
}
