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
