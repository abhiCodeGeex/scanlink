<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBuilderAnswer extends Model
{
    protected $table = 'form_builder_answers';

    protected $primaryKey = 'answer_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'answer_id', 'question_id', 'profile_id', 'question_answer', 'row_number',
        'signature_name_text', 'signature_employer_text', 'signature_email_text',
        'signature_phone_text', 'participant_signature_image', 'participant_employer_text',
        'session_id', 'date_time', 'app_user_firstname', 'app_user_lastname',
        'app_user_email', 'app_user_mobile',
    ];

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
