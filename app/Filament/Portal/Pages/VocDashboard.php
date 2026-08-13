<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
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

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->user_type === UserType::Voc) {
            return true;
        }

        if ($user->user_type !== UserType::Portal) {
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
        return 'VOC';
    }

    public function mount(ProfileQrService $qrService): void
    {
        $this->profiles = collect();
        $this->documents = collect();

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if ($user->user_type === UserType::Voc) {
            $this->isVocUser = true;
            $this->loadVocUserProfiles($user);
        } else {
            $this->loadClientVocProfiles();
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

        return ProfileResource::getUrl('view', ['record' => $profile]);
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

        return EditVocProfile::getUrl(['profile' => $profile->id]);
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
            $raw = $document->getRawOriginal('expiry_date');

            if (blank($raw) || in_array((string) $raw, ['1970-01-01', '0000-00-00'], true)) {
                continue;
            }

            try {
                $expiry = \Illuminate\Support\Carbon::parse($raw)->startOfDay();
            } catch (\Throwable $e) {
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
        $vocUsers = VocUser::query()
            ->where('auth_user_id', $user->id)
            ->with(['profile.client', 'profile.equipmentType', 'profile.vocDocuments'])
            ->get();

        $this->profiles = $vocUsers
            ->map(fn (VocUser $vocUser): ?Profile => $vocUser->profile)
            ->filter()
            ->unique('id')
            ->values();

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
