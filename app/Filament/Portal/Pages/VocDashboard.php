<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Http\Middleware\ConfineVocCardHolder;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocDocument;
use App\Models\VocUser;
use App\Services\ProfileQrService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use App\Support\LoginEntrance;
use App\Support\VocCardLogin;

class VocDashboard extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'VOC Dashboard';

    protected static ?string $title = 'VOC Dashboard';

    protected static ?string $slug = 'voc-dashboard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = -10;

    protected string $view = 'filament.portal.pages.voc-dashboard';

    /** @var Collection<int, Profile> */
    public Collection $profiles;

    /** @var Collection<int, VocDocument> */
    public Collection $documents;

    public bool $isVocUser = false;

    /** Legacy voc_user dashboard had a Search box and a Sort By / Asc-Desc pair. */
    public string $search = '';

    public string $sortOn = 'name';

    public string $sortDirection = 'asc';

    /**
     * @var array<string, string>
     */
    public const SORT_OPTIONS = [
        'id' => 'Profile No.',
        'name' => 'Code Profile Name',
        'expiry' => 'Code Expiry Date',
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        // Two audiences, and the door decides which: a session that came in at the VOCC address
        // (or a pure card holder, who has no other door) gets the card-holder dashboard; anyone
        // else gets the client's VOC overview, which is a primary-member feature. user_type
        // cannot decide this — a card holder who is also a client member is user_type = portal.
        if (ConfineVocCardHolder::fences($user)) {
            return $user->hasVocCardHolderAccess() || $user->user_type === UserType::Voc;
        }

        if (! LoginEntrance::isClientSession($user)) {
            return false;
        }

        $member = $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first();

        return (bool) $member?->isPrimary();
    }

    public static function getNavigationGroup(): ?string
    {
        return ConfineVocCardHolder::fences(auth()->user()) ? null : 'VOC';
    }

    /**
     * Legacy's card holder menu was "VOCC Dashboard" and Logout, nothing else. In a VOCC session
     * this page is that menu; a client reaches it from their account instead.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return ConfineVocCardHolder::fences(auth()->user());
    }

    public function mount(ProfileQrService $qrService): void
    {
        $this->profiles = collect();
        $this->documents = collect();

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        // Same rule as canAccess(): the card-holder view follows the door. Keyed on user_type,
        // a client member signing in at /voclogin was shown every VOC card their client owns
        // instead of their own.
        if (ConfineVocCardHolder::fences($user)) {
            $this->isVocUser = true;
            $this->loadVocUserProfiles($user);
        } else {
            $this->loadClientVocProfiles();
        }
    }

    /**
     * @return array<string, string>
     */
    public function sortOptions(): array
    {
        return self::SORT_OPTIONS;
    }

    /** Tooltip for a document flag, e.g. "2 documents expired". */
    public function documentFlagTitle(int $count, string $state): string
    {
        return $count.' '.($count === 1 ? 'document' : 'documents').' '.$state;
    }

    /**
     * The rows to paint: the loaded set narrowed by the search box and ordered by the
     * Sort By pair. Small enough to do in memory - a card holder has a handful of profiles.
     *
     * @return Collection<int, Profile>
     */
    public function visibleProfiles(): Collection
    {
        $needle = trim($this->search);

        $rows = $needle === ''
            ? $this->profiles
            : $this->profiles->filter(function (Profile $profile) use ($needle): bool {
                // Profile No., the label as shown, and code_profile_name - the column legacy
                // searched, which is not always what displayLabel() ends up printing.
                $haystacks = [
                    (string) $profile->id,
                    $profile->displayLabel(),
                    (string) $profile->code_profile_name,
                ];

                foreach ($haystacks as $haystack) {
                    if ($haystack !== '' && stripos($haystack, $needle) !== false) {
                        return true;
                    }
                }

                return false;
            });

        $rows = $rows->sortBy(
            fn (Profile $profile): string|int => match ($this->sortOn) {
                'id' => (int) $profile->id,
                // Sort on the raw date, not the printed one; a null expiry sorts last.
                'expiry' => $profile->expired_at?->format('Y-m-d') ?? '9999-12-31',
                default => strtolower($profile->displayLabel()),
            },
            SORT_NATURAL,
            $this->sortDirection === 'desc',
        );

        return $rows->values();
    }

    /**
     * Legacy printed "N/A" for a free code that is not flagged for renewal, and d/m/Y
     * otherwise.
     */
    public function codeExpiryLabel(Profile $profile): string
    {
        if (! $profile->isExpiryManaged()) {
            return 'N/A';
        }

        return $profile->expired_at?->format('d/m/Y') ?? '—';
    }

    /** Documents already past their expiry date: legacy DATEDIFF(expiry, today) <= 0. */
    public function expiredDocumentCount(Profile $profile): int
    {
        return $this->countDocuments($profile, fn (\Illuminate\Support\Carbon $expiry, \Illuminate\Support\Carbon $today): bool => $expiry->lte($today));
    }

    /** Documents due inside 30 days but not yet expired: legacy DATEDIFF <= 30 AND > 0. */
    public function nearExpiryDocumentCount(Profile $profile): int
    {
        return $this->countDocuments(
            $profile,
            fn (\Illuminate\Support\Carbon $expiry, \Illuminate\Support\Carbon $today): bool => $expiry->gt($today)
                && $expiry->lte($today->copy()->addDays(30)),
        );
    }

    /**
     * @param  \Closure(\Illuminate\Support\Carbon, \Illuminate\Support\Carbon): bool  $matches
     */
    protected function countDocuments(Profile $profile, \Closure $matches): int
    {
        $today = \Illuminate\Support\Carbon::today();
        $count = 0;

        foreach ($profile->vocDocuments as $document) {
            $expiry = $this->documentExpiry($document);

            if ($expiry && $matches($expiry, $today)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * A usable expiry date, or null. The live dump stores "no date" as a zero date or the
     * 1970 sentinel, and neither means "expired in 1970".
     */
    protected function documentExpiry(VocDocument $document): ?\Illuminate\Support\Carbon
    {
        $raw = $document->getRawOriginal('expiry_date');

        if (blank($raw)) {
            return null;
        }

        // The value arrives as a bare date from the live dump and as a datetime from sqlite,
        // so compare the date part rather than the whole string.
        $date = substr((string) $raw, 0, 10);

        if (in_array($date, ['1970-01-01', '0000-00-00'], true)) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($date)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function profileScanUrl(Profile $profile): string
    {
        return app(ProfileQrService::class)->profileUrl($profile);
    }

    public function profileViewUrl(Profile $profile): ?string
    {
        if (! ProfileResource::canView($profile)) {
            return null;
        }

        return ProfileResource::getUrl('view', ['record' => $profile], panel: 'portal');
    }

    /**
     * Legacy vocedit: VOC secondary users get a restricted self-service editor
     * (Profile Information + Documents only) for the voc profiles linked to them.
     */
    public function profileEditUrl(Profile $profile): ?string
    {
        if (! $this->isVocUser || $profile->typeSlug() !== 'voc') {
            return null;
        }

        // Pin the panel: getUrl() otherwise resolves against whichever panel is current,
        // which is not the portal outside a portal request.
        return EditVocProfile::getUrl(['profile' => $profile->id], panel: 'portal');
    }

    /**
     * Legacy dashboard "Export List" — a .xlsx of VOC documents due within 30 days
     * (or already expired), columns Profile No. / Name / 30 Day Expiry / Expired.
     */
    public function exportDocuments(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = $this->expiringDocumentRows();

        $tmp = tempnam(sys_get_temp_dir(), 'vocdoc_').'.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer;
        $writer->openToFile($tmp);
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(['Profile No.', 'Name', '30 Day Expiry', 'Expired']));

        foreach ($rows as $row) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(array_map('strval', $row)));
        }

        $writer->close();

        return response()->download($tmp, 'document_list.xlsx')->deleteFileAfterSend();
    }

    /**
     * The rows for the expiry export: documents due within 30 days or already expired.
     *
     * @return array<int, array{0: string, 1: string, 2: string, 3: string}>
     */
    public function expiringDocumentRows(): array
    {
        $today = \Illuminate\Support\Carbon::today();
        $cutoff = $today->copy()->addDays(30);

        $rows = [];

        foreach ($this->documents as $document) {
            $expiry = $this->documentExpiry($document);

            if (! $expiry) {
                continue;
            }

            // Legacy: DATEDIFF(expiry, today) <= 30 — expiring within 30 days or already expired.
            if ($expiry->gt($cutoff)) {
                continue;
            }

            $isExpired = $expiry->lte($today);

            $rows[] = [
                (string) $document->profile_id,
                (string) ($document->name ?: 'Document'),
                $isExpired ? '' : $expiry->format('d/m/Y'),
                $isExpired ? $expiry->format('d/m/Y') : '',
            ];
        }

        return $rows;
    }

    protected function loadVocUserProfiles(User $user): void
    {
        // Legacy passed deleted:0 and type_id:12 to its listing endpoint. Following the
        // voc_users row on its own showed a deleted card, and any non-VOCC profile a row
        // happened to point at.
        $vocTypeIds = EquipmentType::query()->where('slag', 'voc')->pluck('id');

        // Only the cards this sign-in unlocked with their own password (legacy profile_id_array),
        // not every card that happens to share the email.
        $linked = VocCardLogin::profileIdsFor($user);

        if ($linked->isEmpty()) {
            return;
        }

        $this->profiles = Profile::query()
            ->whereIn('id', $linked)
            ->active()
            // Guarded: a missing type row must not blank the dashboard entirely.
            ->when($vocTypeIds->isNotEmpty(), fn ($query) => $query->whereIn('type_id', $vocTypeIds))
            ->with(['client', 'equipmentType', 'vocDocuments'])
            ->orderBy('code_profile_name')
            ->get();

        $this->documents = $this->profiles
            ->flatMap(fn (Profile $profile): Collection => $profile->vocDocuments)
            ->values();
    }

    protected function loadClientVocProfiles(): void
    {
        $client = $this->currentClient();

        if (! $client) {
            return;
        }

        $vocTypeIds = EquipmentType::query()
            ->whereIn('slag', ['voc', 'survey'])
            ->pluck('id');

        if ($vocTypeIds->isEmpty()) {
            return;
        }

        $this->profiles = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->whereIn('type_id', $vocTypeIds)
            ->with(['client', 'equipmentType', 'vocDocuments'])
            ->orderBy('name')
            ->get();

        $this->documents = VocDocument::query()
            ->whereIn('profile_id', $this->profiles->pluck('id'))
            ->orderByDesc('expiry_date')
            ->get();
    }
}
