<x-filament-panels::page>
    <style>
        .sl-card { border-radius: 12px; border: 1px solid rgb(229 231 235); background: #fff; box-shadow: 0 1px 3px rgb(0 0 0 / 0.06); overflow: hidden; }
        .dark .sl-card { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .sl-card-pad { padding: 1.25rem; }
        .sl-muted { margin: 0 0 1rem; font-size: .875rem; color: rgb(75 85 99); }
        .dark .sl-muted { color: rgb(156 163 175); }
        .sl-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .sl-table th { padding: .75rem 1rem; text-align: left; font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #008C00; background: rgb(0 140 0 / .08); }
        .dark .sl-table th { background: rgb(0 140 0 / .15); }
        .sl-table td { padding: .85rem 1rem; border-top: 1px solid rgb(229 231 235); vertical-align: middle; }
        .dark .sl-table td { border-color: rgb(55 65 81); }
        .sl-link { color: #008C00; text-decoration: none; font-weight: 600; }
        .sl-link:hover { text-decoration: underline; }
        .sl-sep { margin: 0 .4rem; color: rgb(156 163 175); }
        .sl-empty { padding: 1.5rem; font-size: .875rem; color: rgb(107 114 128); }
        .sl-doc-list { list-style: none; margin: 0; padding: 0; }
        .sl-doc-list li { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .85rem 0; border-top: 1px solid rgb(229 231 235); font-size: .875rem; }
        .dark .sl-doc-list li { border-color: rgb(55 65 81); }
        .sl-doc-list li:first-child { border-top: 0; padding-top: 0; }
        .sl-heading { margin: 0 0 1rem; font-size: 1.05rem; font-weight: 700; }
    </style>

    <div style="display:grid; gap:1.25rem;">
        <p class="sl-muted">
            @if ($isVocUser)
                Profiles linked to your VOC account.
            @else
                VOC and survey profiles for your organisation.
            @endif
        </p>

        <div class="sl-card">
            @if ($profiles->isNotEmpty())
                <div style="overflow-x:auto;">
                    <table class="sl-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Type</th>
                                <th>Links</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profiles as $profile)
                                <tr wire:key="voc-profile-{{ $profile->id }}">
                                    <td>
                                        <strong>#{{ $profile->id }}</strong>
                                        — {{ $profile->displayLabel() }}
                                    </td>
                                    <td>{{ $profile->equipmentType?->name ?? 'Profile' }}</td>
                                    <td>
                                        @if ($editUrl = $this->profileEditUrl($profile))
                                            <a href="{{ $editUrl }}" class="sl-link">Edit</a>
                                            <span class="sl-sep">|</span>
                                        @endif
                                        @if ($viewUrl = $this->profileViewUrl($profile))
                                            <a href="{{ $viewUrl }}" class="sl-link">Portal view</a>
                                            <span class="sl-sep">|</span>
                                        @endif
                                        <a href="{{ $this->profileScanUrl($profile) }}" target="_blank" rel="noopener" class="sl-link">Public scan</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="sl-empty">No VOC profiles are linked to your account.</p>
            @endif
        </div>

        @if ($documents->isNotEmpty())
            <div class="sl-card sl-card-pad">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
                    <h3 class="sl-heading" style="margin:0;">VOC documents</h3>
                    <button
                        type="button"
                        wire:click="exportDocuments"
                        wire:loading.attr="disabled"
                        style="display:inline-block;padding:6px 14px;border-radius:5px;background:#008901;background-image:linear-gradient(to bottom,#008901 0%,#007a01 100%);color:#fff;font-weight:700;font-size:12px;border:1px solid #006201;cursor:pointer;white-space:nowrap;"
                    >Export List (due ≤30 days)</button>
                </div>
                <ul class="sl-doc-list">
                    @foreach ($documents as $document)
                        <li wire:key="voc-doc-{{ $document->id }}">
                            <div>
                                <div style="font-weight:600;">{{ $document->name ?: 'Document' }}</div>
                                <div class="sl-muted" style="margin:0;">
                                    Profile #{{ $document->profile_id }}
                                    @if ($document->expiry_date)
                                        — expires {{ $document->expiry_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            </div>
                            @if ($document->file_name)
                                <a href="{{ asset('storage/'.ltrim(str_replace(['storage/', 'public/'], '', $document->file_name), '/')) }}" target="_blank" rel="noopener" class="sl-link">Download</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-filament-panels::page>
