<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Live MySQL dump uses enum('0','1') / int for several profile flags.
 * Filament toggles use bool; this cast stores '0'/'1' (or 0/1 for int columns).
 */
class LegacyZeroOne implements CastsAttributes
{
    public function __construct(private readonly bool $asInteger = false) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        return (string) $value === '1' || $value === 1 || $value === true;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        $on = filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === 1 || $value === '1';

        // ALWAYS store the string '1'/'0', never an integer. Several live columns are
        // enum('0','1'), and MySQL treats an integer written to an enum as a member INDEX:
        // int 1 selects the first member ('0') and int 0 is an invalid index — so ticking a
        // checkbox saved "off" and could never be re-enabled (e.g. the Words heading
        // checkboxes). Strings match enum members by value, and MySQL/SQLite coerce '1'/'0'
        // for genuine int/tinyint columns, so this is safe for every flag.
        return $on ? '1' : '0';
    }
}
