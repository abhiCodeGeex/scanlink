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
		try{
			$picture = DB::select('*')->from('picture')->where('picture.id', '=', $id)
			->execute()
			->as_array();
			if($picture){
				return $picture[0];
			}
		}
		catch(Exception $e){}
	}
	  
	public function deletePictureById($id){
		DB::delete('picture')->where('id', '=', $id)->execute();
	}
	
	public function saveTempPictures($userid, $path){
		try{
			$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
			$clientid = $client_id[0]['client_id'];
			$query = DB::insert('picture', array('client_id','user_id','profile_id', 'picture_name','created_at'))
					->values(array($clientid, $userid, 0, $path, date("Y-m-d H:i:s")))
					->execute();
			return $query;
		}
		catch(Exception $e){}
	}
	
	public function savePictureFiles($files,$profile_id){
		try{	
			foreach($files as $key=>$file){
				$picture = DB::select('*')->from('picture')->where('id', '=', $file)->execute()->as_array();
				if($picture){
					$old="images/tmp_files/".$picture[0]["picture_name"];
					$new1= "../images/picture/".$picture[0]["picture_name"];
					$new= "images/picture/".$picture[0]["picture_name"];
		
					if(!empty($old) && file_exists($old)){
						if (copy($old,$new1)) {
							@unlink(realpath($old));
							//$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
							$query = DB::update('picture')->set(array("picture_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
						}
					}
				}
			}
		}
		catch(Exception $e){}
	}
	
	public function getTempPictures(){
		$pictures = DB::select('*')->from('picture')
					->where('profile_id', '=', "0")
					->and_where('created_at', '<',date("Y-m-d H:i:s", mktime(date("H"),date("i"),date("s"),date("m"),date("d") - 1, date("Y"))))->execute()->as_array();
					
		if($pictures){
			return $pictures;
		}else{
			return array();
		}
	}
	
}
?>