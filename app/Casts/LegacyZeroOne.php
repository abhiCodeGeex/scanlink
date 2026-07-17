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

    public function set(Model $model, string $key, mixed $value, array $attributes): string|int
    {
        $on = filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === 1 || $value === '1';

        if ($this->asInteger) {
            return $on ? 1 : 0;
        }

        return $on ? '1' : '0';
    }
}
