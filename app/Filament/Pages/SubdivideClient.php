<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use App\Models\Profile;
use App\Services\ClientSubdivisionService;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Validation\ValidationException;
use Throwable;
use UnitEnum;

class SubdivideClient extends Page
{
    use CanUseDatabaseTransactions;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canWrite();
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Client';

    protected static ?string $navigationLabel = 'Sub Divide Client';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Sub Divide Client';

    protected string $view = 'filament-panels::pages.page';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?string $previousUrl = null;

    public function mount(): void
    {
        $this->form->fill();
        $this->previousUrl = url()->previous();
    }

    public function getTitle(): string | Htmlable
    {
        return 'Sub Divide Client';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(null)
            ->components([
                $this->getWizardComponent(),
            ]);
    }

    public function getWizardComponent(): Wizard
    {
        return Wizard::make($this->getSteps())
            ->cancelAction($this->getCancelFormAction())
            ->submitAction($this->getSubmitFormAction())
            ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
            ->contained(false);
    }

    /**
     * @return array<Step>
     */
    public function getSteps(): array
    {
        return [
            Step::make('Select client')
                ->description('Please select client from which you want to get profiles and users')
                ->schema([
                    Select::make('source_client_id')
                        ->label('Select Client')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => Client::query()
                            ->when(
                                filled($search),
                                fn ($q) => $q->where('client_name', 'like', "%{$search}%"),
                            )
                            ->orderBy('client_name')
                            ->limit(50)
                            ->pluck('client_name', 'id')
                            ->all())
                        ->getOptionLabelUsing(fn ($value): ?string => Client::query()->whereKey($value)->value('client_name'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (?int $state, Set $set): void {
                            if (! $state) {
                                $set('profile_ids', []);
                                $set('user_ids', []);
                                $set('url', null);

                                return;
                            }

                            $client = Client::query()->find($state);
                            $set('url', $client?->url);
                            $set('profile_ids', []);
                            $set('user_ids', []);
                        }),
                ]),
            Step::make('Select profiles')
                ->description('Please select one or multiple profiles which you want to transfer to new client')
                ->schema([
                    CheckboxList::make('profile_ids')
                        ->label('Select Profiles')
                        ->options(fn (Get $get): array => Profile::query()
                            ->active()
                            ->legacyVisible()
                            ->where('client_id', $get('source_client_id'))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->columns(2)
                        ->required()
                        ->disabled(fn (Get $get): bool => blank($get('source_client_id'))),
                ]),
            Step::make('New client details')
                ->description('Please provide details for new client')
                ->schema([
                    TextInput::make('client_name')
                        ->label('Client Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->label('Address')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('telephone')
                        ->label('Telephone')
                        ->required()
                        ->tel()
                        ->maxLength(50),
                    TextInput::make('contact_person')
                        ->label('Contact Person')
                        ->required()
                        ->maxLength(255),
                    DatePicker::make('regi_date')
                        ->label('Registration date')
                        ->required()
                        ->default(now()),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->required(),
                    TextInput::make('url')
                        ->label('URL')
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(2),
            Step::make('Select users')
                ->description('Please select one or multiple users whom you want to be under new client')
                ->schema([
                    CheckboxList::make('user_ids')
                        ->label('Users in Old Client')
                        ->options(function (Get $get): array {
                            $clientId = $get('source_client_id');

                            if (blank($clientId)) {
                                return [];
                            }

                            return Client::query()
                                ->find($clientId)
                                ?->subUsers()
                                ->orderBy('email')
                                ->pluck('email', 'id')
                                ->all() ?? [];
                        })
                        ->columns(2)
                        ->disabled(fn (Get $get): bool => blank($get('source_client_id'))),
                ]),
        ];
    }

    public function subdivide(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            $sourceClient = Client::query()->findOrFail($data['source_client_id']);

            $newClient = app(ClientSubdivisionService::class)->subdivide(
                sourceClient: $sourceClient,
                profileIds: array_map('intval', $data['profile_ids'] ?? []),
                newClientData: [
                    'client_name' => $data['client_name'],
                    'address' => $data['address'],
                    'telephone' => $data['telephone'],
                    'contact_person' => $data['contact_person'],
                    'regi_date' => $data['regi_date'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'url' => $data['url'],
                ],
                userIdsToTransfer: array_map('intval', $data['user_ids'] ?? []),
            );
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (ValidationException $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        Notification::make()
            ->title('New Client has been created...')
            ->success()
            ->send();

        $redirectUrl = ClientResource::getUrl('edit', ['record' => $newClient]);

        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('subdivide')
            ->label('Create new client and transfer profiles and users')
            ->submit('subdivide');
    }

    protected function getSubmitFormLivewireMethodName(): string
    {
        return 'subdivide';
    }

    protected function getCancelFormAction(): Action
    {
        $url = $this->previousUrl ?? ClientResource::getUrl();

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler(
                FilamentView::hasSpaMode($url)
                    ? 'document.referrer ? window.history.back() : Livewire.navigate(' . Js::from($url) . ')'
                    : 'document.referrer ? window.history.back() : (window.location.href = ' . Js::from($url) . ')',
            )
            ->color('gray');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Form
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName());
    }

    public function hasFormWrapper(): bool
    {
        return false;
    }
}
