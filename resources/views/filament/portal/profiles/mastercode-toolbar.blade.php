@php
    /** @var list<\App\Models\EquipmentType> $types */
    /** @var string|null $activeTab */
    $labelFor = static function (\App\Models\EquipmentType $type): string {
        return match ($type->slag) {
            'code' => 'URL Link',
            default => (string) $type->name,
        };
    };

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
        }
    }"
>
    <div class="sl-user-guide">
        Download the ScanLink User Guide&nbsp;
        <a href="{{ asset('images/ScanLink_User_Guide.pdf') }}" target="_blank" rel="noopener" download>
            <img src="{{ asset('images/pdf-download-icon.png') }}" alt="PDF" height="30" width="30">
        </a>
    </div>

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
                    @if (! empty($editorMode))
                        <a
                            href="{{ \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('create').'?type='.urlencode($type->slag) }}"
                            class="{{ $activeTab === $type->slag ? 'active' : '' }}"
                        >{{ $labelFor($type) }}</a>
                    @else
                        <a
                            href="{{ \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('index') }}"
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

        <div class="sl-colorcode-define" @if (! empty($hideLegend)) style="display:none" @endif>
            <div class="sl-mainbox">
                <div class="sl-colorcodebox sl-red">&nbsp;</div>
                <div class="sl-colorbox-text">Expired</div>
            </div>
            <div class="sl-mainbox">
                <div class="sl-colorcodebox sl-orange">&nbsp;</div>
                <div class="sl-colorbox-text">Expires within 30 days</div>
            </div>
            <div class="sl-mainbox">
                <div class="sl-colorcodebox sl-green">&nbsp;</div>
                <div class="sl-colorbox-text">Active</div>
            </div>
        </div>
    </div>

    @if (empty($hideActionBar))
    <div class="sl-action-bar">
        <a href="{{ \App\Filament\Portal\Pages\CumulativeAnalytics::getUrl() }}" class="sl-add-code-btn sl-analytics-btn">Multiple Code Analytics</a>
        @if (\App\Filament\Portal\Concerns\InteractsWithClientMembership::portalMembership()?->isPrimary())
            <a href="{{ \App\Filament\Portal\Pages\MultipleCodeRenewal::getUrl() }}" class="sl-add-code-btn">Renew Selected Codes</a>
        @endif
        @if ($canAddCode ?? false)
            <a href="{{ $addCodeUrl }}" class="sl-add-code-btn">Add a New Code</a>
        @endif
    </div>
    @endif

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
