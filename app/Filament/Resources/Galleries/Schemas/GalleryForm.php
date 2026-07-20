<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GalleryForm
{
    public const string STORAGE_DIRECTORY = 'gallery/fullscreen';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Upload images')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Gallery images')
                            ->image()
                            ->multiple()
                            ->required()
                            ->disk('public')
                            ->directory(self::STORAGE_DIRECTORY)
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => time().'_'.str_replace(' ', '_', $file->getClientOriginalName()),
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
