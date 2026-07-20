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

    /**
     * Palette group matching live ScanLink form builder menus.
     *
     * Live hard-codes tool placement (not always the same as DB `type`):
     * Question (green), Format (orange), Answer (blue).
     */
    public function paletteGroup(): string
    {
        $id = (int) $this->question_type_id;

        if (in_array($id, [2, 25], true)) {
            return 'question';
        }

        if (in_array($id, [13, 14, 22, 24, 11, 16, 17, 18, 19, 20, 21], true)) {
            return 'format';
        }

        if (in_array($id, [1, 3, 4, 5, 6, 7, 8, 9, 15, 23], true)) {
            return 'answer';
        }

        return match ((int) $this->type) {
            1 => 'format',
            2 => 'answer',
            default => 'question',
        };
    }

    /**
     * Sort order within a palette column (live formbuilder menu order).
     */
    public function paletteSortOrder(): int
    {
        $orders = [
            'question' => [2, 25],
            'format' => [13, 14, 22, 24, 11, 16, 17, 18, 19, 20, 21],
            'answer' => [1, 3, 4, 5, 6, 7, 8, 9, 15, 23],
        ];

        $group = $this->paletteGroup();
        $list = $orders[$group] ?? [];
        $index = array_search((int) $this->question_type_id, $list, true);

        return $index === false ? 999 : $index;
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
