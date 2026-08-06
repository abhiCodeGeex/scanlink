<?php

namespace App\Support;

use App\Models\FormBuilderQuestionOption;
use App\Models\FormBuilderQuestionType;

/**
 * Compatibility helpers for the ported Kohana formbuilder view.
 */
class LegacyFormBuilderView
{
    /**
     * @return list<array{type: int|string, question_type: string}>
     */
    public static function get_question_types_by_question_type_id(int|string $questionTypeId): array
    {
        $type = FormBuilderQuestionType::query()->find((int) $questionTypeId);

        if (! $type) {
            return [['type' => 2, 'question_type' => 'Question']];
        }

        $group = $type->paletteGroup();
        $legacyType = match ($group) {
            'question' => 0,
            'format' => 1,
            default => 2,
        };

        return [[
            'type' => $legacyType,
            'question_type' => (string) ($type->question_type ?: $type->label ?: 'Type '.$questionTypeId),
        ]];
    }

    /**
     * @return list<array{option_name: string}>
     */
    public static function get_question_option(int|string $questionId, int|string $optionTypeId): array
    {
        return FormBuilderQuestionOption::query()
            ->where('question_id', (int) $questionId)
            ->where('question_option_type_id', (int) $optionTypeId)
            ->get()
            ->map(fn ($o) => ['option_name' => (string) $o->option_name])
            ->values()
            ->all();
    }
}

// Alias used by the ported view.
if (! class_exists('Model_formbuilder', false)) {
    class_alias(\App\Support\LegacyFormBuilderView::class, 'Model_formbuilder');
}
