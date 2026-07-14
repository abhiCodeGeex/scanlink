<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormBuilderQuestion extends Model
{
    protected $table = 'form_builder_question';

    protected $primaryKey = 'question_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'question_id', 'profile_id', 'form_id', 'question_type_id', 'question_text',
        'image_title', 'image_align', 'question_order', 'button_link_url', 'button_colour',
        'doc_title', 'include_name', 'include_employer', 'include_email', 'include_phone',
        'participant_include_signature', 'participant_include_employer', 'is_mandatory',
        'is_logchecked', 'log_columntitle',
    ];

    protected function casts(): array
    {
        return [
            'include_name' => 'boolean',
            'include_employer' => 'boolean',
            'include_email' => 'boolean',
            'include_phone' => 'boolean',
            'participant_include_signature' => 'boolean',
            'participant_include_employer' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_logchecked' => 'boolean',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function questionType(): BelongsTo
    {
        return $this->belongsTo(FormBuilderQuestionType::class, 'question_type_id', 'question_type_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FormBuilderQuestionOption::class, 'question_id', 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormBuilderAnswer::class, 'question_id', 'question_id');
    }
}
