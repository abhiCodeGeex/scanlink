<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Live schema often uses ENUM('0','1'). MySQL treats bare integers as 1-based
 * enum indexes (1 => first value '0'), so boolean/int 1 would store the wrong value.
 * Always persist the string members '0' / '1'.
 *
 * @implements CastsAttributes<bool, string>
 */
class MysqlEnumBoolean implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        return (string) $value === '1' || $value === 1 || $value === true;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
    }
}
