<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Profiles extends Model{
	
	public function getAllProfiles($eq_type=NULL){
		$profiles=array();
		
		if($eq_type==NULL){
			$profiles=DB::select('*')->from('profiles')
			->where('profiles.deleted', '=', 0)
			->execute()
			->as_array();
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
		DB::update('profiles')->set(array("deleted"=>1))->where('id', '=', $id)->execute();
		//DB::delete('profiles')->where('id', '=', $id)->execute();
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
	
}
?>