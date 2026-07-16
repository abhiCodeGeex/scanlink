<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    use HandlesDatabaseSaveFailures;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete this testimonial?'),
        ];
    }
}
