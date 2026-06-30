<?php

require_once("Mail.php");

function forgot_mail_client($to, $txtnewpass, $fromemail, $baseurl)
{
	$fromemail = "info@scanlink.com.au";
	$subject = 'Response from ScanLink Solutions for forgot password request';
	$message = '<html>
				<head>
				<title>Response from ScanLink Solutions for forgot password request</title>
				</head>
				<body style="font-family:calibri;">
				<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
				<hr/>
				<p>Hi there!</p>
				<p>As usual, ScanLink is always happy to help!</p>
				<p>Thank you for your enquiry. Here is the information you requested.</p>
				<table cellpadding="3" cellspacing="0" >
						<tr>
						  <th align="right">New Password : </th>
						  <td>' . $txtnewpass . '</td>
						</tr>					
				</table>
				<br />
				<p>Sincerely,</p>
				<p>Customer Support Team</p>
				<p><a href="' . $baseurl . '">ScanLink</a></p>
				<p style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
				</body>
				</html>';
	// Mail it
	$return = sendHtmlMail($to, $subject, $message, $fromemail);
	return $return;
}

/* * **
 * **** send mail to client after registration 
 * **** @param email string
 * **** @param first_name string
 * **** @param last_name string
 * **** @param password string
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function registration_mail_client($contactemail, $baseurl, $email, $first_name, $last_name, $password)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $email = "webplanex.test@gmail.com"; */
	$to = $email;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Welcome to ScanLink';
	// message
	/*$message = '<html>
		<head>
		<title>Welcome to ScanLink</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $first_name . ' ' . $last_name . '</p>
		<p>Thank you for registering with ScanLink.</p>
		<p>Now you can use the power of mobile visual communication to enhance Health, Safety, Sustainability and Quality in your workplace. You can also use ScanLink to promote products and services.</p>
		<p>If you have any questions or require assistance you can contact us at anytime at admin@scanlink.net.au or call us during business hours on Monday to Friday on 0417557640.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';*/

	$message = '<html>
		<head>
		<title>Welcome to ScanLink</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $first_name . ' ' . $last_name . '</p>
		<p>Thank you for registering with ScanLink.</p>
		<p>To complete your account registration please <a href="' . $baseurl . '?open_login_box=1">click here</a></p>
		<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:admin@scanlink.net.au">admin@scanlink.net.au</a> or call us during business hours on Monday to Friday on 0417557640.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';


	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after registration 
 * **** @param email string
 * **** @param first_name string
 * **** @param last_name string
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function registration_mail_admin($contactemail, $baseurl, $email, $first_name, $last_name, $client_reseller_name)
{

	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink New User Register';
	// message
	$message = '<html>
		<head>
		<title>Scanlink New User Register</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>New user has been created account at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left">Email : </th>
			  <td>' . $email . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $first_name . '</td>
			</tr>				
			<tr>
			  <th align="left">Last name : </th>
			  <td>' . $last_name . '</td>
			</tr>
			<tr>
			  <th align="left">Reseller name : </th>
			  <td>' . $client_reseller_name . '</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it

	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to client after renew code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function renewCode_mail_client($contactemail, $baseurl, $detail)
{


	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">' . $detail['no_of_codes'] . ' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['total_amount'], 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['total_amount'], 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s are now activated and ready to use.<a href="' . $baseurl . '">Click here to login to your account.</a></p>
		<p>If you have any questions or require assistance you can contact us at anytime at admin@scanlink.net.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";


	// Mail it
	$send = sendHtmlMail($to, $subject, $message, $fromemail);

	/* echo "HELLO ";
    if($send){
    echo "TRUE";
    }else{
    echo "FALSE";
    }
    exit;
   */
}

/* * **
 * **** send mail to admin after renew code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function renewCode_mail_admin($contactemail, $baseurl, $detail, $client_reseller_name)
{

	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Renew Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Renew Code</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>User has renewed code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left">Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left" >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left">No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left" valign="top">Amount per code : </th>
			  <td>' . $detail['amount_per_code'] . ' </td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['total_amount'], 2) . ' AUD </td>
			</tr>
			<tr>
			  <th align="left">Reseller Name : </th>
			  <td>' . $client_reseller_name . '</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";



	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to client after purchase code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function purchaseCode_mail_client($contactemail, $baseurl, $detail)
{
	//$contactemail = "webplanex.test@gmail.com";
	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">' . $detail['no_of_codes'] . ' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s are now activated and ready to use.<a href="' . $baseurl . '">Click here to login to your account.</a></p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 0417557640.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";



	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after purchase code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function purchaseCode_mail_admin($contactemail, $baseurl, $detail, $client_reseller_name)
{
	/* $contactemail = "webplanex.test@gmail.com";
    	$detail['email'] = "info@scanlink.com.au"; */
	$contactemail = "info@scanlink.com.au";
	$to = $contactemail;
	$fromemail = $contactemail;
	// subject
	$subject = 'Scanlink Purchase Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Purchase Code</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>User has purchased code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left" >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left">Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left" >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left" >Reseller Name : </th>
			  <td>' . $client_reseller_name . '</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";



	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}


