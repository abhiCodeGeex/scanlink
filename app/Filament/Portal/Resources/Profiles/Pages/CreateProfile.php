<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyFormBuilderSidebar;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Models\EquipmentType;
use App\Services\AnalyticsApiService;
use App\Services\ProfileDraftSlotService;
use App\Services\ProfileQrService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Schema;

class CreateProfile extends CreateRecord
{
    use HandlesDatabaseSaveFailures;
    use HasLegacyFormBuilderSidebar;
    use HasLegacyProfileEditorLayout;
    use InteractsWithClientMembership;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Add a New Code';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $typeSlag = request()->query('type');

        if (filled($typeSlag)) {
            $name = EquipmentType::query()->where('slag', $typeSlag)->value('name');

            if ($name) {
                $label = $typeSlag === 'code' ? 'URL Link' : $name;

                return 'Add a New '.$label.' Code';
            }
        }

        return 'Add a New Code';
    }

    public function mount(): void
    {
        $client = $this->currentClient();
        $member = $this->currentClientUser();
        $typeSlag = request()->query('type')
            ?? request()->input('type')
            ?? request()->route('type');

        // Old portal always opens add with a type + profile_id before Form Builder.
        // Never leave the user on a blank create page ("Save the profile first").
        if (! $client) {
            Notification::make()
                ->title('No active client account')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        if (blank($typeSlag)) {
            Notification::make()
                ->title('Choose a code type first')
                ->body('Use Add a New Code from the Master Code List (e.g. Location).')
                ->warning()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        $slot = app(ProfileDraftSlotService::class)
            ->claimForCreate((int) $client->id, (string) $typeSlag, $member?->id);

        if (! $slot) {
            Notification::make()
                ->title('Could not create profile draft')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        // Full-page redirect so the Form Builder iframe mounts on edit (live Kohana behaviour).
        $this->redirect(
            ProfileResource::getUrl(
                'edit',
                ['record' => $slot->getKey()],
                panel: 'portal',
            ),
            navigate: false,
        );
    }

    public function getView(): string
    {
        return 'filament.portal.profiles.legacy-profile-page';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $client = $this->currentClient();
        $member = $this->currentClientUser();

        $data['client_id'] = $client?->id;
        $data['user_id'] ??= $member?->id;
        $data['deleted'] = false;
        $data['update_or_not'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncProfileAssets();
        $this->syncFormBuilderSidebarSettings();

        /** @var \App\Models\Profile $profile */
        $profile = $this->record;

        try {
            app(ProfileQrService::class)->generateFor($profile);
        } catch (\Throwable) {
            // QR generation is best-effort on create.
        }

        $key = app(AnalyticsApiService::class)->registerUrl(
            app(ProfileQrService::class)->profileUrl($profile),
        );

        if ($key && Schema::hasColumn('profiles', 'analytic_key')) {
            $profile->forceFill(['analytic_key' => $key])->save();
        }
    }

    protected function getRedirectUrl(): string
    {
        return ProfileResource::getUrl('edit', ['record' => $this->record], panel: 'portal');
    }
}
