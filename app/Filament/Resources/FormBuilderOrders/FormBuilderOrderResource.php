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

    protected static ?string $recordTitleAttribute = 'email';

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['email', 'first_name', 'last_name', 'phone', 'postal_code'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        /** @var \App\Models\FormBuilderOrder $record */
        return array_filter([
            'Phone' => $record->phone,
            'Status' => is_object($record->status) && method_exists($record->status, 'label')
                ? $record->status->label()
                : (string) $record->status,
        ]);
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string|\Illuminate\Contracts\Support\Htmlable
    {
        /** @var \App\Models\FormBuilderOrder $record */
        $name = trim(($record->first_name ?? '').' '.($record->last_name ?? ''));

        return $name !== '' ? $name : ($record->email ?: 'FB order #'.$record->getKey());
    }

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
