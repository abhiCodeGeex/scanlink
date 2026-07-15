<?php

namespace App\Services;

use App\Models\FormBuilderLibrary;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FormLibraryService
{
    /**
     * @return Collection<int, FormBuilderLibrary>
     */
    public function libraryFormsForClient(int $clientId): Collection
    {
        $userIds = DB::table('client_users')
            ->where('client_id', $clientId)
            ->pluck('id');

        if ($userIds->isEmpty()) {
            return collect();
        }

        return FormBuilderLibrary::query()
            ->whereIn('user_id', $userIds)
            ->where('is_deleted_from_library', false)
            ->orderBy('form_title')
            ->get();
    }

    public function applyLibraryFormToProfile(int $libraryFormId, Profile $targetProfile): int
    {
        $libraryEntry = FormBuilderLibrary::query()
            ->where('form_id', $libraryFormId)
            ->where('is_deleted_from_library', false)
            ->firstOrFail();

        $sourceQuestions = FormBuilderQuestion::query()
            ->with('options')
            ->where('form_id', $libraryFormId)
            ->orderBy('question_order')
            ->get();

        if ($sourceQuestions->isEmpty()) {
            return 0;
        }

        $targetFormId = (int) ($targetProfile->form_id ?: 0);

        if ($targetFormId === 0) {
            $targetFormId = (int) FormBuilderQuestion::query()->max('form_id') + 1;
            $targetFormId = $targetFormId ?: 1;

            $targetProfile->update([
                'form_id' => $targetFormId,
                'form_active' => true,
                'form_is_enable' => true,
            ]);
        }

        $nextQuestionId = (int) FormBuilderQuestion::query()->max('question_id');
        $nextOptionId = (int) FormBuilderQuestionOption::query()->max('option_id');
        $nextOrder = (int) FormBuilderQuestion::query()
            ->where('profile_id', $targetProfile->id)
            ->max('question_order');

        $cloned = 0;

        foreach ($sourceQuestions as $sourceQuestion) {
            $nextQuestionId++;
            $nextOrder++;

            FormBuilderQuestion::query()->create([
                'question_id' => $nextQuestionId,
                'profile_id' => $targetProfile->id,
                'form_id' => $targetFormId,
                'question_type_id' => $sourceQuestion->question_type_id,
                'question_text' => $sourceQuestion->question_text,
                'image_title' => $sourceQuestion->image_title,
                'image_align' => $sourceQuestion->image_align,
                'question_order' => $nextOrder,
                'button_link_url' => $sourceQuestion->button_link_url,
                'button_colour' => $sourceQuestion->button_colour,
                'doc_title' => $sourceQuestion->doc_title,
                'include_name' => $sourceQuestion->include_name,
                'include_employer' => $sourceQuestion->include_employer,
                'include_email' => $sourceQuestion->include_email,
                'include_phone' => $sourceQuestion->include_phone,
                'participant_include_signature' => $sourceQuestion->participant_include_signature,
                'participant_include_employer' => $sourceQuestion->participant_include_employer,
                'is_mandatory' => $sourceQuestion->is_mandatory,
                'is_logchecked' => $sourceQuestion->is_logchecked,
                'log_columntitle' => $sourceQuestion->log_columntitle,
            ]);

            foreach ($sourceQuestion->options as $option) {
                $nextOptionId++;
                FormBuilderQuestionOption::query()->create([
                    'option_id' => $nextOptionId ?: 1,
                    'question_id' => $nextQuestionId,
                    'option_name' => $option->option_name,
                    'question_option_type_id' => $option->question_option_type_id,
                ]);
            }

            $cloned++;
        }

        $targetProfile->update([
            'form_active' => true,
            'form_is_enable' => true,
        ]);

        return $cloned;
    }

    public function deleteFromLibrary(FormBuilderLibrary $entry): void
    {
        if ($entry->is_deleted) {
            FormBuilderQuestionOption::query()
                ->whereIn('question_id', FormBuilderQuestion::query()
                    ->where('form_id', $entry->form_id)
                    ->pluck('question_id'))
                ->delete();

            FormBuilderQuestion::query()
                ->where('form_id', $entry->form_id)
                ->delete();

            $entry->delete();

            return;
        }

        $entry->update(['is_deleted_from_library' => true]);
    }
}
