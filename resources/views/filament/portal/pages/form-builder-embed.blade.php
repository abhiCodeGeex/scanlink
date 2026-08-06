{{-- Live pattern: Form Name / Recipients / palette / canvas inside iframe --}}
@php
    $typeLabel = fn (?int $id) => $id
        ? (\App\Models\FormBuilderQuestionType::query()->find($id)?->label() ?? "Type {$id}")
        : '';
@endphp

{{-- Single root so Livewire wire:id wraps palette + drop zone (style alone as root broke DnD lookup) --}}
<div class="fb-embed-wrap">
<style>
    .fb-embed-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    .fb-root {
        --fb-green:#008C00; --fb-orange:#ff6600; --fb-blue:#0066ff;
        padding: 8px 8px 14px;
        box-sizing: border-box;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        font-family: Arial, Helvetica, sans-serif;
        color: #333;
    }
    .fb-root *, .fb-root *::before, .fb-root *::after { box-sizing: border-box; }
    .fb-field { margin: 10px 0; }
    .fb-label { display:block; font-weight:700; color:#008901; margin-bottom:5px; font-size:13px; }
    .fb-input, .fb-textarea { width:100%; max-width:100%; box-sizing:border-box; border:1px solid #c8c8c8; border-radius:3px; padding:7px 9px; font-size:13px; background:#fff; }
    .fb-btn { display:inline-flex; align-items:center; justify-content:center; border:none; border-radius:3px; padding:7px 12px; font-size:12px; font-weight:700; cursor:pointer; }
    .fb-btn-primary { background:#008901; color:#fff; }
    .fb-btn-secondary { background:#e8e8e8; color:#333; }
    .fb-btn-danger { background:#c0392b; color:#fff; }
    .fb-btn-sm { padding:4px 8px; font-size:11px; }
    .fb-recipient-row { display:flex; gap:6px; margin-bottom:6px; min-width:0; }
    .fb-recipient-row .fb-input { flex: 1; min-width: 0; }
    .fb-recipient-meta { display:flex; justify-content:space-between; align-items:center; margin-top:4px; }
    .fb-required { color:#c00; font-weight:700; }
    .fb-add-another { background:none; border:none; color:#008901; font-weight:700; cursor:pointer; padding:0; font-size:13px; }
    .fb-participant { display:inline-block; background:#008901; color:#fff !important; text-decoration:none; font-weight:700; padding:10px 14px; border-radius:4px; font-size:13px; margin-top:4px; }
    .fb-heading { text-align:center; font-weight:700; color:#008901; margin:12px 0 8px; font-size:13px; }

    /* 3 equal columns that ALWAYS shrink inside the iframe — never clip Answer Tools */
    .fb-palette {
        display: flex !important;
        flex-direction: row !important;
        align-items: stretch !important;
        gap: 4px !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }
    .fb-palette-col {
        flex: 1 1 0 !important;
        width: 0 !important; /* force equal flex share */
        min-width: 0 !important;
        max-width: 100% !important;
        border: 1px solid !important;
        border-radius: 4px !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }
    .fb-palette-col--question { border-color:#008000 !important; }
    .fb-palette-col--format { border-color:#ff6600 !important; }
    .fb-palette-col--answer { border-color:#0066ff !important; }
    .fb-palette-head {
        color:#fff !important;
        font-weight:700 !important;
        font-size:10px !important;
        text-align:center !important;
        padding:5px 2px !important;
        line-height: 1.2 !important;
        word-break: break-word !important;
    }
    .fb-palette-col--question .fb-palette-head { background:#008000 !important; }
    .fb-palette-col--format .fb-palette-head { background:#ff6600 !important; }
    .fb-palette-col--answer .fb-palette-head { background:#0066ff !important; }
    .fb-palette-body {
        padding: 4px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 3px !important;
        max-height: 260px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        min-width: 0 !important;
    }
    .fb-palette-col--question .fb-palette-body { background:#9bff9b !important; }
    .fb-palette-col--format .fb-palette-body { background:#ffbf93 !important; }
    .fb-palette-col--answer .fb-palette-body { background:#b1d1ff !important; }
    .fb-palette-item {
        display: block !important;
        text-align: left !important;
        border: 1px solid rgba(0,0,0,.12) !important;
        border-radius: 3px !important;
        padding: 4px 5px !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        background: rgba(255,255,255,.85) !important;
        cursor: grab !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        user-select: none !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
        box-sizing: border-box !important;
    }
    .fb-palette-item:active { cursor:grabbing; }
    .fb-palette-item.is-dragging { opacity: .45; }
    .fb-palette-item.is-covid { background:#e74c3c !important; color:#fff !important; border-color:#c0392b !important; }
    .fb-canvas-title {
        display:flex; align-items:center; justify-content:center; gap:8px;
        margin:12px 0 8px;
        font-weight:800; letter-spacing:.04em; color:#008901; font-size:12px;
    }
    .fb-canvas {
        min-height:140px; background:#fff; border:2px dashed #c8c8c8; border-radius:4px; padding:8px;
        transition:border-color .15s, background .15s; width:100%; max-width:100%; overflow-x:hidden;
        box-sizing: border-box;
    }
    .fb-canvas.is-over { border-color:#008901; background:rgba(0,140,0,.08); }
    .fb-empty { min-height:120px; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px; font-weight:600; }
    .fb-question-list { display:flex; flex-direction:column; gap:6px; min-height:40px; }
    .fb-box { display:flex; align-items:flex-start; gap:8px; padding:8px; border-left:4px solid #008000; background:#f7f7f7; border-radius:4px; min-width:0; }
    .fb-box-format { border-left-color:#ff6600; }
    .fb-box-answer { border-left-color:#0066ff; }
    .fb-box-handle { cursor:grab; color:#999; user-select:none; padding-top:2px; }
    .fb-box-body { flex:1; min-width:0; }
    .fb-box-type { font-size:10px; text-transform:uppercase; opacity:.7; font-weight:700; }
    .fb-box-text { font-size:12px; font-weight:600; margin-top:2px; word-break:break-word; }
    .fb-box-actions { display:flex; gap:4px; flex-shrink:0; flex-wrap:wrap; }
    .fb-composer { margin:14px 0 10px; padding:10px; border:2px solid #008901; border-radius:6px; background:#f4fff4; width:100%; overflow-x:hidden; box-sizing:border-box; }
    .fb-composer-head { display:flex; justify-content:space-between; margin-bottom:8px; gap:8px; flex-wrap:wrap; }
    .fb-check { display:flex; align-items:center; gap:6px; font-size:12px; margin-top:8px; }
    .fb-grid-2 { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:8px; margin-top:8px; }
    .sortable-ghost { opacity:.4; }
    .fb-actions-bar { display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }
</style>

<div class="fb-root" wire:key="fb-embed-{{ $selectedProfileId }}">
    @if ($selectedProfileId)
        <div class="fb-field">
            <label class="fb-label" for="fb_form_name">Form Name</label>
            <input id="fb_form_name" type="text" class="fb-input" wire:model.blur="formTitle">
        </div>

        <div class="fb-field">
            <label class="fb-label">Recipients Email</label>
            @foreach ($recipients as $index => $email)
                <div class="fb-recipient-row" wire:key="recipient-{{ $index }}">
                    <input type="email" class="fb-input" wire:model.blur="recipients.{{ $index }}" placeholder="email@example.com">
                    @if (count($recipients) > 1)
                        <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeRecipient({{ $index }})">×</button>
                    @endif
                </div>
            @endforeach
            <div class="fb-recipient-meta">
                <span class="fb-required">*</span>
                <button type="button" class="fb-add-another" wire:click="addRecipient">Add Another</button>
            </div>
        </div>

        <div class="fb-field">
            <label class="fb-label" for="fb_email_tag">Email Tag (optional)</label>
            <input id="fb_email_tag" type="text" class="fb-input" wire:model.blur="formEmailTag">
        </div>

        <div class="fb-actions-bar">
            <button type="button" class="fb-btn fb-btn-primary" wire:click="saveSettings">Save form settings</button>
            <a href="{{ $this->participantsUrl() }}" class="fb-participant" target="_blank" rel="noopener">Add/Edit Participant List</a>
        </div>

        <div class="fb-heading">Click and drag an element on to your form</div>

        <div class="fb-palette">
            @foreach (['question' => 'Question Tools', 'format' => 'Format Tools', 'answer' => 'Answer Tools'] as $group => $title)
                <div class="fb-palette-col fb-palette-col--{{ $group }}">
                    <div class="fb-palette-head">{{ $title }}</div>
                    <div class="fb-palette-body">
                        @forelse ($paletteGroups[$group] ?? [] as $type)
                            @php $typeId = (int) $type->question_type_id; @endphp
                            <button
                                type="button"
                                class="fb-palette-item @if($typeId === 25) is-covid @endif"
                                draggable="true"
                                data-type-id="{{ $typeId }}"
                                wire:key="palette-{{ $group }}-{{ $typeId }}"
                                wire:click="quickAdd({{ $typeId }})"
                                ondragstart="window.__slFbDragging=true; window.__slFbTypeId={{ $typeId }}; try{event.dataTransfer.setData('text/plain','{{ $typeId }}');event.dataTransfer.setData('text','{{ $typeId }}');event.dataTransfer.effectAllowed='copy';}catch(e){}; this.classList.add('is-dragging');"
                                ondragend="this.classList.remove('is-dragging'); setTimeout(function(){window.__slFbDragging=false;},250);"
                            >
                                {{ $type->label() }}
                            </button>
                        @empty
                            <span style="font-size:11px;opacity:.7;">No tools</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Drop zone directly under palette so tools are easy to drag onto --}}
        <div class="fb-canvas-title">
            <span>▼</span> CREATE YOUR FORM HERE <span>▼</span>
        </div>

        <div
            class="fb-canvas"
            id="fb-canvas-drop-zone"
            x-data
            x-on:dragover.prevent="
                $el.classList.add('is-over');
                try { event.dataTransfer.dropEffect = 'copy'; } catch (e) {}
            "
            x-on:dragleave="
                if (! $el.contains(event.relatedTarget)) $el.classList.remove('is-over');
            "
            x-on:drop.prevent="
                $el.classList.remove('is-over');
                window.__slFbDragging = true;
                let typeId = 0;
                try {
                    typeId = parseInt(event.dataTransfer.getData('text/plain') || event.dataTransfer.getData('text') || '0', 10);
                } catch (e) {}
                if (! typeId) typeId = parseInt(window.__slFbTypeId || '0', 10);
                if (typeId > 0) $wire.quickAdd(typeId);
                setTimeout(() => { window.__slFbDragging = false; }, 250);
            "
        >
            @if ($questions->isEmpty())
                <div class="fb-empty">Drop a tool here</div>
            @endif
            <div id="fb-question-canvas" class="fb-question-list">
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
                                {!! \Illuminate\Support\Str::limit(strip_tags((string) $question->question_text), 100) ?: '—' !!}
                            </div>
                        </div>
                        <div class="fb-box-actions">
                            <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="editQuestion({{ $question->question_id }})">Edit</button>
                            <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="deleteQuestion({{ $question->question_id }})" wire:confirm="Remove this question?">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($composingTypeId)
            <div class="fb-composer" wire:key="composer-{{ $composingTypeId }}-{{ $editingQuestionId }}">
                <div class="fb-composer-head">
                    <strong>{{ $editingQuestionId ? 'Edit' : 'Add' }}: {{ $typeLabel($composingTypeId) }}</strong>
                    <button type="button" class="fb-btn fb-btn-secondary fb-btn-sm" wire:click="cancelComposer">Cancel</button>
                </div>

                @if ($composingTypeId === 25)
                    @include('filament.portal.pages.partials.covid-checkin-composer', [
                        'composerCovidBgColor' => $composerCovidBgColor,
                        'composerCovidTextColor' => $composerCovidTextColor,
                    ])
                @elseif (! in_array($composingTypeId, [13, 14], true))
                    <label class="fb-label">Question / label text</label>
                    <textarea wire:model="composerQuestionText" class="fb-textarea" rows="3"></textarea>
                @endif

                @if (in_array($composingTypeId, [3, 4, 5], true))
                    <label class="fb-label" style="margin-top:8px;">Answer options</label>
                    @foreach ($composerOptions as $oi => $opt)
                        <div class="fb-recipient-row" wire:key="opt-{{ $oi }}">
                            <input type="text" class="fb-input" wire:model="composerOptions.{{ $oi }}.option_name" placeholder="Option">
                            <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeComposerOption({{ $oi }})">×</button>
                        </div>
                    @endforeach
                    <button type="button" class="fb-add-another" wire:click="addComposerOption">+ Add option</button>
                @endif

                @if ($composingTypeId === 6)
                    <div class="fb-grid-2">
                        <div>
                            <label class="fb-label">Scale from</label>
                            <input type="text" class="fb-input" wire:model="composerScaleFrom">
                        </div>
                        <div>
                            <label class="fb-label">Scale to</label>
                            <input type="text" class="fb-input" wire:model="composerScaleTo">
                        </div>
                    </div>
                @endif

                @if ($composingTypeId === 7)
                    <div class="fb-grid-2">
                        <div>
                            <label class="fb-label">Grid rows</label>
                            @foreach ($composerGridRows as $ri => $row)
                                <div class="fb-recipient-row" wire:key="grow-{{ $ri }}">
                                    <input type="text" class="fb-input" wire:model="composerGridRows.{{ $ri }}.option_name">
                                    <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeGridRow({{ $ri }})">×</button>
                                </div>
                            @endforeach
                            <button type="button" class="fb-add-another" wire:click="addGridRow">+ Row</button>
                        </div>
                        <div>
                            <label class="fb-label">Grid columns</label>
                            @foreach ($composerGridCols as $ci => $col)
                                <div class="fb-recipient-row" wire:key="gcol-{{ $ci }}">
                                    <input type="text" class="fb-input" wire:model="composerGridCols.{{ $ci }}.option_name">
                                    <button type="button" class="fb-btn fb-btn-danger fb-btn-sm" wire:click="removeGridCol({{ $ci }})">×</button>
                                </div>
                            @endforeach
                            <button type="button" class="fb-add-another" wire:click="addGridCol">+ Column</button>
                        </div>
                    </div>
                @endif

                @if (in_array($composingTypeId, [20, 21], true))
                    <label class="fb-label" style="margin-top:8px;">Button / link URL</label>
                    <input type="text" class="fb-input" wire:model="composerButtonLinkUrl" placeholder="https://">
                    <label class="fb-label" style="margin-top:8px;">Button colour</label>
                    <input type="text" class="fb-input" wire:model="composerButtonColour" placeholder="007A01">
                @endif

                @unless (in_array($composingTypeId, [13, 14, 2, 11, 20, 21, 23, 25], true))
                    <label class="fb-check"><input type="checkbox" wire:model="composerIsMandatory"> Mandatory</label>
                @endunless

                <div class="fb-actions-bar">
                    <button type="button" class="fb-btn fb-btn-primary" wire:click="saveQuestion">Save</button>
                    <button type="button" class="fb-btn fb-btn-secondary" wire:click="cancelComposer">Cancel</button>
                </div>
            </div>
        @endif
    @else
        <div class="fb-empty">Save the profile first to use Form Builder.</div>
    @endif
</div>
</div>{{-- /.fb-embed-wrap --}}
