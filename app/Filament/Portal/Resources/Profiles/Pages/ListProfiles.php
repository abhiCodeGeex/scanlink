<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use App\Models\Profile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProfiles extends ListRecords
{
    use InteractsWithClientMembership;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Master Code List';

    /**
     * @var list<string>
     */
    protected static array $typeTabSlags = [
        'plant',
        'location',
        'asset',
        'product',
        'procedure',
        'misc',
        'code',
        'survey',
        'exhibit',
        'voc',
        'customqr',
    ];

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add profile')
                ->visible(fn (): bool => ProfileResource::canCreate()),
        ];
    }

    public function getTabs(): array
    {
        $clientId = $this->currentClient()?->id;

        $baseQuery = Profile::query()
            ->when($clientId, fn (Builder $query): Builder => $query->where('client_id', $clientId))
            ->active();

        $tabs = [
            'all' => Tab::make('All')
                ->badge((clone $baseQuery)->count()),
        ];

        $types = EquipmentType::query()
            ->whereIn('slag', self::$typeTabSlags)
            ->get()
            ->sortBy(fn (EquipmentType $type): int => array_search($type->slag, self::$typeTabSlags, true) ?: 999);

        foreach ($types as $type) {
            $tabs[$type->slag] = Tab::make($type->name)
                ->badge((clone $baseQuery)->where('type_id', $type->id)->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('type_id', $type->id));
        }

        return $tabs;
    }
}
