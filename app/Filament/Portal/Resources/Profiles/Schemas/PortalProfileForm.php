<?php

namespace App\Filament\Portal\Resources\Profiles\Schemas;

use App\Filament\Resources\Profiles\Schemas\ProfileFormSchema;
use App\Models\ClientUser;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PortalProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        $sharedSections = array_slice(ProfileFormSchema::components(), 1);

        return $schema->components([
            Section::make('Profile type & owner')
                ->columns(2)
                ->schema([
                    Select::make('type_id')
                        ->label('Select Profile Type')
                        ->relationship('equipmentType', 'name', fn ($query) => $query->where('slag', '!=', 'people'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    Hidden::make('client_id')
                        ->required(),
                    Select::make('user_id')
                        ->label('Profile owner')
                        ->options(fn (Get $get): array => ClientUser::query()
                            ->where('client_id', $get('client_id'))
                            ->pluck('email', 'id')
                            ->all())
                        ->searchable()
                        ->visible(fn (Get $get): bool => filled($get('client_id'))),
                ]),
            ...$sharedSections,
        ]);
    }
}
