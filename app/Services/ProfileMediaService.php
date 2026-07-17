<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Logo;
use App\Models\Picture;
use App\Models\Profile;
use App\Models\Video;
use App\Services\YouTubeService;

class ProfileMediaService
{
    /**
     * @param  array<string, mixed>  $uploads
     */
    public function syncUploads(Profile $profile, array $uploads): void
    {
        $profile->loadMissing('client');

        if (! empty($uploads['logo_upload'])) {
            $this->syncLogo($profile, (string) $uploads['logo_upload']);
        }

        if (! empty($uploads['picture_uploads'])) {
            $this->syncPictures($profile, (array) $uploads['picture_uploads']);
        }

        if (! empty($uploads['document_uploads'])) {
            $this->syncDocuments($profile, (array) $uploads['document_uploads']);
        }

        if (! empty($uploads['video_titles'])) {
            $this->syncVideos($profile, (array) $uploads['video_titles']);
        }
    }

    protected function syncLogo(Profile $profile, string $path): void
    {
        $profile->logos()->delete();

        $profile->logos()->create([
            'client_id' => $profile->client_id,
            'user_id' => $profile->user_id,
            'logo_name' => $this->relativeStoragePath($path),
            'is_temp' => false,
        ]);
    }

    /**
     * @param  array<int, string>  $paths
     */
    protected function syncPictures(Profile $profile, array $paths): void
    {
        foreach ($paths as $path) {
            $profile->pictures()->create([
                'client_id' => $profile->client_id,
                'user_id' => $profile->user_id,
                'picture_name' => $this->relativeStoragePath((string) $path),
                'txt_footer' => '',
                'is_temp' => false,
            ]);
        }
    }

    /**
     * @param  array<int, string>  $paths
     */
    protected function syncDocuments(Profile $profile, array $paths): void
    {
        foreach ($paths as $path) {
            $relative = $this->relativeStoragePath((string) $path);
            $basename = basename($relative);

            $profile->documents()->create([
                'client_id' => $profile->client_id,
                'user_id' => $profile->user_id,
                'doc_name' => $relative,
                'name' => pathinfo($basename, PATHINFO_FILENAME) ?: $basename,
                'btn_color' => '',
                'txt_align' => 'left',
                'sort_order' => 0,
                'is_temp' => false,
            ]);
        }
    }

    /**
     * @param  array<int, array{title?: string, video_name?: string}>  $videos
     */
    protected function syncVideos(Profile $profile, array $videos): void
    {
        $youtube = app(YouTubeService::class);

        foreach ($videos as $video) {
            if (empty($video['video_name'])) {
                continue;
            }

            $videoId = $youtube->parseVideoId((string) $video['video_name']) ?? (string) $video['video_name'];

            $profile->videos()->create([
                'client_id' => $profile->client_id,
                'user_id' => $profile->user_id,
                'title' => filled($video['title'] ?? null) ? (string) $video['title'] : '',
                'video_name' => $videoId,
                'is_extra' => false,
            ]);
        }
    }

    protected function relativeStoragePath(string $path): string
    {
        return ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
    }
}
