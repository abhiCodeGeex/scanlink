@php
    // Client's video catalogue (legacy "Select From Existing" table), newest first.
    $clientId = \App\Filament\Portal\Concerns\InteractsWithClientMembership::portalMembership()?->client_id;
    $youtube = app(\App\Services\YouTubeService::class);

    $videoRows = $clientId
        ? \App\Models\Video::query()
            ->where('client_id', $clientId)
            ->orderByDesc('id')
            ->get()
            ->map(function (\App\Models\Video $v) use ($youtube): array {
                $name = trim((string) $v->video_name);
                $url = null;
                if (preg_match('/\.(mp4|m4v|mov|webm|ogg)$/i', $name)) {
                    $url = \App\Support\PublicMediaPath::url(str_contains($name, '/') ? $name : 'images/video/'.$name);
                } elseif (($id = $youtube->parseVideoId($name)) !== null) {
                    $url = $youtube->watchUrl($id);
                }

                return [
                    'id' => (int) $v->id,
                    'title' => trim((string) ($v->title ?: $v->video_name)) ?: 'Video',
                    'profile' => (int) $v->profile_id,
                    'url' => $url,
                ];
            })
            ->values()
            ->all()
        : [];
@endphp

<div
    x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}'),
        videos: @js($videoRows),
        page: 1,
        perPage: 8,
        removingId: null,
        get pages() { return Math.max(1, Math.ceil(this.videos.length / this.perPage)) },
        get slice() { return this.videos.slice((this.page - 1) * this.perPage, this.page * this.perPage) },
        async removeVideo(id) {
            if (! confirm('Remove this video from your library? Profiles using it will lose it.')) return;
            this.removingId = id;
            try {
                const res = await fetch('{{ url('/portal/videos') }}/' + id + '/remove', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                if (res.ok) {
                    this.videos = this.videos.filter(v => v.id !== id);
                    if (this.state == id) this.state = null;
                    if (this.page > this.pages) this.page = this.pages;
                } else {
                    alert('Could not remove the video.');
                }
            } catch (e) { alert('Could not remove the video.'); }
            this.removingId = null;
        },
    }"
    class="sl-existing-videos"
>
    <style>
        .sl-existing-videos { font-size: 13px; }
        .sl-existing-videos table { width: 100%; border-collapse: collapse; }
        .sl-existing-videos th {
            text-align: left; font-size: 11.5px; text-transform: uppercase; letter-spacing: .02em;
            color: #6b7280; padding: 6px 8px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;
        }
        .sl-existing-videos td { padding: 7px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .sl-existing-videos tr:hover td { background: #f7faf7; }
        .sl-existing-videos .sl-ev-title { font-weight: 600; color: #111827; max-width: 190px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .sl-existing-videos a.sl-ev-watch { color: #008C00; font-weight: 600; text-decoration: none; font-size: 12px; }
        .sl-existing-videos a.sl-ev-watch:hover { text-decoration: underline; }
        .sl-existing-videos .sl-ev-remove {
            border: 1px solid #fecaca; color: #b91c1c; background: #fff; border-radius: 6px;
            font-size: 11.5px; font-weight: 600; padding: 3px 9px; cursor: pointer;
        }
        .sl-existing-videos .sl-ev-remove:hover { background: #fef2f2; }
        .sl-existing-videos .sl-ev-paging { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding: 8px 2px 0; color: #6b7280; font-size: 12px; }
        .sl-existing-videos .sl-ev-paging button {
            border: 1px solid #d1d5db; background: #fff; border-radius: 6px; padding: 3px 10px;
            font-size: 12px; font-weight: 600; color: #374151; cursor: pointer;
        }
        .sl-existing-videos .sl-ev-paging button:disabled { opacity: .45; cursor: default; }
        .sl-existing-videos .sl-ev-empty { color: #6b7280; padding: 10px 4px; }
        .sl-existing-videos input[type="radio"] { accent-color: #008C00; width: 15px; height: 15px; cursor: pointer; }
    </style>

    <template x-if="videos.length === 0">
        <div class="sl-ev-empty">You have no videos in your library yet.</div>
    </template>

    <template x-if="videos.length > 0">
        <div>
            <table>
                <thead>
                    <tr>
                        <th style="width:34px;"></th>
                        <th>Title</th>
                        <th style="width:80px;">Profile</th>
                        <th style="width:70px;"></th>
                        <th style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="v in slice" :key="v.id">
                        <tr>
                            <td><input type="radio" name="sl-existing-video-pick" :value="v.id" x-model="state"></td>
                            <td><span class="sl-ev-title" :title="v.title" x-text="v.title"></span></td>
                            <td x-text="'#' + v.profile"></td>
                            <td>
                                <a class="sl-ev-watch" x-show="v.url" :href="v.url" target="_blank" rel="noopener" @click.stop>Watch</a>
                            </td>
                            <td>
                                <button type="button" class="sl-ev-remove" :disabled="removingId === v.id" @click="removeVideo(v.id)" x-text="removingId === v.id ? '…' : 'Remove'"></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="sl-ev-paging" x-show="pages > 1">
                <span x-text="'Page ' + page + ' of ' + pages"></span>
                <button type="button" :disabled="page <= 1" @click="page--">Prev</button>
                <button type="button" :disabled="page >= pages" @click="page++">Next</button>
            </div>
        </div>
    </template>
</div>
