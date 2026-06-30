<?php 
/****
***** send mail to client after registration 
***** @param email string
***** @param first_name string
***** @param last_name string
***** @param password string
***** @param baseurl string
***** @param contactemail string
****/
function registration_mail_client($contactemail,$baseurl,$email,$first_name,$last_name,$password){
	
	$to = $email;
	$fromemail = $contactemail;
	// subject
	$subject = 'Welcome to ScanLink';
	// message
	$message = '<html>
		<head>
		<title>Welcome to ScanLink</title>
		</head>
		<body>
		<p>Hi ' . $first_name . ' ' . $last_name . '</p>
		<p>Thank you for registering with ScanLink.</p>
		<p>Now you can use the power of mobile visual communication to enhance Health, Safety, Sustainability and Quality in your workplace. You can also use ScanLink to promote products and services.</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on +61 364314025.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		<p style="text-align:center;font-size:11px;">ScanLink is a division of North West IT Services ABN 12 838 792 695</p>
		</body>
		</html>';
	

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
}


/****
***** send mail to admin after registration 
***** @param email string
***** @param first_name string
***** @param last_name string
***** @param baseurl string
***** @param contactemail string
****/
function registration_mail_admin($contactemail,$baseurl,$email,$first_name,$last_name){
	
	$to = $contactemail;
	$fromemail = $contactemail;
	// subject
	$subject = 'Scanlink New User Register';
	// message
	$message = '<html>
		<head>
		<title>Scanlink New User Register</title>
		</head>
		<body>
		<p>Hi ' . $fromemail . '!</p>
		<p>New user has been created account at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th >Email : </th>
			  <td>' . $email . '</td>
			</tr>	
			<tr>
			  <th >First name : </th>
			  <td>' . $first_name . '</td>
			</tr>				
			<tr>
			  <th >Last name : </th>
			  <td>' . $last_name . '</td>
			</tr>
		</table>
		<br/>
		<p>Best regards,</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
}


/****
***** send mail to client after renew code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function renewCode_mail_client($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $detail['email'];
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><img src="'.$baseurl.'images/logo.png" width="150" alt="logo" /></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<p>Order Description</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Codes</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">'.$detail['no_of_codes'].' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s are now activated and ready to use.<a href="' . $baseurl. '">Click here to login to your account.</a></p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on +61 364314025.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT Services ABN 12 838 792 695</p>
		</body>
		</html>';
	
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
	
	echo $to;
	echo $message;
	exit;
	
}




/****
***** send mail to admin after renew code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function renewCode_mail_admin($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $contactemail;
	// subject
	$subject = 'Scanlink Renew Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Renew Code</title>
		</head>
		<body>
		<p>Hi ' . $fromemail . '!</p>
		<p>User has renewed code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th >First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th >Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'],2) . ' AUD </td>
			</tr>
			<tr>
			  <th >Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </td>
			</tr>
		</table>
		<br/>
		<p>Best regards,</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
}

/****
***** send mail to client after purchase code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function purchaseCode_mail_client($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $detail['email'];
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><img src="'.$baseurl.'images/logo.png" width="150" alt="logo" /></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<p>Order Description</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Codes</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">'.$detail['no_of_codes'].' ScanLink Code/s 12 month activation</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink code/s are now activated and ready to use.<a href="' . $baseurl. '">Click here to login to your account.</a></p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on +61 364314025.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT Services ABN 12 838 792 695</p>
		</body>
		</html>';
	
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
	
}


/****
***** send mail to admin after purchase code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function purchaseCode_mail_admin($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $contactemail;
	// subject
	$subject = 'Scanlink Purchase Code';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Purchase Code</title>
		</head>
		<body>
		<p>Hi ' . $fromemail . '!</p>
		<p>User has purchased code at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th >First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th >No. of codes : </th>
			  <td>' . $detail['no_of_codes'] . '</td>
			</tr>
			<tr>
			  <th >Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th >Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best regards,</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
}

/****
***** send mail to client after order label 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function orderlabel_mail_client($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $detail['email'];
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><img src="'.$baseurl.'images/logo.png" width="150" alt="logo" /></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] . $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<p>Order Description</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Codes</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="height:30px;" height="30">'.$detail['qty_small'].' ScanLink Label/s 45x60mm</td>
					<td style="height:30px;" height="30" align="right" >$' . number_format($detail['price_small'] *$detail['qty_small'],2)  . ' AUD </td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30">'.$detail['qty_large'].' ScanLink Label/s 90x120mm</td>
					<td style="border-bottom:1px solid #fff;height:30px;" height="30" align="right" >$' . number_format($detail['price_large'] *$detail['qty_large'],2)  . ' AUD </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td style="height:30px;" height="30"><b>Total</b></td>
					<td style="height:30px;" height="30" align="right"><b>$' . number_format(($detail['price_small'] *$detail['qty_small']) + ($detail['price_large'] *$detail['qty_large']),2)  . ' AUD </b></td>
				</tr>
			</tfoot>
		</table>
		<p>Your ScanLink label/s will be dispatched within two business days.</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.net.au or call us during business hours on Monday to Friday on +61 364314025.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT Services ABN 12 838 792 695</p>
		</body>
		</html>';
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
	
}

/****
***** send mail to admin after order label
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function orderlabel_mail_admin($contactemail,$baseurl,$detail){
	
	//$contactemail = "support@scanlink.net.au";
	$to = $contactemail;
	$fromemail = $contactemail;
	// subject
	$subject = 'Scanlink Order Label';
	// message
	$message = '<html>
		<head>
		<title>Scanlink Order Label</title>
		</head>
		<body>
		<p>Hi ' . $fromemail . '!</p>
		<p>User has purchased Order Label at scanlink.</p>
		<br />
		<p>Here is user details</p>
		<table cellpadding="3" cellspacing="0" >
			<tr>
			  <th >Email : </th>
			  <td>' . $detail['email'] . '</td>
			</tr>	
			<tr>
			  <th >First name : </th>
			  <td>' . $detail['first_name'] . '</td>
			</tr>				
			<tr>
			  <th >Last name : </th>
			  <td>' . $detail['last_name'] . '</td>
			</tr>
			<tr>
			  <th >ScanLink Label/s 45x60mm: </th>
			  <td>' . $detail['qty_small'] . '</td>
			</tr>
			<tr>
			  <th >Amount ScanLink Label/s 45x60mm : </th>
			  <td>$' . number_format($detail['price_small']*$detail['qty_small'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th >ScanLink Label/s 90x120mm: </th>
			  <td>' . $detail['qty_large'] . '</td>
			</tr>
			<tr>
			  <th >Amount ScanLink Label/s 90x120mm : </th>
			  <td>$' . number_format($detail['price_small']*$detail['price_large'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th >Total : </th>
			  <td>$' . number_format(($detail['price_small']*$detail['qty_small']) + ($detail['price_small']*$detail['price_large']),2)  . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best regards,</p>
		<p><a href="' . $baseurl. '">Scan Link</a></p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	mail($to, $subject, $message, $headers);
}
?>