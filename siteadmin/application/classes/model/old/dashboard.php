<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Dashboard extends Model{   	
	public function getTypes(){
		return $profiles =  DB::select('*')
		  ->from('equipment_types')
		  ->execute()
		  ->as_array();
		
	}
	public function getProfilesByType($type_id, $client_id){
		return  DB::query(Database::SELECT, "SELECT profiles.*, qrimage.id as 'qr_id', qrimage.qrimg_name FROM profiles, qrimage where qrimage.profile_id = profiles.id and profiles.type_id = '".$type_id."' and client_id = '".$client_id."' and deleted = 0")->execute()->as_array();
		
/*		return DB::select('profiles.*, qrimage.qrimg_name')
		  ->from('profiles')->join('qrimage')->on('profiles.id', '=', 'qrimage.profile_id')
		  ->where('user_id','=',$userid)
		  ->where('type_id','=',$typeid)	  
		  ->execute()
		  ->as_array(); */
	}
	
	public function getTypeBySlag($slag){
		return DB::select('id')
		  ->from('equipment_types')		
		  ->where('slag','=',$slag)  
		  ->execute()
		  ->as_array();
	}
	  

	  
	public function getTypeById($id){
		return DB::select('*')
		  ->from('equipment_types')		
		  ->where('id','=',$id)  
		  ->execute()
		  ->as_array();
	}
}
?>