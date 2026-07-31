<x-emails.layout :title="$title ?? 'ScanLink'">
<p>Dear Administrator,</p>
<p>User has {{ $verb }} code at scanlink.</p>
<br />
<p>Here is user details</p>
<table cellpadding="3" cellspacing="0" >
	<tr>
	  <th align="left" >Email : </th>
	  <td>{{ $email }}</td>
	</tr>
	<tr>
	  <th align="left">First name : </th>
	  <td>{{ $firstName }}</td>
	</tr>
	<tr>
	  <th align="left">Last name : </th>
	  <td>{{ $lastName }}</td>
	</tr>
	<tr>
	  <th align="left" >No. of codes : </th>
	  <td>{{ $noOfCodes }}</td>
	</tr>
	<tr>
	  <th align="left" valign="top">Amount per code : </th>
	  <td>{!! $amountPerCode !!}</td>
	</tr>
	<tr>
	  <th align="left">Total : </th>
	  <td>${{ $total }} AUD</td>
	</tr>
	<tr>
	  <th align="left" >Reseller Name : </th>
	  <td>{{ $resellerName }}</td>
	</tr>
</table>
<br/>
<p>Best Regards,</p>
</x-emails.layout>
