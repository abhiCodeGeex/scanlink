{{--
    Legacy tiles_order parity: exhibit/voc render their content tiles in the user-saved
    order (profiles.tiles_order). Each tile id below maps to the same markup the fixed
    show.blade flow uses for other types. Non-visual tiles (Data Collection form, Code
    Type, Header, Security, Form Builder) are skipped here — they render in their own
    fixed spots in show.blade.

    Exhibit tiles: 1 Logo · 2 Videos · 3 Words · 4 Pictures · 5 Documents · 6 Web Link
                   · 11 Share · 14 Videos #2 · 15 Words #2 · 16 Pictures #2 · 17 Logo #2
    VOC tiles:     1 Logo · 2 Title Bar · 3 Profile Picture · 4 Profile Information · 5 Documents
--}}
@php
    /** @var \App\Models\Profile $profile */
    $slug = $profile->typeSlug();
@endphp
@foreach ($profile->mobileTileOrder() as $tileId)
    @switch($slug . '-' . $tileId)

        {{-- ============================ EXHIBIT ============================ --}}
        @case('exhibit-1')
            @if ($profile->logos->isNotEmpty())
                <div class="logo-row">
                    @foreach ($profile->logos as $logo)
                        @if ($u = $publicMediaUrl($logo->logo_name))<img src="{{ $u }}" alt="Company logo">@endif
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-17')
            @if ($profile->logosExtra->isNotEmpty())
                <div class="logo-row">
                    @foreach ($profile->logosExtra as $lx)
                        @if ($u = $publicMediaUrl($lx->logo_name))
                            @if (filled($lx->logo_url))<a href="{{ $lx->logo_url }}" target="_blank" rel="noopener"><img src="{{ $u }}" alt="Company logo"></a>@else<img src="{{ $u }}" alt="Company logo">@endif
                        @endif
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-2')
        @case('exhibit-14')
            @php $vids = $profile->videos->where('is_extra', $tileId === 14); @endphp
            @if ($vids->isNotEmpty())
                @foreach ($vids as $video)
                    @php $embed = $youtubeEmbedUrl((string) $video->video_name); @endphp
                    @if ($embed)
                        <div class="video-wrap"><iframe src="{{ $embed }}" allowfullscreen loading="lazy" title="{{ $video->title ?: 'YouTube video' }}"></iframe></div>
                    @elseif (filled($video->video_name))
                        <a class="btn" href="{{ $video->video_name }}" target="_blank" rel="noopener">{{ $video->title ?: 'Watch video' }}</a>
                    @endif
                @endforeach
            @endif
            @break

        @case('exhibit-3')
            @php $d = trim(str_replace('&nbsp;', '', strip_tags((string) $profile->description))); @endphp
            @if (filled($profile->name) || $d !== '')
                <div class="MobileBottomText">
                    @if (filled($profile->name))<p>{{ $profile->name }}</p>@endif
                    @if ($d !== '')<p>{!! $profile->description !!}</p>@endif
                </div>
            @endif
            @break

        @case('exhibit-15')
            @php $d2 = trim(str_replace('&nbsp;', '', strip_tags((string) $profile->description2))); @endphp
            @if (filled($profile->name2) || $d2 !== '')
                <div class="MobileBottomText">
                    @if (filled($profile->name2))<p>{{ $profile->name2 }}</p>@endif
                    @if ($d2 !== '')<p>{!! $profile->description2 !!}</p>@endif
                </div>
            @endif
            @break

        @case('exhibit-4')
            @if ($profile->pictures->isNotEmpty())
                <div class="gallery" data-sl-gallery>
                    @foreach ($profile->pictures as $pic)
                        @if ($u = $publicMediaUrl($pic->picture_name))
                            <figure><img src="{{ $u }}" alt="{{ $pic->txt_footer ?: 'Picture' }}" class="sl-gallery-img" style="cursor:pointer;" data-full="{{ $u }}" data-caption="{{ $pic->txt_footer }}">@if ($pic->txt_footer)<figcaption style="font-size:.85rem;color:#666;margin-top:.25rem;">{{ $pic->txt_footer }}</figcaption>@endif</figure>
                        @endif
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-16')
            @if ($profile->picturesExtra->isNotEmpty())
                <div class="gallery">
                    @foreach ($profile->picturesExtra as $pic)
                        @if ($u = $publicMediaUrl($pic->picture_name))
                            <figure><img src="{{ $u }}" alt="{{ $pic->txt_footer ?: 'Picture' }}" class="sl-gallery-img" style="cursor:pointer;" data-full="{{ $u }}" data-caption="{{ $pic->txt_footer }}">@if ($pic->txt_footer)<figcaption style="font-size:.85rem;color:#666;margin-top:.25rem;">{{ $pic->txt_footer }}</figcaption>@endif</figure>
                        @endif
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-5')
            @if ($profile->documents->isNotEmpty())
                <div class="tile-grid" style="display:block;">
                    @foreach ($profile->documents as $doc)
                        @if ($u = $publicMediaUrl($doc->doc_name))
                            @php
                                $c = trim((string) ($doc->btn_color ?: '007A01')); $c = str_starts_with($c, '#') ? $c : '#' . $c;
                                $a = in_array($doc->txt_align, ['left', 'center', 'right'], true) ? $doc->txt_align : 'left';
                            @endphp
                            <a class="btn btn-document" href="{{ $u }}" target="_blank" rel="noopener" style="background-color:{{ $c }};text-align:{{ $a }};">{{ $doc->name ?: 'Download document' }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-6')
            @php
                $links = $profile->weblinks->filter(fn ($w): bool => ($w->link_button === true || $w->link_button === 1 || $w->link_button === '1') && filled($w->link_button_url));
            @endphp
            @if ($links->isNotEmpty())
                <div class="tile-grid" style="display:block;">
                    @foreach ($links as $w)
                        @php
                            $c = trim((string) ($w->link_button_color ?: '007A01')); $c = str_starts_with($c, '#') ? $c : '#' . $c;
                            $a = in_array($w->link_button_align, ['left', 'center', 'right'], true) ? $w->link_button_align : 'left';
                        @endphp
                        <a class="btn btn-weblink" href="{{ $w->link_button_url }}" target="_blank" rel="noopener" style="background-color:{{ $c }};text-align:{{ $a }};">{{ $w->link_button_text ?: 'Open link' }}</a>
                    @endforeach
                </div>
            @endif
            @break

        @case('exhibit-11')
            @if ($profile->display_share_link)
                @php
                    $shareUrl = filled($profile->shorturl) ? (string) $profile->shorturl : url('/' . $clientUrl . '/' . $profile->id);
                    $shareText = trim((string) ($profile->name ?: $profile->code_profile_name ?: 'ScanLink'));
                @endphp
                <div class="shareNav-mob">
                    <a class="shareFB" href="https://www.facebook.com/share.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" title="Facebook">Facebook</a>
                    <a class="shareTWT" href="https://twitter.com/share?text={{ urlencode($shareText . '  ' . $shareUrl) }}" target="_blank" rel="noopener" title="Twitter">Twitter</a>
                    <a class="shareEML" href="mailto:?subject={{ rawurlencode('Visit this link') }}&body={{ rawurlencode('Hi, I found this information for you! Have a nice day :) : ' . $shareUrl) }}" target="_blank" rel="noopener" title="Email">Email</a>
                </div>
            @endif
            @break

        {{-- ============================== VOC ============================== --}}
        @case('voc-1')
            @if ($profile->logos->isNotEmpty())
                <div class="logo-row">
                    @foreach ($profile->logos as $logo)
                        @if ($u = $publicMediaUrl($logo->logo_name))<img src="{{ $u }}" alt="Company logo">@endif
                    @endforeach
                </div>
            @endif
            @break

        @case('voc-2')
            @if ($profile->voc_title_bar_enable && trim(strip_tags((string) $profile->voc_title_bar_text)) !== '')
                @php $tb = trim((string) ($profile->voc_title_bar_colour ?: '777777')); $tb = str_starts_with($tb, '#') ? $tb : '#' . $tb; @endphp
                <div style="background-color:{{ $tb }};color:#fff;padding:10px 12px;border-radius:6px;margin-bottom:.75rem;">{!! $profile->voc_title_bar_text !!}</div>
            @endif
            @break

        @case('voc-3')
            @if ($profile->pictures->isNotEmpty())
                <div class="gallery">
                    @foreach ($profile->pictures as $pic)
                        @if ($u = $publicMediaUrl($pic->picture_name))<figure><img src="{{ $u }}" alt="Profile picture" class="sl-gallery-img" style="cursor:pointer;" data-full="{{ $u }}" data-caption=""></figure>@endif
                    @endforeach
                </div>
            @endif
            @break

        @case('voc-4')
            <div class="MobileBottomText">
                @foreach ([
                    'voc_first_name' => 'First Name', 'voc_last_name' => 'Last Name', 'voc_address' => 'Address',
                    'voc_town' => 'Town', 'voc_state' => 'State/Territory', 'voc_phone' => 'Telephone No.',
                    'voc_dob' => 'Date of Birth', 'voc_known_allergies' => 'Known Allergies/Medical Conditions',
                    'voc_blood_type' => 'Blood Type', 'voc_next_of_kin' => 'Next of Kin', 'voc_contact_phone' => 'Contact Telephone No.',
                    'voc_employer' => 'Employer', 'voc_emp_address' => 'Address', 'voc_emp_town' => 'Town',
                    'voc_emp_state' => 'State/Territory', 'voc_emp_phone' => 'Telephone No.',
                ] as $vf => $vl)
                    @php
                        $vv = $profile->{$vf};
                        if ($vf === 'voc_dob' && $vv) { $vv = $vv instanceof \Carbon\CarbonInterface ? $vv->format('d/m/Y') : $vv; }
                    @endphp
                    @if (filled($vv) && (string) $vv !== '1970-01-01')
                        <h3>{{ $vl }}</h3>
                        <p>{{ $vv }}</p>
                    @endif
                @endforeach
            </div>
            @break

        @case('voc-5')
            @if ($profile->vocDocuments->isNotEmpty())
                <div class="tile-grid" style="display:block;">
                    @foreach ($profile->vocDocuments as $vd)
                        @php $u = $publicMediaUrl($vd->file_name); @endphp
                        @if ($u)
                            @php
                                $exp = $vd->expiry_date;
                                $hasExp = $exp && ! in_array((string) $exp, ['1970-01-01', '0000-00-00'], true);
                                $expCarbon = $hasExp ? \Illuminate\Support\Carbon::parse($exp) : null;
                                $expLabel = $expCarbon ? $expCarbon->format('d/m/Y') : '';
                                // Legacy diff_date_expiry colour: green (>30 days / none),
                                // orange (1-30 days), red (expired / due today).
                                $docColor = '#007A01';
                                if ($expCarbon) {
                                    $today = \Illuminate\Support\Carbon::today();
                                    $expDay = $expCarbon->copy()->startOfDay();
                                    $docColor = $expDay->lte($today)
                                        ? '#c0392b'
                                        : ($expDay->lte($today->copy()->addDays(30)) ? '#e08600' : '#007A01');
                                }
                            @endphp
                            <a class="btn btn-document" href="{{ $u }}" target="_blank" rel="noopener" style="background-color:{{ $docColor }};text-align:left;">{{ $vd->name ?: 'Document' }}@if ($expLabel !== '') (Expires {{ $expLabel }})@endif</a>
                        @endif
                    @endforeach
                </div>
            @endif
            @break

    @endswitch
@endforeach
