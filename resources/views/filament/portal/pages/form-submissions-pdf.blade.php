{{--
    Download / Download All submissions PDF (TCPDF fragment).

    Rendered by TCPDF's HTML engine, which is deliberately primitive: pt font sizes only
    (no rem/em), spacing via table cellpadding (CSS padding/margin are ignored), widths as
    attributes, colors via inline background-color/color. Every construct here is one TCPDF
    is known to render cleanly. All submissions flow in ONE document, divided by <hr> —
    not one page per submission.
--}}
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="62%" style="vertical-align:middle;">@if (! empty($logoUrl))<img src="{{ $logoUrl }}" height="46">@endif</td>
        <td width="38%" align="right" style="vertical-align:middle;"><span style="font-size:17pt;color:#008901;"><b>Form Submissions</b></span></td>
    </tr>
</table>
<table cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td style="font-size:9.5pt;color:#4b5563;">
            <span style="color:#111827;"><b>Profile {{ $profile->id }}</b></span> &nbsp;&middot;&nbsp; {{ $profileName }}<br>
            @if (count($sessions) === 1)
                Submitted {{ $sessions[0]['submittedAt'] }} &nbsp;&middot;&nbsp; Ref: {{ $sessions[0]['sessionId'] }}
            @else
                {{ count($sessions) }} submissions &nbsp;&middot;&nbsp; Generated {{ $generatedAt }}
            @endif
        </td>
    </tr>
</table>
{{-- Thin brand rule under the header (a colored 1pt cell — TCPDF renders this reliably). --}}
<table cellpadding="1" cellspacing="0" width="100%">
    <tr><td style="background-color:#008901;font-size:1pt;">&nbsp;</td></tr>
</table>
<br>

@foreach ($sessions as $i => $s)
    @if ($i > 0)
        <br><hr><br>
    @endif

    {{-- Submission title bar — only when the PDF holds multiple submissions; a single
         submission's date/ref already live in the header, so no duplicate banner. --}}
    @if (count($sessions) > 1)
        <table cellpadding="6" cellspacing="0" width="100%">
            <tr>
                <td width="58%" style="background-color:#008901;color:#ffffff;font-size:11pt;"><b>Submission {{ $i + 1 }}</b></td>
                <td width="42%" align="right" style="background-color:#008901;color:#ffffff;font-size:9.5pt;">{{ $s['submittedAt'] }}</td>
            </tr>
        </table>
        <table cellpadding="3" cellspacing="0" width="100%">
            <tr><td style="font-size:8pt;color:#6b7280;">Reference: {{ $s['sessionId'] }}</td></tr>
        </table>
    @endif

    {{-- Question / answer grid --}}
    <table cellpadding="6" cellspacing="0" border="1" width="100%" style="font-size:10pt;">
        @foreach ($s['rows'] as $row)
            <tr>
                <td width="34%" style="background-color:#f3f4f6;color:#374151;"><b>{{ $row['label'] }}</b></td>
                <td width="66%" style="color:#111827;">{!! $row['html'] !!}</td>
            </tr>
        @endforeach
    </table>
@endforeach
