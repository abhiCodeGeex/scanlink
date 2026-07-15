<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormBuilderQuestionType extends Model
{
    protected $table = 'form_builder_question_types';

    protected $primaryKey = 'question_type_id';

    public $timestamps = false;

    protected $fillable = ['question_type_id', 'question_type', 'type', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'type' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(FormBuilderQuestion::class, 'question_type_id', 'question_type_id');
    }

    /** Palette group: 0 = Question (green), 1 = Format (orange), 2 = Answer (blue). */
    public function paletteGroup(): string
    {
        return match ((int) $this->type) {
            1 => 'format',
            2 => 'answer',
            default => 'question',
        };
    }

    public function paletteColor(): string
    {
        return match ($this->paletteGroup()) {
            'format' => '#ff6600',
            'answer' => '#0066ff',
            default => '#008000',
        };
    }

    public function label(): string
    {
        if (filled($this->question_type)) {
            return (string) $this->question_type;
        }

        if (filled($this->getAttribute('label'))) {
            return (string) $this->getAttribute('label');
        }

        return 'Type '.$this->question_type_id;
    }

    public function needsOptions(): bool
    {
        return in_array((int) $this->question_type_id, [3, 4, 5], true);
    }

    public function needsScale(): bool
    {
        return (int) $this->question_type_id === 6;
    }

    public function needsGrid(): bool
    {
        return (int) $this->question_type_id === 7;
    }

    public function isDisplayOnly(): bool
    {
        return in_array((int) $this->question_type_id, [2, 10, 12, 13, 14], true);
    }
}
