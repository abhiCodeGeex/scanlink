<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class FormBuilderQuestion extends Model
{
    protected $table = 'form_builder_question';

    protected $primaryKey = 'question_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'question_id', 'profile_id', 'form_id', 'question_type_id', 'question_text',
        'image_title', 'image_url', 'image_align', 'question_order',
        'button_link_url', 'button_colour', 'doc_title', 'default_value',
        'include_name', 'include_employer', 'include_email', 'include_phone',
        'participant_include_signature', 'participant_include_employer',
        'is_mandatory', 'is_deleted', 'is_logchecked', 'log_columntitle',
        'covid_bg_color', 'covid_text_color', 'visitor_name', 'visitor_phone',
        'date', 'time', 'venue_name', 'venue_address', 'location_description',
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
            'is_deleted' => 'boolean',
            'is_logchecked' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('not_deleted', function (Builder $query): void {
            if (\Illuminate\Support\Facades\Schema::hasColumn('form_builder_question', 'is_deleted')) {
                $query->where(function (Builder $q): void {
                    $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                });
            }
        });
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
        return $this->hasMany(FormBuilderQuestionOption::class, 'question_id', 'question_id')
            ->orderBy('question_option_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormBuilderAnswer::class, 'question_id', 'question_id');
    }

    public function typeName(): string
    {
        return $this->questionType?->label() ?: 'Question';
    }

    public function boxClass(): string
    {
        return match ($this->questionType?->paletteGroup()) {
            'format' => 'fb-box-format',
            'answer' => 'fb-box-answer',
            default => 'fb-box-question',
        };
    }
}
