<?php

namespace App\Filament\Portal\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Client;
use App\Models\CodePrising;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class PurchaseCodes extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Purchase codes';

    protected static ?string $title = 'Purchase codes';

    protected static ?string $slug = 'purchase-codes';

    protected static ?int $navigationSort = 4;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function mount(): void
    {
        $member = $this->currentClientUser();

        $this->form->fill([
            'pricing_tier_id' => CodePrising::query()->orderBy('code_min_qty')->value('id'),
            'quantity' => null,
            'reseller_code' => $member?->client_reseller_code,
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
                Section::make('Order details')
                    ->columns(2)
                    ->schema([
                        Select::make('pricing_tier_id')
                            ->label('Pricing tier')
                            ->options(fn (): array => CodePrising::query()
                                ->orderBy('code_min_qty')
                                ->get()
                                ->mapWithKeys(fn (CodePrising $tier): array => [
                                    $tier->id => sprintf(
                                        '%s — $%s / code',
                                        $tier->tierLabel(),
                                        number_format((float) $tier->amount, 2),
                                    ),
                                ])
                                ->all())
                            ->required()
                            ->live(),
                        TextInput::make('quantity')
                            ->label('Number of codes')
                            ->numeric()
                            ->integer()
                            ->required()
                            ->minValue(fn (Get $get): int => $this->tierFor($get('pricing_tier_id'))?->code_min_qty ?? 1)
                            ->maxValue(fn (Get $get): int => $this->tierFor($get('pricing_tier_id'))?->code_max_qty ?? 9999)
                            ->helperText(fn (Get $get): ?string => ($tier = $this->tierFor($get('pricing_tier_id')))
                                ? "Enter a quantity between {$tier->code_min_qty} and {$tier->code_max_qty}."
                                : null),
                        TextInput::make('reseller_code')
                            ->label('Reseller code')
                            ->placeholder('Optional')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function submitOrder(): void
    {
        $data = $this->form->getState();
        $member = $this->requireClientUser();
        $client = $this->requireClient();
        $tier = $this->tierFor($data['pricing_tier_id']);

        if (! $tier) {
            throw ValidationException::withMessages([
                'data.pricing_tier_id' => 'Please select a valid pricing tier.',
            ]);
        }

        $quantity = (int) $data['quantity'];

        if ($quantity < $tier->code_min_qty || $quantity > $tier->code_max_qty) {
            throw ValidationException::withMessages([
                'data.quantity' => "Quantity must be between {$tier->code_min_qty} and {$tier->code_max_qty}.",
            ]);
        }

        $resellerClientId = null;
        $isResellerPricing = false;
        $perCodeAmount = (float) $tier->amount;

        $resellerCode = trim((string) ($data['reseller_code'] ?? ''));

        if ($resellerCode !== '') {
            $resellerClientId = Client::query()
                ->where('reseller_code', $resellerCode)
                ->value('id');

            if ($resellerClientId) {
                $isResellerPricing = true;
                $perCodeAmount = (float) $tier->reseller_amount;
            }
        }

        $totalAmount = round($quantity * $perCodeAmount, 2);

        $order = $client->codePurchases()->create([
            'email' => $member->email ?: $client->email,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'company_name' => $member->company_name ?: $client->client_name,
            'billing_address' => $member->billing_address ?: $client->address,
            'town' => $member->town,
            'phone' => $member->phone ?: $client->telephone,
            'postal_code' => $member->postal_code,
            'no_of_codes' => $quantity,
            'per_code_amount' => $perCodeAmount,
            'total_amount' => $totalAmount,
            'status' => CodeOrderStatus::New,
            'enable' => false,
            'is_reseller_pricing_code' => $isResellerPricing,
            'reseller_client_id' => $resellerClientId,
            'free_code' => false,
            'ordered_on' => now(),
        ]);

        Notification::make()
            ->title('Code purchase submitted')
            ->body("Order #{$order->id} is pending payment.")
            ->success()
            ->send();

        $this->form->fill([
            'pricing_tier_id' => $tier->id,
            'quantity' => null,
            'reseller_code' => $member->client_reseller_code,
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('submitOrder')
                ->label('Submit order')
                ->submit('submitOrder'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Purchase Codes';
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
            ->livewireSubmitHandler('submitOrder')
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

    protected function tierFor(mixed $tierId): ?CodePrising
    {
        if (! filled($tierId)) {
            return null;
        }

        return CodePrising::query()->find($tierId);
    }
}
