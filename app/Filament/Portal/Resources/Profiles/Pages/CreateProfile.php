<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Models\EquipmentType;
use App\Services\AnalyticsApiService;
use App\Services\ProfileQrService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Schema;

class CreateProfile extends CreateRecord
{
    use HandlesDatabaseSaveFailures;
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
        $typeSlag = request()->query('type');

        if ($client && filled($typeSlag)) {
            $slot = app(\App\Services\ProfileDraftSlotService::class)
                ->claimForCreate($client->id, (string) $typeSlag);

            if ($slot) {
                $this->redirect(ProfileResource::getUrl('edit', ['record' => $slot]));

                return;
            }
        }

        parent::mount();

        $fill = [];

        if ($client && $member) {
            $fill['client_id'] = $client->id;
            $fill['user_id'] = $member->id;
        }

        if (filled($typeSlag)) {
            $typeId = EquipmentType::query()
                ->where('slag', $typeSlag)
                ->value('id');

            if ($typeId) {
                $fill['type_id'] = $typeId;
            }
        }

        if ($fill !== []) {
            $this->form->fill($fill);
        }
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
        return ProfileResource::getUrl('edit', ['record' => $this->record]);
    }
}
