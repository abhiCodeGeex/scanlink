@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<x-emails.layout title="ScanLink code expiry notification" font="arial" division="North West IT services">
<p>Hi {{ $firstName }}  {{ $lastName }},</p>
@if ($expired)
<p>The following ScanLink code/s have expired.</p>
<p>{!! implode('<br>', array_map('intval', $profileIds)) !!}</p>
<p>You can renew the code/s now by logging in to your <a href="{{ $base }}">ScanLink</a> account and selecting the code renewal function. To login to your account now <a href="{{ $base }}">click here</a></p>

<p>Please note that all functions associated with each expired code will remain inactive until the subscription has been renewed.</p>
@else
<p>The following ScanLink code/s will expire on {{ $expiryDate }}.</p>
<p>{!! implode('<br>', array_map('intval', $profileIds)) !!}</p>
<p>You now have {{ $days }} days to renew before the code/s expire. If the renewal period expires a code will become inactive.</p>
<p>You can quickly and easily renew the code/s now by logging in to your <a href="{{ $base }}">ScanLink</a> account and select the renew option. <a href="{{ $base }}">Click here to log in to your account</a></p>
@endif

<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
<br/>
<p>Sincerely,</p>
<p>Customer Support Team</p>
</x-emails.layout>
