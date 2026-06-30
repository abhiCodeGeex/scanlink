<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Logo extends Model{   	
	public function getLogoById($id){
		$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $id)
		->order_by("created_at","DESC")
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function deleteLogoByProfileId($profile_id){
		DB::delete('logo')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getLogoByProfileId($profile_id){
		$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $profile_id)
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
}
?>