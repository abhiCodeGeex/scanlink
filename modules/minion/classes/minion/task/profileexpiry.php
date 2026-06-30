<?php defined('SYSPATH') or die('No direct script access.');
Log::instance()->add(Log::INFO, 'Profile Expiry Cron Top');

class Minion_Task_profileexpiry extends Minion_Task
{
    /**
     * *
     * @return null
     */
    public function execute(array $params)
    {
        
      $users = Model::factory('profiles')->get_expiry_profiles_user_id("expired_at!='0000-00-00 00:00:00'");
		
		$cron_qry = DB::insert(
             'cron_job', array('start_date','cron_name'))
			 ->values(array(date("Y-m-d H:i:s"),'code_expiry'))
              ->execute();
			  
		$inserted_id= $cron_qry[0]; 
			  
		
		$adminemail = "admin@scanlink.net.au";
		$baseurl = URL::base();
		
		if(is_array($users) && sizeof($users) > 0)
		{
			foreach($users as $user)
			{
				$user_id = $user["user_id"];
				
				///start - 30 days before expiry email notification 
				$profiles = Model::factory('profiles')->get_all_profiles(" user_id='".$user_id."' and (free_code=0 or (free_code=1 and renewal_required=1) ) and expired_at!='0000-00-00 00:00:00' and (DATEDIFF(expired_at,now()))=30");					
				
				if(count($profiles)>0)
				{
					if(count($profiles)>2) //send mail with multiple code renewal link 
					{
						$profile_id_arr = "";
						foreach($profiles as $profile)
						{
							if($profile_id_arr=="")
								$profile_id_arr = $profile["id"];
							else 
								$profile_id_arr = $profile_id_arr ."<br>".$profile["id"];
								
						}
										
						
						$userdata = Model::factory('user')->getUserByID($user_id);
					
						$profile_data['profile_id'] = $profile_id_arr;
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] =  $userdata['email'];
						$profile_data['name'] = $profile['code_profile_name'];
						
						//send mail to user START 
			         $this->code_expire_thirty_days_notification_multiple($adminemail,$baseurl,$profile_data);
				      //send mail to user END
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expire_thirty_days_notification_multiple');	
					}
					else //send mail with single code renewal link 
					{
						foreach($profiles as $profile)
						{
							$userdata = Model::factory('user')->getUserByID($user_id);
					
							$profile_data['profile_id'] = $profile["id"];
							$profile_data['expired_date'] = $profile['expired_at_min'];
							$profile_data['first_name'] = $userdata['first_name'];
							$profile_data['last_name'] = $userdata['last_name'];
							$profile_data['email'] =  $userdata['email'];
							$profile_data['name'] = $profile['code_profile_name'];
							
							
						
							//send mail to user START 
			            $this->code_expire_thirty_days_notification($adminemail,$baseurl,$profile_data);
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expire_thirty_days_notification');
				        	//send mail to user END
						} //end foreach($profiles as $profile)
					}
				} //end if(count($profiles)>0)
				///end - 30 days before expiry email notification
				
				
				///start - 3 days before expiry email notification 
				$profiles = Model::factory('profiles')->get_all_profiles(" user_id='".$user_id."' and (free_code=0 or (free_code=1 and renewal_required=1) ) and expired_at!='0000-00-00 00:00:00' and (DATEDIFF(expired_at,now()))=3");					
				
				if(count($profiles)>0)
				{
					if(count($profiles)>2) //send mail with multiple code renewal link 
					{
						$profile_id_arr = "";
						foreach($profiles as $profile)
						{
							if($profile_id_arr=="")
								$profile_id_arr = $profile["id"];
							else 
								$profile_id_arr = $profile_id_arr ."<br>".$profile["id"];
								
						}
						
						
						$userdata = Model::factory('user')->getUserByID($user_id);
					
						$profile_data['profile_id'] = $profile_id_arr;
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] =  $userdata['email'];
						$profile_data['name'] = $profile['code_profile_name'];
						
						//send mail to user START 
			         $this->code_expire_three_days_notification_multiple($adminemail,$baseurl,$profile_data);
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expire_three_days_notification_multiple');
				      //send mail to user END
						
					}
					else //send mail with single code renewal link 
					{
						foreach($profiles as $profile)
						{
							$userdata = Model::factory('user')->getUserByID($user_id);
					
							$profile_data['profile_id'] = $profile["id"];
							
							$profile_data['expired_date'] = $profile['expired_at_min'];
							$profile_data['first_name'] = $userdata['first_name'];
							$profile_data['last_name'] = $userdata['last_name'];
							$profile_data['email'] =  $userdata['email'];
							$profile_data['name'] = $profile['code_profile_name'];
							
							
						
							//send mail to user START 
			           	$this->code_expire_three_days_notification($adminemail,$baseurl,$profile_data);
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expire_three_days_notification');
				        	//send mail to user END
						} //end foreach($profiles as $profile)
					}
				} //end if(count($profiles)>0)
				///end - 3 days before expiry email notification
				
				///start - expired code email notification 
				$profiles = Model::factory('profiles')->get_all_profiles(" user_id='".$user_id."' and (free_code=0 or (free_code=1 and renewal_required=1) ) and expired_at!='0000-00-00 00:00:00' and (DATEDIFF(expired_at,now()))=0");					
				
				
				if(count($profiles)>0)
				{
					if(count($profiles)>2) //send mail with multiple code renewal link 
					{
						$profile_id_arr = "";
						foreach($profiles as $profile)
						{
							if($profile_id_arr=="")
								$profile_id_arr = $profile["id"];
							else 
								$profile_id_arr = $profile_id_arr ."<br>".$profile["id"];
								
						}
												
						$userdata = Model::factory('user')->getUserByID($user_id);
					
						$profile_data['profile_id'] = $profile_id_arr;
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] =  $userdata['email'];
						$profile_data['name'] = $profile['code_profile_name'];
						
						//send mail to user START 
			        	$this->code_expired_notification_multiple($adminemail,$baseurl,$profile_data);
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expired_notification_multiple');
				      //send mail to user END
						
					}
					else //send mail with single code renewal link 
					{
						foreach($profiles as $profile)
						{
							$userdata = Model::factory('user')->getUserByID($user_id);
					
							$profile_data['profile_id'] = $profile["id"];
							
							$profile_data['expired_date'] = $profile['expired_at_min'];
							$profile_data['first_name'] = $userdata['first_name'];
							$profile_data['last_name'] = $userdata['last_name'];
							$profile_data['email'] =  $userdata['email'];
							$profile_data['name'] = $profile['code_profile_name'];
							
							
						
							//send mail to user START 
			           	$this->code_expired_notification($adminemail,$baseurl,$profile_data);
					Log::instance()->add(Log::INFO, 'Profile Expiry Cron: code_expired_notification');
				        	//send mail to user END
						} //end foreach($profiles as $profile)
					}
				} //end if(count($profiles)>0)
				///end - expired code email notification
				
				
				
			} //end foreach
			
		}
		
		
		
		
		$dataFill = array("end_date"=>date("Y-m-d H:i:s"));
	  	$query = DB::update('cron_job')->set($dataFill)->where('cron_id', '=', $inserted_id);
	  	$query->execute();
    }
	
	function send_mail_with_phpmailer($to, $subject, $message, $fromemail)
	{
		Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function send_mail_with_phpmailer');
		require_once(realpath(dirname($_SERVER['PHP_SELF']))."/phpmailer/PHPMailerAutoload.php");
		
		$fromemail = "info@scanlink.com.au"; //added to enable email send
		$mail = new PHPMailer;

		$mail->isSMTP();     //isMail, isSendmail  ,isSMTP  
		// Set mailer to use SMTP
		$mail->Host = 'email-smtp.us-east-1.amazonaws.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'AKIAZXUXY46OQ37B6ZTY';                 // SMTP username
		$mail->Password = 'BLXaKnquavwSnDtoADmeWXXTM9PGWdGNRuLDa5ThGt22';   
		
		$mail->SMTPSecure = 'ssl';
                //$mail->Port = 443;
		$mail->Port = 465;
		                     
		$mail->From = $fromemail;
		$mail->FromName = 'Scanlink';
		$to = str_replace(' ', '', $to); //remove white space
		$mail->addAddress($to, '');     // Add a recipient
		
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->SMTPDebug = 2;
		$mail->Subject = $subject;
		$mail->Body    = $message;
		$mail->AltBody = "";
		
		//return true; // remove/comment this 2 line for live code 
	  	//exit;
		
		$send = $mail->send();
		$errorinfo = $mail->ErrorInfo;
		$errorinfo = $message."<br>".$errorinfo."<br>".$to."";
		if (!$send) {
			//echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
      			mail('support@scanlink.net.au','Mail sending error 1431 send_mail_profile_expiry',$errorinfo);
			Log::instance()->add(Log::INFO, 'Mail sending error 1431 send_mail_profile_expiry: '.print_r($errorinfo,true));
		} else {
			//echo 'Message has been sent';
      			mail('support@scanlink.net.au','Mail Send Success Profile Expiry Cron Task',$subject);
			Log::instance()->add(Log::INFO, 'Mail Send Success Profile Expiry Cron Task: '.print_r($subject,true));
		}
	
	}
	
	/* * **
	 * **** send mail to client for code expiry notification before 30 days 
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function code_expire_thirty_days_notification($contactemail, $baseurl, $detail) 
	{
	  
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expire_thirty_days_notification');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expiry Notification - Code Profile Number: '.$detail['profile_id'];
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>Your ScanLink code profile No. '.$detail['profile_id'].' will expire on '.$detail["expired_date"].'.</p>
			<p>You now have 30 days to renew this code before it expires. If you do not renew the code it will become inactive.</p>
			<p>You can quickly and easily renew this code now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and select the renew option for this code. <a href="' . $baseurl . '">Click here to log in to your account</a></p>
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			<br/>
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	  
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	
	function code_expire_thirty_days_notification_multiple($contactemail, $baseurl, $detail) 
	{
	  
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expire_thirty_days_notification_multiple');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expiry Notification ';
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>The following ScanLink code/s will expire on '.$detail["expired_date"].'.</p>
			<p>'.$detail["profile_id"].'</p>
			<p>You now have 30 days to renew before the code/s expire. If the renewal period expires a code will become inactive.</p>
			<p>You can quickly and easily renew the code/s now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and select the renew option. <a href="' . $baseurl . '">Click here to log in to your account</a></p>
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			<br/>
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	 
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}

	/* * **
	 * **** send mail to client for code expiry notification before 3 days 
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function code_expire_three_days_notification($contactemail, $baseurl, $detail) 
	{
	  
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expire_three_days_notification');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expiry Notification - Code Profile Number: '.$detail['profile_id'];
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>Your ScanLink code profile No. '.$detail['profile_id'].' will expire on '.$detail["expired_date"].'.</p>
			<p>You now have 3 days to renew this code before it expires. If you do not renew the code it will become inactive.</p>
			<p>You can quickly and easily renew this code now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and select the renew option for this code. <a href="' . $baseurl . '">Click here to log in to your account</a></p>
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			<br/>
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	 
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}

	function code_expire_three_days_notification_multiple($contactemail, $baseurl, $detail) 
	{
	  
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expire_three_days_notification_multiple');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expiry Notification ';
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>The following code/s will expire on '.$detail["expired_date"].'.</p>
			<p>'.$detail["profile_id"].'</p>
			<p>You now have 3 days to renew before the code/s expire. If the renewal period expires a code will become inactive.</p>
			<p>You can quickly and easily renew the code/s now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and select the renew option. <a href="' . $baseurl . '">Click here to log in to your account</a></p>
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			<br/>
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	 
	  
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	

	/* * **
	 * **** send mail to client for code expired notification on expiry date 
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function code_expired_notification($contactemail, $baseurl, $detail) 
	{
	 
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expired_notification');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expired Notification - Code Profile Number: '.$detail['profile_id'];
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>The following ScanLink code profile has expired.</p>
			
			<table width="50%">
			<tr><td width="25%">Profile number:</td><td> '.$detail['profile_id'].'</td></tr>
			<tr><td width="25%">Profile name:</td><td> '.$detail['name'].'</td></tr>
			</table>
			
			<p>You can renew this code now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and selecting the code renewal function. To login to your account now <a href="' . $baseurl . '">click here</a></p>
			
			<p>Please note that all functions associated with this code will remain inactive until it has been renewed.</p>
			
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	
	function code_expired_notification_multiple($contactemail, $baseurl, $detail) 
	{
	 
	  Log::instance()->add(Log::INFO, 'Profile Expiry Cron: Function code_expired_notification_multiple');
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink Code Expired Notification ';
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink code expiry notification</title>
			</head>
			<body style="font-family:arial;">
			<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>
			<hr/>
			<p>Hi ' . $detail['first_name'] . '  ' . $detail['last_name'] . ',</p>
			<p>The following ScanLink code/s have expired.</p>
			<p>'.$detail['profile_id'].'</p>						
			<p>You can renew the code/s now by logging in to your <a href="' . $baseurl . '">ScanLink</a> account and selecting the code renewal function. To login to your account now <a href="' . $baseurl . '">click here</a></p>
			
			<p>Please note that all functions associated with each expired code will remain inactive until the subscription has been renewed.</p>
			
			
			<p>If you have any questions or require assistance you can contact us at anytime at <a href="mailto:support@scanlink.com.au">support@scanlink.com.au</a> or call us during business hours on Monday to Friday on +61 364314025.</p>
			
			<p>Sincerely,</p>
			<p>Customer Support Team</p>
			<p><a href="' . $baseurl . '">ScanLink</a></p>
			<p  style="text-align:center;font-size:11px;">ScanLink is a division of North West IT services ABN 12 838 792 695</p>
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	
}
