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

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

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
        return parent::getEloquentQuery()
            ->with(['client', 'equipmentType', 'owner', 'contacts', 'qrImage', 'logos', 'pictures', 'documents', 'videos', 'weblinks'])
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