/* * **
 * **** send mail to client after purchase code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function resellerCode_mail_client($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "support@scanlink.net.au"; */
	//$contactemail = "webplanex.test@gmail.com";
	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['name'] . '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">' . $detail['no_of_codes'] . ' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s for user account (' . $detail['user_email'] . ') are now activated and ready to use</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 0417557640.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";

	/* 
  echo "<hr/><hr/><br/><br/>resellerCode mail client START<br/><br/><br/>";
  echo "Mail to be sent : ".$to;
  echo "Subject : ".$subject;
  echo "From Email : ".$fromemail;
  echo "Message : <br/>".$message;
  echo "<br/><br/>resellerCode mail client END<br/><br/><br/><hr/><hr/>";
  */

	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after purchase code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function resellerCode_mail_admin($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "webplanex.test@gmail.com"; */
	//$contactemail = "webplanex.test@gmail.com";
	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Purchase Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Purchase Code</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>A reseller has purchased code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left" >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left">Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left" >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] * $detail['per_code_amount'] * 12, 2) . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";


	/* echo "<hr/><hr/><br/><br/>resellerCode mail admin START<br/><br/><br/>";
  echo "Mail to be sent : ".$to;
  echo "Subject : ".$subject;
  echo "From Email : ".$fromemail;
  echo "Message : <br/>".$message;
  echo "<br/><br/>resellerCode mail admin END<br/><br/><br/><hr/><hr/>";
  */
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}


/* * **
 * **** send mail to client after renew code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function reseller_renewCode_mail_client($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "webplanex.test@gmail.com"; */
	//$contactemail = "webplanex.test@gmail.com";
	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['name'] . '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">' . $detail['no_of_codes'] . ' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['total_amount'], 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['total_amount'], 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s for user account (' . $detail['user_email'] . ') are now activated and ready to use</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";

	//echo $message;exit;
	// Mail it
	$send = sendHtmlMail($to, $subject, $message, $fromemail);

	/* echo "HELLO ";
    if($send){
    echo "TRUE";
    }else{
    echo "FALSE";
    }
    exit;
   */
}

/* * **
 * **** send mail to admin after renew code 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function reseller_renewCode_mail_admin($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "webplanex.test@gmail.com"; */
	//$contactemail = "webplanex.test@gmail.com";
	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Renew Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Renew Code</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>Reseller has renewed code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left">Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left" >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left">No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount per code : </th>
			  <td>' . $detail['amount_per_code'] . ' </td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['total_amount'], 2) . ' AUD </td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";

	//echo $message;exit;

	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}


/* * **
 * **** send mail to client after order label 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function orderlabel_mail_client($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "webplanex.test@gmail.com"; */
	//$contactemail = "webplanex.test@gmail.com";


	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . '</p>
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
					<td style="height:30px;" height="30" align="right" >' . $detail['profile_id'] . '</td>
				</tr>
				<tr>
					<td style="height:30px;" height="30">' . $detail['qty_small'] . ' ScanLink Label/s 50x40mm</td>
					<td style="height:30px;" height="30" align="right" >$' . number_format($detail['price_small'] * $detail['qty_small'], 2) . ' AUD </td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">' . $detail['qty_large'] . ' ScanLink Label/s 100x75mm</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['price_large'] * $detail['qty_large'], 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format(($detail['price_small'] * $detail['qty_small']) + ($detail['price_large'] * $detail['qty_large']), 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink label/s will be dispatched within two business days.</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it


	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after order label
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function orderlabel_mail_admin($contactemail, $baseurl, $detail)
{
	/* $contactemail = "webplanex.test@gmail.com";
    $detail['email'] = "webplanex.test@gmail.com"; */
	//$contactemail = "webplanex.test@gmail.com";
	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Order Label';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Order Label</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>User has purchased Order Label at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left">Code Number : </th>
			  <td>' . $detail['profile_id'] . '</td>
			</tr>
			<tr>
			  <th align="left">Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left" >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left">ScanLink Label/s 50x40mm: </th>
			  <td>' . $detail['qty_small'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount ScanLink Label/s 50x40mm : </th>
			  <td>$' . number_format($detail['price_small'] * $detail['qty_small'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">ScanLink Label/s 100x75mm: </th>
			  <td>' . $detail['qty_large'] . '</td>
			</tr>
			<tr>
			  <th align="left" >Amount ScanLink Label/s 100x75mm : </th>
			  <td>$' . number_format($detail['price_large'] * $detail['qty_large'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left" >Total : </th>
			  <td>$' . number_format(($detail['price_small'] * $detail['qty_small']) + ($detail['qty_large'] * $detail['price_large']), 2) . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it

	sendHtmlMail($to, $subject, $message, $fromemail);
}

