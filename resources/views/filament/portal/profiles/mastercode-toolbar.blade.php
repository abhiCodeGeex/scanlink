@php
    /** @var list<\App\Models\EquipmentType> $types */
    /** @var string|null $activeTab */
    $labelFor = static fn (\App\Models\EquipmentType $type): string => \App\Support\LegacyEquipmentTypeLabels::labelFor($type);

    $previewMeta = [
        'plant' => [
            'title' => 'Plant & Equipment Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a particular piece of equipment, plant, machine or vehicle, etc.',
            'image' => 'images/template_preview/plant.png',
        ],
        'location' => [
            'title' => 'Location Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a location/site.',
            'image' => 'images/template_preview/location.png',
        ],
        'asset' => [
            'title' => 'Person/Business Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a person (eg business card) or a business.',
            'image' => 'images/template_preview/asset.png',
        ],
        'product' => [
            'title' => 'Product Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a product.',
            'image' => 'images/template_preview/product.png',
        ],
        'procedure' => [
            'title' => 'Procedure Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a particular procedure (eg \'How to\').',
            'image' => 'images/template_preview/procedure.png',
        ],
        'misc' => [
            'title' => 'Misc Profile',
            'desc' => 'This is a miscellaneous code profile template that can be used for a variety of applications.',
            'image' => 'images/template_preview/misc.png',
        ],
        'code' => [
            'title' => 'URL Link Profile',
            'desc' => 'Use this code profile template for a QR Code that opens a URL of your choice (eg. Your website, etc).',
            'image' => 'images/template_preview/url_link.png',
        ],
        'survey' => [
            'title' => 'Form/Survey/Checklist Profile',
            'desc' => 'Use this code profile template for a QR Code that provides access to a form that can be completed on a mobile or tablet.',
            'image' => 'images/template_preview/survey.png',
        ],
        'exhibit' => [
            'title' => 'Exhibit Profile',
            'desc' => 'Use this code profile template for a QR Code that provides information for a particular piece of art or exhibit.',
            'image' => 'images/template_preview/exhibit.png',
        ],
        'voc' => [
            'title' => 'VOCC Profile',
            'desc' => 'Verification of Competency & Compliance',
            'image' => 'images/template_preview/voc.png',
        ],
    ];
@endphp

<div
    class="sl-mastercode-toolbar"
    x-data="{
        previewOpen: false,
        previewTitle: '',
        previewDesc: '',
        previewImage: '',
        openPreview(title, desc, image) {
            this.previewTitle = title;
            this.previewDesc = desc;
            this.previewImage = image;
            this.previewOpen = true;
        },
        closePreview() {
            this.previewOpen = false;
        },
        alertOpen: false,
        alertMessage: '',
        showAlert(msg) {
            this.alertMessage = msg || '';
            this.alertOpen = true;
        },
        closeAlert() {
            this.alertOpen = false;
        },
        selectedCount() {
            // Prefer Filament row checkboxes; fall back to any table checkbox that is checked.
            const filament = document.querySelectorAll('.fi-ta-record-checkbox:checked').length;
            if (filament > 0) {
                return filament;
            }

            return document.querySelectorAll('.fi-ta-table tbody input[type=checkbox]:checked, table tbody input[type=checkbox]:checked').length;
        }
    }"
    @sl-toolbar-alert.window="showAlert($event.detail.message || $event.detail[0] || '')"
