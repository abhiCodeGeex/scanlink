<x-filament-panels::page>
    {{-- Legacy parity: legacy edit.php replaces the whole form with this message when
         the code is expired (message-only, no editing). --}}
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-17">
    <link rel="stylesheet" href="{{ asset('css/filament/scanlink-theme.css') }}?v=legacy-profile-17">

    <div class="scanlink-container sl-profile-editor clearfix">
        <div class="expired-profile-message"
             style="margin:40px auto;max-width:640px;padding:28px 24px;text-align:center;
                    border:1px solid #e5c7c3;border-radius:6px;background:#fbeae8;
                    color:#c0392b;font-size:16px;font-weight:600;">
            You can not perform this action on expired profile.
        </div>
    </div>
</x-filament-panels::page>
