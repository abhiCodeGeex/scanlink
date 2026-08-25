<x-emails.layout title="Form submission" font="Arial, Helvetica, sans-serif" :logo="$profileLogo ?? null">
@php
    $profileName = $profileName ?: ('Profile #'.$profile->id);
    $rows = $rows ?? [];
@endphp

{{-- Modern, email-client-safe layout (tables + inline styles). --}}
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px;border-collapse:collapse;">
    <tr>
        <td style="padding:0 0 20px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;background:#008901;border-radius:8px;">
                <tr>
                    <td style="padding:18px 22px;">
                        <div style="font-size:11px;letter-spacing:0.08em;text-transform:uppercase;color:#c8f0c8;font-weight:bold;margin:0 0 4px;">ScanLink</div>
                        <div style="font-size:22px;line-height:1.25;color:#ffffff;font-weight:bold;margin:0;">Form submission received</div>
                        <div style="font-size:13px;color:#e8f8e8;margin:6px 0 0;">A new response was submitted for your code profile.</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 18px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;">
                <tr>
                    <td style="padding:14px 18px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:4px 0;width:42%;color:#6b7280;font-size:13px;vertical-align:top;">Profile number</td>
                                <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:bold;vertical-align:top;">{{ $profile->id }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;width:42%;color:#6b7280;font-size:13px;vertical-align:top;">Profile name</td>
                                <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:bold;vertical-align:top;">{{ $profileName }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;width:42%;color:#6b7280;font-size:13px;vertical-align:top;">Submitted</td>
                                <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:bold;vertical-align:top;">{{ $submittedAt }}</td>
                            </tr>
                            @if (! empty($sessionId))
                                <tr>
                                    <td style="padding:4px 0;width:42%;color:#6b7280;font-size:13px;vertical-align:top;">Reference</td>
                                    <td style="padding:4px 0;color:#374151;font-size:12px;font-family:Consolas,Monaco,monospace;vertical-align:top;word-break:break-all;">{{ $sessionId }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 8px;">
            <div style="font-size:15px;font-weight:bold;color:#111827;margin:0;">Responses</div>
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 8px;">
            @forelse ($rows as $index => $row)
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:8px;margin:0 0 12px;background:#ffffff;">
                    <tr>
                        <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background:#fafafa;border-radius:8px 8px 0 0;">
                            <span style="display:inline-block;min-width:22px;height:22px;line-height:22px;text-align:center;border-radius:11px;background:#008901;color:#fff;font-size:11px;font-weight:bold;margin-right:8px;">{{ $index + 1 }}</span>
                            <span style="font-size:14px;font-weight:bold;color:#065f06;vertical-align:middle;">{{ $row['label'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 16px;font-size:14px;line-height:1.45;color:#111827;">
                            {!! \App\Support\FormSubmissionPresenter::answerHtml($row) !!}
                        </td>
                    </tr>
                </table>
            @empty
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;border:1px dashed #d1d5db;border-radius:8px;background:#f9fafb;">
                    <tr>
                        <td style="padding:18px;color:#6b7280;font-size:14px;text-align:center;">No answers were submitted.</td>
                    </tr>
                </table>
            @endforelse
        </td>
    </tr>

    <tr>
        <td style="padding:8px 0 0;">
            <div style="font-size:12px;color:#9ca3af;text-align:center;letter-spacing:0.04em;">— End of submission —</div>
        </td>
    </tr>
</table>
</x-emails.layout>
