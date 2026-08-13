<?php

namespace App\Filament\Portal\Pages;

use App\Enums\ClientUserRole;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\ClientUser;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamUsers extends Page implements HasTable
{
    use InteractsWithClientMembership;
    use InteractsWithTable;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Team Users';

    protected static ?string $title = 'Team Users';

    protected static ?string $slug = 'team-users';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.team-users';

    public static function getNavigationGroup(): ?string
    {
        return 'Account';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ClientUser::query()
                ->where('client_id', $this->requireClient()->id)
                ->where('role', ClientUserRole::SubUser))
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First name'),
                TextColumn::make('last_name')
                    ->label('Last name'),
                IconColumn::make('access_addcode')
                    ->label('Add code')
                    ->boolean(),
                IconColumn::make('access_edit')
                    ->label('Edit')
                    ->boolean(),
                IconColumn::make('access_delete')
                    ->label('Delete')
                    ->boolean(),
                IconColumn::make('access_analytics')
                    ->label('Analytics')
                    ->boolean(),
                IconColumn::make('access_form_submission')
                    ->label('Forms')
                    ->boolean(),
                IconColumn::make('access_download')
                    ->label('Download')
                    ->boolean(),
                IconColumn::make('access_label')
                    ->label('Labels')
                    ->boolean(),
                IconColumn::make('access_log')
                    ->label('Visitor log')
                    ->boolean(),
                IconColumn::make('status')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add team user')
                    ->schema($this->teamUserFormSchema())
                    ->mutateDataUsing(fn (array $data): array => $this->mutateCreateData($data)),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($this->teamUserFormSchema(requirePassword: false)),
                DeleteAction::make(),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    protected function teamUserFormSchema(bool $requirePassword = true): array
    {
        return [
            TextInput::make('first_name')
                ->label('First name'),
            TextInput::make('last_name')
                ->label('Last name'),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required($requirePassword),
            Toggle::make('access_addcode')
                ->label('Can add codes'),
            Toggle::make('access_edit')
                ->label('Can edit codes'),
            Toggle::make('access_delete')
                ->label('Can delete codes'),
            Toggle::make('access_analytics')
                ->label('Can view analytics'),
            Toggle::make('access_form_submission')
                ->label('Can view form submissions'),
            Toggle::make('access_download')
                ->label('Can download QR / PDF'),
            Toggle::make('access_label')
                ->label('Can order labels'),
            Toggle::make('access_log')
                ->label('Can view visitor log'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateCreateData(array $data): array
    {
        $data['client_id'] = $this->requireClient()->id;
        $data['role'] = ClientUserRole::SubUser;
        $data['is_sub_user'] = true;
        $data['status'] = true;
        $data['is_password_change'] = false;
        $data['video_upload'] = true;
        $data['expire_at'] = now()->addYear();

        return $data;
    }
}
