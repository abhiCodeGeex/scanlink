<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Documents extends Model{   	
	public function getDocumentsByProfileId($id){
		$documents = DB::select('*')->from('documents')->where('documents.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($documents){		
			return $documents;
		}else{
			return array();
		}
	}
	
	public function deleteDocumentByProfileId($profile_id){
		DB::delete('documents')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getDocumentById($id){
		$document = DB::select('*')->from('documents')->where('documents.id', '=', $id)
		->execute()
		->as_array();
		
		if($document){		
			return $document[0];
		}else{
			return array();
		}
	}
	
	public function deleteDocumentById($id){
		DB::delete('documents')->where('id', '=', $id)->execute();
	}
	
}
?>