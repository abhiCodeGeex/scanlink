<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\ClientUserRole;
use App\Filament\Support\DateRangeTableFilter;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use App\Models\ClientUser;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientUsersTable
{
    /**
     * @return array{email: null, password: null, video_upload: true}
     */
    public static function defaultCreateFormState(): array
    {
        return [
            'email' => null,
            'password' => null,
            'video_upload' => true,
        ];
    }

    public static function mountCleanCreateForm(Action|CreateAction $action): void
    {
        $action->fillForm(static::defaultCreateFormState());
    }

    /**
     * @return array<int, TextInput|Toggle>
     */
    public static function createFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->placeholder('Enter user email')
                ->required()
                ->autocomplete('off')
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->placeholder('Enter password')
                ->required()
                ->autocomplete('new-password'),
            Toggle::make('video_upload')
                ->label('Video Upload Permission')
                ->default(true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareCreateData(array $data): array
    {
        return [
            ...$data,
            'role' => ClientUserRole::SubUser,
            'is_sub_user' => true,
            'is_password_change' => false,
            'status' => true,
            'video_upload' => (bool) ($data['video_upload'] ?? true),
            'expire_at' => now()->addYear(),
        ];
    }

    public static function configure(Table $table, bool $includeStatusColumns = true): Table
    {
        $columns = [
            TextColumn::make('email')
                ->label('Email Address')
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('d/m/Y')
                ->sortable(),
            TextColumn::make('expire_at')
                ->label('Expires')
                ->dateTime('d/m/Y H:i:s')
                ->color(fn (ClientUser $record): string => $record->expire_at?->isPast() ? 'danger' : 'gray'),
        ];

        if ($includeStatusColumns) {
            $columns[] = IconColumn::make('status')
                ->label('Active')
                ->boolean()
                ->tooltip(fn (ClientUser $record): string => $record->status ? 'Click to deactivate' : 'Click to activate')
                ->action(
                    Action::make('toggleActive')
                        ->requiresConfirmation()
                        ->modalHeading(fn (ClientUser $record): string => $record->status
                            ? 'Deactivate this user?'
                            : 'Activate this user?')
                        ->modalDescription(fn (ClientUser $record): string => $record->status
                            ? 'This user will no longer be able to sign in.'
                            : 'This user will be able to sign in again.')
                        ->action(fn (ClientUser $record) => $record->update(['status' => ! $record->status])),
                );
            $columns[] = IconColumn::make('video_upload')
                ->label('Video')
                ->boolean()
                ->tooltip(fn (ClientUser $record): string => $record->video_upload ? 'Click to disable video upload' : 'Click to enable video upload')
                ->action(
                    Action::make('toggleVideo')
                        ->requiresConfirmation()
                        ->modalHeading(fn (ClientUser $record): string => $record->video_upload
                            ? 'Disable video upload?'
                            : 'Enable video upload?')
                        ->modalDescription(fn (ClientUser $record): string => $record->video_upload
                            ? 'This user will lose permission to upload videos.'
                            : 'This user will be allowed to upload videos.')
                        ->action(fn (ClientUser $record) => $record->update(['video_upload' => ! $record->video_upload])),
                );
        }

        $filters = [
            SearchTableFilter::make(['email']),
        ];

        if ($includeStatusColumns) {
            $filters[] = TernaryFilter::make('status')
                ->label('Active')
                ->trueLabel('Active only')
                ->falseLabel('Inactive only')
                ->queries(
                    true: fn (Builder $query): Builder => $query->where('status', '1'),
                    false: fn (Builder $query): Builder => $query->where('status', '0'),
                );
            $filters[] = TernaryFilter::make('video_upload')
                ->label('Video permission')
                ->trueLabel('Enabled')
                ->falseLabel('Disabled')
                ->queries(
                    true: fn (Builder $query): Builder => $query->where('video_upload', true),
                    false: fn (Builder $query): Builder => $query->where('video_upload', false),
                );
        }

        $filters[] = DateRangeTableFilter::make('created_at', 'Date range');

        return TableFilterDefaults::apply($table
            ->columns($columns)
            ->defaultSort('created_at', 'desc')
            ->filters($filters)
            // Only Option buttons (and icon column actions) should run — not row clicks.
            ->recordAction(null)
            ->recordUrl(null)
            ->recordActionsColumnLabel('Option')
            ->recordActions(static::recordActions()));
    }

    /**
     * @return array<Action|EditAction|DeleteAction>
     */
    public static function recordActions(): array
    {
        return [
            Action::make('toggleStatus')
                ->label(fn (ClientUser $record): string => $record->status ? 'Block' : 'Unblock')
                ->icon(fn (ClientUser $record): string => $record->status ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                ->color(fn (ClientUser $record): string => $record->status ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(fn (ClientUser $record) => $record->update(['status' => ! $record->status])),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public static function createAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Add User')
            ->modalAutofocus(false)
            ->modalCancelActionLabel('Cancel')
            ->schema(static::createFormSchema())
            ->mountUsing(fn (CreateAction $action): mixed => static::mountCleanCreateForm($action))
            ->mutateDataUsing(fn (array $data): array => static::prepareCreateData($data))
            ->after(function (CreateAction $action): void {
                $livewire = $action->getLivewire();

                $livewire->js('window.scanlinkStickOnClientUsers?.()');

                if (method_exists($livewire, 'flushCachedTableRecords')) {
                    $livewire->flushCachedTableRecords();
                } elseif (method_exists($livewire, 'resetTable')) {
                    $livewire->resetTable();
                }

                static::mountCleanCreateForm($action);
            });
    }
}
