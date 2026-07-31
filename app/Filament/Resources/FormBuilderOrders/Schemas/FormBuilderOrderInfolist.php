<?php

namespace App\Filament\Resources\FormBuilderOrders\Schemas;

use App\Enums\CodeOrderStatus;
use App\Models\FormBuilderOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormBuilderOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Form Builder Order Status')
                            ->badge()
                            ->formatStateUsing(fn (CodeOrderStatus $state): string => $state->label())
                            ->color(fn (CodeOrderStatus $state): string => $state->color()),
                        TextEntry::make('id')->label('Order Number'),
                        TextEntry::make('created_at')->label('Date')->dateTime('d/m/Y H:i:s'),
                    ]),
                Section::make('Order Detail')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('no_of_codes')->label('Number Of Item'),
                        TextEntry::make('per_code_amount')
                            ->label('Price Per Item')
                            ->state(fn (FormBuilderOrder $record): string => '$'.number_format((float) $record->per_code_amount, 2)),
                        TextEntry::make('total_amount_display')
                            ->label('Total')
                            ->state(fn (FormBuilderOrder $record): string => '$'.number_format($record->totalAmount(), 2)),
                    ]),
                Section::make('Billing Detail')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('company_name')->label('Company Name'),
                        TextEntry::make('billing_address')->label('Billing Address'),
                        TextEntry::make('town')->label('Town'),
                        TextEntry::make('postal_code')->label('Postal Code'),
                        TextEntry::make('email')->label('E-mail'),
                        TextEntry::make('phone')->label('Contact'),
                    ]),
            ]);
    }
}
