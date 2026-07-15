<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->name }} — ScanLink</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f5f5f5; color: #222; }
        .wrap { max-width: 640px; margin: 0 auto; padding: 1.5rem; }
        .card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); margin-bottom: 1rem; }
        h1 { color: #008C00; margin-top: 0; }
        h2 { font-size: 1.1rem; margin-top: 1.5rem; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; margin: .25rem .25rem .25rem 0; }
        .btn-outline { background: #fff; color: #008C00; border: 1px solid #008C00; }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input, textarea, select { width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .notice { background: #e8f5e9; color: #1b5e20; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
        .visitor-form { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee; }
        .logo-row { display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: 1rem; }
        .logo-row img { max-height: 80px; max-width: 100%; object-fit: contain; border-radius: 6px; }
        .tile-grid { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: .75rem; margin-top: .75rem; }
        .gallery img { width: 100%; border-radius: 8px; object-fit: cover; aspect-ratio: 1; }
        .video-wrap { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin-top: .75rem; }
        .video-wrap iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        .checklist { list-style: none; padding: 0; margin: .75rem 0 0; }
        .checklist li { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .5rem 0; border-bottom: 1px solid #eee; }
        .checklist .done { color: #2e7d32; text-decoration: line-through; }
        .field-choice { margin-top: .35rem; }
        .field-choice label { display: inline-flex; align-items: center; gap: .35rem; font-weight: 400; margin-top: .35rem; }
        .field-grid { width: 100%; border-collapse: collapse; margin-top: .35rem; font-size: .875rem; }
        .field-grid th, .field-grid td { border: 1px solid #ddd; padding: .35rem .5rem; text-align: center; }
        .display-html { margin: .5rem 0; line-height: 1.5; }
        .form-link-btn { display: inline-block; padding: .55rem 1rem; border-radius: 8px; color: #fff; text-decoration: none; font-weight: 600; margin: .25rem 0; }
        .signature-wrap canvas { width: 100%; max-width: 320px; height: 120px; border: 1px dashed #ccc; border-radius: 6px; touch-action: none; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        @if (session('form_submitted'))
            <div class="notice">Thank you — your form was submitted.</div>
        @endif

        @if ($profile->logos->isNotEmpty())
            <div class="logo-row">
                @foreach ($profile->logos as $logo)
                    @if ($logoUrl = $publicMediaUrl($logo->logo_name))
                        <img src="{{ $logoUrl }}" alt="Company logo">
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->show_header || $profile->name_company)
            <p class="text-sm" style="color:#555;margin:0 0 .5rem;">
                {{ $profile->name_company ?: $profile->client?->client_name }}
            </p>
        @endif

        <h1>{{ $profile->name }}</h1>

        @if ($profile->description)
            <p>{{ $profile->description }}</p>
        @endif

        @if ($profile->address)
            <p><strong>Address:</strong> {{ $profile->address }}</p>
        @endif

        @if ($profile->notes)
            <p><strong>Notes:</strong> {{ $profile->notes }}</p>
        @endif

        @if ($profile->telephone)
            <p><strong>Telephone:</strong> <a href="tel:{{ $profile->telephone }}">{{ $profile->telephone }}</a></p>
        @endif

        @if ($profile->weblinks->isNotEmpty())
            <h2>Links</h2>
            <div class="tile-grid">
                @foreach ($profile->weblinks as $weblink)
                    @if (filled($weblink->link_button_url))
                        <a class="btn" href="{{ $weblink->link_button_url }}" target="_blank" rel="noopener">
                            {{ $weblink->link_button_text ?: ($weblink->link_button ?: 'Open link') }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->documents->isNotEmpty())
            <h2>Documents</h2>
            <div class="tile-grid">
                @foreach ($profile->documents as $document)
                    @if ($docUrl = $publicMediaUrl($document->doc_name))
                        <a class="btn btn-outline" href="{{ $docUrl }}" target="_blank" rel="noopener">
                            {{ $document->name ?: 'Download document' }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->pictures->isNotEmpty())
            <h2>Gallery</h2>
            <div class="gallery">
                @foreach ($profile->pictures as $picture)
                    @if ($picUrl = $publicMediaUrl($picture->picture_name))
                        <figure>
                            <img src="{{ $picUrl }}" alt="{{ $picture->txt_footer ?: 'Picture' }}">
                            @if ($picture->txt_footer)
                                <figcaption style="font-size:.85rem;color:#666;margin-top:.25rem;">{{ $picture->txt_footer }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->videos->isNotEmpty())
            <h2>Videos</h2>
            @foreach ($profile->videos as $video)
                @php $embedUrl = $youtubeEmbedUrl((string) $video->video_name); @endphp
                @if ($embedUrl)
                    <p style="margin:.5rem 0 .25rem;font-weight:600;">{{ $video->title ?: 'Video' }}</p>
                    <div class="video-wrap">
                        <iframe src="{{ $embedUrl }}" allowfullscreen loading="lazy" title="{{ $video->title ?: 'YouTube video' }}"></iframe>
                    </div>
                @elseif (filled($video->video_name))
                    <a class="btn" href="{{ $video->video_name }}" target="_blank" rel="noopener">{{ $video->title ?: 'Watch video' }}</a>
                @endif
            @endforeach
        @endif

        @if ($profile->checklistItems->isNotEmpty())
            <h2>Checklist</h2>
            <ul class="checklist">
                @foreach ($profile->checklistItems as $item)
                    <li>
                        <span class="{{ $item->datetime ? 'done' : '' }}">{{ $item->checklist_item }}</span>
                        @if ($item->datetime)
                            <form method="post" action="{{ route('scan.checklist.uncheck', [$clientUrl, $profile->id, $item->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding:.35rem .6rem;font-size:.85rem;">Uncheck</button>
                            </form>
                        @else
                            <form method="post" action="{{ route('scan.checklist.check', [$clientUrl, $profile->id, $item->id]) }}">
                                @csrf
                                <button type="submit" class="btn" style="padding:.35rem .6rem;font-size:.85rem;">Check</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($needsVisitorInfo)
            <div class="visitor-form">
                <h2>Visitor information</h2>
                <form method="post" action="{{ route('scan.visitor', [$clientUrl, $profile->id]) }}">
                    @csrf
                    @if ($profile->data_collection_name)
                        <label>Name</label>
                        <input type="text" name="user_name" required>
                    @endif
                    @if ($profile->data_collection_email)
                        <label>Email</label>
                        <input type="email" name="user_email" required>
                    @endif
                    @if ($profile->data_collection_mobile)
                        <label>Mobile</label>
                        <input type="text" name="user_mobile">
                    @endif
                    <p style="margin-top:1rem;"><button class="btn" type="submit">Continue</button></p>
                </form>
            </div>
        @endif

        @if ($profile->form_active && $questions->isNotEmpty() && ! $needsVisitorInfo)
            <form method="post" action="{{ route('scan.form', [$clientUrl, $profile->id]) }}" enctype="multipart/form-data" style="margin-top:1.5rem;">
                @csrf
                <h2>{{ $profile->form_title ?: 'Form' }}</h2>
                @foreach ($questions as $question)
                    @php
                        $tid = (int) $question->question_type_id;
                        $options = $question->options;
                        $qid = $question->question_id;
                        $required = $question->is_mandatory ? 'required' : '';
                    @endphp
                    <div style="margin-bottom:1rem;">
                        @switch($tid)
                            @case(1)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <input type="text" name="answers[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(2)
                            @case(13)
                            @case(14)
                                <div class="display-html">{!! $question->question_text !!}</div>
                                @break

                            @case(20)
                                <a class="form-link-btn" style="background:#{{ $question->button_colour ?: '008C00' }};" href="{{ $question->button_link_url ?: '#' }}" target="_blank" rel="noopener">
                                    {{ $question->question_text ?: 'Open link' }}
                                </a>
                                @break

                            @case(21)
                            @case(23)
                                <a class="form-link-btn" style="background:#{{ $question->button_colour ?: '008C00' }};" href="{{ $question->button_link_url ?: '#' }}" target="_blank" rel="noopener">
                                    {{ $question->doc_title ?: $question->question_text ?: 'View document' }}
                                </a>
                                @break

                            @case(3)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <div class="field-choice">
                                    @foreach ($options as $option)
                                        <label>
                                            <input type="radio" name="answers[{{ $qid }}]" value="{{ $option->option_name }}" {{ $required }}>
                                            {{ $option->option_name }}
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @case(4)
                                <label>{{ $question->question_text }}</label>
                                <div class="field-choice">
                                    @foreach ($options as $option)
                                        <label>
                                            <input type="checkbox" name="answers[{{ $qid }}][]" value="{{ $option->option_name }}">
                                            {{ $option->option_name }}
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @case(5)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <select name="answers[{{ $qid }}]" {{ $required }}>
                                    <option value="">Select…</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option->option_name }}">{{ $option->option_name }}</option>
                                    @endforeach
                                </select>
                                @break

                            @case(6)
                                @php
                                    $scaleFrom = (int) ($options->firstWhere('question_option_type_id', 1)?->option_name ?? 1);
                                    $scaleTo = (int) ($options->firstWhere('question_option_type_id', 2)?->option_name ?? 5);
                                    if ($scaleFrom > $scaleTo) { [$scaleFrom, $scaleTo] = [$scaleTo, $scaleFrom]; }
                                @endphp
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <select name="answers[{{ $qid }}]" {{ $required }}>
                                    <option value="">Select…</option>
                                    @for ($i = $scaleFrom; $i <= $scaleTo; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @break

                            @case(7)
                                @php
                                    $rows = $options->where('question_option_type_id', 5);
                                    $cols = $options->where('question_option_type_id', 6);
                                @endphp
                                <label>{{ $question->question_text }}</label>
                                @if ($rows->isNotEmpty() && $cols->isNotEmpty())
                                    <table class="field-grid">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                @foreach ($cols as $col)
                                                    <th>{{ $col->option_name }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                                <tr>
                                                    <th style="text-align:left;">{{ $row->option_name }}</th>
                                                    @foreach ($cols as $col)
                                                        <td>
                                                            <input type="radio" name="answers[{{ $qid }}][{{ $row->option_name }}]" value="{{ $col->option_name }}">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <textarea name="answers[{{ $qid }}]" rows="2" {{ $required }}></textarea>
                                @endif
                                @break

                            @case(8)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <input type="date" name="answers[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(9)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <input type="time" name="answers[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(15)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <textarea name="answers[{{ $qid }}]" rows="3" {{ $required }}></textarea>
                                @break

                            @case(16)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <div class="signature-wrap">
                                    <canvas id="sig-{{ $qid }}" width="320" height="120"></canvas>
                                    <input type="hidden" name="answers[{{ $qid }}]" id="sig-input-{{ $qid }}">
                                    <p style="margin:.35rem 0;"><button type="button" class="btn-outline btn" onclick="clearSig({{ $qid }})">Clear signature</button></p>
                                </div>
                                <textarea name="answers_sig_text[{{ $qid }}]" rows="2" placeholder="Or type your name" {{ $required }}></textarea>
                                @break

                            @case(17)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <input type="file" name="answers_file[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(18)
                                <label>{{ $question->question_text ?: 'Participant name' }}@if($question->is_mandatory) *@endif</label>
                                <input type="text" name="answers[{{ $qid }}]" placeholder="Full name" {{ $required }}>
                                @break

                            @case(19)
                                <label>{{ $question->question_text ?: 'Location' }}@if($question->is_mandatory) *@endif</label>
                                <input type="text" name="answers[{{ $qid }}]" placeholder="Location" {{ $required }}>
                                @break

                            @case(24)
                                <label>{{ $question->question_text ?: 'Email' }}@if($question->is_mandatory) *@endif</label>
                                <input type="email" name="answers[{{ $qid }}]" {{ $required }}>
                                @break

                            @default
                                {{-- COVID/SWMS and unknown types: safe textarea fallback --}}
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <textarea name="answers[{{ $qid }}]" rows="2" {{ $required }}></textarea>
                        @endswitch
                    </div>
                @endforeach
                <p style="margin-top:1rem;"><button class="btn" type="submit">Submit</button></p>
            </form>
            <script>
                (function () {
                    const pads = {};
                    @foreach ($questions as $question)
                        @if ((int) $question->question_type_id === 16)
                            (function initSig{{ $question->question_id }}() {
                                const canvas = document.getElementById('sig-{{ $question->question_id }}');
                                const input = document.getElementById('sig-input-{{ $question->question_id }}');
                                if (!canvas || !input) return;
                                const ctx = canvas.getContext('2d');
                                let drawing = false;
                                const pos = (e) => {
                                    const r = canvas.getBoundingClientRect();
                                    const t = e.touches ? e.touches[0] : e;
                                    return { x: t.clientX - r.left, y: t.clientY - r.top };
                                };
                                const start = (e) => { drawing = true; ctx.beginPath(); const p = pos(e); ctx.moveTo(p.x, p.y); e.preventDefault(); };
                                const draw = (e) => { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); e.preventDefault(); };
                                const end = () => { drawing = false; input.value = canvas.toDataURL('image/png'); };
                                canvas.addEventListener('mousedown', start);
                                canvas.addEventListener('mousemove', draw);
                                canvas.addEventListener('mouseup', end);
                                canvas.addEventListener('mouseleave', end);
                                canvas.addEventListener('touchstart', start, { passive: false });
                                canvas.addEventListener('touchmove', draw, { passive: false });
                                canvas.addEventListener('touchend', end);
                                pads[{{ $question->question_id }}] = { canvas, input, ctx };
                            })();
                        @endif
                    @endforeach
                    window.clearSig = function (qid) {
                        const pad = pads[qid];
                        if (!pad) return;
                        pad.ctx.clearRect(0, 0, pad.canvas.width, pad.canvas.height);
                        pad.input.value = '';
                    };
                })();
            </script>
        @endif
    </div>
</div>
</body>
</html>
