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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DbSchema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageParticipants extends Page
{
    use InteractsWithClientMembership;
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Participants';

    protected static ?string $title = 'Form Participants';

    protected static ?string $slug = 'participants';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.manage-participants';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /** @var Collection<int, Participant> */
    public Collection $participants;

    /** @var TemporaryUploadedFile|null */
    public $csvImportFile = null;

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
                        ->searchable()
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

        Participant::query()->create([
            ...$this->nextParticipantPayload((int) $data['profile_id']),
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

    public function importCsv(): void
    {
        $this->validate([
            'csvImportFile' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'data.profile_id' => ['required'],
        ]);

        $profileId = (int) $this->data['profile_id'];
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $handle = fopen($this->csvImportFile->getRealPath(), 'r');

        if ($handle === false) {
            Notification::make()->title('Could not read CSV file')->danger()->send();

            return;
        }

        $imported = 0;
        $rowNum = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if ($row === [null] || $row === []) {
                continue;
            }

            $name = trim((string) ($row[0] ?? ''));
            $employer = trim((string) ($row[1] ?? ''));
            $dueRaw = trim((string) ($row[2] ?? ''));

            if ($name === '' || strtolower($name) === 'name') {
                continue;
            }

            $dueDate = $this->parseParticipantDueDate($dueRaw);

            Participant::query()->create([
                ...$this->nextParticipantPayload($profileId),
                'name' => $name,
                'employer_cmp' => $employer !== '' ? $employer : null,
                'due_date' => $dueDate,
                'is_participated' => false,
            ]);

            $imported++;
        }

        fclose($handle);
        $this->csvImportFile = null;
        $this->loadParticipants($profileId);

        Notification::make()
            ->title('CSV imported')
            ->body("{$imported} participant(s) added.")
            ->success()
            ->send();
    }

    public function exportParticipantsCsv(): StreamedResponse
    {
        $profileId = (int) ($this->data['profile_id'] ?? 0);
        $client = $this->requireClient();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $participants = Participant::query()
            ->where('profile_id', $profile->id)
            ->orderBy('participant_id')
            ->get();

        $filename = 'participants-'.$profile->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($participants): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['name', 'employer', 'due_date', 'participated']);

            foreach ($participants as $participant) {
                fputcsv($out, [
                    $participant->name,
                    $participant->employer_cmp,
                    optional($participant->due_date)->format('Y-m-d'),
                    $participant->is_participated ? 'yes' : 'no',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function loadParticipants(int $profileId): void
    {
        $this->participants = Participant::query()
            ->where('profile_id', $profileId)
            ->orderBy('participant_id')
            ->get();
    }

    /**
     * @return array{participant_id?: int, profile_id: int}
     */
    protected function nextParticipantPayload(int $profileId): array
    {
        $payload = ['profile_id' => $profileId];

        if (! $this->participantUsesAutoIncrement()) {
            $payload['participant_id'] = (int) Participant::query()->max('participant_id') + 1;
        }

        return $payload;
    }

    protected function participantUsesAutoIncrement(): bool
    {
        if (! DbSchema::hasTable('participant')) {
            return true;
        }

        try {
            $column = DB::selectOne(
                'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['participant', 'participant_id']
            );

            return $column && str_contains((string) ($column->EXTRA ?? ''), 'auto_increment');
        } catch (\Throwable) {
            return false;
        }
    }

    protected function parseParticipantDueDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            return sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
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
