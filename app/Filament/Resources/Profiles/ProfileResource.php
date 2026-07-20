<?php

namespace App\Filament\Resources\Profiles;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Resources\Profiles\Schemas\ProfileInfolist;
use App\Filament\Resources\Profiles\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\LogosRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\PicturesRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\VideosRelationManager;
use App\Filament\Resources\Profiles\Tables\ProfilesTable;
use App\Models\Profile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = Profile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Manage Product';

    protected static ?string $modelLabel = 'Profile';

    protected static string|\UnitEnum|null $navigationGroup = 'Product';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        // Profile No. in admin is the primary key `id` — must be searchable.
        return ['id', 'name', 'code_profile_name', 'identification', 'serial_no', 'shorturl', 'notes', 'client.client_name'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var Profile $record */
        $label = trim((string) ($record->name ?: $record->code_profile_name));

        return $label !== ''
            ? $label
            : 'Profile #'.$record->getKey();
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var \App\Models\Profile $record */
        $record->loadMissing('client');

        return array_filter([
            'Client' => $record->client?->client_name,
            'ID' => (string) $record->id,
            'Code' => $record->code_profile_name,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfilesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // List table only needs contacts; view/edit pages load the rest explicitly.
        return parent::getEloquentQuery()
            ->with(['contacts'])
            ->active();
    }

    public static function getRelations(): array
    {
        return [
            LogosRelationManager::class,
            PicturesRelationManager::class,
            DocumentsRelationManager::class,
            VideosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'create' => CreateProfile::route('/create'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }
}
