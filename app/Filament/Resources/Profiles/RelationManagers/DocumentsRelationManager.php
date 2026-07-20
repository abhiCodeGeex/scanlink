<?php

namespace App\Filament\Resources\Profiles\RelationManagers;

use App\Models\Document;
use App\Models\Profile;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
  protected static string $relationship = 'documents';

  protected static ?string $title = 'Documents';

  public function form(Schema $schema): Schema
  {
    return $schema->components([]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->label('Name'),
        TextColumn::make('doc_name')
          ->label('File')
          ->url(fn (Document $record): ?string => self::publicUrl($record->doc_name))
          ->openUrlInNewTab(),
        TextColumn::make('created_at')->label('Uploaded')->dateTime('d/m/Y H:i'),
      ])
      ->recordActions([
        DeleteAction::make()
          ->label('Delete')
          ->before(function (Document $record): void {
            $path = ltrim(str_replace(['storage/', 'public/'], '', $record->doc_name), '/');

            if ($path !== '' && Storage::disk('public')->exists($path)) {
              Storage::disk('public')->delete($path);
            }
          }),
      ])
      ->emptyStateHeading('No documents uploaded');
  }

  public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
  {
    return str_contains($pageClass, 'EditProfile');
  }

  protected static function publicUrl(?string $path): ?string
  {
    if (blank($path)) {
      return null;
    }

    $diskPath = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');

    return $diskPath !== '' ? asset('storage/'.$diskPath) : null;
  }
}
