<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use App\Services\LabelOrderService;
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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class OrderLabel extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Order Labels';

    protected static ?string $title = 'Order Physical Labels';

    protected static ?string $slug = 'order-labels';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.order-label';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }

    public function mount(): void
    {
        $this->form->fill([
            'profile_id' => $this->clientProfileOptions()->keys()->first(),
            'size' => 'small',
            'quantity' => 1,
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
                Section::make('Label order')
                    ->columns(2)
                    ->schema([
                        Select::make('profile_id')
                            ->label('Profile')
                            ->options(fn (): array => $this->clientProfileOptions()->all())
                            ->required(),
                        Select::make('size')
                            ->label('Label size')
                            ->options([
                                'small' => 'Small ($'.number_format((float) config('scanlink.label_price_small'), 2).')',
                                'large' => 'Large ($'.number_format((float) config('scanlink.label_price_large'), 2).')',
                            ])
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->integer()
                            ->required()
                            ->minValue(1)
                            ->maxValue(500),
                    ]),
            ]);
    }

    public function submitOrder(LabelOrderService $labelOrders): void
    {
        $data = $this->form->getState();
        $client = $this->requireClient();
        $member = $this->currentClientUser();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($data['profile_id']);

        $order = $labelOrders->createLabelOrder(
            $profile,
            (string) $data['size'],
            (int) $data['quantity'],
            $member,
        );

        Notification::make()
            ->title('Label order created')
            ->body("Order #{$order->id} is pending payment.")
            ->success()
            ->send();

        $this->form->fill([
            'profile_id' => $profile->id,
            'size' => $data['size'],
            'quantity' => 1,
        ]);
    }

    /**
     * @return Collection<int|string, string>
     */
    protected function clientProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('submitOrder')
                ->label('Create order')
                ->submit('submitOrder'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Order Physical Labels';
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
}
