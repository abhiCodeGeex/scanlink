<?php
/**
 * Description of admin
 *
 * @author megha shah
 */
class Model_Weblink extends Model{   	
	public function getWeblinkByProfileId($id){
		$documents = DB::select('*')->from('weblink')->where('weblink.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($documents){		
			return $documents;
		}else{
			return array();
		}
	}
	
	public function deleteWeblinkByProfileId($profile_id){
		DB::delete('weblink')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getWeblinkById($id){
		$document = DB::select('*')->from('weblink')->where('weblink.id', '=', $id)
		->execute()
		->as_array();
		
		if($document){		
			return $document[0];
		}else{
			return array();
		}
	}
	
	public function deleteWeblinkById($id){
		DB::delete('weblink')->where('id', '=', $id)->execute();
	}
	
}
?>