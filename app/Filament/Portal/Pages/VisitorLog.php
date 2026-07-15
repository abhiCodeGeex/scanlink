<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use App\Models\VisitorContact;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorLog extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Visitor Log';

    protected static ?string $title = 'Visitor Log';

    protected static ?string $slug = 'visitor-log';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.portal.pages.visitor-log';

    public ?int $selectedProfileId = null;

    /** @var Collection<int, VisitorContact> */
    public Collection $visitors;

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessVisitorLog(static::portalMembership());
    }

    public function mount(): void
    {
        $this->visitors = collect();

        $requestedProfile = request()->integer('profile');
        $firstProfileId = $requestedProfile ?: $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadVisitors((int) $firstProfileId);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => $this->visitors->isNotEmpty())
                ->action(fn (): StreamedResponse => $this->exportVisitorsCsv()),
        ];
    }

    public function updatedSelectedProfileId(?int $profileId): void
    {
        if ($profileId) {
            $this->loadVisitors($profileId);
        }
    }

    public function exportVisitorsCsv(): StreamedResponse
    {
        $filename = 'visitor-log-profile-'.($this->selectedProfileId ?? 'export').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Name', 'Email', 'Mobile', 'Date']);

            foreach ($this->visitors as $visitor) {
                fputcsv($handle, [
                    $visitor->user_name,
                    $visitor->user_email,
                    $visitor->user_mobile,
                    $visitor->entry_date?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function loadVisitors(int $profileId): void
    {
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->visitors = VisitorContact::query()
            ->where('profile_id', $profileId)
            ->orderByDesc('entry_date')
            ->limit(200)
            ->get();
    }

}
