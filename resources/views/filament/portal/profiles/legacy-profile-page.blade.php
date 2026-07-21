<x-filament-panels::page>
    {{-- Legacy ScanLink create/edit: form left (500px), iPhone + Form Builder right (450px) --}}
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-5">
    <link rel="stylesheet" href="{{ asset('css/filament/scanlink-theme.css') }}?v=legacy-profile-5">

    <div class="scanlink-container sl-profile-editor clearfix">
        {{-- DOM order matches legacy: right column first, left second --}}
        <section class="add-form-right sl-add-form-right">
            @include('filament.portal.profiles.legacy-preview-sidebar', $this->legacyPreviewData())
        </section>

        <section class="add-form-left sl-add-form-left">
            {{ $this->content }}
        </section>
    </div>

    {{-- Legacy help-icon video tutorial popup --}}
    <div
        id="sl-help-video-modal"
        class="sl-help-video-modal"
        hidden
        aria-hidden="true"
    >
        <div class="sl-help-video-backdrop" data-sl-help-close></div>
        <div class="sl-help-video-window" role="dialog" aria-modal="true" aria-label="Video Tutorial">
            <button type="button" class="sl-help-video-close" data-sl-help-close aria-label="Close">&times;</button>
            <div class="sl-help-video-frame" id="sl-help-video-frame"></div>
        </div>
    </div>

    <style>
        .sl-help-video-modal[hidden] { display: none !important; }
        .sl-help-video-modal {
            position: fixed; inset: 0; z-index: 80;
            display: flex; align-items: center; justify-content: center;
        }
        .sl-help-video-backdrop {
            position: absolute; inset: 0; background: rgba(0,0,0,.55);
        }
        .sl-help-video-window {
            position: relative; z-index: 1; background: #fff;
            padding: 28px 16px 16px; border-radius: 6px;
            box-shadow: 0 8px 30px rgba(0,0,0,.35); width: min(520px, 94vw);
        }
        .sl-help-video-close {
            position: absolute; top: 4px; right: 8px; border: 0; background: transparent;
            font-size: 28px; line-height: 1; cursor: pointer; color: #333;
        }
        .sl-help-video-frame iframe {
            display: block; width: 100%; height: 300px; border: 0;
        }
    </style>
    <script>
        (function () {
            var modal = document.getElementById('sl-help-video-modal');
            var frame = document.getElementById('sl-help-video-frame');
            if (! modal || ! frame) return;

            function openVideo(url) {
                frame.innerHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>';
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeVideo() {
                frame.innerHTML = '';
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
            }

            document.addEventListener('click', function (e) {
                var link = e.target.closest('a.sl-help-video');
                if (link) {
                    e.preventDefault();
                    e.stopPropagation();
                    openVideo(link.getAttribute('data-video') || link.getAttribute('href'));
                    return;
                }
                if (e.target.closest('[data-sl-help-close]')) {
                    closeVideo();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && ! modal.hidden) {
                    closeVideo();
                }
            });
        })();
    </script>
</x-filament-panels::page>