function sendHtmlMail($to, $subject, $message, $fromemail)
{
	require_once 'phpmailer/PHPMailerAutoload.php';

	$mail = new PHPMailer;
	
	$mail->isSMTP();
	// AMAZON WORKING
	$mail->Host = 'email-smtp.us-east-1.amazonaws.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'AKIAZXUXY46OQ37B6ZTY';                 // SMTP username
	$mail->Password = 'BLXaKnquavwSnDtoADmeWXXTM9PGWdGNRuLDa5ThGt22';

	
	$mail->SMTPSecure = 'ssl'; 
	$mail->Port = 465;

	$mail->From = $fromemail;
	$mail->FromName = 'Scanlink';
	$to = str_replace(' ', '', $to); //remove white space
	$mail->addAddress($to, '');     // Add a recipient
	
	$mail->isHTML(true);  // Set email format to HTML
	//$mail->SMTPDebug = true;
	//$mail->SMTPDebug = 2;
	$mail->Subject = $subject;
	$mail->Body    = $message;
	$mail->AltBody = "";

	//$mail->addCustomHeader('MIME-Version: 1.0');
	//$mail->addCustomHeader('Content-type: text/html; charset=iso-8859-1');
	//$header_from = "From: ScanLink Solutions <" . $fromemail . ">";
	//$mail->addCustomHeader('$header_from');

	$send = $mail->send();
	$errorinfo = $mail->ErrorInfo;
	$errorinfo = $message."<br>".$errorinfo;
	if (!$send) {
		//echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
      		mail('support@scanlink.net.au','Mail sending error 1431 sendHTMLMail.',$errorinfo);
	} else {
		//echo 'Message has been sent';
      		//mail('support@scanlink.net.au','HTML Mail Send Success',$subject);
	}
}



/* * **
 * **** send mail to client after order for form builder 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function reseller_mail_client_for_formbuilder($contactemail, $baseurl, $detail, $order_id)
{

	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation - order number:' . $order_id;
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['name'] . ',</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30"> Order Number</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >' . $order_id . '</td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30"> ScanLink Form Builder Activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['no_of_codes'], 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['per_code_amount'], 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";


	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after order for form builder 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function reseller_mail_admin_for_formbuilder($contactemail, $baseurl, $detail, $order_id)
{
	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Form Builder order summary - order number:' . $order_id;
	// message
	$message = '<html>
		<head>
		<title>Scanlink Form Builder order summary</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>A reseller has ordered a form builder at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left" >Order Number : </th>
			  <td>' . $order_id . '</td>
			</tr>
			<tr>
			  <th align="left" >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left">Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left" >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";

	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}



/* * **
 * **** send mail to client after order for form builder 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function client_mail_for_formbuilder($contactemail, $baseurl, $detail, $order_id)
{

	$to = $detail['email'];
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'ScanLink order confirmation - order number:' . $order_id;
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background-color: #008901;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30"> Order Number</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >' . $order_id . '</td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">ScanLink Form Builder Activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['per_code_amount'], 2) . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['per_code_amount'], 2) . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on 0417557640.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";


	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/* * **
 * **** send mail to admin after after order for form builder 
 * **** @param detail array
 * **** @param baseurl string
 * **** @param contactemail string
 * ** */

function admin_mail_for_formbuilder($contactemail, $baseurl, $detail, $order_id)
{

	$to = $contactemail;
	//$fromemail = $contactemail;
	$fromemail = "info@scanlink.com.au";
	// subject
	$subject = 'Scanlink Form Builder order summary - order number:' . $order_id;
	// message
	$message = '<html>
		<head>
		<title>Scanlink Form Builder order summary</title>
		</head>
		<body style="font-family:calibri;">
		<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>User has ordered a form builder at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th align="left" >Order Number : </th>
			  <td>' . $order_id . '</td>
			</tr>
			<tr>
			  <th align="left" >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th align="left">First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th align="left">Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th align="left" >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['per_code_amount'], 2) . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl . '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";


	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

