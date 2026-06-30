<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Code extends Model {

 
  /**** code update first time means insert code data
  ***** @param data array
  ***** @param profile_id  integer
  ****/
  public function insertCode($data, $profile_id) {
	  
	  	$type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', 'code')
            ->execute()
            ->as_array();
    	$typeid = $type_id[0]['id'];
	
		$dataFill = array(
						"type_id"=>$typeid,
						"name" => $data["application"],
						"description"=>$data["txtdesc"],
						"enable_data_collection" => $data["enable_data_collection"], 
						"set_up_compulsory" => $data["set_up_compulsory"], 
						"data_collection_mobile" => $data["data_collection_mobile"], 
						"data_collection_email" => $data["data_collection_email"], 
						"data_collection_name" => $data["data_collection_name"],
						"data_collection_surname" => $data["data_collection_surname"],
						"url"=>$data["destination_url"],
						"update_or_not"=>"1",
						"code_type"=>$data["code_type"],
						"color_code"=>$data["color_code"],
						"created_at"=>date("Y-m-d H:i:s"),
						"updated_at"=>date("Y-m-d H:i:s")
					);
		$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
	    $query->execute();   
		
		
		 $query1 = DB::select()
            ->select('id')
            ->from('logo')
            ->where('user_id', '=', $data['user_id'])
            ->where('client_id', '=', $data['client_id'])
			->where('profile_id', '=', $profile_id)
			;
		$logodata = $query1->execute()
                    ->as_array();
		if($data['images'] != ''){
		if(empty($logodata)){
				$client_id = DB::insert(
								'logo', 
								array(
									'client_id',
									'user_id',
									'profile_id',
									'logo_name',
									'created_at',
									'updated_at'
								)
							)
							->values(
								array(
									$data['client_id'],
									$data['user_id'],
									$profile_id,
									$data['images'],
									date("Y-m-d H:i:s"),
									date("Y-m-d H:i:s")
								)
							)
							->execute();
		}else{
			$dataFill1 = array(
						"logo_name" => $data['images'],
						"updated_at"=>date("Y-m-d H:i:s")
					);	
			
			$query = DB::update('logo')->set($dataFill1)->where('id', '=', $logodata[0]['id']);
	    	$query->execute();
		}
		}
		
  }
  
   /**** code update
  ***** @param data array
  ***** @param profile_id  integer
  ****/
  public function updateCode($data, $profile_id) {
		$dataFill = array(
						"name" => $data["application"],
						"description"=>$data["txtdesc"],
						"enable_data_collection" => $data["enable_data_collection"], 
						"set_up_compulsory" => $data["set_up_compulsory"],
						"data_collection_mobile" => $data["data_collection_mobile"], 
						"data_collection_email" => $data["data_collection_email"], 
						"data_collection_name" => $data["data_collection_name"],
						"data_collection_surname" => $data["data_collection_surname"],
						"url"=>$data["destination_url"],
						"code_type"=>$data["code_type"],
						"color_code"=>$data["color_code"],
						"update_or_not"=>"1",
						"updated_at"=>date("Y-m-d H:i:s")
					);
		$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
	    $query->execute();  
		
		 $query1 = DB::select()
            ->select('id')
            ->from('logo')
            ->where('client_id', '=', $data['client_id'])
			->where('user_id', '=', $data['user_id'])
			->where('profile_id', '=', $profile_id)
			;
		$logodata = $query1->execute()
                    ->as_array();
		if($data['images'] != ''){
		if(empty($logodata)){
				$client_id = DB::insert(
								'logo', 
								array(
									'client_id',
									'user_id',
									'profile_id',
									'logo_name',
									'created_at',
									'updated_at'
								)
							)
							->values(
								array(
									$data['client_id'],
									$data['user_id'],
									$profile_id,
									$data['images'],
									date("Y-m-d H:i:s"),
									date("Y-m-d H:i:s")
								)
							)
							->execute();
		}else{
			$dataFill1 = array(
						"logo_name" => $data['images'],
						"updated_at"=>date("Y-m-d H:i:s")
					);	
			$query = DB::update('logo')->set($dataFill1)->where('id', '=', $logodata[0]['id']);
	    	$query->execute();
		}
		}
  }

  public function getProfilesByType($typeid, $userid) {
    return DB::select()
                    ->from('profiles')
                    ->where('user_id', '=', $userid)
                    ->where('type_id', '=', $typeid)
                    ->execute()
                    ->as_array();
  }

  public function getTypeBySlag($slag) {
    return DB::select('id')
                    ->from('equipment_types')
                    ->where('slag', '=', $slag)
                    ->execute()
                    ->as_array();
  }

  public function getProfilesById($id) {
    $data = DB::select()
            ->from('profiles')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

    if ($data) {
      return $data[0];
    }
  }
  

  public function getQrimgById($id) {
    $data = DB::select('qrimg_name')
            ->from('qrimage')
            ->where('profile_id', '=', $id)
            ->execute()
            ->as_array();

    //print_r($data);exit;
    if ($data) {
      return $data[0]['qrimg_name'];
    }
  }

  public function getPendingCodes($client_id) {
    $query = DB::select()
            ->select(array('count("id")','pending_codes'))
            ->from('profiles')
           // ->where('user_id', '=', $userid)
            ->where('client_id', '=', $client_id)
            ->where('update_or_not', '=', '0');

    return $query->execute()
                    ->as_array();
  }
  
  public function getprofileidtoupdate($client_id){
	  $query = DB::select()
            ->select('id')
            ->from('profiles')
           // ->where('user_id', '=', $userid)
		   ->where('client_id', '=', $client_id)
		   ->where('expired_at', '>',  date("Y-m-d H:i:s"))  
            ->where('update_or_not', '=', '0')
			;
			
			//line added to prevent any expired code warning when select add new code -->  expired_at > date("Y-m-d H:i:s")  
		
	  return $query->execute()
                    ->as_array();
  }

}

?>