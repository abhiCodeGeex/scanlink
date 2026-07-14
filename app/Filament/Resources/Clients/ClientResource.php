<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ManageClientUsers;
use App\Filament\Resources\Clients\RelationManagers\ClientUsersRelationManager;
use App\Filament\Resources\Clients\RelationManagers\ProfilesRelationManager;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Support\original_request;

class ClientResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Manage Client';

    protected static ?string $modelLabel = 'Client';

    protected static string|\UnitEnum|null $navigationGroup = 'Client';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'client_name';

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        $base = static::getRouteBaseName();

        return [
            NavigationItem::make('Add Client')
                ->group(static::getNavigationGroup())
                ->icon(Heroicon::OutlinedUserPlus)
                ->isActiveWhen(fn (): bool => original_request()->routeIs($base.'.create'))
                ->sort(1)
                ->url(static::getUrl('create'))
                ->visible(fn (): bool => static::canCreate()),
            NavigationItem::make('Manage Client')
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => original_request()->routeIs([
                    $base.'.index',
                    $base.'.edit',
                    $base.'.users',
                ]))
                ->sort(2)
                ->url(static::getUrl()),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('primaryUser');
    }

    public static function getRelations(): array
    {
        return [
            ClientUsersRelationManager::class,
            ProfilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
            'users' => ManageClientUsers::route('/{record}/users'),
        ];
    }
}
