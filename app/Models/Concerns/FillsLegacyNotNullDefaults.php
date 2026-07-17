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
        $applyDefaults = function (self $model, bool $onlyNull): void {
            foreach (static::legacyNotNullDefaults() as $column => $default) {
                if (! array_key_exists($column, $model->getAttributes())) {
                    if (! $onlyNull) {
                        $model->setAttribute($column, $default);
                    }

                    continue;
                }

                if ($model->getAttributes()[$column] === null) {
                    $model->setAttribute($column, $default);
                }
            }
        };

        static::creating(function (self $model) use ($applyDefaults): void {
            $applyDefaults($model, onlyNull: false);
        });

        static::saving(function (self $model) use ($applyDefaults): void {
            $applyDefaults($model, onlyNull: true);
        });
    }
}
