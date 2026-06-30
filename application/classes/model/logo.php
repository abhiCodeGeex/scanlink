<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Logo extends Model{   	
	public function getLogoById($id){
		$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $id)
		//->where('is_temp','=',0)
		->order_by('created_at', 'DESC')
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function getLogoExtraById($id){
		$logo = DB::select('*')->from('logo_extra')->where('logo_extra.profile_id', '=', $id)
		//->where('is_temp','=',0)
		->order_by('created_at', 'DESC')
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function get_logo_by_id_for_autosave($id){
		$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $id)
		->order_by('created_at', 'DESC')
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function get_logo_extra_by_id_for_autosave($id){
		$logo = DB::select('*')->from('logo_extra')->where('logo_extra.profile_id', '=', $id)
		->order_by('created_at', 'DESC')
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
	
	public function deleteLogoExtraByProfileId($profile_id){
		DB::delete('logo_extra')->where('profile_id', '=', $profile_id)->execute();
	}
	
	
	public function getLogoByProfileId($profile_id){
		$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $profile_id)
		->where('is_temp','=',0)
		->order_by("created_at","DESC")
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function getLogoExtraByProfileId($profile_id){
		$logo = DB::select('*')->from('logo_extra')->where('logo_extra.profile_id', '=', $profile_id)
		->where('is_temp','=',0)
		->order_by("created_at","DESC")
		->execute()
		->as_array();
		if($logo){
			return $logo[0];
		}else{
			return array();
		}
	}
	
	public function saveTempLogo($userid,$path,$profile_id,$is_temp){
			
		$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
	
		$clientid = $client_id[0]['client_id'];

		$query = DB::insert('logo', array('client_id','user_id','profile_id', 'logo_name','created_at','is_temp'))
				->values(array($clientid, $userid, $profile_id, $path, date("Y-m-d H:i:s"),$is_temp))
				->execute();
		return $query;
	}
	
	public function saveTempLogo_extra($userid,$path,$profile_id,$is_temp){
			
		$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
	
		$clientid = $client_id[0]['client_id'];

		$query = DB::insert('logo_extra', array('client_id','user_id','profile_id', 'logo_name','created_at','is_temp'))
				->values(array($clientid, $userid, $profile_id, $path, date("Y-m-d H:i:s"),$is_temp))
				->execute();
		return $query;
	}
	
	public function update_temp_field($profile_id)
	{	
		$query = DB::update('logo')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
		$query = DB::update('logo_extra')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function update_logo2_url($profile_id,$logo_url)
	{	
		$query = DB::update('logo_extra')->set(array("logo_url"=>$logo_url))->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function saveLogoFile($files,$profile_id){
		foreach($files as $key=>$file){
			$logo = DB::select('*')->from('logo')->where('id', '=', $file)->execute()->as_array();
			if($logo){
				$old="images/tmp_files/".$logo[0]["logo_name"];
				$new= "images/logo/".$logo[0]["logo_name"];
	
				if(!empty($old) && file_exists($old)){
					if (copy($old,$new)) {
						@unlink(realpath($old));
						//$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
						$query = DB::update('logo')->set(array("logo_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
					}
				}
			}
		}
	}
	public function selectOnlyLogoByProfileId($profile_id)
	{
		$logo = DB::select('logo_name')->from('logo')->where('logo.profile_id', '=', $profile_id)
		->where('is_temp', '=', 0)
		->order_by("created_at", "DESC")
		->execute()
		->as_array();
		if ($logo) {
			return $logo[0];
		} else {
			return array();
		}
	}
}
?>