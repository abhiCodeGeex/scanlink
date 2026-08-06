<?php

namespace App\Services;

use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderLibrary;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\FormBuilderQuestionType;
use App\Models\FormBuilderRecipient;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormBuilderService
{
    public function ensureFormId(Profile $profile): int
    {
        $formId = (int) ($profile->form_id ?: 0);

        if ($formId > 0) {
            return $formId;
        }

        $formId = (int) FormBuilderQuestion::query()->withoutGlobalScopes()->max('form_id') + 1;
        $formId = max(1, $formId);

        $profile->update(['form_id' => $formId]);

        return $formId;
    }

    /**
     * @return Collection<int, FormBuilderQuestionType>
     */
    public function activeTypes(): Collection
    {
        return FormBuilderQuestionType::query()
            ->where('is_active', 1)
            ->orderBy('question_type_id')
            ->get();
    }

    /**
     * @return array{question: Collection, format: Collection, answer: Collection}
     */
    public function paletteGroups(): array
    {
        $types = $this->activeTypes();

        $group = function (string $name) use ($types): Collection {
            return $types
                ->filter(fn (FormBuilderQuestionType $t): bool => $t->paletteGroup() === $name)
                ->sortBy(fn (FormBuilderQuestionType $t): int => $t->paletteSortOrder())
                ->values();
        };

        return [
            'question' => $group('question'),
            'format' => $group('format'),
            'answer' => $group('answer'),
        ];
    }

    /**
     * @return Collection<int, FormBuilderQuestion>
     */
    public function questionsForProfile(int $profileId): Collection
    {
        return FormBuilderQuestion::query()
            ->with(['questionType', 'options'])
            ->where('profile_id', $profileId)
            ->orderBy('question_order')
            ->orderBy('question_id')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{option_name?: string, question_option_type_id?: int}>  $options
     */
    public function saveQuestion(Profile $profile, array $data, array $options = [], ?int $questionId = null): FormBuilderQuestion
    {
        return DB::transaction(function () use ($profile, $data, $options, $questionId): FormBuilderQuestion {
            $formId = $this->ensureFormId($profile);

            $payload = [
                'profile_id' => $profile->id,
                'form_id' => $formId,
                'question_type_id' => (int) $data['question_type_id'],
                'question_text' => $data['question_text'] ?? '',
                'image_title' => $data['image_title'] ?? '',
                'image_url' => $data['image_url'] ?? null,
                'image_align' => \App\Support\FormBuilderMedia::alignValue($data['image_align'] ?? 'left'),
                'button_link_url' => $data['button_link_url'] ?? '',
                'button_colour' => $data['button_colour'] ?? '007A01',
                'doc_title' => $data['doc_title'] ?? '',
                'default_value' => $data['default_value'] ?? '',
                'include_name' => (bool) ($data['include_name'] ?? true),
                'include_employer' => (bool) ($data['include_employer'] ?? false),
                'include_email' => (bool) ($data['include_email'] ?? false),
                'include_phone' => (bool) ($data['include_phone'] ?? false),
                'participant_include_signature' => (bool) ($data['participant_include_signature'] ?? false),
                'participant_include_employer' => (bool) ($data['participant_include_employer'] ?? false),
                'is_mandatory' => (bool) ($data['is_mandatory'] ?? true),
                'is_logchecked' => (bool) ($data['is_logchecked'] ?? false),
                'log_columntitle' => $data['log_columntitle'] ?? '',
                'is_deleted' => false,
                // Live schema columns are NOT NULL without defaults.
                'covid_bg_color' => $data['covid_bg_color'] ?? '',
                'covid_text_color' => $data['covid_text_color'] ?? '',
                'visitor_name' => $data['visitor_name'] ?? '',
                'visitor_phone' => $data['visitor_phone'] ?? '',
                'date' => $data['date'] ?? '',
                'time' => $data['time'] ?? '',
                'venue_name' => $data['venue_name'] ?? '',
                'venue_address' => $data['venue_address'] ?? '',
                'location_description' => $data['location_description'] ?? '',
            ];

            $payload = array_filter(
                $payload,
                fn (mixed $value, string $key): bool => Schema::hasColumn('form_builder_question', $key),
                ARRAY_FILTER_USE_BOTH
            );

            if ($questionId) {
                $question = FormBuilderQuestion::query()->where('profile_id', $profile->id)->findOrFail($questionId);
                $question->update($payload);
            } else {
                $nextOrder = (int) FormBuilderQuestion::query()
                    ->where('profile_id', $profile->id)
                    ->max('question_order') + 1;

                $payload['question_order'] = $nextOrder;

                if (! $this->questionUsesAutoIncrement()) {
                    $payload['question_id'] = (int) FormBuilderQuestion::query()
                        ->withoutGlobalScopes()
                        ->max('question_id') + 1;
                    $payload['question_id'] = max(1, $payload['question_id']);
                }

                $question = FormBuilderQuestion::query()->create($payload);

                // Safety net if PK still missing (mixed schema / model config).
                if (! $question->getKey()) {
                    $question->setAttribute(
                        $question->getKeyName(),
                        (int) DB::getPdo()->lastInsertId()
                    );
                    $question->exists = true;
                }
            }

            $this->syncOptions($question, $options);
            $this->ensureLibraryEntry($profile, $formId);

            return $question->fresh(['questionType', 'options']) ?? $question->load(['questionType', 'options']);
        });
    }

    /**
     * @param  array<int, array{option_name?: string, question_option_type_id?: int}>  $options
     */
    public function syncOptions(FormBuilderQuestion $question, array $options): void
    {
        FormBuilderQuestionOption::query()->where('question_id', $question->question_id)->delete();

        $nextOptionId = $this->optionUsesAutoIncrement()
            ? 0
            : (int) FormBuilderQuestionOption::query()->max($this->optionPrimaryKey());

        foreach ($options as $option) {
            $name = trim((string) ($option['option_name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $optionPayload = [
                'question_id' => $question->question_id,
                'option_name' => $name,
                'question_option_type_id' => (int) ($option['question_option_type_id'] ?? 0),
            ];

            if (! $this->optionUsesAutoIncrement()) {
                $nextOptionId++;
                $optionPayload[$this->optionPrimaryKey()] = max(1, $nextOptionId);
            }

            FormBuilderQuestionOption::query()->create($optionPayload);
        }
    }

    public function reorder(Profile $profile, array $orderedQuestionIds): void
    {
        foreach (array_values($orderedQuestionIds) as $index => $questionId) {
            FormBuilderQuestion::query()
                ->where('profile_id', $profile->id)
                ->where('question_id', (int) $questionId)
                ->update(['question_order' => $index + 1]);
        }
    }

    public function deleteQuestion(Profile $profile, int $questionId): void
    {
        $question = FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->findOrFail($questionId);

        if (Schema::hasColumn('form_builder_question', 'is_deleted')) {
            $question->update(['is_deleted' => true]);
        } else {
            FormBuilderQuestionOption::query()->where('question_id', $questionId)->delete();
            $question->delete();
        }
    }

    /**
     * Wipe Form Builder canvas for an "Add a New Code" draft slot.
     * Questions are keyed by profile_id; leftover rows from a reused paid slot
     * otherwise reappear in the legacy iframe on /create.
     */
    public function clearFormForCreate(Profile $profile, ?int $previousFormId = null): void
    {
        $profileId = (int) $profile->id;
        $formId = $previousFormId ?? (int) ($profile->form_id ?: 0);

        DB::transaction(function () use ($profile, $profileId, $formId): void {
            $questionIds = FormBuilderQuestion::query()
                ->withoutGlobalScopes()
                ->where('profile_id', $profileId)
                ->pluck('question_id');

            if ($questionIds->isNotEmpty() && Schema::hasTable('form_builder_question_options')) {
                FormBuilderQuestionOption::query()
                    ->whereIn('question_id', $questionIds)
                    ->delete();
            }

            FormBuilderQuestion::query()
                ->withoutGlobalScopes()
                ->where('profile_id', $profileId)
                ->delete();

            // Recipients are per form_id — only remove when no other profile still uses it.
            if ($formId > 0 && Schema::hasTable('form_builder_recipient')) {
                $stillUsed = Profile::query()
                    ->where('form_id', $formId)
                    ->where('id', '!=', $profileId)
                    ->exists()
                    || FormBuilderQuestion::query()
                        ->withoutGlobalScopes()
                        ->where('form_id', $formId)
                        ->where('profile_id', '!=', $profileId)
                        ->exists();

                if (! $stillUsed) {
                    FormBuilderRecipient::query()->where('form_id', $formId)->delete();
                }
            }

            $profile->forceFill([
                'form_id' => 0,
                'form_title' => '',
                'form_email_tag' => '',
                'form_active' => false,
                'form_is_enable' => false,
                'enable_form_analytics' => false,
            ])->save();
        });
    }

    public function profileHasFormQuestions(Profile $profile): bool
    {
        return FormBuilderQuestion::query()
            ->withoutGlobalScopes()
            ->where('profile_id', $profile->id)
            ->where(function ($query): void {
                if (Schema::hasColumn('form_builder_question', 'is_deleted')) {
                    $query->where(function ($inner): void {
                        $inner->where('is_deleted', 0)->orWhereNull('is_deleted');
                    });
                }
            })
            ->exists();
    }

    /**
     * @param  array<int, string>  $emails
     */
    public function syncRecipients(Profile $profile, array $emails): void
    {
        $formId = $this->ensureFormId($profile);

        FormBuilderRecipient::query()->where('form_id', $formId)->delete();

        $key = (new FormBuilderRecipient)->getKeyName();
        $auto = (new FormBuilderRecipient)->getIncrementing();
        $nextId = $auto ? 0 : (int) FormBuilderRecipient::query()->max($key);

        foreach ($emails as $email) {
            $email = trim(strtolower($email));

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $payload = [
                'form_id' => $formId,
                'recipient_email' => $email,
            ];

            if (! $auto) {
                $nextId++;
                $payload[$key] = max(1, $nextId);
            }

            FormBuilderRecipient::query()->create($payload);
        }
    }

    /**
     * @return Collection<int, FormBuilderRecipient>
     */
    public function recipientsForProfile(Profile $profile): Collection
    {
        $formId = (int) ($profile->form_id ?: 0);

        if ($formId <= 0) {
            return collect();
        }

        $key = (new FormBuilderRecipient)->getKeyName();

        return FormBuilderRecipient::query()
            ->where('form_id', $formId)
            ->orderBy($key)
            ->get();
    }

    public function updateFormSettings(Profile $profile, array $settings): void
    {
        $formId = $this->ensureFormId($profile);
        // Legacy: survey / voc Form Builder is free (no $5 purchase); all other types must buy.
        $enabled = $profile->formBuilderEntitled()
            && (bool) ($settings['form_is_enable'] ?? $profile->form_is_enable);

        $profile->update([
            'form_id' => $formId,
            'form_title' => $settings['form_title'] ?? ($profile->form_title ?: ''),
            'form_email_tag' => $settings['form_email_tag'] ?? ($profile->form_email_tag ?: ''),
            'form_is_enable' => $enabled,
            // form_active is a paid entitlement. Only the purchase lifecycle changes it.
            'form_active' => (bool) $profile->form_active,
            'form_submission_format' => (int) ($settings['form_submission_format'] ?? $profile->form_submission_format ?? 0),
        ]);

        if (isset($settings['recipients']) && is_array($settings['recipients'])) {
            $this->syncRecipients($profile, $settings['recipients']);
        }
    }

    protected function optionPrimaryKey(): string
    {
        return Schema::hasColumn('form_builder_question_options', 'question_option_id')
            ? 'question_option_id'
            : 'option_id';
    }

    protected function optionUsesAutoIncrement(): bool
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

    public function questionUsesAutoIncrement(): bool
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

    public function answerUsesAutoIncrement(): bool
    {
        return (new FormBuilderAnswer)->getIncrementing();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAnswer(array $data): FormBuilderAnswer
    {
        $answer = new FormBuilderAnswer;
        $key = $answer->getKeyName();

        if (! $answer->getIncrementing()) {
            $nextId = (int) FormBuilderAnswer::query()->max($key) + 1;
            $data[$key] = max(1, $nextId);
        } else {
            unset($data['answer_id'], $data['form_builder_answer_id'], $data[$key]);
        }

        // Live schema has many NOT NULL text/int columns without defaults.
        $defaults = [
            'row_number' => 0,
            'question_answer' => '',
            'signature_name_text' => '',
            'signature_employer_text' => '',
            'signature_email_text' => '',
            'signature_phone_text' => '',
            'participant_signature_image' => '',
            'participant_employer_text' => '',
            'session_id' => '',
            'app_user_firstname' => '',
            'app_user_lastname' => '',
            'app_user_email' => '',
            'app_user_mobile' => '',
        ];

        foreach ($defaults as $column => $default) {
            if (! Schema::hasColumn('form_builder_answers', $column)) {
                continue;
            }

            if (! array_key_exists($column, $data) || $data[$column] === null) {
                $data[$column] = $default;
            }
        }

        if (! isset($data['date_time']) && Schema::hasColumn('form_builder_answers', 'date_time')) {
            $data['date_time'] = now();
        }

        return FormBuilderAnswer::query()->create($data);
    }

    protected function ensureLibraryEntry(Profile $profile, int $formId): void
    {
        $exists = FormBuilderLibrary::query()->where('form_id', $formId)->exists();

        if ($exists) {
            return;
        }

        $userId = (int) ($profile->user_id ?: 0);
        $payload = [
            'form_id' => $formId,
            'form_title' => $profile->form_title ?: ($profile->name ?: 'Form '.$formId),
            'is_deleted' => false,
            'is_deleted_from_library' => false,
        ];

        // Prefer a real client_users FK; avoid inserting 0 when the column is nullable.
        if ($userId > 0) {
            $payload['user_id'] = $userId;
        } elseif (Schema::hasColumn('form_builder_library', 'user_id')) {
            $payload['user_id'] = null;
        }

        if (! $this->libraryUsesAutoIncrement()) {
            $nextId = (int) FormBuilderLibrary::query()->max('form_builder_library_id') + 1;
            $payload['form_builder_library_id'] = max(1, $nextId);
        }

        FormBuilderLibrary::query()->create($payload);
    }

    protected function libraryUsesAutoIncrement(): bool
    {
        if (! Schema::hasTable('form_builder_library')) {
            return true;
        }

        try {
            $column = DB::selectOne(
                'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['form_builder_library', 'form_builder_library_id']
            );

            return $column && str_contains((string) ($column->EXTRA ?? ''), 'auto_increment');
        } catch (\Throwable) {
            return false;
        }
    }
}
