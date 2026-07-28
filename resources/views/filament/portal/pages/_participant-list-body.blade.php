<div class="sl-plist" wire:key="participant-list-{{ $profileId }}">
    <div class="sl-plist__head">
        <div class="sl-plist__title">Participant List</div>
    </div>

    <div class="sl-plist__body">
        <section class="sl-plist__section">
            <div class="sl-plist__label">Send email notifications to</div>
            @foreach ($notificationEmails as $index => $email)
                <div class="sl-plist__email-row" wire:key="notify-email-{{ $index }}">
                    <input
                        type="email"
                        wire:model.live="notificationEmails.{{ $index }}"
                        class="sl-plist__input sl-plist__input--email"
                        autocomplete="off"
                    >
                    @if ($index > 0)
                        <a href="javascript:;" class="sl-plist__remove" wire:click.prevent="removeNotificationEmail({{ $index }})">Remove</a>
                    @endif
                </div>
            @endforeach
            <div class="sl-plist__add-another">
                <a href="javascript:;" wire:click.prevent="addNotificationEmail">Add Another</a>
            </div>
        </section>

        <section class="sl-plist__section">
            <div class="sl-plist__label">Add Participant</div>
            <div class="sl-plist__add-row">
                <label class="sl-plist__inline-field" for="sl-plist-name">
                    <span>Name:</span>
                    <input id="sl-plist-name" type="text" wire:model.live="participantName" maxlength="255" class="sl-plist__input sl-plist__input--name">
                </label>
                <label class="sl-plist__inline-field" for="sl-plist-due">
                    <span>Due Date:</span>
                    <input
                        id="sl-plist-due"
                        type="text"
                        wire:model.live="participantDueDate"
                        placeholder="DD/MM/YYYY"
                        class="sl-plist__input sl-plist__input--due"
                    >
                </label>
                <button type="button" class="sl-plist__btn sl-plist__btn--green" wire:click="addParticipant">Add</button>
            </div>
        </section>

        <section class="sl-plist__section sl-plist__section--upload">
            <div class="sl-plist__upload-main">
                <span class="sl-plist__upload-label">Upload List</span>
                <input type="file" wire:model="uploadFile" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="sl-plist__file">
                <button type="button" class="sl-plist__btn sl-plist__btn--green" wire:click="importUpload" wire:loading.attr="disabled">
                    Upload
                </button>
                <span class="sl-plist__hint">(Upload .xlsx file only)</span>
            </div>
            <div class="sl-plist__legend">
                <span class="sl-plist__legend-item">
                    <span class="sl-plist__legend-text">OverDue</span>
                    <span class="sl-plist__swatch sl-plist__swatch--overdue"></span>
                </span>
                <span class="sl-plist__legend-item">
                    <span class="sl-plist__legend-text">Received</span>
                    <span class="sl-plist__swatch sl-plist__swatch--received"></span>
                </span>
            </div>
        </section>

        <section class="sl-plist__section sl-plist__section--table">
            <div class="sl-plist__table-wrap">
                <table class="sl-plist__table">
                    <thead>
                        <tr>
                            <th class="sl-plist__col-name">Name</th>
                            <th class="sl-plist__col-employer">Employer/Company</th>
                            <th class="sl-plist__col-due">Response Due By<br><span class="sl-plist__th-sub">DD/MM/YYYY</span></th>
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
                                <td>{{ $participant->employer_cmp }}</td>
                                <td>{{ $meta['dueFormatted'] }}</td>
                                <td class="sl-plist__actions">
                                    <button type="button" class="sl-plist__icon-btn" title="Edit Participant" wire:click="startEdit({{ $participant->participant_id }})">
                                        <img src="{{ asset('images/edit.png') }}" width="19" alt="Edit">
                                    </button>
                                    <button
                                        type="button"
                                        class="sl-plist__icon-btn"
                                        title="Delete Participant"
                                        wire:click="deleteParticipant({{ $participant->participant_id }})"
                                        wire:confirm="Are you sure you want to delete this Participant?"
                                    >
                                        <img src="{{ asset('images/delete2.png') }}" width="18" alt="Delete">
                                    </button>
                                </td>
                            </tr>
                            @if ($editingId === (int) $participant->participant_id)
                                <tr class="sl-plist__edit-row" wire:key="p-edit-{{ $participant->participant_id }}">
                                    <td>
                                        <input type="text" wire:model.live="editName" maxlength="255" class="sl-plist__input sl-plist__input--name">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" wire:model.live="editDueDate" placeholder="DD/MM/YYYY" class="sl-plist__input sl-plist__input--due">
                                    </td>
                                    <td class="sl-plist__actions">
                                        <button type="button" class="sl-plist__link-btn" wire:click="saveEdit">Save</button>
                                        <button type="button" class="sl-plist__link-btn" wire:click="cancelEdit">Cancel</button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr class="sl-plist__empty">
                                <td colspan="4">No Participant found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="sl-plist__footer">
        <button type="button" class="sl-plist__btn sl-plist__btn--green sl-plist__btn--wide" wire:click="downloadList">
            Download List
        </button>
        <button
            type="button"
            class="sl-plist__btn sl-plist__btn--gray sl-plist__btn--wide"
            wire:click="clearList"
            wire:confirm="Are you sure you want to delete all participants?"
        >
            Clear List
        </button>
        <button type="button" class="sl-plist__btn sl-plist__btn--green sl-plist__btn--wide" wire:click="saveAndExit">
            Save and exit
        </button>
    </div>
