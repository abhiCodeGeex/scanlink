<?php

namespace App\Filament\Pages;

use App\Models\ResellerPricing;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ResellerPricingSettings extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canManageSettings();
    }

    // Hidden from the sidebar: Code Pricing already carries the reseller per-code column,
    // and the duplicate "Reseller Pricing" entry was confusing. The page stays reachable
    // by URL if ever needed.
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Reseller Pricing';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Reseller Pricing';

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = [];

        foreach ($this->tiers() as $tier) {
            $data[$this->amountField($tier->id)] = $tier->amount;
        }

        $this->form->fill($data);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $fields = [];

        foreach ($this->tiers() as $tier) {
            $fields[] = TextInput::make($this->amountField($tier->id))
                ->label(ucwords((string) $tier->code_qty).' Codes')
                ->numeric()
                ->required();
        }

        return $schema
            ->components([
                Section::make('Update Reseller Pricing')
                    ->schema($fields),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($this->tiers() as $tier) {
            $tier->update([
                'amount' => $data[$this->amountField($tier->id)] ?? $tier->amount,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Reseller pricing updated successfully.')
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Edit Pricing')
                ->submit('save'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Reseller Pricing';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ResellerPricing>
     */
    protected function tiers()
    {
        return ResellerPricing::query()
            ->orderBy('code_qty')
            ->get();
    }

    protected function amountField(int $id): string
    {
        return "amount_{$id}";
    }
}
