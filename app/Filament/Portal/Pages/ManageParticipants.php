<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Participant;
use App\Models\ParticipantRecipient;
use App\Models\Profile;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DbSchema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use OpenSpout\Common\Entity\Row;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageParticipants extends Page
{
    use InteractsWithClientMembership;
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Participants';

    protected static ?string $title = 'Participant List';

    protected static ?string $slug = 'participants';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.manage-participants';

    public bool $embed = false;

    public int $profileId = 0;

    /** @var list<string> */
    public array $notificationEmails = [''];

    public string $participantName = '';

    public string $participantDueDate = '';

    /** @var Collection<int, Participant> */
    public Collection $participants;

    public ?int $editingId = null;

    public string $editName = '';

    public string $editDueDate = '';

    /** @var TemporaryUploadedFile|null */
    public $uploadFile = null;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function mount(): void
    {
        $this->embed = request()->boolean('embed');
        $this->participants = collect();

        $client = $this->requireClient();
        $requested = (int) request()->query('profile', 0);

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->when($requested > 0, fn ($q) => $q->whereKey($requested))
            ->orderBy('id')
            ->first();

        abort_unless($profile, 404);

        $this->profileId = (int) $profile->id;
        $this->loadNotificationEmails();
        $this->loadParticipants();
    }

    public function addNotificationEmail(): void
    {
        $this->notificationEmails[] = '';
    }

    public function removeNotificationEmail(int $index): void
    {
        if (count($this->notificationEmails) <= 1) {
            $this->notificationEmails = [''];

            return;
        }

        unset($this->notificationEmails[$index]);
        $this->notificationEmails = array_values($this->notificationEmails);
    }

    public function addParticipant(): void
    {
        $name = trim($this->participantName);
        $dueDate = trim($this->participantDueDate);

        if ($name === '') {
            $this->js('alert('.json_encode('Please enter participant name').')');

            return;
        }

        if ($dueDate === '') {
            $this->js('alert('.json_encode('Please select due date').')');

            return;
        }

        $parsed = $this->parseDueDateOrFail($dueDate);
        if ($parsed === null) {
            return;
        }

        if ($parsed->lt(Carbon::today())) {
            $this->js('alert('.json_encode('Please select due date as today or later').')');

            return;
        }

        $this->assertProfileOwned($this->profileId);

        Participant::query()->create([
            ...$this->nextParticipantPayload($this->profileId),
            'name' => $name,
            'employer_cmp' => '',
            'due_date' => $parsed->toDateString(),
            'participated_date' => $parsed->toDateString(),
            'is_participated' => false,
        ]);

        $this->participantName = '';
        $this->participantDueDate = '';
        $this->loadParticipants();
    }

    public function startEdit(int $participantId): void
    {
        $participant = $this->findOwnedParticipant($participantId);
        $this->editingId = $participantId;
        $this->editName = (string) $participant->name;
        $this->editDueDate = optional($participant->due_date)->format('d/m/Y') ?: '';
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editName = '';
        $this->editDueDate = '';
    }

    public function saveEdit(): void
    {
        if (! $this->editingId) {
            return;
        }

        $name = trim($this->editName);
        $dueDate = trim($this->editDueDate);

        if ($name === '') {
            $this->js('alert('.json_encode('Please enter participant name').')');

            return;
        }

        $parsed = $this->parseDueDateOrFail($dueDate);
        if ($parsed === null) {
            return;
        }

        if ($parsed->lt(Carbon::today())) {
            $this->js('alert('.json_encode('Please select due date as today or later').')');

            return;
        }

        $participant = $this->findOwnedParticipant($this->editingId);
        $participant->update([
            'name' => $name,
            'due_date' => $parsed->toDateString(),
        ]);

        $this->cancelEdit();
        $this->loadParticipants();
    }

    public function deleteParticipant(int $participantId): void
    {
        $this->findOwnedParticipant($participantId)->delete();
        if ($this->editingId === $participantId) {
            $this->cancelEdit();
        }
        $this->loadParticipants();
    }

    public function clearList(): void
    {
        $this->assertProfileOwned($this->profileId);
        Participant::query()->where('profile_id', $this->profileId)->delete();
        $this->cancelEdit();
        $this->loadParticipants();
    }

    public function importUpload(): void
    {
        $this->validate([
            'uploadFile' => ['required', 'file', 'max:5120'],
        ]);

        $this->assertProfileOwned($this->profileId);

        $ext = strtolower((string) $this->uploadFile->getClientOriginalExtension());
        if ($ext !== 'xlsx') {
            $this->js('alert('.json_encode('Upload .xlsx file only').')');
            $this->uploadFile = null;

            return;
        }

        $path = $this->uploadFile->getRealPath();
        $reader = new XlsxReader;
        $reader->open($path);

        $imported = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();
                $name = trim((string) ($cells[0] ?? ''));
                $dueRaw = $cells[1] ?? '';

                if ($name === '' || strtolower($name) === 'name') {
                    continue;
                }

                $dueDate = $this->parseImportDueDate($dueRaw);
                if ($dueDate === null) {
                    continue;
                }

                Participant::query()->create([
                    ...$this->nextParticipantPayload($this->profileId),
                    'name' => $name,
                    'employer_cmp' => '',
                    'due_date' => $dueDate,
                    'participated_date' => $dueDate,
                    'is_participated' => false,
                ]);
                $imported++;
            }
            break; // first sheet only (legacy)
        }

        $reader->close();
        $this->uploadFile = null;
        $this->loadParticipants();

        Notification::make()
            ->title('Upload complete')
            ->body("{$imported} participant(s) added.")
            ->success()
            ->send();
    }

    public function downloadList(): StreamedResponse
    {
        $this->assertProfileOwned($this->profileId);
        $participants = Participant::query()
            ->where('profile_id', $this->profileId)
            ->orderBy('participant_id')
            ->get();

        $filename = 'participant_list.xlsx';

        return response()->streamDownload(function () use ($participants): void {
            $writer = new XlsxWriter;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(['Name', 'Employer/Company', 'Response Due By']));

            foreach ($participants as $participant) {
                $writer->addRow(Row::fromValues([
                    $participant->name,
                    $participant->employer_cmp,
                    optional($participant->due_date)->format('d/m/Y'),
                ]));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function saveAndExit(): void
    {
        $this->assertProfileOwned($this->profileId);

        $emails = [];
        foreach ($this->notificationEmails as $email) {
            $email = trim((string) $email);
            if ($email === '') {
                continue;
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->js('alert('.json_encode('Enter a valid email.').')');

                return;
            }
            $emails[] = $email;
        }

        // Legacy Save and exit requires at least one notification email when any are entered;
        // empty list is allowed (clears recipients).
        ParticipantRecipient::query()->where('profile_id', $this->profileId)->delete();

        foreach ($emails as $email) {
            ParticipantRecipient::query()->create([
                ...$this->nextRecipientPayload(),
                'profile_id' => $this->profileId,
                'email' => $email,
            ]);
        }

        $this->loadNotificationEmails();

        if ($this->embed) {
            // Close Colorbox-style parent modal (runs inside embed iframe).
            $this->js(<<<'JS'
                queueMicrotask(() => {
                    try {
                        window.parent.postMessage({ type: 'scanlink-close-participants' }, '*');
                    } catch (e) {}
                });
            JS);

            return;
        }

        Notification::make()->title('Participant list saved')->success()->send();
    }

    /**
     * @return array{rowClass: string, dueFormatted: string}
     */
    public function participantRowMeta(Participant $participant): array
    {
        $dueFormatted = optional($participant->due_date)->format('d/m/Y') ?: '';
        $rowClass = '';

        if ($participant->is_participated) {
            $rowClass = 'is-received';
        } elseif ($participant->due_date && $participant->due_date->lt(Carbon::today())) {
            $rowClass = 'is-overdue';
        }

        return compact('rowClass', 'dueFormatted');
    }

    protected function loadParticipants(): void
    {
        $this->participants = Participant::query()
            ->where('profile_id', $this->profileId)
            ->orderBy('participant_id')
            ->get();
    }

    protected function loadNotificationEmails(): void
    {
        $emails = ParticipantRecipient::query()
            ->where('profile_id', $this->profileId)
            ->orderBy('participant_recipient_id')
            ->pluck('email')
            ->map(fn ($e) => (string) $e)
            ->values()
            ->all();

        $this->notificationEmails = $emails !== [] ? $emails : [''];
    }

    protected function assertProfileOwned(int $profileId): Profile
    {
        $client = $this->requireClient();

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);
    }

    protected function findOwnedParticipant(int $participantId): Participant
    {
        $participant = Participant::query()->findOrFail($participantId);
        $this->assertProfileOwned((int) $participant->profile_id);

        return $participant;
    }

    /**
     * @return array{participant_id?: int, profile_id: int}
     */
    protected function nextParticipantPayload(int $profileId): array
    {
        $payload = ['profile_id' => $profileId];

        if (! $this->columnIsAutoIncrement('participant', 'participant_id')) {
            $payload['participant_id'] = (int) Participant::query()->max('participant_id') + 1;
        }

        return $payload;
    }

    /**
     * @return array{participant_recipient_id?: int}
     */
    protected function nextRecipientPayload(): array
    {
        $payload = [];

        if (! $this->columnIsAutoIncrement('participant_recipient', 'participant_recipient_id')) {
            $payload['participant_recipient_id'] = (int) ParticipantRecipient::query()->max('participant_recipient_id') + 1;
        }

        return $payload;
    }

    protected function columnIsAutoIncrement(string $table, string $column): bool
    {
        if (! DbSchema::hasTable($table)) {
            return true;
        }

        try {
            $info = DB::selectOne(
                'SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                [$table, $column]
            );

            return $info && str_contains((string) ($info->EXTRA ?? ''), 'auto_increment');
        } catch (\Throwable) {
            return false;
        }
    }

    protected function parseDueDateOrFail(string $value): ?Carbon
    {
        $value = trim($value);

        if (! preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            $this->js('alert('.json_encode('Please select due date as DD/MM/YYYY').')');

            return null;
        }

        try {
            return Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->startOfDay();
        } catch (\Throwable) {
            $this->js('alert('.json_encode('Please select a valid due date').')');

            return null;
        }
    }

    protected function parseImportDueDate(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance(\DateTimeImmutable::createFromInterface($value))->toDateString();
        }

        if (is_numeric($value)) {
            // Excel serial date
            try {
                $unix = ((int) $value - 25569) * 86400;

                return Carbon::createFromTimestampUTC($unix)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    public function getLayout(): string
    {
        return $this->embed
            ? 'layouts.participant-list-embed'
            : parent::getLayout();
    }

    public function getView(): string
    {
        return $this->embed
            ? 'filament.portal.pages.participant-list-embed'
            : $this->view;
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->embed ? null : parent::getHeading();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Participant List';
    }
}
