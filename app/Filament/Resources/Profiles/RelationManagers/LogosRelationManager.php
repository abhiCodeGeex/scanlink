<?php

namespace App\Filament\Resources\Profiles\RelationManagers;

use App\Models\Logo;
use App\Models\Profile;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LogosRelationManager extends RelationManager
{
  protected static string $relationship = 'logos';

  protected static ?string $title = 'Company logo';

  public function form(Schema $schema): Schema
  {
    return $schema->components([]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('logo_name')
          ->label('Logo')
          ->disk('public')
          ->state(fn (Logo $record): string => ltrim(str_replace(['storage/', 'public/'], '', $record->logo_name), '/')),
        TextColumn::make('created_at')->label('Uploaded')->dateTime('d/m/Y H:i'),
      ])
      ->recordActions([
        DeleteAction::make()
          ->label('Remove logo')
          ->modalHeading('Remove bridge / company logo?')
          ->before(function (Logo $record): void {
            $path = ltrim(str_replace(['storage/', 'public/'], '', $record->logo_name), '/');

            if ($path !== '' && Storage::disk('public')->exists($path)) {
              Storage::disk('public')->delete($path);
            }
          }),
      ])
      ->emptyStateHeading('No logo uploaded');
  }

  public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
  {
    return str_contains($pageClass, 'EditProfile');
  }
}
