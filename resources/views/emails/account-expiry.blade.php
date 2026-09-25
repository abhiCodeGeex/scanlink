@php
    $base = rtrim(config('scanlink.portal_url'), '/').'/';
@endphp
<x-emails.layout title="ScanLink re-new account notification." font="arial" division="North West IT services">
<p>Hi {{ trim($firstName.' '.$lastName) ?: 'there' }}!</p>
<p>As usual, ScanLink is always happy to help!</p>
@if ($finalNotice)
<p>This is a final reminder from ScanLink that your account is going to expire on {{ $expiryDate }}, so do not delay and contact admin to re-new your account.</p>
@else
<p>This is a notification from ScanLink that your account is going to expire on {{ $expiryDate }}, so do not delay and contact admin to re-new your account.</p>
@endif
<p>Once it expires you will not be able to log in to your <a href="{{ $base }}">ScanLink</a> account.</p>
<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a> or call us during business hours on Monday to Friday on {{ $supportPhone }}.</p>
<p>Best regards,</p>
<p>Customer Support Team</p>
</x-emails.layout>
