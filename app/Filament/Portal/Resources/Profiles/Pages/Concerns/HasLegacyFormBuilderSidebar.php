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

    /** Client id of the account signed into via "Another account" (guards form copying). */
    public ?int $otherAccountClientId = null;

    /** User id signed into via "Another account" (listing + copy guard are per-user). */
    public ?int $otherAccountUserId = null;

    public bool $showOtherAccountLibrary = false;

    public bool $showLibraryFormPreview = false;

    public string $libraryPreviewTitle = '';

    /** Rendered form markup for the preview modal (scan-page-style, disabled controls). */
    public string $libraryPreviewHtml = '';

    /** @var list<array{text: string, type: string}> */
    public array $libraryPreviewQuestions = [];

    /** Bumps the Form Builder iframe URL so it reloads after applying a form. */
    public int $formBuilderEmbedNonce = 0;

    /**
     * Existing forms applied to this profile, derived from question `applied_source`.
     * Drives the "Use an existing form" checked state + removable chips.
     *
     * @var list<array{key: string, label: string}>
     */
    public array $appliedExistingForms = [];

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
        $this->otherAccountClientId = null;
        $this->otherAccountUserId = null;
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
            $this->js('(window.slAlert || window.alert)('.json_encode('Source profile has no questions. Please choose a form that contains questions.').')');

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
            $this->js('(window.slAlert || window.alert)('.json_encode('This library form has no questions. Please choose a form that contains questions.').')');

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

        // Not every previewable form is a library save — "Another account" also lists forms
        // straight from that user's profiles, so fall back to the profile for the title.
        $sourceProfile = Profile::query()->where('form_id', $formId)->orderBy('id')->first();

        if (! $entry && ! $sourceProfile) {
            return;
        }

        $this->libraryPreviewTitle = (string) ($entry?->form_title
            ?: ($sourceProfile?->form_title ?: 'Form '.$formId));

        // ALWAYS show where this form comes from in the preview heading: the code profile
        // that carries the form — or, for orphaned library saves (source form replaced /
        // profile gone), the account user who saved it.
        if ($sourceProfile) {
            $sourceLabel = trim((string) ($sourceProfile->code_profile_name ?: $sourceProfile->name));
            $this->libraryPreviewTitle .= ' — Profile #'.$sourceProfile->id.($sourceLabel !== '' ? ' ('.$sourceLabel.')' : '');
        } elseif ($entry) {
            $owner = ClientUser::query()->find((int) $entry->user_id);
            $ownerLabel = trim((string) ($owner?->email ?: ''));
            $this->libraryPreviewTitle .= $ownerLabel !== ''
                ? ' — saved by '.$ownerLabel
                : ' — Library form #'.$formId;
        }

        // Legacy parity (formbuilder/get_data_for_form_preview): the preview renders the
        // ACTUAL form — real inputs, choices, textareas — not a list of question names.
        $questions = FormBuilderQuestion::query()
            ->with('options')
            ->where('form_id', $formId)
            ->orderBy('question_order')
            ->get();

        $this->libraryPreviewHtml = $this->buildLibraryPreviewHtml($questions);
        $this->libraryPreviewQuestions = [];

        $this->showLibraryFormPreview = true;
    }

    /**
     * Render the form questions as scan-page-style markup for the preview modal
     * (all controls disabled — display only).
     *
     * @param  Collection<int, FormBuilderQuestion>  $questions
     */
    protected function buildLibraryPreviewHtml(Collection $questions): string
    {
        $html = '';
        $star = '<span style="color:#c00;">*</span>';

        foreach ($questions as $q) {
            $tid = (int) $q->question_type_id;
            $label = e(trim(html_entity_decode(strip_tags((string) ($q->question_text ?? '')), ENT_QUOTES)));
            $req = $q->is_mandatory ? ' '.$star : '';
            $options = \App\Support\FormBuilderMedia::choiceOptions($q);
            $choiceLabel = e(\App\Support\FormBuilderMedia::choiceLabel($q));

            $html .= '<div class="sl-fp-q">';

            $html .= match (true) {
                $tid === 1 => '<label>'.$label.$req.'</label><input type="text" disabled>',
                in_array($tid, [2, 13, 14], true) => '<div class="sl-fp-html">'.($tid === 13 ? '<hr>' : ($tid === 14 ? '<br>' : $q->question_text)).'</div>',
                $tid === 10 => '<h1 style="font-size:20px;margin:.2rem 0;">'.$label.'</h1>',
                $tid === 12 => '<h3 style="font-size:15px;margin:.2rem 0;">'.$label.'</h3>',
                $tid === 3 => '<label>'.($choiceLabel !== '' ? $choiceLabel : '').$req.'</label>'
                    .collect($options)->map(fn ($o) => '<div class="sl-fp-choice"><input type="radio" disabled> '.e($o).'</div>')->implode(''),
                $tid === 4 => '<label>'.($choiceLabel !== '' ? $choiceLabel : '').$req.'</label>'
                    .collect($options)->map(fn ($o) => '<div class="sl-fp-choice"><input type="checkbox" disabled> '.e($o).'</div>')->implode(''),
                $tid === 5, $tid === 6 => '<label>'.($choiceLabel !== '' ? $choiceLabel : $label).$req.'</label>'
                    .'<select disabled><option>Select…</option></select>',
                $tid === 7 => '<label>'.$label.$req.'</label><div class="sl-fp-grid">Grid ('
                    .$q->options->where('question_option_type_id', 5)->count().' rows × '
                    .$q->options->where('question_option_type_id', 6)->count().' columns)</div>',
                $tid === 8 => '<label>'.$label.$req.'</label><input type="date" disabled>',
                $tid === 9 => '<label>'.$label.$req.'</label><input type="time" disabled>',
                $tid === 15 => '<label>'.$label.$req.'</label><textarea rows="2" disabled></textarea>',
                $tid === 16 => '<label>'.($label !== '' ? $label : 'Signature').$req.'</label><div class="sl-fp-sign">Signature</div>'
                    .($q->include_name ? '<label>Name</label><input type="text" disabled>' : '')
                    .($q->include_employer ? '<label>Employer</label><input type="text" disabled>' : '')
                    .($q->include_email ? '<label>Email</label><input type="text" disabled>' : '')
                    .($q->include_phone ? '<label>Phone</label><input type="text" disabled>' : ''),
                $tid === 17 => '<label>'.($label !== '' ? $label : 'Upload').$req.'</label><div class="sl-fp-btn" style="background:#007A01;">Upload</div>',
                $tid === 18 => '<label>'.($label !== '' ? $label : 'Participant name').$req.'</label><input type="text" disabled placeholder="Full name">',
                $tid === 19 => '<label>'.($label !== '' ? $label : 'Location').$req.'</label><input type="text" disabled placeholder="Location">'
                    .'<div class="sl-fp-btn" style="background:#808080;">MAP</div>',
                $tid === 20 => '<div class="sl-fp-btn" style="background:#'.e($q->button_colour ?: '007A01').';">'.($label !== '' ? $label : 'Open link').'</div>',
                $tid === 21 => '<div class="sl-fp-btn" style="background:#'.e($q->button_colour ?: '007A01').';">'.e($q->doc_title ?: 'View document').'</div>',
                $tid === 22 => '<label>'.($label !== '' ? $label : 'SWMS Hazard / Risk').$req.'</label><div class="sl-fp-btn" style="background:#808080;">SWMS Hazard/Risk</div>',
                $tid === 23 => '<label>'.e($q->doc_title ?: 'Select documents').$req.'</label><div class="sl-fp-btn" style="background:#808080;">Document Menu</div>',
                $tid === 24 => '<label>'.($label !== '' ? $label : 'Additional recipient email').$req.'</label><input type="text" disabled placeholder="Email">',
                $tid === 25 => '<div class="sl-fp-btn" style="background:#007A01;">COVID CHECK-IN</div>',
                default => '<label>'.$label.$req.'</label><input type="text" disabled>',
            };

            $html .= '</div>';
        }

        return $html !== '' ? $html : '<p class="sl-efm-empty">This form has no questions.</p>';
    }

    public function backFromLibraryFormPreview(): void
    {
        $this->showLibraryFormPreview = false;
        $this->libraryPreviewTitle = '';
        $this->libraryPreviewHtml = '';
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
            // Legacy welcome/login_from_other_user.
            $this->existingFormStatus = 'Invalid Username or Password...!';
            $this->showOtherAccountLibrary = false;
            $this->otherAccountLibraryForms = [];

            return;
        }

        // Legacy: reject a blocked client account, then a blocked sub-user.
        if (! (bool) ($member->client?->approve)) {
            $this->existingFormStatus = 'Oops! your account is blocked, please contact admin to un-block account.';
            $this->showOtherAccountLibrary = false;
            $this->otherAccountLibraryForms = [];

            return;
        }

        if ($member->is_sub_user && ! (bool) $member->status) {
            $this->existingFormStatus = 'Oops! your account is blocked.';
            $this->showOtherAccountLibrary = false;
            $this->otherAccountLibraryForms = [];

            return;
        }

        $this->existingFormStatus = null;
        $this->otherAccountPassword = '';
        $this->showOtherAccountLibrary = true;
        $this->otherAccountClientId = (int) $member->client_id;
        $this->otherAccountUserId = (int) $member->id;

        // Per-user scope (like legacy's get_library_forms_other_user), but complete: the
        // signed-in user's library saves PLUS every form profile that user created —
        // "all forms of that particular user account", not just explicit library saves.
        $library = FormBuilderLibrary::query()
            ->where('user_id', $member->id)
            ->where('is_deleted_from_library', false)
            ->get()
            ->map(fn (FormBuilderLibrary $row): array => [
                'type' => 'library',
                'id' => (int) $row->form_id,
                'form_id' => (int) $row->form_id,
                'form_title' => (string) ($row->form_title ?: 'Form '.$row->form_id),
                'meta' => 'Library form #'.$row->form_id,
            ]);

        $libraryFormIds = $library->pluck('form_id')->all();

        $profiles = Profile::query()
            ->where('client_id', $member->client_id)
            ->where('user_id', $member->id)
            ->where('form_id', '>', 0)
            ->active()
            ->when($libraryFormIds !== [], fn ($q) => $q->whereNotIn('form_id', $libraryFormIds))
            ->orderByDesc('id')
            ->get(['id', 'form_id', 'form_title', 'name', 'code_profile_name'])
            ->map(fn (Profile $p): array => [
                'type' => 'profile',
                'id' => (int) $p->id,
                'form_id' => (int) $p->form_id,
                'form_title' => (string) ($p->form_title ?: ($p->code_profile_name ?: ($p->name ?: 'Profile #'.$p->id))),
                'meta' => 'Profile #'.$p->id,
            ]);

        $this->otherAccountLibraryForms = $library->concat($profiles)
            ->sortBy('form_title', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * Apply a form chosen on the "Another account" tab: library entries clone via the
     * library path; plain profile forms clone straight from that profile. The source is
     * validated against the account that was signed into (otherAccountClientId).
     */
    public function selectOtherAccountForm(string $type, int $id): void
    {
        if ($type !== 'profile') {
            $this->selectExistingFormFromLibrary($id);

            return;
        }

        $profile = $this->sidebarProfile();

        if (! $profile?->exists || ! $this->otherAccountClientId) {
            return;
        }

        $source = Profile::query()
            ->where('client_id', $this->otherAccountClientId)
            ->when($this->otherAccountUserId, fn ($q) => $q->where('user_id', $this->otherAccountUserId))
            ->where('form_id', '>', 0)
            ->findOrFail($id);

        $this->existingFormApplying = true;
        $this->existingFormStatus = 'Please Wait. Applying Form to Profile...';

        $cloned = app(FormLibraryService::class)->copyFromProfile((int) $source->id, $profile);

        if ($cloned === 0) {
            $this->existingFormApplying = false;
            $this->existingFormStatus = null;
            $this->js('(window.slAlert || window.alert)('.json_encode("That profile's form has no questions. Please choose a form that contains questions.").')');

            return;
        }

        $this->finishExistingFormApply($profile, $cloned);
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

        // Sub-users only see forms of the profiles selected for them in Manage User.
        $allowed = \App\Filament\Portal\Concerns\InteractsWithClientMembership::portalMembership()?->allowedProfileIds();

        $this->existingFormProfiles = Profile::query()
            ->where('client_id', $client->id)
            ->where('id', '!=', $profile->id)
            ->where('form_id', '>', 0)
            ->when($allowed !== null, fn ($q) => $q->whereIn('id', $allowed))
            ->orderByDesc('id')
            ->get(['id', 'form_title', 'name'])
            ->map(fn (Profile $row): array => [
                'id' => (int) $row->id,
                'form_title' => (string) ($row->form_title ?: $row->name ?: 'Profile #'.$row->id),
            ])
            ->values()
            ->all();

        $currentFormId = (int) ($profile->form_id ?: 0);

        $libraryRows = app(FormLibraryService::class)
            ->libraryFormsForClient((int) $client->id)
            ->filter(fn (FormBuilderLibrary $row): bool => $currentFormId === 0 || (int) $row->form_id !== $currentFormId);

        // Resolve each library form's source code profile so the list can show it.
        $sourceProfiles = Profile::query()
            ->whereIn('form_id', $libraryRows->pluck('form_id')->all() ?: [0])
            ->orderBy('id')
            ->get(['id', 'form_id', 'name', 'code_profile_name'])
            ->keyBy(fn (Profile $p): int => (int) $p->form_id);

        $this->existingLibraryForms = $libraryRows
            ->map(function (FormBuilderLibrary $row) use ($sourceProfiles): array {
                $src = $sourceProfiles->get((int) $row->form_id);
                $srcLabel = $src
                    ? 'Profile #'.$src->id.(trim((string) ($src->code_profile_name ?: $src->name)) !== ''
                        ? ' — '.trim((string) ($src->code_profile_name ?: $src->name))
                        : '')
                    : 'Library form #'.$row->form_id;

                return [
                    'form_id' => (int) $row->form_id,
                    'form_title' => (string) ($row->form_title ?: 'Form '.$row->form_id),
                    'source' => $srcLabel,
                ];
            })
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

        $this->reloadAppliedExistingForms($profile);
    }

    /**
     * Rebuild the applied-existing-form chips from the profile's live questions.
     */
    protected function reloadAppliedExistingForms(Profile $profile): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('form_builder_question', 'applied_source')) {
            $this->appliedExistingForms = [];

            return;
        }

        $sources = FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->whereNotNull('applied_source')
            ->where('applied_source', '!=', '')
            ->pluck('applied_source')
            ->unique()
            ->values();

        $this->appliedExistingForms = $sources
            ->map(fn (string $key): array => [
                'key' => $key,
                'label' => $this->appliedSourceLabel($key),
            ])
            ->all();
    }

    protected function appliedSourceLabel(string $key): string
    {
        [$type, $id] = array_pad(explode(':', $key, 2), 2, '');
        $id = (int) $id;

        if ($type === 'profile') {
            return 'Profile #'.$id;
        }

        if ($type === 'library') {
            $title = FormBuilderLibrary::query()
                ->where('form_id', $id)
                ->value('form_title');

            return filled($title) ? (string) $title : 'Library form #'.$id;
        }

        return $key;
    }

    /**
     * Remove every question copied from a given applied existing form (chip ×),
     * updating both the Form Builder canvas and the mobile preview.
     */
    public function removeAppliedForm(string $key): void
    {
        $profile = $this->sidebarProfile();

        if (! $profile?->exists) {
            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('form_builder_question', 'applied_source')) {
            return;
        }

        $removed = FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->where('applied_source', $key)
            ->update(['is_deleted' => true]);

        // Reload canvas + chips and force the legacy Form Builder iframe + preview to refresh.
        $this->reloadFormBuilderQuestions($profile->refresh());
        $this->formBuilderEmbedNonce++;

        Notification::make()
            ->title('Form removed')
            ->body($removed.' question(s) from '.$this->appliedSourceLabel($key).' were removed.')
            ->success()
            ->send();
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
     * Legacy parity: ticking "Enable Form Analytics" does NOT persist or lock anything —
     * the flag (and the form-edit lock) only take effect when the profile is actually
     * saved. Until then the user can freely untick the box to change their mind.
     */
    public function updatedDataEnableFormAnalytics(mixed $value): void
    {
        $enabled = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($enabled && ! (bool) ($this->sidebarProfile()?->enable_form_analytics)) {
            Notification::make()
                ->title('Form Analytics will be enabled when you save — form editing locks after saving.')
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

        // Legacy parity (location/edit.php:1001-1021): a configured participant list —
        // participant rows, or notification recipients with a NON-EMPTY email (legacy
        // trims and skips blanks) — requires a Participant Name element (type 18) on the
        // form, otherwise submissions can never be matched back to a participant.
        $hasParticipantList = (\Illuminate\Support\Facades\Schema::hasTable('participant')
                && \App\Models\Participant::query()->where('profile_id', $profile->id)->exists())
            || (\Illuminate\Support\Facades\Schema::hasTable('participant_recipient')
                && \App\Models\ParticipantRecipient::query()
                    ->where('profile_id', $profile->id)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->exists());

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
                    ->body('The primary account user can activate Form Builder for $'.number_format(\App\Support\PricingSettings::formBuilder(), 2).' AUD.')
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
