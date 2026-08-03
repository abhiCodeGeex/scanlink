<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use App\Models\ClientUser;
use App\Models\FormBuilderLibrary;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Services\FormBuilderService;
use App\Services\FormLibraryService;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

    public bool $showExistingFormModal = false;

    /** existing|library|account */
    public string $existingFormTab = 'existing';

    /** @var list<array{id: int, form_title: string}> */
    public array $existingFormProfiles = [];

    /** @var list<array{form_id: int, form_title: string}> */
    public array $existingLibraryForms = [];

    public string $otherAccountEmail = '';

    public string $otherAccountPassword = '';

    public ?string $existingFormStatus = null;

    public bool $existingFormApplying = false;

    /** @var list<array{form_id: int, form_title: string}> */
    public array $otherAccountLibraryForms = [];

    public bool $showOtherAccountLibrary = false;

    public bool $showLibraryFormPreview = false;

    public string $libraryPreviewTitle = '';

    /** @var list<array{text: string, type: string}> */
    public array $libraryPreviewQuestions = [];

    /** Bumps the Form Builder iframe URL so it reloads after applying a form. */
    public int $formBuilderEmbedNonce = 0;

    public function openExistingFormModal(): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            Notification::make()
                ->title('Save the profile first to use an existing form')
                ->warning()
                ->send();

            return;
        }

        if (method_exists($this, 'canAccessFormBuilder') && ! $this->canAccessFormBuilder()) {
            Notification::make()
                ->title('You do not have access to Form Builder')
                ->warning()
                ->send();

            return;
        }

        $this->existingFormTab = 'existing';
        $this->existingFormStatus = null;
        $this->existingFormApplying = false;
        $this->showLibraryFormPreview = false;
        $this->showOtherAccountLibrary = false;
        $this->otherAccountLibraryForms = [];
        $this->otherAccountEmail = '';
        $this->otherAccountPassword = '';
        $this->loadExistingFormModalLists($profile);
        $this->showExistingFormModal = true;
    }

    public function closeExistingFormModal(): void
    {
        $this->showExistingFormModal = false;
        $this->existingFormApplying = false;
        $this->showLibraryFormPreview = false;
        $this->existingFormStatus = null;
    }

    public function setExistingFormTab(string $tab): void
    {
        if (! in_array($tab, ['existing', 'library', 'account'], true)) {
            return;
        }

        $this->existingFormTab = $tab;
        $this->showLibraryFormPreview = false;
        $this->existingFormStatus = null;
    }

    public function selectExistingFormFromProfile(int $sourceProfileId): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        $client = method_exists($this, 'currentClient') ? $this->currentClient() : null;

        if (! $client) {
            Notification::make()->title('No client context')->danger()->send();

            return;
        }

        if ($sourceProfileId === (int) $profile->id) {
            Notification::make()->title('Choose a different profile')->warning()->send();

            return;
        }

        Profile::query()
            ->where('client_id', $client->id)
            ->whereKey($sourceProfileId)
            ->firstOrFail();

        $this->existingFormApplying = true;
        $this->existingFormStatus = 'Please Wait. Applying Form to Profile...';

        $cloned = app(FormLibraryService::class)->copyFromProfile($sourceProfileId, $profile);

        if ($cloned === 0) {
            $this->existingFormApplying = false;
            $this->existingFormStatus = null;
            Notification::make()->title('Source profile has no questions')->warning()->send();

            return;
        }

        $this->finishExistingFormApply($profile, $cloned);
    }

    public function selectExistingFormFromLibrary(int $formId): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        $this->existingFormApplying = true;
        $this->existingFormStatus = 'Please Wait. Applying Form to Profile...';

        $cloned = app(FormLibraryService::class)->applyLibraryFormToProfile($formId, $profile);

        if ($cloned === 0) {
            $this->existingFormApplying = false;
            $this->existingFormStatus = null;
            Notification::make()->title('Library form has no questions')->warning()->send();

            return;
        }

        $this->finishExistingFormApply($profile, $cloned);
    }

    public function deleteExistingLibraryForm(int $formId): void
    {
        $entry = FormBuilderLibrary::query()
            ->where('form_id', $formId)
            ->where('is_deleted_from_library', false)
            ->first();

        if (! $entry) {
            return;
        }

        $client = method_exists($this, 'currentClient') ? $this->currentClient() : null;

        if ($client) {
            $allowedUserIds = \Illuminate\Support\Facades\DB::table('client_users')
                ->where('client_id', $client->id)
                ->pluck('id');

            if (! $allowedUserIds->contains($entry->user_id)) {
                Notification::make()->title('Unable to remove this form')->danger()->send();

                return;
            }
        }

        app(FormLibraryService::class)->deleteFromLibrary($entry);

        $this->existingLibraryForms = array_values(array_filter(
            $this->existingLibraryForms,
            fn (array $row): bool => (int) $row['form_id'] !== $formId
        ));

        Notification::make()->title('Form removed from library')->success()->send();
    }

    public function previewExistingLibraryForm(int $formId): void
    {
        $entry = FormBuilderLibrary::query()
            ->where('form_id', $formId)
            ->where('is_deleted_from_library', false)
            ->first();

        if (! $entry) {
            return;
        }

        $this->libraryPreviewTitle = (string) ($entry->form_title ?: 'Form '.$formId);
        $this->libraryPreviewQuestions = FormBuilderQuestion::query()
            ->where('form_id', $formId)
            ->orderBy('question_order')
            ->get()
            ->map(fn (FormBuilderQuestion $q): array => [
                'text' => (string) ($q->question_text ?? ''),
                'type' => $q->typeName(),
            ])
            ->values()
            ->all();

        $this->showLibraryFormPreview = true;
    }

    public function backFromLibraryFormPreview(): void
    {
        $this->showLibraryFormPreview = false;
        $this->libraryPreviewTitle = '';
        $this->libraryPreviewQuestions = [];
    }

    public function loginOtherAccountForForms(): void
    {
        $email = strtolower(trim($this->otherAccountEmail));
        $password = $this->otherAccountPassword;

        if ($email === '') {
            $this->existingFormStatus = 'Enter email address.';

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->existingFormStatus = 'Enter a valid email.';

            return;
        }

        if ($password === '') {
            $this->existingFormStatus = 'Enter password.';

            return;
        }

        $member = ClientUser::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        $auth = $member?->authUser;

        if (! $member || ! $auth || ! Hash::check($password, $auth->password)) {
            $this->existingFormStatus = 'Invalid email or password.';
            $this->showOtherAccountLibrary = false;
            $this->otherAccountLibraryForms = [];

            return;
        }

        $this->existingFormStatus = null;
        $this->otherAccountPassword = '';
        $this->showOtherAccountLibrary = true;
        $this->otherAccountLibraryForms = FormBuilderLibrary::query()
            ->where('user_id', $member->id)
            ->where('is_deleted_from_library', false)
            ->orderBy('form_title')
            ->get()
            ->map(fn (FormBuilderLibrary $row): array => [
                'form_id' => (int) $row->form_id,
                'form_title' => (string) ($row->form_title ?: 'Form '.$row->form_id),
            ])
            ->values()
            ->all();
    }

    protected function finishExistingFormApply(Profile $profile, int $cloned): void
    {
        $this->reloadFormBuilderQuestions($profile->refresh());
        $this->formBuilderEmbedNonce++;
        $this->existingFormApplying = false;
        $this->existingFormStatus = null;
        $this->showExistingFormModal = false;

        if (property_exists($this, 'data') && is_array($this->data)) {
            // Legacy: survey / voc Form Builder is free; others need form_active.
            $this->data['form_is_enable'] = $profile->formBuilderEntitled();
            $this->data['form_active'] = (bool) $profile->form_active;
        }

        Notification::make()
            ->title('Form applied')
            ->body("{$cloned} question(s) copied onto this profile.")
            ->success()
            ->send();
    }

    protected function loadExistingFormModalLists(Profile $profile): void
    {
        $client = method_exists($this, 'currentClient') ? $this->currentClient() : null;

        if (! $client) {
            $this->existingFormProfiles = [];
            $this->existingLibraryForms = [];

            return;
        }

        $this->existingFormProfiles = Profile::query()
            ->where('client_id', $client->id)
            ->where('id', '!=', $profile->id)
            ->where('form_id', '>', 0)
            ->orderByDesc('id')
            ->get(['id', 'form_title', 'name'])
            ->map(fn (Profile $row): array => [
                'id' => (int) $row->id,
                'form_title' => (string) ($row->form_title ?: $row->name ?: 'Profile #'.$row->id),
            ])
            ->values()
            ->all();

        $currentFormId = (int) ($profile->form_id ?: 0);

        $this->existingLibraryForms = app(FormLibraryService::class)
            ->libraryFormsForClient((int) $client->id)
            ->filter(fn (FormBuilderLibrary $row): bool => $currentFormId === 0 || (int) $row->form_id !== $currentFormId)
            ->map(fn (FormBuilderLibrary $row): array => [
                'form_id' => (int) $row->form_id,
                'form_title' => (string) ($row->form_title ?: 'Form '.$row->form_id),
            ])
            ->values()
            ->all();
    }

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
        $this->persistFormBuilderSidebarFlags(notifyEnable: false);
    }

    /**
     * Persist Enable immediately so phone preview + live scan URL show the form without waiting for Save.
     */
    public function updatedDataFormIsEnable(mixed $value): void
    {
        $this->persistFormBuilderSidebarFlags(notifyEnable: true);
    }

    /**
     * Persist submission format immediately (legacy posts it with the parent form).
     */
    public function updatedDataFormSubmissionFormat(mixed $value): void
    {
        $this->persistFormBuilderSidebarFlags(notifyEnable: false);
    }

    /**
     * Persist analytics immediately; once on, iframe reloads in locked mode (legacy behaviour).
     */
    public function updatedDataEnableFormAnalytics(mixed $value): void
    {
        $wasLocked = (bool) ($this->sidebarProfile()?->enable_form_analytics);
        $this->persistFormBuilderSidebarFlags(notifyEnable: false);

        $enabled = filter_var(data_get($this->data, 'enable_form_analytics'), FILTER_VALIDATE_BOOLEAN);

        if ($enabled && ! $wasLocked) {
            $this->formBuilderEmbedNonce++;

            Notification::make()
                ->title('Form Analytics enabled — form editing is now locked')
                ->warning()
                ->send();
        }
    }

    /**
     * Sync Form Name / Email Tag / recipients from the Form Builder iframe (legacy Save behaviour).
     *
     * @param  list<string>  $recipients
     */
    public function syncFormBuilderIframeMeta(string $formName = '', string $emailTag = '', array $recipients = []): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        // Legacy parity (location/edit.php:1001-1021): a configured participant list
        // (any participant rows or notification recipients) requires a Participant Name
        // element (question_type_id 18) on the form, otherwise submissions can never be
        // matched back to a participant. Block the save with the legacy message.
        $hasParticipantList = (\Illuminate\Support\Facades\Schema::hasTable('participant')
                && \App\Models\Participant::query()->where('profile_id', $profile->id)->exists())
            || (\Illuminate\Support\Facades\Schema::hasTable('participant_recipient')
                && \App\Models\ParticipantRecipient::query()->where('profile_id', $profile->id)->exists());

        if ($hasParticipantList) {
            $hasParticipantNameField = \App\Models\FormBuilderQuestion::query()
                ->where('profile_id', $profile->id)
                ->where('question_type_id', 18)
                ->exists();

            if (! $hasParticipantNameField) {
                $message = 'This form has an active participant list but no Participant Name field has been added. Please add a Participant Name field to the form before saving.';

                Notification::make()->title($message)->danger()->send();

                throw ValidationException::withMessages([
                    'data.form_is_enable' => $message,
                ]);
            }
        }

        $enabled = $profile->formBuilderEntitled()
            && filter_var(data_get($this->data, 'form_is_enable'), FILTER_VALIDATE_BOOLEAN);
        $formName = trim($formName);
        $emailTag = trim($emailTag);
        $emails = [];

        foreach ($recipients as $recipient) {
            $email = trim((string) $recipient);
            if ($email === '') {
                continue;
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Notification::make()
                    ->title('Enter a valid email.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'data.form_is_enable' => 'Enter a valid email.',
                ]);
            }
            $emails[] = $email;
        }

        if ($enabled) {
            if ($formName === '') {
                Notification::make()
                    ->title('Please enter form name')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'data.form_is_enable' => 'Please enter form name',
                ]);
            }

            if ($emails === []) {
                Notification::make()
                    ->title('Please enter at least one recipient email')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'data.form_is_enable' => 'Please enter at least one recipient email',
                ]);
            }
        }

        $format = (int) data_get($this->data, 'form_submission_format', $profile->form_submission_format ?? 0);
        $analytics = filter_var(
            data_get($this->data, 'enable_form_analytics', $profile->enable_form_analytics),
            FILTER_VALIDATE_BOOLEAN
        );

        app(FormBuilderService::class)->updateFormSettings($profile, [
            'form_title' => $formName !== '' ? $formName : ($profile->form_title ?: ''),
            'form_email_tag' => $emailTag,
            'form_is_enable' => $enabled,
            'form_submission_format' => $format,
            'recipients' => $emails,
        ]);

        $profile->forceFill([
            'enable_form_analytics' => $analytics,
            'form_submission_format' => $format,
        ])->save();

        data_set($this->data, 'form_email_tag', $emailTag);

        if (property_exists($this, 'record') && $this->record instanceof Profile) {
            $this->record = $profile->fresh([
                'client',
                'equipmentType',
                'qrImage',
            ]) ?? $profile;
        }
    }

    public function persistFormBuilderEnableFlags(): void
    {
        $this->persistFormBuilderSidebarFlags(notifyEnable: true);
    }

    /**
     * Parent owns Enable / Analytics / submission format (read from Livewire $data, not Filament getState).
     */
    protected function persistFormBuilderSidebarFlags(bool $notifyEnable = false): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        $requestedEnabled = filter_var(data_get($this->data, 'form_is_enable'), FILTER_VALIDATE_BOOLEAN);
        // Legacy: survey / voc Form Builder is free (no $5 purchase); all other types must buy.
        $entitled = $profile->formBuilderEntitled();
        $enabled = $entitled && $requestedEnabled;

        if ($requestedEnabled && ! $entitled) {
            data_set($this->data, 'form_is_enable', false);

            if ($notifyEnable) {
                Notification::make()
                    ->title('Purchase Form Builder activation first')
                    ->body('The primary account user can activate Form Builder for $5 AUD.')
                    ->warning()
                    ->send();
            }
        }
        // Legacy: once analytics is on, it stays on (checkbox disabled in UI).
        $analytics = $profile->enable_form_analytics
            || filter_var(data_get($this->data, 'enable_form_analytics'), FILTER_VALIDATE_BOOLEAN);
        $format = (int) data_get($this->data, 'form_submission_format', $profile->form_submission_format ?? 0);

        if (! in_array($format, [0, 1], true)) {
            $format = 0;
        }

        data_set($this->data, 'form_is_enable', $enabled);
        data_set($this->data, 'enable_form_analytics', $analytics);
        data_set($this->data, 'form_submission_format', $format);

        $profile->forceFill([
            'form_is_enable' => $enabled,
            'enable_form_analytics' => $analytics,
            'form_submission_format' => $format,
        ])->save();

        if (property_exists($this, 'record') && $this->record instanceof Profile) {
            $this->record = $profile->fresh([
                'client',
                'equipmentType',
                'qrImage',
            ]) ?? $profile;
        }

        if (method_exists($this, 'refreshPhonePreview')) {
            $this->refreshPhonePreview();
        }

        if ($notifyEnable && ! ($requestedEnabled && ! (bool) $profile->form_active)) {
            Notification::make()
                ->title($enabled ? 'Form enabled on profile' : 'Form disabled on profile')
                ->success()
                ->send();
        }
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
