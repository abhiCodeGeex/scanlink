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
        .field-choice input { width: auto; margin: 0; }
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
            <form method="post" action="{{ route('scan.form', [$clientUrl, $profile->id]) }}" style="margin-top:1.5rem;">
                @csrf
                <h2>Form</h2>
                @foreach ($questions as $question)
                    @php
                        $type = $question->questionType?->type ?? 'text';
                        $options = $question->options;
                    @endphp
                    <div style="margin-bottom:1rem;">
                        <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>

                        @if (in_array($type, ['radio', 'select', 'checkbox'], true) && $options->isNotEmpty())
                            @if ($type === 'select')
                                <select name="answers[{{ $question->question_id }}]" @if($question->is_mandatory) required @endif>
                                    <option value="">Select…</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option->option_name }}">{{ $option->option_name }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'radio')
                                <div class="field-choice">
                                    @foreach ($options as $option)
                                        <label>
                                            <input type="radio" name="answers[{{ $question->question_id }}]" value="{{ $option->option_name }}" @if($question->is_mandatory) required @endif>
                                            {{ $option->option_name }}
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="field-choice">
                                    @foreach ($options as $option)
                                        <label>
                                            <input type="checkbox" name="answers[{{ $question->question_id }}][]" value="{{ $option->option_name }}">
                                            {{ $option->option_name }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($type === 'textarea')
                            <textarea name="answers[{{ $question->question_id }}]" rows="3" @if($question->is_mandatory) required @endif></textarea>
                        @else
                            <input type="{{ $type === 'date' ? 'date' : 'text' }}" name="answers[{{ $question->question_id }}]" @if($question->is_mandatory) required @endif>
                        @endif
                    </div>
                @endforeach
                <p style="margin-top:1rem;"><button class="btn" type="submit">Submit</button></p>
            </form>
        @endif
    </div>
</div>
</body>
</html>
