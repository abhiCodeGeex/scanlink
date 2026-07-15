<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Enums\ClientUserRole;
use App\Models\ClientUser;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientUsersTable
{
    public static function configure(Table $table, bool $includeStatusColumns = false): Table
    {
        $columns = [
            TextColumn::make('email')
                ->label('Email Address')
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('d/m/Y'),
            TextColumn::make('expire_at')
                ->label('Expires')
                ->dateTime('d/m/Y H:i:s')
                ->color(fn (ClientUser $record): string => $record->expire_at?->isPast() ? 'danger' : 'gray'),
        ];

        if ($includeStatusColumns) {
            $columns[] = IconColumn::make('status')
                ->label('Active')
                ->boolean();
            $columns[] = IconColumn::make('video_upload')
                ->label('Video')
                ->boolean();
        }

        return $table
            ->columns($columns)
            ->recordActionsColumnLabel('Option')
            ->recordActions(static::recordActions());
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
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->mutateDataUsing(function (array $data): array {
                $data['role'] = ClientUserRole::SubUser;
                $data['is_sub_user'] = true;
                $data['is_password_change'] = true;
                $data['status'] = true;
                $data['video_upload'] = true;
                $data['expire_at'] = now()->addYear();

                return $data;
            });
    }
}
