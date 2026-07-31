<x-filament-panels::page>
    {{-- Legacy profile/index: Edit Profile → Change Password | Add sub user → Manage User --}}
    <div class="sl-edit-account">
        @if ($this->isPrimaryUser())
            <section class="sl-edit-account__profile">
                <form wire:submit.prevent="save" class="sl-edit-account__form">
                    {{ $this->form }}
                    <div class="sl-edit-account__actions">
                        <x-filament::button type="submit" color="primary" size="sm">
                            Submit
                        </x-filament::button>
                    </div>
                </form>
            </section>
        @endif

        <div @class([
            'sl-edit-account__row',
            'sl-edit-account__row--single' => ! $this->isPrimaryUser(),
        ])>
            <section class="sl-edit-account__col">
                <form wire:submit.prevent="changePassword" class="sl-edit-account__form">
                    {{ $this->getSchema('passwordForm') }}
                    <div class="sl-edit-account__actions">
                        <x-filament::button type="submit" color="primary" size="sm">
                            Submit
                        </x-filament::button>
                    </div>
                </form>
            </section>

            @if ($this->isPrimaryUser())
                <section class="sl-edit-account__col">
                    <form wire:submit.prevent="addSubUser" class="sl-edit-account__form">
                        {{ $this->getSchema('subUserForm') }}
                        <div class="sl-edit-account__actions">
                            <x-filament::button type="submit" color="primary" size="sm">
                                Submit
                            </x-filament::button>
                        </div>
                    </form>
                </section>
            @endif
        </div>

        @if ($this->isPrimaryUser())
            <section class="sl-edit-account__users">
                {{ $this->table }}
            </section>
        @endif
    </div>
</x-filament-panels::page>
