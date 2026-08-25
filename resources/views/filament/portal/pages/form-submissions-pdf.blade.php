{{--
    Download / Download All submissions PDF (TCPDF fragment).

    TCPDF's HTML engine is primitive: pt font sizes, spacing via table cellpadding,
    widths as attributes, colors inline. Every construct here is one TCPDF renders
    cleanly. Design language: no heavy grids — muted label column, hairline row
    separators, compact single-line header. All submissions flow in ONE document.
--}}
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="62%" style="vertical-align:middle;">@if (! empty($logoUrl))<img src="{{ $logoUrl }}" height="42">@endif</td>
        <td width="38%" align="right" style="vertical-align:middle;"><span style="font-size:15pt;color:#008901;"><b>Form Submission{{ count($sessions) === 1 ? '' : 's' }}</b></span></td>
    </tr>
</table>
<table cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td style="font-size:9.5pt;color:#6b7280;">
            <span style="color:#111827;font-size:10.5pt;"><b>Profile {{ $profile->id }}</b></span> &nbsp;&middot;&nbsp; {{ $profileName }}<br>
            @if (count($sessions) === 1)
                Submitted {{ $sessions[0]['submittedAt'] }}
            @else
                {{ count($sessions) }} submissions &nbsp;&middot;&nbsp; Generated {{ $generatedAt }}
            @endif
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr><td style="background-color:#e5e7eb;font-size:0.6pt;">&nbsp;</td></tr>
</table>
<br>

@foreach ($sessions as $i => $s)
    @if ($i > 0)
        <br>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr><td style="background-color:#e5e7eb;font-size:0.6pt;">&nbsp;</td></tr>
        </table>
        <br>
    @endif

    @if (count($sessions) > 1)
        <table cellpadding="4" cellspacing="0" width="100%">
            <tr>
                <td style="font-size:9pt;"><span style="color:#008901;"><b>SUBMISSION {{ $i + 1 }}</b></span>
                    <span style="color:#9ca3af;">&nbsp;&middot;&nbsp; {{ $s['submittedAt'] }}</span></td>
            </tr>
        </table>
    @endif

    <table cellpadding="6" cellspacing="0" width="100%" style="font-size:10pt;">
        @foreach ($s['rows'] as $ri => $row)
            <tr>
                <td width="30%" style="color:#6b7280;font-size:9.5pt;"><b>{{ $row['label'] }}</b></td>
                <td width="70%" style="color:#111827;">{!! $row['html'] !!}</td>
            </tr>
            @if ($ri < count($s['rows']) - 1)
                <tr><td colspan="2" style="background-color:#f3f4f6;font-size:0.5pt;">&nbsp;</td></tr>
            @endif
        @endforeach
    </table>
@endforeach