</div>

<style>
    .sl-plist {
        padding: 0;
        background: #fff;
        color: #333;
        font: 13px/1.45 Arial, Helvetica, sans-serif;
        box-sizing: border-box;
    }
    .sl-plist *, .sl-plist *::before, .sl-plist *::after { box-sizing: border-box; }

    .sl-plist__head {
        padding: 16px 44px 0 20px;
    }
    .sl-plist__title {
        color: #008901;
        font-weight: 700;
        font-size: 18px;
        margin: 0;
        line-height: 1.2;
    }

    .sl-plist__body {
        padding: 14px 20px 4px;
    }

    .sl-plist__section { margin: 0 0 16px; }
    .sl-plist__section--table { margin-bottom: 0; }

    .sl-plist__label {
        margin: 0 0 6px;
        font-weight: 400;
        color: #333;
    }

    .sl-plist__email-row {
        display: grid;
        grid-template-columns: 1fr 64px;
        align-items: center;
        column-gap: 10px;
        margin-bottom: 6px;
        max-width: 480px;
    }
    .sl-plist__input {
        border: 1px solid #bdbdbd;
        border-radius: 2px;
        padding: 0 10px;
        background: #fff;
        font: inherit;
        color: #333;
        height: 32px;
        line-height: 30px;
        vertical-align: middle;
    }
    .sl-plist__input:focus {
        outline: none;
        border-color: #008901;
        box-shadow: 0 0 0 2px rgba(0, 137, 1, 0.12);
    }
    .sl-plist__input--email { width: 100%; min-width: 0; grid-column: 1; }
    .sl-plist__email-row .sl-plist__remove { grid-column: 2; justify-self: start; }
    .sl-plist__input--name { width: 200px; max-width: 100%; }
    .sl-plist__input--due { width: 110px; }

    .sl-plist__remove {
        color: #c00;
        font-size: 12px;
        text-decoration: none;
        white-space: nowrap;
    }
    .sl-plist__remove:hover { text-decoration: underline; }

    .sl-plist__add-another {
        max-width: 480px;
        text-align: right;
        margin-top: 2px;
    }
    .sl-plist__add-another a {
        color: #666;
        text-decoration: none;
        font-size: 12px;
    }
    .sl-plist__add-another a:hover { text-decoration: underline; color: #008901; }

    .sl-plist__add-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 16px;
    }
    .sl-plist__inline-field {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-weight: 400;
        color: #333;
        white-space: nowrap;
    }
    .sl-plist__inline-field span { flex: 0 0 auto; }

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
        height: 32px;
        line-height: 32px;
        border-radius: 2px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .sl-plist__btn:hover { filter: brightness(0.96); }
    .sl-plist__btn:disabled { opacity: 0.6; cursor: default; }
    .sl-plist__btn--green { background: #008901; }
    .sl-plist__btn--gray { background: #888; }
    .sl-plist__btn--wide { min-width: 140px; width: 140px; }

    .sl-plist__section--upload {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px 18px;
        padding: 4px 0 2px;
        border-top: 1px solid #ececec;
        border-bottom: 1px solid #ececec;
        margin-bottom: 14px;
        min-height: 48px;
    }
    .sl-plist__upload-main {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1 1 auto;
    }
    .sl-plist__upload-label {
        font-weight: 400;
        white-space: nowrap;
        line-height: 32px;
    }
    .sl-plist__file {
        max-width: 230px;
        font-size: 12px;
        height: 32px;
        line-height: 32px;
    }
    .sl-plist__hint {
        color: #c60;
        font-size: 12px;
        white-space: nowrap;
        line-height: 32px;
    }

    .sl-plist__legend {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 0 0 auto;
        margin-left: auto;
    }
    .sl-plist__legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sl-plist__legend-text { font-size: 12px; color: #555; }
    .sl-plist__swatch {
        display: inline-block;
        width: 36px;
        height: 14px;
        border: 1px solid #bbb;
    }
    .sl-plist__swatch--overdue { background: #ffc0cb; }
    .sl-plist__swatch--received { background: #c9ffc9; }

    .sl-plist__table-wrap {
        border: 1px solid #222;
        overflow: hidden;
        background: #fff;
    }
    .sl-plist__table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .sl-plist__table thead th {
        background: #111;
        color: #fff;
        font-weight: 700;
        text-align: left;
        padding: 9px 12px;
        font-size: 12px;
        line-height: 1.35;
        vertical-align: middle;
        border: 0;
    }
    .sl-plist__th-sub {
        display: block;
        font-weight: 400;
        font-size: 11px;
        opacity: 0.9;
        margin-top: 1px;
    }
    .sl-plist__col-name { width: 28%; }
    .sl-plist__col-employer { width: 30%; }
    .sl-plist__col-due { width: 26%; }
    .sl-plist__col-actions { width: 16%; }

    .sl-plist__table tbody td {
        padding: 9px 12px;
        border-top: 1px solid #e5e5e5;
        vertical-align: middle;
        word-break: break-word;
        background: #fff;
    }
    .sl-plist__table tbody tr.is-overdue td { background: #ffc0cb; }
    .sl-plist__table tbody tr.is-received td { background: #c9ffc9; }
    .sl-plist__table tbody tr.sl-plist__empty td {
        text-align: center;
        color: #777;
        padding: 28px 12px;
        background: #fff;
    }
    .sl-plist__edit-row td { background: #f7f7f7 !important; }

    .sl-plist__actions {
        white-space: nowrap;
        text-align: left;
    }
    .sl-plist__icon-btn {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 2px 4px;
        vertical-align: middle;
        line-height: 1;
    }
    .sl-plist__icon-btn img {
        display: block;
    }
    .sl-plist__link-btn {
        border: 0;
        background: transparent;
        color: #008901;
        font-weight: 700;
        cursor: pointer;
        padding: 0 8px 0 0;
        font-size: 12px;
    }
    .sl-plist__link-btn:hover { text-decoration: underline; }

    .sl-plist__footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding: 14px 20px 18px;
        margin-top: 4px;
    }

    @media (max-width: 720px) {
        .sl-plist__head { padding: 14px 44px 0 14px; }
        .sl-plist__body { padding: 12px 14px 8px; }
        .sl-plist__footer { padding: 12px 14px 14px; justify-content: stretch; }
        .sl-plist__section--upload { flex-direction: column; align-items: flex-start; }
        .sl-plist__legend { margin-left: 0; }
        .sl-plist__input--name,
        .sl-plist__input--due,
        .sl-plist__input--email { width: 100%; max-width: none; }
        .sl-plist__email-row { max-width: none; }
        .sl-plist__add-another { max-width: none; }
        .sl-plist__inline-field { width: 100%; }
        .sl-plist__inline-field .sl-plist__input { flex: 1; }
        .sl-plist__btn--wide { flex: 1; min-width: 0; width: auto; }
        .sl-plist__col-employer { display: none; }
        .sl-plist__table thead th:nth-child(2),
        .sl-plist__table tbody td:nth-child(2) { display: none; }
    }
</style>

@if ($embed ?? false)
<script>
    (function () {
        function reportHeight() {
            try {
                var root = document.querySelector('.sl-plist');
                var h = root
                    ? Math.ceil(root.getBoundingClientRect().height)
                    : Math.ceil(document.documentElement.scrollHeight || 0);
                if (h > 0) {
                    parent.postMessage({ type: 'scanlink-participants-height', height: h }, '*');
                }
            } catch (e) {}
        }
        reportHeight();
        window.addEventListener('load', reportHeight);
        window.addEventListener('resize', reportHeight);
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
