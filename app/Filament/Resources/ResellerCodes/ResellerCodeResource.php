<?php

namespace App\Filament\Resources\ResellerCodes;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\ResellerCodes\Pages\ListResellerCodes;
use App\Filament\Resources\ResellerCodes\Pages\ResellerCodeHistory;
use App\Filament\Resources\ResellerCodes\Tables\ResellerCodesTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ResellerCodeResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Reseller Codes';

    protected static ?string $modelLabel = 'Reseller code';

    protected static ?string $pluralModelLabel = 'Reseller codes';

    protected static string|UnitEnum|null $navigationGroup = 'Reseller';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'reseller-codes';

    protected static ?string $recordTitleAttribute = 'reseller_code';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->hasResellerCode()
            ->with('primaryUser');
    }

    public static function table(Table $table): Table
    {
        return ResellerCodesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['reseller_code', 'client_name', 'email', 'telephone'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var Client $record */
        return array_filter([
            'Client' => $record->client_name,
            'Email' => $record->email,
            'Status' => $record->isResellerCodeActive() ? 'Active' : 'Inactive',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResellerCodes::route('/'),
            'history' => ResellerCodeHistory::route('/{record}/history'),
        ];
    }
}
