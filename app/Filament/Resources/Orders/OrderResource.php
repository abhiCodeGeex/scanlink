<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Concerns\AuthorizesAdminRole;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    use AuthorizesAdminRole;

    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Manage Order';

    protected static string|UnitEnum|null $navigationGroup = 'Order';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'email';

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'first_name', 'last_name', 'contact', 'zip', 'profile_id', 'transaction_id'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var \App\Models\Order $record */
        return array_filter([
            'Code #' => (string) $record->profile_id,
            'Status' => $record->status instanceof \App\Enums\PhysicalOrderStatus
                ? $record->status->label()
                : (string) $record->status,
        ]);
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var \App\Models\Order $record */
        $name = trim(($record->first_name ?? '').' '.($record->last_name ?? ''));

        return $name !== '' ? $name : ($record->email ?: 'Order #'.$record->getKey());
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
