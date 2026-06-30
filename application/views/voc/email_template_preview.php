<div style="padding: 10px;">
    <?php if($section=="thirty_days_notification")
    {

            $message = "";
            if($profile["voc_email_url"]!="")
                $voc_email_url = $profile["voc_email_url"];
            else
                $voc_email_url = "http://www.myskills.gov.au/";

            if($profile["voc_email_text"]!="")
                $voc_email_text = $profile["voc_email_text"];
            else
                $voc_email_text= "Click here to find a service provider near you";

            if($profile_logo!="")
	  	$message .= '<p><a href="' . URL::base() . '"><img src="' . URL::base() . $profile_logo.'" width="150" alt="logo" /></a></p>';
            else
		$message .='<p><a href="' . URL::base() . '"><img src="' . URL::base() . 'images/email-logo.png"  alt="logo" /></a></p>';
          
            $message .= '<hr/>
			<p>Dear '.$profile["voc_first_name"].' '.$profile["voc_last_name"].',</p>
			<p>This is a courtesy reminder to notify you that the following document is due for renewal by the ##date##.</p>
                        
			<p>You now have 30 days to update the document.</p>
			<p><a href="' . $voc_email_url. '">'.$voc_email_text.'</a></p>
			
			<p>Please contact your VOCC administrator listed below for assistance or to update your profile.</p>
			
			<p>Regards,</p>';
            
            if($profile["voc_email_sign_line1"]=="" )
                $message .= '<p>ScanLink Support Team</p>';
            else
            {
                $message .= '<p>';
                
                if($profile["voc_email_sign_line1"]!="")
                {
                    $voc_email_sign_line1 = $profile["voc_email_sign_line1"];
                    
                    $message .= $voc_email_sign_line1;
                }
                
                
                
                $message .= '</p>';
            }
    }
    
    if($section=="expired_notification")
    {
            $message = "";
            if($profile["voc_email_url"]!="")
                $voc_email_url = $profile["voc_email_url"];
            else
                $voc_email_url = "http://www.myskills.gov.au/";

            if($profile["voc_email_text"]!="")
                $voc_email_text = $profile["voc_email_text"];
            else
                $voc_email_text= "Click here to find a service provider near you";

            if($profile_logo!="")
	  	$message .= '<p><a href="' . URL::base() . '"><img src="' . URL::base() . $profile_logo.'" width="150" alt="logo" /></a></p>';
            else
		$message .='<p><a href="' . URL::base() . '"><img src="' . URL::base() . 'images/email-logo.png"  alt="logo" /></a></p>';
          
            $message .= '<hr/>
			<p>Dear '.$profile["voc_first_name"].' '.$profile["voc_last_name"].',</p>
			<p>This is a courtesy reminder to notify you that the following document has expired.</p>
                        
			<p><a href="' . $voc_email_url. '">'.$voc_email_text.'</a></p>
			
			<p>Please contact your VOCC administrator listed below for assistance or to update your profile.</p>
			
			<p>Regards,</p>';
            
            if($profile["voc_email_sign_line1"]=="" )
                $message .= '<p>ScanLink Support Team</p>';
            else
            {
                $message .= '<p>';
                
                if($profile["voc_email_sign_line1"]!="")
                {
                    $voc_email_sign_line1 = $profile["voc_email_sign_line1"];
                    
                   
                    
                    $message .= $voc_email_sign_line1;
                }
                
               
                
                $message .= '</p>';
            }
          
    }
    
    echo $message;
?>
 </div>  