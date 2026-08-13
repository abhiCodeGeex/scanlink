{{--
    Legacy parity: minion/task/documentexpiry.php — VOCC Renewal Reminder (30 days before a
    voc document's expiry_date) and VOCC Expiry Notification (on the expiry date). Sent to the
    profile's Email Notification recipients (voc_recipients), using the profile's configured
    voc_email_url / voc_email_text / voc_email_sign_line1.

    Vars: $expired (bool), $vocUsername, $docNames (raw "Name<br>Name<br>"), $expiryDate,
          $emailUrl, $emailText, $signature (raw html or ''), $logoUrl (absolute or null), $base
--}}
@php($base = $base ?? rtrim((string) config('scanlink.portal_url'), '/').'/')
<html>
<head>
<title>{{ $expired ? 'VOCC Expiry Notification' : 'VOCC Renewal Reminder' }}</title>
</head>
<body style="font-family:arial;">
@if (! empty($logoUrl))
    <p><a href="{{ $base }}"><img src="{{ $logoUrl }}" width="150" alt="logo" /></a></p>
@else
    <p><a href="{{ $base }}"><img src="{{ $base }}images/email-logo.png" alt="logo" /></a></p>
@endif
<hr/>
<p>Dear {{ $vocUsername }},</p>
@if ($expired)
    <p>This is a courtesy reminder to notify you that the following document has expired.</p>
@else
    <p>This is a courtesy reminder to notify you that the following document is due for renewal by the {{ $expiryDate }}.</p>
@endif
{!! $docNames !!}
@unless ($expired)
    <p>You now have 30 days to update the document.</p>
@endunless
<p><a href="{{ $emailUrl }}">{{ $emailText }}</a></p>
<p>Please contact your VOCC administrator listed below for assistance or to update your profile.</p>
<p>Regards,</p>
@if (filled($signature))
    <p>{!! $signature !!}</p>
@else
    <p>ScanLink Support Team</p>
@endif
</body>
</html>
