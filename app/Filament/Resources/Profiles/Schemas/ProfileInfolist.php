<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Models\Profile;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('Profile No.'),
                        TextEntry::make('equipmentType.name')->label('Profile Type'),
                        ImageEntry::make('logo_preview')
                            ->label('Company logo')
                            ->state(fn (Profile $record): ?string => self::mediaDiskPath($record->logos->first()?->logo_name))
                            ->disk('public')
                            ->placeholder('No Logo !')
                            ->columnSpanFull(),
                        TextEntry::make('name')->label('Name'),
                        TextEntry::make('identification')->label('Identification')->placeholder('-'),
                        TextEntry::make('serial_no')->label('Serial No.')->placeholder('-'),
                        TextEntry::make('address')->label('Address')->placeholder('-'),
                        TextEntry::make('description')->label('Description')->placeholder('-'),
                        TextEntry::make('notes')->label('Notes')->placeholder('-'),
                    ]),
                Section::make('Videos')
                    ->schema([
                        RepeatableEntry::make('videos')
                            ->label('')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title')
                                    ->url(fn ($record): string => 'https://www.youtube.com/watch?v='.$record->video_name)
                                    ->openUrlInNewTab(),
                            ])
                            ->placeholder('No Videos !'),
                    ]),
                Section::make('Web links')
                    ->schema([
                        RepeatableEntry::make('weblinks')
                            ->label('')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('link_button_text')->label('Button text'),
                                TextEntry::make('link_button_url')->label('URL'),
                                TextEntry::make('link_button_color')->label('Color'),
                            ])
                            ->placeholder('No web link !'),
                    ]),
                Section::make('Pictures')
                    ->schema([
                        RepeatableEntry::make('pictures')
                            ->label('')
                            ->schema([
                                ImageEntry::make('picture_preview')
                                    ->label('')
                                    ->state(fn ($record): ?string => self::mediaDiskPath($record->picture_name))
                                    ->disk('public'),
                                TextEntry::make('txt_footer')->label('Footer')->placeholder('-'),
                            ])
                            ->placeholder('No Pictures !'),
                    ]),
                Section::make('Documents')
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')->label('Name'),
                                TextEntry::make('doc_link')
                                    ->label('Document')
                                    ->state(fn ($record): string => $record->doc_name)
                                    ->url(fn ($record): ?string => self::mediaPublicUrl($record->doc_name))
                                    ->openUrlInNewTab(),
                            ])
                            ->placeholder('No Documents !'),
                    ]),
                Section::make('Contacts')
                    ->schema([
                        TextEntry::make('contacts.name_company')
                            ->label('Contact Name/Company')
                            ->listWithLineBreaks(),
                        TextEntry::make('contacts.telephone')
                            ->label('Telephone')
                            ->listWithLineBreaks(),
                    ]),
                Section::make('QR')
                    ->schema([
                        ImageEntry::make('qr_preview')
                            ->label('QR Img')
                            ->state(fn (Profile $record): ?string => $record->qrImage?->diskPath())
                            ->disk('public')
                            ->visible(fn (Profile $record): bool => $record->qrImage !== null),
                    ]),
                Section::make('Data collection')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('enable_data_collection')->label('Enable data collection')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('set_up_compulsory')->label('Set as compulsory')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('data_collection_mobile')->label('Mobile')->placeholder('-'),
                        TextEntry::make('data_collection_email')->label('Email')->placeholder('-'),
                        TextEntry::make('data_collection_name')->label('Name')->placeholder('-'),
                        TextEntry::make('data_collection_content')->label('Content')->placeholder('-'),
                    ]),
                Section::make('Code & security')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code_type')
                            ->label('Code Type')
                            ->formatStateUsing(fn (?ProfileCodeType $state): string => $state?->label() ?? '-'),
                        TextEntry::make('show_header')->label('Show header in mobile')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('protect')->label('Password protect?')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('display_share_link')->label('Display share links')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                    ]),
            ]);
    }

    protected static function mediaDiskPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
    }

    protected static function mediaPublicUrl(?string $path): ?string
    {
        $diskPath = self::mediaDiskPath($path);

        return $diskPath ? asset('storage/'.$diskPath) : null;
    }
}
