<link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>

<div class="sl-plist" wire:key="participant-list-{{ $profileId }}">
    <div class="sl-plist__head">
        <div class="sl-plist__title">Participant List</div>
        <p class="sl-plist__subtitle">Manage invitees, due dates, and notification recipients for this form.</p>
    </div>

    <div class="sl-plist__body">
        <section class="sl-plist__card">
            <div class="sl-plist__card-title">Email notifications</div>
            <p class="sl-plist__card-hint">Responses and reminders are sent to these addresses.</p>
            @foreach ($notificationEmails as $index => $email)
                <div class="sl-plist__email-row" wire:key="notify-email-{{ $index }}">
                    <input
                        type="email"
                        wire:model.live="notificationEmails.{{ $index }}"
                        class="sl-plist__input sl-plist__input--email"
                        placeholder="name@example.com"
                        autocomplete="off"
                    >
                    @if ($index > 0)
                        <button type="button" class="sl-plist__text-btn sl-plist__text-btn--danger" wire:click.prevent="removeNotificationEmail({{ $index }})">
                            Remove
                        </button>
                    @endif
                </div>
            @endforeach
            <button type="button" class="sl-plist__text-btn" wire:click.prevent="addNotificationEmail">
                + Add another email
            </button>
        </section>

        <section class="sl-plist__card">
            <div class="sl-plist__card-title">Add participant</div>
            <div class="sl-plist__add-row">
                <label class="sl-plist__field" for="sl-plist-name">
                    <span class="sl-plist__field-label">Name</span>
                    <input
                        id="sl-plist-name"
                        type="text"
                        wire:model.live="participantName"
                        maxlength="255"
                        class="sl-plist__input sl-plist__input--name"
                        placeholder="Participant name"
                        autocomplete="off"
                    >
                </label>

                <label
                    class="sl-plist__field"
                    for="sl-plist-due"
                    x-data="slPlistDatePicker({
                        property: 'participantDueDate',
                        initial: @js($participantDueDate),
                    })"
                >
                    <span class="sl-plist__field-label">Due date</span>
                    <div class="sl-plist__date-wrap">
                        <input
                            id="sl-plist-due"
                            x-ref="input"
                            type="text"
                            placeholder="DD/MM/YYYY"
                            class="sl-plist__input sl-plist__input--due"
                            autocomplete="off"
                            readonly
                            aria-label="Due date"
                        >
                        <span class="sl-plist__date-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </span>
                    </div>
                </label>

                <div class="sl-plist__field sl-plist__field--action">
                    <span class="sl-plist__field-label">&nbsp;</span>
                    <button type="button" class="sl-plist__btn sl-plist__btn--primary" wire:click="addParticipant">
                        Add
                    </button>
                </div>
            </div>
        </section>

        <section class="sl-plist__card sl-plist__card--upload">
            <div class="sl-plist__upload-main">
                <div>
                    <div class="sl-plist__card-title">Upload list</div>
                    <p class="sl-plist__card-hint">Import participants from an Excel spreadsheet.</p>
                </div>
                <div class="sl-plist__upload-controls">
                    <input
                        type="file"
                        wire:model="uploadFile"
                        accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                        class="sl-plist__file"
                    >
                    <button type="button" class="sl-plist__btn sl-plist__btn--primary" wire:click="importUpload" wire:loading.attr="disabled">
                        Upload
                    </button>
                </div>
                <span class="sl-plist__hint">.xlsx only</span>
            </div>
            <div class="sl-plist__legend">
                <span class="sl-plist__legend-item">
                    <span class="sl-plist__swatch sl-plist__swatch--overdue"></span>
                    <span class="sl-plist__legend-text">Overdue</span>
                </span>
                <span class="sl-plist__legend-item">
                    <span class="sl-plist__swatch sl-plist__swatch--received"></span>
                    <span class="sl-plist__legend-text">Received</span>
                </span>
            </div>
        </section>

        <section class="sl-plist__section--table">
            <div class="sl-plist__table-wrap">
                <table class="sl-plist__table">
                    <thead>
                        <tr>
                            <th class="sl-plist__col-name">Name</th>
                            <th class="sl-plist__col-employer">Employer / company</th>
                            <th class="sl-plist__col-due">Response due by</th>
                            <th class="sl-plist__col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($participants as $participant)
                            @php
                                $meta = $this->participantRowMeta($participant);
                            @endphp
                            <tr
                                wire:key="p-row-{{ $participant->participant_id }}"
                                class="{{ $meta['rowClass'] }}"
                                @if ($editingId === (int) $participant->participant_id) style="display:none" @endif
                            >
                                <td>{{ $participant->name }}</td>
                                <td>{{ $participant->employer_cmp ?: '—' }}</td>
                                <td>{{ $meta['dueFormatted'] }}</td>
                                <td class="sl-plist__actions">
                                    <button type="button" class="sl-plist__icon-btn" title="Edit participant" wire:click="startEdit({{ $participant->participant_id }})">
                                        <img src="{{ asset('images/edit.png') }}" width="18" alt="Edit">
                                    </button>
                                    <button
                                        type="button"
                                        class="sl-plist__icon-btn"
                                        title="Delete participant"
                                        x-on:click="window.slConfirm('Are you sure you want to delete this Participant?').then(ok => ok && $wire.deleteParticipant({{ $participant->participant_id }}))"
                                    >
                                        <img src="{{ asset('images/delete2.png') }}" width="17" alt="Delete">
                                    </button>
                                </td>
                            </tr>
                            @if ($editingId === (int) $participant->participant_id)
                                <tr class="sl-plist__edit-row" wire:key="p-edit-{{ $participant->participant_id }}">
                                    <td>
                                        <input type="text" wire:model.live="editName" maxlength="255" class="sl-plist__input" placeholder="Name" autocomplete="off">
                                    </td>
                                    <td></td>
                                    <td>
                                        <div
                                            class="sl-plist__date-wrap"
                                            x-data="slPlistDatePicker({
                                                property: 'editDueDate',
                                                initial: @js($editDueDate),
                                            })"
                                        >
                                            <input
                                                x-ref="input"
                                                type="text"
                                                placeholder="DD/MM/YYYY"
                                                class="sl-plist__input sl-plist__input--due"
                                                autocomplete="off"
                                                readonly
                                                aria-label="Edit due date"
                                            >
                                            <span class="sl-plist__date-icon" aria-hidden="true">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="sl-plist__actions">
                                        <button type="button" class="sl-plist__text-btn sl-plist__text-btn--strong" wire:click="saveEdit">Save</button>
                                        <button type="button" class="sl-plist__text-btn" wire:click="cancelEdit">Cancel</button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr class="sl-plist__empty">
                                <td colspan="4">No participants yet. Add one above or upload a list.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="sl-plist__footer">
        <button type="button" class="sl-plist__btn sl-plist__btn--primary" wire:click="downloadList">
            Download list
        </button>
        <button
            type="button"
            class="sl-plist__btn sl-plist__btn--muted"
            x-on:click="window.slConfirm('Are you sure you want to delete all participants?').then(ok => ok && $wire.clearList())"
        >
            Clear list
        </button>
        <button type="button" class="sl-plist__btn sl-plist__btn--primary" wire:click="saveAndExit">
            Save and exit
        </button>
    </div>
