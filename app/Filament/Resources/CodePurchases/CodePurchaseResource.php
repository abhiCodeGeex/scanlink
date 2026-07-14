<?php

namespace App\Filament\Resources\CodePurchases;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\CodePurchases\Pages\ListCodePurchases;
use App\Filament\Resources\CodePurchases\Pages\ViewCodePurchase;
use App\Filament\Resources\CodePurchases\Schemas\CodePurchaseInfolist;
use App\Filament\Resources\CodePurchases\Tables\CodePurchasesTable;
use App\Models\CodePurchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CodePurchaseResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = CodePurchase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Manage Code Order';

    protected static ?string $modelLabel = 'Code order';

    protected static string|UnitEnum|null $navigationGroup = 'Order';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'email';

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        // Live `code_purchase` has no `transaction_id` (that column exists on `orders` only).
        $attributes = ['email', 'first_name', 'last_name', 'company_name', 'phone', 'postal_code', 'transaction_id'];

        $table = (new CodePurchase)->getTable();

        return array_values(array_filter(
            $attributes,
            fn (string $column): bool => \Illuminate\Support\Facades\Schema::hasColumn($table, $column),
        ));
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var \App\Models\CodePurchase $record */
        return array_filter([
            'Company' => $record->company_name,
            'Codes' => (string) $record->no_of_codes,
        ]);
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var \App\Models\CodePurchase $record */
        $name = trim(($record->first_name ?? '').' '.($record->last_name ?? ''));

        return $name !== '' ? $name : ($record->email ?: 'Code order #'.$record->getKey());
    }

    public static function infolist(Schema $schema): Schema
    {
        return CodePurchaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CodePurchasesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCodePurchases::route('/'),
            'view' => ViewCodePurchase::route('/{record}'),
        ];
    }
}
