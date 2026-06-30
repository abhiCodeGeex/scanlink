<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Profiles extends Model{
	
	public function updateClientIdsOfTransferredProfiles($profile_ids,$client_id){
		DB::update('profiles')->set(array('client_id'=>$client_id))
		->where('id','IN',$profile_ids)->execute();
	}

	public function updateUserIdsOfConflictProfilesOfNewClient($profile_ids, $user_id){	
		DB::update('profiles')->set(array('user_id'=>$user_id))
		->where('id','IN',$profile_ids)->execute();
	}
	
	public function updateUserIdsOfConflictProfiles($profile_ids, $user_id){
		
		DB::update('profiles')->set(array('user_id'=>$user_id))
		->where('id','IN',$profile_ids)->execute();
	}
	
	public function getConfictProfilesOfNewClient($users,$profiles,$client){
		$resutls = DB::select('id')->from('profiles')
					->where('client_id', '=', $client)
					->where('user_id', 'NOT IN', $users)
					->where('id', 'IN', $profiles)
					->execute()
					->as_array();
		
		$profile_ids = array();
		foreach($resutls as $value){
			array_push($profile_ids,$value['id']);
		}

		return $profile_ids;	
	}
	
	public function getConfictProfiles($users,$profiles,$client){
		$resutls = DB::select('id')->from('profiles')
					->where('client_id', '=', $client)
					->where('user_id', 'IN', $users)
					->where('id', 'NOT IN', $profiles)
					->execute()
					->as_array();
		
		$profile_ids = array();
		foreach($resutls as $value){
			array_push($profile_ids,$value['id']);
		}
		
		return $profile_ids;
	}
	
	public function getProfilesByClientId($id){
		return DB::select('*')->from('profiles')
		->where('client_id', '=', $id)
		->and_where('deleted', '=', 0)
		->execute()
		->as_array();
	}
	
	public function getAllProfiles($eq_type=NULL){
		$profiles=array();
		//"if(`type_id` = 9,(if(`update_or_not` = 1,`id`,'')),`id`)";
		if($eq_type==NULL){
			/*$profiles=DB::select('*')->from('profiles')
			->where('profiles.deleted', '=', 0)
			->execute()
			->as_array();
			*/
			//$query    = DB::query(Database::SELECT, 'SELECT * FROM profiles WHERE profiles.deleted = 0 AND  if(`type_id` = 9,(if(`update_or_not` = "1","",`id`)),`id`) ');
			
			$query = DB::query(Database::SELECT, '
			select * from profiles p
				where p.deleted = 0
				and p.type_id between 1 and 8
				or p.id in (select id from profiles p1 where p1.type_id = 9 and p1.update_or_not="1")
				order by id desc
			');
			
			$profiles = $query->execute()->as_array();
			
			/*echo "<pre>";
				print_r ($profiles);
			echo "</pre>";
			exit;*/
			
			
		}		
		return $profiles;
	}
	
	public function getProfileById($id){
		$profile=DB::select('*')->from('profiles')->where('profiles.id', '=', $id)
		->execute()
		->as_array();
		
		if($profile):
			return $profile[0];
		else:
			return array();
		endif;
	}
	
	public function getQRByProfileId($id){
		$qr=DB::select('*')->from('qrimage')->where('qrimage.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($qr):
			return $qr[0];
		else:
			return array();
		endif;
	}
	
	public function deleteprofile($id) {
        // Delete profiles
		//DB::update('profiles')->set(array("deleted"=>1))->where('id', '=', $id)->execute();
		
	}
	public function getEquipmentTypeById($id) {
        $qr=DB::select('name')->from('equipment_types')->where('equipment_types.id', '=', $id)
		->execute()
		->as_array();
		
		if($qr):
			return $qr[0]['name'];
		else:
			return '';
		endif;
	}
	public function getUserIdByProfileId($pid){
		$prs = DB::select('user_id')->from('profiles')
					->where('id', '=', $pid)
					->execute()->as_array();
					
		if($prs){
			return $prs[0]['user_id'];
		}else{
			return 0;
		}
	}
	public function updateProfile($id,$data){
		DB::update('profiles')->set($data)->where('profiles.id', '=', $id)->execute();
	}
	
}
?>