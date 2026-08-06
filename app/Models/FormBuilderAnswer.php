<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormBuilderAnswer extends Model
{
    protected $table = 'form_builder_answers';

    public $timestamps = false;

    /**
     * Live Kohana schema: PK is `form_builder_answer_id` (AUTO_INCREMENT).
     * Some Laravel-shaped DBs use `answer_id` instead.
     */
    public function getKeyName(): string
    {
        return once(function (): string {
            if (Schema::hasColumn($this->getTable(), 'form_builder_answer_id')) {
                return 'form_builder_answer_id';
            }

            if (Schema::hasColumn($this->getTable(), 'answer_id')) {
                return 'answer_id';
            }

            return 'form_builder_answer_id';
        });
    }

    public function getIncrementing(): bool
    {
        return once(function (): bool {
            try {
                $column = DB::selectOne(
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
            'question_id',
            'profile_id',
            'question_answer',
            'row_number',
            'signature_name_text',
            'signature_employer_text',
            'signature_email_text',
            'signature_phone_text',
            'participant_signature_image',
            'participant_employer_text',
            'session_id',
            'date_time',
            'app_user_firstname',
            'app_user_lastname',
            'app_user_email',
            'app_user_mobile',
        ]));
    }

    protected function casts(): array
    {
        return [
            'date_time' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(FormBuilderQuestion::class, 'question_id', 'question_id');
    }
}
