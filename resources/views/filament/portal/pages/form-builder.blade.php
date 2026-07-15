<x-filament-panels::page>
    @php
        $typeLabel = fn (?int $id) => $id
            ? (\App\Models\FormBuilderQuestionType::query()->find($id)?->label() ?? "Type {$id}")
            : '';
    @endphp

    <style>
        .fb-root { --fb-green: #008C00; --fb-green-light: #9bff9b; --fb-orange: #ff6600; --fb-orange-light: #ffbf93; --fb-blue: #0066ff; --fb-blue-light: #b1d1ff; }
        .fb-card { border-radius: 12px; border: 1px solid rgb(229 231 235); background: #fff; padding: 1.25rem; box-shadow: 0 1px 3px rgb(0 0 0 / 0.06); }
        .dark .fb-card { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .fb-heading { font-size: 1rem; font-weight: 700; color: var(--fb-green); margin: 0 0 .75rem; letter-spacing: .02em; }
        .fb-select, .fb-input, .fb-textarea { width: 100%; border-radius: 8px; border: 1px solid rgb(209 213 219); padding: .5rem .75rem; font-size: .875rem; }
        .dark .fb-select, .dark .fb-input, .dark .fb-textarea { border-color: rgb(75 85 99); background: rgb(31 41 55); color: #fff; }
        .fb-label { display: block; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: rgb(107 114 128); margin-bottom: .35rem; }
        .fb-btn { display: inline-flex; align-items: center; gap: .35rem; border-radius: 8px; padding: .45rem .85rem; font-size: .8125rem; font-weight: 600; cursor: pointer; border: none; transition: opacity .15s; }
        .fb-btn:hover { opacity: .9; }
        .fb-btn-primary { background: var(--fb-green); color: #fff; }
        .fb-btn-secondary { background: rgb(243 244 246); color: rgb(55 65 81); }
        .dark .fb-btn-secondary { background: rgb(55 65 81); color: #fff; }
        .fb-btn-danger { background: rgb(220 38 38); color: #fff; }
        .fb-btn-sm { padding: .3rem .55rem; font-size: .75rem; }
        .fb-palette { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; }
        @media (max-width: 1024px) { .fb-palette { grid-template-columns: 1fr; } }
        .fb-palette-col { border-radius: 10px; overflow: hidden; border: 2px solid; }
        .fb-palette-col--question { border-color: #008000; }
        .fb-palette-col--format { border-color: #ff6600; }
        .fb-palette-col--answer { border-color: #0066ff; }
        .fb-palette-head { padding: .5rem .75rem; font-weight: 700; font-size: .8125rem; text-align: center; color: #fff; }
        .fb-palette-col--question .fb-palette-head { background: #008000; }
        .fb-palette-col--format .fb-palette-head { background: #ff6600; }
        .fb-palette-col--answer .fb-palette-head { background: #0066ff; }
        .fb-palette-body { padding: .5rem; display: flex; flex-direction: column; gap: .35rem; max-height: 280px; overflow-y: auto; }
        .fb-palette-col--question .fb-palette-body { background: #9bff9b; }
        .fb-palette-col--format .fb-palette-body { background: #ffbf93; }
        .fb-palette-col--answer .fb-palette-body { background: #b1d1ff; }
        .fb-palette-item { text-align: left; border: 1px solid rgb(0 0 0 / .08); border-radius: 6px; padding: .4rem .55rem; font-size: .75rem; font-weight: 600; background: rgb(255 255 255 / .75); cursor: pointer; transition: transform .1s; }
        .fb-palette-item:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgb(0 0 0 / .1); }
        .fb-canvas-title { text-align: center; font-weight: 800; font-size: .875rem; letter-spacing: .08em; color: var(--fb-green); padding: .75rem; border: 2px dashed var(--fb-green); border-radius: 10px; margin-bottom: .75rem; background: rgb(0 140 0 / .04); }
        .fb-question-list { display: flex; flex-direction: column; gap: .5rem; min-height: 80px; }
        .fb-box { display: flex; align-items: flex-start; gap: .75rem; border-radius: 8px; padding: .65rem .85rem; border-left: 5px solid; background: rgb(255 255 255 / .9); box-shadow: 0 1px 2px rgb(0 0 0 / .06); }
        .dark .fb-box { background: rgb(31 41 55); }
        .fb-box-question { border-left-color: #008000; background: linear-gradient(90deg, rgb(155 255 155 / .25), transparent); }
        .fb-box-format { border-left-color: #ff6600; background: linear-gradient(90deg, rgb(255 191 147 / .35), transparent); }
        .fb-box-answer { border-left-color: #0066ff; background: linear-gradient(90deg, rgb(177 209 255 / .35), transparent); }
        .fb-box-handle { cursor: grab; color: rgb(156 163 175); font-size: 1.1rem; line-height: 1; padding-top: .15rem; user-select: none; }
        .fb-box-handle:active { cursor: grabbing; }
        .fb-box-body { flex: 1; min-width: 0; }
        .fb-box-type { font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; opacity: .75; }
        .fb-box-text { font-size: .875rem; font-weight: 600; margin-top: .15rem; word-break: break-word; }
        .fb-badge { display: inline-block; font-size: .625rem; font-weight: 700; text-transform: uppercase; padding: .15rem .4rem; border-radius: 4px; background: rgb(220 38 38); color: #fff; margin-left: .35rem; vertical-align: middle; }
        .fb-box-actions { display: flex; flex-shrink: 0; gap: .35rem; flex-wrap: wrap; }
        .fb-composer { border: 2px solid var(--fb-green); border-radius: 10px; padding: 1rem; margin-top: 1rem; background: rgb(0 140 0 / .03); }
        .fb-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        @media (max-width: 768px) { .fb-grid-2 { grid-template-columns: 1fr; } }
        .fb-layout { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        .fb-layout--preview { grid-template-columns: 1fr 320px; }
        @media (max-width: 1200px) { .fb-layout--preview { grid-template-columns: 1fr; } }
        .fb-preview { border: 1px solid rgb(209 213 219); border-radius: 12px; padding: 1rem; background: #f5f5f5; max-height: 600px; overflow-y: auto; }
        .dark .fb-preview { border-color: rgb(75 85 99); background: rgb(17 24 39); }
        .fb-preview-phone { max-width: 320px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 1rem; box-shadow: 0 4px 12px rgb(0 0 0 / .12); }
        .dark .fb-preview-phone { background: rgb(31 41 55); }
        .fb-preview h4 { color: var(--fb-green); margin: 0 0 .75rem; font-size: 1rem; }
        .fb-preview-field { margin-bottom: .75rem; }
        .fb-preview-field label { display: block; font-size: .8125rem; font-weight: 600; margin-bottom: .25rem; }
        .fb-preview-field input, .fb-preview-field select, .fb-preview-field textarea { width: 100%; font-size: .8125rem; padding: .35rem; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box; }
        .fb-empty { text-align: center; padding: 2rem 1rem; color: rgb(107 114 128); }
        .fb-empty-cta { margin-top: .75rem; }
        .fb-settings-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem; }
        @media (max-width: 768px) { .fb-settings-grid { grid-template-columns: 1fr; } }
        .fb-recipient-row { display: flex; gap: .5rem; margin-bottom: .5rem; }
        .fb-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .sortable-ghost { opacity: .4; }
        .fb-palette-item { cursor: grab; }
        .fb-palette-item:active { cursor: grabbing; }
        .fb-canvas-drop { transition: border-color .2s, background .2s; }
        .fb-canvas-drop--over { border-color: var(--fb-green) !important; background: rgb(0 140 0 / .08) !important; }
        .fb-composer-wrap { overflow: hidden; max-height: 0; opacity: 0; transform: translateY(-8px); transition: max-height .35s ease, opacity .25s ease, transform .25s ease; }
        .fb-composer-wrap.is-open { max-height: 2000px; opacity: 1; transform: translateY(0); margin-top: 1rem; }
        .fb-loading { position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgb(0 0 0 / .25); }
        .fb-spinner { width: 2.5rem; height: 2.5rem; border: 3px solid rgb(255 255 255 / .35); border-top-color: #fff; border-radius: 50%; animation: fb-spin .7s linear infinite; }
        @keyframes fb-spin { to { transform: rotate(360deg); } }
        .fb-copy-row { display: flex; gap: .5rem; align-items: flex-end; flex-wrap: wrap; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgb(229 231 235); }
        .dark .fb-copy-row { border-color: rgb(55 65 81); }
        .fb-image-preview { max-width: 200px; max-height: 120px; border-radius: 8px; margin-top: .5rem; object-fit: contain; }
    </style>

    <div wire:loading.flex wire:target="saveQuestion,saveSettings,reorderQuestions,copyFromProfile" class="fb-loading">
        <div class="fb-spinner" aria-label="Saving…"></div>
    </div>

    <div class="fb-root space-y-4">
        {{-- Profile selector --}}
        <div class="fb-card">
            <div class="fb-toolbar">
                <div style="flex:1; max-width: 360px;">
                    <label class="fb-label">Profile</label>
                    <select wire:model.live="selectedProfileId" class="fb-select">
                        <option value="">Select profile…</option>
                        @foreach ($this->clientProfileOptions() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                    <button type="button" class="fb-btn fb-btn-secondary" wire:click="togglePreview">
                        {{ $showPreview ? 'Hide preview' : 'Live preview' }}
                    </button>
                    <button type="button" class="fb-btn fb-btn-secondary" wire:click="saveToLibrary" @disabled(! $selectedProfileId)>
                        Save to library
                    </button>
                </div>
            </div>
        </div>

        @if ($selectedProfileId)
            {{-- Form settings --}}
            <div class="fb-card">
                <h3 class="fb-heading">Form settings</h3>
                <div class="fb-settings-grid">
                    <div>
                        <label class="fb-label">Form title</label>
                        <input type="text" wire:model="formTitle" class="fb-input" placeholder="Form title">
                    </div>
                    <div>
                        <label class="fb-label">Email tag</label>
                        <input type="text" wire:model="formEmailTag" class="fb-input" placeholder="Email subject tag">
                    </div>
                    <div>
                        <label class="fb-label">Submission format</label>
                        <select wire:model="formSubmissionFormat" class="fb-select">
                            <option value="0">Email only</option>
                            <option value="1">Email + PDF notice</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:.5rem; justify-content:center;">
                        <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                            <input type="checkbox" wire:model="formIsEnable"> Form enabled
                        </label>
                        <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                            <input type="checkbox" wire:model="formActive"> Form active on scan page
                        </label>
                    </div>
                </div>
                <div style="margin-top:.75rem;">
                    <label class="fb-label">Recipients</label>
                    @foreach ($recipients as $index => $email)
                        <div class="fb-recipient-row" wire:key="recipient-{{ $index }}">
                            <input type="email" wire:model="recipients.{{ $index }}" class="fb-input" placeholder="email@example.com">
                            <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeRecipient({{ $index }})">×</button>
                        </div>
                    @endforeach
                    <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="addRecipient">+ Add recipient</button>
                </div>
                <div style="margin-top:1rem;">
                    <button type="button" class="fb-btn fb-btn-primary" wire:click="saveSettings">Save settings</button>
                </div>

                @if ($this->profilesWithExistingForms()->isNotEmpty())
                    <div class="fb-copy-row">
                        <div style="flex:1; min-width:200px;">
                            <label class="fb-label">Use an existing form</label>
                            <select wire:model="copyFromProfileId" class="fb-select">
                                <option value="">Select profile with form…</option>
                                @foreach ($this->profilesWithExistingForms() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="fb-btn fb-btn-secondary" wire:click="copyFromProfile" @disabled(! $copyFromProfileId)>
                            Copy form
                        </button>
                    </div>
                @endif
            </div>

            <div class="fb-layout {{ $showPreview ? 'fb-layout--preview' : '' }}">
                <div>
                    {{-- Palette --}}
                    <div class="fb-palette">
                        @foreach (['question' => 'Question Tools', 'format' => 'Format Tools', 'answer' => 'Answer Tools'] as $group => $title)
                            <div class="fb-palette-col fb-palette-col--{{ $group }}">
                                <div class="fb-palette-head">{{ $title }}</div>
                                <div class="fb-palette-body">
                                    @forelse ($paletteGroups[$group] ?? [] as $type)
                                        <button
                                            type="button"
                                            class="fb-palette-item"
                                            draggable="true"
                                            data-type-id="{{ $type->question_type_id }}"
                                            wire:click="openComposer({{ $type->question_type_id }})"
                                            wire:key="palette-{{ $group }}-{{ $type->question_type_id }}"
                                        >
                                            {{ $type->label() }}
                                        </button>
                                    @empty
                                        <span style="font-size:.75rem; opacity:.7;">No types</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Composer --}}
                    <div class="fb-composer-wrap {{ $composingTypeId ? 'is-open' : '' }}">
                    @if ($composingTypeId)
                        <div class="fb-composer" wire:key="composer-{{ $composingTypeId }}-{{ $editingQuestionId }}">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
                                <strong style="color:var(--fb-green);">
                                    {{ $editingQuestionId ? 'Edit' : 'Add' }}: {{ $typeLabel($composingTypeId) }}
                                </strong>
                                <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="cancelComposer">Cancel</button>
                            </div>

                            @unless (in_array($composingTypeId, [2, 13, 14, 20, 21, 23, 25], true))
                                <div style="margin-bottom:.75rem;">
                                    <label class="fb-label">Question / label text</label>
                                    <textarea wire:model="composerQuestionText" class="fb-textarea" rows="3"></textarea>
                                </div>
                            @else
                                <div style="margin-bottom:.75rem;">
                                    <label class="fb-label">{{ in_array($composingTypeId, [2, 25], true) ? 'Rich content (HTML supported)' : 'Display HTML / content' }}</label>
                                    <textarea
                                        wire:model="composerQuestionText"
                                        class="fb-textarea fb-rich-target"
                                        rows="6"
                                        id="fb-rich-{{ $composingTypeId }}-{{ $editingQuestionId ?? 'new' }}"
                                    ></textarea>
                                    @if (in_array($composingTypeId, [2, 25], true))
                                        <p style="font-size:.75rem;color:#6b7280;margin-top:.35rem;">Tip: you can paste HTML or use basic tags like &lt;b&gt;, &lt;p&gt;, &lt;ul&gt;.</p>
                                    @endif
                                </div>
                            @endunless

                            @if ($composingTypeId === 25)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <div>
                                        <label class="fb-label">Background colour</label>
                                        <div style="display:flex; gap:.5rem; align-items:center;">
                                            <input type="color" value="{{ str_starts_with($composerCovidBgColor ?: '#ffffff', '#') ? ($composerCovidBgColor ?: '#ffffff') : '#'.ltrim($composerCovidBgColor, '#') }}"
                                                   oninput="$wire.set('composerCovidBgColor', this.value)"
                                                   style="width:2.5rem;height:2.5rem;border:none;background:transparent;cursor:pointer;">
                                            <input type="text" wire:model="composerCovidBgColor" class="fb-input" style="flex:1;">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="fb-label">Text colour</label>
                                        <div style="display:flex; gap:.5rem; align-items:center;">
                                            <input type="color" value="{{ str_starts_with($composerCovidTextColor ?: '#222222', '#') ? ($composerCovidTextColor ?: '#222222') : '#'.ltrim($composerCovidTextColor, '#') }}"
                                                   oninput="$wire.set('composerCovidTextColor', this.value)"
                                                   style="width:2.5rem;height:2.5rem;border:none;background:transparent;cursor:pointer;">
                                            <input type="text" wire:model="composerCovidTextColor" class="fb-input" style="flex:1;">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (in_array($composingTypeId, [3, 4, 5], true))
                                <label class="fb-label">Answer options</label>
                                @foreach ($composerOptions as $oi => $opt)
                                    <div class="fb-recipient-row" wire:key="opt-{{ $oi }}">
                                        <input type="text" wire:model="composerOptions.{{ $oi }}.option_name" class="fb-input" placeholder="Option">
                                        <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeComposerOption({{ $oi }})">×</button>
                                    </div>
                                @endforeach
                                <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="addComposerOption">+ Add option</button>
                            @endif

                            @if ($composingTypeId === 6)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <div>
                                        <label class="fb-label">Scale from</label>
                                        <input type="text" wire:model="composerScaleFrom" class="fb-input">
                                    </div>
                                    <div>
                                        <label class="fb-label">Scale to</label>
                                        <input type="text" wire:model="composerScaleTo" class="fb-input">
                                    </div>
                                </div>
                            @endif

                            @if ($composingTypeId === 7)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <div>
                                        <label class="fb-label">Grid rows</label>
                                        @foreach ($composerGridRows as $ri => $row)
                                            <input type="text" wire:model="composerGridRows.{{ $ri }}.option_name" class="fb-input" style="margin-bottom:.35rem;" wire:key="grow-{{ $ri }}">
                                        @endforeach
                                        <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="addGridRow">+ Row</button>
                                    </div>
                                    <div>
                                        <label class="fb-label">Grid columns</label>
                                        @foreach ($composerGridCols as $ci => $col)
                                            <input type="text" wire:model="composerGridCols.{{ $ci }}.option_name" class="fb-input" style="margin-bottom:.35rem;" wire:key="gcol-{{ $ci }}">
                                        @endforeach
                                        <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="addGridCol">+ Column</button>
                                    </div>
                                </div>
                            @endif

                            @if (in_array($composingTypeId, [20, 21, 23], true))
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <div>
                                        <label class="fb-label">Button / link URL</label>
                                        <input type="text" wire:model="composerButtonLinkUrl" class="fb-input">
                                    </div>
                                    <div>
                                        <label class="fb-label">Button colour</label>
                                        <div style="display:flex; gap:.5rem; align-items:center;">
                                            <input type="color" value="#{{ ltrim($composerButtonColour ?: '007A01', '#') }}"
                                                   oninput="const hex=this.value.replace('#',''); $wire.set('composerButtonColour', hex)"
                                                   style="width:2.5rem;height:2.5rem;border:none;background:transparent;cursor:pointer;">
                                            <input type="text" wire:model="composerButtonColour" class="fb-input" placeholder="007A01" style="flex:1;">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (in_array($composingTypeId, [21, 23], true))
                                <div style="margin-top:.75rem;">
                                    <label class="fb-label">Document title</label>
                                    <input type="text" wire:model="composerDocTitle" class="fb-input">
                                </div>
                            @endif

                            @if ($composingTypeId === 11)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <div>
                                        <label class="fb-label">Image title</label>
                                        <input type="text" wire:model="composerImageTitle" class="fb-input">
                                    </div>
                                    <div>
                                        <label class="fb-label">Alignment</label>
                                        <select wire:model="composerImageAlign" class="fb-select">
                                            <option value="0">Left</option>
                                            <option value="1">Centre</option>
                                            <option value="2">Right</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="margin-top:.75rem;">
                                    <label class="fb-label">Upload image</label>
                                    <input type="file" wire:model="composerImageUpload" accept="image/*" class="fb-input">
                                    @if ($composerImageUrl)
                                        <img src="{{ \App\Support\PublicMediaPath::url($composerImageUrl) }}" alt="Preview" class="fb-image-preview">
                                    @endif
                                </div>
                            @endif

                            @if ($composingTypeId === 16)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerIncludeName"> Include name
                                    </label>
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerIncludeEmployer"> Include employer
                                    </label>
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerIncludeEmail"> Include email
                                    </label>
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerIncludePhone"> Include phone
                                    </label>
                                </div>
                            @endif

                            @if ($composingTypeId === 18)
                                <div class="fb-grid-2" style="margin-top:.75rem;">
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerParticipantIncludeSignature"> Include signature
                                    </label>
                                    <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                        <input type="checkbox" wire:model="composerParticipantIncludeEmployer"> Include employer
                                    </label>
                                </div>
                            @endif

                            <div class="fb-grid-2" style="margin-top:.75rem;">
                                <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                    <input type="checkbox" wire:model="composerIsMandatory"> Mandatory
                                </label>
                                <label style="display:flex; align-items:center; gap:.5rem; font-size:.875rem;">
                                    <input type="checkbox" wire:model="composerIsLogchecked"> Show in submission log
                                </label>
                            </div>

                            @if ($composerIsLogchecked)
                                <div style="margin-top:.5rem;">
                                    <label class="fb-label">Log column title</label>
                                    <input type="text" wire:model="composerLogColumntitle" class="fb-input">
                                </div>
                            @endif

                            <div style="margin-top:1rem;">
                                <button type="button" class="fb-btn fb-btn-primary" wire:click="saveQuestion">
                                    {{ $editingQuestionId ? 'Update question' : 'Save question' }}
                                </button>
                            </div>
                        </div>
                    @endif
                    </div>

                    {{-- Canvas --}}
                    <div class="fb-card fb-canvas-drop" style="margin-top:1rem;" id="fb-canvas-drop-zone">
                        <div class="fb-canvas-title">CREATE YOUR FORM HERE</div>

                        @if ($questions->isEmpty())
                            <div class="fb-empty">
                                <p>No questions yet. Pick a tool from the palette above to get started.</p>
                                <div class="fb-empty-cta">
                                    @if (($paletteGroups['question'] ?? collect())->isNotEmpty())
                                        <button type="button" class="fb-btn fb-btn-primary" wire:click="openComposer({{ $paletteGroups['question']->first()->question_type_id }})">
                                            Add your first question
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div id="fb-question-canvas" class="fb-question-list" wire:ignore.self>
                                @foreach ($questions as $question)
                                    <div
                                        class="fb-box {{ $question->boxClass() }}"
                                        data-question-id="{{ $question->question_id }}"
                                        wire:key="q-{{ $question->question_id }}"
                                    >
                                        <span class="fb-box-handle" title="Drag to reorder">⠿</span>
                                        <div class="fb-box-body">
                                            <div class="fb-box-type">{{ $question->typeName() }}</div>
                                            <div class="fb-box-text">
                                                {!! \Illuminate\Support\Str::limit(strip_tags($question->question_text), 120) !!}
                                                @if ($question->is_mandatory)
                                                    <span class="fb-badge">Mandatory</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="fb-box-actions">
                                            <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="editQuestion({{ $question->question_id }})">Edit</button>
                                            <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="deleteQuestion({{ $question->question_id }})" wire:confirm="Remove this question?">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Live preview --}}
                @if ($showPreview)
                    <div class="fb-preview">
                        <div class="fb-preview-phone">
                            <h4>{{ $formTitle ?: 'Form preview' }}</h4>
                            @forelse ($questions as $question)
                                @php $tid = (int) $question->question_type_id; @endphp
                                <div class="fb-preview-field">
                                    @if (in_array($tid, [2, 13, 14], true))
                                        <div>{!! $question->question_text !!}</div>
                                    @elseif (in_array($tid, [20, 21, 23], true))
                                        <a href="#" onclick="return false;" style="display:inline-block;background:#{{ $question->button_colour ?: '007A01' }};color:#fff;padding:.4rem .75rem;border-radius:6px;text-decoration:none;font-size:.8125rem;">
                                            {{ $question->doc_title ?: $question->question_text ?: 'Link' }}
                                        </a>
                                    @elseif ($tid === 3 && $question->options->isNotEmpty())
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        @foreach ($question->options as $opt)
                                            <label style="font-weight:400;display:block;"><input type="radio" disabled> {{ $opt->option_name }}</label>
                                        @endforeach
                                    @elseif ($tid === 4 && $question->options->isNotEmpty())
                                        <label>{{ $question->question_text }}</label>
                                        @foreach ($question->options as $opt)
                                            <label style="font-weight:400;display:block;"><input type="checkbox" disabled> {{ $opt->option_name }}</label>
                                        @endforeach
                                    @elseif ($tid === 5 && $question->options->isNotEmpty())
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        <select disabled><option>Select…</option>@foreach($question->options as $opt)<option>{{ $opt->option_name }}</option>@endforeach</select>
                                    @elseif ($tid === 6)
                                        <label>{{ $question->question_text }}</label>
                                        @php
                                            $from = (int) ($question->options->firstWhere('question_option_type_id', 1)?->option_name ?? 1);
                                            $to = (int) ($question->options->firstWhere('question_option_type_id', 2)?->option_name ?? 5);
                                        @endphp
                                        <select disabled>@for($i = $from; $i <= $to; $i++)<option>{{ $i }}</option>@endfor</select>
                                    @elseif ($tid === 11 && $question->image_url)
                                        @php
                                            $imgAlign = match ((string) ($question->image_align ?? '0')) {
                                                '1' => 'center',
                                                '2' => 'right',
                                                default => 'left',
                                            };
                                        @endphp
                                        <div style="text-align:{{ $imgAlign }};">
                                            @if ($question->image_title)
                                                <p style="font-weight:600;font-size:.8125rem;">{{ $question->image_title }}</p>
                                            @endif
                                            <img src="{{ \App\Support\PublicMediaPath::url($question->image_url) }}" alt="{{ $question->image_title ?: 'Image' }}" style="max-width:100%;border-radius:6px;">
                                        </div>
                                    @elseif ($tid === 15 || $tid === 16)
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        <textarea rows="2" disabled></textarea>
                                    @elseif ($tid === 8)
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        <input type="date" disabled>
                                    @elseif ($tid === 9)
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        <input type="time" disabled>
                                    @elseif ($tid === 17)
                                        <label>{{ $question->question_text }}</label>
                                        <input type="file" disabled>
                                    @else
                                        <label>{{ $question->question_text }}@if($question->is_mandatory)*@endif</label>
                                        <input type="text" disabled>
                                    @endif
                                </div>
                            @empty
                                <p style="font-size:.8125rem;color:#888;">Add questions to see preview.</p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="fb-card">
                <p style="color:rgb(107 114 128); font-size:.875rem;">Select a profile to build its form.</p>
            </div>
        @endif
    </div>

    @script
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        let fbCanvasSortable = null;

        function fbDestroySortable() {
            if (fbCanvasSortable) {
                fbCanvasSortable.destroy();
                fbCanvasSortable = null;
            }
            const el = document.getElementById('fb-question-canvas');
            if (el) {
                el.dataset.sortableInit = '0';
            }
        }

        function fbInitSortable() {
            const el = document.getElementById('fb-question-canvas');
            if (!el || el.dataset.sortableInit === '1') return;
            if (typeof Sortable === 'undefined') return;

            fbDestroySortable();
            el.dataset.sortableInit = '1';
            fbCanvasSortable = Sortable.create(el, {
                handle: '.fb-box-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd() {
                    const ids = [...el.querySelectorAll('[data-question-id]')].map(n => parseInt(n.dataset.questionId, 10));
                    $wire.reorderQuestions(ids);
                },
            });
        }

        function fbInitPaletteDrag() {
            document.querySelectorAll('.fb-palette-item[draggable="true"]').forEach(item => {
                if (item.dataset.dragInit === '1') return;
                item.dataset.dragInit = '1';
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', item.dataset.typeId || '');
                    e.dataTransfer.effectAllowed = 'copy';
                });
            });

            const dropZone = document.getElementById('fb-canvas-drop-zone');
            if (!dropZone || dropZone.dataset.dropInit === '1') return;
            dropZone.dataset.dropInit = '1';

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
                dropZone.classList.add('fb-canvas-drop--over');
            });
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('fb-canvas-drop--over'));
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('fb-canvas-drop--over');
                const typeId = parseInt(e.dataTransfer.getData('text/plain'), 10);
                if (typeId > 0) {
                    $wire.openComposer(typeId);
                }
            });
        }

        function fbInitAll() {
            fbInitSortable();
            fbInitPaletteDrag();
        }

        document.addEventListener('livewire:navigated', fbInitAll);

        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                requestAnimationFrame(() => {
                    fbDestroySortable();
                    fbInitAll();
                });
            });
        });

        fbInitAll();
    </script>
    @endscript
</x-filament-panels::page>
