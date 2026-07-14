<?php

namespace App\Filament\Resources\Galleries\Tables;

use App\Enums\AdminRole;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Models\Gallery;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Image ID')
                    ->sortable(),
                ImageColumn::make('name')
                    ->label('Image')
                    ->disk('public')
                    ->getStateUsing(fn (Gallery $record): ?string => self::thumbnailPath($record))
                    ->imageHeight(80)
                    ->checkFileExistence(false),
            ])
            ->recordActions([
                Action::make('toggleApproval')
                    ->label(fn (Gallery $record): string => $record->approve ? 'Block' : 'Unblock')
                    ->icon(fn (Gallery $record): string => $record->approve ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (Gallery $record): string => $record->approve ? 'danger' : 'success')
                    ->visible(fn (): bool => auth()->user()?->admin_role instanceof AdminRole
                        && auth()->user()->admin_role->canWrite())
                    ->requiresConfirmation()
                    ->modalHeading(fn (Gallery $record): string => $record->approve ? 'Block this image?' : 'Unblock this image?')
                    ->action(fn (Gallery $record) => $record->update(['approve' => ! $record->approve])),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete this image?')
                    ->before(function (Gallery $record): void {
                        self::deleteStoredFiles($record);
                    }),
            ]);
    }

    public static function thumbnailPath(Gallery $record): ?string
    {
        if (! filled($record->name)) {
            return null;
        }

        // Prefer thumb path without Storage::exists() — exists() is very slow on Windows bind mounts.
        // ImageColumn already uses checkFileExistence(false).
        return GalleryForm::STORAGE_DIRECTORY.'/thumb_'.$record->name;
    }

    public static function deleteStoredFiles(Gallery $record): void
    {
        $disk = Storage::disk('public');

        $disk->delete([
            GalleryForm::STORAGE_DIRECTORY.'/'.$record->name,
            GalleryForm::STORAGE_DIRECTORY.'/thumb_'.$record->name,
        ]);
    }
}
