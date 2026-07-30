<x-emails.layout title="Form submission">
    {{-- Legacy parity (mobile.php action_save_form_builder_data): HTML email with a
         profile header followed by each answer. --}}
    <div><b>Profile Number</b> : {{ $profile->id }}</div>
    <div><b>Profile Name</b> : {{ $profileName }}</div>
    <div><b>Submission Date/Time</b> : {{ $submittedAt }}</div>
    <br/>
    <hr/>

    @forelse ($rows as $row)
        <div><b>{{ $row['label'] }}:</b> {!! nl2br(e($row['answer'])) !!}</div>
    @empty
        <div>No answers were submitted.</div>
    @endforelse
</x-emails.layout>
