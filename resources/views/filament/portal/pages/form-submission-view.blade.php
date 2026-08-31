<x-filament-panels::page>
    <style>
        .sl-fsview {
            --fs-green: #008901;
            --fs-border: #e5e7eb;
            --fs-muted: #6b7280;
            --fs-text: #111827;
            /* Typography matches the Download All submissions PDF exactly.
               That PDF is TCPDF with SetFont('helvetica', '', 10) and pt sizes; at 96dpi
               1pt = 4/3px, so 10pt = 13.3px, 9.5pt = 12.7px, 10.5pt = 14px, 15pt = 20px. */
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13.3px;
            max-width: 660px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--fs-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.06);
            overflow: hidden;
            color: var(--fs-text);
        }
        .sl-fsview__head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px 24px;
            padding: 22px 28px;
            border-bottom: 1px solid var(--fs-border);
            background: linear-gradient(180deg, #f7faf7 0%, #ffffff 100%);
        }
        .sl-fsview__logo img { max-height: 72px; max-width: 240px; width: auto; height: auto; display: block; }
        .sl-fsview__meta { text-align: right; }
        /* PDF: 15pt green bold. */
        .sl-fsview__title { font-size: 20px; font-weight: 700; color: var(--fs-green); margin: 0 0 4px; }
        /* PDF: 9.5pt meta line, with the "Profile N" run at 10.5pt. */
        .sl-fsview__meta-line { font-size: 12.7px; color: var(--fs-muted); margin: 1px 0; }
        .sl-fsview__meta-line b { color: var(--fs-text); font-weight: 700; font-size: 14px; }
        .sl-fsview__body { padding: 10px 28px 6px; }
        .sl-fsview__row { display: flex; gap: 20px; padding: 13px 0; border-bottom: 1px solid #f3f4f6; }
        .sl-fsview__row:last-child { border-bottom: 0; }
        .sl-fsview__q {
            flex: 0 0 26%;
            max-width: 26%;
            /* PDF label column: 26% wide, 9.5pt. */
            font-size: 12.7px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: var(--fs-muted);
            padding-top: 2px;
            word-break: break-word;
        }
        /* PDF value column: 74% wide, 10pt. */
        .sl-fsview__a { flex: 1; min-width: 0; font-size: 13.3px; line-height: 1.5; overflow-wrap: break-word; }
        .sl-fsview__a a { color: var(--fs-green); font-weight: 600; }
        /* Natural-size thumbnails, never upscaled — the image links open the full file. */
        .sl-fsview__a img { width: auto !important; max-width: min(260px, 100%) !important; height: auto !important; border-radius: 6px; }
        .sl-fsview__a img[alt="Signature"] { max-width: min(340px, 100%) !important; }
        .sl-fsview__section { padding: 13px 0; border-bottom: 1px solid #f3f4f6; }
        .sl-fsview__section:last-child { border-bottom: 0; }
        .sl-fsview__sec-label {
            font-size: 12.7px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: var(--fs-muted);
            margin: 0 0 10px;
        }
        .sl-fsview__sec-body { font-size: 13.3px; line-height: 1.5; overflow-wrap: break-word; }
        .sl-fsview__sec-body a { color: var(--fs-green); font-weight: 600; }
        .sl-fsview__sec-body img { width: auto !important; max-width: min(260px, 100%) !important; height: auto !important; border-radius: 6px; }
        .sl-fsview__sec-body img[alt="Signature"] { max-width: min(340px, 100%) !important; }
        .sl-fsview__sec-body table { width: 100% !important; table-layout: fixed; }
        .sl-fsview__a table { width: 100% !important; table-layout: fixed; }
        /* FormSubmissionPresenter emits the screen HTML for SWMS rows, repeatable
           signatures and field groups with INLINE sizes (12.5px labels / headings,
           13.5px values). The PDF renders the same content at 9pt / 10pt. Re-point the
           inline sizes at the pt equivalents so both documents read identically -- same
           technique as the dark-mode block below (a !important rule beats a
           non-important inline style). */
        .sl-fsview__a [style*="font-size:12.5px"],
        .sl-fsview__sec-body [style*="font-size:12.5px"] { font-size: 12px !important; }
        .sl-fsview__a [style*="font-size:13.5px"],
        .sl-fsview__sec-body [style*="font-size:13.5px"] { font-size: 13.3px !important; }
        .sl-fsview__h1 { font-size: 19px; color: var(--fs-green); margin: 18px 0 4px; font-weight: 700; }
        .sl-fsview__h3 { font-size: 15px; color: var(--fs-text); margin: 14px 0 2px; font-weight: 700; }
        .sl-fsview__html { padding: 10px 0; font-size: 13.3px; color: var(--fs-text); line-height: 1.5; }
        .sl-fsview__foot {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
            padding: 16px 28px;
            border-top: 1px solid var(--fs-border);
            background: #fafbfa;
        }
        .sl-fsview__btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--fs-green);
            color: #fff !important;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none !important;
            border-radius: 8px;
            padding: 9px 16px;
            transition: background .15s ease;
        }
        .sl-fsview__btn:hover { background: #006b01; }
        .sl-fsview__btn--ghost { background: #fff; color: #374151 !important; border: 1px solid #d1d5db; }
        .sl-fsview__btn--ghost:hover { background: #f3f4f6; }
        @media (max-width: 560px) {
            .sl-fsview__head { padding: 16px 18px; }
            .sl-fsview__body { padding: 6px 18px 2px; }
            .sl-fsview__foot { padding: 14px 18px; }
            .sl-fsview__row { flex-direction: column; gap: 4px; }
            .sl-fsview__q { flex: none; max-width: none; }
        }
        /* Dark mode: the presenter emits INLINE colors (#111827 values, #6b7280 labels,
           #008901 headings) that vanish on the dark card — override them by matching the
           inline style attribute (CSS !important beats non-important inline styles). */
        html.dark .sl-fsview__a [style*="#111827"],
        html.dark .sl-fsview__sec-body [style*="#111827"] { color: rgb(229 231 235) !important; }
        html.dark .sl-fsview__a [style*="#6b7280"],
        html.dark .sl-fsview__sec-body [style*="#6b7280"] { color: rgb(156 163 175) !important; }
        html.dark .sl-fsview__a [style*="#008901"],
        html.dark .sl-fsview__sec-body [style*="#008901"],
        html.dark .sl-fsview__a [style*="#065f06"],
        html.dark .sl-fsview__sec-body [style*="#065f06"] { color: rgb(74 222 128) !important; }
        html.dark .sl-fsview__a [style*="#4b5563"],
        html.dark .sl-fsview__sec-body [style*="#4b5563"] { color: rgb(156 163 175) !important; }
        html.dark .sl-fsview__sec-body { color: rgb(229 231 235); }
        html.dark .sl-fsview { background: rgb(17 24 39); border-color: rgb(55 65 81); }
        html.dark .sl-fsview__head { background: rgb(24 33 47); border-color: rgb(55 65 81); }
        html.dark .sl-fsview__foot { background: rgb(24 33 47); border-color: rgb(55 65 81); }
        html.dark .sl-fsview__a, html.dark .sl-fsview__html, html.dark .sl-fsview__h3 { color: rgb(229 231 235); }
        html.dark .sl-fsview__meta-line b { color: rgb(243 244 246); }
        html.dark .sl-fsview__row { border-color: rgb(41 51 65); }
        /* The overrides above only catch the presenter's INLINE colours. Everything driven
           by these classes kept its light-theme colour on the dark card: the title and the
           muted labels all sat under 4:1, and the SWMS row divider drew a near-white line.
           Measured on the page: 3.53 / 3.35 / 3.67 before, 9.29 / 6.38 / 6.99 after. */
        html.dark .sl-fsview__title { color: rgb(74 222 128); }
        html.dark .sl-fsview__meta-line { color: rgb(156 163 175); }
        html.dark .sl-fsview__sec-label,
        html.dark .sl-fsview__q { color: rgb(156 163 175); }
        html.dark .sl-fsview__a [style*="#d1d5db"],
        html.dark .sl-fsview__sec-body [style*="#d1d5db"] { border-color: rgb(55 65 81) !important; }
        /* Uploaded photos/signatures are drawn on a white chip with a light border. */
        html.dark .sl-fsview__a [style*="#e5e7eb"],
        html.dark .sl-fsview__sec-body [style*="#e5e7eb"] {
            border-color: rgb(55 65 81) !important;
            background: rgb(31 41 55) !important;
        }
        html.dark .sl-fsview__btn--ghost {
            background: rgb(31 41 55);
            color: rgb(229 231 235) !important;
            border-color: rgb(75 85 99);
        }
        html.dark .sl-fsview__btn--ghost:hover { background: rgb(55 65 81); }
    </style>

    <div class="sl-fsview">
        <div class="sl-fsview__head">
            <div class="sl-fsview__logo">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo">
                @endif
            </div>
            <div class="sl-fsview__meta">
                <div class="sl-fsview__title">Form Submission</div>
                <div class="sl-fsview__meta-line"><b>Profile {{ $selectedProfileId }}</b> · {{ $profileName }}</div>
                <div class="sl-fsview__meta-line">Submitted {{ $submittedAt }}</div>
            </div>
        </div>

        <div class="sl-fsview__body">
            @foreach ($questions as $question)
                @php
                    $tid = (int) $question->question_type_id;
                    $answer = $this->answerFor((int) $question->question_id);
                @endphp

                @if ($tid === 10)
                    <div class="sl-fsview__h1">{{ strip_tags((string) $question->question_text) }}</div>
                @elseif ($tid === 12)
                    <div class="sl-fsview__h3">{{ strip_tags((string) $question->question_text) }}</div>
                @elseif ($tid === 2 || $tid === 13 || $tid === 14)
                    <div class="sl-fsview__html">{!! $question->question_text !!}</div>
                @elseif ($tid === 21)
                    {{-- Document Button: the element's document is the content — link it. --}}
                    @php $docHref = \App\Support\FormBuilderMedia::resolveDocumentHref($question); @endphp
                    @if ($docHref)
                        <div class="sl-fsview__row">
                            <div class="sl-fsview__q">{{ $question->doc_title ?: 'Document' }}</div>
                            <div class="sl-fsview__a">
                                <a href="{{ $docHref }}" target="_blank" rel="noopener">{{ trim((string) ($question->doc_title ?: '')) ?: basename((string) (parse_url($docHref, PHP_URL_PATH) ?: 'document')) }}</a>
                            </div>
                        </div>
                    @endif
                @elseif (($tid === 11 || $tid === 20) && trim((string) ($answer?->question_answer ?? '')) === '')
                    {{-- Image / Web Link Button: form-only chrome with no answer — omitted from the
                         submission response (matches the print page and PDF). --}}
                @else
                    @php
                        $raw = (string) ($answer?->question_answer ?? '');
                        $presented = \App\Support\FormSubmissionPresenter::presentOne($question, $raw);
                        $isBlock = in_array($presented['kind'], ['swms', 'sigrows', 'fields'], true);
                    @endphp
                    @if ($isBlock && $raw !== '')
                        {{-- Complex answers (SWMS / signatures / field groups) span the full width —
                             their inner label/value tables need the room. --}}
                        <div class="sl-fsview__section">
                            <div class="sl-fsview__sec-label">{{ $presented['label'] }}</div>
                            <div class="sl-fsview__sec-body">
                                {!! \App\Support\FormSubmissionPresenter::answerHtml($presented) !!}
                            </div>
                        </div>
                    @else
                        <div class="sl-fsview__row">
                            <div class="sl-fsview__q">{{ $presented['label'] }}</div>
                            <div class="sl-fsview__a">
                                @if ($raw === '')
                                    <span style="color:#9ca3af;">—</span>
                                @else
                                    {!! \App\Support\FormSubmissionPresenter::answerHtml($presented) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>

        <div class="sl-fsview__foot">
            <a class="sl-fsview__btn sl-fsview__btn--ghost" href="{{ $this->backUrl() }}">Back to Log</a>
            <a class="sl-fsview__btn" href="{{ route('portal.form-submissions.print', ['sessionId' => $sessionId, 'profile' => $selectedProfileId]) }}" target="_blank" rel="noopener">Download / Print</a>
        </div>
    </div>
</x-filament-panels::page>
