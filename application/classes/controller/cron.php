<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cron extends Controller_Template_Master {
	
	public function action_index()
	{
       	$profiles = Model::factory('profiles')->get_all_profiles("expired_at!='0000-00-00 00:00:00'");	
		
		include_once("mailsend.php");
		$adminemail = "admin@scanlink.net.au";
		$baseurl = URL::base();
		
		$cron_qry = DB::insert(
                      'cron_job', array(
                  'start_date'
                                     )
              )
              ->values(
                      array(
                          date("Y-m-d H:i:s")                         
                      )
              )
              ->execute();
			  
		$inserted_id= $cron_qry[0];
		
		if(is_array($profiles) && sizeof($profiles) > 0)
		{
			foreach($profiles as $profile)
			{
				if($profile["expired_at"]!="0000-00-00 00:00:00" && ($profile["date_diff"]>="0" && $profile["date_diff"]<="30"))
				{
					
					if($profile["date_diff"]=="30")
					{	
						$userdata = Model::factory('user')->getUserByID($profile['user_id']);
					
						$profile_data['profile_id'] = $profile["id"];
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] = $userdata['email'];
						$profile_data['name'] = $profile['name'];
					
						//send mail to user START 
		            	code_expire_thirty_days_notification($adminemail,$baseurl,$profile_data);
			        	//send mail to user END
					}
					
					if($profile["date_diff"]=="3")
					{	
						$userdata = Model::factory('user')->getUserByID($profile['user_id']);
					
						$profile_data['profile_id'] = $profile["id"];
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] = $userdata['email'];
						$profile_data['name'] = $profile['name'];
						
						//send mail to user START 
		            	code_expire_three_days_notification($adminemail,$baseurl,$profile_data);
			        	//send mail to user END
					}
					
					if($profile["date_diff"]=="0") // expired
					{	
						$userdata = Model::factory('user')->getUserByID($profile['user_id']);
					
						$profile_data['profile_id'] = $profile["id"];
						$profile_data['expired_date'] = $profile['expired_at_min'];
						$profile_data['first_name'] = $userdata['first_name'];
						$profile_data['last_name'] = $userdata['last_name'];
						$profile_data['email'] = $userdata['email'];
						$profile_data['name'] = $profile['name'];
						
						//send mail to user START 
		            	code_expired_notification($adminemail,$baseurl,$profile_data);
			        	//send mail to user END
					}
				}
				
			} //end foreach
			
		}

		$dataFill = array(
						"end_date"=>date("Y-m-d H:i:s")
					);
	  	$query = DB::update('cron_job')->set($dataFill)->where('cron_id', '=', $inserted_id);
	  	$query->execute();
		exit;
		
	}
	
	public function action_participant()
	{
       	$profile_id_arr = Model::factory('formbuilder')->get_participant_list_with_cond(" due_date='".date("Y-m-d")."' and is_participated=0 ","profile_id");	
		
		$cron_qry = DB::insert(
             'cron_job', array('start_date','cron_name'))
			 ->values(array(date("Y-m-d H:i:s"),'participant'))
              ->execute();
			  
		$inserted_id= $cron_qry[0];
		
		include_once("mailsend.php");
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
						
						if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
					        $profile_logo = $logo["logo_name"];
					    }
						
						$profile =  Model::factory('profiles')->getProfileByIdForMobile($profile_id);
						
						$profile_data['profile_id'] = $profile_id;
						$profile_data['code_profile_name'] = $profile["code_profile_name"];
						$profile_data['email'] = $participant_recipient["email"];
						$profile_data['not_participated_list'] = $not_participated_list;
						$profile_data['profile_logo'] = $profile_logo;
						
						//send mail to recepient START 
		            	not_participated_notification($adminemail,$baseurl,$profile_data);
			        	//send mail to recepient END
					}
				}
			}
		}
		
		
		$dataFill = array("end_date"=>date("Y-m-d H:i:s"));
	  	$query = DB::update('cron_job')->set($dataFill)->where('cron_id', '=', $inserted_id);
	  	$query->execute();
		exit;
		
		
	}

} // End Cron
