<?php

namespace App\Filament\Resources\CodePurchases\Schemas;

use App\Enums\CodeOrderStatus;
use App\Models\CodePurchase;
use App\Support\CodePurchasePricing;
use App\Support\CodePurchaseResellerDetails;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CodePurchaseInfolist
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
                            ->formatStateUsing(fn (CodeOrderStatus $state): string => $state->label())
                            ->color(fn (CodeOrderStatus $state): string => $state->color()),
                        TextEntry::make('id')
                            ->label('Code ID'),
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime('d/m/Y H:i:s'),
                    ]),
                Section::make('Order Detail')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('no_of_codes')
                            ->label('Number Of Codes'),
                        TextEntry::make('per_code_amount_display')
                            ->label('Price Per Code')
                            ->state(function (CodePurchase $record): string {
                                $summary = CodePurchasePricing::summarize($record);

                                return implode('<br>', $summary['lines']);
                            })
                            ->html(),
                        TextEntry::make('grand_total_display')
                            ->label('Grand Total')
                            ->state(fn (CodePurchase $record): string => sprintf(
                                '$%s AUD',
                                number_format(CodePurchasePricing::summarize($record)['grand_total'], 2),
                            )),
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
                Section::make('Reseller Detail')
                    ->columns(2)
                    ->visible(fn (CodePurchase $record): bool => CodePurchaseResellerDetails::forOrder($record) !== null)
                    ->schema([
                        TextEntry::make('reseller_first_name')
                            ->label('First Name')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->first_name ?? '-'),
                        TextEntry::make('reseller_last_name')
                            ->label('Last Name')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->last_name ?? '-'),
                        TextEntry::make('reseller_company')
                            ->label('Company Name')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->company_name ?? '-'),
                        TextEntry::make('reseller_billing')
                            ->label('Billing Address')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->billing_address ?? '-'),
                        TextEntry::make('reseller_town')
                            ->label('Town')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->town ?? '-'),
                        TextEntry::make('reseller_postal')
                            ->label('Postal Code')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->postal_code ?? '-'),
                        TextEntry::make('reseller_email')
                            ->label('E-mail')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->email ?? '-'),
                        TextEntry::make('reseller_phone')
                            ->label('Contact')
                            ->state(fn (CodePurchase $record): string => CodePurchaseResellerDetails::forOrder($record)?->phone ?? '-'),
                    ]),
            ]);
    }
}
