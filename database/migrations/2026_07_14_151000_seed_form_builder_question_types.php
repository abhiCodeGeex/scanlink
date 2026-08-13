<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Legacy imports may carry a form_builder_question_types table whose columns
        // differ from the app schema (e.g. `question_type` instead of `label`, no
        // `type`/`is_active`). Only write columns that actually exist, and bail if the
        // primary key column is absent — the later full-palette seed supersedes these
        // rows anyway.
        if (! Schema::hasTable('form_builder_question_types')
            || ! Schema::hasColumn('form_builder_question_types', 'question_type_id')) {
            return;
        }

        $hasType = Schema::hasColumn('form_builder_question_types', 'type');
        $hasLabel = Schema::hasColumn('form_builder_question_types', 'label');
        $hasQuestionType = Schema::hasColumn('form_builder_question_types', 'question_type');
        $hasIsActive = Schema::hasColumn('form_builder_question_types', 'is_active');

        $types = [
            1 => ['type' => 'text', 'label' => 'Text'],
            2 => ['type' => 'textarea', 'label' => 'Textarea'],
            3 => ['type' => 'radio', 'label' => 'Radio'],
            4 => ['type' => 'checkbox', 'label' => 'Checkbox'],
            5 => ['type' => 'select', 'label' => 'Select'],
            6 => ['type' => 'date', 'label' => 'Date'],
            7 => ['type' => 'signature', 'label' => 'Signature'],
            8 => ['type' => 'file', 'label' => 'File'],
        ];

        foreach ($types as $id => $row) {
            $payload = [];

            if ($hasType) {
                $payload['type'] = match ($id) {
                    2 => 1,
                    3, 4, 5 => 2,
                    default => 0,
                };
            }

            if ($hasLabel) {
                $payload['label'] = $row['label'];
            }

            if ($hasQuestionType) {
                $payload['question_type'] = $row['label'];
            }

            if ($hasIsActive) {
                $payload['is_active'] = true;
            }

            if ($payload === []) {
                continue;
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

        DB::table('form_builder_question_types')->whereIn('question_type_id', range(1, 8))->delete();
    }
};
