<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    use HandlesDatabaseSaveFailures;

    protected static string $resource = TestimonialResource::class;
}
