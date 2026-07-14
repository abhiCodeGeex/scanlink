<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormBuilderQuestionType extends Model
{
    protected $table = 'form_builder_question_types';

    protected $primaryKey = 'question_type_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['question_type_id', 'type', 'label', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(FormBuilderQuestion::class, 'question_type_id', 'question_type_id');
    }
}
