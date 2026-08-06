<?php

namespace App\Filament\Portal\Resources\Profiles;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Filament\Portal\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Portal\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Portal\Resources\Profiles\Schemas\PortalProfileForm;
use App\Filament\Resources\Profiles\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\LogosRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\PicturesRelationManager;
use App\Filament\Resources\Profiles\RelationManagers\VideosRelationManager;
use App\Filament\Resources\Profiles\Schemas\ProfileInfolist;
use App\Filament\Portal\Resources\Profiles\Tables\PortalProfilesTable;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Master Code List';

    protected static ?string $modelLabel = 'Profile';

    protected static ?string $slug = 'profiles';

    /** Live My Account submenu item. */
    protected static string|\UnitEnum|null $navigationGroup = 'My Account';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PortalProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalProfilesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // Master Code List shows only activated profiles. Unsaved create drafts
        // stay open (update_or_not=0) and belong on Code Balance only.
        return static::baseClientQuery()->claimedSlot();
    }

    /**
     * Record-route binding must resolve BOTH activated profiles (view/edit) and
     * still-open create drafts. "Add a New Code" (CreateProfile) binds an open
     * slot (update_or_not=0) before Save; applying claimedSlot() here — as the
     * default getEloquentQuery() now does — would make /profiles/create 404.
     * Client scoping is preserved so a portal user cannot bind another client's
     * record.
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::baseClientQuery();
    }

    protected static function baseClientQuery(): Builder
    {
        $clientId = static::currentClientId();

        return parent::getEloquentQuery()
            ->with(['contacts', 'equipmentType'])
            ->when($clientId, fn (Builder $query): Builder => $query->where('client_id', $clientId))
            ->active();
    }

    public static function canViewAny(): bool
    {
        return static::currentMembership() !== null;
    }

    public static function canCreate(): bool
    {
        $member = static::currentMembership();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_addcode;
    }

    public static function canEdit($record): bool
    {
        $member = static::currentMembership();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_edit;
    }

    public static function canDelete($record): bool
    {
        $member = static::currentMembership();

        if (! $member) {
            return false;
        }

        return $member->isPrimary() || (bool) $member->access_delete;
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

    protected static function currentMembership(): ?ClientUser
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        if (! in_array($user->user_type, [UserType::Portal, UserType::Voc], true)) {
            return null;
        }

        return $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first();
    }

    protected static function currentClientId(): ?int
    {
        return static::currentMembership()?->client_id;
    }
}
