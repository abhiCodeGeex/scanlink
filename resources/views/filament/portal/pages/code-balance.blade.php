<x-filament-panels::page>
    <style>
        .sl-stat-grid { display: grid; gap: 1rem; grid-template-columns: repeat(1, minmax(0, 1fr)); }
        @media (min-width: 768px) { .sl-stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        .sl-stat-card {
            border-radius: 12px;
            border: 1px solid rgb(229 231 235);
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.06);
        }
        .dark .sl-stat-card { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .sl-stat-label { margin: 0; font-size: .8125rem; font-weight: 600; color: rgb(107 114 128); text-transform: uppercase; letter-spacing: .04em; }
        .sl-stat-value { margin: .5rem 0 0; font-size: 2rem; font-weight: 700; color: #008C00; line-height: 1.1; }
        .sl-stat-value.is-neutral { color: rgb(17 24 39); }
        .dark .sl-stat-value.is-neutral { color: #fff; }
    </style>

    <div class="sl-stat-grid">
        <div class="sl-stat-card">
            <p class="sl-stat-label">Purchased codes</p>
            <p class="sl-stat-value">{{ number_format($purchasedCodes) }}</p>
        </div>
        <div class="sl-stat-card">
            <p class="sl-stat-label">Used profiles</p>
            <p class="sl-stat-value is-neutral">{{ number_format($usedProfiles) }}</p>
        </div>
        <div class="sl-stat-card">
            <p class="sl-stat-label">Remaining codes</p>
            <p class="sl-stat-value">{{ number_format($remainingCodes) }}</p>
        </div>
    </div>
</x-filament-panels::page>
