<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\User;
use App\Support\PricingSettings;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class GlobalSettings extends Page
{
    protected static ?string $navigationLabel = 'Global Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Global Settings';

    protected static ?string $slug = 'global-settings';

    protected string $view = 'filament.pages.global-settings';

    /** @var array<string, string> title => value */
    public array $values = [];

    public string $formMessage = '';

    public string $formMessageType = '';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canManageSettings();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Global Settings';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function mount(): void
    {
        $this->reloadValues();
    }

    /**
     * @return Collection<int, Setting>
     */
    public function settingsRows(): Collection
    {
        if (! Schema::hasTable('settings')) {
            return collect();
        }

        // Label / Form Builder prices are managed on the dedicated Order Pricing page.
        return Setting::query()
            ->whereNotIn('title', PricingSettings::keys())
            ->orderBy('id')
            ->get();
    }

    public static function settingLabel(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }

    public function save(): void
    {
        $rows = $this->settingsRows();

        if ($rows->isEmpty()) {
            $this->formMessage = 'Please Enter All Fields Value';
            $this->formMessageType = 'error';

            return;
        }

        // Legacy required set from siteadmin Controller_Settings::action_index.
        $required = [
            'paypal_email',
            'youtube_username',
            'youtube_password',
            'contact_email',
            'youtube_developer_key',
            'youtube_client_id',
            'youtube_application_id',
        ];

        foreach ($required as $key) {
            if (! filled(trim((string) ($this->values[$key] ?? '')))) {
                $this->formMessage = 'Please Enter All Fields Value';
                $this->formMessageType = 'error';

                return;
            }
        }

        foreach ($rows as $row) {
            $title = (string) $row->title;
            Setting::setValue($title, (string) ($this->values[$title] ?? ''));
        }

        $this->reloadValues();
        $this->formMessage = 'Settings updated successfully..!';
        $this->formMessageType = 'success';

        Notification::make()
            ->success()
            ->title('Settings updated successfully..!')
            ->send();
    }

    protected function reloadValues(): void
    {
        $this->values = [];

        foreach ($this->settingsRows() as $row) {
            $this->values[(string) $row->title] = (string) ($row->values ?? '');
        }
    }
}
