<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use Livewire\Attributes\On;

/**
 * Keeps the profile's Videos list honest when a video is deleted from the client's library.
 *
 * The "Select From Existing" table can delete a video outright. The open profile form still
 * held that video as a row in its Videos / Videos #2 repeater, so the deleted video kept
 * showing in the form — and, because saving re-syncs the repeater to the video table, it
 * would have been written straight back on the next save.
 */
trait DropsRemovedVideosFromForm
{
    #[On('sl-video-removed')]
    public function dropRemovedVideoFromForm(string $videoName = '', string $title = ''): void
    {
        $videoName = trim($videoName);

        if ($videoName === '') {
            return;
        }

        foreach (['video_titles', 'video_extra_titles'] as $key) {
            $items = $this->data[$key] ?? null;

            if (! is_array($items)) {
                continue;
            }

            // Repeater items are keyed by uuid — preserve the keys so the rest of the
            // repeater state stays addressable.
            $this->data[$key] = array_filter(
                $items,
                fn ($item): bool => ! is_array($item)
                    || trim((string) ($item['video_name'] ?? '')) !== $videoName,
            );
        }
    }
}