</div>

<style>
    .sl-plist {
        --sl-green: #008901;
        --sl-green-dark: #006b01;
        --sl-green-soft: #e8f6e8;
        --sl-border: #e5e7eb;
        --sl-muted: #6b7280;
        --sl-text: #111827;
        --sl-bg: #ffffff;
        --sl-surface: #f9fafb;
        padding: 0;
        background: var(--sl-bg);
        color: var(--sl-text);
        font: 13px/1.45 Arial, Helvetica, sans-serif;
        box-sizing: border-box;
    }
    .sl-plist *, .sl-plist *::before, .sl-plist *::after { box-sizing: border-box; }

    .sl-plist__head {
        padding: 18px 48px 12px 20px;
        border-bottom: 1px solid var(--sl-border);
        background: linear-gradient(180deg, #f7faf7 0%, #fff 100%);
    }
    .sl-plist__title {
        color: var(--sl-green);
        font-weight: 700;
        font-size: 18px;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    .sl-plist__subtitle {
        margin: 4px 0 0;
        color: var(--sl-muted);
        font-size: 12px;
    }

    .sl-plist__body {
        padding: 14px 20px 8px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .sl-plist__card {
        border: 1px solid var(--sl-border);
        border-radius: 10px;
        background: var(--sl-bg);
        padding: 14px 16px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }
    .sl-plist__card-title {
        margin: 0 0 2px;
        font-size: 13px;
        font-weight: 700;
        color: var(--sl-text);
    }
    .sl-plist__card-hint {
        margin: 0 0 10px;
        color: var(--sl-muted);
        font-size: 12px;
    }

    .sl-plist__email-row {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        column-gap: 10px;
        margin-bottom: 8px;
        max-width: 520px;
    }

    .sl-plist__input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        font: inherit;
        color: var(--sl-text);
        height: 36px;
        line-height: 34px;
        width: 100%;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .sl-plist__input:focus {
        outline: none;
        border-color: var(--sl-green);
        box-shadow: 0 0 0 3px rgba(0, 137, 1, 0.14);
    }
    .sl-plist__input--name { min-width: 200px; }
    .sl-plist__input--due {
        width: 148px;
        padding-right: 36px;
        cursor: pointer;
        background: #fff;
    }

    .sl-plist__field {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin: 0;
        min-width: 0;
    }
    .sl-plist__field-label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }
    .sl-plist__field--action { flex: 0 0 auto; }

    .sl-plist__date-wrap {
        position: relative;
        display: inline-block;
    }
    .sl-plist__date-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sl-green);
        pointer-events: none;
        display: inline-flex;
    }

    .sl-plist__text-btn {
        border: 0;
        background: transparent;
        color: var(--sl-green);
        font: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        padding: 4px 0;
        text-align: left;
    }
    .sl-plist__text-btn:hover { text-decoration: underline; }
    .sl-plist__text-btn--danger { color: #b91c1c; }
    .sl-plist__text-btn--strong { font-weight: 700; margin-right: 8px; }

    .sl-plist__add-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px 14px;
    }

    .sl-plist__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        cursor: pointer;
        color: #fff;
        font: inherit;
        font-weight: 700;
        padding: 0 16px;
        height: 36px;
        border-radius: 8px;
        white-space: nowrap;
        transition: background .15s ease, filter .15s ease;
    }
    .sl-plist__btn:hover { filter: brightness(0.97); }
    .sl-plist__btn:disabled { opacity: 0.6; cursor: default; }
    .sl-plist__btn--primary {
        background: var(--sl-green);
        background-image: linear-gradient(180deg, #009a02 0%, var(--sl-green) 100%);
    }
    .sl-plist__btn--primary:hover { background: var(--sl-green-dark); background-image: none; }
    .sl-plist__btn--muted {
        background: #e5e7eb;
        color: #374151;
    }

    .sl-plist__card--upload {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px 18px;
    }
    .sl-plist__upload-main {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 14px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .sl-plist__upload-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .sl-plist__file {
        max-width: 240px;
        font-size: 12px;
    }
    .sl-plist__hint {
        color: #b45309;
        font-size: 12px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 999px;
        padding: 3px 10px;
        white-space: nowrap;
    }

    .sl-plist__legend {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 0 0 auto;
    }
    .sl-plist__legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sl-plist__legend-text { font-size: 12px; color: var(--sl-muted); }
    .sl-plist__swatch {
        display: inline-block;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,.08);
    }
    .sl-plist__swatch--overdue { background: #fecdd3; }
    .sl-plist__swatch--received { background: #bbf7d0; }

    .sl-plist__table-wrap {
        border: 1px solid var(--sl-border);
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }
    .sl-plist__table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .sl-plist__table thead th {
        background: #f3f4f6;
        color: #374151;
        font-weight: 700;
        text-align: left;
        padding: 11px 14px;
        font-size: 12px;
        line-height: 1.35;
        vertical-align: middle;
        border-bottom: 1px solid var(--sl-border);
    }
    .sl-plist__col-name { width: 28%; }
    .sl-plist__col-employer { width: 30%; }
    .sl-plist__col-due { width: 26%; }
    .sl-plist__col-actions { width: 16%; }

    .sl-plist__table tbody td {
        padding: 11px 14px;
        border-top: 1px solid #f3f4f6;
        vertical-align: middle;
        word-break: break-word;
        background: #fff;
    }
    .sl-plist__table tbody tr.is-overdue td { background: #fff1f2; }
    .sl-plist__table tbody tr.is-received td { background: #f0fdf4; }
    .sl-plist__table tbody tr.sl-plist__empty td {
        text-align: center;
        color: var(--sl-muted);
        padding: 32px 14px;
        background: var(--sl-surface);
    }
    .sl-plist__edit-row td { background: #f8fafc !important; }

    .sl-plist__actions {
        white-space: nowrap;
        text-align: left;
    }
    .sl-plist__icon-btn {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 4px;
        vertical-align: middle;
        line-height: 1;
        border-radius: 6px;
    }
    .sl-plist__icon-btn:hover { background: #f3f4f6; }
    .sl-plist__icon-btn img { display: block; }

    .sl-plist__footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding: 14px 20px 18px;
        border-top: 1px solid var(--sl-border);
        background: var(--sl-surface);
    }

    /* Flatpickr — ScanLink green theme, above modal content */
    .flatpickr-calendar {
        z-index: 100001 !important;
        border: 1px solid var(--sl-border) !important;
        border-radius: 10px !important;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.16) !important;
        font-family: Arial, Helvetica, sans-serif !important;
    }
    .flatpickr-months { padding: 4px 4px 0 !important; }
    .flatpickr-current-month {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }
    .flatpickr-months .flatpickr-prev-month:hover svg,
    .flatpickr-months .flatpickr-next-month:hover svg { fill: var(--sl-green) !important; }
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange,
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover {
        background: var(--sl-green) !important;
        border-color: var(--sl-green) !important;
        color: #fff !important;
    }
    .flatpickr-day.today {
        border-color: var(--sl-green) !important;
    }
    .flatpickr-day:hover {
        background: var(--sl-green-soft) !important;
        border-color: var(--sl-green-soft) !important;
    }

    @media (max-width: 720px) {
        .sl-plist__head { padding: 14px 44px 10px 14px; }
        .sl-plist__body { padding: 12px 14px 8px; }
        .sl-plist__footer { padding: 12px 14px 14px; justify-content: stretch; }
        .sl-plist__card--upload { flex-direction: column; align-items: flex-start; }
        .sl-plist__input--due { width: 100%; }
        .sl-plist__date-wrap { width: 100%; }
        .sl-plist__btn { flex: 1; }
        .sl-plist__col-employer,
        .sl-plist__table thead th:nth-child(2),
        .sl-plist__table tbody td:nth-child(2) { display: none; }
    }

    html.dark .sl-plist,
    html.dark .sl-plist__table-wrap,
    html.dark .sl-plist__card,
    html.dark .sl-plist__head,
    html.dark .sl-plist__footer { background: rgb(17 24 39) !important; color: rgb(229 231 235) !important; border-color: rgb(55 65 81) !important; }
    html.dark .sl-plist__input { background: rgb(31 41 55) !important; border-color: rgb(75 85 99) !important; color: rgb(243 244 246) !important; }
    html.dark .sl-plist__table thead th { background: rgb(31 41 55) !important; color: rgb(243 244 246) !important; }
    html.dark .sl-plist__table tbody td { background: rgb(17 24 39) !important; border-color: rgb(55 65 81) !important; }
</style>

<script>
    // "Clear list" and the row delete buttons call window.slConfirm(), which is defined on the
    // parent Form Builder page but NOT inside this (embedded) participant iframe — so without a
    // fallback the call throws and the action never runs. Provide a native-confirm fallback.
    window.slConfirm = window.slConfirm || function (message) {
        return Promise.resolve(window.confirm(message));
    };

    window.slPlistDatePicker = function (config) {
        return {
            fp: null,
            property: config.property,
            initial: config.initial || '',
            init() {
                var self = this;
                if (typeof flatpickr !== 'function' || ! this.$refs.input) {
                    return;
                }

                this.fp = flatpickr(this.$refs.input, {
                    dateFormat: 'd/m/Y',
                    allowInput: false,
                    clickOpens: true,
                    disableMobile: true,
                    minDate: 'today',
                    defaultDate: this.initial || null,
                    appendTo: document.body,
                    onChange(selectedDates, dateStr) {
                        self.$wire.set(self.property, dateStr || '');
                    },
                    onClose(selectedDates, dateStr) {
                        if (dateStr) {
                            self.$wire.set(self.property, dateStr);
                        }
                    },
                });

                // Keep picker in sync when Livewire clears / updates the bound property.
                if (this.$wire && typeof this.$wire.$watch === 'function') {
                    this.$wire.$watch(this.property, (value) => {
                        if (! this.fp) {
                            return;
                        }
                        var next = value || '';
                        if (! next) {
                            if (this.fp.input.value !== '') {
                                this.fp.clear();
                            }
                            return;
                        }
                        if (this.fp.input.value !== next) {
                            this.fp.setDate(next, false);
                        }
                    });
                }
            },
            destroy() {
                if (this.fp) {
                    this.fp.destroy();
                    this.fp = null;
                }
            },
        };
    };
</script>

@if ($embed ?? false)
<script>
    (function () {
        function reportHeight() {
            try {
                var root = document.querySelector('.sl-plist');
                var cal = document.querySelector('.flatpickr-calendar.open');
                var h = root
                    ? Math.ceil(root.getBoundingClientRect().height)
                    : Math.ceil(document.documentElement.scrollHeight || 0);
                if (cal) {
                    var calBottom = Math.ceil(cal.getBoundingClientRect().bottom);
                    var rootTop = root ? Math.ceil(root.getBoundingClientRect().top) : 0;
                    h = Math.max(h, calBottom - rootTop + 16);
                }
                if (h > 0) {
                    parent.postMessage({ type: 'scanlink-participants-height', height: h }, '*');
                }
            } catch (e) {}
        }
        reportHeight();
        window.addEventListener('load', reportHeight);
        window.addEventListener('resize', reportHeight);
        document.addEventListener('click', function () { setTimeout(reportHeight, 30); });
        document.addEventListener('livewire:init', function () {
            Livewire.hook('commit', function ({ succeed }) {
                succeed(function () { setTimeout(reportHeight, 50); });
            });
        });
        if (window.Livewire) {
            try {
                Livewire.hook('commit', function ({ succeed }) {
                    succeed(function () { setTimeout(reportHeight, 50); });
                });
            } catch (e) {}
        }
        setTimeout(reportHeight, 150);
        setTimeout(reportHeight, 500);
        setTimeout(reportHeight, 1200);
    })();
</script>
@endif
