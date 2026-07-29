<x-emails.layout title="Scanlink New User Register">
<p>Dear Administrator,</p>
<p>New user has been created account at scanlink.</p>
<br />
<p>Here is user details</p>
<table cellpadding="3" cellspacing="0" >
	<tr>
	  <th align="left">Email : </th>
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
	  <th align="left">Reseller name : </th>
	  <td>{{ $resellerName }}</td>
	</tr>
</table>
<br/>
<p>Best Regards,</p>
</x-emails.layout>
