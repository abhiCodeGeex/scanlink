<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form submission — {{ filled(trim((string) $profile->code_profile_name)) ? $profile->code_profile_name : $profile->displayLabel() }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 28px 16px;
            background: #f3f4f6;
            color: #111827;
            font-size: 14px;
        }
        .sheet {
            max-width: 660px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px 24px;
            padding: 22px 30px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #f7faf7 0%, #ffffff 100%);
        }
        .head img.logo { max-height: 64px; max-width: 240px; width: auto; height: auto; display: block; }
        .meta { text-align: right; }
        .meta .title { font-size: 17px; font-weight: 700; color: #008901; margin: 0 0 4px; }
        .meta .line { font-size: 13px; color: #6b7280; margin: 1px 0; }
        .meta .line b { color: #111827; }
        .body { padding: 8px 30px 14px; }
        .row { display: flex; gap: 20px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .row:last-child { border-bottom: 0; }
        .q {
            flex: 0 0 26%;
            max-width: 26%;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #6b7280;
            padding-top: 2px;
            word-break: break-word;
        }
        .a { flex: 1; min-width: 0; line-height: 1.5; overflow-wrap: break-word; }
        .a a { color: #008901; font-weight: 600; }
        /* Natural-size thumbnails, never upscaled — the image links open the full file. */
        .a img { width: auto !important; max-width: min(260px, 100%) !important; height: auto !important; border-radius: 6px; }
        .a img[alt="Signature"] { max-width: min(340px, 100%) !important; }
        .a table { width: 100% !important; table-layout: fixed; }
        .section { padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .section:last-child { border-bottom: 0; }
        .sec-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #6b7280;
            margin: 0 0 10px;
        }
        .sec-body { line-height: 1.5; overflow-wrap: break-word; }
        .sec-body a { color: #008901; font-weight: 600; }
        .sec-body img { width: auto !important; max-width: min(260px, 100%) !important; height: auto !important; border-radius: 6px; }
        .sec-body img[alt="Signature"] { max-width: min(340px, 100%) !important; }
        .sec-body table { width: 100% !important; table-layout: fixed; }
        .print-bar { max-width: 660px; margin: 0 auto 14px; text-align: right; }
        .print-btn {
            background: #008901;
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }
        .print-btn:hover { background: #006b01; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-bar { display: none; }
            .sheet { border: 0; border-radius: 0; max-width: none; }
        }
        @media (max-width: 560px) {
            .head, .body { padding-left: 18px; padding-right: 18px; }
            .row { flex-direction: column; gap: 4px; }
            .q { flex: none; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button type="button" class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="sheet">
        <div class="head">
            <div>
                @if (! empty($logoUrl))
                    <img class="logo" src="{{ $logoUrl }}" alt="Logo">
                @endif
            </div>
            <div class="meta">
                <div class="title">Form Submission</div>
                <div class="line"><b>Profile {{ $profile->id }}</b> · {{ filled(trim((string) $profile->code_profile_name)) ? $profile->code_profile_name : $profile->displayLabel() }}</div>
                <div class="line">Submitted {{ $submittedAt ? \Illuminate\Support\Carbon::parse($submittedAt)->format('d M Y H:i') : '—' }}</div>
            </div>
        </div>

        <div class="body">
            @foreach ($rows as $row)
                @if (($row['kind'] ?? '') === 'display')
                    {{-- Form context (Text / Heading blocks the visitor saw). --}}
                    <div class="section">{!! $row['html'] !!}</div>
                @elseif (in_array($row['kind'] ?? 'text', ['swms', 'sigrows', 'fields'], true))
                    {{-- Complex answers span the full width — their inner tables need the room. --}}
                    <div class="section">
                        <div class="sec-label">{{ $row['label'] }}</div>
                        <div class="sec-body">{!! $row['html'] !!}</div>
                    </div>
                @else
                    <div class="row">
                        <div class="q">{{ $row['label'] }}</div>
                        <div class="a">{!! $row['html'] !== '' ? $row['html'] : '<span style="color:#9ca3af;">—</span>' !!}</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</body>
</html>
