{{-- Livewire v3 requires a SINGLE root element per component. The included body has
     several top-level nodes (a <link>, <script>s, the .sl-plist <div>, a <style>). In the
     normal panel the <x-filament-panels::page> wrapper supplies the single root, but this
     embed view has none — without a wrapper Livewire refuses to hydrate the component and
     every wire:click / wire:model goes dead (all participant buttons stop working). Wrap the
     body in one root element so the component hydrates. --}}
<div>
    @include('filament.portal.pages._participant-list-body')
</div>
