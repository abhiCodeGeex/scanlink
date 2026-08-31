<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->name }} — ScanLink</title>
    <style>
        /* Legacy mobile page: plain white body, 12px #555755 base text. */
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; background: #fff; color: #555755; font-size: 12px; line-height: 22px; }
        /* Legacy insets: .wrapper margin 0 10px + .content-container padding 10px = ~20px each side. */
        .wrap { max-width: 640px; margin: 0 auto; padding: 0 10px; }
        /* Legacy content sits on plain white (card shadow intentionally omitted for a flat look). */
        .card { background: #fff; border-radius: 0; padding: 10px; box-shadow: none; margin-bottom: 0; }
        /* Keep every embedded image inside the mobile width and at its natural aspect ratio.
           (figure has a 40px browser default margin that otherwise insets images.) */
        img { max-width: 100%; height: auto; }
        figure { margin: 0 0 0.75rem; }
        a { color: #278b28; }
        /* Legacy MobileBottomText: label (bold) then value; block runs at 14px/22px. */
        .MobileBottomText { font-size: 14px; line-height: 22px; }
        .MobileBottomText h3 { margin: 0 0 7px 0; font-size: 1rem; font-weight: 700; color: #222; }
        .MobileBottomText p { margin: 0 0 7px 0; color: #555755; }
        h1 { color: #222; margin: 0 0 .5rem; font-size: 1.25rem; }
        h2 { font-size: 1.1rem; margin-top: .6rem; margin-bottom: .3rem; }
        .btn { display: inline-block; background: #008C00; color: #fff; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; margin: .25rem .25rem .25rem 0; }
        .btn-outline { background: #fff; color: #008C00; border: 1px solid #008C00; }
        /* Legacy View Map (.gray-mob-btn): solid grey button floated right. */
        .gray-mob-btn { display: inline-block; float: right; background: #808080; color: #fff !important; height: 40px; line-height: 40px; padding: 0 16px; border: 0; border-radius: 6px; font-weight: bold; text-decoration: none; }
        .gray-mob-btn:hover { background: #777; }
        /* Legacy WeblinkList / documentList tiles: full-width UPPERCASE bars, 6px radius,
           drop shadow, and a right-aligned arrow icon. The tile colour is applied inline
           via background-color so it never clears this arrow background-image. */
        .btn-weblink,
        .btn-document {
            display: block;
            width: 100%;
            box-sizing: border-box;
            color: #fff !important;
            font-weight: bold;
            font-size: 13px;
            line-height: 20px;
            text-transform: uppercase;
            padding: 17px 10px;
            margin: 5px 0;
            border-radius: 6px;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
            background-image: url('{{ asset('images/mobile-list-icon.png') }}');
            background-repeat: no-repeat;
            background-position: right center;
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
        /* Themed dialog (shared slAlert/slConfirm look — same as the portal). */
        .sl-dialog-overlay {
            position: fixed; inset: 0; z-index: 10060; display: flex; align-items: center;
            justify-content: center; padding: 24px 16px; background: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(2px); box-sizing: border-box;
        }
        .sl-dialog-overlay[hidden] { display: none !important; }
        .sl-dialog {
            width: min(400px, 100%); background: #fff; border-radius: 14px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3); padding: 22px 22px 18px; box-sizing: border-box;
        }
        .sl-dialog-msg { margin: 0 0 18px; font-size: 14.5px; line-height: 1.5; color: #1f2937; }
        .sl-dialog-actions { display: flex; justify-content: flex-end; gap: 10px; }
        .sl-dialog-btn { border: 0; border-radius: 9px; padding: 9px 20px; cursor: pointer; font-size: 13px; font-weight: 700; }
        .sl-dialog-btn--ghost { background: #f3f4f6; color: #374151; }
        .sl-dialog-btn--primary { background: #008C00; color: #fff; }
        /* Validation feedback: banner + per-question highlight (submission must never fail silently). */
        .sl-form-errors {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            color: #991b1b;
            padding: .7rem .85rem;
            margin: 0 0 .9rem;
            font-size: .92rem;
        }
        .sl-form-errors__hint { font-weight: 400; font-size: .8rem; margin-top: .25rem; color: #b45309; }
        .fb-q-error {
            border-left: 3px solid #dc2626;
            background: #fef7f7;
            border-radius: 6px;
            padding: .4rem .6rem .5rem;
        }
        .fb-q-error-msg { color: #dc2626; font-size: .8rem; font-weight: 700; margin-bottom: .1rem; }
        /* Form Submit: solid brand-green button, consistent with the rest of the form. */
        .frm_builder .submit-btn {
            display: inline-block;
            width: 100%;
            margin: .75rem 0 0;
            padding: .65rem 1rem;
            background: #008C00;
            color: #fff;
            border: 0;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease;
        }
        .frm_builder .submit-btn:hover { background: #00a300; }
        /* Legacy form rhythm is tight (.frm_builder 14px/25px, labels are inline text + <br>). */
        label { display: block; margin-top: .4rem; font-weight: 600; }
        input, textarea, select { width: 100%; padding: .4rem .5rem; margin-top: .2rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
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
        /* Legacy .BannerImage: the company logo is a full-width banner at the top — centered,
           natural aspect, NO height cap (style.css .MobilePage .BannerImage img { max-width:100% }). */
        .logo-row { display: block; text-align: center; min-height: 50px; margin-bottom: .5rem; }
        .logo-row img { display: block; width: 100%; max-width: 100%; height: auto; margin: 0 auto .25rem; border-radius: 0; }
        .tile-grid { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
        /* Legacy gallery: uploaded pictures render as a single-row, horizontally-swipeable strip
           of thumbnails (Swiper) — each ≤250px wide / ≤166px tall, centered — NOT a full-width
           grid. (style.css .swiper-slide 280×300; img max-width:250px, max-height:166px.) */
        .gallery {
            display: flex;
            gap: 12px;
            margin-top: .75rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }
        .gallery figure {
            flex: 0 0 auto;
            width: 250px;
            max-width: 80vw;
            margin: 0;
            scroll-snap-align: center;
            text-align: center;
        }
        .gallery img {
            max-width: 100%;
            max-height: 166px;
            width: auto;
            height: auto;
            display: inline-block;
            border-radius: 0;
        }
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
        /* Location Function (type 19): text + Locate me (GPS) + View map. */
        .sl-loc-row { display: flex; flex-wrap: wrap; align-items: center; gap: .4rem; }
        .sl-loc-row .sl-loc-input { flex: 1 1 150px; min-width: 0; margin-top: 0; }
        .sl-loc-btn { display: inline-flex; align-items: center; gap: .3rem; padding: .5rem .7rem; border: 1px solid #008C00; border-radius: 6px; background: #fff; color: #008C00; font: inherit; font-size: .82rem; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .sl-loc-btn:hover { background: #f0fbf0; }
        .sl-loc-btn[disabled] { opacity: .55; cursor: default; }
        .sl-loc-map { font-size: .82rem; color: #278b28; font-weight: 600; text-decoration: none; white-space: nowrap; }
        .sl-loc-map:hover { text-decoration: underline; }
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
        /* Keep the legacy thumbnail carousel in the narrow preview (smaller thumbs so they fit). */
        body.portal-preview .gallery figure { width: 190px; max-width: 78%; }
        body.portal-preview .gallery img { max-height: 130px; }
        @endif
    </style>
</head>
<body @class(['portal-preview' => $portalPreview ?? false])>
<div class="wrap">
    <div class="card">
        {{-- Legacy: a success popup with a green check + "Thank You / …submitted successfully". --}}
        @if (session('form_submitted'))
            <div class="success-popup" style="text-align:center;padding:1.5rem 1rem;margin-bottom:1rem;border:1px solid #cde9cd;background:#e8f5e9;border-radius:8px;">
                <svg width="46" height="46" viewBox="0 0 24 24" style="margin-bottom:.35rem;" aria-hidden="true">
                    <circle cx="12" cy="12" r="11" fill="#008C00"/>
                    <path d="M7 12.5l3.2 3.2L17 9" stroke="#fff" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h2 style="margin:.15rem 0;color:#1b5e20;font-size:1.1rem;">Thank You</h2>
                <p style="margin:0;color:#1b5e20;">Your response has been submitted successfully</p>
            </div>
        @endif

        {{-- Legacy tiles_order: exhibit/voc render their content tiles in the user-saved order. --}}
        @php $slOrderedTileTypes = ['exhibit', 'voc']; @endphp

        {{-- Legacy mobile/index.php: outside the activation window the page shows only the logo. --}}
        @if ($withinActivationWindow ?? true)

        {{-- Legacy: "Display the code number at the top of mobile screen" shows a green header
             bar at the very top of the page — for EVERY profile type (incl. exhibit/voc). --}}
        @if ($profile->show_header)
            <div class="code-header-bar" style="background:#178a00;color:#fff;font-weight:700;font-size:1rem;line-height:1.3;padding:10px 12px;margin:-10px -10px 10px;">
                Profile No: {{ $profile->id }}
            </div>
        @endif

        @if (in_array($profile->typeSlug(), $slOrderedTileTypes, true))
            @include('scan.partials.ordered-tiles')
        @endif

        @if ($profile->logos->isNotEmpty() && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
            <div class="logo-row">
                @foreach ($profile->logos as $logo)
                    @if ($logoUrl = $publicMediaUrl($logo->logo_name))
                        <img src="{{ $logoUrl }}" alt="Company logo">
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->name_company && $profile->typeSlug() !== 'people' && ! $profile->show_header)
            <p class="text-sm" style="color:#555;margin:0 0 .5rem;">
                {{ $profile->name_company }}
            </p>
        @endif

        {{-- Video(s) render above the Words / description block (legacy parity). --}}
        @if ($profile->videos->isNotEmpty() && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
            @foreach ($profile->videos as $video)
                @php
                    $embedUrl = $youtubeEmbedUrl((string) $video->video_name);
                    $vName = (string) $video->video_name;
                    // Legacy: a video_name that is a file (has an extension) is an uploaded clip.
                    $vFileUrl = (! $embedUrl && preg_match('/\.(mp4|m4v|mov|webm|ogg)$/i', $vName))
                        ? $publicMediaUrl(str_contains($vName, '/') ? $vName : 'images/video/'.$vName)
                        : null;
                @endphp
                @if ($embedUrl)
                    <div class="video-wrap">
                        <iframe src="{{ $embedUrl }}" allowfullscreen loading="lazy" title="{{ $video->title ?: 'YouTube video' }}"></iframe>
                    </div>
                @elseif ($vFileUrl)
                    <div class="video-wrap">
                        <video controls playsinline style="position:absolute;top:0;left:0;width:100%;height:100%;"><source src="{{ $vFileUrl }}"></video>
                    </div>
                @elseif (filled($vName))
                    <a class="btn" href="{{ $vName }}" target="_blank" rel="noopener">{{ $video->title ?: 'Watch video' }}</a>
                @endif
            @endforeach
        @endif

        {{-- Legacy mobile/index.php: only show Make/Model / Location name when profile name is set.
             Never fall back to form_title (that caused duplicate green+black titles).
             exhibit/voc render Words / Profile Information via the ordered-tiles partial. --}}
        @unless (in_array($profile->typeSlug(), $slOrderedTileTypes, true))
        <div class="MobileBottomText">
            {{-- Asset "Words" checkboxes hide the WHOLE block (heading + value), not just the heading. --}}
            @php $slAsset = $profile->typeSlug() === 'asset'; @endphp
            @if (filled($profile->name) && (! $slAsset || $profile->show_name))
                @if (! empty($nameHeading))
                    <h3>{{ $nameHeading }}</h3>
                @endif
                {{-- Legacy echoes name/notes as raw HTML (rich text), like description. --}}
                <p>{!! $profile->name !!}</p>
            @endif

            @if ($profile->typeSlug() === 'exhibit' && filled($profile->name2))
                <p>{{ $profile->name2 }}</p>
            @endif

            {{-- Legacy mobile/index.php: Position renders between Name and ID (people type). --}}
            @if (filled($profile->position))
                <h3>Position</h3>
                <p>{{ $profile->position }}</p>
            @endif

            @if ($profile->identification && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc'], true))
                <h3>{{ $profile->typeSlug() === 'plant' ? 'ID' : 'Identification' }}</h3>
                <p>{{ $profile->identification }}</p>
            @endif

            @if ($profile->serial_no && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc', 'product'], true))
                <h3>Serial No.</h3>
                <p>{{ $profile->serial_no }}</p>
            @endif

            @if ($profile->description && (! $slAsset || $profile->show_description))
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

            @if ($profile->address && $profile->typeSlug() !== 'voc' && (! $slAsset || $profile->show_address))
                <h3>Address</h3>
                <p>{{ $profile->address }}</p>
                @if (in_array($profile->typeSlug(), ['location', 'asset'], true))
                    <p>&nbsp;<a class="gray-mob-btn" href="https://maps.google.com?q={{ urlencode($profile->address) }}" target="_blank" rel="noopener">View Map</a></p>
                    <div style="clear:both;"></div>
                @endif
            @endif

            @if ($profile->notes && ! in_array($profile->typeSlug(), ['asset', 'exhibit', 'voc', 'misc'], true))
                <h3>{{ $profile->typeSlug() === 'plant' ? 'Note' : 'Notes' }}</h3>
                <p>{!! $profile->notes !!}</p>
            @endif

            {{-- Legacy people mobile: Contact (name_company) renders after Notes, before Telephone. --}}
            @if ($profile->typeSlug() === 'people' && filled($profile->name_company))
                <h3>Contact</h3>
                <p>{{ $profile->name_company }}</p>
            @endif

            @if ($profile->telephone && $profile->typeSlug() !== 'voc' && (! $slAsset || $profile->show_telephone))
                <h3>Telephone</h3>
                <p><a href="tel:{{ $profile->telephone }}">{{ $profile->telephone }}</a></p>
            @endif

            @if (filled($profile->mobile) && (! $slAsset || $profile->show_mobile))
                <h3>Mobile</h3>
                <p><a href="tel:{{ $profile->mobile }}">{{ $profile->mobile }}</a></p>
            @endif

            @if (filled($profile->email) && (! $slAsset || $profile->show_email))
                <h3>Email</h3>
                <p><a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></p>
            @endif

            @if (filled($profile->url) && $profile->typeSlug() !== 'code' && (! $slAsset || $profile->show_url))
                <h3>Website</h3>
                <p><a href="{{ $profile->url }}" target="_blank" rel="noopener">{{ $profile->url }}</a></p>
            @endif

            {{-- Contacts added on the profile editor (people/companies associated with this code). --}}
            @if ($profile->contacts->isNotEmpty())
                <h3>Contacts</h3>
                @foreach ($profile->contacts as $contact)
                    <p>
                        {{ $contact->name_company }}
                        @if (filled($contact->telephone))
                            &middot; <a href="tel:{{ $contact->telephone }}">{{ $contact->telephone }}</a>
                        @endif
                    </p>
                @endforeach
            @endif
        </div>
        @endunless

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
                @if ($errors->any())
                    {{-- Never fail silently: name the problem, highlight the fields, scroll to the first one. --}}
                    <div class="sl-form-errors" id="sl-form-errors">
                        <strong>{{ $errors->first('form') ?: $errors->first() }}</strong>
                        <div class="sl-form-errors__hint">Your answers below have been kept — signatures, photos and file uploads need to be re-added.</div>
                    </div>
                @endif
                {{-- Legacy does not print form_title as a form section heading. --}}
                @foreach ($questions as $question)
                    @php
                        $tid = (int) $question->question_type_id;
                        $options = $question->options;
                        $qid = $question->question_id;
                        $required = $question->is_mandatory ? 'required' : '';
                        // Red mandatory marker, used inline on every required element's label.
                        $star = $question->is_mandatory ? ' <span class="mandatory_field">*</span>' : '';
                        $hasError = $errors->has('q_'.$qid);
                    @endphp
                    <div id="fb-q-{{ $qid }}" class="{{ $hasError ? 'fb-q-error' : '' }}" style="margin-bottom:.5rem;">
                        @if ($hasError)
                            <div class="fb-q-error-msg">This field is required.</div>
                        @endif
                        @switch($tid)
                            @case(1)
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <input type="text" name="answers[{{ $qid }}]" value="{{ old('answers.'.$qid) }}" {{ $required }}>
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
                                {{-- question_text stores the internal upload filename(s) (fb_doc_*) — never show those. --}}
                                @if ($question->question_text
                                    && ! str_contains($question->question_text, ',')
                                    && ! str_contains($question->question_text, ':::')
                                    && ! preg_match('/^fb_(doc|img)_/i', $question->question_text))
                                    <p style="font-weight:600;margin-bottom:.35rem;">{{ $question->question_text }}</p>
                                @endif
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
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
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <div class="field-choice">
                                    @foreach ($choiceOptions as $optionName)
                                        <label>
                                            <input type="radio" name="answers[{{ $qid }}]" value="{{ $optionName }}" @checked(old('answers.'.$qid) === $optionName) {{ $required }}>
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
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <div class="field-choice">
                                    @foreach ($choiceOptions as $optionName)
                                        <label>
                                            <input type="checkbox" name="answers[{{ $qid }}][]" value="{{ $optionName }}" @checked(in_array($optionName, (array) old('answers.'.$qid, []), true))>
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
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <select name="answers[{{ $qid }}]" {{ $required }}>
                                    <option value="">Select…</option>
                                    @foreach ($choiceOptions as $optionName)
                                        <option value="{{ $optionName }}" @selected(old('answers.'.$qid) === $optionName)>{{ $optionName }}</option>
                                    @endforeach
                                </select>
                                @break

                            @case(6)
                                @php
                                    $scaleFrom = (int) ($options->firstWhere('question_option_type_id', 1)?->option_name ?? 1);
                                    $scaleTo = (int) ($options->firstWhere('question_option_type_id', 2)?->option_name ?? 5);
                                    if ($scaleFrom > $scaleTo) { [$scaleFrom, $scaleTo] = [$scaleTo, $scaleFrom]; }
                                @endphp
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <select name="answers[{{ $qid }}]" {{ $required }}>
                                    <option value="">Select…</option>
                                    @for ($i = $scaleFrom; $i <= $scaleTo; $i++)
                                        <option value="{{ $i }}" @selected(old('answers.'.$qid) == $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                                @break

                            @case(7)
                                @php
                                    $rows = $options->where('question_option_type_id', 5);
                                    $cols = $options->where('question_option_type_id', 6);
                                @endphp
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                @php $oldGrid = (array) old('answers.'.$qid, []); @endphp
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
                                                            <input type="radio" name="answers[{{ $qid }}][{{ $row->option_name }}]" value="{{ $col->option_name }}" @checked(($oldGrid[$row->option_name] ?? null) === $col->option_name)>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <textarea name="answers[{{ $qid }}]" rows="2" {{ $required }}>{{ is_string(old('answers.'.$qid)) ? old('answers.'.$qid) : '' }}</textarea>
                                @endif
                                @break

                            @case(8)
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <input type="date" name="answers[{{ $qid }}]" value="{{ old('answers.'.$qid) }}" {{ $required }}>
                                @break

                            @case(9)
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <input type="time" name="answers[{{ $qid }}]" value="{{ old('answers.'.$qid) }}" {{ $required }}>
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
                                @if ($question->is_mandatory)<span class="mandatory_field" style="display:inline;">*</span>@endif
                                <textarea name="answers[{{ $qid }}]" rows="3" {{ $required }}>{{ old('answers.'.$qid) }}</textarea>
                                @break

                            @case(16)
                                {{-- Legacy parity: fields first, signature pad last; repeatable via Add another. --}}
                                <div class="sl-repeat" data-sl-repeat>
                                    <div class="sl-repeat-item">
                                        @if ($question->include_name)
                                            <label>Name{!! $star !!}</label>
                                            <input type="text" name="answers_meta[{{ $qid }}][name][]" value="{{ old('answers_meta.'.$qid.'.name.0') }}" {{ $required }}>
                                        @endif
                                        @if ($question->include_employer)
                                            <label>Employer</label>
                                            <input type="text" name="answers_meta[{{ $qid }}][employer][]" value="{{ old('answers_meta.'.$qid.'.employer.0') }}">
                                        @endif
                                        @if ($question->include_email)
                                            <label>Email</label>
                                            <input type="email" name="answers_meta[{{ $qid }}][email][]" value="{{ old('answers_meta.'.$qid.'.email.0') }}">
                                        @endif
                                        @if ($question->include_phone)
                                            <label>Phone</label>
                                            <input type="text" name="answers_meta[{{ $qid }}][phone][]" value="{{ old('answers_meta.'.$qid.'.phone.0') }}">
                                        @endif
                                        <label>{{ $question->question_text }}{!! $star !!}</label>
                                        <div class="signature-wrap">
                                            <canvas class="sl-sig-canvas" width="320" height="120"></canvas>
                                            <input type="hidden" name="answers_meta[{{ $qid }}][signature][]" class="sl-sig-input">
                                            <p style="margin:.35rem 0;"><button type="button" class="btn-outline btn sl-sig-clear">Clear signature</button></p>
                                        </div>
                                    </div>
                                    {{-- Legacy add_another: appends another name + signature entry. --}}
                                    <a href="javascript:;" class="sl-add-another" style="display:inline-block;margin-top:.5rem;font-size:.9rem;color:#008C00;font-weight:600;">+ Add another</a>
                                </div>
                                @break

                            @case(17)
                                @if (filled($question->question_text))<label>{{ $question->question_text }}{!! $star !!}</label>@else{!! $question->is_mandatory ? '<span class="mandatory_field" style="display:inline;">*</span>' : '' !!}@endif
                                {{-- Legacy "Add another": native multi-file selection. --}}
                                <input type="file" name="answers_file[{{ $qid }}][]" multiple class="sl-multi-upload" {{ $required }}>
                                @break

                            @case(18)
                                <label>{{ $question->question_text ?: 'Name' }}{!! $star !!}</label>
                                @if ($question->participant_include_signature)
                                    {{-- With a signature pad this stays single-instance (one canvas). --}}
                                    <input type="text" name="answers[{{ $qid }}]" value="{{ is_string(old('answers.'.$qid)) ? old('answers.'.$qid) : '' }}" placeholder="Full name" {{ $required }}>
                                    @if ($question->participant_include_employer)
                                        <label>Employer / company</label>
                                        <input type="text" name="answers_meta[{{ $qid }}][employer]" value="{{ old('answers_meta.'.$qid.'.employer') }}">
                                    @endif
                                    <div class="signature-wrap">
                                        <canvas id="sig-{{ $qid }}" width="320" height="120"></canvas>
                                        <input type="hidden" name="answers_meta[{{ $qid }}][signature]" id="sig-input-{{ $qid }}">
                                        <p style="margin:.35rem 0;"><button type="button" class="btn-outline btn" onclick="clearSig({{ $qid }})">Clear signature</button></p>
                                    </div>
                                @else
                                    {{-- Legacy "Add another": collect multiple participants in one submission. --}}
                                    <div class="sl-repeat" data-sl-repeat>
                                        <div class="sl-repeat-item">
                                            <input type="text" name="answers[{{ $qid }}][]" value="{{ old('answers.'.$qid.'.0') }}" placeholder="Full name" {{ $required }}>
                                            @if ($question->participant_include_employer)
                                                <input type="text" name="answers_meta[{{ $qid }}][employer][]" value="{{ old('answers_meta.'.$qid.'.employer.0') }}" placeholder="Employer / company" style="margin-top:.35rem;">
                                            @endif
                                        </div>
                                        <a href="javascript:;" class="sl-add-another" style="display:inline-block;margin-top:.4rem;font-size:.9rem;color:#008C00;font-weight:600;">+ Add another</a>
                                    </div>
                                @endif
                                @break

                            @case(19)
                                @if (filled($question->question_text))<label>{{ $question->question_text }}{!! $star !!}</label>@else{!! $question->is_mandatory ? '<span class="mandatory_field" style="display:inline;">*</span>' : '' !!}@endif
                                <div class="sl-loc-row">
                                    <input type="text" name="answers[{{ $qid }}]" id="sl-loc-{{ $qid }}" class="sl-loc-input" value="{{ old('answers.'.$qid) }}" placeholder="Location" {{ $required }}>
                                    <button type="button" class="sl-loc-btn" data-loc-target="sl-loc-{{ $qid }}">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4.5 8-11a8 8 0 1 0-16 0c0 6.5 8 11 8 11z"/><circle cx="12" cy="11" r="3"/></svg>
                                        Locate me
                                    </button>
                                    <a href="https://maps.google.com" class="sl-loc-map" data-loc-src="sl-loc-{{ $qid }}" target="_blank" rel="noopener">View map</a>
                                </div>
                                @break

                            @case(22)
                                @if (filled($question->question_text))<label>{{ $question->question_text }}{!! $star !!}</label>@endif
                                <div class="sl-repeat" data-sl-repeat>
                                    <div class="sl-repeat-item">
                                        <label>Task | Activity{!! $star !!}</label>
                                        <textarea name="answers_meta[{{ $qid }}][task][]" rows="1" {{ $required }}>{{ old('answers_meta.'.$qid.'.task.0') }}</textarea>
                                        <label>Potential Hazards{!! $star !!}</label>
                                        <textarea name="answers_meta[{{ $qid }}][potential_hazards][]" rows="1" {{ $required }}>{{ old('answers_meta.'.$qid.'.potential_hazards.0') }}</textarea>
                                        <label>Risk Score (Before){!! $star !!}</label>
                                        <select name="answers_meta[{{ $qid }}][risk_score_before][]" style="width:150px;max-width:100%;" {{ $required }}>
                                            <option value="">Select</option>
                                            @for ($rs = 1; $rs <= 5; $rs++)
                                                <option value="{{ $rs }}" @selected(old('answers_meta.'.$qid.'.risk_score_before.0') == $rs)>{{ $rs }}</option>
                                            @endfor
                                        </select>
                                        <label>Photo (optional)</label>
                                        {{-- Legacy SWMS photo input is `multiple`; names are re-indexed per row by the Add-another JS. --}}
                                        <input type="file" name="answers_file[{{ $qid }}][0][]" multiple accept="image/*" class="sl-multi-upload">
                                        <label>Control Measures{!! $star !!}</label>
                                        <textarea name="answers_meta[{{ $qid }}][control_measures][]" rows="1" {{ $required }}>{{ old('answers_meta.'.$qid.'.control_measures.0') }}</textarea>
                                        <label>Risk Score (After){!! $star !!}</label>
                                        <select name="answers_meta[{{ $qid }}][risk_score_after][]" style="width:150px;max-width:100%;" {{ $required }}>
                                            <option value="">Select</option>
                                            @for ($rs = 1; $rs <= 5; $rs++)
                                                <option value="{{ $rs }}" @selected(old('answers_meta.'.$qid.'.risk_score_after.0') == $rs)>{{ $rs }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    {{-- Legacy add_another_hazard: appends another full SWMS hazard row. --}}
                                    <a href="javascript:;" class="sl-add-another" style="display:inline-block;margin-top:.5rem;font-size:.9rem;color:#008C00;font-weight:600;">+ Add another</a>
                                </div>
                                @break

                            @case(24)
                                <label>{{ $question->question_text ?: 'Send email notifications to' }}{!! $star !!}</label>
                                {{-- Legacy "Add another": multiple additional recipient emails. --}}
                                <div class="sl-repeat" data-sl-repeat>
                                    <div class="sl-repeat-item">
                                        <input type="email" name="answers[{{ $qid }}][]" value="{{ old('answers.'.$qid.'.0') }}" {{ $required }}>
                                    </div>
                                    <a href="javascript:;" class="sl-add-another" style="display:inline-block;margin-top:.4rem;font-size:.9rem;color:#008C00;font-weight:600;">+ Add another</a>
                                </div>
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
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][visitor_name]" value="{{ old('answers_meta.'.$qid.'.visitor_name') }}" required {{ $required }}>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Visitor phone <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" inputmode="numeric" name="answers_meta[{{ $qid }}][visitor_phone]" value="{{ old('answers_meta.'.$qid.'.visitor_phone') }}" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Date <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="date" name="answers_meta[{{ $qid }}][checkin_date]" value="{{ old('answers_meta.'.$qid.'.checkin_date') }}" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Time <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="time" name="answers_meta[{{ $qid }}][checkin_time]" value="{{ old('answers_meta.'.$qid.'.checkin_time') }}" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Venue name <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][venue_name]" value="{{ old('answers_meta.'.$qid.'.venue_name') }}" required>
                                    </p>
                                    <p>
                                        <span style="color:{{ $fg }};">Venue Address <span style="color:red">*</span></span><br>
                                        <input class="input-field" type="text" name="answers_meta[{{ $qid }}][venue_address]" value="{{ old('answers_meta.'.$qid.'.venue_address') }}" required>
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
                                                <option value="{{ $locationType }}" @selected(old('answers_meta.'.$qid.'.location_type') === $locationType)>{{ $locationType }}</option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p class="vehicle_no" id="vehicle_no_{{ $qid }}"></p>
                                </div>
                                @break

                            @default
                                <label>{{ $question->question_text }}{!! $star !!}</label>
                                <textarea name="answers[{{ $qid }}]" rows="2" {{ $required }}></textarea>
                        @endswitch
                    </div>
                @endforeach
                <p style="margin-top:.5rem;"><input class="submit-btn" type="submit" value="Submit"></p>
                @if ($errors->any())
                    <script>
                        // Bring the visitor straight to the problem after the redirect back.
                        document.addEventListener('DOMContentLoaded', function () {
                            var target = document.querySelector('.fb-q-error') || document.getElementById('sl-form-errors');
                            if (target) {
                                setTimeout(function () {
                                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }, 150);
                            }
                        });
                    </script>
                @endif
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
            <script>
                // Repeatable signature pads (type 16): class-based init so "Add another" clones work.
                (function () {
                    function initSigCanvas(canvas) {
                        if (!canvas || canvas.dataset.slSigInit === '1') return;
                        canvas.dataset.slSigInit = '1';
                        var wrap = canvas.closest('.signature-wrap');
                        var input = wrap ? wrap.querySelector('.sl-sig-input') : null;
                        var ctx = canvas.getContext('2d');
                        ctx.strokeStyle = '#111';
                        ctx.lineWidth = 2;
                        ctx.lineCap = 'round';
                        var drawing = false;
                        function pos(e) {
                            var r = canvas.getBoundingClientRect();
                            var t = e.touches ? e.touches[0] : e;
                            return { x: t.clientX - r.left, y: t.clientY - r.top };
                        }
                        function start(e) { drawing = true; ctx.beginPath(); var p = pos(e); ctx.moveTo(p.x, p.y); e.preventDefault(); }
                        function draw(e) { if (!drawing) return; var p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); e.preventDefault(); }
                        function end() { drawing = false; if (input) input.value = canvas.toDataURL('image/png'); }
                        canvas.addEventListener('mousedown', start);
                        canvas.addEventListener('mousemove', draw);
                        canvas.addEventListener('mouseup', end);
                        canvas.addEventListener('mouseleave', end);
                        canvas.addEventListener('touchstart', start, { passive: false });
                        canvas.addEventListener('touchmove', draw, { passive: false });
                        canvas.addEventListener('touchend', end);
                    }
                    window.slInitSignatures = function () {
                        document.querySelectorAll('.sl-sig-canvas').forEach(initSigCanvas);
                    };
                    window.slInitSignatures();
                    document.addEventListener('click', function (e) {
                        var btn = e.target.closest('.sl-sig-clear');
                        if (!btn) return;
                        e.preventDefault();
                        var wrap = btn.closest('.signature-wrap');
                        if (!wrap) return;
                        var canvas = wrap.querySelector('.sl-sig-canvas');
                        var input = wrap.querySelector('.sl-sig-input');
                        if (canvas) { canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height); }
                        if (input) input.value = '';
                    });
                })();
            </script>
            <script>
                // Legacy "Add another": clone the last repeat item, clearing its inputs.
                // Every ADDED section gets a "Remove" link (the original section never does),
                // so only sections created on this page can be deleted.
                (function () {
                    document.addEventListener('click', function (e) {
                        var rm = e.target.closest('.sl-remove-section');
                        if (rm) {
                            e.preventDefault();
                            var item = rm.closest('.sl-repeat-item');
                            if (item) { item.remove(); }
                            return;
                        }
                        var link = e.target.closest('.sl-add-another');
                        if (!link) return;
                        e.preventDefault();
                        var wrap = link.closest('[data-sl-repeat]');
                        if (!wrap) return;
                        var items = wrap.querySelectorAll('.sl-repeat-item');
                        if (!items.length) return;
                        var clone = items[items.length - 1].cloneNode(true);
                        clone.querySelectorAll('input, textarea, select').forEach(function (el) {
                            if (el.type === 'checkbox' || el.type === 'radio') { el.checked = false; } else { el.value = ''; }
                        });
                        // Reset any cloned signature pad so it initialises fresh (blank + re-bound).
                        clone.querySelectorAll('.sl-sig-canvas').forEach(function (c) {
                            c.removeAttribute('data-sl-sig-init');
                            try { c.getContext('2d').clearRect(0, 0, c.width, c.height); } catch (err) {}
                        });
                        // Cloned dropzones/previews lose their listeners — strip them and let
                        // slInitDropzones build fresh ones for the new row.
                        clone.querySelectorAll('.sl-dropzone, .sl-file-preview').forEach(function (el) { el.remove(); });
                        clone.querySelectorAll('input[type="file"]').forEach(function (fi) {
                            fi.removeAttribute('data-sl-dz');
                            fi.style.display = '';
                        });
                        if (!clone.querySelector('.sl-remove-section')) {
                            var del = document.createElement('a');
                            del.href = 'javascript:;';
                            del.className = 'sl-remove-section';
                            del.textContent = 'Remove';
                            del.style.cssText = 'display:block;text-align:right;color:#c62828;font-size:.85rem;font-weight:700;margin-top:.35rem;text-decoration:none;';
                            clone.appendChild(del);
                        }
                        wrap.insertBefore(clone, link);
                        // Re-index per-row file inputs (answers_file[qid][row][]) so each SWMS
                        // row keeps its own photo set.
                        var rows = wrap.querySelectorAll('.sl-repeat-item');
                        rows.forEach(function (row, idx) {
                            row.querySelectorAll('input[type="file"]').forEach(function (fi) {
                                if (/\[\d+\]\[\]$/.test(fi.name)) {
                                    fi.name = fi.name.replace(/\[\d+\]\[\]$/, '[' + idx + '][]');
                                }
                            });
                        });
                        if (window.slInitDropzones) { window.slInitDropzones(); }
                        if (window.slInitSignatures) { window.slInitSignatures(); }
                    });
                })();
            </script>
            <script>
                // Multi-image inputs (Upload Button + SWMS photos — the two spots legacy allows
                // multiple files) render as a drag & drop box; dropped/browsed files merge into
                // the input and feed the existing preview strip.
                (function () {
                    function mergeFiles(input, newFiles) {
                        try {
                            var dt = new DataTransfer();
                            Array.prototype.forEach.call(input.files || [], function (f) { dt.items.add(f); });
                            Array.prototype.forEach.call(newFiles || [], function (f) {
                                if (!input.accept || input.accept.indexOf('image') === -1 || /^image\//.test(f.type)) {
                                    dt.items.add(f);
                                }
                            });
                            input.files = dt.files;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        } catch (e) {}
                    }
                    function initDropzone(input) {
                        if (input.dataset.slDz === '1') return;
                        input.dataset.slDz = '1';
                        input.style.display = 'none';
                        var box = document.createElement('div');
                        box.className = 'sl-dropzone';
                        box.innerHTML = '<span style="font-size:1.3rem;display:block;margin-bottom:2px;">&#128247;</span>'
                            + 'Drag &amp; drop images here or <span style="color:#008C00;font-weight:700;text-decoration:underline;">browse</span>';
                        box.style.cssText = 'border:2px dashed #b6c8b6;border-radius:10px;background:#f6faf6;color:#557055;'
                            + 'text-align:center;padding:16px 10px;font-size:.85rem;cursor:pointer;margin:4px 0 2px;';
                        input.insertAdjacentElement('beforebegin', box);
                        box.addEventListener('click', function () { input.click(); });
                        box.addEventListener('dragover', function (e) { e.preventDefault(); box.style.background = '#e8f6e8'; });
                        box.addEventListener('dragleave', function () { box.style.background = '#f6faf6'; });
                        box.addEventListener('drop', function (e) {
                            e.preventDefault();
                            box.style.background = '#f6faf6';
                            if (e.dataTransfer && e.dataTransfer.files) { mergeFiles(input, e.dataTransfer.files); }
                        });
                    }
                    window.slInitDropzones = function () {
                        document.querySelectorAll('input.sl-multi-upload').forEach(initDropzone);
                    };
                    window.slInitDropzones();
                })();
            </script>
            <script>
                // Every file input shows a live preview of what was chosen — image thumbnails
                // or a document chip — each with a ✕ to remove that file before submitting.
                (function () {
                    function renderFilePreview(input) {
                        var wrap = input.nextElementSibling;
                        if (!wrap || !wrap.classList || !wrap.classList.contains('sl-file-preview')) {
                            wrap = document.createElement('div');
                            wrap.className = 'sl-file-preview';
                            wrap.style.cssText = 'display:flex;flex-wrap:wrap;gap:8px;margin:6px 0 2px;';
                            input.insertAdjacentElement('afterend', wrap);
                        }
                        wrap.innerHTML = '';
                        Array.prototype.forEach.call(input.files || [], function (f, idx) {
                            var item = document.createElement('div');
                            item.style.cssText = 'position:relative;border:1px solid #d1d5db;border-radius:8px;background:#fff;padding:4px;max-width:130px;';
                            if (/^image\//.test(f.type)) {
                                var img = document.createElement('img');
                                img.src = URL.createObjectURL(f);
                                img.onload = function () { URL.revokeObjectURL(img.src); };
                                img.style.cssText = 'display:block;width:120px;height:90px;object-fit:cover;border-radius:5px;';
                                item.appendChild(img);
                            } else {
                                var doc = document.createElement('div');
                                doc.textContent = '📄 ' + f.name;
                                doc.style.cssText = 'font-size:.78rem;color:#374151;max-width:120px;padding:14px 4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';
                                item.appendChild(doc);
                            }
                            var rm = document.createElement('button');
                            rm.type = 'button';
                            rm.className = 'sl-file-remove';
                            rm.setAttribute('data-idx', idx);
                            rm.textContent = '×';
                            rm.title = 'Remove';
                            rm.style.cssText = 'position:absolute;top:-7px;right:-7px;width:20px;height:20px;line-height:17px;text-align:center;border:0;border-radius:50%;background:#c62828;color:#fff;font-size:14px;cursor:pointer;padding:0;';
                            item.appendChild(rm);
                            wrap.appendChild(item);
                        });
                    }

                    document.addEventListener('change', function (e) {
                        if (e.target && e.target.matches && e.target.matches('form input[type="file"]')) {
                            renderFilePreview(e.target);
                        }
                    });

                    document.addEventListener('click', function (e) {
                        var rm = e.target.closest && e.target.closest('.sl-file-remove');
                        if (!rm) return;
                        e.preventDefault();
                        var wrap = rm.closest('.sl-file-preview');
                        var input = wrap && wrap.previousElementSibling;
                        if (!input || input.type !== 'file') return;
                        var idx = parseInt(rm.getAttribute('data-idx'), 10);
                        try {
                            var dt = new DataTransfer();
                            Array.prototype.forEach.call(input.files || [], function (f, i) {
                                if (i !== idx) { dt.items.add(f); }
                            });
                            input.files = dt.files;
                        } catch (err) {
                            input.value = '';
                        }
                        renderFilePreview(input);
                    });
                })();
            </script>
            <script>
                // Location Function (type 19): "Locate me" fills GPS coords; "View map" opens Google Maps.
                (function () {
                    document.addEventListener('click', function (e) {
                        var btn = e.target.closest('.sl-loc-btn');
                        if (btn) {
                            var input = document.getElementById(btn.getAttribute('data-loc-target'));
                            if (!input) { return; }
                            // Browsers only allow geolocation on secure (https) origins.
                            if (!navigator.geolocation || (!window.isSecureContext && location.hostname !== 'localhost')) {
                                (window.slAlert || alert)('Location requires a secure (https) connection. Please type your location instead.');
                                return;
                            }
                            var restore = btn.innerHTML;
                            btn.disabled = true;
                            btn.textContent = 'Locating…';
                            navigator.geolocation.getCurrentPosition(function (p) {
                                input.value = p.coords.latitude.toFixed(6) + ', ' + p.coords.longitude.toFixed(6);
                                btn.disabled = false;
                                btn.innerHTML = restore;
                            }, function (err) {
                                btn.disabled = false;
                                btn.innerHTML = restore;
                                // Tell the visitor why nothing happened instead of failing silently.
                                (window.slAlert || alert)(err && err.code === 1
                                    ? 'Location permission was denied. Please allow location access, or type your location.'
                                    : 'Unable to get your location. Please type it instead.');
                            }, { enableHighAccuracy: true, timeout: 8000 });
                            return;
                        }
                        var map = e.target.closest('.sl-loc-map');
                        if (map) {
                            var src = document.getElementById(map.getAttribute('data-loc-src'));
                            var val = src ? src.value.trim() : '';
                            map.href = val
                                ? ('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(val))
                                : 'https://www.google.com/maps';
                        }
                    });
                })();
            </script>
            <script>
                // Mobile/tablet only: add a "Take photo" (camera capture) option next to every
                // file field so users can shoot a photo, not just pick one from the library.
                // Desktop is left untouched (no camera → an upload-only file picker is correct).
                (function () {
                    function isMobileOrTablet() {
                        var ua = navigator.userAgent || '';
                        // Client Hints: the browser's own mobile flag (most reliable when present).
                        if (navigator.userAgentData && navigator.userAgentData.mobile === true) return true;
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|Tablet|Silk|Kindle/i.test(ua)) return true;
                        // iPadOS 13+ masquerades as desktop Safari but reports multi-touch.
                        if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) return true;
                        // Real touch device with a coarse primary pointer and no hover — i.e. a phone
                        // or tablet. Guards against touchscreen laptops (which still expose hover / a
                        // fine pointer) and desktop Electron shells (which report maxTouchPoints 0).
                        return navigator.maxTouchPoints > 0
                            && !!window.matchMedia
                            && window.matchMedia('(pointer: coarse)').matches
                            && window.matchMedia('(hover: none)').matches;
                    }
                    if (!isMobileOrTablet()) return;

                    document.querySelectorAll('form input[type="file"]').forEach(function (input) {
                        if (input.dataset.slCam) return;
                        input.dataset.slCam = '1';

                        // Hidden input whose `capture` hint opens the rear camera directly.
                        var cam = document.createElement('input');
                        cam.type = 'file';
                        cam.accept = 'image/*';
                        cam.setAttribute('capture', 'environment');
                        cam.style.display = 'none';

                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-outline';
                        btn.style.marginTop = '.4rem';
                        btn.innerHTML = '📷 Take photo';

                        input.insertAdjacentElement('afterend', btn);
                        btn.insertAdjacentElement('afterend', cam);

                        btn.addEventListener('click', function () { cam.click(); });
                        cam.addEventListener('change', function () {
                            if (!cam.files || !cam.files.length) return;
                            try {
                                // Merge the captured photo into the real field so it submits under
                                // the field's own name (keeps `multiple`/required/preview intact).
                                var dt = new DataTransfer();
                                if (input.multiple && input.files) {
                                    for (var i = 0; i < input.files.length; i++) dt.items.add(input.files[i]);
                                }
                                for (var j = 0; j < cam.files.length; j++) dt.items.add(cam.files[j]);
                                input.files = dt.files;
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                            } catch (e) {
                                // Old browsers without DataTransfer: submit the camera input directly.
                                cam.name = input.name;
                                cam.style.display = '';
                            }
                        });
                    });
                })();
            </script>
        @endif

        {{-- Legacy mobile order: Form → Gallery → Documents → Links → Share (mobile/index.php). --}}
        @if ($profile->pictures->isNotEmpty() && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
            <div class="gallery" data-sl-gallery>
                @foreach ($profile->pictures as $picture)
                    @if ($picUrl = $publicMediaUrl($picture->picture_name))
                        <figure>
                            <img src="{{ $picUrl }}" alt="{{ $picture->txt_footer ?: 'Picture' }}"
                                 class="sl-gallery-img" style="cursor:pointer;"
                                 data-full="{{ $picUrl }}" data-caption="{{ $picture->txt_footer }}">
                            @if ($picture->txt_footer)
                                <figcaption style="font-size:.85rem;color:#666;margin-top:.25rem;">{{ $picture->txt_footer }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($profile->documents->isNotEmpty() && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
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
                            style="background-color:{{ $docColor }};text-align:{{ $docAlign }};"
                        >
                            {{ $document->name ?: 'Download document' }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        @php
            $visibleWeblinks = $profile->weblinks->filter(function ($weblink): bool {
                $enabled = $weblink->link_button === true
                    || $weblink->link_button === 1
                    || $weblink->link_button === '1';

                return $enabled && filled($weblink->link_button_url);
            });
        @endphp
        @if ($visibleWeblinks->isNotEmpty() && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
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
                        style="background-color:{{ $color }};text-align:{{ $align }};"
                    >
                        {{ $weblink->link_button_text ?: 'Open link' }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($profile->display_share_link && ! in_array($profile->typeSlug(), $slOrderedTileTypes, true))
            @php
                // Legacy: Facebook shares the live page URL; Twitter/Email use the short URL.
                $liveUrl = url('/'.$clientUrl.'/'.$profile->id);
                $shareUrl = filled($profile->shorturl) ? (string) $profile->shorturl : $liveUrl;
                $shareText = trim((string) ($profile->name ?: $profile->code_profile_name ?: 'ScanLink'));
            @endphp
            <div class="shareNav-mob">
                <a
                    class="shareFB"
                    href="https://www.facebook.com/share.php?u={{ urlencode($liveUrl) }}"
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

        {{-- Legacy: data collection is a popup shown over the content on load (name/surname/mobile/email). --}}
        @if ($needsVisitorInfo)
            @php
                $dcBtnText = filled($profile->data_collection_btn_text) ? $profile->data_collection_btn_text : 'Proceed';
                $dcBtnColor = trim((string) ($profile->data_collection_btn_color ?: ''));
                $dcBtnStyle = $dcBtnColor !== ''
                    ? 'background-color:'.(str_starts_with($dcBtnColor, '#') ? $dcBtnColor : '#'.$dcBtnColor).';'
                    : '';
            @endphp
            <div class="sl-dc-overlay" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:flex;align-items:flex-start;justify-content:center;padding:2rem 1rem;overflow:auto;">
                <div style="background:#fff;border-radius:8px;max-width:380px;width:100%;padding:1.25rem;box-sizing:border-box;">
                    <p style="margin:0 0 .75rem;font-weight:600;">{{ $profile->data_collection_content ?: 'Register your mobile number to receive exclusive weekly offers.' }}</p>
                    <form method="post" action="{{ route('scan.visitor', [$clientUrl, $profile->id]) }}">
                        @csrf
                        {{-- Legacy: fields are required only in compulsory mode (set_up_compulsory);
                             in that mode every enabled field is required, incl. mobile. --}}
                        @if ($profile->data_collection_name)
                            <label>Name</label>
                            <input type="text" name="name" {{ $profile->set_up_compulsory ? 'required' : '' }}>
                        @endif
                        @if ($profile->data_collection_surname)
                            <label>Surname</label>
                            <input type="text" name="surname" {{ $profile->set_up_compulsory ? 'required' : '' }}>
                        @endif
                        @if ($profile->data_collection_mobile)
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Enter a valid 10 digit mobile phone number" {{ $profile->set_up_compulsory ? 'required' : '' }}>
                        @endif
                        @if ($profile->data_collection_email)
                            <label>Email Address</label>
                            <input type="email" name="email" maxlength="255" {{ $profile->set_up_compulsory ? 'required' : '' }}>
                        @endif
                        <p style="margin-top:1rem;text-align:center;"><button class="btn" type="submit" style="{{ $dcBtnStyle }}">{{ $dcBtnText }}</button></p>
                    </form>
                </div>
            </div>
        @endif

        @else
            {{-- Outside the activation window: legacy shows only the company logo. --}}
            @if ($profile->logos->isNotEmpty())
                <div class="logo-row">
                    @foreach ($profile->logos as $logo)
                        @if ($outLogoUrl = $publicMediaUrl($logo->logo_name))
                            <img src="{{ $outLogoUrl }}" alt="Company logo">
                        @endif
                    @endforeach
                </div>
            @endif
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

{{-- Gallery lightbox: click a gallery image to open it full-size with prev/next + autoplay. --}}
<div id="sl-lightbox" style="display:none;position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.9);align-items:center;justify-content:center;flex-direction:column;">
    <img id="sl-lightbox-img" src="" alt="" style="max-width:92vw;max-height:80vh;object-fit:contain;box-shadow:0 4px 30px rgba(0,0,0,.5);border-radius:4px;">
    <div id="sl-lightbox-caption" style="color:#fff;margin-top:12px;font-size:14px;text-align:center;max-width:92vw;"></div>
    <button type="button" id="sl-lb-close" aria-label="Close" style="position:absolute;top:14px;right:18px;background:none;border:0;color:#fff;font-size:34px;line-height:1;cursor:pointer;">&times;</button>
    <button type="button" id="sl-lb-prev" aria-label="Previous" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:0;color:#fff;font-size:28px;width:46px;height:46px;border-radius:50%;cursor:pointer;">&#8249;</button>
    <button type="button" id="sl-lb-next" aria-label="Next" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:0;color:#fff;font-size:28px;width:46px;height:46px;border-radius:50%;cursor:pointer;">&#8250;</button>
    <button type="button" id="sl-lb-play" style="position:absolute;bottom:16px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,.15);border:0;color:#fff;font-size:13px;padding:8px 14px;border-radius:20px;cursor:pointer;">&#9654; Autoplay</button>
</div>
<script>
    (function () {
        var imgs = Array.prototype.slice.call(document.querySelectorAll('.sl-gallery-img'));
        if (! imgs.length) { return; }
        var lb = document.getElementById('sl-lightbox');
        var lbImg = document.getElementById('sl-lightbox-img');
        var lbCap = document.getElementById('sl-lightbox-caption');
        var playBtn = document.getElementById('sl-lb-play');
        var idx = 0, timer = null;
        function show(i) {
            idx = (i + imgs.length) % imgs.length;
            var el = imgs[idx];
            lbImg.src = el.getAttribute('data-full') || el.src;
            lbCap.textContent = el.getAttribute('data-caption') || '';
        }
        function stop() { if (timer) { clearInterval(timer); timer = null; playBtn.innerHTML = '&#9654; Autoplay'; } }
        function next() { show(idx + 1); }
        function prev() { show(idx - 1); }
        function open(i) { show(i); lb.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        function close() { lb.style.display = 'none'; document.body.style.overflow = ''; stop(); }
        function play() { if (timer) { stop(); return; } timer = setInterval(next, 2500); playBtn.innerHTML = '&#10073;&#10073; Pause'; }
        imgs.forEach(function (el, i) { el.addEventListener('click', function () { open(i); }); });
        document.getElementById('sl-lb-close').addEventListener('click', close);
        document.getElementById('sl-lb-next').addEventListener('click', function () { stop(); next(); });
        document.getElementById('sl-lb-prev').addEventListener('click', function () { stop(); prev(); });
        playBtn.addEventListener('click', play);
        lb.addEventListener('click', function (e) { if (e.target === lb) { close(); } });
        document.addEventListener('keydown', function (e) {
            if (lb.style.display !== 'flex') { return; }
            if (e.key === 'Escape') { close(); }
            else if (e.key === 'ArrowRight') { stop(); next(); }
            else if (e.key === 'ArrowLeft') { stop(); prev(); }
        });
    })();
</script>

{{--
    Scan geolocation: attach the visitor's GPS (and IP-derived country/region/city, resolved
    server-side) to the scan row recorded on page load. Self-contained — posts back to this app
    instead of the external Galatech API. Suppressed for the editor preview and right after a
    form submit (legacy guarded on `$form_submit_success == ''`).
--}}
@if (! ($portalPreview ?? false)
    && request('ask_for_location') !== 'no'
    && ! session('form_submitted')
    && ! empty($scanHitId))
    <script>
        (function () {
            var ENDPOINT = @json(route('scan.geo', [$clientUrl, $profile->id]));
            var TOKEN = @json(csrf_token());
            var HITID = @json((int) $scanHitId);

            function send(lat, lng) {
                var body = 'scan_hit_id=' + encodeURIComponent(HITID)
                    + '&lat=' + encodeURIComponent(lat == null ? '' : lat)
                    + '&lng=' + encodeURIComponent(lng == null ? '' : lng)
                    + '&screensize=' + encodeURIComponent(window.screen ? (window.screen.width + 'x' + window.screen.height) : '');
                try {
                    fetch(ENDPOINT, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': TOKEN, 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body,
                        keepalive: true,
                        credentials: 'same-origin'
                    }).catch(function () {});
                } catch (e) {}
            }

            function fire() {
                if (!navigator.geolocation) { send('', ''); return; }
                navigator.geolocation.getCurrentPosition(
                    function (p) { send(p.coords.latitude.toFixed(4), p.coords.longitude.toFixed(4)); },
                    function () { send('', ''); },
                    { enableHighAccuracy: true, timeout: 3000 }
                );
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fire);
            } else {
                fire();
            }
        })();
    </script>
@endif
{{-- Themed slAlert/slConfirm (same dialog as the portal) for the visitor page. --}}
@include('filament.hooks.themed-dialog')
</body>
</html>
