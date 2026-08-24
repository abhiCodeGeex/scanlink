<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form submission — {{ $profile->name }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 2rem; color: #222; }
        h1 { color: #008C00; margin: 0 0 .25rem; font-size: 1.35rem; }
        .meta { color: #666; font-size: .875rem; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { border: 1px solid #ddd; padding: .6rem .75rem; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; width: 35%; font-weight: 600; }
        .print-bar { margin-bottom: 1.5rem; }
        .print-btn { background: #008C00; color: #fff; border: 0; border-radius: 8px; padding: .55rem 1rem; font-weight: 600; cursor: pointer; }
        @media print {
            .print-bar { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button type="button" class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    @if (! empty($logoUrl))
        <img src="{{ $logoUrl }}" alt="Logo" style="max-height:90px;max-width:260px;width:auto;height:auto;display:block;margin:0 0 1rem;">
    @endif

    <h1>{{ $profile->form_title ?: $profile->name }}</h1>
    <p class="meta">
        Profile number: {{ $profile->id }}<br>
        Profile: {{ $profile->code_profile_name ?: ($profile->name ?: $profile->form_title) }}<br>
        Session: {{ $sessionId }}<br>
        Submitted: {{ $submittedAt ? \Illuminate\Support\Carbon::parse($submittedAt)->format('d M Y H:i') : '—' }}
    </p>

    <table>
        <tbody>
            @foreach ($answers as $answer)
                @php
                    $q = $answer->question;
                    $raw = (string) ($answer->question_answer ?? '');
                    $presented = \App\Support\FormSubmissionPresenter::presentOne($q, $raw);
                @endphp
                <tr>
                    <th>{{ $presented['label'] }}</th>
                    <td>
                        @if ($raw === '')
                            —
                        @else
                            {!! \App\Support\FormSubmissionPresenter::answerHtml($presented) !!}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
