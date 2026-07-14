<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Testimonial')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('video')
                            ->label('Video')
                            ->html(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/m/Y H:i:s'),
                    ]),
            ]);
    }
}