function client_mail_for_formbuilder_answer($fromemail, $baseurl, $recipient_email, $details, $profile_id, $profile_name, $profile_logo, $uploaded_image, $subject = "")
{

	$to = $recipient_email;

	if ($profile_logo == "")
		$profile_logo = 'images/email-logo.png';

	// $subject = 'User response for profile No. ' . $profile_id;
	if (empty($subject)) {
		$subject = 'User response for profile No. ' . $profile_id;
	}
	// message
	$message = '<html>
			<head>
			<title>Scanlink Form Builder Answer summary</title>
			</head>
			<body style="font-family:calibri;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . $profile_logo . '" width="150"  alt="logo" /></a></p>
			<hr/>
			<p>Profile number : ' . $profile_id . '</p>
			<p>Profile name : ' . $profile_name . '</p>
			<p>Submission Date/Time : ' . date('d/m/Y H:i') . '</p>
			<p><hr/></p>
			<p></p>
			' . $details . '
			<br/>
			<p>END OF SUBMISSION</p>
			
			</body>
			</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";



	// Mail it
	if ($uploaded_image != "")
		send_mail_with_attachment($to, $subject, $message, $fromemail, $uploaded_image);
	else
		sendHtmlMail($to, $subject, $message, $fromemail);
}

function client_mail_for_formbuilder_answer_as_pdf($fromemail, $baseurl, $recipient_email, $details, $profile_id, $profile_name, $profile_logo, $uploaded_image, $subject = "")
{
	$to = $recipient_email;


	if ($profile_logo == "")
		$profile_logo = 'images/email-logo.png';

	// $subject = 'User response for profile No. ' . $profile_id;

	if (empty($subject)) {
		$subject = 'User response for profile No. ' . $profile_id;
	}
	// message
	$message = '<html>
			<head>
			<title>Scanlink Form Builder Answer summary</title>
			</head>
			<body style="font-family:calibri;">
			<p>Hi,</p>
                        <p>Please find user\'s response for profile No. ' . $profile_id . ' as attached PDF.</p>
			
			</body>
			</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";

	// Mail it
	if ($uploaded_image != "")
		send_mail_with_attachment($to, $subject, $message, $fromemail, $uploaded_image);
	else
		sendHtmlMail($to, $subject, $message, $fromemail);
}

function forgot_mail_client_voc($to, $txtnewpass, $fromemail, $baseurl)
{

	$subject = 'Response from ScanLink Solutions for forgot password request';
	$message = '<html>
				<head>
				<title>Response from ScanLink Solutions for forgot password request</title>
				</head>
				<body style="font-family:calibri;">
				<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
				<hr/>
				<p>Hi there!</p>
				<p>As usual, ScanLink is always happy to help!</p>
				<p>Thank you for your enquiry. Here is the information you requested.</p>
				<table cellpadding="3" cellspacing="0" >
						<tr>
						  <th align="right">Your Password : </th>
						  <td>' . $txtnewpass . '</td>
						</tr>					
				</table>
				<br />
				<p>Sincerely,</p>
				<p>Customer Support Team</p>
				<p><a href="' . $baseurl . '">ScanLink</a></p>
				<p style="text-align:center;font-size:11px;">ScanLink is a division of NWIT Services ABN 12 838 792 695</p>
				</body>
				</html>';
	// Mail it


	$return = sendHtmlMail($to, $subject, $message, $fromemail);
	return $return;
}




function send_mail_with_attachment($to, $subject, $message, $fromemail, $uploaded_image)
{
	require_once 'phpmailer/PHPMailerAutoload.php';

	$mail = new PHPMailer;
	$mail->isSMTP();
	 
	// AMAZON WORKING
	$mail->Host = 'email-smtp.us-east-1.amazonaws.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'AKIAZXUXY46OQ37B6ZTY';                 // SMTP username
	$mail->Password = 'BLXaKnquavwSnDtoADmeWXXTM9PGWdGNRuLDa5ThGt22';

	// NWIT TEST
	//$mail->Host = 'mail.nwit.com.au';  // Specify main and backup SMTP servers
	//$mail->SMTPAuth = true;                               // Enable SMTP authentication
	//$mail->Username = 'scanlink@nwit.com.au';                 // SMTP username
	//$mail->Password = 'gb#TQDlaGph+';
	
	$mail->SMTPSecure = 'ssl'; 
	$mail->Port = 465;
	//$mail->Port = 587;

	$mail->From = $fromemail;
	$mail->FromName = 'Scanlink';
	$to = str_replace(' ', '', $to); //remove white space
	$mail->addAddress($to, '');     // Add a recipient

	$uploaded_image = explode(",", $uploaded_image);
	foreach ($uploaded_image as $image) {
		if (file_exists("images/formbuilder_upload/" . $image)) {
			$mail->addAttachment("images/formbuilder_upload/" . $image);
		}
	}

	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = $subject;
	$mail->Body    = $message;
	$mail->AltBody = "";
	$send = $mail->send();
	$errorinfo = $mail->ErrorInfo;
	$errorinfo = $message."<br>".$errorinfo;
	if (!$send) {
		//echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
      		mail('support@scanlink.net.au','Mail sending error 1431 send_mail_with_attachment',$errorinfo);
	} else {
		echo 'Message has been sent';
      		//mail('support@scanlink.net.au','Mail Send Success',$subject);
	}
}
