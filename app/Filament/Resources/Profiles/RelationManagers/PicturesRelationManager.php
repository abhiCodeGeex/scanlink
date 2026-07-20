<?php

namespace App\Filament\Resources\Profiles\RelationManagers;

use App\Models\Picture;
use App\Models\Profile;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PicturesRelationManager extends RelationManager
{
  protected static string $relationship = 'pictures';

  protected static ?string $title = 'Pictures';

  public function form(Schema $schema): Schema
  {
    return $schema->components([]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('picture_name')
          ->label('Picture')
          ->disk('public')
          ->state(fn (Picture $record): string => ltrim(str_replace(['storage/', 'public/'], '', $record->picture_name), '/')),
        TextColumn::make('txt_footer')->label('Footer')->placeholder('-'),
        TextColumn::make('created_at')->label('Uploaded')->dateTime('d/m/Y H:i'),
      ])
      ->recordActions([
        DeleteAction::make()
          ->label('Delete')
          ->before(function (Picture $record): void {
            $path = ltrim(str_replace(['storage/', 'public/'], '', $record->picture_name), '/');

            if ($path !== '' && Storage::disk('public')->exists($path)) {
              Storage::disk('public')->delete($path);
            }
          }),
      ])
      ->emptyStateHeading('No pictures uploaded');
  }

  public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
  {
    return str_contains($pageClass, 'EditProfile');
  }
}
