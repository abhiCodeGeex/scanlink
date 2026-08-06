@if ($this->showExistingFormModal ?? false)
    <div
        class="sl-existing-form-overlay"
        wire:click.self="closeExistingFormModal"
        role="dialog"
        aria-modal="true"
        aria-label="Use an existing form"
    >
        <div class="sl-existing-form-dialog" id="form_exist" wire:click.stop>
            @if ($this->existingFormApplying)
                <div id="progress_bar" class="sl-existing-form-progress">
                    <img src="{{ asset('images/spinner_gray.gif') }}" alt="" width="16" height="16">
                    Please Wait. Applying Form to Profile...
                </div>
            @endif

            @if ($this->existingFormStatus && ! $this->existingFormApplying)
                <div class="sl-existing-form-status">{{ $this->existingFormStatus }}</div>
            @endif

            @if ($this->showLibraryFormPreview)
                <div id="preview_form" class="sl-existing-form-preview">
                    <button type="button" class="sl-existing-form-back" wire:click="backFromLibraryFormPreview">
                        &larr; Go Back
                    </button>
                    <h2>Form Preview : <span>{{ $this->libraryPreviewTitle }}</span></h2>
                    <div id="form_content" class="sl-existing-form-preview-list">
                        @forelse ($this->libraryPreviewQuestions as $question)
                            <div class="sl-existing-form-preview-item">
                                <strong>{{ $question['text'] !== '' ? $question['text'] : '(untitled)' }}</strong>
                                <span>{{ $question['type'] }}</span>
                            </div>
                        @empty
                            <p>No questions in this form.</p>
                        @endforelse
                    </div>
                    <button type="button" class="sl-existing-form-back" wire:click="backFromLibraryFormPreview">
                        &larr; Go Back
                    </button>
                </div>
            @else
                <div id="form_listing" align="left">
                    <fieldset class="existing-form">
                        <legend>
                            <img src="{{ asset('images/logo.png') }}" alt="SCANLINK" width="144">
                        </legend>

                        <div id="tabed_content_main" class="sl-existing-form-tabs">
                            <div class="sl-existing-form-tablist" role="tablist">
                                <button
                                    type="button"
                                    role="tab"
                                    class="sl-existing-form-tab {{ ($this->existingFormTab ?? '') === 'existing' ? 'is-active' : '' }}"
                                    wire:click="setExistingFormTab('existing')"
                                >
                                    Select From existing code profile
                                </button>
                                <button
                                    type="button"
                                    role="tab"
                                    class="sl-existing-form-tab {{ ($this->existingFormTab ?? '') === 'library' ? 'is-active' : '' }}"
                                    wire:click="setExistingFormTab('library')"
                                >
                                    Select from library
                                </button>
                                <button
                                    type="button"
                                    role="tab"
                                    class="sl-existing-form-tab {{ ($this->existingFormTab ?? '') === 'account' ? 'is-active' : '' }}"
                                    wire:click="setExistingFormTab('account')"
                                >
                                    Select from another account
                                </button>
                            </div>

                            @if (($this->existingFormTab ?? '') === 'existing')
                                <div id="tabs-1" class="sl-existing-form-tabpanel">
                                    @forelse ($this->existingFormProfiles as $index => $existingForm)
                                        <div class="form-list">
                                            <div class="sr-no">{{ $index + 1 }}</div>
                                            <div class="form-name">{{ $existingForm['form_title'] !== '' ? $existingForm['form_title'] : ' ' }}</div>
                                            <div class="form-name">Profile # {{ $existingForm['id'] }}</div>
                                            <div class="form-select">
                                                <button
                                                    type="button"
                                                    class="save sl-existing-form-select"
                                                    wire:click="selectExistingFormFromProfile({{ (int) $existingForm['id'] }})"
                                                    wire:loading.attr="disabled"
                                                >
                                                    SELECT
                                                </button>
                                            </div>
                                        </div>
                                        <div class="clear">&nbsp;</div>
                                    @empty
                                        <p class="sl-existing-form-empty">No other code profiles with forms were found.</p>
                                    @endforelse
                                </div>
                            @elseif (($this->existingFormTab ?? '') === 'library')
                                <div id="tabs-2" class="sl-existing-form-tabpanel">
                                    @forelse ($this->existingLibraryForms as $index => $libraryForm)
                                        <div class="form-list" id="form_{{ $libraryForm['form_id'] }}">
                                            <div class="sr-no">{{ $index + 1 }}</div>
                                            <div class="form-name" style="width: 64%;">
                                                {{ $libraryForm['form_title'] !== '' ? $libraryForm['form_title'] : ' ' }}
                                            </div>
                                            <div class="form-select sl-existing-form-actions">
                                                <button
                                                    type="button"
                                                    class="save sl-existing-form-select"
                                                    wire:click="selectExistingFormFromLibrary({{ (int) $libraryForm['form_id'] }})"
                                                >
                                                    SELECT
                                                </button>
                                                <button
                                                    type="button"
                                                    class="sl-existing-form-link"
                                                    wire:click="previewExistingLibraryForm({{ (int) $libraryForm['form_id'] }})"
                                                >
                                                    Preview
                                                </button>
                                                <button
                                                    type="button"
                                                    class="sl-existing-form-link sl-existing-form-link--danger"
                                                    wire:click="deleteExistingLibraryForm({{ (int) $libraryForm['form_id'] }})"
                                                    wire:confirm="Are you sure you want to remove this form?"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        <div class="clear" id="clear_{{ $libraryForm['form_id'] }}">&nbsp;</div>
                                    @empty
                                        <p class="sl-existing-form-empty">No forms in your library.</p>
                                    @endforelse
                                </div>
                            @else
                                <div id="tabs-3" class="sl-existing-form-tabpanel">
                                    @if ($this->showOtherAccountLibrary)
                                        @forelse ($this->otherAccountLibraryForms as $index => $libraryForm)
                                            <div class="form-list">
                                                <div class="sr-no">{{ $index + 1 }}</div>
                                                <div class="form-name" style="width: 64%;">
                                                    {{ $libraryForm['form_title'] !== '' ? $libraryForm['form_title'] : ' ' }}
                                                </div>
                                                <div class="form-select">
                                                    <button
                                                        type="button"
                                                        class="save sl-existing-form-select"
                                                        wire:click="selectExistingFormFromLibrary({{ (int) $libraryForm['form_id'] }})"
                                                    >
                                                        SELECT
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="clear">&nbsp;</div>
                                        @empty
                                            <p class="sl-existing-form-empty">That account has no library forms.</p>
                                        @endforelse
                                    @else
                                        <div id="login_form">
                                            <div class="clear">&nbsp;</div>
                                            <div class="label">Email</div>
                                            <div class="txtbox">
                                                <input
                                                    type="text"
                                                    id="txt_other_user_email"
                                                    maxlength="255"
                                                    class="textbox"
                                                    wire:model="otherAccountEmail"
                                                >
                                            </div>
                                            <div class="clear">&nbsp;</div>
                                            <div class="label">Password</div>
                                            <div class="txtbox">
                                                <input
                                                    type="password"
                                                    id="txt_other_user_pass"
                                                    maxlength="255"
                                                    class="textbox"
                                                    wire:model="otherAccountPassword"
                                                >
                                            </div>
                                            <div class="clear">&nbsp;</div>
                                            <div style="width: 30%; float: right;">
                                                <button type="button" class="save" wire:click="loginOtherAccountForForms">
                                                    Login
                                                </button>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </fieldset>
                </div>
            @endif

            <button
                type="button"
                class="sl-existing-form-close"
                wire:click="closeExistingFormModal"
                aria-label="Close"
            >
                &times;
            </button>
        </div>
    </div>
@endif
