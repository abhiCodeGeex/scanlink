@php
    use App\Filament\Portal\Concerns\InteractsWithClientMembership;
    use App\Filament\Portal\Pages\FormSubmissions;
    use App\Filament\Portal\Pages\OrderLabel;
    use App\Filament\Portal\Pages\ScanAnalytics;
    use App\Filament\Portal\Pages\VisitorLog;
    use App\Filament\Portal\Resources\Profiles\ProfileResource;
    use App\Models\Profile;

    /** @var Profile $record */
    $member = InteractsWithClientMembership::portalMembership();
    $canEdit = InteractsWithClientMembership::memberCanEditCode($member);
    $canAnalytics = InteractsWithClientMembership::memberCanAccessAnalytics($member);
    $canSubmissions = InteractsWithClientMembership::memberCanAccessFormSubmissions($member);
    $canDownload = InteractsWithClientMembership::memberCanDownload($member);
    $canLabel = InteractsWithClientMembership::memberCanOrderLabel($member);
    $canLog = InteractsWithClientMembership::memberCanAccessVisitorLog($member);
    $canDelete = InteractsWithClientMembership::memberCanDeleteCode($member);
    $expired = $record->expiryStatusClass() === 'sl-row-expired';
@endphp

<div class="sl-tools table-n">
    @if ($canEdit)
        <a
            class="Box_recycle Edit"
            href="{{ ProfileResource::getUrl('edit', ['record' => $record]) }}"
            title="Edit"
        ></a>
    @endif

    @if ($canAnalytics)
        <a
            class="Box_recycle Scanalytics"
            href="{{ ScanAnalytics::getUrl().'?profile='.$record->id }}"
            title="Scanalytics"
        ></a>
    @endif

    @if ($canSubmissions)
        <a
            class="Box_recycle Submissions"
            href="{{ FormSubmissions::getUrl().'?profile='.$record->id }}"
            title="Form Submissions"
        ></a>
    @endif

    @if ($canDownload)
        <a
            class="Box_recycle Download"
            href="{{ ProfileResource::getUrl('edit', ['record' => $record]) }}#qr"
            title="Download QR/DM Code"
            wire:click.prevent="mountTableAction('downloadQr', '{{ $record->getKey() }}')"
        ></a>
    @endif

    @if ($canLabel)
        <a
            class="Box_recycle OrderLabels"
            href="{{ OrderLabel::getUrl().'?profile='.$record->id }}"
            title="Order Labels"
        ></a>
    @endif

    @if ($canLog)
        <a
            class="Box_recycle MSExcel"
            href="{{ VisitorLog::getUrl().'?profile='.$record->id }}"
            title="View visitor log"
        ></a>
    @endif

    @if ($canDelete)
        <a
            class="Box_recycle Delete"
            href="#"
            title="Delete"
            @if (! $expired)
                onclick="return confirm('Are you sure you want to delete this code?')"
            @endif
            wire:click.prevent="mountTableAction('delete', '{{ $record->getKey() }}')"
        ></a>
    @endif
</div>
