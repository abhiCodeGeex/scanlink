@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<x-emails.layout title="ScanLink code expiry notification" font="arial" division="North West IT services">
<p>Hi {{ $firstName }}  {{ $lastName }},</p>
@if ($expired)
<p>The following ScanLink code profile has expired.</p>

<table width="50%">
<tr><td width="25%">Profile number:</td><td> {{ $profileId }}</td></tr>
<tr><td width="25%">Profile name:</td><td> {{ $name }}</td></tr>
</table>

<p>You can renew this code now by logging in to your <a href="{{ $base }}">ScanLink</a> account and selecting the code renewal function. To login to your account now <a href="{{ $base }}">click here</a></p>

<p>Please note that all functions associated with this code will remain inactive until it has been renewed.</p>
@else
<p>Your ScanLink code profile No. {{ $profileId }} will expire on {{ $expiryDate }}.</p>
<p>You now have {{ $days }} days to renew this code before it expires. If you do not renew the code it will become inactive.</p>
<p>You can quickly and easily renew this code now by logging in to your <a href="{{ $base }}">ScanLink</a> account and select the renew option for this code. <a href="{{ $base }}">Click here to log in to your account</a></p>
@endif

<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
<br/>
<p>Sincerely,</p>
<p>Customer Support Team</p>
</x-emails.layout>
