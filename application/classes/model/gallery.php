<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Gallery extends Model{   	
	public function getAllImages(){
		$images = DB::select('*')->from('gallery')
		->where('approve','=',1)
		->execute()
		->as_array();
		
		if($images){		
			return $images;
		}else{
			return array();
		}
	}
}
?>