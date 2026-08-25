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
                    {{-- Legacy parity: render the ACTUAL form (disabled controls), not a name list. --}}
                    <div class="sl-fp">
                        {!! $this->libraryPreviewHtml !!}
                    </div>
                    <style>
                        .sl-fp { max-height: 55vh; overflow-y: auto; padding: 4px 2px; font: 13px/1.45 Arial, Helvetica, sans-serif; color: #333; }
                        .sl-fp .sl-fp-q { margin: 0 0 12px; }
                        .sl-fp label { display: block; font-weight: 700; font-size: 12.5px; color: #374151; margin: 0 0 3px; }
                        .sl-fp input[type="text"], .sl-fp input[type="date"], .sl-fp input[type="time"],
                        .sl-fp select, .sl-fp textarea {
                            width: 100%; max-width: 420px; box-sizing: border-box; height: 34px; padding: 0 10px;
                            border: 1px solid #d1d5db; border-radius: 7px; background: #fafafa; color: #6b7280; font: inherit;
                        }
                        .sl-fp textarea { height: auto; min-height: 54px; padding: 8px 10px; }
                        .sl-fp .sl-fp-choice { margin: 2px 0; color: #374151; }
                        .sl-fp .sl-fp-choice input { width: auto; height: auto; }
                        .sl-fp .sl-fp-btn {
                            display: block; width: 100%; max-width: 420px; box-sizing: border-box; text-align: center;
                            color: #fff; font-weight: 700; border-radius: 7px; padding: 9px 12px; margin: 4px 0;
                        }
                        .sl-fp .sl-fp-sign {
                            width: 100%; max-width: 420px; box-sizing: border-box; height: 64px; line-height: 64px;
                            text-align: center; border: 1px dashed #c7ccd3; border-radius: 7px; color: #9ca3af; background: #fff;
                        }
                        .sl-fp .sl-fp-grid {
                            width: 100%; max-width: 420px; box-sizing: border-box; padding: 10px 12px;
                            border: 1px solid #d1d5db; border-radius: 7px; color: #6b7280; background: #fafafa;
                        }
                        .sl-fp .sl-fp-html { color: #374151; }
                        .sl-fp .sl-fp-html img { max-width: 100%; height: auto; }
                    </style>
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
                                    <span class="sl-efm-row-meta">{{ $libraryForm['source'] ?? 'Library form #'.$libraryForm['form_id'] }}</span>
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
                                        <span class="sl-efm-row-meta">{{ $libraryForm['meta'] ?? 'Library form #'.$libraryForm['form_id'] }}</span>
                                    </div>
                                    <div class="sl-efm-row-actions">
                                        <button type="button" class="sl-efm-link" wire:click="previewExistingLibraryForm({{ (int) $libraryForm['form_id'] }})">Preview</button>
                                        <button
                                            type="button"
                                            class="sl-efm-select"
                                            wire:click="selectOtherAccountForm('{{ $libraryForm['type'] ?? 'library' }}', {{ (int) ($libraryForm['id'] ?? $libraryForm['form_id']) }})"
                                        >Select</button>
                                    </div>
                                </div>
                            @empty
                                <p class="sl-efm-empty">That account has no forms.</p>
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
