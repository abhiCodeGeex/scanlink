<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->name }} — ScanLink</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; background: #f5f5f5; color: #222; font-size: 14px; line-height: 22px; }
        .wrap { max-width: 640px; margin: 0 auto; padding: 1.5rem; }
        .card { background: #fff; border-radius: 4px; padding: 1rem 1.1rem 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,.12); margin-bottom: .75rem; }
        /* Legacy MobileBottomText: label then value — not a green form-name hero. */
        .MobileBottomText h3 { margin: 0 0 7px 0; font-size: 1rem; font-weight: 700; color: #222; }
        .MobileBottomText p { margin: 0 0 7px 0; color: #222; }
        h1 { color: #222; margin: 0 0 .5rem; font-size: 1.25rem; }
        h2 { font-size: 1.1rem; margin-top: 1.5rem; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; margin: .25rem .25rem .25rem 0; }
        .btn-outline { background: #fff; color: #008C00; border: 1px solid #008C00; }
        /* Legacy WeblinkList / document tiles: full-width colored bars with text alignment. */
        .btn-weblink,
        .btn-document {
            display: block;
            width: 100%;
            box-sizing: border-box;
            border-radius: 8px;
            margin: .25rem 0;
            color: #fff !important;
        }
        .btn-weblink a,
        .btn-document a { color: inherit; text-decoration: none; }
        /* Legacy shareNav-mob icons */
        .shareNav-mob {
            width: 100%;
            display: block;
            text-align: center;
            clear: both;
            margin: 1rem 0 0.5rem;
        }
        .shareNav-mob a {
            width: 48px;
            height: 48px;
            text-indent: -99999px;
            margin: 0 10px;
            display: inline-block;
            background-position: top left;
            background-repeat: no-repeat;
            background-size: 48px 48px;
        }
        .shareNav-mob a.shareFB { background-image: url('{{ asset('images/facebook-icon.jpg') }}'); }
        .shareNav-mob a.shareTWT { background-image: url('{{ asset('images/twitter-icon.jpg') }}'); }
        .shareNav-mob a.shareEML { background-image: url('{{ asset('images/email-grn-icon.jpg') }}'); }
        /* Legacy mobile form Submit: native-looking button (white / black border). */
        .frm_builder .submit-btn {
            display: inline-block;
            width: auto;
            margin: .75rem 0 0;
            padding: .2rem .55rem;
            background: #fff;
            color: #111;
            border: 1px solid #767676;
            border-radius: 2px;
            font: inherit;
            cursor: pointer;
        }
        label { display: block; margin-top: .75rem; font-weight: 600; }
        input, textarea, select { width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .frm_builder input[type="checkbox"],
        .frm_builder input[type="radio"] {
            width: auto;
            margin: 0 .35rem 0 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            vertical-align: middle;
        }
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
        .field-choice { margin-top: .35rem; display: flex; flex-direction: column; gap: .35rem; }
        .field-choice label {
            display: flex !important;
            align-items: flex-start;
            gap: .35rem;
            font-weight: 400 !important;
            margin-top: 0 !important;
            line-height: 1.35;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        .field-choice input[type="radio"],
        .field-choice input[type="checkbox"] {
            width: auto !important;
            margin: .15rem 0 0 !important;
            flex-shrink: 0;
        }
        .field-choice .choice-label {
            flex: 1;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: normal;
        }
        .mandatory_field { color: #c00; font-size: .85rem; display: inline; }
        .field-grid { width: 100%; border-collapse: collapse; margin-top: .35rem; font-size: .875rem; }
        .field-grid th, .field-grid td { border: 1px solid #ddd; padding: .35rem .5rem; text-align: center; }
        .display-html { margin: .5rem 0; line-height: 1.5; }
        .form-link-btn { display: inline-block; padding: .55rem 1rem; border-radius: 8px; color: #fff; text-decoration: none; font-weight: 600; margin: .25rem 0; }
        .signature-wrap canvas { width: 100%; max-width: 320px; height: 120px; border: 1px dashed #ccc; border-radius: 6px; touch-action: none; }
        .mobile-footer { display: flex; justify-content: flex-end; padding: .25rem .15rem 0; }
        .mobile-footer img { max-height: 28px; width: auto; opacity: .85; }
        @if ($portalPreview ?? false)
        html, body { height: auto; min-height: 0; overflow-x: hidden; }
        body.portal-preview { background: #fff; margin: 0; width: 100%; }
        body.portal-preview .wrap { max-width: 100%; width: 100%; margin: 0; padding: 0.5rem 0.65rem 0.35rem; box-sizing: border-box; }
        body.portal-preview .card { border-radius: 0; box-shadow: none; padding: 0.5rem 0.35rem 0.25rem; margin: 0; width: 100%; box-sizing: border-box; }
        body.portal-preview h1 { font-size: 1.15rem; line-height: 1.25; margin-bottom: 0.35rem; color: #222; }
        body.portal-preview .MobileBottomText h3 { font-size: 0.95rem; }
        body.portal-preview p { margin: 0.35rem 0; font-size: 0.92rem; line-height: 1.35; }
        body.portal-preview .btn { font-size: 0.85rem; padding: 0.45rem 0.75rem; }
        body.portal-preview .frm_builder .submit-btn { font-size: 0.9rem; }
        body.portal-preview .mobile-footer { padding: 0.15rem 0.35rem 0.1rem; }
        body.portal-preview .mobile-footer img { max-height: 22px; }
        body.portal-preview h2:last-of-type,
        body.portal-preview .gallery { margin-bottom: 0; }
        body.portal-preview .card > :last-child { margin-bottom: 0 !important; }
        body.portal-preview input,
        body.portal-preview textarea,
        body.portal-preview select { width: 100%; max-width: 100%; box-sizing: border-box; }
        body.portal-preview .gallery { grid-template-columns: 1fr; }
        body.portal-preview .gallery img { max-width: 100%; height: auto; aspect-ratio: auto; }
        @endif
    </style>
</head>
<body @class(['portal-preview' => $portalPreview ?? false])>
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

        @if ($profile->show_header)
            <nav class="top-navigation" style="margin:0 0 .75rem;padding:.35rem 0;border-bottom:1px solid #eee;">
                <h2 style="margin:0;font-size:.95rem;font-weight:700;color:#222;">Profile No: {{ $profile->id }}</h2>
            </nav>
        @elseif ($profile->name_company)
            <p class="text-sm" style="color:#555;margin:0 0 .5rem;">
                {{ $profile->name_company }}
            </p>
        @endif

        {{-- Legacy mobile/index.php: only show Make/Model / Location name when profile name is set.
             Never fall back to form_title (that caused duplicate green+black titles). --}}
        <div class="MobileBottomText">
            @if (filled($profile->name))
                @if (! empty($nameHeading))
                    <h3>{{ $nameHeading }}</h3>
                @endif
                <p>{{ $profile->name }}</p>
            @endif

            @if ($profile->typeSlug() === 'exhibit' && filled($profile->name2))
                <p>{{ $profile->name2 }}</p>
            @endif

            @if ($profile->identification && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc'], true))
                <h3>{{ $profile->typeSlug() === 'plant' ? 'ID' : 'Identification' }}</h3>
                <p>{{ $profile->identification }}</p>
            @endif

            @if ($profile->serial_no && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc', 'product'], true))
                <h3>Serial No.</h3>
                <p>{{ $profile->serial_no }}</p>
            @endif

            @if ($profile->description)
                @php
                    $descPlain = trim(str_replace('&nbsp;', '', strip_tags((string) $profile->description)));
                    $slug = $profile->typeSlug();
                    $showDescHeading = match ($slug) {
                        'asset' => (bool) $profile->show_description,
                        'misc', 'exhibit', 'voc', 'code' => false,
                        default => true,
                    };
                @endphp
                @if ($descPlain !== '')
                    @if ($showDescHeading)
                        <h3>Description</h3>
                    @endif
                    <p>{!! $profile->description !!}</p>
                @endif
            @endif

            @if ($profile->typeSlug() === 'exhibit' && filled($profile->description2))
                @php $desc2Plain = trim(str_replace('&nbsp;', '', strip_tags((string) $profile->description2))); @endphp
                @if ($desc2Plain !== '')
                    <p>{!! $profile->description2 !!}</p>
                @endif
            @endif

            @if ($profile->typeSlug() === 'voc')
                @foreach ([
                    'voc_first_name' => 'First Name',
                    'voc_last_name' => 'Last Name',
                    'voc_address' => 'Address',
                    'voc_town' => 'Town',
                    'voc_state' => 'State/Territory',
                    'voc_phone' => 'Telephone No.',
                    'voc_dob' => 'Date of Birth',
                    'voc_known_allergies' => 'Known Allergies/Medical Conditions',
                    'voc_blood_type' => 'Blood Type',
                    'voc_next_of_kin' => 'Next of Kin',
                    'voc_contact_phone' => 'Contact Telephone No.',
                    'voc_employer' => 'Employer',
                    'voc_emp_address' => 'Address',
                    'voc_emp_town' => 'Town',
                    'voc_emp_state' => 'State/Territory',
                    'voc_emp_phone' => 'Telephone No.',
                ] as $vocField => $vocLabel)
                    @php
                        $vocValue = $profile->{$vocField};
                        if ($vocField === 'voc_dob' && $vocValue) {
                            $vocValue = $vocValue instanceof \Carbon\CarbonInterface
                                ? $vocValue->format('d/m/Y')
                                : $vocValue;
                        }
                    @endphp
                    @if (filled($vocValue) && (string) $vocValue !== '1970-01-01')
                        <h3>{{ $vocLabel }}</h3>
                        <p>{{ $vocValue }}</p>
                    @endif
                @endforeach
            @endif

            @if ($profile->address && $profile->typeSlug() !== 'voc')
                @php
                    $showAddressHeading = $profile->typeSlug() === 'asset'
                        ? (bool) $profile->show_address
                        : true;
                @endphp
                @if ($showAddressHeading)
                    <h3>Address</h3>
                @endif
                <p>{{ $profile->address }}</p>
                @if (in_array($profile->typeSlug(), ['location', 'asset'], true))
                    <p>&nbsp;<a class="btn btn-outline" href="https://maps.google.com?q={{ urlencode($profile->address) }}" target="_blank" rel="noopener">View Map</a></p>
                @endif
            @endif

            @if ($profile->notes && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc', 'misc'], true))
                <h3>{{ $profile->typeSlug() === 'plant' ? 'Note' : 'Notes' }}</h3>
                <p>{{ $profile->notes }}</p>
            @endif

            @if ($profile->telephone && $profile->typeSlug() !== 'voc')
                @if ($profile->typeSlug() !== 'asset' || $profile->show_telephone)
                    <h3>Telephone</h3>
                @endif
                <p><a href="tel:{{ $profile->telephone }}">{{ $profile->telephone }}</a></p>
            @endif

            @if (filled($profile->mobile))
                @if ($profile->typeSlug() !== 'asset' || $profile->show_mobile)
                    <h3>Mobile</h3>
                @endif
                <p><a href="tel:{{ $profile->mobile }}">{{ $profile->mobile }}</a></p>
            @endif

            @if (filled($profile->email))
                @if ($profile->typeSlug() !== 'asset' || $profile->show_email)
                    <h3>Email</h3>
                @endif
                <p><a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></p>
            @endif

            @if (filled($profile->url) && $profile->typeSlug() !== 'code')
                @if ($profile->typeSlug() !== 'asset' || $profile->show_url)
                    <h3>Website</h3>
                @endif
                <p><a href="{{ $profile->url }}" target="_blank" rel="noopener">{{ $profile->url }}</a></p>
            @endif
        </div>

        @php
            $visibleWeblinks = $profile->weblinks->filter(function ($weblink): bool {
                $enabled = $weblink->link_button === true
                    || $weblink->link_button === 1
                    || $weblink->link_button === '1';

                return $enabled && filled($weblink->link_button_url);
            });
        @endphp
        @if ($visibleWeblinks->isNotEmpty())
            <h2>Links</h2>
            <div class="tile-grid" style="display:block;">
                @foreach ($visibleWeblinks as $weblink)
                    @php
                        $color = trim((string) ($weblink->link_button_color ?: '007A01'));
                        $color = str_starts_with($color, '#') ? $color : '#'.$color;
                        $align = in_array($weblink->link_button_align, ['left', 'center', 'right'], true)
                            ? $weblink->link_button_align
                            : 'left';
                    @endphp
                    <a
                        class="btn btn-weblink"
                        href="{{ $weblink->link_button_url }}"
                        target="_blank"
                        rel="noopener"
                        style="background:{{ $color }};text-align:{{ $align }};"
                    >
                        {{ $weblink->link_button_text ?: 'Open link' }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($profile->display_share_link)
            @php
                $shareUrl = filled($profile->shorturl)
                    ? (string) $profile->shorturl
                    : url('/'.$clientUrl.'/'.$profile->id);
                $shareText = trim((string) ($profile->name ?: $profile->code_profile_name ?: 'ScanLink'));
            @endphp
            <div class="shareNav-mob">
                <a
                    class="shareFB"
                    href="https://www.facebook.com/share.php?u={{ urlencode($shareUrl) }}"
                    target="_blank"
                    rel="noopener"
                    title="Facebook"
                >Facebook</a>
                <a
                    class="shareTWT"
                    href="https://twitter.com/share?text={{ urlencode($shareText.'  '.$shareUrl) }}"
                    target="_blank"
                    rel="noopener"
                    title="Twitter"
                >Twitter</a>
                <a
                    class="shareEML"
                    href="mailto:?subject={{ rawurlencode('Visit this link') }}&body={{ rawurlencode('Hi, I found this information for you! Have a nice day :) : '.$shareUrl) }}"
                    target="_blank"
                    rel="noopener"
                    title="Email"
                >Email</a>
            </div>
        @endif

        @if ($profile->documents->isNotEmpty())
            <h2>Documents</h2>
            <div class="tile-grid" style="display:block;">
                @foreach ($profile->documents as $document)
                    @if ($docUrl = $publicMediaUrl($document->doc_name))
                        @php
                            $docColor = trim((string) ($document->btn_color ?: '007A01'));
                            $docColor = str_starts_with($docColor, '#') ? $docColor : '#'.$docColor;
                            $docAlign = in_array($document->txt_align, ['left', 'center', 'right'], true)
                                ? $document->txt_align
                                : 'left';
                        @endphp
                        <a
                            class="btn btn-document"
                            href="{{ $docUrl }}"
                            target="_blank"
                            rel="noopener"
                            style="background:{{ $docColor }};text-align:{{ $docAlign }};"
                        >
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

        @if (($profile->form_is_enable || $profile->form_active) && $questions->isNotEmpty())
            <form method="post" action="{{ route('scan.form', [$clientUrl, $profile->id]) }}" enctype="multipart/form-data" class="frm_builder" style="margin-top:.75rem;">
                @csrf
                {{-- Legacy does not print form_title as a form section heading. --}}
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

                            @case(10)
                                <h1>{{ strip_tags($question->question_text) }}</h1>
                                @break

                            @case(12)
                                <h3>{{ strip_tags($question->question_text) }}</h3>
                                @break

                            @case(20)
                                <a class="form-link-btn" style="background:#{{ $question->button_colour ?: '008C00' }};" href="{{ $question->button_link_url ?: '#' }}" target="_blank" rel="noopener">
                                    {{ $question->question_text ?: 'Open link' }}
                                </a>
                                @break

                            @case(21)
                                @php $docHref = \App\Support\FormBuilderMedia::resolveDocumentHref($question); @endphp
                                @if ($docHref)
                                    <a class="form-link-btn" style="background:#{{ $question->button_colour ?: '008C00' }};" href="{{ $docHref }}" target="_blank" rel="noopener">
                                        {{ $question->doc_title ?: 'View document' }}
                                    </a>
                                @endif
                                @break

                            @case(23)
                                @php $docChoices = \App\Support\FormBuilderMedia::documentChoices($question); @endphp
                                @if ($question->question_text && ! str_contains($question->question_text, ',') && ! str_contains($question->question_text, ':::'))
                                    <p style="font-weight:600;margin-bottom:.35rem;">{{ $question->question_text }}</p>
                                @endif
                                <label>{{ $question->doc_title ?: 'Select documents' }}@if($question->is_mandatory) *@endif</label>
                                <div class="field-choice">
                                    @foreach ($docChoices as $doc)
                                        <div style="margin-bottom:.35rem;">
                                            <label>
                                                <input type="checkbox" name="answers[{{ $qid }}][]" value="{{ $doc['title'] }}" {{ $required }}>
                                                {{ $doc['title'] }}
                                            </label>
                                            @if ($doc['href'])
                                                <a href="{{ $doc['href'] }}" target="_blank" rel="noopener" style="font-size:.85rem;margin-left:.5rem;">Download</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @break

                            @case(3)
                                @php
                                    $choiceLabel = \App\Support\FormBuilderMedia::choiceLabel($question);
                                    $choiceOptions = \App\Support\FormBuilderMedia::choiceOptions($question);
                                @endphp
                                @if ($choiceLabel !== '')
                                    <label>{{ $choiceLabel }}@if($question->is_mandatory) *@endif</label>
                                @elseif ($question->is_mandatory)
                                    <span style="color:#c00;font-size:.85rem;">*</span>
                                @endif
                                <div class="field-choice">
                                    @foreach ($choiceOptions as $optionName)
                                        <label>
                                            <input type="radio" name="answers[{{ $qid }}]" value="{{ $optionName }}" {{ $required }}>
                                            <span class="choice-label">{{ $optionName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @case(4)
                                @php
                                    $choiceLabel = \App\Support\FormBuilderMedia::choiceLabel($question);
                                    $choiceOptions = \App\Support\FormBuilderMedia::choiceOptions($question);
                                @endphp
                                @if ($choiceLabel !== '')
                                    <label>{{ $choiceLabel }}@if($question->is_mandatory) <span class="mandatory_field">*</span>@endif</label>
                                @elseif ($question->is_mandatory)
                                    <span class="mandatory_field">*</span><br>
                                @endif
                                <div class="field-choice">
                                    @foreach ($choiceOptions as $optionName)
                                        <label>
                                            <input type="checkbox" name="answers[{{ $qid }}][]" value="{{ $optionName }}">
                                            <span class="choice-label">{{ $optionName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @break

                            @case(5)
                                @php
                                    $choiceLabel = \App\Support\FormBuilderMedia::choiceLabel($question);
                                    $choiceOptions = \App\Support\FormBuilderMedia::choiceOptions($question);
                                @endphp
                                @if ($choiceLabel !== '')
                                    <label>{{ $choiceLabel }}@if($question->is_mandatory) *@endif</label>
                                @elseif ($question->is_mandatory)
                                    <span style="color:#c00;font-size:.85rem;">*</span>
                                @endif
                                <select name="answers[{{ $qid }}]" {{ $required }}>
                                    <option value="">Select…</option>
                                    @foreach ($choiceOptions as $optionName)
                                        <option value="{{ $optionName }}">{{ $optionName }}</option>
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

                            @case(11)
                                @php
                                    $imgUrl = \App\Support\FormBuilderMedia::url($question->image_url)
                                        ?: \App\Support\FormBuilderMedia::url($question->question_text);
                                    $align = \App\Support\FormBuilderMedia::alignCss($question->image_align);
                                @endphp
                                @if ($question->image_title)
                                    <p style="font-weight:600;text-align:{{ $align }};">{{ $question->image_title }}</p>
                                @endif
                                @if ($imgUrl)
                                    <div style="text-align:{{ $align }};">
                                        <img src="{{ $imgUrl }}" alt="{{ $question->image_title ?: 'Form image' }}" style="max-width:100%;border-radius:8px;">
                                    </div>
                                @elseif ($question->question_text)
                                    <div class="display-html">{!! $question->question_text !!}</div>
                                @endif
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
                                @if ($question->include_name)
                                    <label>Name</label>
                                    <input type="text" name="answers_meta[{{ $qid }}][name]" {{ $required }}>
                                @endif
                                @if ($question->include_employer)
                                    <label>Employer</label>
                                    <input type="text" name="answers_meta[{{ $qid }}][employer]">
                                @endif
                                @if ($question->include_email)
                                    <label>Email</label>
                                    <input type="email" name="answers_meta[{{ $qid }}][email]">
                                @endif
                                @if ($question->include_phone)
                                    <label>Phone</label>
                                    <input type="text" name="answers_meta[{{ $qid }}][phone]">
                                @endif
                                @break

                            @case(17)
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <input type="file" name="answers_file[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(18)
                                <label>{{ $question->question_text ?: 'Participant name' }}@if($question->is_mandatory) *@endif</label>
                                <input type="text" name="answers[{{ $qid }}]" placeholder="Full name" {{ $required }}>
                                @if ($question->participant_include_employer)
                                    <label>Employer / company</label>
                                    <input type="text" name="answers_meta[{{ $qid }}][employer]">
                                @endif
                                @if ($question->participant_include_signature)
                                    <div class="signature-wrap">
                                        <canvas id="sig-{{ $qid }}" width="320" height="120"></canvas>
                                        <input type="hidden" name="answers_meta[{{ $qid }}][signature]" id="sig-input-{{ $qid }}">
                                        <p style="margin:.35rem 0;"><button type="button" class="btn-outline btn" onclick="clearSig({{ $qid }})">Clear signature</button></p>
                                    </div>
                                @endif
                                @break

                            @case(19)
                                <label>{{ $question->question_text ?: 'Location' }}@if($question->is_mandatory) *@endif</label>
                                <input type="text" name="answers[{{ $qid }}]" placeholder="Location" {{ $required }}>
                                @break

                            @case(22)
                                <label>{{ $question->question_text ?: 'SWMS Hazard / Risk' }}@if($question->is_mandatory) *@endif</label>
                                <label>Task / activity</label>
                                <input type="text" name="answers_meta[{{ $qid }}][task]" {{ $required }}>
                                <label>Hazards</label>
                                <textarea name="answers_meta[{{ $qid }}][hazards]" rows="2"></textarea>
                                <label>Risk before controls</label>
                                <input type="text" name="answers_meta[{{ $qid }}][risk_before]">
                                <label>Controls / risk after</label>
                                <textarea name="answers_meta[{{ $qid }}][risk_after]" rows="2"></textarea>
                                <label>Photo (optional)</label>
                                <input type="file" name="answers_file[{{ $qid }}]" accept="image/*">
                                @break

                            @case(24)
                                <label>{{ $question->question_text ?: 'Additional recipient email' }}@if($question->is_mandatory) *@endif</label>
                                <input type="email" name="answers[{{ $qid }}]" {{ $required }}>
                                @break

                            @case(25)
                                @php
                                    $bg = $question->covid_bg_color ?: 'ffffff';
                                    $fg = $question->covid_text_color ?: '000000';
                                    if (! str_starts_with($bg, '#')) {
                                        $bg = '#'.$bg;
                                    }
                                    if (! str_starts_with($fg, '#')) {
                                        $fg = '#'.$fg;
                                    }
                                    $locationTypes = config('scanlink.covid_location_descriptions', []);
                                @endphp
                                <div class="covid-checkinform" style="padding:10px;background-color:{{ $bg }};color:{{ $fg }};border-radius:8px;">
                                    @if (filled($question->question_text))
                                        <div class="display-html" style="color:{{ $fg }};margin-bottom:10px;">{!! $question->question_text !!}</div>
                                    @endif

                                    <p>
                                        <span style="color:{{ $fg }};">Visitor name <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][visitor_name]" required {{ $required }}>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Visitor phone <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" inputmode="numeric" name="answers_meta[{{ $qid }}][visitor_phone]" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Date <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="date" name="answers_meta[{{ $qid }}][checkin_date]" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Time <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="time" name="answers_meta[{{ $qid }}][checkin_time]" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Venue name <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][venue_name]" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Venue Address <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][venue_address]" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Location Description/Type <span style="color:red">*</span></span><br>
                                        <select
                                            name="answers_meta[{{ $qid }}][location_type]"
                                            class="input-field location_desc_option"
                                            required
                                            onchange="window.slCovidLocationChange && window.slCovidLocationChange(this.value, {{ $qid }}, '{{ $fg }}')"
                                        >
                                            <option value="">Select</option>
                                            @foreach ($locationTypes as $locationType)
                                                <option value="{{ $locationType }}">{{ $locationType }}</option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p class="vehicle_no" id="vehicle_no_{{ $qid }}"></p>
                                </div>
                                @break

                            @default
                                <label>{{ $question->question_text }}@if($question->is_mandatory) *@endif</label>
                                <textarea name="answers[{{ $qid }}]" rows="2" {{ $required }}></textarea>
                        @endswitch
                    </div>
                @endforeach
                <p style="margin-top:.5rem;"><input class="submit-btn" type="submit" value="Submit"></p>
            </form>
            <script>
                window.slCovidLocationChange = function (value, questionId, labelColor) {
                    const host = document.getElementById('vehicle_no_' + questionId);
                    if (!host) return;
                    if (value === 'Vehicle') {
                        host.innerHTML = '<br><span style="color:' + labelColor + '">Vehicle Registration ID <span style="color:red">*</span></span><br>'
                            + '<input class="input-field" type="text" name="answers_meta[' + questionId + '][vehicle_or_other]" required />';
                    } else if (value === 'Other') {
                        host.innerHTML = '<br><span style="color:' + labelColor + '">Please specify other <span style="color:red">*</span></span><br>'
                            + '<input class="input-field" type="text" name="answers_meta[' + questionId + '][vehicle_or_other]" required />';
                    } else {
                        host.innerHTML = '';
                    }
                };
            </script>
            <script>
                (function () {
                    const pads = {};
                    @foreach ($questions as $question)
                        @if (
                            (int) $question->question_type_id === 16
                            || ((int) $question->question_type_id === 18 && $question->participant_include_signature)
                        )
                            (function initSig{{ $question->question_id }}() {
                                const canvas = document.getElementById('sig-{{ $question->question_id }}');
                                const input = document.getElementById('sig-input-{{ $question->question_id }}');
                                if (!canvas || !input) return;
                                const ctx = canvas.getContext('2d');
                                ctx.strokeStyle = '#111';
                                ctx.lineWidth = 2;
                                ctx.lineCap = 'round';
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

        @if ($needsVisitorInfo)
            <div class="visitor-form">
                <h2>Visitor information</h2>
                <form method="post" action="{{ route('scan.visitor', [$clientUrl, $profile->id]) }}">
                    @csrf
                    @if ($profile->data_collection_name)
                        <label>Name</label>
                        <input type="text" name="name" required>
                    @endif
                    @if ($profile->data_collection_surname)
                        <label>Surname</label>
                        <input type="text" name="surname" {{ $profile->set_up_compulsory ? 'required' : '' }}>
                    @endif
                    @if ($profile->data_collection_email)
                        <label>Email</label>
                        <input type="email" name="email" required>
                    @endif
                    @if ($profile->data_collection_mobile)
                        <label>Mobile</label>
                        <input type="text" name="mobile">
                    @endif
                    <p style="margin-top:1rem;"><button class="btn" type="submit">Continue</button></p>
                </form>
            </div>
        @endif
    </div>

    @php
        $ownerFooter = trim((string) ($profile->owner?->footer_logo ?? ''));
        $footerSrc = ($ownerFooter !== '' && file_exists(public_path('images/logo/'.$ownerFooter)))
            ? asset('images/logo/'.$ownerFooter)
            : asset('images/PoweredbyScanLink.png');
    @endphp
    <footer class="mobile-footer">
        <figure style="margin:0;">
            <img src="{{ $footerSrc }}" alt="Powered by SCANLINK">
        </figure>
    </footer>
</div>
</body>
</html>
