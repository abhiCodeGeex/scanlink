<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['title', 'values'];

    public static function valueFor(string $title): ?string
    {
        return static::query()->where('title', $title)->value('values');
    }

    public static function setValue(string $title, ?string $values): void
    {
        static::query()->updateOrCreate(
            ['title' => $title],
            ['values' => $values],
        );
    }
}
