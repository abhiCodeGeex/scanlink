@php
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $wireModel = $applyStateBindingModifiers('wire:model');
    $profiles = $profiles ?? [];
    $allIds = collect($profiles)->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
@endphp

<div class="sl-edit-user-profiles" @if ($isDisabled) data-disabled="1" @endif>
    <h3 class="sl-edit-user-profiles__heading">Select Code Profile</h3>

    <div class="sl-edit-user-profiles__table-wrap">
        <table class="sl-edit-user-profiles__table">
            <thead>
                <tr>
                    <th class="sl-edit-user-profiles__col-no">Profile No.</th>
                    <th class="sl-edit-user-profiles__col-name">Code Profile Name</th>
                    <th class="sl-edit-user-profiles__col-expiry">Code Expiry Date</th>
                    <th class="sl-edit-user-profiles__col-select">
                        <label class="sl-edit-user-profiles__select-all">
                            <input
                                type="checkbox"
                                @disabled($isDisabled)
                                x-data
                                x-on:change="
                                    if (@js($isDisabled)) return;
                                    $wire.set(@js($statePath), $el.checked ? @js($allIds) : []);
                                "
                            />
                            Select
                        </label>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($profiles as $profile)
                    <tr class="sl-edit-user-profiles__row--{{ $profile['expiry_class'] }}">
                        <td>
                            @if ($profile['inactive'])
                                <img
                                    src="{{ asset('images/blocked.png') }}"
                                    alt="Inactive"
                                    title="Inactive"
                                    height="15"
                                    class="sl-edit-user-profiles__blocked"
                                />
                            @endif
                            {{ $profile['id'] }}
                        </td>
                        <td>{{ $profile['name'] }}</td>
                        <td class="sl-edit-user-profiles__expiry">{{ $profile['expiry'] }}</td>
                        <td class="sl-edit-user-profiles__select">
                            <input
                                type="checkbox"
                                class="sl-edit-user-profile-cb"
                                value="{{ $profile['id'] }}"
                                @disabled($isDisabled)
                                {{ $wireModel }}="{{ $statePath }}"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="sl-edit-user-profiles__empty">No code profiles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
