<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Support\PricingSettings;
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

/**
 * Admin master for physical label prices + Form Builder activation price.
 * Values persist to the cached `settings` table via {@see PricingSettings} and
 * are read dynamically by the client portal (Order Label + Form Builder purchase).
 */
class OrderPricing extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static ?string $navigationLabel = 'Label & Form Pricing';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Label & Form Builder Pricing';

    protected static ?string $slug = 'order-pricing';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canManageSettings();
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Label & Form Builder Pricing';
    }

    public function mount(): void
    {
        $this->form->fill([
            PricingSettings::KEY_LABEL_SMALL => number_format(PricingSettings::labelSmall(), 2, '.', ''),
            PricingSettings::KEY_LABEL_LARGE => number_format(PricingSettings::labelLarge(), 2, '.', ''),
            PricingSettings::KEY_LABEL_POSTAGE => number_format(PricingSettings::labelPostage(), 2, '.', ''),
            PricingSettings::KEY_FORM_BUILDER => number_format(PricingSettings::formBuilder(), 2, '.', ''),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Label Pricing')
                    ->description('Prices used on the client Order Label page and order confirmation emails.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make(PricingSettings::KEY_LABEL_SMALL)
                                    ->label('50 X 40 mm label (AUD)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step('0.01')
                                    ->prefix('$')
                                    ->required(),
                                TextInput::make(PricingSettings::KEY_LABEL_LARGE)
                                    ->label('100 X 75 mm label (AUD)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step('0.01')
                                    ->prefix('$')
                                    ->required(),
                                TextInput::make(PricingSettings::KEY_LABEL_POSTAGE)
                                    ->label('Postage & Handling (AUD)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step('0.01')
                                    ->prefix('$')
                                    ->required(),
                            ]),
                    ]),
                Section::make('Form Builder Pricing')
                    ->description('One-time Form Builder activation price shown and charged in the client portal.')
                    ->schema([
                        TextInput::make(PricingSettings::KEY_FORM_BUILDER)
                            ->label('Form Builder activation (AUD)')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('$')
                            ->required(),
                    ])
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Pricing')
                                ->action('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (PricingSettings::keys() as $key) {
            PricingSettings::set($key, $data[$key] ?? null);
        }

        // Reflect the normalised, persisted values back into the form.
        $this->mount();

        Notification::make()
            ->success()
            ->title('Pricing updated successfully.')
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form');
    }
}
