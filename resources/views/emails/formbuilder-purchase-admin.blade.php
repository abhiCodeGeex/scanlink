<x-emails.layout title="Scanlink Form Builder order summary">
    <p>Dear Administrator,</p>
    <p>User has ordered a form builder at scanlink.</p>
    <br/>
    <p>Here is user details</p>
    <table cellpadding="3" cellspacing="0">
        <tr>
            <th align="left">Order Number : </th>
            <td>{{ $orderId }}</td>
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
            <th align="left">Last name : </th>
            <td>{{ $lastName }}</td>
        </tr>
        <tr>
            <th align="left">No. of codes : </th>
            <td>{{ $noOfCodes }}</td>
        </tr>
        <tr>
            <th align="left">Amount per code : </th>
            <td>${{ $amount }} AUD</td>
        </tr>
        <tr>
            <th align="left">Total : </th>
            <td>${{ $amount }} AUD</td>
        </tr>
    </table>
    <br/>
    <p>Best Regards,</p>
</x-emails.layout>
