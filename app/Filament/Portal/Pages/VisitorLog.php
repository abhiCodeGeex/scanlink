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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

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

    public string $fromDate = '';

    public string $toDate = '';

    public int $page = 1;

    public int $perPage = 20;

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
        $this->fromDate = (string) request()->query('from_date', '');
        $this->toDate = (string) request()->query('to_date', '');

        $requestedProfile = request()->integer('profile');

        if ($requestedProfile > 0) {
            $this->loadProfile($requestedProfile);
        }
    }

    public function applyDateRange(string $from = '', string $to = ''): void
    {
        $this->fromDate = trim($from);
        $this->toDate = trim($to);
        $this->page = 1;
    }

    public function clearDates(): void
    {
        $this->fromDate = '';
        $this->toDate = '';
        $this->page = 1;
    }

    /**
     * @return Builder<CollectedContact>
     */
    protected function filteredQuery(): Builder
    {
        $query = CollectedContact::query()
            ->where('id_profile', (int) $this->selectedProfileId)
            // Legacy getContactsById has no ORDER BY → natural insertion (oldest-first) order.
            ->orderBy('id');

        $from = $this->parseFilterDate($this->fromDate, startOfDay: true);
        $to = $this->parseFilterDate($this->toDate, startOfDay: false);

        // Use whereDate so filter is calendar-day based and timezone-safe.
        if ($from) {
            $query->whereDate('created_at', '>=', $from->toDateString());
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to->toDateString());
        }

        return $query;
    }

    protected function parseFilterDate(string $value, bool $startOfDay): ?Carbon
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // Strict dd/mm/yyyy only (Flatpickr outputs this). Avoid Carbon::parse()
        // which can misread day/month order and silently break the filter.
        if (! preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            return null;
        }

        $day = (int) $m[1];
        $month = (int) $m[2];
        $year = (int) $m[3];

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        $date = Carbon::create($year, $month, $day, 0, 0, 0);

        return $startOfDay ? $date->startOfDay() : $date->endOfDay();
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, min($page, $this->totalPages()));
    }

    public function nextPage(): void
    {
        $this->goToPage($this->page + 1);
    }

    public function previousPage(): void
    {
        $this->goToPage($this->page - 1);
    }

    public function totalPages(): int
    {
        if (! $this->selectedProfileId || $this->profileExpired) {
            return 1;
        }

        return max(1, (int) ceil($this->filteredQuery()->count() / $this->perPage));
    }

    /**
     * @return LengthAwarePaginator<int, CollectedContact>|null
     */
    public function paginatedVisitors(): ?LengthAwarePaginator
    {
        if (! $this->selectedProfileId || $this->profileExpired) {
            return null;
        }

        return $this->filteredQuery()->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    /**
     * Legacy exported the visitor log as .xls — produce a real .xlsx (respects date filter).
     */
    public function exportXlsx(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $tmp = tempnam(sys_get_temp_dir(), 'vlog_').'.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer;
        $writer->openToFile($tmp);
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(['ID', 'Date', 'Name', 'Surname', 'Mobile', 'Email']));

        foreach ($this->filteredQuery()->get() as $contact) {
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

    protected function loadProfile(int $profileId): void
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
    }
}
