<x-emails.layout title="Form submission">
    {{-- Legacy parity (mobile.php action_save_form_builder_data): HTML email with a
         profile header, each answer prefixed with its question label, ending with
         "END OF SUBMISSION". Signature answers embed as an image. --}}
    <div><b>Profile Number</b> : {{ $profile->id }}</div>
    <div><b>Profile Name</b> : {{ $profileName }}</div>
    <div><b>Submission Date/Time</b> : {{ $submittedAt }}</div>
    <br/>
    <hr/>

    @forelse ($rows as $row)
        @php
            $ans = trim((string) $row['answer']);
            $isSignature = str_starts_with($ans, 'data:image');
        @endphp
        <div style="margin-bottom:8px;">
            <b>{{ $row['label'] }}:</b>
            @if ($isSignature)
                @php [$sigSrc, $sigMeta] = array_pad(explode(' | ', $ans, 2), 2, ''); @endphp
                <br><img src="{{ $sigSrc }}" alt="Signature" style="max-width:320px;border:1px solid #ccc;">
                @if (filled($sigMeta))<br>{!! \App\Support\FormAnswerHtml::text($sigMeta) !!}@endif
            @else
                {!! \App\Support\FormAnswerHtml::text($ans) !!}
            @endif
        </div>
    @empty
        <div>No answers were submitted.</div>
    @endforelse

    <br/>
    <div><b>END OF SUBMISSION</b></div>
</x-emails.layout>
