<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Models\Gallery;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreateGallery extends CreateRecord
{
    use HandlesDatabaseSaveFailures;

    protected static string $resource = GalleryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $images = $data['images'] ?? [];
        $firstRecord = null;

        foreach ($images as $storedPath) {
            $filename = basename($storedPath);

            $record = Gallery::query()->create([
                'name' => $filename,
                'approve' => true,
            ]);

            self::generateThumbnail($filename);

            $firstRecord ??= $record;
        }

        return $firstRecord ?? Gallery::query()->make();
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Gallery image(s) added successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return GalleryResource::getUrl('index');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected static function generateThumbnail(string $filename): void
    {
        $disk = Storage::disk('public');
        $sourcePath = $disk->path(GalleryForm::STORAGE_DIRECTORY.'/'.$filename);
        $thumbPath = $disk->path(GalleryForm::STORAGE_DIRECTORY.'/thumb_'.$filename);

        if (! is_file($sourcePath) || ! function_exists('imagecreatefromstring')) {
            return;
        }

        $contents = @file_get_contents($sourcePath);

        if ($contents === false) {
            return;
        }

        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $targetWidth = 200;
        $targetHeight = (int) round(($height / max($width, 1)) * $targetWidth);
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height,
        );

        imagejpeg($thumbnail, $thumbPath, 100);

        imagedestroy($image);
        imagedestroy($thumbnail);
    }

}
