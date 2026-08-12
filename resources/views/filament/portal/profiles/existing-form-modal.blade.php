@if ($this->showExistingFormModal ?? false)
    <div
        class="sl-efm-overlay"
        wire:click.self="closeExistingFormModal"
        role="dialog"
        aria-modal="true"
        aria-label="Use an existing form"
    >
        <div class="sl-efm-dialog" wire:click.stop>
            <div class="sl-efm-header">
                <div class="sl-efm-header-titles">
                    <h2 class="sl-efm-title">Use an existing form</h2>
                    <p class="sl-efm-subtitle">Copy the questions from another code profile or your saved library.</p>
                </div>
                <button type="button" class="sl-efm-close" wire:click="closeExistingFormModal" aria-label="Close">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>

            @if ($this->existingFormApplying)
                <div class="sl-efm-banner sl-efm-banner--busy">
                    <span class="sl-efm-spinner" aria-hidden="true"></span>
                    Applying form to this profile&hellip;
                </div>
            @elseif ($this->existingFormStatus)
                <div class="sl-efm-banner">{{ $this->existingFormStatus }}</div>
            @endif

            @if ($this->showLibraryFormPreview)
                <div class="sl-efm-body">
                    <button type="button" class="sl-efm-back" wire:click="backFromLibraryFormPreview">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                        Back
                    </button>
                    <h3 class="sl-efm-preview-title">Form preview — <span>{{ $this->libraryPreviewTitle }}</span></h3>
                    <div class="sl-efm-preview-list">
                        @forelse ($this->libraryPreviewQuestions as $question)
                            <div class="sl-efm-preview-item">
                                <span class="sl-efm-preview-q">{{ $question['text'] !== '' ? $question['text'] : '(untitled)' }}</span>
                                <span class="sl-efm-badge">{{ $question['type'] }}</span>
                            </div>
                        @empty
                            <p class="sl-efm-empty">No questions in this form.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="sl-efm-tabs" role="tablist">
                    @php
                        $efmTabs = [
                            'existing' => 'Existing code profile',
                            'library' => 'My library',
                            'account' => 'Another account',
                        ];
                    @endphp
                    @foreach ($efmTabs as $tabKey => $tabLabel)
                        <button
                            type="button"
                            role="tab"
                            class="sl-efm-tab {{ ($this->existingFormTab ?? '') === $tabKey ? 'is-active' : '' }}"
                            wire:click="setExistingFormTab('{{ $tabKey }}')"
                        >{{ $tabLabel }}</button>
                    @endforeach
                </div>

                <div class="sl-efm-body">
                    @if (($this->existingFormTab ?? '') === 'existing')
                        @forelse ($this->existingFormProfiles as $index => $existingForm)
                            <div class="sl-efm-row">
                                <span class="sl-efm-row-no">{{ $index + 1 }}</span>
                                <div class="sl-efm-row-main">
                                    <span class="sl-efm-row-title">{{ $existingForm['form_title'] !== '' ? $existingForm['form_title'] : 'Untitled form' }}</span>
                                    <span class="sl-efm-row-meta">Profile #{{ $existingForm['id'] }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="sl-efm-select"
                                    wire:click="selectExistingFormFromProfile({{ (int) $existingForm['id'] }})"
                                    wire:loading.attr="disabled"
                                >Select</button>
                            </div>
                        @empty
                            <p class="sl-efm-empty">No other code profiles with forms were found.</p>
                        @endforelse
                    @elseif (($this->existingFormTab ?? '') === 'library')
                        @forelse ($this->existingLibraryForms as $index => $libraryForm)
                            <div class="sl-efm-row">
                                <span class="sl-efm-row-no">{{ $index + 1 }}</span>
                                <div class="sl-efm-row-main">
                                    <span class="sl-efm-row-title">{{ $libraryForm['form_title'] !== '' ? $libraryForm['form_title'] : 'Untitled form' }}</span>
                                    <span class="sl-efm-row-meta">Library form #{{ $libraryForm['form_id'] }}</span>
                                </div>
                                <div class="sl-efm-row-actions">
                                    <button type="button" class="sl-efm-link" wire:click="previewExistingLibraryForm({{ (int) $libraryForm['form_id'] }})">Preview</button>
                                    <button
                                        type="button"
                                        class="sl-efm-link sl-efm-link--danger"
                                        x-on:click="window.slConfirm('Are you sure you want to remove this form?').then(ok => ok && $wire.deleteExistingLibraryForm({{ (int) $libraryForm['form_id'] }}))"
                                    >Delete</button>
                                    <button
                                        type="button"
                                        class="sl-efm-select"
                                        wire:click="selectExistingFormFromLibrary({{ (int) $libraryForm['form_id'] }})"
                                    >Select</button>
                                </div>
                            </div>
                        @empty
                            <p class="sl-efm-empty">No forms in your library.</p>
                        @endforelse
                    @else
                        @if ($this->showOtherAccountLibrary)
                            @forelse ($this->otherAccountLibraryForms as $index => $libraryForm)
                                <div class="sl-efm-row">
                                    <span class="sl-efm-row-no">{{ $index + 1 }}</span>
                                    <div class="sl-efm-row-main">
                                        <span class="sl-efm-row-title">{{ $libraryForm['form_title'] !== '' ? $libraryForm['form_title'] : 'Untitled form' }}</span>
                                        <span class="sl-efm-row-meta">Library form #{{ $libraryForm['form_id'] }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="sl-efm-select"
                                        wire:click="selectExistingFormFromLibrary({{ (int) $libraryForm['form_id'] }})"
                                    >Select</button>
                                </div>
                            @empty
                                <p class="sl-efm-empty">That account has no library forms.</p>
                            @endforelse
                        @else
                            <div class="sl-efm-login">
                                <p class="sl-efm-login-hint">Sign in to another ScanLink account to browse its saved forms.</p>
                                <label class="sl-efm-field">
                                    <span>Email</span>
                                    <input type="text" maxlength="255" wire:model="otherAccountEmail" autocomplete="off">
                                </label>
                                <label class="sl-efm-field">
                                    <span>Password</span>
                                    <input type="password" maxlength="255" wire:model="otherAccountPassword" autocomplete="off">
                                </label>
                                <button type="button" class="sl-efm-select sl-efm-select--full" wire:click="loginOtherAccountForForms">Log in</button>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif
