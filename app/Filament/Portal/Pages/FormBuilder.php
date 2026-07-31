<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\EquipmentType;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionType;
use App\Models\Profile;
use App\Services\FormBuilderService;
use App\Services\FormLibraryService;
use App\Support\FormBuilderMedia;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class FormBuilder extends Page
{
    use InteractsWithClientMembership;
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Form Builder';

    protected static ?string $title = 'Form Builder';

    protected static ?string $slug = 'form-builder';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.portal.pages.form-builder';

    public bool $isEmbed = false;

    public ?int $selectedProfileId = null;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $questions;

    /** @var array{question: Collection, format: Collection, answer: Collection} */
    public array $paletteGroups = [];

    public string $formTitle = '';

    /** @var array<int, string> */
    public array $recipients = [];

    public string $formEmailTag = '';

    public bool $formIsEnable = false;

    public bool $formActive = false;

    public int $formSubmissionFormat = 0;

    public ?int $composingTypeId = null;

    public ?int $editingQuestionId = null;

    public string $composerQuestionText = '';

    public bool $composerIsMandatory = true;

    public bool $composerIsLogchecked = false;

    public string $composerLogColumntitle = '';

    /** @var array<int, array{option_name: string}> */
    public array $composerOptions = [];

    public string $composerScaleFrom = '';

    public string $composerScaleTo = '';

    /** @var array<int, array{option_name: string}> */
    public array $composerGridRows = [];

    /** @var array<int, array{option_name: string}> */
    public array $composerGridCols = [];

    public string $composerImageTitle = '';

    public string $composerImageUrl = '';

    public string $composerImageAlign = 'left';

    /** @var TemporaryUploadedFile|null */
    public $composerImageUpload = null;

    /** @var TemporaryUploadedFile|null */
    public $composerDocUpload = null;

    /** @var array<int, TemporaryUploadedFile|null> */
    public array $composerDocUploads = [];

    public bool $composerIncludeName = true;

    public bool $composerIncludeEmployer = false;

    public bool $composerIncludeEmail = false;

    public bool $composerIncludePhone = false;

    public bool $composerParticipantIncludeSignature = false;

    public bool $composerParticipantIncludeEmployer = false;

    public ?int $copyFromProfileId = null;

    public string $composerButtonLinkUrl = '';

    public string $composerButtonColour = '007A01';

    public string $composerDocTitle = '';

    public string $composerDefaultValue = '';

    public string $composerCovidBgColor = '#ffffff';

    public string $composerCovidTextColor = '#222222';

    public bool $showPreview = false;

    protected FormBuilderService $formBuilder;

    protected FormLibraryService $formLibrary;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormBuilder(static::portalMembership());
    }

    public function boot(FormBuilderService $formBuilder, FormLibraryService $formLibrary): void
    {
        $this->formBuilder = $formBuilder;
        $this->formLibrary = $formLibrary;
    }

    public function mount(): void
    {
        $this->isEmbed = request()->boolean('embed');
        $this->questions = collect();
        $this->paletteGroups = $this->formBuilder->paletteGroups();

        $requestedProfileId = (int) (request()->query('profile') ?: request()->query('profile_id') ?: 0);

        if ($requestedProfileId > 0) {
            try {
                $this->selectProfile($requestedProfileId);

                return;
            } catch (\Throwable) {
                // Fall through to default profile selection.
            }
        }

        if ($this->isEmbed) {
            return;
        }

        $firstProfileId = $this->formProfileOptions()->keys()->first()
            ?: $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->selectProfile((int) $firstProfileId);
        }
    }

    public function getLayout(): string
    {
        return $this->isEmbed
            ? 'layouts.form-builder-embed'
            : parent::getLayout();
    }

    public function getView(): string
    {
        return $this->isEmbed
            ? 'filament.portal.pages.form-builder-embed'
            : $this->view;
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->isEmbed ? null : parent::getHeading();
    }

    public function getTitle(): string|Htmlable
    {
        return $this->isEmbed ? 'Form Builder' : (static::$title ?? 'Form Builder');
    }

    /**
     * Prefer Form/Survey/Checklist profiles (live Form Builder entry point),
     * then any other active profiles for this client.
     *
     * @return Collection<int|string, string>
     */
    public function formProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        $surveyTypeId = EquipmentType::query()->where('slag', 'survey')->value('id');

        $surveyOptions = Profile::selectOptionsForClient((int) $client->id, function ($query) use ($surveyTypeId): void {
            if ($surveyTypeId) {
                $query->where('type_id', $surveyTypeId);
            }
        });

        if ($surveyOptions->isNotEmpty()) {
            return $surveyOptions;
        }

        return $this->clientProfileOptions();
    }

    public function participantsUrl(): string
    {
        $url = ManageParticipants::getUrl();

        if ($this->selectedProfileId) {
            $url .= '?profile='.$this->selectedProfileId;
        }

        return $url;
    }

    public function formLibraryUrl(): string
    {
        return FormLibrary::getUrl();
    }

    public function formSubmissionsUrl(): string
    {
        $url = FormSubmissions::getUrl();

        if ($this->selectedProfileId) {
            $url .= '?profile='.$this->selectedProfileId;
        }

        return $url;
    }

    /**
     * Live behaviour: Line Divider / Blank Space drop straight onto the canvas.
     */
    public function quickAdd(int $typeId): void
    {
        if (! in_array($typeId, [13, 14], true)) {
            $this->openComposer($typeId);

            return;
        }

        $profile = $this->resolveProfile();

        try {
            $this->formBuilder->saveQuestion($profile, [
                'question_type_id' => $typeId,
                'question_text' => $typeId === 13 ? '—' : '',
                'is_mandatory' => false,
            ]);
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Could not add item')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title($typeId === 13 ? 'Line divider added' : 'Blank space added')
            ->success()
            ->send();

        $this->loadProfileData();
    }

    public function selectProfile(?int $profileId): void
    {
        if (! $profileId) {
            $this->selectedProfileId = null;
            $this->questions = collect();

            return;
        }

        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->cancelComposer();
        $this->loadProfileData();
    }

    public function updatedSelectedProfileId(?int $profileId): void
    {
        $this->selectProfile($profileId);
    }

    public function saveSettings(): void
    {
        $profile = $this->resolveProfile();
        $this->formActive = (bool) $profile->form_active;
        $this->formIsEnable = (bool) $profile->form_active && $this->formIsEnable;

        $this->formBuilder->updateFormSettings($profile, [
            'form_title' => $this->formTitle,
            'form_email_tag' => $this->formEmailTag,
            'form_is_enable' => $this->formIsEnable,
            'form_active' => $this->formActive,
            'form_submission_format' => $this->formSubmissionFormat,
            'recipients' => array_values(array_filter($this->recipients, fn (string $e): bool => trim($e) !== '')),
        ]);

        Notification::make()->title('Form settings saved')->success()->send();

        $this->loadProfileData();
    }

    public function purchaseFormBuilderUrl(): ?string
    {
        if (! $this->selectedProfileId || $this->formActive || ! $this->isPrimaryUser()) {
            return null;
        }

        return PurchaseFormBuilder::getUrl(
            ['profile' => $this->selectedProfileId],
            panel: 'portal',
        );
    }

    public function openComposer(int $typeId): void
    {
        if (in_array($typeId, [13, 14], true)) {
            $this->quickAdd($typeId);

            return;
        }

        $this->composingTypeId = $typeId;
        $this->editingQuestionId = null;
        $this->resetComposerFields();

        // Live Covid check-in defaults: log checkbox on, black text / white bg.
        if ($typeId === 25) {
            $this->composerIsLogchecked = true;
            $this->composerCovidTextColor = '#000000';
            $this->composerCovidBgColor = '#ffffff';
            $this->composerIsMandatory = false;
        }
    }

    public function editQuestion(int $questionId): void
    {
        $profile = $this->resolveProfile();

        $question = FormBuilderQuestion::query()
            ->with(['questionType', 'options'])
            ->where('profile_id', $profile->id)
            ->findOrFail($questionId);

        $this->editingQuestionId = $questionId;
        $this->composingTypeId = (int) $question->question_type_id;
        $this->composerQuestionText = (string) $question->question_text;
        $this->composerIsMandatory = (bool) $question->is_mandatory;
        $this->composerIsLogchecked = (bool) $question->is_logchecked;
        $this->composerLogColumntitle = (string) ($question->log_columntitle ?? '');
        $this->composerImageTitle = (string) ($question->image_title ?? '');
        $this->composerImageUrl = (string) ($question->image_url ?? '');
        $this->composerImageAlign = FormBuilderMedia::alignValue((string) ($question->image_align ?? 'left'));
        $this->composerDocUpload = null;
        $this->composerDocUploads = [];
        $this->composerIncludeName = (bool) ($question->include_name ?? true);
        $this->composerIncludeEmployer = (bool) ($question->include_employer ?? false);
        $this->composerIncludeEmail = (bool) ($question->include_email ?? false);
        $this->composerIncludePhone = (bool) ($question->include_phone ?? false);
        $this->composerParticipantIncludeSignature = (bool) ($question->participant_include_signature ?? false);
        $this->composerParticipantIncludeEmployer = (bool) ($question->participant_include_employer ?? false);
        $this->composerImageUpload = null;
        $this->composerButtonLinkUrl = (string) ($question->button_link_url ?? '');
        $this->composerButtonColour = (string) ($question->button_colour ?: '007A01');
        $this->composerDocTitle = (string) ($question->doc_title ?? '');
        $this->composerDefaultValue = (string) ($question->default_value ?? '');
        $this->composerCovidBgColor = (string) ($question->covid_bg_color ?: '#ffffff');
        $this->composerCovidTextColor = (string) ($question->covid_text_color ?: '#222222');

        if (! str_starts_with($this->composerCovidBgColor, '#')) {
            $this->composerCovidBgColor = '#'.$this->composerCovidBgColor;
        }
        if (! str_starts_with($this->composerCovidTextColor, '#')) {
            $this->composerCovidTextColor = '#'.$this->composerCovidTextColor;
        }

        $typeId = (int) $question->question_type_id;

        if (in_array($typeId, [3, 4, 5], true)) {
            $opts = $question->options->map(fn ($o): array => [
                'option_name' => (string) $o->option_name,
            ])->values()->all();

            if ($opts === [] && str_contains($this->composerQuestionText, ':::')) {
                [$labelPart, $optionsPart] = explode(':::', $this->composerQuestionText, 2);
                $optionNames = array_values(array_filter(
                    array_map('trim', explode(',', $optionsPart)),
                    fn (string $name): bool => $name !== '',
                ));

                if ($optionNames !== []) {
                    $this->composerOptions = array_map(
                        fn (string $name): array => ['option_name' => $name],
                        $optionNames,
                    );
                    $labelTrim = trim($labelPart);

                    if ($labelTrim !== '' && ! str_contains($labelTrim, ',')) {
                        $this->composerQuestionText = $labelTrim;
                    } else {
                        $this->composerQuestionText = '';
                    }
                } else {
                    $this->composerOptions = [['option_name' => '']];
                }
            } else {
                $this->composerOptions = $opts !== [] ? $opts : [['option_name' => '']];
            }
        } elseif ($typeId === 6) {
            $from = $question->options->firstWhere('question_option_type_id', 1);
            $to = $question->options->firstWhere('question_option_type_id', 2);
            $this->composerScaleFrom = (string) ($from?->option_name ?? '1');
            $this->composerScaleTo = (string) ($to?->option_name ?? '5');
            $this->composerOptions = [];
        } elseif ($typeId === 7) {
            $this->composerGridRows = $question->options
                ->where('question_option_type_id', 5)
                ->map(fn ($o): array => ['option_name' => (string) $o->option_name])
                ->values()->all();
            $this->composerGridCols = $question->options
                ->where('question_option_type_id', 6)
                ->map(fn ($o): array => ['option_name' => (string) $o->option_name])
                ->values()->all();
            $this->composerOptions = [];
        } else {
            $this->composerOptions = [];
        }
    }

    public function cancelComposer(): void
    {
        $this->composingTypeId = null;
        $this->editingQuestionId = null;
        $this->resetComposerFields();
    }

    public function saveQuestion(): void
    {
        $profile = $this->resolveProfile();
        $typeId = (int) $this->composingTypeId;

        if ($typeId <= 0) {
            return;
        }

        $type = FormBuilderQuestionType::query()->find($typeId);
        $isDisplay = $type?->isDisplayOnly() ?? false;

        if (! $isDisplay && trim($this->composerQuestionText) === '' && ! in_array($typeId, [11, 13, 14, 22, 24, 25], true)) {
            Notification::make()->title('Question text is required')->danger()->send();

            return;
        }

        if ($typeId === 22 && trim($this->composerQuestionText) === '') {
            $this->composerQuestionText = 'SWMS Hazard / Risk';
        }

        if ($typeId === 11 && $this->composerImageUpload instanceof TemporaryUploadedFile) {
            $this->composerImageUrl = $this->composerImageUpload->store('form-builder/images', 'public');
            $this->composerImageUpload = null;
        }

        if ($typeId === 21 && $this->composerDocUpload instanceof TemporaryUploadedFile) {
            $storedPath = $this->composerDocUpload->store('form-builder/docs', 'public');
            $this->composerQuestionText = $storedPath;
            $this->composerDocTitle = trim($this->composerDocTitle) !== ''
                ? $this->composerDocTitle
                : basename($storedPath);
            $this->composerButtonLinkUrl = trim($this->composerButtonLinkUrl) !== ''
                ? $this->composerButtonLinkUrl
                : $storedPath;
            $this->composerDocUpload = null;
        }

        if ($typeId === 23 && $this->composerDocUploads !== []) {
            $storedPaths = [];

            foreach ($this->composerDocUploads as $upload) {
                if ($upload instanceof TemporaryUploadedFile) {
                    $storedPaths[] = $upload->store('form-builder/docs', 'public');
                }
            }

            if ($storedPaths !== []) {
                $titles = FormBuilderMedia::splitCsv($this->composerDocTitle);

                if ($titles === []) {
                    $titles = array_map('basename', $storedPaths);
                } elseif (count($titles) < count($storedPaths)) {
                    foreach ($storedPaths as $index => $path) {
                        if (! isset($titles[$index])) {
                            $titles[$index] = basename($path);
                        }
                    }
                }

                $this->composerQuestionText = implode(',', $storedPaths);
                $this->composerDocTitle = implode(',', array_slice($titles, 0, count($storedPaths)));
            }

            $this->composerDocUploads = [];
        }

        if (in_array($typeId, [3, 4, 5], true)) {
            $nonEmptyOptions = array_filter(
                $this->composerOptions,
                fn (array $row): bool => trim((string) ($row['option_name'] ?? '')) !== '',
            );

            if ($nonEmptyOptions === []) {
                Notification::make()->title('Add at least one answer option')->danger()->send();

                return;
            }
        }

        if ($typeId === 6) {
            $from = is_numeric($this->composerScaleFrom) ? (float) $this->composerScaleFrom : null;
            $to = is_numeric($this->composerScaleTo) ? (float) $this->composerScaleTo : null;

            if ($from === null || $to === null || $from > $to) {
                Notification::make()->title('Scale requires numeric from/to values with from ≤ to')->danger()->send();

                return;
            }
        }

        if ($typeId === 7) {
            $nonEmptyRows = array_filter(
                $this->composerGridRows,
                fn (array $row): bool => trim((string) ($row['option_name'] ?? '')) !== '',
            );
            $nonEmptyCols = array_filter(
                $this->composerGridCols,
                fn (array $row): bool => trim((string) ($row['option_name'] ?? '')) !== '',
            );

            if ($nonEmptyRows === [] || $nonEmptyCols === []) {
                Notification::make()->title('Grid needs at least one row and one column')->danger()->send();

                return;
            }
        }

        $data = [
            'question_type_id' => $typeId,
            'question_text' => $this->composerQuestionText,
            'image_title' => $this->composerImageTitle,
            'image_url' => $this->composerImageUrl ?: null,
            'image_align' => FormBuilderMedia::alignValue($this->composerImageAlign),
            'button_link_url' => $this->composerButtonLinkUrl,
            'button_colour' => $this->composerButtonColour,
            'doc_title' => $this->composerDocTitle,
            'default_value' => $this->composerDefaultValue,
            'include_name' => $this->composerIncludeName,
            'include_employer' => $this->composerIncludeEmployer,
            'include_email' => $this->composerIncludeEmail,
            'include_phone' => $this->composerIncludePhone,
            'participant_include_signature' => $this->composerParticipantIncludeSignature,
            'participant_include_employer' => $this->composerParticipantIncludeEmployer,
            'is_mandatory' => $this->composerIsMandatory,
            'is_logchecked' => $this->composerIsLogchecked,
            'log_columntitle' => $this->composerLogColumntitle,
            'covid_bg_color' => ltrim($this->composerCovidBgColor, '#'),
            'covid_text_color' => ltrim($this->composerCovidTextColor, '#'),
        ];

        $options = $this->buildOptionsPayload($typeId);

        try {
            $this->formBuilder->saveQuestion(
                $profile,
                $data,
                $options,
                $this->editingQuestionId,
            );
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Could not save question')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again or contact support.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title($this->editingQuestionId ? 'Question updated' : 'Question added')
            ->success()
            ->send();

        $this->cancelComposer();
        $this->loadProfileData();
    }

    public function deleteQuestion(int $questionId): void
    {
        $profile = $this->resolveProfile();

        $this->formBuilder->deleteQuestion($profile, $questionId);

        if ($this->editingQuestionId === $questionId) {
            $this->cancelComposer();
        }

        Notification::make()->title('Question removed')->success()->send();

        $this->loadProfileData();
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    public function reorderQuestions(array $ids): void
    {
        $profile = $this->resolveProfile();

        $this->formBuilder->reorder($profile, array_map('intval', $ids));

        $this->loadProfileData();
    }

    public function addRecipient(): void
    {
        $this->recipients[] = '';
    }

    public function removeRecipient(int $index): void
    {
        unset($this->recipients[$index]);
        $this->recipients = array_values($this->recipients);
    }

    public function saveToLibrary(): void
    {
        $profile = $this->resolveProfile();

        $this->formLibrary->saveCurrentFormToLibrary($profile, $this->formTitle ?: null);

        Notification::make()->title('Form saved to library')->success()->send();
    }

    public function copyFromProfile(): void
    {
        if (! $this->copyFromProfileId) {
            Notification::make()->title('Select a profile to copy from')->warning()->send();

            return;
        }

        $profile = $this->resolveProfile();
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($this->copyFromProfileId);

        if ($this->copyFromProfileId === $profile->id) {
            Notification::make()->title('Choose a different profile')->warning()->send();

            return;
        }

        $cloned = $this->formLibrary->copyFromProfile($this->copyFromProfileId, $profile);

        if ($cloned === 0) {
            Notification::make()->title('Source profile has no questions')->warning()->send();

            return;
        }

        Notification::make()
            ->title('Form copied')
            ->body("{$cloned} question(s) copied from the selected profile.")
            ->success()
            ->send();

        $this->copyFromProfileId = null;
        $this->loadProfileData();
    }

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    public function addComposerOption(): void
    {
        $this->composerOptions[] = ['option_name' => ''];
    }

    public function removeComposerOption(int $index): void
    {
        unset($this->composerOptions[$index]);
        $this->composerOptions = array_values($this->composerOptions);
    }

    public function addGridRow(): void
    {
        $this->composerGridRows[] = ['option_name' => ''];
    }

    public function addGridCol(): void
    {
        $this->composerGridCols[] = ['option_name' => ''];
    }

    public function removeGridRow(int $index): void
    {
        unset($this->composerGridRows[$index]);
        $this->composerGridRows = array_values($this->composerGridRows);
    }

    public function removeGridCol(int $index): void
    {
        unset($this->composerGridCols[$index]);
        $this->composerGridCols = array_values($this->composerGridCols);
    }

    /**
     * @return Collection<int|string, string>
     */
    public function profilesWithExistingForms(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        $profileIdsWithQuestions = FormBuilderQuestion::query()
            ->whereIn('profile_id', Profile::query()
                ->where('client_id', $client->id)
                ->active()
                ->pluck('id'))
            ->distinct()
            ->pluck('profile_id');

        return Profile::selectOptionsForClient((int) $client->id, function ($query) use ($profileIdsWithQuestions): void {
            $query->whereIn('id', $profileIdsWithQuestions)
                ->when($this->selectedProfileId, fn ($q) => $q->where('id', '!=', $this->selectedProfileId));
        });
    }

    protected function loadProfileData(): void
    {
        if (! $this->selectedProfileId) {
            return;
        }

        $profile = $this->resolveProfile();

        $this->formTitle = (string) ($profile->form_title ?? '');
        $this->formEmailTag = (string) ($profile->form_email_tag ?? '');
        $this->formIsEnable = (bool) $profile->form_is_enable;
        $this->formActive = (bool) $profile->form_active;
        $this->formSubmissionFormat = (int) ($profile->form_submission_format ?? 0);

        $this->recipients = $this->formBuilder
            ->recipientsForProfile($profile)
            ->pluck('recipient_email')
            ->values()
            ->all();

        if ($this->recipients === []) {
            $this->recipients = [''];
        }

        $this->questions = $this->formBuilder->questionsForProfile($profile->id);
        $this->paletteGroups = $this->formBuilder->paletteGroups();
    }

    protected function resolveProfile(): Profile
    {
        $client = $this->requireClient();

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($this->selectedProfileId);
    }

    protected function resetComposerFields(): void
    {
        $this->composerQuestionText = '';
        $this->composerIsMandatory = true;
        $this->composerIsLogchecked = false;
        $this->composerLogColumntitle = '';
        $this->composerOptions = [['option_name' => '']];
        $this->composerScaleFrom = '1';
        $this->composerScaleTo = '5';
        $this->composerGridRows = [['option_name' => '']];
        $this->composerGridCols = [['option_name' => '']];
        $this->composerImageTitle = '';
        $this->composerImageUrl = '';
        $this->composerImageAlign = 'left';
        $this->composerImageUpload = null;
        $this->composerDocUpload = null;
        $this->composerDocUploads = [];
        $this->composerIncludeName = true;
        $this->composerIncludeEmployer = false;
        $this->composerIncludeEmail = false;
        $this->composerIncludePhone = false;
        $this->composerParticipantIncludeSignature = false;
        $this->composerParticipantIncludeEmployer = false;
        $this->composerButtonLinkUrl = '';
        $this->composerButtonColour = '007A01';
        $this->composerDocTitle = '';
        $this->composerDefaultValue = '';
        $this->composerCovidBgColor = '#ffffff';
        $this->composerCovidTextColor = '#222222';
    }

    /**
     * @return array<int, array{option_name: string, question_option_type_id?: int}>
     */
    protected function buildOptionsPayload(int $typeId): array
    {
        if (in_array($typeId, [3, 4, 5], true)) {
            return array_map(fn (array $row): array => [
                'option_name' => (string) ($row['option_name'] ?? ''),
                'question_option_type_id' => 0,
            ], $this->composerOptions);
        }

        if ($typeId === 6) {
            return [
                ['option_name' => $this->composerScaleFrom ?: '1', 'question_option_type_id' => 1],
                ['option_name' => $this->composerScaleTo ?: '5', 'question_option_type_id' => 2],
            ];
        }

        if ($typeId === 7) {
            $rows = array_map(fn (array $row): array => [
                'option_name' => (string) ($row['option_name'] ?? ''),
                'question_option_type_id' => 5,
            ], $this->composerGridRows);

            $cols = array_map(fn (array $row): array => [
                'option_name' => (string) ($row['option_name'] ?? ''),
                'question_option_type_id' => 6,
            ], $this->composerGridCols);

            return array_merge($rows, $cols);
        }

        return [];
    }
}
