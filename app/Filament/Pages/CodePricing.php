<?php

namespace App\Filament\Pages;

use App\Models\CodePrising;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CodePricing extends Page
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

    protected static ?string $navigationLabel = 'Code Pricing';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Code Pricing';

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = [];

        foreach ($this->tiers() as $tier) {
            $data[$this->amountField($tier->id)] = $tier->amount;
            $data[$this->resellerAmountField($tier->id)] = $tier->reseller_amount;
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
        $retailFields = [];
        $resellerFields = [];

        foreach ($this->tiers() as $tier) {
            $label = $tier->tierLabel();

            $retailFields[] = TextInput::make($this->amountField($tier->id))
                ->label($label)
                ->numeric()
                ->required();

            $resellerFields[] = TextInput::make($this->resellerAmountField($tier->id))
                ->label($label)
                ->numeric()
                ->required();
        }

        return $schema
            ->components([
                Section::make('Update Code Pricing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Section::make('Retail Pricing')
                                    ->schema($retailFields)
                                    ->footer([
                                        Actions::make([
                                            Action::make('saveRetail')
                                                ->label('Edit Pricing')
                                                ->action('saveRetail'),
                                        ]),
                                    ]),
                                Section::make('Reseller Pricing')
                                    ->schema($resellerFields)
                                    ->footer([
                                        Actions::make([
                                            Action::make('saveReseller')
                                                ->label('Edit Pricing')
                                                ->action('saveReseller'),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function saveRetail(): void
    {
        $data = $this->form->getState();

        foreach ($this->tiers() as $tier) {
            $tier->update([
                'amount' => $data[$this->amountField($tier->id)] ?? $tier->amount,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Pricing updated successfully.')
            ->send();
    }

    public function saveReseller(): void
    {
        $data = $this->form->getState();

        foreach ($this->tiers() as $tier) {
            $tier->update([
                'reseller_amount' => $data[$this->resellerAmountField($tier->id)] ?? $tier->reseller_amount,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Pricing updated successfully.')
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Code Pricing';
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return ['fi-page-code-pricing'];
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
            ->id('form');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, CodePrising>
     */
    protected function tiers()
    {
        return CodePrising::query()
            ->orderBy('code_min_qty')
            ->get();
    }

    protected function amountField(int $id): string
    {
        return "amount_{$id}";
    }

    protected function resellerAmountField(int $id): string
    {
        return "reseller_amount_{$id}";
    }
}
