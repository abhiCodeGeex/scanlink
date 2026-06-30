<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Logo extends Model{   	
	public function getLogoById($id){
		try{
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
		catch(Exception $e){}
	}
	
	public function deleteLogoByProfileId($profile_id){
		DB::delete('logo')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getLogoByProfileId($profile_id){
		try{		
			$logo = DB::select('*')->from('logo')->where('logo.profile_id', '=', $profile_id)
			->order_by("created_at","DESC")
			->execute()
			->as_array();
			if($logo){
				return $logo[0];
			}else{
				return array();
			}
		}
		catch(Exception $e){}
	}
	
	public function saveTempLogo($userid,$path){
		try{	
			$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
		
			$clientid = $client_id[0]['client_id'];
	
			$query = DB::insert('logo', array('client_id','user_id','profile_id', 'logo_name','created_at'))
					->values(array($clientid, $userid, 0, $path, date("Y-m-d H:i:s")))
					->execute();
			return $query;
		}
		catch(Exception $e){}
	}
	
	public function saveLogoFile($files,$profile_id){
		try{
			foreach($files as $key=>$file){
				$logo = DB::select('*')->from('logo')->where('id', '=', $file)->execute()->as_array();
				if($logo){
					$old="images/tmp_files/".$logo[0]["logo_name"];
					$new1= "../images/logo/".$logo[0]["logo_name"];
					$new= "images/logo/".$logo[0]["logo_name"];
		
					if(!empty($old) && file_exists($old)){
						if (copy($old,$new1)) {
							@unlink(realpath($old));
							//$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
							$query = DB::update('logo')->set(array("logo_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
						}
					}
				}
			}
		}
		catch(Exception $e){}
	}
}
?>