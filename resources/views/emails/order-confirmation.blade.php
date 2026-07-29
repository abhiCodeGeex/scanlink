@php($base = rtrim(config('scanlink.portal_url'), '/').'/')
<x-emails.layout title="ScanLink order confirmation">
<p>Hi {{ $firstName }}  {{ $lastName }}</p>
<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
	<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
		<tr>
			<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border-bottom:1px solid #fff;height:30px;" height="30">{{ $noOfCodes }} ScanLink Code/s 12 month activation</td>
			<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >${{ $total }} AUD </td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td style="height:30px;" height="30"><b>Total</b></td>
			<td style="height:30px;" height="30" align="right"><b>${{ $total }} AUD </b></td>
		</tr>
	</tfoot>
</table>
@if (! empty($isReseller))
<p>Your ScanLink code/s for user account ({{ $userEmail }}) are now activated and ready to use.<a href="{{ $base }}">Click here to login to your account.</a></p>
@else
<p>Your ScanLink code/s are now activated and ready to use.<a href="{{ $base }}">Click here to login to your account.</a></p>
@endif
<p>If you have any questions or require assistance you can contact us at anytime at {{ $supportEmail }} or call us during business hours on Monday to Friday on {{ $supportPhone }}.</p>
<br/>
<p>Sincerely,</p>
<p>Customer Support Team</p>
</x-emails.layout>
