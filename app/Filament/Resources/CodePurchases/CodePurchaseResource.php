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
