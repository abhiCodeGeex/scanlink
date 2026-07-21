<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Services\FormBuilderService;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

trait HasLegacyFormBuilderSidebar
{
    /** @var array<int, string> */
    public array $formRecipients = [''];

    /** @var array{question: array<int, array{id: int, label: string}>, format: array<int, array{id: int, label: string}>, answer: array<int, array{id: int, label: string}>} */
    public array $formBuilderPalette = [
        'question' => [],
        'format' => [],
        'answer' => [],
    ];

    /** @var array<int, array{id: int, type: string, text: string, box: string}> */
    public array $formBuilderQuestions = [];

    public ?int $fbComposingTypeId = null;

    public ?int $fbEditingQuestionId = null;

    public string $fbComposerText = '';

    public bool $fbComposerMandatory = true;

    /** @var array<int, array{option_name: string}> */
    public array $fbComposerOptions = [['option_name' => '']];

    public function addFormRecipient(): void
    {
        $this->formRecipients[] = '';
    }

    public function removeFormRecipient(int $index): void
    {
        unset($this->formRecipients[$index]);
        $this->formRecipients = array_values($this->formRecipients);

        if ($this->formRecipients === []) {
            $this->formRecipients = [''];
        }
    }

    protected function loadFormBuilderSidebarState(?Profile $profile): void
    {
        $this->loadFormBuilderPalette();

        if (! $profile?->exists) {
            $this->formRecipients = [''];
            $this->formBuilderQuestions = [];

            return;
        }

        $emails = app(FormBuilderService::class)
            ->recipientsForProfile($profile)
            ->pluck('recipient_email')
            ->filter()
            ->values()
            ->all();

        $this->formRecipients = $emails !== [] ? $emails : [''];
        $this->reloadFormBuilderQuestions($profile);
    }

    protected function loadFormBuilderPalette(): void
    {
        $groups = app(FormBuilderService::class)->paletteGroups();

        $map = function (Collection $items): array {
            return $items
                ->map(fn ($type): array => [
                    'id' => (int) $type->question_type_id,
                    'label' => $type->label(),
                ])
                ->values()
                ->all();
        };

        $this->formBuilderPalette = [
            'question' => $map($groups['question'] ?? collect()),
            'format' => $map($groups['format'] ?? collect()),
            'answer' => $map($groups['answer'] ?? collect()),
        ];

        // Fallback labels matching live if DB types are empty.
        if ($this->formBuilderPalette['question'] === []
            && $this->formBuilderPalette['format'] === []
            && $this->formBuilderPalette['answer'] === []) {
            $this->formBuilderPalette = [
                'question' => [
                    ['id' => 2, 'label' => 'Text'],
                    ['id' => 25, 'label' => 'Covid check-in'],
                ],
                'format' => [
                    ['id' => 13, 'label' => 'Line Divider'],
                    ['id' => 14, 'label' => 'Blank Space'],
                    ['id' => 22, 'label' => 'SWMS Hazard/Risk'],
                    ['id' => 24, 'label' => 'Add recipient'],
                    ['id' => 11, 'label' => 'Image'],
                    ['id' => 16, 'label' => 'Signature Panel'],
                    ['id' => 17, 'label' => 'Upload Button'],
                    ['id' => 18, 'label' => 'Participant Name'],
                    ['id' => 19, 'label' => 'Location Function'],
                    ['id' => 20, 'label' => 'Web Link Button'],
                    ['id' => 21, 'label' => 'Document Button'],
                ],
                'answer' => [
                    ['id' => 1, 'label' => 'Text Field'],
                    ['id' => 3, 'label' => 'Multiple Choices'],
                    ['id' => 4, 'label' => 'Check Box'],
                    ['id' => 5, 'label' => 'Drop Down Menu'],
                    ['id' => 6, 'label' => 'Number Scale'],
                    ['id' => 7, 'label' => 'Grid'],
                    ['id' => 8, 'label' => 'Date'],
                    ['id' => 9, 'label' => 'Time'],
                    ['id' => 15, 'label' => 'Comments'],
                    ['id' => 23, 'label' => 'Document Menu'],
                ],
            ];
        }
    }

    protected function reloadFormBuilderQuestions(Profile $profile): void
    {
        $this->formBuilderQuestions = app(FormBuilderService::class)
            ->questionsForProfile($profile->id)
            ->map(fn (FormBuilderQuestion $q): array => [
                'id' => (int) $q->question_id,
                'type' => $q->typeName(),
                'text' => (string) ($q->question_text ?? ''),
                'box' => $q->boxClass(),
            ])
            ->values()
            ->all();
    }

