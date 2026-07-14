<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
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

class EditAccount extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'My Account';

    protected static ?string $title = 'My Account';

    protected static ?string $slug = 'account';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->user_type !== UserType::Portal) {
            return false;
        }

        return $user->clientMemberships()
            ->where('status', true)
            ->exists();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Account';
    }

    public function mount(): void
    {
        $member = $this->currentClientUser();
        $client = $this->currentClient();

        if (! $member || ! $client) {
            return;
        }

        $this->form->fill([
            'client_name' => $client->client_name,
            'address' => $client->address,
            'telephone' => $client->telephone,
            'contact_person' => $client->contact_person,
            'email' => $client->email,
            'url' => $client->url,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'company_name' => $member->company_name,
            'billing_address' => $member->billing_address,
            'town' => $member->town,
            'phone' => $member->phone,
            'postal_code' => $member->postal_code,
            'member_email' => $member->email,
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
                Section::make('Company details')
                    ->columns(2)
                    ->visible(fn (): bool => $this->isPrimaryUser())
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Company name')
                            ->required(),
                        TextInput::make('contact_person')
                            ->label('Contact person'),
                        TextInput::make('email')
                            ->label('Company email')
                            ->email(),
                        TextInput::make('telephone')
                            ->label('Telephone'),
                        TextInput::make('url')
                            ->label('Portal URL slug'),
                        TextInput::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ]),
                Section::make('Your profile')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First name'),
                        TextInput::make('last_name')
                            ->label('Last name'),
                        TextInput::make('member_email')
                            ->label('Login email')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone'),
                        TextInput::make('company_name')
                            ->label('Company name'),
                        TextInput::make('town')
                            ->label('Town / city'),
                        TextInput::make('postal_code')
                            ->label('Postal code'),
                        TextInput::make('billing_address')
                            ->label('Billing address')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $member = $this->requireClientUser();
        $data = $this->form->getState();

        $member->update([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['member_email'],
            'company_name' => $data['company_name'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'town' => $data['town'] ?? null,
            'phone' => $data['phone'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ]);

        if ($this->isPrimaryUser()) {
            $client = $this->requireClient();

            $client->update([
                'client_name' => $data['client_name'],
                'address' => $data['address'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'email' => $data['email'] ?? null,
                'url' => $data['url'] ?? null,
            ]);
        }

        Notification::make()
            ->title('Account details saved.')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'My Account';
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
}
