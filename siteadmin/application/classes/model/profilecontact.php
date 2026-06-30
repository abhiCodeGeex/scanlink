<?php
/**
 * Description of admin
 *
 * @author megha shah
 */
class Model_Profilecontact extends Model{   	
	public function getContactByProfileId($id){
		$documents = DB::select('*')->from('profile_contact')->where('profile_contact.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($documents){		
			return $documents;
		}else{
			return array();
		}
	}
	
	public function deleteContactByProfileId($profile_id){
		DB::delete('profile_contact')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getContactById($id){
		$document = DB::select('*')->from('profile_contact')->where('profile_contact.id', '=', $id)
		->execute()
		->as_array();
		
		if($document){		
			return $document[0];
		}else{
			return array();
		}
	}
	
	public function deleteContactById($id){
		DB::delete('profile_contact')->where('id', '=', $id)->execute();
	}
	
}
?>