>
    <div class="sl-user-guide">
        Download the ScanLink User Guide&nbsp;
        <a href="{{ asset('images/ScanLink_User_Guide.pdf') }}" target="_blank" rel="noopener" download>
            <img src="{{ asset('images/pdf-download-icon.png') }}" alt="PDF" height="30" width="30">
        </a>
    </div>

    <style>
        .sl-lowbalance {
            clear: both;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 8px 0 12px;
            padding: 10px 14px;
            border: 1px solid #f0c36d;
            border-left: 4px solid #e6a100;
            border-radius: 6px;
            background: #fff8e6;
            color: #7a5c00;
            font-size: 13px;
            line-height: 1.35;
        }
        .sl-lowbalance--empty {
            border-color: #e6b0b0;
            border-left-color: #cc2b2b;
            background: #fdf0f0;
            color: #8a1f1f;
        }
        .sl-lowbalance__icon { font-size: 17px; line-height: 1; }
        .sl-lowbalance__text { flex: 1; }
        .sl-lowbalance__cta {
            flex: 0 0 auto;
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            background: #008901;
            background-image: linear-gradient(to bottom, #008901 0%, #007a01 100%);
            color: #fff !important;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            white-space: nowrap;
        }
        .sl-lowbalance__cta:hover { background: #00a001; }
        html.dark .sl-lowbalance { background: #3a3320; border-color: #6b571f; color: #f0d98a; }
        html.dark .sl-lowbalance--empty { background: #3a2323; border-color: #7a3030; color: #f0b0b0; }
    </style>

    {{-- Low code-balance warning (fewer than 5 unused codes remaining). --}}
    @if (isset($codeBalance) && $codeBalance < 5)
        <div class="sl-lowbalance {{ $codeBalance <= 0 ? 'sl-lowbalance--empty' : '' }}" role="status">
            <span class="sl-lowbalance__icon" aria-hidden="true">&#9888;</span>
            <span class="sl-lowbalance__text">
                @if ($codeBalance <= 0)
                    You have <strong>no unused codes</strong> left. Purchase codes to add new code profiles.
                @else
                    Low code balance: only <strong>{{ $codeBalance }}</strong> unused {{ \Illuminate\Support\Str::plural('code', $codeBalance) }} remaining.
                @endif
            </span>
            @if (! empty($purchaseCodesUrl))
                <a href="{{ $purchaseCodesUrl }}" class="sl-lowbalance__cta">Purchase codes</a>
            @endif
        </div>
    @endif

    <div class="sl-subnav-wrap">
        <ul class="sl-sub-navigation">
            @foreach ($types as $index => $type)
                @php
                    $meta = $previewMeta[$type->slag] ?? null;
                @endphp
                <li
                    class="{{ $index === 0 ? 'first' : '' }} {{ $activeTab === $type->slag ? 'is-active' : '' }}"
                    wire:key="type-tab-{{ $type->slag }}"
                >
                    @if (! empty($editorMode) || ! empty($readonlyNav))
                        {{-- Legacy innermenu / analytics pages: tab clicks are plain navigations, no Livewire $set --}}
                        <a
                            href="{{ \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('index').'?tab='.urlencode($type->slag) }}"
                            class="{{ $activeTab === $type->slag ? 'active' : '' }}"
                        >{{ $labelFor($type) }}</a>
                    @else
                        <a
                            href="{{ \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('index').'?tab='.urlencode($type->slag) }}"
                            wire:click.prevent="$set('activeTab', @js($type->slag))"
                            class="{{ $activeTab === $type->slag ? 'active' : '' }}"
                        >{{ $labelFor($type) }}</a>
                    @endif
                    @if ($meta)
                        <button
                            type="button"
                            class="sl-preview-icon-link"
                            title="Preview"
                            @click.prevent="openPreview(@js($meta['title']), @js($meta['desc']), @js(asset($meta['image'])))"
                        >
                            <img src="{{ asset('images/preview_profile.png') }}" alt="Preview" class="sl-preview-icon">
                        </button>
                        <div class="sl-preview-tip">Preview</div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    @if (empty($hideActionBar) || empty($hideLegend))
    <div class="sl-mastercode-controls">
        <div class="sl-colorcode-define" @if (! empty($hideLegend)) style="display:none" @endif>
            @php
                $canFilterByExpiry = ! empty($canFilterByExpiry);
                $expiryStatusFilter = $expiryStatusFilter ?? null;
                $legendTag = $canFilterByExpiry ? 'button' : 'div';
            @endphp
            <{{ $legendTag }}
                @if ($canFilterByExpiry) type="button" @endif
                class="sl-mainbox {{ $canFilterByExpiry ? 'sl-mainbox--filterable' : '' }} {{ $expiryStatusFilter === 'expired' ? 'is-active' : '' }}"
                @if ($canFilterByExpiry)
                    wire:click="setExpiryStatusFilter('expired')"
                    title="Show expired codes only"
                @endif
            >
                <div class="sl-colorcodebox sl-red">&nbsp;</div>
                <div class="sl-colorbox-text">Expired</div>
            </{{ $legendTag }}>
            <{{ $legendTag }}
                @if ($canFilterByExpiry) type="button" @endif
                class="sl-mainbox {{ $canFilterByExpiry ? 'sl-mainbox--filterable' : '' }} {{ $expiryStatusFilter === 'expiring' ? 'is-active' : '' }}"
                @if ($canFilterByExpiry)
                    wire:click="setExpiryStatusFilter('expiring')"
                    title="Show codes expiring within 30 days"
                @endif
            >
                <div class="sl-colorcodebox sl-orange">&nbsp;</div>
                <div class="sl-colorbox-text">Expires within 30 days</div>
            </{{ $legendTag }}>
            <{{ $legendTag }}
                @if ($canFilterByExpiry) type="button" @endif
                class="sl-mainbox {{ $canFilterByExpiry ? 'sl-mainbox--filterable' : '' }} {{ $expiryStatusFilter === 'active' ? 'is-active' : '' }}"
                @if ($canFilterByExpiry)
                    wire:click="setExpiryStatusFilter('active')"
                    title="Show active codes only"
                @endif
            >
                <div class="sl-colorcodebox sl-green">&nbsp;</div>
                <div class="sl-colorbox-text">Active</div>
            </{{ $legendTag }}>
            @if ($canFilterByExpiry && filled($expiryStatusFilter))
                <button
                    type="button"
                    class="sl-expiry-filter-clear"
                    wire:click="clearExpiryStatusFilter"
                >Clear status filter</button>
            @endif
        </div>

        @if (empty($hideActionBar))
        <div class="sl-action-bar">
            {{-- On the Master Code List these two duplicate the working Filament table bulk
                 actions (Multiple Code Analytics / Renew Selected Codes), so they are hidden
                 there via hideBulkActions to avoid a non-working duplicate set. --}}
            @if (empty($hideBulkActions))
                @if (! empty($bindToolbarActions))
                    <button
                        type="button"
                        class="sl-add-code-btn sl-analytics-btn"
                        x-on:click="
                            if (selectedCount() < 1) {
                                showAlert('No code profiles have been selected');
                                return;
                            }
                            $wire.toolbarMultipleCodeAnalytics();
                        "
                    >Multiple Code Analytics</button>
                    @if ($canRenewCodes ?? \App\Filament\Portal\Concerns\InteractsWithClientMembership::portalMembership()?->isPrimary())
                        <button
                            type="button"
                            class="sl-add-code-btn {{ empty($hasProfiles) ? 'is-disabled' : '' }}"
                            x-on:click="
                                if (selectedCount() < 1) {
                                    showAlert('Please select the code to be renew.');
                                    return;
                                }
                                $wire.toolbarRenewSelectedCodes();
                            "
                        >Renew Selected Codes</button>
                    @endif
                @else
                    <a href="{{ \App\Filament\Portal\Pages\CumulativeAnalytics::getUrl() }}" class="sl-add-code-btn sl-analytics-btn">Multiple Code Analytics</a>
                    @if ($canRenewCodes ?? \App\Filament\Portal\Concerns\InteractsWithClientMembership::portalMembership()?->isPrimary())
                        <a href="{{ \App\Filament\Portal\Pages\MultipleCodeRenewal::getUrl() }}" class="sl-add-code-btn">Renew Selected Codes</a>
                    @endif
                @endif
            @endif
            @if ($canAddCode ?? false)
                @if (($codeBalance ?? 1) > 0)
                    <a href="{{ $addCodeUrl }}" class="sl-add-code-btn">Add a New Code</a>
                @else
                    {{-- No unused codes: show a themed popup instead of bouncing to a page. --}}
                    <a
                        href="#"
                        class="sl-add-code-btn"
                        @click.prevent="showAlert('You have no unused codes available. Please purchase codes before adding a new code profile.')"
                    >Add a New Code</a>
                @endif
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ScanLink-themed alert (matches dark subnav + green CTAs) --}}
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

    {{-- Type template preview modal (legacy Colorbox equivalent) --}}
    <div
        class="sl-preview-overlay"
        x-show="previewOpen"
        x-cloak
        :class="{ 'sl-preview-overlay--open': previewOpen }"
        @keydown.escape.window="closePreview()"
        @click.self="closePreview()"
    >
        <div class="sl-preview-modal" role="dialog" aria-modal="true">
            <button type="button" class="sl-preview-close" @click="closePreview()" aria-label="Close">&times;</button>
            <div class="sl-preview-heading" x-text="previewTitle"></div>
            <div class="sl-preview-desc" x-text="previewDesc"></div>
            <img class="sl-preview-image" :src="previewImage" :alt="previewTitle">
        </div>
    </div>
</div>
