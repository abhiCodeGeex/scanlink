<?php

namespace App\Filament\Resources\ResellerCodes\Pages;

use App\Filament\Resources\ResellerCodes\ResellerCodeResource;
use App\Models\CodePurchase;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

/**
 * Admin: usage history for a single reseller code (owned by $this->record, a Client).
 * A "use" is a code_purchase whose reseller_client_id points at the owning client, so
 * this lists which clients used the code and how many times.
 */
class ResellerCodeHistory extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ResellerCodeResource::class;

    protected string $view = 'filament.resources.reseller-codes.pages.reseller-code-history';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Usage history — '.($this->record->reseller_code ?: 'Reseller code');
    }

    public function getHeader(): ?View
    {
        return view('filament.resources.reseller-codes.partials.history-page-header', [
            'record' => $this->record,
            'breadcrumbs' => filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : [],
            'scopes' => $this->getRenderHookScopes(),
        ]);
    }

    public function getBreadcrumb(): string
    {
        return 'History';
    }

    public function table(Table $table): Table
    {
        // Aggregate per purchasing client. Wrap the GROUP BY in a subquery aliased to
        // the model table so Filament's implicit "order by code_purchase.id" tiebreaker
        // resolves to the derived id column (avoids ONLY_FULL_GROUP_BY errors).
        $usagePerClient = CodePurchase::query()
            ->where('reseller_client_id', $this->record->getKey())
            ->selectRaw('MIN(id) as id, client_id, COUNT(*) as uses_count, SUM(no_of_codes) as codes_total, SUM(total_amount) as amount_total, MAX(created_at) as last_used_at')
            ->groupBy('client_id');

        return $table
            ->query(CodePurchase::query()->fromSub($usagePerClient, 'code_purchase'))
            ->heading('Usage by client')
            ->description('Each client that has purchased codes using this reseller code.')
            ->columns([
                TextColumn::make('client.client_name')
                    ->label('Client')
                    ->placeholder('—')
                    ->weight('medium')
                    ->description(fn (CodePurchase $record): ?string => $record->client?->email ?: null)
                    ->sortable(),
                TextColumn::make('uses_count')
                    ->label('Uses')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('codes_total')
                    ->label('Codes')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('amount_total')
                    ->label('Amount')
                    // Legacy shows a plain "$" (no AUD "A$" prefix).
                    ->formatStateUsing(fn ($state): string => '$'.number_format((float) $state, 2))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->dateTime('d/m/Y H:i')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->defaultSort('uses_count', 'desc')
            ->emptyStateHeading('No usage yet')
            ->emptyStateDescription('This reseller code has not been used for any purchase.');
    }
}
