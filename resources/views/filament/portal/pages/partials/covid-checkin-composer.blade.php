{{-- Legacy Covid check-in composer (formbuilder/index.php case 25) --}}
@php
    $bg = str_starts_with($composerCovidBgColor ?: '#ffffff', '#')
        ? ($composerCovidBgColor ?: '#ffffff')
        : '#'.ltrim($composerCovidBgColor, '#');
    $fg = str_starts_with($composerCovidTextColor ?: '#000000', '#')
        ? ($composerCovidTextColor ?: '#000000')
        : '#'.ltrim($composerCovidTextColor, '#');
@endphp

<div class="fb-covid-composer">
    <label class="fb-label">Header / intro text (HTML supported)</label>
    <textarea wire:model="composerQuestionText" class="fb-textarea" rows="5" placeholder="Optional message shown above the check-in fields"></textarea>

    <div class="fb-covid-colors">
        <div>
            <label class="fb-label">Text colour</label>
            <div class="fb-covid-color-row">
                <input type="color" value="{{ $fg }}" oninput="$wire.set('composerCovidTextColor', this.value)">
                <input type="text" class="fb-input" wire:model="composerCovidTextColor">
            </div>
        </div>
        <div>
            <label class="fb-label">Background color</label>
            <div class="fb-covid-color-row">
                <input type="color" value="{{ $bg }}" oninput="$wire.set('composerCovidBgColor', this.value)">
                <input type="text" class="fb-input" wire:model="composerCovidBgColor">
            </div>
        </div>
    </div>

    <label class="fb-check fb-covid-log">
        <input type="checkbox" wire:model="composerIsLogchecked">
        Record entry on Form Submission Log
    </label>

    <div class="fb-covid-preview" style="background:{{ $bg }};color:{{ $fg }};">
        <div class="fb-covid-preview-title">Fields shown on the scan page</div>
        <div class="fb-covid-field"><span>Visitor Name</span><div class="fb-covid-box"></div></div>
        <div class="fb-covid-field"><span>Visitor Phone</span><div class="fb-covid-box"></div></div>
        <div class="fb-covid-row">
            <div class="fb-covid-field"><span>Date</span><div class="fb-covid-box"></div></div>
            <div class="fb-covid-field"><span>Time</span><div class="fb-covid-box"></div></div>
        </div>
        <div class="fb-covid-field"><span>Venue Name</span><div class="fb-covid-box"></div></div>
        <div class="fb-covid-field"><span>Venue address</span><div class="fb-covid-box"></div></div>
        <div class="fb-covid-field"><span>Location Description/Type</span><div class="fb-covid-box fb-covid-select">Select ▾</div></div>
    </div>
</div>

<style>
    .fb-covid-composer { margin-top: 4px; }
    .fb-covid-hint { font-size: 11px; color: #6b7280; margin: 4px 0 10px; }
    .fb-covid-colors { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 10px 0; }
    .fb-covid-color-row { display: flex; gap: 6px; align-items: center; }
    .fb-covid-color-row input[type=color] { width: 2.4rem; height: 2.4rem; border: none; background: transparent; cursor: pointer; padding: 0; }
    .fb-covid-log { margin: 8px 0 12px; font-size: 12px; }
    .fb-covid-preview { border: 2px solid #e74c3c; border-radius: 0 0 12px 12px; padding: 12px; margin-top: 8px; }
    .fb-covid-preview-title { font-size: 11px; font-weight: 700; margin-bottom: 10px; opacity: .85; }
    .fb-covid-field { margin-bottom: 8px; }
    .fb-covid-field > span { display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; }
    .fb-covid-box { height: 28px; border: 1px solid rgba(0,0,0,.25); border-radius: 3px; background: rgba(255,255,255,.65); }
    .fb-covid-select { display: flex; align-items: center; padding: 0 8px; font-size: 12px; opacity: .7; }
    .fb-covid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
</style>
