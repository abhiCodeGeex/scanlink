{{--
    Download / Download All submissions PDF (TCPDF fragment).

    TCPDF's HTML engine is primitive: pt font sizes, spacing via table cellpadding,
    widths as attributes, colors inline — and it CANNOT nest tables. Rows are
    therefore split into segments: consecutive simple answers share one 28/72
    label/value table; complex answers (SWMS, repeatable signatures, field groups)
    break out as full-width sections whose own tables sit at the TOP level.
    All submissions flow in ONE document.
--}}
@php
    $blockKinds = ['swms', 'sigrows', 'fields', 'display'];

    $segment = static function (array $rows) use ($blockKinds): array {
        $segments = [];
        $buffer = [];
        foreach ($rows as $row) {
            if (in_array($row['kind'] ?? 'text', $blockKinds, true)) {
                if ($buffer !== []) {
                    $segments[] = ['type' => 'table', 'rows' => $buffer];
                    $buffer = [];
                }
                $segments[] = ['type' => 'block', 'row' => $row];
            } else {
                $buffer[] = $row;
            }
        }
        if ($buffer !== []) {
            $segments[] = ['type' => 'table', 'rows' => $buffer];
        }

        return $segments;
    };
@endphp
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="62%" style="vertical-align:middle;">@if (! empty($logoUrl))<img src="{{ $logoUrl }}" height="42">@endif</td>
        <td width="38%" align="right" style="vertical-align:middle;"><span style="font-size:15pt;color:#008901;"><b>Form Submission{{ count($sessions) === 1 ? '' : 's' }}</b></span></td>
    </tr>
</table>
<table cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td style="font-size:9.5pt;color:#6b7280;">
            <span style="color:#111827;font-size:10.5pt;"><b>Profile {{ $profile->id }}</b></span> &nbsp;-&nbsp; {{ $profileName }}<br>
            @if (count($sessions) === 1)
                Submitted {{ $sessions[0]['submittedAt'] }}
            @else
                {{ count($sessions) }} submissions &nbsp;-&nbsp; Generated {{ $generatedAt }}
            @endif
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr><td style="background-color:#e5e7eb;font-size:0.6pt;">&nbsp;</td></tr>
</table>
<br>

@foreach ($sessions as $i => $s)
    {{-- Multi-submission report: every submission opens with an unmissable solid green
         banner (number + date) after clear whitespace, so submissions never blur together. --}}
    @if (count($sessions) > 1)
        @if ($i > 0)
            <br><br>
        @endif
        <table cellpadding="9" cellspacing="0" width="100%">
            <tr nobr="true">
                <td width="70%" style="background-color:#008901;color:#ffffff;font-size:11.5pt;"><b>SUBMISSION {{ $i + 1 }} OF {{ count($sessions) }}</b></td>
                <td width="30%" align="right" style="background-color:#008901;color:#d8f5d8;font-size:9pt;">Submitted {{ $s['submittedAt'] }}</td>
            </tr>
        </table>
        <br>
    @endif

    @foreach ($segment($s['rows']) as $si => $seg)
        @if ($si > 0)
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td style="background-color:#f3f4f6;font-size:0.5pt;">&nbsp;</td></tr>
            </table>
        @endif

        @if ($seg['type'] === 'table')
            <table cellpadding="6" cellspacing="0" width="100%" style="font-size:10pt;">
                @foreach ($seg['rows'] as $row)
                    <tr nobr="true">
                        <td width="26%" style="color:#6b7280;font-size:9.5pt;"><b>{{ $row['label'] }}</b></td>
                        <td width="74%" style="color:#111827;">{!! $row['html'] !!}</td>
                    </tr>
                @endforeach
            </table>
        @else
            {{-- Full-width section: label heading (when present), then the block's own top-level tables. --}}
            @if (($seg['row']['label'] ?? '') !== '')
                <table cellpadding="6" cellspacing="0" width="100%">
                    <tr><td style="color:#6b7280;font-size:9.5pt;"><b>{{ strtoupper($seg['row']['label']) }}</b></td></tr>
                </table>
            @endif
            @if (($seg['row']['kind'] ?? '') === 'display')
                <table cellpadding="6" cellspacing="0" width="100%">
                    <tr nobr="true"><td>{!! $seg['row']['html'] !!}</td></tr>
                </table>
            @else
                {!! $seg['row']['html'] !!}
            @endif
        @endif
    @endforeach
@endforeach
