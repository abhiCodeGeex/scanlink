<?php 
require_once("Mail.php");
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
	/*$contactemail = "webplanex.test@gmail.com";
	$email = "webplanex.test@gmail.com";*/
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
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $first_name . ' ' . $last_name . '</p>
		<p>Thank you for registering with ScanLink.</p>
		<p>Now you can use the power of mobile visual communication to enhance Health, Safety, Sustainability and Quality in your workplace. You can also use ScanLink to promote products and services.</p>
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.com.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';
	

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
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
	//$contactemail = "webplanex.test@gmail.com";
	
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
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
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
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}


/****
***** send mail to client after renew code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function renewCode_mail_client($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
	$to =  $detail['email'];
	$fromemail =$contactemail;
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] .'  ' . $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
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
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.com.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';
	
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	$send = sendHtmlMail($to, $subject, $message, $fromemail);
	
	/*echo "HELLO ";
	if($send){
		echo "TRUE";
	}else{
		echo "FALSE";
	}
	exit;
	*/
}




/****
***** send mail to admin after renew code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function renewCode_mail_admin($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
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
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
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
			  <th align="left">Amount per code : </th>
			  <td>$' . number_format($detail['per_code_amount'],2) . ' AUD </td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD </td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/****
***** send mail to client after purchase code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function purchaseCode_mail_client($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
	$to = $detail['email'];
	$fromemail = $contactemail;
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'] .'  '. $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
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
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.com.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';
	
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
	
}


/****
***** send mail to admin after purchase code 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function purchaseCode_mail_admin($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
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
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
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
			  <td>$' . number_format($detail['per_code_amount'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">Total : </th>
			  <td>$' . number_format($detail['no_of_codes'] *$detail['per_code_amount'] * 12,2)  . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

/****
***** send mail to client after order label 
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function orderlabel_mail_client($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
	$to =$detail['email'];
	$fromemail =  $contactemail;
	// subject
	$subject = 'ScanLink order confirmation';
	// message
	$message = '<html>
		<head>
		<title>ScanLink order confirmation</title>
		</head>
		<body>
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Hi ' . $detail['first_name'].'  ' . $detail['last_name'] .  '</p>
		<p>Your order has been received and a tax invoice will be mailed to you shortly.</p>
		<table cellpadding="6" cellspacing="0" class="orderdetail" style="background: linear-gradient(to bottom, #008901 0%, #007A01 100%) repeat scroll 0 0 transparent;border: 1px solid #006201;border-radius: 6px 6px 6px 6px;color: #FFFFFF;width: 100%;" >
			<thead style=" background: none repeat scroll 0 0 #004A00;border: 0 none;">
				<tr>
					<th colspan="2" style="height:40px;font-size: 18px;" height="40" align="left">Order Description</th>
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
		<p>If you have any questions or require assistance you can contact us at anytime at support@scanlink.com.au or call us during business hours on Monday to Friday on 1300 566 696.</p>
		<br/>
		<p>Sincerely,</p>
		<p>Customer Support Team</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';
	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
	
}

/****
***** send mail to admin after order label
***** @param detail array
***** @param baseurl string
***** @param contactemail string
****/
function orderlabel_mail_admin($contactemail,$baseurl,$detail){
	/*$contactemail = "webplanex.test@gmail.com";
	$detail['email'] = "webplanex.test@gmail.com";*/
	//$contactemail = "webplanex.test@gmail.com";
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
		<p><a href="' . $baseurl. '"><img src="'.$baseurl.'images/email-logo.png"  alt="logo" /></a></p>
		<hr/>
		<p>Dear Administrator,</p>
		<p>User has purchased Order Label at scanlink.</p>
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
			  <th align="left">ScanLink Label/s 45x60mm: </th>
			  <td>' . $detail['qty_small'] . '</td>
			</tr>
			<tr>
			  <th align="left">Amount ScanLink Label/s 45x60mm : </th>
			  <td>$' . number_format($detail['price_small']*$detail['qty_small'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left">ScanLink Label/s 90x120mm: </th>
			  <td>' . $detail['qty_large'] . '</td>
			</tr>
			<tr>
			  <th align="left" >Amount ScanLink Label/s 90x120mm : </th>
			  <td>$' . number_format($detail['price_small']*$detail['price_large'],2) . ' AUD</td>
			</tr>
			<tr>
			  <th align="left" >Total : </th>
			  <td>$' . number_format(($detail['price_small']*$detail['qty_small']) + ($detail['price_small']*$detail['price_large']),2)  . ' AUD</td>
			</tr>
		</table>
		<br/>
		<p>Best Regards,</p>
		<p><a href="' . $baseurl. '">ScanLink</a></p>
		<p  style="text-align:center;font-size:11px;">ScanLink is a division of GALA Technologies P/L ACN 161 505 513</p>
		</body>
		</html>';

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	// Mail it
	sendHtmlMail($to, $subject, $message, $fromemail);
}

function sendHtmlMail($to, $subject, $body, $from) {

   /* $from = "admin@scanlink.com.au";
    $to = $to;
	*/
	//$to = "webplanex.test@gmail.com";
	$to = $to;
    $from = $from;
    $subject = $subject;
    $body = $body;

    /*    $host = "webplanex.biz";
      $username = "noreply@webplanex.biz";
      $password = "WebPl@nex123"; */


    $host = "email-smtp.us-east-1.amazonaws.com";
    // $username = "AKIAI63S6TGN5UMVESWA";
    // $password = "Atg56rSJZOdzqWJAqoBCv223NwZX1rjtn2ATMzBOae6n";
    $username = "AKIAZXUXY46OQ37B6ZTY";
    $password = "BLXaKnquavwSnDtoADmeWXXTM9PGWdGNRuLDa5ThGt22";
    // $host = "smtp.sendgrid.net";
    // $username = "apikey";
    // $password = "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";



    $headers = array(
        'From' => $from,
        'To' => $to,
        'Subject' => $subject,
        'Content-type' => 'text/html; charset=iso-8859-1',
        'MIME-Version' => '1.0'
    );


    $mailObj = new Mail();
    $pearObj = new PEAR();

    $smtp = $mailObj->factory('smtp', array(
        'host' => $host,
        'auth' => true,
        'username' => $username,
        'password' => $password
            )
    );

    if (PEAR::isError($smtp)) {
      return false;
	  /*echo "FIRST";
      echo("<p>" . $smtp->getMessage() . "</p>");
	  exit;
	  */
    }


    $mail = $smtp->send($to, $headers, $body);


    if (PEAR::isError($mail)) {
      return false;
      /*echo("<p>" . $mail->getMessage() . "</p>"); 
	  exit;
	  */
    } else {
      return true;
      //echo("<p>Message successfully sent!</p>"); 
    }
  }
?>
