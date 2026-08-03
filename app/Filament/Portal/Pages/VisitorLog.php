<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\CollectedContact;
use App\Models\Profile;
use App\Support\LegacyEquipmentTypeLabels;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class VisitorLog extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Visitor Log';

    protected static ?string $title = 'Visitor Log';

    protected static ?string $slug = 'visitor-log';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.portal.pages.visitor-log';

    public ?int $selectedProfileId = null;

    public ?string $profileName = null;

    /**
     * Legacy action_visitorlog redirects expired profiles away (dashboard.php:980-984).
     */
    public bool $profileExpired = false;

    /** @var Collection<int, CollectedContact> */
    public Collection $visitors;

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Visitor Log';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessVisitorLog(static::portalMembership());
    }

    public function mount(): void
    {
        $this->visitors = collect();

        $requestedProfile = request()->integer('profile');

        if ($requestedProfile > 0) {
            $this->loadVisitors($requestedProfile);
        }
    }

    /**
     * Legacy exported the visitor log as .xls — produce a real .xlsx.
     */
    public function exportXlsx(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $tmp = tempnam(sys_get_temp_dir(), 'vlog_').'.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer;
        $writer->openToFile($tmp);
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(['ID', 'Date', 'Name', 'Surname', 'Mobile', 'Email']));

        foreach ($this->visitors as $contact) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                (string) $this->selectedProfileId,
                $contact->created_at?->format('d/m/Y H:i') ?? '',
                stripslashes((string) $contact->name),
                stripslashes((string) $contact->surname),
                stripslashes((string) $contact->mobile),
                stripslashes((string) $contact->email),
            ]));
        }

        $writer->close();

        return response()
            ->download($tmp, 'data_collection-'.$this->selectedProfileId.'.xlsx')
            ->deleteFileAfterSend();
    }

    public function returnToListUrl(): string
    {
        return ProfileResource::getUrl('index');
    }

    protected function loadVisitors(int $profileId): void
    {
        $client = $this->requireClient();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : (string) ($profile->name ?? '');

        // Legacy blocks the visitor log for expired profiles.
        $this->profileExpired = $profile->isExpired();

        if ($this->profileExpired) {
            $this->visitors = collect();

            return;
        }

        // Legacy getContactsById has no ORDER BY → natural insertion (oldest-first) order.
        $this->visitors = CollectedContact::query()
            ->where('id_profile', $profileId)
            ->orderBy('id')
            ->get();
    }
}
