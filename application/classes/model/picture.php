<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Picture extends Model{   	
	public function getPicturesByProfileId($id){
		$picture = DB::select('*')->from('picture')->where('picture.profile_id', '=', $id)
		//->where('is_temp','=',0)
		->order_by('id','asc')
		->execute()
		->as_array();
		
		if($picture){
			return $picture;
		}else{
			return array();
		}
	  }
	  
	  public function getPicturesExtraByProfileId($id){
		$picture = DB::select('*')->from('picture_extra')->where('picture_extra.profile_id', '=', $id)
		//->where('is_temp','=',0)
		->order_by('id','asc')
		->execute()
		->as_array();
		
		if($picture){
			return $picture;
		}else{
			return array();
		}
	  }
          
           public function getPicturesVocByProfileId($id){
		$picture = DB::select('*')->from('voc_profile_image')->where('profile_id', '=', $id)
		->order_by('voc_profile_image_id','desc')
		->execute()
		->as_array();
		
		if($picture){
			return $picture;
		}else{
			return array();
		}
	  }
	  
	public function get_pictures_by_id_for_autosave($id){
		$picture = DB::select('*')->from('picture')->where('picture.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($picture){
			return $picture;
		}else{
			return array();
		}
	  }
	  
	 public function get_pictures_extra_by_id_for_autosave($id){
		$picture = DB::select('*')->from('picture_extra')->where('picture_extra.profile_id', '=', $id)
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
		->where('is_temp','=',0)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
	
	public function getPictureByIdForDelete($id){
		$picture = DB::select('*')->from('picture')->where('picture.id', '=', $id)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
	
	public function getPictureExtraById($id){
		$picture = DB::select('*')->from('picture_extra')->where('picture_extra.id', '=', $id)
		->where('is_temp','=',0)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
	
	public function getPictureExtraByIdForDelete($id){
		$picture = DB::select('*')->from('picture_extra')->where('picture_extra.id', '=', $id)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
        
        public function getPictureVocByIdForDelete($id){
		$picture = DB::select('*')->from('voc_profile_image')->where('voc_profile_image_id', '=', $id)
		->execute()
		->as_array();
		if($picture){
			return $picture[0];
		}
	}
	  
	public function deletePictureById($id){
		DB::delete('picture')->where('id', '=', $id)->execute();
	}
	
	public function deletePictureExtraById($id){
		DB::delete('picture_extra')->where('id', '=', $id)->execute();
	}
        
        public function deleteVocPictureById($id){
		DB::delete('voc_profile_image')->where('voc_profile_image_id', '=', $id)->execute();
	}
	
	public function saveTempPictures($userid, $path,$profile_id,$txt_footer,$is_temp){
			
		$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
	
		$clientid = $client_id[0]['client_id'];

		$query = DB::insert('picture', array('client_id','user_id','profile_id','txt_footer', 'picture_name','created_at','is_temp'))
				->values(array($clientid, $userid, $profile_id,$txt_footer, $path, date("Y-m-d H:i:s"),$is_temp))
				->execute();

				
		return $query;
	}
	
	public function saveTempPictures_extra($userid, $path,$profile_id,$txt_footer,$is_temp){
			
		$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
	
		$clientid = $client_id[0]['client_id'];

		$query = DB::insert('picture_extra', array('client_id','user_id','profile_id','txt_footer', 'picture_name','created_at','is_temp'))
				->values(array($clientid, $userid, $profile_id,$txt_footer, $path, date("Y-m-d H:i:s"),$is_temp))
				->execute();

				
		return $query;
	}
	
	public function saveTempPictures_voc($userid, $path,$profile_id,$is_temp){
			
		$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
	
		$clientid = $client_id[0]['client_id'];

		$query = DB::insert('voc_profile_image', array('client_id','user_id','profile_id','image_name','created_at','is_temp'))
				->values(array($clientid, $userid, $profile_id,$path, date("Y-m-d H:i:s"),$is_temp))
				->execute();

				
		return $query;
	}
	
	public function update_temp_field($profile_id)
	{
		$query = DB::update('picture')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
		$query = DB::update('picture_extra')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function update_temp_field_voc($profile_id)
	{
		$query = DB::update('voc_profile_image')->set(array("is_temp"=>'0'))->where('profile_id', '=', $profile_id)->execute();
		
	}
	
	public function savePictureFiles($files,$profile_id){
		foreach($files as $key=>$file){
			$picture = DB::select('*')->from('picture')->where('id', '=', $file)->execute()->as_array();
			if($picture){
				$old="images/tmp_files/".$picture[0]["picture_name"];
				$new= "images/picture/".$picture[0]["picture_name"];
	
				if(!empty($old) && file_exists($old)){
					if (copy($old,$new)) {
						@unlink(realpath($old));
						//$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
						$query = DB::update('picture')->set(array("picture_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
					}
				}
			}
		}
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