<x-filament-panels::page>
    {{-- Legacy ScanLink create/edit: form left (500px), iPhone + Form Builder right (450px) --}}
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-2">

    <div class="scanlink-container sl-profile-editor clearfix">
        {{-- DOM order matches legacy: right column first, left second --}}
        <section class="add-form-right sl-add-form-right">
            @include('filament.portal.profiles.legacy-preview-sidebar', $this->legacyPreviewData())
        </section>

        <section class="add-form-left sl-add-form-left">
            {{ $this->content }}
        </section>
    </div>
</x-filament-panels::page>
