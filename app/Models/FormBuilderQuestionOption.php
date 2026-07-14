<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBuilderQuestionOption extends Model
{
    protected $table = 'form_builder_question_options';

    protected $primaryKey = 'option_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['option_id', 'question_id', 'option_name', 'question_option_type_id'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(FormBuilderQuestion::class, 'question_id', 'question_id');
    }
}
