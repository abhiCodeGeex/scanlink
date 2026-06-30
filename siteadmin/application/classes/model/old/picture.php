<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Picture extends Model{   	
	public function getPicturesByProfileId($id){
		$picture = DB::select('*')->from('picture')->where('picture.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($picture){
			return $picture;
		}else{
			return array();
		}
	  }
	  
	public function deletePicturesByProfileId($profile_id){
		 DB::delete('picture')->where('profile_id', '=', $profile_id)->execute();
	}
	  
	public function getPictureById($id){
		$picture = DB::select('*')->from('picture')->where('picture.id', '=', $id)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
	  
	public function deletePictureById($id){
		DB::delete('picture')->where('id', '=', $id)->execute();
	}
}
?>