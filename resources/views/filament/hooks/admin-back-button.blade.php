{{-- Back button on internal admin pages only (edit / view / create sub-pages, etc.). --}}
@php
    $state = \App\Support\AdminRequestPath::backButtonState();
    $showBack = $state['show'];
    $resourceSlug = $state['resource'];
    $fallbackUrl = $state['fallback'];
@endphp

@if ($showBack)
    <button
        type="button"
        class="fi-btn fi-color fi-color-primary fi-btn-color-primary fi-size-md fi-ac-btn-action sl-admin-back-btn"
        style="margin-inline-end: .5rem;"
        data-fallback-url="{{ $fallbackUrl }}"
        data-resource="{{ $resourceSlug }}"
        onclick="window.scanlinkAdminBack(this)"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="fi-icon fi-size-md" style="width:1.25rem;height:1.25rem;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        <span class="fi-btn-label">Back</span>
    </button>
@endif
