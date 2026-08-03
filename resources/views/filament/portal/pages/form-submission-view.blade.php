<x-filament-panels::page>
    <style>
        .sl-fsview {
            font-family: Arial, Helvetica, sans-serif;
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 24px 32px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
        }
        .sl-fsview img.logo { max-height: 108px; width: auto; display: block; margin-bottom: 12px; }
        .sl-fsview hr { border: 0; border-top: 1px solid #ddd; margin: 12px 0; }
        .sl-fsview .label { display: inline-block; min-width: 140px; }
        .sl-fsview .meta { text-align: right; margin-bottom: 16px; color: #444; }
        .sl-fsview .results h1 { font-size: 22px; margin: 16px 0 8px; color: #008901; }
        .sl-fsview .results h3 { font-size: 16px; margin: 14px 0 6px; color: #333; }
        .sl-fsview .qa { margin: 10px 0; }
        .sl-fsview .qa strong { display: block; margin-bottom: 2px; }
        .sl-fsview .actions { margin-top: 24px; display: flex; gap: 10px; flex-wrap: wrap; }
        .sl-fsview .btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            text-decoration: none !important;
            border-radius: 5px;
            padding: 8px 14px;
        }
    </style>

    <div class="sl-fsview">
        @if ($logoUrl)
            <img class="logo" src="{{ $logoUrl }}" alt="Logo" height="108">
        @endif
        <hr>
        <p><span class="label"><b>Profile Number</b> :</span> {{ $selectedProfileId }}</p>
        <p><span class="label"><b>Profile Name</b> :</span> {{ $profileName }}</p>
        <hr>
        <div class="meta">Date/Time: {{ $submittedAt }}</div>

        <div class="results">
            @foreach ($questions as $question)
                @php
                    $tid = (int) $question->question_type_id;
                    $answer = $this->answerFor((int) $question->question_id);
                @endphp

                @if ($tid === 10)
                    <h1>{{ strip_tags((string) $question->question_text) }}</h1>
                @elseif ($tid === 12)
                    <h3>{{ strip_tags((string) $question->question_text) }}</h3>
                @elseif ($tid === 2 || $tid === 13 || $tid === 14)
                    <div class="qa">{!! $question->question_text !!}</div>
                @else
                    <div class="qa">
                        <strong>{{ strip_tags((string) $question->question_text) ?: ('Question #'.$question->question_id) }}</strong>
                        @php $raw = (string) ($answer?->question_answer ?? ''); @endphp
                        @if ($raw === '')
                            <div>—</div>
                        @elseif (\Illuminate\Support\Str::startsWith($raw, 'data:image'))
                            <div><img src="{{ $raw }}" alt="Signature" style="max-width:300px;border:1px solid #ccc;"></div>
                        @elseif ($tid === 25)
                            @php $p = explode(':::', $raw); @endphp
                            <div>
                                Visitor name: {{ $p[0] ?? '' }}<br>
                                Phone: {{ $p[1] ?? '' }}<br>
                                Date/Time: {{ trim(($p[2] ?? '').' '.($p[3] ?? '')) }}<br>
                                Venue: {{ trim(($p[4] ?? '').', '.($p[5] ?? ''), ', ') }}<br>
                                Location type: {{ $p[6] ?? '' }}@if (($p[6] ?? '') === 'Vehicle' && isset($p[7])) — Vehicle: {{ $p[7] }}@endif
                            </div>
                        @elseif ($tid === 7)
                            <div>{!! nl2br(e(str_replace('; ', "\n", $raw))) !!}</div>
                        @else
                            <div>{!! \App\Support\FormAnswerHtml::text($raw) !!}</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="actions">
            <a class="btn" href="{{ $this->backUrl() }}">Back to Log</a>
            <a class="btn" href="{{ route('portal.form-submissions.print', ['sessionId' => $sessionId, 'profile' => $selectedProfileId]) }}" target="_blank" rel="noopener">Download / Print</a>
        </div>
    </div>
</x-filament-panels::page>
