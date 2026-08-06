<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class FormBuilderRecipient extends Model
{
    protected $table = 'form_builder_recipient';

    public $timestamps = false;

    /**
     * Live Kohana schema: PK column is `form_builder_recipient` (AUTO_INCREMENT).
     * Some Laravel-shaped DBs use `recipient_id` instead.
     */
    public function getKeyName(): string
    {
        return once(function (): string {
            if (Schema::hasColumn($this->getTable(), 'form_builder_recipient')) {
                return 'form_builder_recipient';
            }

            if (Schema::hasColumn($this->getTable(), 'recipient_id')) {
                return 'recipient_id';
            }

            return 'form_builder_recipient';
        });
    }

    public function getIncrementing(): bool
    {
        return once(function (): bool {
            try {
                $column = \Illuminate\Support\Facades\DB::selectOne(
                    'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$this->getTable(), $this->getKeyName()]
                );

                return $column && str_contains((string) ($column->EXTRA ?? ''), 'auto_increment');
            } catch (\Throwable) {
                return true;
            }
        });
    }

    protected $keyType = 'int';

    public function getFillable(): array
    {
        return array_values(array_filter([
            $this->getKeyName(),
            'form_id',
            'recipient_email',
        ]));
    }

    public function formProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'form_id', 'form_id');
    }
}
