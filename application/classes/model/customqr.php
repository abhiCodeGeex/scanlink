<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Customqr extends Model{
	public function insertCustomqr($data, $userid){
		$client_id = DB::select('client_id')
		->from('users')
		->where('id','=',$userid)
		->execute()
		->as_array();
		
		$clientid = $client_id[0]['client_id'];
		
		$type_id = DB::select('id')
		->from('equipment_types')
		->where('slag','=',$data['type'])
		->execute()
		->as_array();
		
		$typeid = $type_id[0]['id'];
		
		$profile_id = DB::insert('profiles', array('client_id','user_id','type_id','name','created_at'))
            ->values(array($clientid, $userid, $typeid, $data['txturl'], $data['created_at']))
            ->execute();
		
        return $profile_id[0];
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
		  if($data){
		 	return $data[0]['qrimg_name'];
		  }
	}
}
?>