    public function openFormBuilderTool(int $typeId): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile) {
            Notification::make()
                ->title('Save the profile first to add form elements')
                ->warning()
                ->send();

            return;
        }

        // Live: line divider / blank space drop straight onto canvas.
        if (in_array($typeId, [13, 14], true)) {
            app(FormBuilderService::class)->saveQuestion($profile, [
                'question_type_id' => $typeId,
                'question_text' => $typeId === 13 ? '—' : '',
                'is_mandatory' => false,
            ]);
            $this->reloadFormBuilderQuestions($profile->refresh());

            return;
        }

        $this->fbComposingTypeId = $typeId;
        $this->fbEditingQuestionId = null;
        $this->fbComposerText = '';
        $this->fbComposerMandatory = true;
        $this->fbComposerOptions = [['option_name' => '']];
    }

    public function editFormBuilderQuestion(int $questionId): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile) {
            return;
        }

        $question = FormBuilderQuestion::query()
            ->with('options')
            ->where('profile_id', $profile->id)
            ->find($questionId);

        if (! $question) {
            return;
        }

        $this->fbEditingQuestionId = $questionId;
        $this->fbComposingTypeId = (int) $question->question_type_id;
        $this->fbComposerText = (string) $question->question_text;
        $this->fbComposerMandatory = (bool) $question->is_mandatory;
        $this->fbComposerOptions = $question->options
            ->map(fn ($o): array => ['option_name' => (string) $o->option_name])
            ->values()
            ->all() ?: [['option_name' => '']];
    }

    public function cancelFormBuilderComposer(): void
    {
        $this->fbComposingTypeId = null;
        $this->fbEditingQuestionId = null;
        $this->fbComposerText = '';
        $this->fbComposerMandatory = true;
        $this->fbComposerOptions = [['option_name' => '']];
    }

    public function addFormBuilderOption(): void
    {
        $this->fbComposerOptions[] = ['option_name' => ''];
    }

    public function removeFormBuilderOption(int $index): void
    {
        unset($this->fbComposerOptions[$index]);
        $this->fbComposerOptions = array_values($this->fbComposerOptions) ?: [['option_name' => '']];
    }

    public function saveFormBuilderQuestion(): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile || ! $this->fbComposingTypeId) {
            return;
        }

        $payload = [
            'question_type_id' => $this->fbComposingTypeId,
            'question_text' => $this->fbComposerText,
            'is_mandatory' => $this->fbComposerMandatory,
        ];

        $options = [];
        if (in_array($this->fbComposingTypeId, [3, 4, 5], true)) {
            $options = array_map(
                fn (array $o): array => ['option_name' => trim((string) ($o['option_name'] ?? ''))],
                $this->fbComposerOptions
            );
        }

        app(FormBuilderService::class)->saveQuestion(
            $profile,
            $payload,
            $options,
            $this->fbEditingQuestionId
        );
        $this->cancelFormBuilderComposer();
        $this->reloadFormBuilderQuestions($profile->refresh());

        Notification::make()->title('Form element saved')->success()->send();
    }

    public function deleteFormBuilderQuestion(int $questionId): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile) {
            return;
        }

        FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->where('question_id', $questionId)
            ->delete();

        $this->reloadFormBuilderQuestions($profile->refresh());
    }

    protected function syncFormBuilderSidebarSettings(): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile) {
            return;
        }

        // Parent owns Enable / Analytics / submission format only.
        // Form Name, recipients, and questions are owned by the Form Builder iframe (live behaviour).
        $state = $this->form->getState();

        $enabled = (bool) ($state['form_is_enable'] ?? $profile->form_is_enable);

        $profile->forceFill([
            'form_is_enable' => $enabled,
            // Live scan page checks form_active; keep it in sync with the Enable checkbox.
            'form_active' => $enabled,
            'enable_form_analytics' => (bool) ($state['enable_form_analytics'] ?? $profile->enable_form_analytics),
            'form_submission_format' => (int) ($state['form_submission_format'] ?? $profile->form_submission_format ?? 0),
        ])->save();
    }

    /**
     * Persist Enable immediately so phone preview + live scan URL show the form without waiting for Save.
     */
    public function updatedDataFormIsEnable(mixed $value): void
    {
        $this->persistFormBuilderEnableFlags();
    }

    public function persistFormBuilderEnableFlags(): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        $enabled = filter_var(data_get($this->data, 'form_is_enable'), FILTER_VALIDATE_BOOLEAN);

        $profile->forceFill([
            'form_is_enable' => $enabled,
            'form_active' => $enabled,
        ])->save();

        if (property_exists($this, 'record') && $this->record instanceof Profile) {
            $this->record = $profile->fresh([
                'client',
                'equipmentType',
                'qrImage',
            ]) ?? $profile;
        }

        Notification::make()
            ->title($enabled ? 'Form enabled on profile' : 'Form disabled on profile')
            ->success()
            ->send();
    }

    protected function sidebarProfile(): ?Profile
    {
        $record = null;

        if (method_exists($this, 'getRecord')) {
            try {
                $record = $this->getRecord();
            } catch (\Throwable) {
                $record = null;
            }
        }

        if (! $record && property_exists($this, 'record') && $this->record instanceof Profile) {
            $record = $this->record;
        }

        return ($record instanceof Profile && $record->exists) ? $record : null;
    }
}
