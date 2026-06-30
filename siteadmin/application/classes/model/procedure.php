<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Procedure extends Model{
	public function insertProcedure($data, $userid){
		$client_id = DB::select('client_id')
						->from('users')
						->where('id','=',$userid)
						->execute()
						->as_array();
		$clientid = $client_id[0]['client_id'];
		
		$type_id = DB::select('id')
					->from('equipment_types')
					->where('slag','=','procedure')
					->execute()
					->as_array();
		$typeid = $type_id[0]['id'];
		
		$profile_id = DB::insert('profiles', 
									array(
										'client_id',
										'user_id',
										'type_id',
										'name',
										'identification',
										'address',
										'description',
										'notes',
										'protect',
										'password',
										'show_header',
										'code_type',
										'buttonbackcolor',
										'buttonfontcolor',
										'display_share_link',
										'enable_data_collection', 
										'set_up_compulsory',
										'data_collection_mobile', 
										'data_collection_email', 
										'data_collection_name',
										'data_collection_content',
										'update_or_not',
										'created_at',
										'updated_at',
										'expired_at',
									)
								)
				     ->values(
					 	array(
							$clientid,
							$userid,
							$typeid,
							$data['txtname'],
							$data['txtidenty'],
							$data['txtaddress'],
							$data['txtdesc'],
							$data['txtnotes'],
							$data['protect'],
							$data['password'],
							$data['show_header'],
							$data['code_type'],
							$data['txtbuttonbackcolor'],
							$data['txtbuttonfontcolor'],
							$data['display_share_link'],
							$data["enable_data_collection"], 
							$data["set_up_compulsory"],
							$data["data_collection_mobile"], 
							$data["data_collection_email"], 
							$data["data_collection_name"],
							$data["data_collection_content"],
							'1',
							$data['created_at'],
							date("Y-m-d H:i:s"),
							self::add_date(date("Y-m-d H:i:s"), 365)
						)
					)
		            ->execute();
					
					
		//insert web link 
			if (is_array($data["link_button"]) && !empty($data["link_button"])) {
			  foreach ($data["link_button"] as $key => $weblink_data) {
				
				  DB::insert('weblink', array(
							  'profile_id',
							  'link_button',
							  'link_button_text',
							  'link_button_url',
							  'link_button_color',
							  'created_at'
								  )
						  )
						  ->values(
								  array(
									  $profile_id[0],
									  $weblink_data,
									  $data['link_button_text'][$key],
									  $data['link_button_url'][$key],
									  $data['link_button_color'][$key],
									  date("Y-m-d H:i:s")
								  )
						  )
						  ->execute();
			  }
			}
		
		
			//first delete all contact
			DB::delete('profile_contact')->where('profile_id', '=', $profile_id[0])->execute();
			//insert contact
			if ((is_array($data["txtcompname"]) && !empty($data["txtcompname"])) || (is_array($data["txttelephone"]) && !empty($data["txttelephone"]))) {
		
			  $total = 0;
			  if (count($data["txtcompname"]) > $data["txttelephone"]) {
				$total = count($data["txtcompname"]);
			  } else {
				$total = count($data["txttelephone"]);
			  }
		
			  for ($counter = 0; $counter < $total; $counter++) {
				if ($data["txtcompname"][$counter] != '' || $data['txttelephone'][$counter] != '') {
				  DB::insert('profile_contact', array(
							  'profile_id',
							  'name_company',
							  'telephone',
							  'datestamp'
								  )
						  )
						  ->values(
								  array(
									  $profile_id[0],
									  $data["txtcompname"][$counter],
									  $data['txttelephone'][$counter],
									  date("Y-m-d H:i:s")
								  )
						  )
						  ->execute();
				}
			  }
			}
		
			
			
		if(!empty($data["videos"])){
			$query = DB::insert('video', array('client_id','user_id','profile_id', 'title','video_name','created_at'));
			foreach($data["videos"] as $video){

				$query->values(array($clientid,$userid,$profile_id[0],$video['title'],$video['id'],$data['created_at']));
			}
			$query->execute();
		}

		
        return $profile_id[0];
	  }
	  
	  
		public function updateProcedure($data,$profile_id){
			

			
			$profile = DB::select('*')
						->from('profiles')
						->where('id','=',$profile_id)
						->execute()
						->as_array();
			
			
			$dataFill = array(
						  "name" => $data["txtname"], 
						  "identification" => $data["txtidenty"], 
						  "address" => $data["txtaddress"], 
						  "description" => $data["txtdesc"], 
						  "notes" => $data["txtnotes"],
						  "protect" => $data["protect"], 
						  "password" => $data["password"], 
						  "show_header" => $data["show_header"], 
						  "code_type" => $data["code_type"], 
						  "buttonbackcolor" => $data["txtbuttonbackcolor"], 
						  "buttonfontcolor" => $data["txtbuttonfontcolor"], 
						  "display_share_link"=>$data['display_share_link'],
						  "enable_data_collection" => $data["enable_data_collection"],
						  "set_up_compulsory" => $data["set_up_compulsory"],
						  "data_collection_mobile" => $data["data_collection_mobile"], 
						  "data_collection_email" => $data["data_collection_email"],
						  "data_collection_name" => $data["data_collection_name"], 
						  "data_collection_content" => $data["data_collection_content"],
    					);
			
			$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
			$query->execute();
			
			
			//first delete all weblink
			DB::delete('weblink')->where('profile_id', '=', $profile_id)->execute();
			
			//insert web link 
			if (is_array($data["link_button"]) && !empty($data["link_button"])) {
			  foreach ($data["link_button"] as $key => $weblink_data) {
				
				  DB::insert('weblink', array(
							  'profile_id',
							  'link_button',
							  'link_button_text',
							  'link_button_url',
							  'link_button_color',
							  'created_at'
								  )
						  )
						  ->values(
								  array(
									  $profile_id,
									  $weblink_data,
									  $data['link_button_text'][$key],
									  $data['link_button_url'][$key],
									  $data['link_button_color'][$key],
									  date("Y-m-d H:i:s")
								  )
						  )
						  ->execute();
			  }
			}
		
		
			//first delete all contact
			DB::delete('profile_contact')->where('profile_id', '=', $profile_id)->execute();
			//insert contact
			if ((is_array($data["txtcompname"]) && !empty($data["txtcompname"])) || (is_array($data["txttelephone"]) && !empty($data["txttelephone"]))) {
		
			  $total = 0;
			  if (count($data["txtcompname"]) > $data["txttelephone"]) {
				$total = count($data["txtcompname"]);
			  } else {
				$total = count($data["txttelephone"]);
			  }
		
			  for ($counter = 0; $counter < $total; $counter++) {
				if ($data["txtcompname"][$counter] != '' || $data['txttelephone'][$counter] != '') {
				  DB::insert('profile_contact', array(
							  'profile_id',
							  'name_company',
							  'telephone',
							  'datestamp'
								  )
						  )
						  ->values(
								  array(
									  $profile_id,
									  $data["txtcompname"][$counter],
									  $data['txttelephone'][$counter],
									  date("Y-m-d H:i:s")
								  )
						  )
						  ->execute();
				}
			  }
			}
			
			
			if(!empty($data["videos"])){
				$query = DB::insert('video', array('client_id','user_id','profile_id', 'title','video_name','created_at'));
		
				foreach($data["videos"] as $video){

					$query->values(array($profile[0]["client_id"], $profile[0]["user_id"],$profile_id, $video['title'], $video['id'], $data['created_at']));
				}
				$query->execute();
			}
			
		}
	  
	 public function getProfilesByType($typeid, $userid){
		return DB::select()
		  ->from('profiles')	
		  ->where('user_id','=',$userid)
		  ->where('type_id','=',$typeid)
		  ->execute()
		  ->as_array();
	  }
	  public function getTypeBySlag($slag){
		return DB::select('id')
		  ->from('equipment_types')		
		  ->where('slag','=',$slag)  
		  ->execute()
		  ->as_array();
	  }
	 
	 public function getProfilesById($id){
		 $data = DB::select()
		  ->from('profiles')	
		  ->where('id','=',$id)
		  ->execute()
		  ->as_array();
		  
		  if($data){
			  return $data[0];
		  }
	}
	public function getQrimgById($id){
		 $data = DB::select('qrimg_name')
		  ->from('qrimage')	
		  ->where('profile_id','=',$id)
		  ->execute()
		  ->as_array();
		  //print_r($data);exit;
		  if($data){
		 	return $data[0]['qrimg_name'];
		  }
	}
	
	/**** 
	***** add date 
  	****/
  	protected function add_date($givendate, $day = 0, $mth = 0, $yr = 0) {
    	$cd = strtotime($givendate);
	    $newdate = date('Y-m-d H:i:s', mktime(date('H', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
    	return $newdate;
   }
}
?>