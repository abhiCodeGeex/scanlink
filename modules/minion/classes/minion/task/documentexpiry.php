<?php defined('SYSPATH') or die('No direct script access.');
Log::instance()->add(Log::INFO, 'Document Expiry Cron Top');

class Minion_Task_documentexpiry extends Minion_Task
{
    /**
     * *
     * @return null
     */
    public function execute(array $params)
    {
        
                $profiles= Model::factory('voc')->get_expiry_document_profile_id("expiry_date!='0000-00-00'");
		
                
                
		$cron_qry = DB::insert(
             'cron_job', array('start_date','cron_name'))
			 ->values(array(date("Y-m-d H:i:s"),'document_expiry'))
              ->execute();
			  
		$inserted_id= $cron_qry[0]; 
			  
		
		$adminemail = "admin@scanlink.net.au";
		$baseurl = URL::base();
		
		
		foreach($profiles as $profile)
                {
                    $profile_data_arr =  Model::factory('profiles')->getProfileByIdForMobile($profile["profile_id"]);
                    
                    $logo = Model::factory('logo')->getLogoByProfileId($profile["profile_id"]);
			
                    $profile_logo = "";

                    if (!empty($logo) && isset($logo["logo_name"]) && file_exists(realpath(dirname($_SERVER['PHP_SELF']))."/".$logo["logo_name"])) 
                    {
                        $profile_logo = $logo["logo_name"];
                    }
                    
                    $voc_recipients= Model::factory('voc')->get_voc_recipients_by_profile_id($profile["profile_id"]);
                    
                    $document_exp_thirty_days_arr= Model::factory('voc')->get_expiry_document_with_condition(" profile_id='".$profile["profile_id"]."' and (DATEDIFF(expiry_date,curdate()))=30");
                    
                    $document_name_str = "";
                    foreach($document_exp_thirty_days_arr as $document)
                    {
                        $expiry_date= $document["expiry_date_formated"];
                        if($document_name_str=="")
                                $document_name_str = $document["name"]."<br>";
                        else
                                $document_name_str .= $document["name"]."<br>";
                    }
                    
                    if($document_name_str!="") //send 30 days reminder mail
                    {
                        
                        $profile_data['profile_logo'] = $profile_logo;
				
                        foreach($voc_recipients as $voc_recipient)
			{
                                                     
                            $profile_data['email'] = $voc_recipient["email"];
                            $profile_data['doc_names'] = $document_name_str;
                            $profile_data['voc_username'] = $profile_data_arr["voc_first_name"]." ".$profile_data_arr["voc_last_name"];
                            $profile_data['voc_email_sign_line1'] = $profile_data_arr["voc_email_sign_line1"];
                            $profile_data['voc_email_sign1_bold'] = $profile_data_arr["voc_email_sign1_bold"];
                            $profile_data['voc_email_sign1_italic'] = $profile_data_arr["voc_email_sign1_italic"];
                            $profile_data['voc_email_sign1_underline'] = $profile_data_arr["voc_email_sign1_underline"];
                            $profile_data['voc_email_sign_line2'] = $profile_data_arr["voc_email_sign_line2"];
                            $profile_data['voc_email_sign2_bold'] = $profile_data_arr["voc_email_sign2_bold"];
                            $profile_data['voc_email_sign2_italic'] = $profile_data_arr["voc_email_sign2_italic"];
                            $profile_data['voc_email_sign2_underline'] = $profile_data_arr["voc_email_sign2_underline"];
                            
                            if($profile_data_arr["voc_email_url"]!="")
                                $profile_data['voc_email_url'] = $profile_data_arr["voc_email_url"];
                            else
                                $profile_data['voc_email_url'] = "http://www.myskills.gov.au/";
                            
                            if($profile_data_arr["voc_email_text"]!="")
                                $profile_data['voc_email_text'] = $profile_data_arr["voc_email_text"];
                            else
                                $profile_data['voc_email_text']= "Click here to find a service provider near you";
                            
                            $profile_data['expiry_date'] = $expiry_date;
                            $this->document_expire_thirty_days_notification($adminemail,$baseurl,$profile_data);
                        }
                    }
                    
                    $document_expired_arr= Model::factory('voc')->get_expiry_document_with_condition(" profile_id='".$profile["profile_id"]."' and (DATEDIFF(expiry_date,curdate()))=0");
                    
                    $document_name_str = "";
                    foreach($document_expired_arr as $document)
                    {
                       if($document_name_str=="")
                                $document_name_str = $document["name"]."<br>";
                        else
                                $document_name_str .= $document["name"]."<br>";
                    }
                    
                    if($document_name_str!="") //send document expired mail
                    {
                        $profile_data['profile_logo'] = $profile_logo;
				
                        foreach($voc_recipients as $voc_recipient)
			{
                           
                            $profile_data['email'] = $voc_recipient["email"];
                            $profile_data['doc_names'] = $document_name_str;
                            $profile_data['voc_username'] = $profile_data_arr["voc_first_name"]." ".$profile_data_arr["voc_last_name"];
                            $profile_data['voc_email_sign_line1'] = $profile_data_arr["voc_email_sign_line1"];
                            $profile_data['voc_email_sign1_bold'] = $profile_data_arr["voc_email_sign1_bold"];
                            $profile_data['voc_email_sign1_italic'] = $profile_data_arr["voc_email_sign1_italic"];
                            $profile_data['voc_email_sign1_underline'] = $profile_data_arr["voc_email_sign1_underline"];
                            $profile_data['voc_email_sign_line2'] = $profile_data_arr["voc_email_sign_line2"];
                            $profile_data['voc_email_sign2_bold'] = $profile_data_arr["voc_email_sign2_bold"];
                            $profile_data['voc_email_sign2_italic'] = $profile_data_arr["voc_email_sign2_italic"];
                            $profile_data['voc_email_sign2_underline'] = $profile_data_arr["voc_email_sign2_underline"];
                            
                            if($profile_data_arr["voc_email_url"]!="")
                                $profile_data['voc_email_url'] = $profile_data_arr["voc_email_url"];
                            else
                                $profile_data['voc_email_url'] = "http://www.myskills.gov.au/";
                            
                            if($profile_data_arr["voc_email_text"]!="")
                                $profile_data['voc_email_text'] = $profile_data_arr["voc_email_text"];
                            else
                                $profile_data['voc_email_text']= "Click here to find a service provider near you";
                            
                            
                            $this->document_expired_notification($adminemail,$baseurl,$profile_data);
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
		
		//return true; // remove/comment this 2 line for live code 
	  	//exit;
		
		$send = $mail->send();
		$errorinfo = $mail->ErrorInfo;
		$errorinfo = $message."<br>".$errorinfo;
		if (!$send) {
			//echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
      			mail('support@scanlink.net.au','Mail sending error 1431 send_mail_documentexpiry',$errorinfo);
		} else {
			//echo 'Message has been sent';
      			mail('support@scanlink.net.au','Mail Send Success',$subject);
		}
			
	}
	
	/* * **
	 * **** send mail to client for document expiry notification before 30 days 
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function document_expire_thirty_days_notification($contactemail, $baseurl, $detail) 
	{
	  
	 
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'VOCC Renewal Reminder';
	  
	  // message
	  $message = '<html>
			<head>
			<title>VOCC Renewal Reminder</title>
			</head>
			<body style="font-family:arial;">';
          
          if($detail["profile_logo"]!="")
	  	$message .= '<p><a href="' . $baseurl . '"><img src="' . $baseurl . $detail["profile_logo"].'" width="150" alt="logo" /></a></p>';
	  else
		$message .='<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>';
          
            $message .= '<hr/>
			<p>Dear '.$detail["voc_username"].',</p>
			<p>This is a courtesy reminder to notify you that the following document is due for renewal by the '.$detail["expiry_date"].'.</p>
                            '.$detail["doc_names"].'
			<p>You now have 30 days to update the document.</p>
			<p><a href="' . $detail["voc_email_url"]. '">'.$detail["voc_email_text"].'</a></p>
			
			<p>Please contact your VOCC administrator listed below for assistance or to update your profile.</p>
			
			<p>Regards,</p>';
            
            if($detail["voc_email_sign_line1"]=="" && $detail["voc_email_sign_line2"]=="")
                $message .= '<p>ScanLink Support Team</p>';
            else
            {
                $message .= '<p>';
                
                if($detail["voc_email_sign_line1"]!="")
                {
                    $voc_email_sign_line1 = $detail["voc_email_sign_line1"];
                    
                   
                    $message .= $voc_email_sign_line1;
                }
                
                
                
                $message .= '</p>';
            }
            
            $message .= '</body>
			</html>';

	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
          //echo $message;
	 
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	
	
	/* * **
	 * **** send mail to client for code expired notification on expiry date 
	 * **** @param detail array
	 * **** @param baseurl string
	 * **** @param contactemail string
	 * ** */

	function document_expired_notification($contactemail, $baseurl, $detail) 
	{
	 
	  
	  $to = $detail['email'];
	  $fromemail = $contactemail;
	  // subject
	  $subject = 'VOCC Expiry Notification';
	  
	  // message
	  $message = '<html>
			<head>
			<title>VOCC Expiry Notification</title>
			</head>
			<body style="font-family:arial;">';
          
          if($detail["profile_logo"]!="")
	  	$message .= '<p><a href="' . $baseurl . '"><img src="' . $baseurl . $detail["profile_logo"].'" width="150" alt="logo" /></a></p>';
	  else
		$message .='<p><a href="' . $baseurl . '"><img src="' . $baseurl . 'images/email-logo.png"  alt="logo" /></a></p>';
          
            $message .= '<hr/>
			<p>Dear '.$detail["voc_username"].',</p>
			<p>This is a courtesy reminder to notify you that the following document has expired.</p>
                            '.$detail["doc_names"].'
			<p><a href="' . $detail["voc_email_url"]. '">'.$detail["voc_email_text"].'</a></p>
			
			<p>Please contact your VOCC administrator listed below for assistance or to update your profile.</p>
			
			<p>Regards,</p>';
            
            if($detail["voc_email_sign_line1"]=="" && $detail["voc_email_sign_line2"]=="")
                $message .= '<p>ScanLink Support Team</p>';
            else
            {
                $message .= '<p>';
                
                if($detail["voc_email_sign_line1"]!="")
                {
                    $voc_email_sign_line1 = $detail["voc_email_sign_line1"];
                    
                    
                    
                    $message .= $voc_email_sign_line1;
                }
                
                
                
                $message .= '</p>';
            }
            
            $message .= '</body>
			</html>';
                        
	  // To send HTML mail, the Content-type header must be set
	  $headers = 'MIME-Version: 1.0' . "\r\n";
	  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	  $headers .= 'From: ScanLink Solutions <' . $fromemail . '>' . "\r\n";
	  
         //echo $message;
	  // Mail it
	  $this->send_mail_with_phpmailer($to, $subject, $message, $fromemail);
	}
	
	
	
}
