<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_People extends Model{
	public function insertPeople($data, $userid){
		$client_id = DB::select('client_id')
		->from('users')
		->where('id','=',$userid)
		->execute()
		->as_array();
		
		$clientid = $client_id[0]['client_id'];
		
		$type_id = DB::select('id')
		->from('equipment_types')
		->where('slag','=','people')
		->execute()
		->as_array();
		
		$typeid = $type_id[0]['id'];
		
		$profile_id = DB::insert('profiles', array('client_id','user_id','type_id','name', 'position','notes','name_company','telephone','protect','created_at'))
            ->values(array($clientid, $userid, $typeid, $data['txtname'], $data['txtposition'], $data['txtnotes'], $data['txtcompname'], $data['txttelephone'], $data['protect'], $data['created_at']))
            ->execute();
		
/*		DB::insert('logo', array('client_id','user_id','profile_id','logo_name','created_at'))
            ->values(array($clientid, $userid, $profile_id[0], $data['logo_file'], $data['created_at']))
            ->execute();*/
			
/*		DB::insert('picture', array('client_id','user_id','profile_id','picture_name','created_at'))
            ->values(array($clientid,$userid,$profile_id[0],$data['picture_file'],$data['created_at']))
            ->execute(); */
			
/*		if(!empty($data["picture_file"])){
			$query = DB::insert('picture', array('client_id','user_id','profile_id','picture_name','created_at'));
			foreach($data["picture_file"] as $file){
				$query->values(array($clientid, $userid, $profile_id[0], $file,$data['created_at']));
			}
			$query->execute();
		}*/	
			
			
		if(!empty($data["videos"])){
			$query = DB::insert('video', array('client_id','user_id','profile_id', 'title','video_name','created_at'));
			foreach($data["videos"] as $video){

				$query->values(array($clientid,$userid,$profile_id[0],$video['title'],$video['id'],$data['created_at']));
			}
			$query->execute();
		}

		if(!empty($data["checklist_items"])){
			$query = DB::insert('checklist_item', array('checklist_item','profile_id','datetime'));
			foreach($data["checklist_items"] as $checklist_item){
				$query->values(array($checklist_item, $profile_id[0],date('Y-m-d H:i:s')));
			}
			$query->execute();
		}		
			
/*		DB::insert('video', array('client_id','user_id','profile_id','video_name','created_at'))
            ->values(array($clientid,$userid,$profile_id[0],$data['video_file'],$data['created_at']))
            ->execute();*/
/*		if(!empty($data["docu_file"])){
			$query = DB::insert('documents', array('client_id','user_id','profile_id','name','doc_name','created_at'));
			foreach($data["docu_file"] as $file){
				$query->values(array($clientid, $userid, $profile_id[0], $file["name"], $file["path"], $data['created_at']));
			}
			$query->execute();
		}*/
        return $profile_id[0];
	  }
	  
	  
		public function updatePeople($data,$profile_id){
			
/*			if($data["logo_file"]!==false){
				DB::update('logo')->set(array("logo_name"=>$data["logo_file"]))->where('logo.profile_id', '=', $profile_id)->execute();
			}*/
			
			$profile = DB::select('*')
			->from('profiles')
			->where('id','=',$profile_id)
			->execute()
			->as_array();
			
			$dataFill = array( "name" => $data["txtname"], "position" => $data["txtposition"], "notes" => $data["txtnotes"], "name_company" => $data["txtcompname"], "telephone" => $data["txttelephone"], "protect" => $data["protect"]);
			
			$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
			$query->execute();
			
/*			if(!empty($data["picture_file"])){
				$query = DB::insert('picture', array('client_id','user_id','profile_id','picture_name','created_at'));
				foreach($data["picture_file"] as $file){
					$query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $file, $data['created_at']));
				}
				$query->execute();
			}*/
			
			if(!empty($data["videos"])){
				$query = DB::insert('video', array('client_id','user_id','profile_id', 'title','video_name','created_at'));
		
				foreach($data["videos"] as $video){

					$query->values(array($profile[0]["client_id"], $profile[0]["user_id"],$profile_id, $video['title'], $video['id'], $data['created_at']));
				}
				$query->execute();
			}
			if(!empty($data["checklist_item_ids"])){
				foreach($data["checklist_item_ids"] as $key=>$checklist_item_id){
					$query = DB::update('checklist_item')->set(array('checklist_item'=>$data["checklist_items"][$key]))->where('id', '=', $checklist_item_id);
					$query->execute();
					unset($data["checklist_items"][$key]);
				}
			}
			
			if(!empty($data["checklist_items"])){
				$query = DB::insert('checklist_item', array('checklist_item','profile_id','datetime'));
				foreach($data["checklist_items"] as $checklist_item){
					$query->values(array($checklist_item, $profile_id,date('Y-m-d H:i:s')));
				}
				$query->execute();
			}
			
/*			if(!empty($data["docu_file"])){
				$query = DB::insert('documents', array('client_id','user_id','profile_id','name','doc_name','created_at'));
				foreach($data["docu_file"] as $file){
					$query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $file['name'], $file['path'], $data['created_at']));
				}
				$query->execute();
			}*/
			
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
}
?>