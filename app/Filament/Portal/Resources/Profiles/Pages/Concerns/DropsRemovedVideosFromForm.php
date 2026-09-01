<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use Filament\Forms\Components\Repeater;
use Livewire\Attributes\On;

/**
 * Keeps the profile's Videos list honest when a video is deleted from the client's library.
 *
 * The "Select From Existing" table can delete a video outright. The open profile form still
 * held that video as a row in its Videos / Videos #2 repeater, so the deleted video kept
 * showing in the form — and, because saving re-syncs the repeater to the video table, it
 * would have been written straight back on the next save.
 *
 * The row has to be dropped through the Repeater COMPONENT, not by editing $this->data.
 * A repeater builds its child schemas once and renders from those; rewriting the page's
 * data array updates the state but leaves the rendered list untouched, so the deleted
 * video stayed on screen even after a full $refresh. This mirrors what Filament's own
 * delete action does: rewrite the component's raw state, then partially render it.
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

        foreach (['video_titles', 'video_extra_titles'] as $statePath) {
            // withHidden: "Videos #2" only renders for exhibit codes, and the default
            // lookup skips hidden components — without this the second list was never
            // found and kept its deleted video.
            $component = $this->getSchemaComponent('form.'.$statePath, withHidden: true);

            if (! $component instanceof Repeater) {
                continue;
            }

            $items = $component->getRawState();

            if (! is_array($items)) {
                continue;
            }

            $kept = array_filter(
                $items,
                fn ($item): bool => ! is_array($item)
                    || trim((string) ($item['video_name'] ?? '')) !== $videoName,
            );

            if (count($kept) === count($items)) {
                continue;
            }

            $component->rawState($kept);
            $component->callAfterStateUpdated();

            // Only the list actually on screen needs re-rendering.
            if ($component->isVisible()) {
                $component->partiallyRender();
            }
        }
    }
}
