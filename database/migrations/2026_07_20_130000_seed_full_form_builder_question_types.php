<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seed the full live Form Builder palette (types 1–25).
 * Docker was only getting the stub 8-type seed, so Covid / Location Function / etc. were missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('form_builder_question_types')) {
            return;
        }

        $hasQuestionType = Schema::hasColumn('form_builder_question_types', 'question_type');
        $hasLabel = Schema::hasColumn('form_builder_question_types', 'label');

        // Labels match live Kohana formbuilder menus (HasLegacyFormBuilderSidebar fallback).
        // `group` is legacy DB `type`: 0=question, 1=format, 2=answer (paletteGroup() also hard-codes ids).
        $types = [
            1 => ['label' => 'Text Field', 'group' => 2],
            2 => ['label' => 'Text', 'group' => 0],
            3 => ['label' => 'Multiple Choices', 'group' => 2],
            4 => ['label' => 'Check Box', 'group' => 2],
            5 => ['label' => 'Drop Down Menu', 'group' => 2],
            6 => ['label' => 'Number Scale', 'group' => 2],
            7 => ['label' => 'Grid', 'group' => 2],
            8 => ['label' => 'Date', 'group' => 2],
            9 => ['label' => 'Time', 'group' => 2],
            11 => ['label' => 'Image', 'group' => 1],
            13 => ['label' => 'Line Divider', 'group' => 1],
            14 => ['label' => 'Blank Space', 'group' => 1],
            15 => ['label' => 'Comments', 'group' => 2],
            16 => ['label' => 'Signature Panel', 'group' => 1],
            17 => ['label' => 'Upload Button', 'group' => 1],
            18 => ['label' => 'Participant Name', 'group' => 1],
            19 => ['label' => 'Location Function', 'group' => 1],
            20 => ['label' => 'Web Link Button', 'group' => 1],
            21 => ['label' => 'Document Button', 'group' => 1],
            22 => ['label' => 'SWMS Hazard/Risk', 'group' => 1],
            23 => ['label' => 'Document Menu', 'group' => 2],
            24 => ['label' => 'Add recipient', 'group' => 1],
            25 => ['label' => 'Covid check-in', 'group' => 0],
        ];

        foreach ($types as $id => $row) {
            $payload = [
                'type' => (string) $row['group'],
                'is_active' => true,
            ];

            if ($hasLabel) {
                $payload['label'] = $row['label'];
            }

            if ($hasQuestionType) {
                $payload['question_type'] = $row['label'];
            }

            DB::table('form_builder_question_types')->updateOrInsert(
                ['question_type_id' => $id],
                $payload,
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('form_builder_question_types')) {
            return;
        }

        DB::table('form_builder_question_types')
            ->whereIn('question_type_id', [11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25])
            ->delete();
    }
};
