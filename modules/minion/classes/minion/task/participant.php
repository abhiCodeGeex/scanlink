<?php defined('SYSPATH') or die('No direct script access.');
Log::instance()->add(Log::INFO, 'Participant Expiry Cron Top');

class Minion_Task_participant extends Minion_Task
{
    /**
     * *
     * @return null
     */
    public function execute(array $params)
    {
        
        $profile_id_arr = Model::factory('formbuilder')->get_participant_list_with_cond(" due_date='".date("Y-m-d")."' and is_participated=0 ","profile_id");	
		
		$cron_qry = DB::insert(
             'cron_job', array('start_date','cron_name'))
			 ->values(array(date("Y-m-d H:i:s"),'participant'))
              ->execute();
			  
		$inserted_id= $cron_qry[0]; 
			  
		$adminemail = "support@scanlink.com.au";
		$baseurl = URL::base();
		
		
		
		if(is_array($profile_id_arr) && sizeof($profile_id_arr) > 0)
		{
			foreach($profile_id_arr as $profile)
			{
				$profile_id = $profile["profile_id"];
				
				
				$participant_arr = Model::factory('formbuilder')->get_participant_list_with_cond(" due_date='".date("Y-m-d")."' and is_participated=0 and profile_id='".$profile_id."' ");	
				
				$not_participated_list="";
				
				if(count($participant_arr)>0)
				{
					foreach($participant_arr as $participant)
					{
						if($not_participated_list=="")
							$not_participated_list = $participant["name"]."<br>";
						else
							$not_participated_list .= $participant["name"]."<br>";
							
					}
				}
				
				
				//get participant recepient list
				$participant_recipient_list_arr = Model::factory('formbuilder')->get_participant_recipient_list($profile_id);	
				
				if(count($participant_recipient_list_arr)>0 && $not_participated_list!="")
				{
					foreach($participant_recipient_list_arr as $participant_recipient)
					{
						$logo = Model::factory('logo')->getLogoByProfileId($profile_id);
			
						$profile_logo = "";
						
						if (!empty($logo) && isset($logo["logo_name"]) && file_exists(realpath(dirname($_SERVER['PHP_SELF']))."/".$logo["logo_name"])) 
						{
					        $profile_logo = $logo["logo_name"];
					    }
						
						$profile =  Model::factory('profiles')->getProfileByIdForMobile($profile_id);
						
						$profile_data['profile_id'] = $profile_id;
						$profile_data['code_profile_name'] = $profile["code_profile_name"];
						$profile_data['email'] = $participant_recipient["email"];
						$profile_data['not_participated_list'] = $not_participated_list;
						$profile_data['profile_logo'] = $profile_logo;
						
						//send mail to recepient START 
		            	$this->not_participated_notification($adminemail,$baseurl,$profile_data);
			        	//send mail to recepient END
					}
				}
			}
		}
		
		
		
		$dataFill = array("end_date"=>date("Y-m-d H:i:s"));
	  	$query = DB::update('cron_job')->set($dataFill)->where('cron_id', '=', $inserted_id);
	  	$query->execute();
    }
	
	function send_mail_with_phpmailer($to, $subject, $message, $fromemail)
	{
		require_once(realpath(dirname($_SERVER['PHP_SELF']))."/phpmailer/PHPMailerAutoload.php");
		$fromemail = "info@scanlink.com.au"; //added to enable email send
		$mail = new PHPMailer;

		$mail->isSMTP();     //isMail, isSendmail  ,isSMTP  
		// Set mailer to use SMTP
		$mail->Host = 'email-smtp.us-east-1.amazonaws.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = TRUE;                               // Enable SMTP authentication
		// $mail->Username = 'AKIAI63S6TGN5UMVESWA';                 // SMTP username
		// $mail->Password = 'Atg56rSJZOdzqWJAqoBCv223NwZX1rjtn2ATMzBOae6n';   
		$mail->Username = 'AKIAZXUXY46OQ37B6ZTY';                 // SMTP username
		$mail->Password = 'BLXaKnquavwSnDtoADmeWXXTM9PGWdGNRuLDa5ThGt22';   
		// $mail->Host = 'ssl://smtp.sendgrid.net';  // Specify main and backup SMTP servers
		// $mail->Username = 'apikey';                 // SMTP username
		// $mail->Password = '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';   
		$mail->SMTPSecure = 'ssl'; 
		//$mail->Port = 443;   
		 $mail->Port = 465;   
		                     
		$mail->From = $fromemail;
		$mail->FromName = 'Scanlink';
		$mail->addAddress($to, '');     // Add a recipient
		
		$mail->isHTML(TRUE);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $message;
		$mail->AltBody = "";
		
		$send = $mail->send();
		$errorinfo = $mail->ErrorInfo;
		$errorinfo = $message."<br>".$errorinfo;
		if (!$send) {
			//echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
      			mail('support@scanlink.net.au','Mail sending error 1431 send_mail_participant',$errorinfo);
		} else {
			//echo 'Message has been sent';
      			//mail('support@scanlink.net.au','Mail Send Success',$subject);
		}	
		
	}
	
	/* * **
	 * **** send mail to recipient for not participated user list on due date
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function not_participated_notification($contactemail, $baseurl, $detail) 
	{
	 
	  
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'ScanLink - Overdue Form Response';
	  
	 
	  
	  // message
	  $message = '<html>
			<head>
			<title>ScanLink - Overdue Form Response</title>
			</head>
			<body style="font-family:calibri;">';
			
	  if($detail["profile_logo"]!="")
	  	$message .= '<p><a href="' . $baseurl . '"><img src="' . $baseurl . $detail["profile_logo"].'" width="150" alt="logo" /></a></p>';
	  else
		$message .= '<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>';
		
		
		$message .=	'<hr/>
			<table width="50%">
			<tr><td width="30%" valign="top">Profile number:</td><td valign="top"> '.$detail['profile_id'].'</td></tr>
			<tr><td width="30%" valign="top">Profile name:</td><td valign="top"> '.$detail['code_profile_name'].'</td></tr>
			</table>
			<hr/>
			<p>The due by date for form submissions from the following participants has expired</p>
			'.$detail["not_participated_list"].'
			<br>END OF SUBMISSION
			<p align="center">Powered by <a href="' . $baseurl . '">ScanLink</a></p>
			
			
			</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
	   
	  // Mail it
	  $this->send_mail_with_phpmailer($to,$subject,$message,$fromemail);
	}
	
}
