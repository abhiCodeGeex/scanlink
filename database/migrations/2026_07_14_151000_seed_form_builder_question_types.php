<?php

use App\Models\FormBuilderQuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('form_builder_question_types')) {
            return;
        }

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
            DB::table('form_builder_question_types')->updateOrInsert(
                ['question_type_id' => $id],
                [
                    'type' => $row['type'],
                    'label' => $row['label'],
                    'is_active' => true,
                ],
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
