<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Participant;
use App\Models\Profile;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Support\Collection;

class ManageParticipants extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Participants';

    protected static ?string $title = 'Form Participants';

    protected static ?string $slug = 'participants';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.manage-participants';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /** @var Collection<int, Participant> */
    public Collection $participants;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function mount(): void
    {
        $this->participants = collect();
        $first = $this->clientProfileOptions()->keys()->first();
        $this->form->fill([
            'profile_id' => $first,
            'name' => null,
            'employer_cmp' => null,
            'due_date' => null,
        ]);

        if ($first) {
            $this->loadParticipants((int) $first);
        }
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Add participant')
                ->columns(2)
                ->schema([
                    Select::make('profile_id')
                        ->label('Profile')
                        ->options(fn (): array => $this->clientProfileOptions()->all())
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (?string $state) => $this->loadParticipants((int) $state)),
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('employer_cmp')->label('Employer')->maxLength(255),
                    DatePicker::make('due_date')->label('Due date'),
                ]),
        ]);
    }

    public function addParticipant(): void
    {
        $data = $this->form->getState();
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($data['profile_id']);

        $nextId = (int) Participant::query()->max('participant_id') + 1;

        Participant::query()->create([
            'participant_id' => $nextId,
            'profile_id' => $data['profile_id'],
            'name' => $data['name'],
            'employer_cmp' => $data['employer_cmp'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'is_participated' => false,
        ]);

        Notification::make()->title('Participant added')->success()->send();
        $this->loadParticipants((int) $data['profile_id']);
        $this->form->fill([
            'profile_id' => $data['profile_id'],
            'name' => null,
            'employer_cmp' => null,
            'due_date' => null,
        ]);
    }

    public function deleteParticipant(int $participantId): void
    {
        $client = $this->requireClient();
        $participant = Participant::query()->findOrFail($participantId);

        abort_unless(
            Profile::query()->where('client_id', $client->id)->whereKey($participant->profile_id)->exists(),
            403,
        );

        $participant->delete();
        $this->loadParticipants((int) ($this->data['profile_id'] ?? $participant->profile_id));
        Notification::make()->title('Participant removed')->success()->send();
    }

    protected function loadParticipants(int $profileId): void
    {
        $this->participants = Participant::query()
            ->where('profile_id', $profileId)
            ->orderBy('participant_id')
            ->get();
    }

    /** @return Collection<int|string, string> */
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
            Action::make('addParticipant')->label('Add participant')->submit('addParticipant'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('addParticipant')
            ->footer([
                Actions::make($this->getFormActions())->key('form-actions'),
            ]);
    }
}
