<?php

namespace App\Models\Concerns;

/**
 * Live MySQL dump has many NOT NULL columns without DB defaults.
 * Fill safe empty defaults on create so Filament forms don't 500.
 */
trait FillsLegacyNotNullDefaults
{
    /**
     * @return array<string, mixed>
     */
    abstract protected static function legacyNotNullDefaults(): array;

    protected static function bootFillsLegacyNotNullDefaults(): void
    {
        static::creating(function (self $model): void {
            foreach (static::legacyNotNullDefaults() as $column => $default) {
                $current = $model->getAttribute($column);

                if ($current === null) {
                    $model->setAttribute($column, $default);
                }
            }
        });
    }
}
