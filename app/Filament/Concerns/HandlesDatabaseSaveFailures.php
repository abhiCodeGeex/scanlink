<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/**
 * Convert MySQL/schema save failures into Filament validation + toast,
 * instead of Livewire's generic "Error while loading page".
 */
trait HandlesDatabaseSaveFailures
{
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (QueryException $exception) {
            $this->throwFriendlyDatabaseException($exception);
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (QueryException $exception) {
            $this->throwFriendlyDatabaseException($exception);
        }
    }

    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (QueryException $exception) {
            $this->throwFriendlyDatabaseException($exception);
        }
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (QueryException $exception) {
            $this->throwFriendlyDatabaseException($exception);
        }
    }

    protected function throwFriendlyDatabaseException(QueryException $exception): never
    {
        report($exception);

        $message = $this->friendlyDatabaseMessage($exception);

        Notification::make()
            ->title('Could not save')
            ->body($message)
            ->danger()
            ->persistent()
            ->send();

        throw ValidationException::withMessages([
            'data' => $message,
        ]);
    }

    protected function friendlyDatabaseMessage(QueryException $exception): string
    {
        $sqlMessage = (string) ($exception->errorInfo[2] ?? $exception->getMessage());

        if (
            str_contains($sqlMessage, "doesn't have a default value")
            || str_contains($sqlMessage, 'cannot be null')
        ) {
            if (preg_match("/(?:Field|Column) '([^']+)'/", $sqlMessage, $matches) === 1) {
                return 'Missing required value for "'.$matches[1].'". Please fill required fields and try again.';
            }
        }

        if (str_contains($sqlMessage, 'Duplicate entry')) {
            return 'This record already exists. Check unique fields (email, URL, etc.) and try again.';
        }

        if (str_contains($sqlMessage, 'Data truncated') || str_contains($sqlMessage, 'Incorrect ')) {
            return 'One or more values are invalid for this form. Please check your entries and try again.';
        }

        return 'Unable to save this record. Please check your entries and try again.';
    }
}
