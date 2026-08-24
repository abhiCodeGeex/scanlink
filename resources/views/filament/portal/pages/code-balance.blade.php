<x-filament-panels::page>
    {{-- Each profile-type label must stay on one line (the row can still wrap between labels). --}}
    <style>
        .sl-cb__templates a { white-space: nowrap; }
    </style>
    {{-- Legacy /profile/codeBalance — listing-table layout --}}
    <div
        class="sl-cb"
        x-data="{
            alertOpen: @entangle('alertOpen'),
            alertMessage: @entangle('alertMessage'),
            closeAlert() { $wire.closeAlert(); }
        }"
    >
        <div class="sl-cb__header">
            <div class="sl-cb__header-left">
                <h1 class="sl-cb__title">Code Balance</h1>
                <div class="sl-cb__balance">
                    Your current code availability balance
                    <span class="sl-cb__balance-num">{{ $this->availabilityBalance() }}</span>
                </div>
            </div>

            <div class="sl-cb__header-right">
                <div class="sl-cb__legend">
                    <div class="sl-cb__legend-item">
                        <span class="sl-cb__swatch sl-cb__swatch--expired"></span>
                        <span>Exipred</span>
                    </div>
                    <div class="sl-cb__legend-item">
                        <span class="sl-cb__swatch sl-cb__swatch--expiring"></span>
                        <span>Expires within 30 days</span>
                    </div>
                    <div class="sl-cb__legend-item">
                        <span class="sl-cb__swatch sl-cb__swatch--active"></span>
                        <span>Active</span>
                    </div>
                </div>

                <div class="sl-cb__actions">
                    <button type="button" class="sl-cb__btn" wire:click="renewSelected">Renew Selected</button>
                    <button type="button" class="sl-cb__btn" wire:click="exitPage">Exit</button>
                </div>
            </div>
        </div>

        <form class="sl-cb__search" wire:submit.prevent="search">
            <label for="sl-cb-search">Search Code No.</label>
            <input
                id="sl-cb-search"
                type="text"
                wire:model="searchCodeNo"
                maxlength="12"
                inputmode="numeric"
                autocomplete="off"
            >
            <button type="submit" class="sl-cb__btn sl-cb__btn--search">Search</button>
            @if ($appliedSearch !== '')
                <button type="button" class="sl-cb__link-clear" wire:click="clearSearch">Clear</button>
            @endif
        </form>

        @php
            $slots = $this->paginatedSlots();
            $types = $this->templateTypes();
            $totalPages = $this->totalPages();
            $total = $this->availabilityBalance();
        @endphp

        <div class="sl-cb__table-wrap">
            <table class="listing-table sl-cb__table" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th class="sl-cb__col-code">Code No.</th>
                        <th class="sl-cb__col-expiry">Code expiry date</th>
                        <th class="sl-cb__col-renew">Renewal Select</th>
                        <th class="sl-cb__col-create">Create - Select a code profile template to activate your code</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($slots as $slot)
                        <tr class="{{ $slot->expiryStatusClass() }}" wire:key="cb-slot-{{ $slot->id }}">
                            <td class="sl-cb__col-code">{{ $slot->id }}</td>
                            <td class="sl-cb__col-expiry">
                                {{ $slot->expired_at ? $slot->expired_at->format('d/m/Y') : '—' }}
                            </td>
                            <td class="sl-cb__col-renew sl-cb__check">
                                <input
                                    type="checkbox"
                                    value="{{ $slot->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td class="sl-cb__col-create">
                                <div class="sl-cb__templates">
                                    @foreach ($types as $type)
                                        <a href="{{ $this->activateUrl((int) $slot->id, (string) $type->slag) }}">
                                            {{ \App\Support\LegacyEquipmentTypeLabels::labelFor($type) }}
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sl-cb__empty">
                                @if ($appliedSearch !== '')
                                    No unused codes match Code No. “{{ $appliedSearch }}”.
                                @else
                                    No unused code slots available. Purchase codes to add more.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($total > 0)
            <div class="sl-cb__pager">
                <span>Page</span>
                <select wire:change="goToPage(parseInt($event.target.value))">
                    @for ($p = 1; $p <= $totalPages; $p++)
                        <option value="{{ $p }}" @selected($p === $page)>{{ $p }}</option>
                    @endfor
                </select>
                <span>of {{ $totalPages }}</span>
                <button
                    type="button"
                    class="sl-cb__pager-arrow"
                    wire:click="nextPage"
                    @disabled($page >= $totalPages)
                >&gt;</button>
                <button
                    type="button"
                    class="sl-cb__pager-arrow"
                    wire:click="goToPage({{ $totalPages }})"
                    @disabled($page >= $totalPages)
                >&gt;&gt;</button>
            </div>
        @endif

        <p class="sl-cb__footnote">
            Enter the amount of codes you would like to activate and click the calculate button to display the total order value.
            <a href="{{ \App\Filament\Portal\Pages\PurchaseCodes::getUrl() }}">Purchase codes</a>
        </p>

        <div
            class="sl-alert-overlay"
            x-show="alertOpen"
            x-cloak
            x-transition.opacity.duration.150ms
            @keydown.escape.window="if (alertOpen) closeAlert()"
            x-on:click.self="closeAlert()"
            style="display: none;"
        >
            <div class="sl-alert-dialog" role="alertdialog" aria-modal="true" x-on:click.stop>
                <div class="sl-alert-dialog__head">
                    <span class="sl-alert-dialog__brand">ScanLink</span>
                    <button type="button" class="sl-alert-dialog__close" x-on:click="closeAlert()" aria-label="Close">&times;</button>
                </div>
                <div class="sl-alert-dialog__body" x-text="alertMessage"></div>
                <div class="sl-alert-dialog__foot">
                    <button type="button" class="sl-alert-dialog__ok" x-on:click="closeAlert()">OK</button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
