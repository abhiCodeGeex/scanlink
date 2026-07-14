<?php

namespace App\Filament\Resources\FormBuilderOrders;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\FormBuilderOrders\Pages\ListFormBuilderOrders;
use App\Filament\Resources\FormBuilderOrders\Pages\ViewFormBuilderOrder;
use App\Filament\Resources\FormBuilderOrders\Schemas\FormBuilderOrderInfolist;
use App\Filament\Resources\FormBuilderOrders\Tables\FormBuilderOrdersTable;
use App\Models\FormBuilderOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FormBuilderOrderResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = FormBuilderOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Manage Form Builder Order';

    protected static string|UnitEnum|null $navigationGroup = 'Order';

    protected static ?int $navigationSort = 3;

    public static function infolist(Schema $schema): Schema
    {
        return FormBuilderOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormBuilderOrdersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('details');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormBuilderOrders::route('/'),
            'view' => ViewFormBuilderOrder::route('/{record}'),
        ];
    }
}
