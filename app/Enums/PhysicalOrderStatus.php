<?php

namespace App\Enums;

enum PhysicalOrderStatus: string
{
    case New = 'New';
    case Paid = 'Paid';
    case Shipped = 'Shipped';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    /**
     * Status options on the view page (legacy dropdown).
     *
     * @return array<string, string>
     */
    public static function changeOptions(): array
    {
        return self::filterOptions();
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Paid => 'primary',
            self::Shipped => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}
