<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Gallery extends Model{   	
	public function getAllImages(){
		$images = DB::select('*')->from('gallery')
		->execute()
		->as_array();
		
		if($images){		
			return $images;
		}else{
			return array();
		}
	}
	
	public function saveImages($images){
		return DB::insert('gallery', array('name'))
            ->values(array($images))
            ->execute();
	}
	public function getLastId(){
		$lastid = DB::select('id')->from('gallery')
		->order_by('id', 'DESC')
		->limit(1)
		->execute()
		->as_array();
		if(isset($lastid) && !empty($lastid)){
			return $lastid[0]['id'];
		}
		else{
			return 0;
		}
	}
	public function deleteImage($id){
		$fnameArr = $this->getImageById($id);
		if(DB::delete('gallery')->where('id', '=', $id)->execute()){
			$fullimg = $fnameArr['name'];
			/*$ext = pathinfo($fullimg,PATHINFO_EXTENSION);
			$fname = pathinfo($fullimg,PATHINFO_FILENAME);*/
			//$fname = $image['name'];
			$thumbimg = "thumb_".$fullimg;
			//$thumbimg = $fname."_thumb.".$ext;
			$dir = DOCROOT.'images/galleryApi/fullscreen/';
			unlink($dir.$fullimg);
			unlink($dir.$thumbimg);
		}
	}
	
	public function getImageById($id){
		try{
			$imagesList = DB::select('*')->from('gallery')->where('gallery.id', '=', $id)
			->execute()
			->as_array();
			
			if($imagesList){		
				return $imagesList[0];
			}else{
				return array();
			}
		}
		catch(Exception $e){}
	}
	
	public function blockImage($id){
		$cstatus = DB::select('approve')
          ->from('gallery')
		  ->where('id','=',$id)		  
		  ->execute()
		  ->as_array();
		if($cstatus[0]['approve'] == 1):
			$sflag = '0';
		else:
			$sflag = '1';
		endif;
		DB::update('gallery')->set(array('approve' => $sflag))->where('id', '=', $id)->execute();
	}
	
	
	
}
?>