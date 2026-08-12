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
			<td style="height:30px;" height="30">Code Number</td>
			<td style="height:30px;" height="30" align="right" >{{ $profileId }}</td>
		</tr>
		<tr>
			<td style="height:30px;" height="30">{{ $qtySmall }} ScanLink Label/s 50x40mm</td>
			<td style="height:30px;" height="30" align="right" >${{ $amountSmall }} AUD </td>
		</tr>
		<tr>
			<td style="height:30px;" height="30">{{ $qtyLarge }} ScanLink Label/s 100x75mm</td>
			<td style="height:30px;" height="30" align="right" >${{ $amountLarge }} AUD </td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid #fff;height:30px;" height="30">Postage &amp; Handling</td>
			<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >${{ $postage }} AUD </td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td style="height:30px;" height="30"><b>Total</b></td>
			<td style="height:30px;" height="30" align="right"><b>${{ $total }} AUD </b></td>
		</tr>
	</tfoot>
</table>
<p>Your ScanLink label/s will be dispatched within two business days.</p>
<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
<br/>
<p>Sincerely,</p>
<p>Customer Support Team</p>
</x-emails.layout>
