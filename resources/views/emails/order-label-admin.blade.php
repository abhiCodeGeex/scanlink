<x-emails.layout title="Scanlink Order Label">
<p>Dear Administrator,</p>
<p>User has purchased Order Label at scanlink.</p>
<br />
<p>Here is user details</p>
<table cellpadding="3" cellspacing="0" >
	<tr>
	  <th align="left">Code Number : </th>
	  <td>{{ $profileId }}</td>
	</tr>
	<tr>
	  <th align="left">Email : </th>
	  <td>{{ $email }}</td>
	</tr>
	<tr>
	  <th align="left">First name : </th>
	  <td>{{ $firstName }}</td>
	</tr>
	<tr>
	  <th align="left" >Last name : </th>
	  <td>{{ $lastName }}</td>
	</tr>
	<tr>
	  <th align="left">ScanLink Label/s 50x40mm: </th>
	  <td>{{ $qtySmall }}</td>
	</tr>
	<tr>
	  <th align="left">Amount ScanLink Label/s 50x40mm : </th>
	  <td>${{ $amountSmall }} AUD</td>
	</tr>
	<tr>
	  <th align="left">ScanLink Label/s 100x75mm: </th>
	  <td>{{ $qtyLarge }}</td>
	</tr>
	<tr>
	  <th align="left" >Amount ScanLink Label/s 100x75mm : </th>
	  <td>${{ $amountLarge }} AUD</td>
	</tr>
	<tr>
	  <th align="left" >Postage &amp; Handling : </th>
	  <td>${{ $postage }} AUD</td>
	</tr>
	<tr>
	  <th align="left" >Total : </th>
	  <td>${{ $total }} AUD</td>
	</tr>
</table>
<br/>
<p>Best Regards,</p>
</x-emails.layout>
