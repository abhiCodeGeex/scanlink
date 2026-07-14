<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Services\AnalyticsApiService;
use App\Services\ProfileQrService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Schema;

class CreateProfile extends CreateRecord
{
    use InteractsWithClientMembership;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Add Profile';

    public function mount(): void
    {
        parent::mount();

        $client = $this->currentClient();
        $member = $this->currentClientUser();

        if ($client && $member) {
            $this->form->fill([
                'client_id' => $client->id,
                'user_id' => $member->id,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $client = $this->currentClient();
        $member = $this->currentClientUser();

        $data['client_id'] = $client?->id;
        $data['user_id'] ??= $member?->id;
        $data['deleted'] = false;

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
}
