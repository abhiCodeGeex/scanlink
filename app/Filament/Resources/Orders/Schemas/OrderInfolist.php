<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\PhysicalOrderStatus;
use App\Models\Order;
use App\Support\PhysicalOrderPricing;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Order Status')
                            ->badge()
                            ->formatStateUsing(fn (PhysicalOrderStatus $state): string => $state->label())
                            ->color(fn (PhysicalOrderStatus $state): string => $state->color()),
                        TextEntry::make('transaction_id')
                            ->label('Transaction Id')
                            ->visible(fn (Order $record): bool => $record->status !== PhysicalOrderStatus::New
                                && filled($record->transaction_id)),
                        TextEntry::make('ordered_on')
                            ->label('Date')
                            ->dateTime('d/m/Y H:i:s'),
                    ]),
                Section::make('Profile Detail')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('profile_id')->label('Code Number'),
                        TextEntry::make('profile.name')->label('Name')->placeholder('-'),
                        TextEntry::make('profile.identification')->label('Identification')->placeholder('-'),
                        TextEntry::make('profile.serial_no')->label('Serial No')->placeholder('-'),
                        TextEntry::make('profile.description')->label('Description')->placeholder('-'),
                        TextEntry::make('profile.notes')->label('Notes')->placeholder('-'),
                        TextEntry::make('profile.name_company')->label('Company Name')->placeholder('-'),
                        TextEntry::make('profile.telephone')->label('Telephone')->placeholder('-'),
                        TextEntry::make('profile.address')->label('Address')->placeholder('-'),
                    ]),
                Section::make('Order Detail')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('small_line')
                            ->label('Item')
                            ->state(fn (Order $record): string => '1) '.PhysicalOrderPricing::summarize($record)['small_label']),
                        TextEntry::make('small_qty')
                            ->label('Quantity')
                            ->state(fn (Order $record): int => $record->qty_small),
                        TextEntry::make('small_price')
                            ->label('Unit Price')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format((float) $record->price_small, 2))),
                        TextEntry::make('small_total')
                            ->label('Total')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format(PhysicalOrderPricing::summarize($record)['small_total'], 2))),
                        TextEntry::make('large_line')
                            ->label('Item')
                            ->state(fn (Order $record): string => '2) '.PhysicalOrderPricing::summarize($record)['big_label']),
                        TextEntry::make('large_qty')
                            ->label('Quantity')
                            ->state(fn (Order $record): int => $record->qty_large),
                        TextEntry::make('large_price')
                            ->label('Unit Price')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format((float) $record->price_large, 2))),
                        TextEntry::make('large_total')
                            ->label('Total')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format(PhysicalOrderPricing::summarize($record)['large_total'], 2))),
                        TextEntry::make('order_subtotal')
                            ->label('Total')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format(PhysicalOrderPricing::summarize($record)['subtotal'], 2))),
                        TextEntry::make('postage')
                            ->label('Postage & Handling')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format(PhysicalOrderPricing::summarize($record)['postage'], 2))),
                        TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->state(fn (Order $record): string => sprintf('$%s AUD', number_format(PhysicalOrderPricing::summarize($record)['grand_total'], 2))),
                    ]),
                Section::make('Shipping Detail')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('address1')->label('Address 1'),
                        TextEntry::make('address2')->label('Address 2'),
                        TextEntry::make('city')->label('City'),
                        TextEntry::make('state')->label('State'),
                        TextEntry::make('zip')->label('Zip'),
                        TextEntry::make('country')->label('Country'),
                        TextEntry::make('email')->label('E-mail'),
                        TextEntry::make('contact')->label('Contact'),
                    ]),
            ]);
    }
}
