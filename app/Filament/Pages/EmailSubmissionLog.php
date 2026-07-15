<?php

namespace App\Filament\Pages;

use App\Models\CollectedContact;
use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailSubmissionLog extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static ?string $navigationLabel = 'Email Submission Log';

    protected static ?string $title = 'Email Submission Log';

    protected static ?string $slug = 'email-submission-log';

    protected static ?int $navigationSort = -5;

    protected string $view = 'filament.pages.email-submission-log';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canAccessPanel();
    }

    public function getCsv(): ?StreamedResponse
    {
        if (! filled($this->dateFrom) || ! filled($this->dateTo)) {
            Notification::make()
                ->title('From and To dates are required.')
                ->danger()
                ->send();

            return null;
        }

        if ($this->dateFrom > $this->dateTo) {
            Notification::make()
                ->title('From date must be on or before To date.')
                ->danger()
                ->send();

            return null;
        }

        $from = $this->dateFrom;
        $to = $this->dateTo;
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
}
