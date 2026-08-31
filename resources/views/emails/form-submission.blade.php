<x-emails.layout title="Form submission" font="Arial, Helvetica, sans-serif" :logo="$profileLogo ?? null">
@php
    $profileName = $profileName ?: ('Profile #'.$profile->id);
    $rows = $rows ?? [];
    $blockKinds = ['swms', 'sigrows', 'fields'];
@endphp

{{-- Email-client-safe (tables + inline styles). Same design language as the submission
     view / print / PDF: compact green header, one clean label/value table, full-width
     sections for complex answers. --}}
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px;border-collapse:collapse;">
    <tr>
        <td style="padding:0 0 14px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background:#008901;border-radius:8px;">
                <tr>
                    <td style="padding:13px 18px;">
                        <span style="font-size:17px;color:#ffffff;font-weight:bold;">Form Submission</span>
                    </td>
                    <td align="right" style="padding:13px 18px;font-size:12px;color:#d8f5d8;">
                        {{ $submittedAt }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:0 2px 14px;font-size:13px;color:#6b7280;">
            <span style="color:#111827;font-weight:bold;">Profile {{ $profile->id }}</span>
            &nbsp;&middot;&nbsp; {{ $profileName }}
        </td>
    </tr>

    <tr>
        <td>
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:8px;background:#ffffff;">
                @forelse ($rows as $row)
                    @if (($row['kind'] ?? '') === 'display')
                        {{-- Form context (Text / Heading blocks the visitor saw). --}}
                        <tr>
                            <td colspan="2" style="padding:11px 16px;border-bottom:1px solid #f3f4f6;font-size:13.5px;line-height:1.5;color:#111827;">
                                {!! \App\Support\FormSubmissionPresenter::answerHtml($row) !!}
                            </td>
                        </tr>
                    @elseif (in_array($row['kind'] ?? 'text', $blockKinds, true))
                        {{-- Complex answers (SWMS / signatures / field groups) span the full width. --}}
                        <tr>
                            <td colspan="2" style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                <div style="font-size:12.5px;font-weight:bold;letter-spacing:0.02em;text-transform:uppercase;color:#6b7280;margin:0 0 8px;">{{ $row['label'] }}</div>
                                <div style="font-size:13.5px;line-height:1.5;color:#111827;">
                                    {!! \App\Support\FormSubmissionPresenter::answerHtml($row) !!}
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="30%" style="padding:10px 12px 10px 16px;border-bottom:1px solid #f3f4f6;font-size:12.5px;font-weight:bold;letter-spacing:0.02em;text-transform:uppercase;color:#6b7280;vertical-align:top;">
                                {{ $row['label'] }}
                            </td>
                            <td style="padding:10px 16px 10px 0;border-bottom:1px solid #f3f4f6;font-size:13.5px;line-height:1.5;color:#111827;vertical-align:top;">
                                {!! \App\Support\FormSubmissionPresenter::answerHtml($row) !!}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td style="padding:18px;color:#6b7280;font-size:13.5px;text-align:center;">No answers were submitted.</td>
                    </tr>
                @endforelse
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:12px 0 0;">
            <div style="font-size:11px;color:#9ca3af;text-align:center;">End of submission &middot; Ref {{ $sessionId }}</div>
        </td>
    </tr>
</table>
</x-emails.layout>
