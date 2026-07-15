<?php

namespace App\Services;

use App\Models\FormBuilderLibrary;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    public function saveCurrentFormToLibrary(Profile $profile, ?string $title = null): FormBuilderLibrary
    {
        $formBuilder = app(FormBuilderService::class);
        $formId = $formBuilder->ensureFormId($profile);

        $entry = FormBuilderLibrary::query()->where('form_id', $formId)->first();

        $payload = [
            'user_id' => $profile->user_id ?: 0,
            'form_id' => $formId,
            'form_title' => $title ?: $profile->form_title ?: ($profile->name ?: 'Form '.$formId),
            'is_deleted' => false,
            'is_deleted_from_library' => false,
        ];

        if ($entry) {
            $entry->update($payload);

            return $entry->fresh();
        }

        $nextId = (int) FormBuilderLibrary::query()->max('form_builder_library_id') + 1;

        return FormBuilderLibrary::query()->create(array_merge($payload, [
            'form_builder_library_id' => max(1, $nextId),
        ]));
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

        $usesAutoQuestionId = $this->questionUsesAutoIncrement();
        $nextQuestionId = $usesAutoQuestionId
            ? 0
            : (int) FormBuilderQuestion::query()->withoutGlobalScopes()->max('question_id');

        $nextOptionId = $this->usesAutoOptionId()
            ? 0
            : (int) FormBuilderQuestionOption::query()->max($this->optionPrimaryKey());

        $nextOrder = (int) FormBuilderQuestion::query()
            ->where('profile_id', $targetProfile->id)
            ->max('question_order');

        $cloned = 0;

        foreach ($sourceQuestions as $sourceQuestion) {
            if (! $usesAutoQuestionId) {
                $nextQuestionId++;
            }

            $nextOrder++;

            $questionPayload = [
                'profile_id' => $targetProfile->id,
                'form_id' => $targetFormId,
                'question_type_id' => $sourceQuestion->question_type_id,
                'question_text' => $sourceQuestion->question_text,
                'image_title' => $sourceQuestion->image_title ?? '',
                'image_url' => $sourceQuestion->image_url ?? null,
                'image_align' => $sourceQuestion->image_align ?? '0',
                'question_order' => $nextOrder,
                'button_link_url' => $sourceQuestion->button_link_url ?? '',
                'button_colour' => $sourceQuestion->button_colour ?? '007A01',
                'doc_title' => $sourceQuestion->doc_title ?? '',
                'default_value' => $sourceQuestion->default_value ?? '',
                'include_name' => $sourceQuestion->include_name,
                'include_employer' => $sourceQuestion->include_employer,
                'include_email' => $sourceQuestion->include_email,
                'include_phone' => $sourceQuestion->include_phone,
                'participant_include_signature' => $sourceQuestion->participant_include_signature,
                'participant_include_employer' => $sourceQuestion->participant_include_employer,
                'is_mandatory' => $sourceQuestion->is_mandatory,
                'is_logchecked' => $sourceQuestion->is_logchecked,
                'log_columntitle' => $sourceQuestion->log_columntitle ?? '',
                'is_deleted' => false,
                'covid_bg_color' => $sourceQuestion->covid_bg_color ?? '',
                'covid_text_color' => $sourceQuestion->covid_text_color ?? '',
                'visitor_name' => $sourceQuestion->visitor_name ?? '',
                'visitor_phone' => $sourceQuestion->visitor_phone ?? '',
                'date' => $sourceQuestion->date ?? '',
                'time' => $sourceQuestion->time ?? '',
                'venue_name' => $sourceQuestion->venue_name ?? '',
                'venue_address' => $sourceQuestion->venue_address ?? '',
                'location_description' => $sourceQuestion->location_description ?? '',
            ];

            if (! $usesAutoQuestionId) {
                $questionPayload['question_id'] = $nextQuestionId;
            }

            $newQuestion = FormBuilderQuestion::query()->create($questionPayload);
            $newQuestionId = (int) $newQuestion->question_id;

            foreach ($sourceQuestion->options as $option) {
                if (! $this->usesAutoOptionId()) {
                    $nextOptionId++;
                }

                $optionPayload = [
                    'question_id' => $newQuestionId,
                    'option_name' => $option->option_name,
                    'question_option_type_id' => $option->question_option_type_id,
                ];

                if (! $this->usesAutoOptionId()) {
                    $optionPayload[$this->optionPrimaryKey()] = max(1, $nextOptionId);
                }

                FormBuilderQuestionOption::query()->create($optionPayload);
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

    protected function optionPrimaryKey(): string
    {
        return Schema::hasColumn('form_builder_question_options', 'question_option_id')
            ? 'question_option_id'
            : 'option_id';
    }

    protected function usesAutoOptionId(): bool
    {
        if (! Schema::hasTable('form_builder_question_options')) {
            return true;
        }

        $key = $this->optionPrimaryKey();

        try {
            $column = DB::selectOne(
                'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['form_builder_question_options', $key]
            );

            return $column && str_contains((string) ($column->EXTRA ?? ''), 'auto_increment');
        } catch (\Throwable) {
            return $key === 'question_option_id';
        }
    }

    protected function questionUsesAutoIncrement(): bool
    {
        if (! Schema::hasTable('form_builder_question')) {
            return true;
        }

        try {
            $column = DB::selectOne(
                'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['form_builder_question', 'question_id']
            );

            return $column && str_contains((string) ($column->EXTRA ?? ''), 'auto_increment');
        } catch (\Throwable) {
            return false;
        }
    }
}
