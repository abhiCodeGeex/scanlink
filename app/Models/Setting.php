<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** Live `settings` table has updated_at only. */
    public const CREATED_AT = null;

    protected $fillable = ['title', 'values'];

    public static function valueFor(string $title): ?string
    {
        return Cache::remember(
            self::cacheKey($title),
            now()->addHour(),
            fn (): ?string => static::query()->where('title', $title)->value('values'),
        );
    }

    public static function setValue(string $title, ?string $values): void
    {
        static::query()->updateOrCreate(
            ['title' => $title],
            ['values' => $values],
        );

        Cache::forget(self::cacheKey($title));
    }

    private static function cacheKey(string $title): string
    {
        return 'settings.value.'.$title;
    }
}
