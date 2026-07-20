<?php

namespace App\Filament\Pages;

use App\Models\CollectedContact;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailSubmissionLog extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?string $navigationLabel = 'Email Submission Log';

    protected static ?string $title = 'Email Submission Log';

    protected static ?string $slug = 'email-submission-log';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected Width|string|null $maxContentWidth = Width::ThreeExtraLarge;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canAccessPanel();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Export contact form submissions collected from profile pages within a date range.';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Export email submissions')
                    ->description('Select a date range, then download the results as a CSV file. Fields marked with * are required.')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                DatePicker::make('dateFrom')
                                    ->label('From')
                                    ->required()
                                    ->native(false)
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->helperText('Start date (inclusive)'),
                                DatePicker::make('dateTo')
                                    ->label('To')
                                    ->required()
                                    ->native(false)
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->helperText('End date (inclusive)'),
                            ]),
                    ]),
            ]);
    }

    public function getCsv(): ?StreamedResponse
    {
        $data = $this->form->getState();
        $from = $data['dateFrom'];
        $to = $data['dateTo'];

        if ($from > $to) {
            Notification::make()
                ->title('From date must be on or before To date.')
                ->danger()
                ->send();

            return null;
        }

        $filename = 'email-submission-log-'.$from.'-to-'.$to.'.csv';

        return response()->streamDownload(function () use ($from, $to): void {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'ID',
                'Date',
                'Client',
                'Profile',
                'Name',
                'Surname',
                'Mobile',
                'Email',
            ]);

            CollectedContact::query()
                ->with(['profile.client'])
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('id')
                ->chunkById(500, function ($contacts) use ($out): void {
                    foreach ($contacts as $contact) {
                        fputcsv($out, [
                            $contact->id_profile,
                            optional($contact->created_at)?->format('d/m/Y H:i') ?? '',
                            $contact->profile?->client?->client_name ?? '',
                            $contact->profile?->displayLabel() ?? '',
                            $contact->name ?? '',
                            $contact->surname ?? '',
                            $contact->mobile ?? '',
                            $contact->email ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('getCsv')
                ->label('Download CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->submit('getCsv'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Email Submission Log';
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
            ->livewireSubmitHandler('getCsv')
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
